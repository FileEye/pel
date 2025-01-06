<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\Pel;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelJpegMarker;

class MisplacedExifTest extends TestCase
{
    // NOTE: this test relies on the assumption that internal PelJpeg::sections order is kept between section
    // manipulations. It may fail it this changes.
    public function testRead(): void
    {
        Pel::clearExceptions();
        Pel::setStrictParsing(false);
        // Image contains non-EXIF APP1 section ahead of the EXIF one
        $jpeg = new PelJpeg(__DIR__ . '/broken_images/misplaced-exif.jpg');
        // Assert we just have loaded correct file for the test
        $this->assertNotInstanceOf('\lsolesen\pel\PelExif', $jpeg->getSection(PelJpegMarker::APP1));

        // Manually find exif APP1 section index
        $sections1 = $jpeg->getSections();
        $exifIdx = null;
        $idx = 0;
        foreach ($sections1 as $section) {
            if (($section[0] === PelJpegMarker::APP1) && ($section[1] instanceof PelExif)) {
                $exifIdx = $idx;
                break;
            }
            ++$idx;
        }
        $this->assertNotNull($exifIdx);
        $newExif = new PelExif();
        $jpeg->setExif($newExif);
        // Ensure EXIF is set to correct position among sections
        $sections2 = $jpeg->getSections();
        $this->assertSame($sections1[$exifIdx][0], $sections2[$exifIdx][0]);
        $this->assertNotSame($sections1[$exifIdx][1], $sections2[$exifIdx][1]);
        $this->assertSame($newExif, $sections2[$exifIdx][1]);

        $this->assertInstanceOf('\lsolesen\pel\PelExif', $jpeg->getExif());
        $jpeg->clearExif();
        // Assert that only EXIF section is gone and all other shifted correctly.
        $sections3 = $jpeg->getSections();
        $numSections3 = count($sections3);
        for ($idx = 0; $idx < $numSections3; ++$idx) {
            if ($idx >= $exifIdx) {
                $s2idx = $idx + 1;
            } else {
                $s2idx = $idx;
            }
            $this->assertSame($sections2[$s2idx][0], $sections3[$idx][0]);
            $this->assertSame($sections2[$s2idx][1], $sections3[$idx][1]);
        }
        $this->assertNull($jpeg->getExif());
    }
}
