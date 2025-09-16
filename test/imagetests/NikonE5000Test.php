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

class NikonE5000Test extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/nikon-e5000.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        /* The first IFD. */
        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        /* Start of IDF $ifd0. */
        $this->assertCount(9, $ifd0->getEntries());

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
        $this->assertSame('E5000', $entry->getValue());
        $this->assertSame('E5000', $entry->getText());

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
        $this->assertSame('E5000v1.6', $entry->getValue());
        $this->assertSame('E5000v1.6', $entry->getText());

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1021992832', $entry->getValue());
        $this->assertSame('2002:05:21 14:53:52', $entry->getText());

        $entry = $ifd0->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('centered', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(2, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(22, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 1642036,
            1 => 100000000,
        ]);
        $this->assertSame('1/60 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 28,
            1 => 10,
        ]);
        $this->assertSame('f/2.8', $entry->getText());

        $entry = $ifd0_0->getEntry(34850); // ExposureProgram
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Normal program', $entry->getText());

        $entry = $ifd0_0->getEntry(34855); // ISOSpeedRatings
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(100, $entry->getValue());
        $this->assertSame('100', $entry->getText());

        $entry = $ifd0_0->getEntry(36864); // ExifVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEqualsWithDelta(2.1, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Exif Version 2.1', $entry->getText());

        $entry = $ifd0_0->getEntry(36867); // DateTimeOriginal
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1021992832', $entry->getValue());
        $this->assertSame('2002:05:21 14:53:52', $entry->getText());

        $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1021992832', $entry->getValue());
        $this->assertSame('2002:05:21 14:53:52', $entry->getText());

        $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01\x02\x03\0", $entry->getValue());
        $this->assertSame('Y Cb Cr -', $entry->getText());

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
            0 => 3,
            1 => 1,
        ]);
        $this->assertSame('3/1', $entry->getText());

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
            0 => 71,
            1 => 10,
        ]);
        $this->assertSame('7.1 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x15\0\x01\0\x07\0\x04\0\0\0\0\x01\0\0\x02\0\x03\0\x02\0\0\0\0\0\0\0\x03\0\x02\0\x06\0\0\0\xbc\x03\0\0\x04\0\x02\0\x08\0\0\0\xc2\x03\0\0\x05\0\x02\0\x0d\0\0\0\xca\x03\0\0\x06\0\x02\0\x07\0\0\0\xd8\x03\0\0\x07\0\x02\0\x07\0\0\0\xe0\x03\0\0\x08\0\x02\0\x0d\0\0\0\xe8\x03\0\0\x0a\0\x05\0\x01\0\0\0\xf6\x03\0\0\x0f\0\x02\0\x07\0\0\0\xfe\x03\0\0\x11\0\x04\0\x01\0\0\0\x14\x05\0\0\x80\0\x02\0\x0e\0\0\0\x06\x04\0\0\x82\0\x02\0\x0d\0\0\0\x14\x04\0\0\x85\0\x05\0\x01\0\0\0\x22\x04\0\0\x86\0\x05\0\x01\0\0\0\x2a\x04\0\0\x88\0\x07\0\x04\0\0\0\0\0\0\0\x8f\0\x02\0\x11\0\0\0\x32\x04\0\0\x94\0\x08\0\x01\0\0\0\0\0\0\0\x95\0\x02\0\x05\0\0\0\x44\x04\0\0\0\x0e\x07\0\xca\0\0\0\x4a\x04\0\0\x10\x0e\x04\0\x01\0\0\0\x72\x05\0\0\0\0\0\0\x43\x4f\x4c\x4f\x52\0\x46\x49\x4e\x45\x20\x20\x20\0\x41\x55\x54\x4f\x20\x20\x20\x20\x20\x20\x20\x20\0\x31\x41\x55\x54\x4f\x20\x20\0\0\x41\x46\x2d\x43\x20\x20\0\x3a\x4e\x4f\x52\x4d\x41\x4c\x20\x20\x20\x20\x20\x20\0\0\x80\x22\0\0\xe8\x03\0\0\x41\x55\x54\x4f\x20\x20\0\0\x41\x55\x54\x4f\x20\x20\x20\x20\x20\x20\x20\x20\x20\0\x4f\x46\x46\x20\x20\x20\x20\x20\x20\x20\x20\x20\0\x20\0\0\0\0\0\0\0\0\x01\0\0\0\x01\0\0\0\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\0\x20\x4f\x46\x46\x20\0\x20\x50\x72\x69\x6e\x74\x49\x4d\0\x30\x31\x30\x30\0\0\x0d\0\x01\0\x16\0\x16\0\x02\0\x01\0\0\0\x03\0\x5e\0\0\0\x07\0\0\0\0\0\x08\0\0\0\0\0\x09\0\0\0\0\0\x0a\0\0\0\0\0\x0b\0\xa6\0\0\0\x0c\0\0\0\0\0\x0d\0\0\0\0\0\x0e\0\xbe\0\0\0\0\x01\x05\0\0\0\x01\x01\x01\0\0\0\x09\x11\0\0\x10\x27\0\0\x0b\x0f\0\0\x10\x27\0\0\x97\x05\0\0\x10\x27\0\0\xb0\x08\0\0\x10\x27\0\0\x01\x1c\0\0\x10\x27\0\0\x5e\x02\0\0\x10\x27\0\0\x8b\0\0\0\x10\x27\0\0\xcb\x03\0\0\x10\x27\0\0\xe5\x1b\0\0\x10\x27\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x06\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('604 bytes unknown MakerNote data', $entry->getText());

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
        $this->assertCount(0, $ifd0_0->getSubIfds());

        $this->assertSame('', $ifd0_0->getThumbnailData());

        /* Next IFD. */
        $ifd0_1 = $ifd0_0->getNextIfd();
        $this->assertNull($ifd0_1);
        /* End of IFD $ifd0_0. */
        $ifd0_1 = $ifd0->getSubIfd(3); // IFD GPS
        $this->assertInstanceOf(PelIfd::class, $ifd0_1);

        /* Start of IDF $ifd0_1. */
        $this->assertCount(0, $ifd0_1->getEntries());

        /* Sub IFDs of $ifd0_1. */
        $this->assertCount(0, $ifd0_1->getSubIfds());

        $this->assertSame('', $ifd0_1->getThumbnailData());

        /* Next IFD. */
        $ifd0_2 = $ifd0_1->getNextIfd();
        $this->assertNull($ifd0_2);
        /* End of IFD $ifd0_1. */

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
            0 => 72,
            1 => 1,
        ]);
        $this->assertSame('72/1', $entry->getText());

        $entry = $ifd1->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 72,
            1 => 1,
        ]);
        $this->assertSame('72/1', $entry->getText());

        $entry = $ifd1->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Inch', $entry->getText());

        /* Sub IFDs of $ifd1. */
        $this->assertCount(0, $ifd1->getSubIfds());

        $thumb_data = file_get_contents(__DIR__ . '/nikon-e5000-thumb.jpg');
        $this->assertSame($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        $exceptions = Pel::getExceptions();
        $this->assertCount(1, Pel::getExceptions());
        $this->assertEquals('Found trailing content after EOI: 1396 bytes', $exceptions[0]->getMessage());
    }
}
