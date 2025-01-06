<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelConvert;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelDataWindowOffsetException;
use PHPUnit\Framework\TestCase;

class DataWindowTest extends TestCase
{
    public function testReadBytes(): void
    {
        $window = new PelDataWindow('abcdefgh');

        $this->assertSame(8, $window->getSize());
        $this->assertSame('abcdefgh', $window->getBytes());

        $this->assertSame('abcdefgh', $window->getBytes(0));
        $this->assertSame('bcdefgh', $window->getBytes(1));
        $this->assertSame('h', $window->getBytes(7));
        // $this->assertEquals($window->getBytes(8), '');

        $this->assertSame('h', $window->getBytes(- 1));
        $this->assertSame('gh', $window->getBytes(- 2));
        $this->assertSame('bcdefgh', $window->getBytes(- 7));
        $this->assertSame('abcdefgh', $window->getBytes(- 8));

        $clone = $window->getClone(2, 4);
        $this->assertSame(4, $clone->getSize());
        $this->assertSame('cdef', $clone->getBytes());

        $this->assertSame('cdef', $clone->getBytes(0));
        $this->assertSame('def', $clone->getBytes(1));
        $this->assertSame('f', $clone->getBytes(3));
        // $this->assertEquals($clone->getBytes(4), '');

        $this->assertSame('f', $clone->getBytes(- 1));
        $this->assertSame('ef', $clone->getBytes(- 2));
        $this->assertSame('def', $clone->getBytes(- 3));
        $this->assertSame('cdef', $clone->getBytes(- 4));

        $caught = false;
        try {
            $clone->getBytes(0, 6);
        } catch (PelDataWindowOffsetException) {
            $caught = true;
        }
        $this->assertTrue($caught);
    }

    public function testReadIntegers(): void
    {
        $window = new PelDataWindow("\x01\x02\x03\x04", PelConvert::BIG_ENDIAN);

        $this->assertSame(4, $window->getSize());
        $this->assertSame("\x01\x02\x03\x04", $window->getBytes());

        $this->assertSame(0x01, $window->getByte(0));
        $this->assertSame(0x02, $window->getByte(1));
        $this->assertSame(0x03, $window->getByte(2));
        $this->assertSame(0x04, $window->getByte(3));

        $this->assertSame(0x0102, $window->getShort(0));
        $this->assertSame(0x0203, $window->getShort(1));
        $this->assertSame(0x0304, $window->getShort(2));

        $this->assertSame(0x01020304, $window->getLong(0));

        $window->setByteOrder(PelConvert::LITTLE_ENDIAN);
        $this->assertSame(4, $window->getSize());
        $this->assertSame("\x01\x02\x03\x04", $window->getBytes());

        $this->assertSame(0x01, $window->getByte(0));
        $this->assertSame(0x02, $window->getByte(1));
        $this->assertSame(0x03, $window->getByte(2));
        $this->assertSame(0x04, $window->getByte(3));

        $this->assertSame(0x0201, $window->getShort(0));
        $this->assertSame(0x0302, $window->getShort(1));
        $this->assertSame(0x0403, $window->getShort(2));

        $this->assertSame(0x04030201, $window->getLong(0));
    }

    public function testReadBigIntegers(): void
    {
        $window = new PelDataWindow("\x89\xAB\xCD\xEF", PelConvert::BIG_ENDIAN);

        $this->assertSame(4, $window->getSize());
        $this->assertSame("\x89\xAB\xCD\xEF", $window->getBytes());

        $this->assertSame(0x89, $window->getByte(0));
        $this->assertSame(0xAB, $window->getByte(1));
        $this->assertSame(0xCD, $window->getByte(2));
        $this->assertSame(0xEF, $window->getByte(3));

        $this->assertSame(0x89AB, $window->getShort(0));
        $this->assertSame(0xABCD, $window->getShort(1));
        $this->assertSame(0xCDEF, $window->getShort(2));

        $this->assertSame(0x89ABCDEF, $window->getLong(0));

        $window->setByteOrder(PelConvert::LITTLE_ENDIAN);
        $this->assertSame(4, $window->getSize());
        $this->assertSame("\x89\xAB\xCD\xEF", $window->getBytes());

        $this->assertSame(0x89, $window->getByte(0));
        $this->assertSame(0xAB, $window->getByte(1));
        $this->assertSame(0xCD, $window->getByte(2));
        $this->assertSame(0xEF, $window->getByte(3));

        $this->assertSame(0xAB89, $window->getShort(0));
        $this->assertSame(0xCDAB, $window->getShort(1));
        $this->assertSame(0xEFCD, $window->getShort(2));

        $this->assertSame(0xEFCDAB89, $window->getLong(0));
    }
}
