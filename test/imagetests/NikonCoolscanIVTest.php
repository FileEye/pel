<?php

declare(strict_types=1);

namespace Pel\Test\imagetests;

use lsolesen\pel\Pel;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelEntryTime;
use lsolesen\pel\PelEntryUndefined;
use lsolesen\pel\PelEntryVersion;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\TestCase;

class NikonCoolscanIVTest extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/nikon-coolscan-iv.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        /* The first IFD. */
        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        /* Start of IDF $ifd0. */
        $this->assertCount(6, $ifd0->getEntries());

        $entry = $ifd0->getEntry(271); // Make
        $this->assertInstanceOf(PelEntryAscii::class, $entry);
        $this->assertSame('Nikon', $entry->getValue());
        $this->assertSame('Nikon', $entry->getText());

        $entry = $ifd0->getEntry(282); // XResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 2000,
            1 => 1,
        ]);
        $this->assertSame('2000/1', $entry->getText());

        $entry = $ifd0->getEntry(283); // YResolution
        $this->assertInstanceOf(PelEntryRational::class, $entry);
        $this->assertEquals($entry->getValue(), [
            0 => 2000,
            1 => 1,
        ]);
        $this->assertSame('2000/1', $entry->getText());

        $entry = $ifd0->getEntry(296); // ResolutionUnit
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(2, $entry->getValue());
        $this->assertSame('Inch', $entry->getText());

        $entry = $ifd0->getEntry(306); // DateTime
        $this->assertInstanceOf(PelEntryTime::class, $entry);
        $this->assertSame('1090023875', $entry->getValue());
        $this->assertSame('2004:07:17 00:24:35', $entry->getText());

        $entry = $ifd0->getEntry(531); // YCbCrPositioning
        $this->assertInstanceOf(PelEntryShort::class, $entry);
        $this->assertSame(1, $entry->getValue());
        $this->assertSame('centered', $entry->getText());

        /* Sub IFDs of $ifd0. */
        $this->assertCount(2, $ifd0->getSubIfds());
        $ifd0_0 = $ifd0->getSubIfd(2); // IFD Exif
        $this->assertInstanceOf(PelIfd::class, $ifd0_0);

        /* Start of IDF $ifd0_0. */
        $this->assertCount(7, $ifd0_0->getEntries());

        $entry = $ifd0_0->getEntry(36864); // ExifVersion
        $this->assertInstanceOf(PelEntryVersion::class, $entry);
        $this->assertEqualsWithDelta(2.1, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Exif Version 2.1', $entry->getText());

        $entry = $ifd0_0->getEntry(37121); // ComponentsConfiguration
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $this->assertSame("\x01\x02\x03\0", $entry->getValue());
        $this->assertSame('Y Cb Cr -', $entry->getText());

        $entry = $ifd0_0->getEntry(37500); // MakerNote
        $this->assertInstanceOf(PelEntryUndefined::class, $entry);
        $expected = "\x02\0\x01\0\x07\0\x04\0\0\0\x30\x31\x30\x30\x10\x0e\x04\0\x01\0\0\0\x16\x01\0\0\0\0\0\0\x05\0";
        $this->assertSame($entry->getValue(), $expected);
        $this->assertSame('32 bytes unknown MakerNote data', $entry->getText());

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
        $this->assertSame(960, $entry->getValue());
        $this->assertSame('960', $entry->getText());

        $entry = $ifd0_0->getEntry(40963); // PixelYDimension
        $this->assertInstanceOf(PelEntryLong::class, $entry);
        $this->assertSame(755, $entry->getValue());
        $this->assertSame('755', $entry->getText());

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
        $this->assertNull($ifd1);
        /* End of IFD $ifd0. */

        $this->assertCount(0, Pel::getExceptions());
    }
}
