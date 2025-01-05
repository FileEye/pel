<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelConvert;
use lsolesen\pel\PelEntryWindowsString;
use lsolesen\pel\PelTag;

class PelEntryWindowsStringTest extends TestCase
{
    public function testWindowsString(): void
    {
        $test_str = 'Tést';
        $test_str_ucs2 = mb_convert_encoding($test_str, 'UCS-2LE', 'auto');
        $test_str_ucs2_zt = $test_str_ucs2 . PelEntryWindowsString::ZEROES;

        $entry = new PelEntryWindowsString(PelTag::XP_TITLE, $test_str);
        $this->assertNotEquals($entry->getValue(), $entry->getBytes(PelConvert::LITTLE_ENDIAN));
        $this->assertEquals($entry->getValue(), $test_str);
        $this->assertEquals($entry->getBytes(PelConvert::LITTLE_ENDIAN), $test_str_ucs2_zt);

        // correct zero-terminated data from the exif
        $entry->setValue($test_str_ucs2_zt, true);
        $this->assertNotEquals($entry->getValue(), $entry->getBytes(PelConvert::LITTLE_ENDIAN));
        $this->assertEquals($entry->getValue(), $test_str);
        $this->assertEquals($entry->getBytes(PelConvert::LITTLE_ENDIAN), $test_str_ucs2_zt);

        // incorrect data from exif
        $entry->setValue($test_str_ucs2, true);
        $this->assertNotEquals($entry->getValue(), $entry->getBytes(PelConvert::LITTLE_ENDIAN));
        $this->assertEquals($entry->getValue(), $test_str);
        $this->assertEquals($entry->getBytes(PelConvert::LITTLE_ENDIAN), $test_str_ucs2_zt);
    }
}
