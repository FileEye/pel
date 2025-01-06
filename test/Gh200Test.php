<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelJpeg;
use PHPUnit\Framework\TestCase;

class Gh200Test extends TestCase
{
    public function testPelDataWindowOffsetExceptionOffsetNotWithin(): void
    {
        $file = __DIR__ . '/images/gh-200.jpg';

        $fileContent = file_get_contents($file);

        $this->assertNotFalse($fileContent);

        $data = new PelDataWindow($fileContent);
        $pelJpeg = new PelJpeg($data);

        $this->assertNotSame('', $pelJpeg->getBytes());
    }
}
