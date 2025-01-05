<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;

class PelTagTest extends TestCase
{
    public const NONEXISTENT_TAG_NAME = 'nonexistent tag name';

    public const NONEXISTENT_EXIF_TAG = 0xFCFC;

    public const NONEXISTENT_GPS_TAG = 0xFCFC;

    public const EXIF_TAG_NAME = 'ImageDescription';

    public const GPS_TAG_NAME = 'GPSLongitude';

    public const EXIF_TAG = PelTag::IMAGE_DESCRIPTION;

    public const GPS_TAG = PelTag::GPS_LONGITUDE;

    public function testReverseLookup(): void
    {
        $this->assertFalse(PelTag::getExifTagByName(self::NONEXISTENT_TAG_NAME), 'Non-existent EXIF tag name');
        $this->assertFalse(PelTag::getGpsTagByName(self::NONEXISTENT_TAG_NAME), 'Non-existent GPS tag name');
        $this->assertStringStartsWith('Unknown: ', PelTag::getName(PelIfd::IFD0, self::NONEXISTENT_EXIF_TAG), 'Non-existent EXIF tag');
        $this->assertStringStartsWith('Unknown: ', PelTag::getName(PelIfd::GPS, self::NONEXISTENT_GPS_TAG), 'Non-existent GPS tag');

        $this->assertSame(static::EXIF_TAG, PelTag::getExifTagByName(self::EXIF_TAG_NAME), 'EXIF tag name');
        $this->assertSame(static::GPS_TAG, PelTag::getGpsTagByName(self::GPS_TAG_NAME), 'GPS tag name');
        $this->assertEquals(static::EXIF_TAG_NAME, PelTag::getName(PelIfd::IFD0, self::EXIF_TAG), 'EXIF tag');
        $this->assertEquals(static::GPS_TAG_NAME, PelTag::getName(PelIfd::GPS, self::GPS_TAG), 'GPS tag');
    }
}
