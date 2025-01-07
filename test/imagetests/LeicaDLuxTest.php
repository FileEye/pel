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
use lsolesen\pel\PelEntryVersion;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\TestCase;

class LeicaDLuxTest extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/leica-d-lux.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        /* The first IFD. */
        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        /* Start of IDF $ifd0. */
        $this->assertCount(10, $ifd0->getEntries());

        $entry = $ifd0->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('LEICA', $entry->getValue());
        $this->assertSame('LEICA', $entry->getText());

        $entry = $ifd0->getEntry(272); // Model
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('D-LUX', $entry->getValue());
        $this->assertSame('D-LUX', $entry->getText());

        $entry = $ifd0->getEntry(274); // Orientation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('top - left', $entry->getText());

        $entry = $ifd0->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 72,
            1 => 1,
        ]);
        $this->assertSame('72/1', $entry->getText());

        $entry = $ifd0->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 72,
            1 => 1,
        ]);
        $this->assertSame('72/1', $entry->getText());

        $entry = $ifd0->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Inch', $entry->getText());

        $entry = $ifd0->getEntry(305); // Software
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('Ver1.06', $entry->getValue());
        $this->assertSame('Ver1.06', $entry->getText());

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1099994128', $entry->getValue());
        $this->assertSame('2004:11:09 09:55:28', $entry->getText());

        $entry = $ifd0->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('co-sited', $entry->getText());

        $entry = $ifd0->getEntry(50341); // PrintIM
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x50\x72\x69\x6e\x74\x49\x4d\0\x30\x32\x35\x30\0\0\x0e\0\x01\0\x16\0\x16\0\x02\0\0\0\0\0\x03\0\x64\0\0\0\x07\0\0\0\0\0\x08\0\0\0\0\0\x09\0\0\0\0\0\x0a\0\0\0\0\0\x0b\0\xac\0\0\0\x0c\0\0\0\0\0\x0d\0\0\0\0\0\x0e\0\xc4\0\0\0\0\x01\x05\0\0\0\x01\x01\x01\0\0\0\x10\x01\x80\0\0\0\x09\x11\0\0\x10\x27\0\0\x0b\x0f\0\0\x10\x27\0\0\x37\x05\0\0\x10\x27\0\0\xb0\x08\0\0\x10\x27\0\0\x01\x1c\0\0\x10\x27\0\0\x5e\x02\0\0\x10\x27\0\0\x8b\0\0\0\x10\x27\0\0\xcb\x03\0\0\x10\x27\0\0\xe5\x1b\0\0\x10\x27\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('(undefined)', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(1, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(37, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 10,
            1 => 1000,
        ]);
        $this->assertSame('1/100 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 97,
            1 => 10,
        ]);
        $this->assertSame('f/9.7', $entry->getText());

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
        $this->assertEqualsWithDelta(2.2, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Exif Version 2.2', $entry->getText());

        $entry = $ifd0_0->getEntry(36867); // DateTimeOriginal
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1099994128', $entry->getValue());
        $this->assertSame('2004:11:09 09:55:28', $entry->getText());

        $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1099994128', $entry->getValue());
        $this->assertSame('2004:11:09 09:55:28', $entry->getText());

        $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame($entry->getValue(), "\x01\x02\x03\0" . '');
        $this->assertSame('Y Cb Cr -', $entry->getText());

        $entry = $ifd0_0->getEntry(37122); // CompressedBitsPerPixel
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 21,
            1 => 10,
        ]);
        $this->assertSame('21/10', $entry->getText());

        $entry = $ifd0_0->getEntry(37377); // ShutterSpeedValue
        $this->assertInstanceOf(PelEntrySRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 6644,
            1 => 1000,
        ]);
        $this->assertSame('6644/1000 sec. (APEX: 10)', $entry->getText());

        $entry = $ifd0_0->getEntry(37378); // ApertureValue
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 66,
            1 => 10,
        ]);
        $this->assertSame('f/9.8', $entry->getText());

        $entry = $ifd0_0->getEntry(37380); // ExposureBiasValue
        $this->assertInstanceOf(PelEntrySRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 0,
            1 => 100,
        ]);
        $this->assertSame('0.0', $entry->getText());

        $entry = $ifd0_0->getEntry(37381); // MaxApertureValue
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 30,
            1 => 10,
        ]);
        $this->assertSame('30/10', $entry->getText());

        $entry = $ifd0_0->getEntry(37383); // MeteringMode
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(3, $entry->getValue());
        $this->assertSame('Spot', $entry->getText());

        $entry = $ifd0_0->getEntry(37384); // LightSource
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('Daylight', $entry->getText());

        $entry = $ifd0_0->getEntry(37385); // Flash
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(24, $entry->getValue());
        $this->assertSame('Flash did not fire, auto mode.', $entry->getText());

        $entry = $ifd0_0->getEntry(37386); // FocalLength
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 88,
            1 => 10,
        ]);
        $this->assertSame('8.8 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x4c\x45\x49\x43\x41\0\0\0\x0b\0\x01\0\x03\0\x01\0\0\0\x03\0\0\0\x02\0\x07\0\x04\0\0\0\x30\x31\x30\x30\x03\0\x03\0\x01\0\0\0\x01\0\0\0\x07\0\x03\0\x01\0\0\0\x01\0\0\0\x0f\0\x01\0\x02\0\0\0\0\x10\0\0\x1a\0\x03\0\x01\0\0\0\x02\0\0\0\x1c\0\x03\0\x01\0\0\0\x02\0\0\0\x1f\0\x03\0\x01\0\0\0\x01\0\0\0\x20\0\x03\0\x01\0\0\0\x02\0\0\0\x21\0\x07\0\x72\0\0\0\x5e\x04\0\0\x22\0\x03\0\x01\0\0\0\0\0\0\0\x57\x42\x02\x62\x01\x36\0\xb9\0\x92\0\xa8\0\x8c\0\xdc\0\xa0\0\x4b\x01\x9c\x01\xd5\x02\x3b\x01\x1c\x02\x44\x01\x1d\x0b\xab\x1b\x6c\x16\xb8\0\0\x41\x46\x01\x96\x04\x8e\x20\x06\0\0\x53\x54\0\0\0\0\0\0\x01\x24\0\0\0\0\0\0\0\x05\0\x05\0\0\x41\x45\x06\xa7\0\x4c\0\0\x02\x28\x09\x01\0\x93\x01\x44\0\0\x08\x08\x08\x08\x03\x03\0\0\0\xe0\0\0\x20\x20\0\x0a\0\0\x45\x50\x01\x0a\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('256 bytes unknown MakerNote data', $entry->getText());

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
        $this->assertSame(640, $entry->getValue());
        $this->assertSame('640', $entry->getText());

        $entry = $ifd0_0->getEntry(40963); // PixelYDimension
        $this->assertInstanceOf(PelEntryLong::class, $entry);
        $this->assertSame(480, $entry->getValue());
        $this->assertSame('480', $entry->getText());

        $entry = $ifd0_0->getEntry(41495); // SensingMethod
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('One-chip color area sensor', $entry->getText());

        $entry = $ifd0_0->getEntry(41728); // FileSource
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x03", $entry->getValue());
        $this->assertSame('DSC', $entry->getText());

        $entry = $ifd0_0->getEntry(41729); // SceneType
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01", $entry->getValue());
        $this->assertSame('Directly photographed', $entry->getText());

        $entry = $ifd0_0->getEntry(41730); // CFAPattern
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\0\x02\0\x02\0\x01\x01\x02", $entry->getValue());
        $this->assertSame('(undefined)', $entry->getText());

        $entry = $ifd0_0->getEntry(41985); // CustomRendered
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Normal process', $entry->getText());

        $entry = $ifd0_0->getEntry(41986); // ExposureMode
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Auto exposure', $entry->getText());

        $entry = $ifd0_0->getEntry(41987); // WhiteBalance
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Auto white balance', $entry->getText());

        $entry = $ifd0_0->getEntry(41988); // DigitalZoomRatio
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 0,
            1 => 10,
        ]);
        $this->assertSame('0/10', $entry->getText());

        $entry = $ifd0_0->getEntry(41989); // FocalLengthIn35mmFilm
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(53, $entry->getValue());
        $this->assertSame('53', $entry->getText());

        $entry = $ifd0_0->getEntry(41990); // SceneCaptureType
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Standard', $entry->getText());

        $entry = $ifd0_0->getEntry(41991); // GainControl
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Normal', $entry->getText());

        $entry = $ifd0_0->getEntry(41992); // Contrast
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Normal', $entry->getText());

        $entry = $ifd0_0->getEntry(41993); // Saturation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Normal', $entry->getText());

        $entry = $ifd0_0->getEntry(41994); // Sharpness
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Normal', $entry->getText());

        $entry = $ifd0_0->getEntry(41996); // SubjectDistanceRange
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Close view', $entry->getText());

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
        $this->assertCount(5, $ifd1->getEntries());

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

        $entry = $ifd1->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('co-sited', $entry->getText());

        /* Sub IFDs of $ifd1. */
        $this->assertCount(0, $ifd1->getSubIfds());

        $thumb_data = file_get_contents(__DIR__ . '/leica-d-lux-thumb.jpg');
        $this->assertSame($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        $this->assertCount(0, Pel::getExceptions());
    }
}
