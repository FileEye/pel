<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelEntryWindowsString;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelTiff;
use PHPUnit\Framework\TestCase;

class Gh16Test extends TestCase
{
    protected string $file;

    protected function setUp(): void
    {
        $this->file = __DIR__ . '/images/gh-16-tmp.jpg';
        $file = __DIR__ . '/images/gh-16.jpg';
        copy($file, $this->file);
    }

    public function tearDown(): void
    {
        unlink($this->file);
    }

    public function testThisDoesNotWorkAsExpected(): void
    {
        $subject = 'Превед, медвед!';

        $fileContent = file_get_contents($this->file);

        $this->assertNotFalse($fileContent);

        $data = new PelDataWindow($fileContent);

        $this->assertTrue(PelJpeg::isValid($data));

        $jpeg = new PelJpeg();
        $jpeg->load($data);
        $exif = $jpeg->getExif();

        if ($exif === null) {
            $exif = new PelExif();
            $jpeg->setExif($exif);
            $tiff = new PelTiff();
            $exif->setTiff($tiff);
        }

        $tiff = $exif->getTiff();
        $this->assertNotNull($tiff);

        $ifd0 = $tiff->getIfd();
        if ($ifd0 === null) {
            $ifd0 = new PelIfd(PelIfd::IFD0);
            $tiff->setIfd($ifd0);
        }
        $ifd0->addEntry(new PelEntryWindowsString(PelTag::XP_SUBJECT, $subject));

        file_put_contents($this->file, $jpeg->getBytes());

        $jpeg = new PelJpeg($this->file);
        $exif = $jpeg->getExif();
        $this->assertNotNull($exif);
        $tiff = $exif->getTiff();
        $this->assertNotNull($tiff);
        $ifd0 = $tiff->getIfd();
        $this->assertNotNull($ifd0);
        $written_subject = $ifd0->getEntry(PelTag::XP_SUBJECT);
        $this->assertNotNull($written_subject);
        $this->assertEquals($subject, $written_subject->getValue());
    }
}
