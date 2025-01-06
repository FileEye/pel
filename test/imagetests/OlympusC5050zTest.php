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
use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\TestCase;

class OlympusC5050zTest extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/olympus-c5050z.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        /* The first IFD. */
        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        /* Start of IDF $ifd0. */
        $this->assertCount(11, $ifd0->getEntries());

        $entry = $ifd0->getEntry(270); // ImageDescription
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('OLYMPUS DIGITAL CAMERA         ', $entry->getValue());
        $this->assertSame('OLYMPUS DIGITAL CAMERA         ', $entry->getText());

        $entry = $ifd0->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('OLYMPUS OPTICAL CO.,LTD', $entry->getValue());
        $this->assertSame('OLYMPUS OPTICAL CO.,LTD', $entry->getText());

        $entry = $ifd0->getEntry(272); // Model
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('C5050Z', $entry->getValue());
        $this->assertSame('C5050Z', $entry->getText());

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
        $this->assertSame('v558-83', $entry->getValue());
        $this->assertSame('v558-83', $entry->getText());

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);

        $caught = false;
        try {
            $entry->getValue();
        } catch (PelInvalidArgumentException) {
            $caught = true;
        }
        $this->assertTrue($caught);

        $this->assertSame('0000:00:00 00:00:00', $entry->getText());

        $entry = $ifd0->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('co-sited', $entry->getText());

        $entry = $ifd0->getEntry(50341); // PrintIM
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x50\x72\x69\x6e\x74\x49\x4d\0\x30\x32\x35\x30\0\0\x14\0\x01\0\x14\0\x14\0\x02\0\x01\0\0\0\x03\0\x88\0\0\0\x07\0\0\0\0\0\x08\0\0\0\0\0\x09\0\0\0\0\0\x0a\0\0\0\0\0\x0b\0\xd0\0\0\0\x0c\0\0\0\0\0\x0d\0\0\0\0\0\x0e\0\xe8\0\0\0\0\x01\x01\0\0\0\x01\x01\xff\0\0\0\x02\x01\x83\0\0\0\x03\x01\x83\0\0\0\x04\x01\x80\0\0\0\x05\x01\x83\0\0\0\x06\x01\x83\0\0\0\x07\x01\x80\x80\x80\0\x10\x01\x80\0\0\0\x09\x11\0\0\x10\x27\0\0\x0b\x0f\0\0\x10\x27\0\0\x97\x05\0\0\x10\x27\0\0\xb0\x08\0\0\x10\x27\0\0\x01\x1c\0\0\x10\x27\0\0\x5e\x02\0\0\x10\x27\0\0\x8b\0\0\0\x10\x27\0\0\xcb\x03\0\0\x10\x27\0\0\xe5\x1b\0\0\x10\x27\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('(undefined)', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(1, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(32, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 10,
            1 => 40,
        ]);
        $this->assertSame('1/4 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 26,
            1 => 10,
        ]);
        $this->assertSame('f/2.6', $entry->getText());

        $entry = $ifd0_0->getEntry(34850); // ExposureProgram
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Normal program', $entry->getText());

        $entry = $ifd0_0->getEntry(34855); // ISOSpeedRatings
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(64, $entry->getValue());
        $this->assertSame('64', $entry->getText());

        $entry = $ifd0_0->getEntry(36864); // ExifVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEqualsWithDelta(2.2, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Exif Version 2.2', $entry->getText());

        $entry = $ifd0_0->getEntry(36867); // DateTimeOriginal
        $this->assertInstanceOf(PelEntryTime::class, $entry);

        $caught = false;
        try {
            $entry->getValue();
        } catch (PelInvalidArgumentException) {
            $caught = true;
        }
        $this->assertTrue($caught);

        $this->assertSame('0000:00:00 00:00:00', $entry->getText());

        $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
        $this->assertInstanceOf(PelEntryTime::class, $entry);

        $caught = false;
        try {
            $entry->getValue();
        } catch (PelInvalidArgumentException) {
            $caught = true;
        }
        $this->assertTrue($caught);

        $this->assertSame('0000:00:00 00:00:00', $entry->getText());

        $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01\x02\x03\0", $entry->getValue());
        $this->assertSame('Y Cb Cr -', $entry->getText());

        $entry = $ifd0_0->getEntry(37122); // CompressedBitsPerPixel
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 2,
            1 => 1,
        ]);
        $this->assertSame('2/1', $entry->getText());

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
            0 => 28,
            1 => 10,
        ]);
        $this->assertSame('28/10', $entry->getText());

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
        $this->assertSame(16, $entry->getValue());
        $this->assertSame('Flash did not fire, compulsory flash mode.', $entry->getText());

        $entry = $ifd0_0->getEntry(37386); // FocalLength
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 213,
            1 => 10,
        ]);
        $this->assertSame('21.3 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x4f\x4c\x59\x4d\x50\0\x01\0\x10\0\0\x02\x04\0\x03\0\0\0\xd6\x05\0\0\x01\x02\x03\0\x01\0\0\0\x01\0\0\0\x02\x02\x03\0\x01\0\0\0\0\0\0\0\x03\x02\x03\0\x01\0\0\0\0\0\0\0\x04\x02\x05\0\x01\0\0\0\xe2\x05\0\0\x05\x02\x05\0\x01\0\0\0\xea\x05\0\0\x06\x02\x08\0\x06\0\0\0\xf2\x05\0\0\x07\x02\x02\0\x08\0\0\0\xfe\x05\0\0\x08\x02\x02\0\x34\0\0\0\x06\x06\0\0\x09\x02\x07\0\x20\0\0\0\x42\x06\0\0\0\x03\x03\0\x01\0\0\0\0\0\0\0\x01\x03\x03\0\x01\0\0\0\0\0\0\0\x02\x03\x03\0\x01\0\0\0\x01\0\0\0\x03\x03\x03\0\x01\0\0\0\0\0\0\0\x04\x03\x03\0\x01\0\0\0\0\0\0\0\0\x0f\x07\0\xfe\0\0\0\x62\x06\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x64\0\0\0\x64\0\0\0\x88\x03\0\0\x64\0\0\0\x03\0\x0d\0\x12\0\x19\0\x38\0\x49\0\x53\x58\x35\x35\x38\0\0\0\x5b\x70\x69\x63\x74\x75\x72\x65\x49\x6e\x66\x6f\x5d\x20\x52\x65\x73\x6f\x6c\x75\x74\x69\x6f\x6e\x3d\x31\x20\x5b\x43\x61\x6d\x65\x72\x61\x20\x49\x6e\x66\x6f\x5d\x20\x54\x79\x70\x65\x3d\x53\x58\x35\x35\x38\0\0\0\0\0\0\0\0\0\x4f\x4c\x59\x4d\x50\x55\x53\x20\x44\x49\x47\x49\x54\x41\x4c\x20\x43\x41\x4d\x45\x52\x41\0\xff\xff\xff\xff\xff\xff\xff\xff\xff\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x31\xcf\x13\0\0\0\0\x01\xe8\x48\0\0\x03\xb5\0\x01\xe5\xd3\0\0\x14\x55\0\0\x14\x55\x01\0\x1f\0\x0b\xa9\0\x12\x03\x31\x01\0\x01\xc0\x01\xe6\x01\xfc\x01\xe9\xd0\0\0\xe8\x11\x4a\0\0\x14\x14\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x0b\x1c\0\0\x40\0\x0b\x19\0\0\0\x67\0\0\0\x67\0\xe8\x16\x49\0\0\0\x12\0\x03\xcb\xa6\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\x19\x61\x12\x31\0\x76\x01\x3f\x01\xcf\x02\x59\x01\x15\x02\0\x02\x9c\0\x30\x0b\x95\x0d\x14\x02\0\0\xc9\x03\x8b\x02\0\x01\x6e\x03\x93\x03\xed\x01\x72\x01\0\xd0\x5b\0\x0c\0\x0c\0\x02\x03\x52\0\x01\0\0\0\0\0\x09\0\x32\0\x0a\0\0\0\x01\0\x48\0\x87\0\x64\0\x78\x0e\x0e\x0e\x0e\x11\x11\x11\x11\0\0\0\0\0\0\x14\x1d\x0e\x17\0\0\x0a\0\x1b\0\0\x1a\0\0\0\0\x01\0\x32\x0f\x42\x40\x0f\0\0\x64\x2a\x20\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('600 bytes unknown MakerNote data', $entry->getText());

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
        $this->assertSame(640, $entry->getValue());
        $this->assertSame('640', $entry->getText());

        $entry = $ifd0_0->getEntry(40963); // PixelYDimension
        $this->assertInstanceOf(PelEntryLong::class, $entry);
        $this->assertSame(480, $entry->getValue());
        $this->assertSame('480', $entry->getText());

        $entry = $ifd0_0->getEntry(41728); // FileSource
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x03", $entry->getValue());
        $this->assertSame('DSC', $entry->getText());

        $entry = $ifd0_0->getEntry(41729); // SceneType
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01", $entry->getValue());
        $this->assertSame('Directly photographed', $entry->getText());

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
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('Manual white balance', $entry->getText());

        $entry = $ifd0_0->getEntry(41988); // DigitalZoomRatio
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 0,
            1 => 100,
        ]);
        $this->assertSame('0/100', $entry->getText());

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

        $thumb_data = file_get_contents(__DIR__ . '/olympus-c5050z-thumb.jpg');
        $this->assertSame($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        $this->assertCount(0, Pel::getExceptions());
    }
}
