<?php

declare(strict_types=1);

namespace Pel\Test;

use Exception;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryAscii;
use PHPUnit\Framework\TestCase;

class Bug3017880Test extends TestCase
{

    public function testThisDoesNotWorkAsExpected(): void
    {
        $filename = dirname(__FILE__) . '/images/bug3017880.jpg';
        try {
            $resave_file = 0;
            $jpeg = new PelJpeg($filename);
            $this->assertNotSame('', $jpeg->getBytes());

            // should all exif data on photo be cleared (gd and iu will always strip it anyway, so only
            // force strip if you know the image you're branding is an original)
            // $jpeg->clearExif();

            $exif = new PelExif();
            $jpeg->setExif($exif);
            $tiff = new PelTiff();
            $exif->setTiff($tiff);

            $tiff = $exif->getTiff();
            $this->assertNotNull($tiff);
            $ifd0 = $tiff->getIfd();
            if ($ifd0 === null) {
                $ifd0 = new PelIfd(PelIfd::IFD0);
                $tiff->setIfd($ifd0);
            }

            $software_name = 'Example V2';
            $software = $ifd0->getEntry(PelTag::SOFTWARE);

            if ($software === null) {
                $software = new PelEntryAscii(PelTag::SOFTWARE, $software_name);
                $ifd0->addEntry($software);
                $resave_file = 1;
            } else {
                $software->setValue($software_name);
                $resave_file = 1;
            }

            if (! file_put_contents($filename, $jpeg->getBytes())) {
                // if it was okay to resave the file, but it did not save correctly
            }
        } catch (Exception $e) {
            $this->fail('Test should not throw an exception');
        }
    }
}
