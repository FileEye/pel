<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\Pel;
use lsolesen\pel\PelJpegInvalidMarkerException;
use lsolesen\pel\PelJpegMarker;
use PHPUnit\Framework\TestCase;

class PelJpegMarkerTest extends TestCase
{
    public function testNames(): void
    {
        $jpegMarker = new PelJpegMarker();
        $this->assertSame('SOF0', $jpegMarker::getName(PelJpegMarker::SOF0));
        $this->assertSame('RST3', $jpegMarker::getName(PelJpegMarker::RST3));
        $this->assertSame('APP3', $jpegMarker::getName(PelJpegMarker::APP3));
        $this->assertSame('JPG11', $jpegMarker::getName(PelJpegMarker::JPG11));
        $this->assertSame($jpegMarker::getName(100), Pel::fmt('Unknown marker: 0x%02X', 100));
    }

    public function testDescriptions(): void
    {
        $jpegMarker = new PelJpegMarker();
        $this->assertSame('Encoding (baseline)', $jpegMarker::getDescription(PelJpegMarker::SOF0));
        $this->assertSame($jpegMarker::getDescription(PelJpegMarker::RST3), Pel::fmt('Restart %d', 3));
        $this->assertSame($jpegMarker::getDescription(PelJpegMarker::APP3), Pel::fmt('Application segment %d', 3));
        $this->assertSame($jpegMarker::getDescription(PelJpegMarker::JPG11), Pel::fmt('Extension %d', 11));
        $this->assertSame($jpegMarker::getDescription(100), Pel::fmt('Unknown marker: 0x%02X', 100));
    }

    /**
     * @throws PelJpegInvalidMarkerException
     */
    public function testInvalidMarkerException(): void
    {
        $this->expectException(PelJpegInvalidMarkerException::class);
        throw new PelJpegInvalidMarkerException(1, 2);
    }
}
