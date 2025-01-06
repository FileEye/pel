<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\Pel;
use lsolesen\pel\PelJpeg;
use PHPUnit\Framework\TestCase;

class NoExifTest extends TestCase
{
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        $jpeg = new PelJpeg(__DIR__ . '/images/no-exif.jpg');

        $exif = $jpeg->getExif();
        $this->assertNull($exif);

        $this->assertCount(0, Pel::getExceptions());
    }
}
