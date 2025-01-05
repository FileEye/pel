<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelFormat;
use lsolesen\pel\PelIllegalFormatException;

class PelFormatTest extends TestCase
{

    public function testNames(): void
    {
        $pelFormat = new PelFormat();
        $this->assertEquals($pelFormat::getName(PelFormat::ASCII), 'Ascii');
        $this->assertEquals($pelFormat::getName(PelFormat::FLOAT), 'Float');
        $this->assertEquals($pelFormat::getName(PelFormat::UNDEFINED), 'Undefined');
        $this->expectException(PelIllegalFormatException::class);
        $pelFormat::getName(100);
    }

    public function testDescriptions(): void
    {
        $pelFormat = new PelFormat();
        $this->assertEquals($pelFormat::getSize(PelFormat::ASCII), 1);
        $this->assertEquals($pelFormat::getSize(PelFormat::FLOAT), 4);
        $this->assertEquals($pelFormat::getSize(PelFormat::UNDEFINED), 1);
        $this->expectException(PelIllegalFormatException::class);
        $pelFormat::getSize(100);
    }
}
