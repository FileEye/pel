<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\Pel;
use lsolesen\pel\PelJpegInvalidMarkerException;
use lsolesen\pel\PelJpegMarker;

class PelJpegMarkerTest extends TestCase
{
    public function testNames(): void
    {
        $jpegMarker = new PelJpegMarker();
        $this->assertEquals($jpegMarker::getName(PelJpegMarker::SOF0), 'SOF0');
        $this->assertEquals($jpegMarker::getName(PelJpegMarker::RST3), 'RST3');
        $this->assertEquals($jpegMarker::getName(PelJpegMarker::APP3), 'APP3');
        $this->assertEquals($jpegMarker::getName(PelJpegMarker::JPG11), 'JPG11');
        $this->assertEquals($jpegMarker::getName(100), Pel::fmt('Unknown marker: 0x%02X', 100));
    }

    public function testDescriptions(): void
    {
        $jpegMarker = new PelJpegMarker();
        $this->assertEquals($jpegMarker::getDescription(PelJpegMarker::SOF0), 'Encoding (baseline)');
        $this->assertEquals($jpegMarker::getDescription(PelJpegMarker::RST3), Pel::fmt('Restart %d', 3));
        $this->assertEquals($jpegMarker::getDescription(PelJpegMarker::APP3), Pel::fmt('Application segment %d', 3));
        $this->assertEquals($jpegMarker::getDescription(PelJpegMarker::JPG11), Pel::fmt('Extension %d', 11));
        $this->assertEquals($jpegMarker::getDescription(100), Pel::fmt('Unknown marker: 0x%02X', 100));
    }

    /**
     *
     * @throws PelJpegInvalidMarkerException
     */
    public function testInvalidMarkerException(): void
    {
        $this->expectException(PelJpegInvalidMarkerException::class);
        throw new PelJpegInvalidMarkerException(1, 2);
    }
}
