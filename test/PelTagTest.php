<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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

        $this->assertSame(self::EXIF_TAG, PelTag::getExifTagByName(self::EXIF_TAG_NAME), 'EXIF tag name');
        $this->assertSame(self::GPS_TAG, PelTag::getGpsTagByName(self::GPS_TAG_NAME), 'GPS tag name');
        $this->assertSame(self::EXIF_TAG_NAME, PelTag::getName(PelIfd::IFD0, self::EXIF_TAG), 'EXIF tag');
        $this->assertSame(self::GPS_TAG_NAME, PelTag::getName(PelIfd::GPS, self::GPS_TAG), 'GPS tag');
    }

    /**
     * @param array<int, string> $container
     */
    #[DataProvider('getValueProvider')]
    public function testGetValue(array $container, int $tag, string $expected): void
    {
        $this->assertSame($expected, PelTag::getValue($container, $tag));
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getValueProvider(): \Iterator
    {
        yield [PelTag::$exifTagsShort, PelTag::IMAGE_WIDTH, 'ImageWidth'];
        yield [PelTag::$exifTagsShort, 0xFFFF, 'Unknown: 0xFFFF'];
        yield [PelTag::$gpsTagsShort, PelTag::GPS_LATITUDE, 'GPSLatitude'];
        yield [PelTag::$gpsTagsShort, 0xFFFF, 'Unknown: 0xFFFF'];
    }

    #[DataProvider('getTagByNameProvider')]
    public function testGetTagByName(string $name, int|false $expected): void
    {
        $this->assertSame($expected, PelTag::getTagByName($name));
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getTagByNameProvider(): \Iterator
    {
        yield ['ImageWidth', PelTag::IMAGE_WIDTH];
        yield ['GPSLatitude', PelTag::GPS_LATITUDE];
        yield ['NonExistentTag', false];
    }

    #[DataProvider('getExifTagByNameProvider')]
    public function testGetExifTagByName(string $name, int|false $expected): void
    {
        $this->assertSame($expected, PelTag::getExifTagByName($name));
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getExifTagByNameProvider(): \Iterator
    {
        yield ['ImageWidth', PelTag::IMAGE_WIDTH];
        yield ['NonExistentTag', false];
    }

    #[DataProvider('getGpsTagByNameProvider')]
    public function testGetGpsTagByName(string $name, int|false $expected): void
    {
        $this->assertSame($expected, PelTag::getGpsTagByName($name));
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getGpsTagByNameProvider(): \Iterator
    {
        yield ['GPSLatitude', PelTag::GPS_LATITUDE];
        yield ['NonExistentTag', false];
    }

    #[DataProvider('getNameProvider')]
    public function testGetName(int $type, int $tag, string $expected): void
    {
        $this->assertSame($expected, PelTag::getName($type, $tag));
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getNameProvider(): \Iterator
    {
        yield [PelIfd::IFD0, PelTag::IMAGE_WIDTH, 'ImageWidth'];
        yield [PelIfd::GPS, PelTag::GPS_LATITUDE, 'GPSLatitude'];
        yield [PelIfd::IFD0, 0xFFFF, 'Unknown: 0xFFFF'];
    }

    #[DataProvider('getTitleProvider')]
    public function testGetTitle(int $type, int $tag, string $expected): void
    {
        $pelTag = new PelTag();
        $this->assertSame($expected, $pelTag->getTitle($type, $tag));
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getTitleProvider(): \Iterator
    {
        yield [PelIfd::IFD0, PelTag::IMAGE_WIDTH, 'Image Width'];
        yield [PelIfd::GPS, PelTag::GPS_LATITUDE, 'GPSLatitude'];
        yield [PelIfd::IFD0, 0xFFFF, 'Unknown: 0xFFFF'];
    }
}
