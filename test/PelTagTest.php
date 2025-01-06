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

    /**
     * @dataProvider getValueProvider
     *
     * @param array<int, string> $container
     */
    public function testGetValue(array $container, int $tag, string $expected): void
    {
        $this->assertEquals($expected, PelTag::getValue($container, $tag));
    }

    /**
     * @return array<int, mixed>
     */
    public static function getValueProvider(): array
    {
        return [
            [PelTag::$exifTagsShort, PelTag::IMAGE_WIDTH, 'ImageWidth'],
            [PelTag::$exifTagsShort, 0xFFFF, 'Unknown: 0xFFFF'],
            [PelTag::$gpsTagsShort, PelTag::GPS_LATITUDE, 'GPSLatitude'],
            [PelTag::$gpsTagsShort, 0xFFFF, 'Unknown: 0xFFFF'],
        ];
    }

    /**
     * @dataProvider getTagByNameProvider
     */
    public function testGetTagByName(string $name, int|false $expected): void
    {
        $this->assertSame($expected, PelTag::getTagByName($name));
    }

    /**
     * @return array<int, mixed>
     */
    public static function getTagByNameProvider(): array
    {
        return [
            ['ImageWidth', PelTag::IMAGE_WIDTH],
            ['GPSLatitude', PelTag::GPS_LATITUDE],
            ['NonExistentTag', false],
        ];
    }

    /**
     * @dataProvider getExifTagByNameProvider
     */
    public function testGetExifTagByName(string $name, int|false $expected): void
    {
        $this->assertSame($expected, PelTag::getExifTagByName($name));
    }

    /**
     * @return array<int, mixed>
     */
    public static function getExifTagByNameProvider(): array
    {
        return [
            ['ImageWidth', PelTag::IMAGE_WIDTH],
            ['NonExistentTag', false],
        ];
    }

    /**
     * @dataProvider getGpsTagByNameProvider
     */
    public function testGetGpsTagByName(string $name, int|false $expected): void
    {
        $this->assertSame($expected, PelTag::getGpsTagByName($name));
    }

    /**
     * @return array<int, mixed>
     */
    public static function getGpsTagByNameProvider(): array
    {
        return [
            ['GPSLatitude', PelTag::GPS_LATITUDE],
            ['NonExistentTag', false],
        ];
    }

    /**
     * @dataProvider getNameProvider
     */
    public function testGetName(int $type, int $tag, string $expected): void
    {
        $this->assertEquals($expected, PelTag::getName($type, $tag));
    }

    /**
     * @return array<int, mixed>
     */
    public static function getNameProvider(): array
    {
        return [
            [PelIfd::IFD0, PelTag::IMAGE_WIDTH, 'ImageWidth'],
            [PelIfd::GPS, PelTag::GPS_LATITUDE, 'GPSLatitude'],
            [PelIfd::IFD0, 0xFFFF, 'Unknown: 0xFFFF'],
        ];
    }

    /**
     * @dataProvider getTitleProvider
     */
    public function testGetTitle(int $type, int $tag, string $expected): void
    {
        $pelTag = new PelTag();
        $this->assertEquals($expected, $pelTag->getTitle($type, $tag));
    }

    /**
     * @return array<int, mixed>
     */
    public static function getTitleProvider(): array
    {
        return [
            [PelIfd::IFD0, PelTag::IMAGE_WIDTH, 'Image Width'],
            [PelIfd::GPS, PelTag::GPS_LATITUDE, 'GPSLatitude'],
            [PelIfd::IFD0, 0xFFFF, 'Unknown: 0xFFFF'],
        ];
    }
}
