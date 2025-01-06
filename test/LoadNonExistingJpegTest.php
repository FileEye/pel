<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelException;
use lsolesen\pel\PelJpeg;
use PHPUnit\Framework\TestCase;

class LoadNonExistingJpegTest extends TestCase
{
    public function testWindowWindowExceptionIsCaught(): void
    {
        $this->expectException(PelException::class);
        new PelJpeg('non-existing-file');
    }
}
