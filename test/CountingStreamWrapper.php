<?php

declare(strict_types=1);

namespace Pel\Test;

// PHP defines the method names of a stream wrapper (stream_open, stream_read, ...).
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
