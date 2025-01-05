<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTag;

class Gh77Test extends TestCase
{

    public function testReturnModul(): void
    {
        $file = dirname(__FILE__) . '/images/gh-77.jpg';

        $input_jpeg = new PelJpeg($file);
        $app1 = $input_jpeg->getExif();
        $this->assertNotNull($app1);

        $tiff = $app1->getTiff();
        $this->assertNotNull($tiff);

        $ifd0 = $tiff->getIfd();
        $this->assertNotNull($ifd0);

        $model = $ifd0->getEntry(PelTag::MODEL);
        $this->assertNotNull($model);

        $this->assertEquals($model->getValue(), "Canon EOS 5D Mark III");
    }
}
