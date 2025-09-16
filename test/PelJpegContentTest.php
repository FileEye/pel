<?php

declare(strict_types=1);

namespace lsolesen\pel;

use PHPUnit\Framework\TestCase;

class PelJpegContentTest extends TestCase
{
    public function testGetBytesWithData(): void
    {
        $dataWindowMock = $this->createMock(PelDataWindow::class);
        $dataWindowMock->method('getBytes')->willReturn('JPEG content bytes');

        $content = new PelJpegContent($dataWindowMock);
        $this->assertSame('JPEG content bytes', $content->getBytes());
    }

    public function testGetBytesWithoutData(): void
    {
        $content = new PelJpegContent(null);
        $this->assertSame('', $content->getBytes());
    }
}
