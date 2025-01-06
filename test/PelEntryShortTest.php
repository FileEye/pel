<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntryShort;
use lsolesen\pel\PelOverflowException;
use lsolesen\pel\PelTag;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PelEntryShortTest extends TestCase
{
    /**
     * @param array<int, int> $values
     * @param array<int, mixed> $expected
     */
    #[DataProvider('constructorProvider')]
    public function testConstructor(int $tag, array $values, array $expected): void
    {
        $entry = new PelEntryShort($tag, ...$values);
        $this->assertEquals($expected, $entry->getValue());
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function constructorProvider(): \Iterator
    {
        yield [PelTag::IMAGE_WIDTH, [42, 42], [42, 42]];
        yield [PelTag::IMAGE_LENGTH, [100, 200], [100, 200]];
        yield [PelTag::IMAGE_WIDTH, [], []];
    }

    /**
     * @param array<int, int> $values
     */
    #[DataProvider('getTextProvider')]
    public function testGetText(int $tag, array $values, string $expected): void
    {
        $entry = new PelEntryShort($tag, ...$values);
        $this->assertSame($expected, $entry->getText());
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function getTextProvider(): \Iterator
    {
        yield [PelTag::METERING_MODE, [2], 'Center-Weighted Average'];
        yield [PelTag::METERING_MODE, [0], 'Unknown'];
        yield [PelTag::IMAGE_WIDTH, [42], '42'];
    }

    public function testSetValueThrowsException(): void
    {
        $this->expectException(PelOverflowException::class);
        $entry = new PelEntryShort(PelTag::IMAGE_WIDTH, 42);
        $entry->setValue(70000); // Out of range for unsigned short
    }
}
