<?php

declare(strict_types=1);

namespace Pel\Test\imagetests;

use lsolesen\pel\Pel;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelEntrySRational;
use lsolesen\pel\PelEntryTime;
use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelEntryVersion;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\TestCase;

class NikonE950Test extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/nikon-e950.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        /* The first IFD. */
        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        /* Start of IDF $ifd0. */
        $this->assertCount(10, $ifd0->getEntries());

        $entry = $ifd0->getEntry(270); // ImageDescription
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('          ', $entry->getValue());
        $this->assertSame('          ', $entry->getText());

        $entry = $ifd0->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('NIKON', $entry->getValue());
        $this->assertSame('NIKON', $entry->getText());

        $entry = $ifd0->getEntry(272); // Model
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('E950', $entry->getValue());
        $this->assertSame('E950', $entry->getText());

        $entry = $ifd0->getEntry(274); // Orientation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('top - left', $entry->getText());

        $entry = $ifd0->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 300,
            1 => 1,
        ]);
        $this->assertSame('300/1', $entry->getText());

        $entry = $ifd0->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 300,
            1 => 1,
        ]);
        $this->assertSame('300/1', $entry->getText());

        $entry = $ifd0->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Inch', $entry->getText());

        $entry = $ifd0->getEntry(305); // Software
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('v981p-78', $entry->getValue());
        $this->assertSame('v981p-78', $entry->getText());

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('978276013', $entry->getValue());
        $this->assertSame('2000:12:31 15:20:13', $entry->getText());

        $entry = $ifd0->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('co-sited', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(1, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(23, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 10,
            1 => 1120,
        ]);
        $this->assertSame('1/112 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 60,
            1 => 10,
        ]);
        $this->assertSame('f/6.0', $entry->getText());

        $entry = $ifd0_0->getEntry(34850); // ExposureProgram
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Normal program', $entry->getText());

        $entry = $ifd0_0->getEntry(34855); // ISOSpeedRatings
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(80, $entry->getValue());
        $this->assertSame('80', $entry->getText());

        $entry = $ifd0_0->getEntry(36864); // ExifVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEqualsWithDelta(2.1, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Exif Version 2.1', $entry->getText());

        $entry = $ifd0_0->getEntry(36867); // DateTimeOriginal
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('978276013', $entry->getValue());
        $this->assertSame('2000:12:31 15:20:13', $entry->getText());

        $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('978276013', $entry->getValue());
        $this->assertSame('2000:12:31 15:20:13', $entry->getText());

        $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01\x02\x03\0", $entry->getValue());
        $this->assertSame('Y Cb Cr -', $entry->getText());

        $entry = $ifd0_0->getEntry(37122); // CompressedBitsPerPixel
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 4,
            1 => 1,
        ]);
        $this->assertSame('4/1', $entry->getText());

        $entry = $ifd0_0->getEntry(37380); // ExposureBiasValue
        $this->assertInstanceOf(PelEntrySRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 0,
            1 => 10,
        ]);
        $this->assertSame('0.0', $entry->getText());

        $entry = $ifd0_0->getEntry(37381); // MaxApertureValue
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 26,
            1 => 10,
        ]);
        $this->assertSame('26/10', $entry->getText());

        $entry = $ifd0_0->getEntry(37383); // MeteringMode
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(5, $entry->getValue());
        $this->assertSame('Pattern', $entry->getText());

        $entry = $ifd0_0->getEntry(37384); // LightSource
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Unknown', $entry->getText());

        $entry = $ifd0_0->getEntry(37385); // Flash
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Flash did not fire.', $entry->getText());

        $entry = $ifd0_0->getEntry(37386); // FocalLength
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 158,
            1 => 10,
        ]);
        $this->assertSame('15.8 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x4e\x69\x6b\x6f\x6e\0\x01\0\x0b\0\x02\0\x02\0\x06\0\0\0\x26\x04\0\0\x03\0\x03\0\x01\0\0\0\x0c\0\0\0\x04\0\x03\0\x01\0\0\0\x01\0\0\0\x05\0\x03\0\x01\0\0\0\0\0\0\0\x06\0\x03\0\x01\0\0\0\0\0\0\0\x07\0\x03\0\x01\0\0\0\0\0\0\0\x08\0\x05\0\x01\0\0\0\x2c\x04\0\0\x09\0\x02\0\x14\0\0\0\x34\x04\0\0\x0a\0\x05\0\x01\0\0\0\x48\x04\0\0\x0b\0\x03\0\x01\0\0\0\0\0\0\0\0\x0f\x04\0\x1e\0\0\0\x50\x04\0\0\0\0\0\0\x30\x38\x2e\x30\x30\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x64\0\0\0\x01\x3e\0\x80\x01\x58\0\0\0\0\xff\x01\0\0\0\0\x0c\xe5\x10\x8c\0\0\0\0\x0a\x5b\0\0\x18\x6a\0\0\x23\x04\0\0\x11\x16\0\0\x11\x16\0\0\x1f\x05\x0c\x9f\0\x2f\0\0\0\0\x01\xcb\x02\x27\x02\x7b\x02\xd8\x03\x6a\x08\x5c\0\0\0\0\x10\x0e\x15\0\0\x01\x60\0\0\x30\0\0\0\x10\0\0\x5b\x18\x02\0\x48\x04\x16\x68\0\x0b\x58\x29\0\x3f\0\0\x15\x19\x15\x1a\x0f\xe1\x42\0\xff\0\x4f\x5d\x32\x0c\xa1\x02\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('308 bytes unknown MakerNote data', $entry->getText());

        $entry = $ifd0_0->getEntry(37510); // UserComment
        $this->assertInstanceOf(PelEntryUserComment::class, $entry);
        $this->assertSame('                                                                                                                     ', $entry->getValue());
        $this->assertSame('                                                                                                                     ', $entry->getText());

        $entry = $ifd0_0->getEntry(40960); // FlashPixVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEquals(1, $entry->getValue());
        $this->assertSame('FlashPix Version 1.0', $entry->getText());

        $entry = $ifd0_0->getEntry(40961); // ColorSpace
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('sRGB', $entry->getText());

        $entry = $ifd0_0->getEntry(40962); // PixelXDimension
        $this->assertInstanceOf(PelEntryLong::class, $entry);
        $this->assertSame(1600, $entry->getValue());
        $this->assertSame('1600', $entry->getText());

        $entry = $ifd0_0->getEntry(40963); // PixelYDimension
        $this->assertInstanceOf(PelEntryLong::class, $entry);
        $this->assertSame(1200, $entry->getValue());
        $this->assertSame('1200', $entry->getText());

        $entry = $ifd0_0->getEntry(41728); // FileSource
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x03", $entry->getValue());
        $this->assertSame('DSC', $entry->getText());

        $entry = $ifd0_0->getEntry(41729); // SceneType
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01", $entry->getValue());
        $this->assertSame('Directly photographed', $entry->getText());

        /* Sub IFDs of $ifd0_0. */
        $this->assertCount(1, $ifd0_0->getSubIfds());
        $ifd0_0_0 = $ifd0_0->getSubIfd(4); // IFD Interoperability
        $this->assertInstanceOf(PelIfd::class, $ifd0_0_0);

        /* Start of IDF $ifd0_0_0. */
        $this->assertCount(2, $ifd0_0_0->getEntries());

        $entry = $ifd0_0_0->getEntry(1); // InteroperabilityIndex
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('R98', $entry->getValue());
        $this->assertSame('R98', $entry->getText());

        $entry = $ifd0_0_0->getEntry(2); // InteroperabilityVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEquals(1, $entry->getValue());
        $this->assertSame('Interoperability Version 1.0', $entry->getText());

        /* Sub IFDs of $ifd0_0_0. */
        $this->assertCount(0, $ifd0_0_0->getSubIfds());

        $this->assertSame('', $ifd0_0_0->getThumbnailData());

        /* Next IFD. */
        $ifd0_0_1 = $ifd0_0_0->getNextIfd();
        $this->assertNull($ifd0_0_1);
        /* End of IFD $ifd0_0_0. */

        $this->assertSame('', $ifd0_0->getThumbnailData());

        /* Next IFD. */
        $ifd0_1 = $ifd0_0->getNextIfd();
        $this->assertNull($ifd0_1);
        /* End of IFD $ifd0_0. */

        $this->assertSame('', $ifd0->getThumbnailData());

        /* Next IFD. */
        $ifd1 = $ifd0->getNextIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd1);
        /* End of IFD $ifd0. */

        /* Start of IDF $ifd1. */
        $this->assertCount(4, $ifd1->getEntries());

        $entry = $ifd1->getEntry(259); // Compression
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(6, $entry->getValue());
        $this->assertSame('JPEG compression', $entry->getText());

        $entry = $ifd1->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 300,
            1 => 1,
        ]);
        $this->assertSame('300/1', $entry->getText());

        $entry = $ifd1->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 300,
            1 => 1,
        ]);
        $this->assertSame('300/1', $entry->getText());

        $entry = $ifd1->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Inch', $entry->getText());

        /* Sub IFDs of $ifd1. */
        $this->assertCount(0, $ifd1->getSubIfds());

        $thumb_data = file_get_contents(__DIR__ . '/nikon-e950-thumb.jpg');
        $this->assertSame($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        $this->assertCount(0, Pel::getExceptions());
    }
}
