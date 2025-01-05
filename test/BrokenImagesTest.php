<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelIllegalFormatException;
use lsolesen\pel\PelJpeg;

class BrokenImagesTest extends TestCase
{

    public function testWindowWindowExceptionIsCaught(): void
    {
        $jpeg = new PelJpeg(dirname(__FILE__) . '/broken_images/gh-10-a.jpg');
        $this->assertNotSame('', $jpeg->getBytes());
    }

    public function testWindowOffsetExceptionIsCaught(): void
    {
        $jpeg = new PelJpeg(dirname(__FILE__) . '/broken_images/gh-10-b.jpg');
        $this->assertNotSame('', $jpeg->getBytes());
    }

    public function testParsingNotFailingOnRecursingIfd(): void
    {
        $jpeg = new PelJpeg(dirname(__FILE__) . '/broken_images/gh-11.jpg');
        $this->assertNotSame('', $jpeg->getBytes());
    }

    public function testInvalidIfd(): void
    {
        $this->expectException(PelIllegalFormatException::class) ;
        $jpeg = new PelJpeg(dirname(__FILE__) . '/broken_images/gh-156.jpg');
        $this->assertNotSame('', $jpeg->getBytes());
    }
}
