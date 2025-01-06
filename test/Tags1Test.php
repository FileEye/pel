<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\Pel;
use lsolesen\pel\PelEntry;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\TestCase;

class Tags1Test extends TestCase
{
    public function testTags(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(true);
        $jpeg = new PelJpeg(__DIR__ . '/images/test-tags-1.jpg');

        $exif = $jpeg->getExif();
        $this->assertInstanceOf(PelExif::class, $exif);

        $tiff = $exif->getTiff();
        $this->assertInstanceOf(PelTiff::class, $tiff);

        $ifd0 = $tiff->getIfd();
        $this->assertInstanceOf(PelIfd::class, $ifd0);

        $ratingPercent = $ifd0->getEntry(PelTag::RATING_PERCENT);
        $this->assertInstanceOf(PelEntry::class, $ratingPercent);
        $this->assertEquals(78, $ratingPercent->getValue());

        $exifIfd = $ifd0->getSubIfd(PelIfd::EXIF);
        $this->assertInstanceOf(PelIfd::class, $exifIfd);

        $offsetTime = $exifIfd->getEntry(PelTag::OFFSET_TIME);
        $this->assertInstanceOf(PelEntry::class, $offsetTime);
        $this->assertEquals('-09:00', $offsetTime->getValue());

        $offsetTimeDigitized = $exifIfd->getEntry(PelTag::OFFSET_TIME_DIGITIZED);
        $this->assertInstanceOf(PelEntry::class, $offsetTimeDigitized);
        $this->assertEquals('-10:00', $offsetTimeDigitized->getValue());

        $offsetTimeOriginal = $exifIfd->getEntry(PelTag::OFFSET_TIME_ORIGINAL);
        $this->assertInstanceOf(PelEntry::class, $offsetTimeOriginal);
        $this->assertEquals('-11:00', $offsetTimeOriginal->getValue());
    }
}
