<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelConvert;
use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
    private string $bytes = "\x00\x00\x00\x00\x01\x23\x45\x67\x89\xAB\xCD\xEF\xFF\xFF\xFF\xFF";

    public function testLongLittle(): void
    {
        $o = PelConvert::LITTLE_ENDIAN;

        $this->assertSame(0x00000000, PelConvert::bytesToLong($this->bytes, 0, $o));
        $this->assertSame(0x01000000, PelConvert::bytesToLong($this->bytes, 1, $o));
        $this->assertSame(0x23010000, PelConvert::bytesToLong($this->bytes, 2, $o));
        $this->assertSame(0x45230100, PelConvert::bytesToLong($this->bytes, 3, $o));
        $this->assertSame(0x67452301, PelConvert::bytesToLong($this->bytes, 4, $o));
        $this->assertSame(0x89674523, PelConvert::bytesToLong($this->bytes, 5, $o));
        $this->assertSame(0xAB896745, PelConvert::bytesToLong($this->bytes, 6, $o));
        $this->assertSame(0xCDAB8967, PelConvert::bytesToLong($this->bytes, 7, $o));
        $this->assertSame(0xEFCDAB89, PelConvert::bytesToLong($this->bytes, 8, $o));
        $this->assertSame(0xFFEFCDAB, PelConvert::bytesToLong($this->bytes, 9, $o));
        $this->assertSame(0xFFFFEFCD, PelConvert::bytesToLong($this->bytes, 10, $o));
        $this->assertSame(0xFFFFFFEF, PelConvert::bytesToLong($this->bytes, 11, $o));
        $this->assertSame(0xFFFFFFFF, PelConvert::bytesToLong($this->bytes, 12, $o));
    }

    public function testLongBig(): void
    {
        $o = PelConvert::BIG_ENDIAN;

        $this->assertSame(0x00000000, PelConvert::bytesToLong($this->bytes, 0, $o));
        $this->assertSame(0x00000001, PelConvert::bytesToLong($this->bytes, 1, $o));
        $this->assertSame(0x00000123, PelConvert::bytesToLong($this->bytes, 2, $o));
        $this->assertSame(0x00012345, PelConvert::bytesToLong($this->bytes, 3, $o));
        $this->assertSame(0x01234567, PelConvert::bytesToLong($this->bytes, 4, $o));
        $this->assertSame(0x23456789, PelConvert::bytesToLong($this->bytes, 5, $o));
        $this->assertSame(0x456789AB, PelConvert::bytesToLong($this->bytes, 6, $o));
        $this->assertSame(0x6789ABCD, PelConvert::bytesToLong($this->bytes, 7, $o));
        $this->assertSame(0x89ABCDEF, PelConvert::bytesToLong($this->bytes, 8, $o));
        $this->assertSame(0xABCDEFFF, PelConvert::bytesToLong($this->bytes, 9, $o));
        $this->assertSame(0xCDEFFFFF, PelConvert::bytesToLong($this->bytes, 10, $o));
        $this->assertSame(0xEFFFFFFF, PelConvert::bytesToLong($this->bytes, 11, $o));
        $this->assertSame(0xFFFFFFFF, PelConvert::bytesToLong($this->bytes, 12, $o));
    }

    public function testShortLittle(): void
    {
        $o = PelConvert::LITTLE_ENDIAN;

        $this->assertSame(0x0000, PelConvert::bytesToShort($this->bytes, 0, $o));
        $this->assertSame(0x0000, PelConvert::bytesToShort($this->bytes, 1, $o));
        $this->assertSame(0x0000, PelConvert::bytesToShort($this->bytes, 2, $o));
        $this->assertSame(0x0100, PelConvert::bytesToShort($this->bytes, 3, $o));
        $this->assertSame(0x2301, PelConvert::bytesToShort($this->bytes, 4, $o));
        $this->assertSame(0x4523, PelConvert::bytesToShort($this->bytes, 5, $o));
        $this->assertSame(0x6745, PelConvert::bytesToShort($this->bytes, 6, $o));
        $this->assertSame(0x8967, PelConvert::bytesToShort($this->bytes, 7, $o));
        $this->assertSame(0xAB89, PelConvert::bytesToShort($this->bytes, 8, $o));
        $this->assertSame(0xCDAB, PelConvert::bytesToShort($this->bytes, 9, $o));
        $this->assertSame(0xEFCD, PelConvert::bytesToShort($this->bytes, 10, $o));
        $this->assertSame(0xFFEF, PelConvert::bytesToShort($this->bytes, 11, $o));
        $this->assertSame(0xFFFF, PelConvert::bytesToShort($this->bytes, 12, $o));
        $this->assertSame(0xFFFF, PelConvert::bytesToShort($this->bytes, 13, $o));
        $this->assertSame(0xFFFF, PelConvert::bytesToShort($this->bytes, 14, $o));
    }

    public function testShortBig(): void
    {
        $o = PelConvert::BIG_ENDIAN;

        $this->assertSame(0x0000, PelConvert::bytesToShort($this->bytes, 0, $o));
        $this->assertSame(0x0000, PelConvert::bytesToShort($this->bytes, 1, $o));
        $this->assertSame(0x0000, PelConvert::bytesToShort($this->bytes, 2, $o));
        $this->assertSame(0x0001, PelConvert::bytesToShort($this->bytes, 3, $o));
        $this->assertSame(0x0123, PelConvert::bytesToShort($this->bytes, 4, $o));
        $this->assertSame(0x2345, PelConvert::bytesToShort($this->bytes, 5, $o));
        $this->assertSame(0x4567, PelConvert::bytesToShort($this->bytes, 6, $o));
        $this->assertSame(0x6789, PelConvert::bytesToShort($this->bytes, 7, $o));
        $this->assertSame(0x89AB, PelConvert::bytesToShort($this->bytes, 8, $o));
        $this->assertSame(0xABCD, PelConvert::bytesToShort($this->bytes, 9, $o));
        $this->assertSame(0xCDEF, PelConvert::bytesToShort($this->bytes, 10, $o));
        $this->assertSame(0xEFFF, PelConvert::bytesToShort($this->bytes, 11, $o));
        $this->assertSame(0xFFFF, PelConvert::bytesToShort($this->bytes, 12, $o));
        $this->assertSame(0xFFFF, PelConvert::bytesToShort($this->bytes, 13, $o));
        $this->assertSame(0xFFFF, PelConvert::bytesToShort($this->bytes, 14, $o));
    }

    public function testSShortLittle(): void
    {
        $o = PelConvert::LITTLE_ENDIAN;

        $this->assertSame(0, PelConvert::bytesToSShort($this->bytes, 0, $o));
        $this->assertSame(0, PelConvert::bytesToSShort($this->bytes, 1, $o));
        $this->assertSame(0, PelConvert::bytesToSShort($this->bytes, 2, $o));
        $this->assertSame(256, PelConvert::bytesToSShort($this->bytes, 3, $o));
        $this->assertSame(8961, PelConvert::bytesToSShort($this->bytes, 4, $o));
        $this->assertSame(17699, PelConvert::bytesToSShort($this->bytes, 5, $o));
        $this->assertSame(26437, PelConvert::bytesToSShort($this->bytes, 6, $o));
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 7, $o), - 30361);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 8, $o), - 21623);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 9, $o), - 12885);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 10, $o), - 4147);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 11, $o), - 17);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 12, $o), - 1);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 13, $o), - 1);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 14, $o), - 1);
    }

    public function testSShortBig(): void
    {
        $o = PelConvert::BIG_ENDIAN;

        $this->assertSame(0, PelConvert::bytesToSShort($this->bytes, 0, $o));
        $this->assertSame(0, PelConvert::bytesToSShort($this->bytes, 1, $o));
        $this->assertSame(0, PelConvert::bytesToSShort($this->bytes, 2, $o));
        $this->assertSame(1, PelConvert::bytesToSShort($this->bytes, 3, $o));
        $this->assertSame(291, PelConvert::bytesToSShort($this->bytes, 4, $o));
        $this->assertSame(9029, PelConvert::bytesToSShort($this->bytes, 5, $o));
        $this->assertSame(17767, PelConvert::bytesToSShort($this->bytes, 6, $o));
        $this->assertSame(26505, PelConvert::bytesToSShort($this->bytes, 7, $o));
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 8, $o), - 30293);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 9, $o), - 21555);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 10, $o), - 12817);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 11, $o), - 4097);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 12, $o), - 1);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 13, $o), - 1);
        $this->assertSame(PelConvert::bytesToSShort($this->bytes, 14, $o), - 1);
    }

    public function testByte(): void
    {
        $this->assertSame(0x00, PelConvert::bytesToByte($this->bytes, 0));
        $this->assertSame(0x00, PelConvert::bytesToByte($this->bytes, 1));
        $this->assertSame(0x00, PelConvert::bytesToByte($this->bytes, 2));
        $this->assertSame(0x00, PelConvert::bytesToByte($this->bytes, 3));
        $this->assertSame(0x01, PelConvert::bytesToByte($this->bytes, 4));
        $this->assertSame(0x23, PelConvert::bytesToByte($this->bytes, 5));
        $this->assertSame(0x45, PelConvert::bytesToByte($this->bytes, 6));
        $this->assertSame(0x67, PelConvert::bytesToByte($this->bytes, 7));
        $this->assertSame(0x89, PelConvert::bytesToByte($this->bytes, 8));
        $this->assertSame(0xAB, PelConvert::bytesToByte($this->bytes, 9));
        $this->assertSame(0xCD, PelConvert::bytesToByte($this->bytes, 10));
        $this->assertSame(0xEF, PelConvert::bytesToByte($this->bytes, 11));
        $this->assertSame(0xFF, PelConvert::bytesToByte($this->bytes, 12));
        $this->assertSame(0xFF, PelConvert::bytesToByte($this->bytes, 13));
        $this->assertSame(0xFF, PelConvert::bytesToByte($this->bytes, 14));
        $this->assertSame(0xFF, PelConvert::bytesToByte($this->bytes, 15));
    }

    public function testSByte(): void
    {
        $this->assertSame(0, PelConvert::bytesToSByte($this->bytes, 0));
        $this->assertSame(0, PelConvert::bytesToSByte($this->bytes, 1));
        $this->assertSame(0, PelConvert::bytesToSByte($this->bytes, 2));
        $this->assertSame(0, PelConvert::bytesToSByte($this->bytes, 3));
        $this->assertSame(1, PelConvert::bytesToSByte($this->bytes, 4));
        $this->assertSame(35, PelConvert::bytesToSByte($this->bytes, 5));
        $this->assertSame(69, PelConvert::bytesToSByte($this->bytes, 6));
        $this->assertSame(103, PelConvert::bytesToSByte($this->bytes, 7));
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 8), - 119);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 9), - 85);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 10), - 51);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 11), - 17);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 12), - 1);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 13), - 1);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 14), - 1);
        $this->assertSame(PelConvert::bytesToSByte($this->bytes, 15), - 1);
    }
}
