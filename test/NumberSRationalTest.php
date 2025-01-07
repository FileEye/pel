<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntrySRational;
use lsolesen\pel\PelOverflowException;

class NumberSRationalTest extends NumberTestCase
{
    public function testOverflow(): void
    {
        $entry = new PelEntrySRational(42, [
            - 1,
            2,
        ]);
        $this->assertEquals($entry->getValue(), [
            - 1,
            2,
        ]);

        $caught = false;
        try {
            $entry->setValue([
                - 10,
                - 20,
            ], [
                - 1,
                - 2147483649,
            ]);
        } catch (PelOverflowException) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertEquals($entry->getValue(), [
            - 1,
            2,
        ]);

        $caught = false;
        try {
            $entry->setValue([
                3,
                4,
            ], [
                1,
                2147483648,
            ]);
        } catch (PelOverflowException) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertEquals($entry->getValue(), [
            - 1,
            2,
        ]);

        $caught = false;
        try {
            $entry->setValue([
                3,
                4,
            ], [
                4294967296,
                1,
            ]);
        } catch (PelOverflowException) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertEquals($entry->getValue(), [
            - 1,
            2,
        ]);
    }

    public function testReturnValues(): void
    {
        $entry = new PelEntrySRational(42);
        $this->assertEquals($entry->getValue(), []);

        $entry->setValue([
            - 1,
            2,
        ], [
            3,
            4,
        ], [
            5,
            - 6,
        ]);
        $this->assertEquals($entry->getValue(), [
            [
                - 1,
                2,
            ],
            [
                3,
                4,
            ],
            [
                5,
                - 6,
            ],
        ]);
        $this->assertSame('-1/2, 3/4, -5/6', $entry->getText());

        $entry->setValue([
            - 7,
            - 8,
        ]);
        $this->assertEquals($entry->getValue(), [
            - 7,
            - 8,
        ]);
        $this->assertSame('7/8', $entry->getText());

        $entry->setValue([
            0,
            2147483647,
        ]);
        $this->assertEquals($entry->getValue(), [
            0,
            2147483647,
        ]);
        $this->assertSame('0/2147483647', $entry->getText());
    }
}
