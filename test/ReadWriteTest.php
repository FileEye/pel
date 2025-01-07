<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\Pel;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelEntrySByte;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelEntrySLong;
use lsolesen\pel\PelEntrySShort;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelFormat;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ReadWriteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Pel::setStrictParsing(true);
    }

    /**
     * @dataProvider writeEntryProvider
     *
     * @param array<string, mixed> $entries
     */
    #[DataProvider('writeEntryProvider')]
    public function testWriteRead(array $entries): void
    {
        $ifd = new PelIfd(PelIfd::IFD0);
        $this->assertTrue($ifd->isLastIfd());

        foreach ($entries as $entry) {
            $ifd->addEntry($entry);
        }

        $tiff = new PelTiff();
        $this->assertNull($tiff->getIfd());
        $tiff->setIfd($ifd);
        $this->assertNotNull($tiff->getIfd());

        $exif = new PelExif();
        $this->assertNull($exif->getTiff());
        $exif->setTiff($tiff);
        $this->assertNotNull($exif->getTiff());

        $jpeg = new PelJpeg(__DIR__ . '/images/no-exif.jpg');
        $this->assertNull($jpeg->getExif());
        $jpeg->setExif($exif);
        $this->assertNotNull($jpeg->getExif());

        $jpeg->saveFile('test-output.jpg');
        $this->assertFileExists('test-output.jpg');
        $this->assertGreaterThan(0, filesize('test-output.jpg'));

        /* Now read the file and see if the entries are still there. */
        $jpeg = new PelJpeg('test-output.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        $ifd = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd);

        $this->assertSame(PelIfd::IFD0, $ifd->getType());
        $this->assertTrue($ifd->isLastIfd());

        foreach ($entries as $entry) {
            $ifdEntry = $ifd->getEntry($entry->getTag());
            $this->assertNotNull($ifdEntry);
            if ($ifdEntry->getFormat() === PelFormat::ASCII) {
                $ifdValue = $ifd->getEntry($entry->getTag())?->getValue();
                $entryValue = $entry->getValue();
                // cut off after the first nul byte
                // since $ifdValue comes from parsed ifd,
                // it is already cut off
                $canonicalEntry = strstr((string) $entryValue, "\0", true);
                // if no nul byte found, use original value
                if ($canonicalEntry === false) {
                    $canonicalEntry = $entryValue;
                }
                $this->assertEquals($ifdValue, $canonicalEntry);
            } else {
                $this->assertEquals($ifdEntry->getValue(), $entry->getValue());
            }
        }

        unlink('test-output.jpg');
    }

    /**
     * @return \Iterator<string, mixed>
     */
    public static function writeEntryProvider(): \Iterator
    {
        yield 'PEL Byte Read/Write Tests' => [
            [
                new PelEntryByte(0xF001, 0),
                new PelEntryByte(0xF002, 1),
                new PelEntryByte(0xF003, 2),
                new PelEntryByte(0xF004, 253),
                new PelEntryByte(0xF005, 254),
                new PelEntryByte(0xF006, 255),
                new PelEntryByte(0xF007, 0, 1, 2, 253, 254, 255),
                new PelEntryByte(0xF008),
            ],
        ];
        yield 'PEL SByte Read/Write Tests' => [
            [
                new PelEntrySByte(0xF101, - 128),
                new PelEntrySByte(0xF102, - 127),
                new PelEntrySByte(0xF103, - 1),
                new PelEntrySByte(0xF104, 0),
                new PelEntrySByte(0xF105, 1),
                new PelEntrySByte(0xF106, 126),
                new PelEntrySByte(0xF107, 127),
                new PelEntrySByte(0xF108, - 128, - 1, 0, 1, 127),
                new PelEntrySByte(0xF109),
            ],
        ];
        yield 'PEL Short Read/Write Tests' => [
            [
                new PelEntryShort(0xF201, 0),
                new PelEntryShort(0xF202, 1),
                new PelEntryShort(0xF203, 2),
                new PelEntryShort(0xF204, 65533),
                new PelEntryShort(0xF205, 65534),
                new PelEntryShort(0xF206, 65535),
                new PelEntryShort(0xF208, 0, 1, 65534, 65535),
                new PelEntryShort(0xF209),
            ],
        ];
        yield 'PEL SShort Read/Write Tests' => [
            [
                new PelEntrySShort(0xF301, - 32768),
                new PelEntrySShort(0xF302, - 32767),
                new PelEntrySShort(0xF303, - 1),
                new PelEntrySShort(0xF304, 0),
                new PelEntrySShort(0xF305, 1),
                new PelEntrySShort(0xF306, 32766),
                new PelEntrySShort(0xF307, 32767),
                new PelEntrySShort(0xF308, - 32768, - 1, 0, 1, 32767),
                new PelEntrySShort(0xF309),
            ],
        ];
        yield 'PEL Long Read/Write Tests' => [
            [
                new PelEntryLong(0xF401, 0),
                new PelEntryLong(0xF402, 1),
                new PelEntryLong(0xF403, 2),
                new PelEntryLong(0xF404, 4294967293),
                new PelEntryLong(0xF405, 4294967294),
                new PelEntryLong(0xF406, 4294967295),
                new PelEntryLong(0xF408, 0, 1, 4294967295),
                new PelEntryLong(0xF409),
            ],
        ];
        yield 'PEL SLong Read/Write Tests' => [
            [
                new PelEntrySLong(0xF501, - 2147483648),
                new PelEntrySLong(0xF502, - 2147483647),
                new PelEntrySLong(0xF503, - 1),
                new PelEntrySLong(0xF504, 0),
                new PelEntrySLong(0xF505, 1),
                new PelEntrySLong(0xF506, 2147483646),
                new PelEntrySLong(0xF507, 2147483647),
                new PelEntrySLong(0xF508, - 2147483648, 0, 2147483647),
                new PelEntrySLong(0xF509),
            ],
        ];
        yield 'PEL Ascii Read/Write Tests' => [
            [
                new PelEntryAscii(0xF601),
                new PelEntryAscii(0xF602, ''),
                new PelEntryAscii(0xF603, 'Hello World!'),
                new PelEntryAscii(0xF604, "\x00\x01\x02...\xFD\xFE\xFF"),
            ],
        ];
    }

    /**
     * Tests loading and writing back a TIFF image file.
     */
    public function testTiffLoadSave(): void
    {
        $file_uri = __DIR__ . '/images/sample-1.tiff';

        $data = exif_read_data($file_uri);
        $this->assertNotFalse($data);
        $this->assertEquals(1, $data['Orientation']);
        $this->assertEquals(2, $data['PhotometricInterpretation']);

        $tiff = new PelTiff($file_uri);
        $ifd = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd);
        $orientation = $ifd->getEntry(PelTag::ORIENTATION);
        $this->assertNotNull($orientation);
        $this->assertEquals(1, $orientation->getValue());
        $photometric_interpretation = $ifd->getEntry(PelTag::PHOTOMETRIC_INTERPRETATION);
        $this->assertNotNull($photometric_interpretation);
        $this->assertEquals(2, $photometric_interpretation->getValue());

        $bits_per_sample = $ifd->getEntry(PelTag::BITS_PER_SAMPLE);
        $this->assertNotNull($bits_per_sample);
        $this->assertInstanceOf(PelEntryShort::class, $bits_per_sample);
        $this->assertSame([
            8,
            8,
            8,
            8,
        ], $bits_per_sample->getValue());

        $orientation->setValue(4);
        $photometric_interpretation->setValue(4);
        $bits_per_sample->setValueArray([
            7,
            6,
            5,
            4,
        ]);

        $out_uri = __DIR__ . '/images/output.sample-1.tiff';
        $tiff->saveFile($out_uri);

        $data_reload = exif_read_data($out_uri);
        $this->assertNotFalse($data_reload);
        $this->assertEquals(4, $data_reload['Orientation']);
        $this->assertEquals(4, $data_reload['PhotometricInterpretation']);
        $this->assertEquals([
            7,
            6,
            5,
            4,
        ], $data_reload['BitsPerSample']);
        unlink($out_uri);
    }
}
