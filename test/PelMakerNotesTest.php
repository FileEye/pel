<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelCanonMakerNotes;
use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelMakerNotes;
use PHPUnit\Framework\TestCase;

class PelMakerNotesTest extends TestCase
{
    public function testCreateMakerNotesFromManufacturer(): void
    {
        $parentMock = $this->createMock(PelIfd::class);
        $dataMock = $this->createMock(PelDataWindow::class);

        $makerNotes = PelMakerNotes::createMakerNotesFromManufacturer('Canon', $parentMock, $dataMock, 100, 0);
        $this->assertInstanceOf(PelCanonMakerNotes::class, $makerNotes);

        $makerNotes = PelMakerNotes::createMakerNotesFromManufacturer('Unknown', $parentMock, $dataMock, 100, 0);
        $this->assertNull($makerNotes);
    }
}
