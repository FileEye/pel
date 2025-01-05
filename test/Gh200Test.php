<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\Pel;
use lsolesen\pel\PelExif;

class Gh200Test extends TestCase
{
    public function testPelDataWindowOffsetExceptionOffsetNotWithin(): void
    {
        $file = dirname(__FILE__) . '/images/gh-200.jpg';

        $fileContent = file_get_contents($file);

        $this->assertNotFalse($fileContent);

        $data = new PelDataWindow($fileContent);
        $pelJpeg = new PelJpeg($data);

        $this->assertNotSame('', $pelJpeg->getBytes());
    }
}
