<?php

declare(strict_types=1);

namespace Pel\Test\imagetests;

use lsolesen\pel\Pel;
use lsolesen\pel\PelEntryAscii;
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

class OlympusC50zTest extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/olympus-c50z.jpg');

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
        $this->assertSame('X-2,C-50Z       ', $entry->getValue());
        $this->assertSame('X-2,C-50Z       ', $entry->getText());

        $entry = $ifd0->getEntry(274); // Orientation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('top - left', $entry->getText());

        $entry = $ifd0->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 144,
            1 => 1,
        ]);
        $this->assertSame('144/1', $entry->getText());

        $entry = $ifd0->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 144,
            1 => 1,
        ]);
        $this->assertSame('144/1', $entry->getText());

        $entry = $ifd0->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Inch', $entry->getText());

        $entry = $ifd0->getEntry(305); // Software
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('28-1012                        ', $entry->getValue());
        $this->assertSame('28-1012                        ', $entry->getText());

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
        $expected = "\x50\x72\x69\x6e\x74\x49\x4d\0\x30\x32\x35\x30\0\0\x14\0\x01\0\x12\0\x12\0\x02\0\x01\0\0\0\x03\0\x88\0\0\0\x07\0\0\0\0\0\x08\0\0\0\0\0\x09\0\0\0\0\0\x0a\0\0\0\0\0\x0b\0\xd0\0\0\0\x0c\0\0\0\0\0\x0d\0\0\0\0\0\x0e\0\xe8\0\0\0\0\x01\x01\0\0\0\x01\x01\xff\0\0\0\x02\x01\x80\0\0\0\x03\x01\x80\0\0\0\x04\x01\x80\0\0\0\x05\x01\x80\0\0\0\x06\x01\x80\0\0\0\x07\x01\x80\x80\x80\0\x10\x01\x80\0\0\0\x09\x11\0\0\x10\x27\0\0\x0b\x0f\0\0\x10\x27\0\0\x97\x05\0\0\x10\x27\0\0\xb0\x08\0\0\x10\x27\0\0\x01\x1c\0\0\x10\x27\0\0\x5e\x02\0\0\x10\x27\0\0\x8b\0\0\0\x10\x27\0\0\xcb\x03\0\0\x10\x27\0\0\xe5\x1b\0\0\x10\x27\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('(undefined)', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(1, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(30, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 1,
            1 => 80,
        ]);
        $this->assertSame('1/80 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 45,
            1 => 10,
        ]);
        $this->assertSame('f/4.5', $entry->getText());

        $entry = $ifd0_0->getEntry(34850); // ExposureProgram
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(5, $entry->getValue());
        $this->assertSame('Creative program (biased toward depth of field)', $entry->getText());

        $entry = $ifd0_0->getEntry(34855); // ISOSpeedRatings
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(80, $entry->getValue());
        $this->assertSame('80', $entry->getText());

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
            0 => 300,
            1 => 100,
        ]);
        $this->assertSame('300/100', $entry->getText());

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
        $this->assertSame(25, $entry->getValue());
        $this->assertSame('Flash fired, auto mode.', $entry->getText());

        $entry = $ifd0_0->getEntry(37386); // FocalLength
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 1883,
            1 => 100,
        ]);
        $this->assertSame('18.8 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x4f\x4c\x59\x4d\x50\0\x01\0\x3e\0\0\x02\x04\0\x03\0\0\0\x24\x0f\0\0\x01\x02\x03\0\x01\0\0\0\x02\0\0\0\x02\x02\x03\0\x01\0\0\0\0\0\0\0\x03\x02\x03\0\x01\0\0\0\0\0\0\0\x04\x02\x05\0\x01\0\0\0\x38\x0f\0\0\x05\x02\x05\0\x01\0\0\0\x40\x0f\0\0\x06\x02\x08\0\x06\0\0\0\x48\x0f\0\0\x07\x02\x02\0\x06\0\0\0\x54\x0f\0\0\x09\x02\x07\0\x20\0\0\0\x5a\x0f\0\0\0\x10\x0a\0\x01\0\0\0\x7c\x0f\0\0\x01\x10\x0a\0\x01\0\0\0\x84\x0f\0\0\x02\x10\x0a\0\x01\0\0\0\x8c\x0f\0\0\x03\x10\x0a\0\x01\0\0\0\x94\x0f\0\0\x04\x10\x03\0\x01\0\0\0\0\0\0\0\x05\x10\x03\0\x02\0\0\0\0\0\0\0\x06\x10\x0a\0\x01\0\0\0\xa4\x0f\0\0\x09\x10\x03\0\x01\0\0\0\x01\0\0\0\x0a\x10\x03\0\x01\0\0\0\0\0\0\0\x0b\x10\x03\0\x01\0\0\0\0\0\0\0\x0c\x10\x05\0\x01\0\0\0\xb8\x0f\0\0\x0d\x10\x03\0\x01\0\0\0\x1c\0\x51\x01\x0e\x10\x03\0\x01\0\0\0\x51\x01\x02\0\x0f\x10\x03\0\x01\0\0\0\x02\0\0\0\x10\x10\x03\0\x01\0\0\0\0\0\0\0\x11\x10\x03\0\x09\0\0\0\x36\x10\0\0\x12\x10\x03\0\x04\0\0\0\x48\x10\0\0\x13\x10\x03\0\x01\0\0\0\0\0\0\0\x14\x10\x03\0\x01\0\0\0\0\0\x01\0\x15\x10\x03\0\x02\0\0\0\x01\0\0\0\x16\x10\x03\0\x01\0\0\0\0\0\x70\x01\x17\x10\x03\0\x02\0\0\0\x70\x01\x40\0\x18\x10\x03\0\x02\0\0\0\x26\x01\x40\0\x1a\x10\x02\0\x20\0\0\0\xdc\x0f\0\0\x1b\x10\x04\0\x01\0\0\0\0\0\0\0\x1c\x10\x04\0\x01\0\0\0\0\0\0\0\x1d\x10\x04\0\x01\0\0\0\xe8\xb8\x03\0\x1e\x10\x04\0\x01\0\0\0\0\0\0\0\x1f\x10\x04\0\x01\0\0\0\0\0\0\0\x20\x10\x04\0\x01\0\0\0\0\0\0\0\x21\x10\x04\0\x01\0\0\0\xb0\x27\0\0\x22\x10\x04\0\x01\0\0\0\x20\x6e\x0f\x04\x23\x10\x0a\0\x01\0\0\0\x1c\x10\0\0\x24\x10\x03\0\x01\0\0\0\x36\0\0\0\x25\x10\x0a\0\x01\0\0\0\x28\x10\0\0\x26\x10\x03\0\x01\0\0\0\0\0\0\0\x27\x10\x03\0\x01\0\0\0\0\0\0\0\x28\x10\x03\0\x01\0\0\0\0\0\x64\x01\x29\x10\x03\0\x01\0\0\0\x02\0\0\x02\x2a\x10\x03\0\x01\0\0\0\0\x02\x18\0\x2b\x10\x03\0\x06\0\0\0\x54\x10\0\0\x2c\x10\x03\0\x02\0\0\0\x0a\0\0\0\x2d\x10\x03\0\x01\0\0\0\0\x08\0\0\x2e\x10\x04\0\x01\0\0\0\0\x0a\0\0\x2f\x10\x04\0\x01\0\0\0\x80\x07\0\0\x30\x10\x03\0\x01\0\0\0\x02\0\0\0\x31\x10\x04\0\x08\0\0\0\x74\x10\0\0\x33\x10\x04\0\xd0\x02\0\0\xa0\x10\0\0\x38\x10\x03\0\x01\0\0\0\0\0\0\0\x3b\x10\x03\0\x01\0\0\0\x21\x01\xbe\x01\x3c\x10\x03\0\x01\0\0\0\xbe\x01\0\0\x3d\x10\x0a\0\x01\0\0\0\xe4\x1b\0\0\x3e\x10\x0a\0\x01\0\0\0\xec\x1b\0\0\0\0\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('758 bytes unknown MakerNote data', $entry->getText());

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
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2560, $entry->getValue());
        $this->assertSame('2560', $entry->getText());

        $entry = $ifd0_0->getEntry(40963); // PixelYDimension
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1920, $entry->getValue());
        $this->assertSame('1920', $entry->getText());

        $entry = $ifd0_0->getEntry(41728); // FileSource
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x03", $entry->getValue());
        $this->assertSame('DSC', $entry->getText());

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
            0 => 100,
            1 => 100,
        ]);
        $this->assertSame('100/100', $entry->getText());

        $entry = $ifd0_0->getEntry(41990); // SceneCaptureType
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Portrait', $entry->getText());

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

        $thumb_data = file_get_contents(__DIR__ . '/olympus-c50z-thumb.jpg');
        $this->assertSame($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        $this->assertCount(0, Pel::getExceptions());
    }
}
