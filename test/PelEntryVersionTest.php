<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelConvert;
use lsolesen\pel\PelEntryVersion;
use PHPUnit\Framework\TestCase;

class PelEntryVersionTest extends TestCase
{
    public function testVersion(): void
    {
        $entry = new PelEntryVersion(42);

        $this->assertEqualsWithDelta(0.0, $entry->getValue(), PHP_FLOAT_EPSILON);

        $entry->setValue(2.0);
        $this->assertEqualsWithDelta(2.0, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Version 2.0', $entry->getText(false));
        $this->assertSame('2.0', $entry->getText(true));
        $this->assertSame('0200', $entry->getBytes(PelConvert::LITTLE_ENDIAN));

        $entry->setValue(2.1);
        $this->assertEqualsWithDelta(2.1, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Version 2.1', $entry->getText(false));
        $this->assertSame('2.1', $entry->getText(true));
        $this->assertSame('0210', $entry->getBytes(PelConvert::LITTLE_ENDIAN));

        $entry->setValue(2.01);
        $this->assertEqualsWithDelta(2.01, $entry->getValue(), PHP_FLOAT_EPSILON);
        $this->assertSame('Version 2.01', $entry->getText(false));
        $this->assertSame('2.01', $entry->getText(true));
        $this->assertSame('0201', $entry->getBytes(PelConvert::LITTLE_ENDIAN));
    }
}
