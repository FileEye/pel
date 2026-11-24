<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\Pel;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelDataWindowOffsetException;
use lsolesen\pel\PelException;
use lsolesen\pel\PelFileStream;
use lsolesen\pel\PelJpeg;
use PHPUnit\Framework\TestCase;

/**
 * Tests reading files through a userspace stream wrapper.
 *
 * PHP returns at most 8192 bytes per fread() call on userspace stream
 * wrappers, and every seek discards PHP's read buffer. Frameworks like
 * Drupal pass such URIs (public://image.jpg) to PEL.
 */
class StreamWrapperTest extends TestCase
{
    private const SCHEME = 'peltest';

    private string $tempFile = '';

    protected function setUp(): void
    {
        stream_wrapper_register(self::SCHEME, CountingStreamWrapper::class);
        CountingStreamWrapper::$reads = 0;
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
    }

    protected function tearDown(): void
    {
        stream_wrapper_unregister(self::SCHEME);
        if ($this->tempFile !== '' && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testReadReturnsRequestedLength(): void
    {
        $data = random_bytes(200000);
        $stream = new PelFileStream($this->wrap($this->createTempFile($data)));

        $this->assertSame($data, $stream->read(0, 200000));
        $this->assertSame(substr($data, 100000, 70000), $stream->read(100000, 70000));
        $this->assertSame(substr($data, 150000, 10), $stream->read(150000, 10));
        $this->assertSame(substr($data, 199990), $stream->read(199990, 100));
        $stream->close();

        // The buffer must not serve data after the stream is closed.
        $this->expectException(PelException::class);
        $stream->read(150000, 10);
    }

    public function testJpegFromStreamWrapperMatchesFile(): void
    {
        $file = __DIR__ . '/images/gh-77.jpg';

        $jpeg = new PelJpeg($this->wrap($file));

        $this->assertSame((new PelJpeg($file))->getBytes(), $jpeg->getBytes());
    }

    public function testTrailingDataIsNotReadByteByByte(): void
    {
        $image = (string) file_get_contents(__DIR__ . '/images/gh-77.jpg');
        $trailing = str_repeat("\0", 1048576);
        $file = $this->createTempFile($image . $trailing);

        $jpeg = new PelJpeg($this->wrap($file));

        $this->assertSame((new PelJpeg(new PelDataWindow($image . $trailing)))->getBytes(), $jpeg->getBytes());
        $this->assertSame($trailing, $this->getTrailingContent($jpeg));
        // Reading byte by byte takes more than a million reads.
        $this->assertLessThan(1000, CountingStreamWrapper::$reads);
    }

    public function testEoiMarkerSpanningSearchChunks(): void
    {
        $image = (string) file_get_contents(__DIR__ . '/images/gh-77.jpg');
        // The EOI search reads 64 KiB chunks from the end: with this length,
        // the 0xFF of the marker is the last byte before the first chunk.
        $trailing = str_repeat("\0", 65535);

        $jpeg = new PelJpeg(new PelDataWindow($image . $trailing));

        $this->assertSame($trailing, $this->getTrailingContent($jpeg));
    }

    public function testLastEoiMarkerIsUsed(): void
    {
        $image = (string) file_get_contents(__DIR__ . '/images/gh-77.jpg');
        $trailing = "trailing\xFF\xD9content";

        $jpeg = new PelJpeg(new PelDataWindow($image . $trailing));

        $this->assertSame('content', $this->getTrailingContent($jpeg));
    }

    public function testMissingEoiMarker(): void
    {
        $image = (string) file_get_contents(__DIR__ . '/images/gh-77.jpg');

        $this->expectException(PelDataWindowOffsetException::class);
        new PelJpeg(new PelDataWindow(substr($image, 0, -2)));
    }

    private function createTempFile(string $data): string
    {
        $this->tempFile = (string) tempnam(sys_get_temp_dir(), 'pel');
        file_put_contents($this->tempFile, $data);

        return $this->tempFile;
    }

    private function wrap(string $path): string
    {
        return self::SCHEME . '://' . $path;
    }

    /**
     * Returns the content after the EOI marker, stored with marker 0x00.
     */
    private function getTrailingContent(PelJpeg $jpeg): ?string
    {
        foreach ($jpeg->getSections() as [$marker, $content]) {
            if ($marker === 0x00) {
                return $content->getBytes();
            }
        }

        return null;
    }
}

// A stream wrapper class is needed for the test, PHP defines its method names.
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * Passes through to local files and counts the reads.
 *
 * Use it with URIs like peltest:///path/to/file.
 */
class CountingStreamWrapper
{
    public static int $reads = 0;

    /**
     * @var resource|null
     */
    public $context;

    /**
     * @var resource|null
     */
    private $handle;

    public function stream_open(string $path, string $mode): bool
    {
        $handle = fopen(self::localPath($path), $mode);
        if ($handle === false) {
            return false;
        }
        $this->handle = $handle;

        return true;
    }

    public function stream_read(int $count): string|false
    {
        self::$reads++;

        return $this->handle !== null && $count > 0 ? fread($this->handle, $count) : false;
    }

    public function stream_seek(int $offset, int $whence): bool
    {
        return $this->handle !== null && fseek($this->handle, $offset, $whence) === 0;
    }

    public function stream_tell(): int|false
    {
        return $this->handle !== null ? ftell($this->handle) : false;
    }

    public function stream_eof(): bool
    {
        return $this->handle === null || feof($this->handle);
    }

    /**
     * @return array<int|string, int>|false
     */
    public function stream_stat(): array|false
    {
        return $this->handle !== null ? fstat($this->handle) : false;
    }

    public function stream_close(): void
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    /**
     * @return array<int|string, int>|false
     */
    public function url_stat(string $path, int $flags): array|false
    {
        return @stat(self::localPath($path));
    }

    private static function localPath(string $path): string
    {
        return substr($path, strlen('peltest://'));
    }
}
