<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelCanonMakerNotes;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelMakerNotesMalformedException;
use PHPUnit\Framework\TestCase;

class PelCanonMakerNotesTest extends TestCase
{
    public function testConstructor(): void
    {
        $parentMock = $this->createMock(PelIfd::class);
        $dataMock = $this->createMock(PelDataWindow::class);

        $makerNotes = new PelCanonMakerNotes($parentMock, $dataMock, 100, 0);
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(PelCanonMakerNotes::class, $makerNotes);
    }

    public function testLoad(): void
    {
        $parentMock = $this->createMock(PelIfd::class);
        $dataMock = $this->createMock(PelDataWindow::class);
        $dataMock->method('getShort')->willReturn(4);
        $dataMock->method('getLong')->willReturn(2);

        $makerNotes = new PelCanonMakerNotes($parentMock, $dataMock, 4, 2);
        $makerNotes->load();
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(PelCanonMakerNotes::class, $makerNotes);
    }

    public function testParseCameraSettingsThrowsException(): void
    {
        $this->expectException(PelMakerNotesMalformedException::class);

        $parentMock = $this->createMock(PelIfd::class);
        $dataMock = $this->createMock(PelDataWindow::class);
        $dataMock->method('getShort')->willReturn(1);

        $makerNotes = new PelCanonMakerNotes($parentMock, $dataMock, 100, 2);
        $reflection = new \ReflectionClass($makerNotes);
        $method = $reflection->getMethod('parseCameraSettings');
        $method->setAccessible(true);

        $method->invoke($makerNotes, $parentMock, $dataMock, 0, 0);
    }
}
