<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelConvert;
use lsolesen\pel\PelOverflowException;

class PelEntryShortTest extends TestCase
{
    /**
     * @dataProvider constructorProvider
     *
     * @param array<int, int> $values
     * @param array<int, mixed> $expected
     */
    public function testConstructor(int $tag, array $values, array $expected): void
    {
        $entry = new PelEntryShort($tag, ...$values);
        $this->assertEquals($expected, $entry->getValue());
    }

    /**
     * @return array<int, mixed>
     */
    public static function constructorProvider(): array
    {
        return [
            [PelTag::IMAGE_WIDTH, [42, 42], [42, 42]],
            [PelTag::IMAGE_LENGTH, [100, 200], [100, 200]],
            [PelTag::IMAGE_WIDTH, [], []],
        ];
    }

    /**
     * @dataProvider getTextProvider
     * 
     * @param array<int, int> $values
     */
    public function testGetText(int $tag, array $values, string $expected): void
    {
        $entry = new PelEntryShort($tag, ...$values);
        $this->assertEquals($expected, $entry->getText());
    }

    /**
     * @return array<int, mixed>
     */
    public static function getTextProvider(): array
    {
        return [
            [PelTag::METERING_MODE, [2], 'Center-Weighted Average'],
            [PelTag::METERING_MODE, [0], 'Unknown'],
            [PelTag::IMAGE_WIDTH, [42], '42'],
        ];
    }

    public function testSetValueThrowsException(): void
    {
        $this->expectException(PelOverflowException::class);
        $entry = new PelEntryShort(PelTag::IMAGE_WIDTH, 42);
        $entry->setValue(70000); // Out of range for unsigned short
    }
}
