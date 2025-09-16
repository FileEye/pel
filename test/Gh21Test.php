<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelJpeg;
use PHPUnit\Framework\TestCase;

class Gh21Test extends TestCase
{
    protected string $file;

    public function setUp(): void
    {
        $this->file = __DIR__ . '/images/gh-21-tmp.jpg';
        $file = __DIR__ . '/images/gh-21.jpg';
        copy($file, $this->file);
    }

    public function tearDown(): void
    {
        unlink($this->file);
    }

    public function testThisDoesNotWorkAsExpected(): void
    {
        $scale = 0.75;
        $input_jpeg = new PelJpeg($this->file);

        $original = imagecreatefromstring($input_jpeg->getBytes());

        $this->assertNotFalse($original, 'New image must not be false');

        $original_w = imagesx($original);
        $original_h = imagesy($original);

        $scaled_w = max(1, (int) ($original_w * $scale));
        $scaled_h = max(1, (int) ($original_h * $scale));

        $scaled = imagecreatetruecolor($scaled_w, $scaled_h);
        $this->assertNotFalse($scaled, 'Resized image must not be false');

        imagecopyresampled($scaled, $original, 0, 0, 0, 0, $scaled_w, $scaled_h, $original_w, $original_h);

        $output_jpeg = new PelJpeg($scaled);

        $exif = $input_jpeg->getExif();

        if ($exif !== null) {
            $output_jpeg->setExif($exif);
        }

        file_put_contents($this->file, $output_jpeg->getBytes());

        $jpeg = new PelJpeg($this->file);
        $exifin = $jpeg->getExif();
        $this->assertEquals($exif, $exifin);
    }
}
