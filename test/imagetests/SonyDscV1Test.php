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

class SonyDscV1Test extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/sony-dsc-v1.jpg');

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
        $this->assertSame('                               ', $entry->getValue());
        $this->assertSame('                               ', $entry->getText());

        $entry = $ifd0->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('SONY', $entry->getValue());
        $this->assertSame('SONY', $entry->getText());

        $entry = $ifd0->getEntry(272); // Model
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('DSC-V1', $entry->getValue());
        $this->assertSame('DSC-V1', $entry->getText());

        $entry = $ifd0->getEntry(274); // Orientation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(6, $entry->getValue());
        $this->assertSame('right - top', $entry->getText());

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

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1089482993', $entry->getValue());
        $this->assertSame('2004:07:10 18:09:53', $entry->getText());

        $entry = $ifd0->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('co-sited', $entry->getText());

        $entry = $ifd0->getEntry(50341); // PrintIM
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x50\x72\x69\x6e\x74\x49\x4d\0\x30\x32\x35\x30\0\0\x02\0\x02\0\x01\0\0\0\x01\x01\0\0\0\0", $entry->getValue());
        $this->assertSame('(undefined)', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(1, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(26, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 10,
            1 => 600,
        ]);
        $this->assertSame('1/60 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 32,
            1 => 10,
        ]);
        $this->assertSame('f/3.2', $entry->getText());

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
        $this->assertSame('1089482993', $entry->getValue());
        $this->assertSame('2004:07:10 18:09:53', $entry->getText());

        $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1089482993', $entry->getValue());
        $this->assertSame('2004:07:10 18:09:53', $entry->getText());

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
            0 => 7,
            1 => 10,
        ]);
        $this->assertSame('+0.7', $entry->getText());

        $entry = $ifd0_0->getEntry(37381); // MaxApertureValue
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 48,
            1 => 16,
        ]);
        $this->assertSame('48/16', $entry->getText());

        $entry = $ifd0_0->getEntry(37383); // MeteringMode
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Center-Weighted Average', $entry->getText());

        $entry = $ifd0_0->getEntry(37384); // LightSource
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Unknown', $entry->getText());

        $entry = $ifd0_0->getEntry(37385); // Flash
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(31, $entry->getValue());
        $this->assertSame('Flash fired, auto mode, return light detected.', $entry->getText());

        $entry = $ifd0_0->getEntry(37386); // FocalLength
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 139,
            1 => 10,
        ]);
        $this->assertSame('13.9 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x53\x4f\x4e\x59\x20\x44\x53\x43\x20\0\0\0\x0c\0\x01\x90\x07\0\x94\0\0\0\x40\x03\0\0\x02\x90\x07\0\xc8\0\0\0\xd4\x03\0\0\x03\x90\x07\0\xc8\0\0\0\x9c\x04\0\0\x04\x90\x07\0\x1a\0\0\0\x64\x05\0\0\x05\x90\x07\0\x78\0\0\0\x7e\x05\0\0\x06\x90\x07\0\xfc\0\0\0\xf6\x05\0\0\x07\x90\x07\0\xc8\0\0\0\xf2\x06\0\0\x08\x90\x07\0\xc8\0\0\0\xba\x07\0\0\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xdf\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\0\0\0\0\0\0\0\x0c\0\0\0\x05\0\x6e\x7e\xa0\0\xe4\x89\x2c\0\xe7\x8f\0\0\0\0\x0c\0\0\0\0\0\0\0\x0c\0\x01\xe7\x41\xff\0\0\0\0\0\x08\x07\x02\x09\xfe\0\x60\x1f\x08\xdf\x5e\xf0\xff\x4a\xfe\0\0\0\0\x4a\x30\0\x88\x1f\x05\x4a\x30\x4a\x88\0\x70\0\0\x70\0\0\x01\xb8\0\x5e\0\x81\0\0\x56\x0d\0\0\0\x63\0\0\xd8\xc4\0\0\xd2\x90\x88\x1f\xbe\0\x70\0\0\xc8\0\xbe\x5e\0\xd8\x6f\0\0\x40\x50\0\x01\0\0\0\0\0\0\0\0\0\0\0\0\0\x04\0\x1b\0\0\0\x07\0\0\x01\xb8\x08\0\x8a\xb7\0\x74\x91\xf0\0\x8c\x75\x3d\0\0\0\xd8\0\0\0\0\0\0\0\0\0\0\0\0\x05\xc4\0\0\xd3\0\xc3\x74\xd7\x93\xd8\x67\x05\xc4\xd7\x22\x30\x63\0\0\0\0\xd7\xa7\xbe\x21\0\0\0\0\0\0\0\0\0\0\x01\x14\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x01\x14\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x0e\x08\xe3\0\x7d\0\x28\x8a\x0e\0\x5e\0\0\xa8\0\xbe\xc0\xbe\xc0\xd7\x93\x40\0\0\0\xdc\xdc\x73\x46\0\x92\0\xb7\0\x23\0\x23\0\x47\0\x82\0\x06\0\x55\xef\x10\x24\x31\xef\xc3\x79\x73\xef\x10\x79\x8a\x79\x05\xc4\xef\x5b\xc5\x18\xc5\x18\x7d\xa5\x88\xb7\xcf\0\0\x01\x69\0\0\0\0\0\0\0\0\xff\0\0\xf1\0\x07\0\x47\xff\0\x01\x15\0\x6a\0\x20\x23\0\x5e\0\0\0\x56\0\x08\xdd\x01\xc5\x01\0\x01\0\x04\x80\x1b\xf2\x45\xdd\x38\x54\0\x69\x24\xdc\x0c\x63\xf0\xde\xec\x23\x28\x47\xf0\xa8\x92\x45\xef\x21\xc5\x49\x58\x2f\0\xc3\x46\xfe\x59\x5a\x9f\xfe\0\x07\xf3\x52\xa0\x6e\x51\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\xbd\x20\x08\x1b\0\x01\xdc\xdc\xbc\x5f\x10\0\0\x08\xec\x08\xe7\x08\x04\x08\x51\0\xdc\0\xe3\0\xe3\0\xc2\0\0\0\0\0\0\0\0\x01\x01\0\0\0\0\0\x7d\0\0\0\0\0\x7b\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x01\0\x01\0\0\0\0\0\x01\0\x01\0\x08\0\x01\0\x01\xe5\x91\x51\x08\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\xff\xff\0\xff\0\0\0\0\0\0\0\0\xb3\xe5\0\0\xff\xff\0\0\xcd\x95\x51\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x7d\0\x40\0\x1b\0\0\0\x01\xd8\x2b\xd8\x47\x5e\xa7\xbb\x9e\x0e\x8b\x20\x08\xa0\xfd\xd8\xf4\x5e\x2b\x56\x69\x88\xe8\xd8\xaa\xb6\x60\x6a\x98\x0e\x53\x56\x8e\x56\x8d\x05\xe1\xd8\x5d\x20\xcc\xd3\x58\x5e\xb6\x04\xb7\xe7\xe9\xcd\x57\x7d\x2c\xd7\x24\x5d\xbc\x5e\xd3\x04\xdc\xd8\x8f\xea\x4f\x7d\x6e\x30\x28\xd3\x4b\x7d\xf7\xe7\xf8\x7d\x09\x8a\x26\x7d\xc4\x88\x24\xa0\x9e\x40\x94\x7d\xf1\x7d\x95\x5e\x46\x40\xd3\x70\x44\xbe\xfb\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x08\0\0\0\0\0\x1b\0\x1b\0\0\0\x7d\0\x5e\0\0\0\0\0\x08\0\x40\0\0\0\xd8\0\xe7\0\0\0\0\0\x08\0\x40\0\0\0\x0e\0\x0e\0\0\0\0\0\x08\0\x7d\0\0\0\xe7\0\x08\0\x7d\0\x1b\0\0\0\x01\0\0\0\xd8\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\x40\x43\xd8\xe0\x5e\xd1\x04\xf7\x8a\xde\x30\xf4\x20\x39\x05\x45\x05\xce\xb6\x31\x88\xbf\x30\x24\x30\x77\x30\xb7\x88\x65\0\xa3\x7d\xdd\xd8\xd6\x0e\x43\xea\x11\x69\x6c\x81\xb9\xec\x45\x28\x13\xb6\xe6\x20\xdc\xbe\x5b\x81\x3e\x0c\xc3\xec\x9e\x6c\x35\0\0\0\0\x08\x4a\x08\x22\x1b\xe2\x40\x9e\x5e\x4b\xb6\x8b\x05\xa0\xd8\x41\x5e\xa9\x56\xdd\x8a\x43\x69\x80\x69\x5b\x70\xe0\xea\x88\0\xa3\x08\x8d\x1b\xc4\x1b\xa8\x7d\x57\x56\xa7\x69\x8c\x88\xe6\x69\x94\x56\xa9\x8a\x08\xb6\xba\x69\x23\x69\xba\x88\x94\xbe\x81\0\0\0\0\x40\x43\x7d\x40\x7d\x54\xd8\x54\xe7\xea\x70\xc7\x05\x07\x5e\xe1\x0e\xa8\x56\xa4\x05\x80\x70\xc7\x70\xc7\x05\x80\x56\x0d\0\xa3\x30\x63\xbe\xd8\xbe\x5b\xbe\x9e\xbe\x21\xd7\x96\xd7\x86\xd7\x22\xbe\x21\xd7\xb6\xd7\xef\xd7\x74\xd7\xd4\xd7\xb3\xd7\xa7\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('1504 bytes unknown MakerNote data', $entry->getText());

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
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('Manual exposure', $entry->getText());

        $entry = $ifd0_0->getEntry(41987); // WhiteBalance
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Auto white balance', $entry->getText());

        $entry = $ifd0_0->getEntry(41990); // SceneCaptureType
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(0, $entry->getValue());
        $this->assertSame('Standard', $entry->getText());

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
        $this->assertCount(8, $ifd1->getEntries());

        $entry = $ifd1->getEntry(259); // Compression
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(6, $entry->getValue());
        $this->assertSame('JPEG compression', $entry->getText());

        $entry = $ifd1->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('SONY', $entry->getValue());
        $this->assertSame('SONY', $entry->getText());

        $entry = $ifd1->getEntry(272); // Model
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('DSC-V1', $entry->getValue());
        $this->assertSame('DSC-V1', $entry->getText());

        $entry = $ifd1->getEntry(274); // Orientation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(6, $entry->getValue());
        $this->assertSame('right - top', $entry->getText());

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

        $entry = $ifd1->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1089482993', $entry->getValue());
        $this->assertSame('2004:07:10 18:09:53', $entry->getText());

        /* Sub IFDs of $ifd1. */
        $this->assertCount(0, $ifd1->getSubIfds());

        $thumb_data = file_get_contents(__DIR__ . '/sony-dsc-v1-thumb.jpg');
        $this->assertSame($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        $this->assertCount(0, Pel::getExceptions());
    }
}
