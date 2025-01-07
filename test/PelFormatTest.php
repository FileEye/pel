<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelFormat;
use lsolesen\pel\PelIllegalFormatException;
use PHPUnit\Framework\TestCase;

class PelFormatTest extends TestCase
{
    public function testNames(): void
    {
        $pelFormat = new PelFormat();
        $this->assertSame('Ascii', $pelFormat::getName(PelFormat::ASCII));
        $this->assertSame('Float', $pelFormat::getName(PelFormat::FLOAT));
        $this->assertSame('Undefined', $pelFormat::getName(PelFormat::UNDEFINED));
        $this->expectException(PelIllegalFormatException::class);
        $pelFormat::getName(100);
    }

    public function testDescriptions(): void
    {
        $pelFormat = new PelFormat();
        $this->assertSame(1, $pelFormat::getSize(PelFormat::ASCII));
        $this->assertSame(4, $pelFormat::getSize(PelFormat::FLOAT));
        $this->assertSame(1, $pelFormat::getSize(PelFormat::UNDEFINED));
        $this->expectException(PelIllegalFormatException::class);
        $pelFormat::getSize(100);
    }
}
