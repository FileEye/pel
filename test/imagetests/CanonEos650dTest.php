<?php

/*
 * PEL: PHP Exif Library. A library with support for reading and
 * writing all Exif headers in JPEG and TIFF images using PHP.
 *
 * Copyright (C) 2005, 2006 Martin Geisler.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program in the file COPYING; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA 02110-1301 USA
 */
namespace Pel\Test\imagetests;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\Pel;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelEntrySRational;
use lsolesen\pel\PelEntrySShort;
use lsolesen\pel\PelEntryTime;
use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelEntryVersion;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;

class CanonEos650dTest extends TestCase
{

    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(dirname(__FILE__) . '/canon-eos-650d.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        /* The first IFD. */
        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        /* Start of IDF $ifd0. */
        $this->assertEquals(9, count($ifd0->getEntries()));

        $entry = $ifd0->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertEquals('Canon', $entry->getValue());
        $this->assertEquals('Canon', $entry->getText());

        $entry = $ifd0->getEntry(272); // Model
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertEquals('Canon EOS 650D', $entry->getValue());
        $this->assertEquals('Canon EOS 650D', $entry->getText());

        $entry = $ifd0->getEntry(274); // Orientation
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(1, $entry->getValue());
        $this->assertEquals('top - left', $entry->getText());

        $entry = $ifd0->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 72,
            1 => 1
        ], $entry->getValue());
        $this->assertEquals('72/1', $entry->getText());

        $entry = $ifd0->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 72,
            1 => 1
        ], $entry->getValue());
        $this->assertEquals($entry->getText(), '72/1');

        $entry = $ifd0->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(2, $entry->getValue());
        $this->assertEquals('Inch', $entry->getText());

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertEquals(1509974253, $entry->getValue());
        $this->assertEquals('2017:11:06 13:17:33', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertEquals(count($ifd0->getSubIfds()), 2);
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertEquals(count($ifd0_0->getEntries()), 29);

        $entry = $ifd0_0->getEntry(33434); // ExposureTime
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 1,
            1 => 800
        ], $entry->getValue());
        $this->assertEquals('1/800 sec.', $entry->getText());

        $entry = $ifd0_0->getEntry(33437); // FNumber
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 63,
            1 => 10
        ], $entry->getValue());
        $this->assertEquals('f/6.3', $entry->getText());

        $entry = $ifd0_0->getEntry(36864); // ExifVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEquals(2.3, $entry->getValue());
        $this->assertEquals('Exif Version 2.3', $entry->getText());

        $entry = $ifd0_0->getEntry(36867); // DateTimeOriginal
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertEquals(1497623444, $entry->getValue());
        $this->assertEquals('2017:06:16 14:30:44', $entry->getText());

        $entry = $ifd0_0->getEntry(36868); // DateTimeDigitized
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertEquals(1497623444, $entry->getValue());
        $this->assertEquals('2017:06:16 14:30:44', $entry->getText());

        $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertEquals("\x01\x02\x03\0", $entry->getValue());
        $this->assertEquals('Y Cb Cr -', $entry->getText());

        $entry = $ifd0_0->getEntry(37378); // ApertureValue
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 352256,
            1 => 65536
        ]);
        $this->assertEquals($entry->getText(), 'f/6.4');

        $entry = $ifd0_0->getEntry(37380); // ExposureBiasValue
        $this->assertInstanceOf(PelEntrySRational::class, $entry);
        $this->assertEquals([
            0 => 0,
            1 => 1
        ], $entry->getValue());
        $this->assertEquals('0.0', $entry->getText());

        $entry = $ifd0_0->getEntry(37383); // MeteringMode
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(5, $entry->getValue());
        $this->assertEquals('Pattern', $entry->getText());

        $entry = $ifd0_0->getEntry(37385); // Flash
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(16, $entry->getValue());
        $this->assertEquals('Flash did not fire, compulsory flash mode.', $entry->getText());

        $entry = $ifd0_0->getEntry(37386); // FocalLength
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 600,
            1 => 1
        ], $entry->getValue());
        $this->assertEquals('600.0 mm', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertNull($entry);

        $entry = $ifd0_0->getEntry(37510); // UserComment
        $this->assertInstanceOf(PelEntryUserComment::class, $entry);

        $expected = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $this->assertEquals($expected, $entry->getValue());

        $expected = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        $this->assertEquals($expected, $entry->getText());

        $entry = $ifd0_0->getEntry(40960); // FlashPixVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEquals(1, $entry->getValue());
        $this->assertEquals('FlashPix Version 1.0', $entry->getText());

        $entry = $ifd0_0->getEntry(40961); // ColorSpace
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(1, $entry->getValue());
        $this->assertEquals('sRGB', $entry->getText());

        $entry = $ifd0_0->getEntry(41488); // FocalPlaneResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(2, $entry->getValue());
        $this->assertEquals('Inch', $entry->getText());

        $entry = $ifd0_0->getEntry(41985); // CustomRendered
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(0, $entry->getValue());
        $this->assertEquals('Normal process', $entry->getText());

        $entry = $ifd0_0->getEntry(41986); // ExposureMode
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(0, $entry->getValue());
        $this->assertEquals('Auto exposure', $entry->getText());

        $entry = $ifd0_0->getEntry(41987); // WhiteBalance
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(0, $entry->getValue());
        $this->assertEquals('Auto white balance', $entry->getText());

        $entry = $ifd0_0->getEntry(41990); // SceneCaptureType
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(0, $entry->getValue());
        $this->assertEquals('Standard', $entry->getText());

        /* Sub IFDs of $ifd0_0. */
        $this->assertEquals(count($ifd0_0->getSubIfds()), 2);
        $ifd0_0_0 = $ifd0_0->getSubIfd(4); // IFD Interoperability
        $this->assertInstanceOf(PelIfd::class, $ifd0_0_0);

        /* Start of IDF $ifd0_0_0. */
        $this->assertEquals(2, count($ifd0_0_0->getEntries()));

        $entry = $ifd0_0_0->getEntry(1); // InteroperabilityIndex
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertEquals('R98', $entry->getValue());
        $this->assertEquals('R98', $entry->getText());

        $entry = $ifd0_0_0->getEntry(2); // InteroperabilityVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEquals(1, $entry->getValue());
        $this->assertEquals('Interoperability Version 1.0', $entry->getText());

        /* Sub IFDs of $ifd0_0_0. */
        $this->assertEquals(0, count($ifd0_0_0->getSubIfds()));

        $this->assertEquals('', $ifd0_0_0->getThumbnailData());

        /* Next IFD. */
        $ifd0_0_1 = $ifd0_0_0->getNextIfd();
        $this->assertNull($ifd0_0_1);
        /* End of IFD $ifd0_0_0. */

        $this->assertEquals('', $ifd0_0->getThumbnailData());

        /* Next IFD. */
        $ifd0_1 = $ifd0_0->getNextIfd();
        $this->assertNull($ifd0_1);
        /* End of IFD $ifd0_0. */

        $this->assertEquals('', $ifd0->getThumbnailData());

        /* Next IFD. */
        $ifd1 = $ifd0->getNextIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd1);
        /* End of IFD $ifd0. */

        /* Start of IDF $ifd1. */
        $this->assertEquals(count($ifd1->getEntries()), 4);

        $entry = $ifd1->getEntry(259); // Compression
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(0, $entry->getValue());
        $this->assertEquals('0', $entry->getText());

        $entry = $ifd1->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 72,
            1 => 1
        ], $entry->getValue());
        $this->assertEquals('72/1', $entry->getText());

        $entry = $ifd1->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals([
            0 => 72,
            1 => 1
        ], $entry->getValue());
        $this->assertEquals('72/1', $entry->getText());

        $entry = $ifd1->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertEquals(2, $entry->getValue());
        $this->assertEquals('Inch', $entry->getText());

        /* Sub IFDs of $ifd1. */
        $this->assertEquals(0, count($ifd1->getSubIfds()));

        $thumb_data = file_get_contents(dirname(__FILE__) . '/canon-eos-650d-thumb.jpg');
        $this->assertEquals($ifd1->getThumbnailData(), $thumb_data);

        /* Next IFD. */
        $ifd2 = $ifd1->getNextIfd();
        $this->assertNull($ifd2);
        /* End of IFD $ifd1. */

        /* Start of IDF $ifd0_mn */
        $ifd0_mn = $ifd0_0->getSubIfd(5); // IFD MakerNotes
        $this->assertInstanceOf(PelIfd::class, $ifd0_mn);

        $entry = $ifd0_mn->getEntry(6); // ImageType
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertEquals('Canon EOS 650D', $entry->getValue());

        $entry = $ifd0_mn->getEntry(7); // FirmwareVersion
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertEquals('Firmware Version 1.0.4', $entry->getValue());

        /* Start of IDF $ifd0_mn_cs. */
        $ifd0_mn_cs = $ifd0_mn->getSubIfd(6); // CameraSettings
        $this->assertInstanceOf(PelIfd::class, $ifd0_mn_cs);
        $this->assertEquals(37, count($ifd0_mn_cs->getEntries()));

        $entry = $ifd0_mn_cs->getEntry(1); // MacroMode
        $this->assertInstanceOf(PelEntrySShort::class, $entry);
        $this->assertEquals('2', $entry->getValue());
        $this->assertEquals('Normal', $entry->getText());

        $entry = $ifd0_mn_cs->getEntry(9); // RecordMode
        $this->assertInstanceOf(PelEntrySShort::class, $entry);
        $this->assertEquals('6', $entry->getValue());
        $this->assertEquals('CR2', $entry->getText());

        $entry = $ifd0_mn_cs->getEntry(22); // LensModel
        $this->assertInstanceOf(PelEntrySShort::class, $entry);
        $this->assertEquals(747, $entry->getValue());
        // Tamron 150-600mm G2
        $this->assertEquals('Canon EF 100-400mm f/4.5-5.6L IS II USM or Tamron Lens', $entry->getText());

        $this->assertEquals(0, count(Pel::getExceptions()));
    }
}
