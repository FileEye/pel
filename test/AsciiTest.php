<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelConvert;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryCopyright;
use lsolesen\pel\PelEntryTime;
use lsolesen\pel\PelFormat;
use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelTag;
use PHPUnit\Framework\TestCase;

class AsciiTest extends TestCase
{
    public function testReturnValues(): void
    {
        $entry = new PelEntryAscii(42);
        $this->assertSame(PelFormat::ASCII, $entry->getFormat());

        $entry = new PelEntryAscii(42, 'foo bar baz');
        $this->assertSame(PelFormat::ASCII, $entry->getFormat());
        $this->assertSame(12, $entry->getComponents());
        $this->assertSame('foo bar baz', $entry->getValue());
    }

    public function testTime(): void
    {
        $entry = new PelEntryTime(PelTag::DATE_TIME_ORIGINAL, 10);

        $this->assertSame(PelFormat::ASCII, $entry->getFormat());
        $this->assertSame(PelTag::DATE_TIME_ORIGINAL, $entry->getTag());
        $this->assertSame(20, $entry->getComponents());
        $this->assertSame('10', $entry->getValue());
        $this->assertSame('10', $entry->getValue(PelEntryTime::UNIX_TIMESTAMP));
        $this->assertSame('1970:01:01 00:00:10', $entry->getValue(PelEntryTime::EXIF_STRING));
        $this->assertSame($entry->getValue(PelEntryTime::JULIAN_DAY_COUNT), (string) (int) (2440588 + round(10 / 86400, 2)));
        $this->assertSame('1970:01:01 00:00:10', $entry->getText());
        $this->assertSame($entry->getBytes(PelConvert::LITTLE_ENDIAN), '1970:01:01 00:00:10' . chr(0x00));

        // Malformed Exif timestamp.
        $entry->setValue('1970!01-01 00 00 30', PelEntryTime::EXIF_STRING);
        $this->assertSame('30', $entry->getValue());

        $entry->setValue(2415021.75, PelEntryTime::JULIAN_DAY_COUNT);
        // This is Jan 1st 1900 at 18:00, outside the range of a UNIX
        $caught = false;
        try {
            $entry->getValue();
        } catch (PelInvalidArgumentException) {
            $caught = true;
        }
        $this->assertTrue($caught);

        $this->assertSame('1900:01:01 18:00:00', $entry->getValue(PelEntryTime::EXIF_STRING));
        $this->assertEqualsWithDelta(2415021.75, $entry->getValue(PelEntryTime::JULIAN_DAY_COUNT), PHP_FLOAT_EPSILON);

        $entry->setValue('0000:00:00 00:00:00', PelEntryTime::EXIF_STRING);

        $caught = false;
        try {
            $entry->getValue();
        } catch (PelInvalidArgumentException) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertSame('0000:00:00 00:00:00', $entry->getValue(PelEntryTime::EXIF_STRING));
        $this->assertSame('0', $entry->getValue(PelEntryTime::JULIAN_DAY_COUNT));

        $entry->setValue('9999:12:31 23:59:59', PelEntryTime::EXIF_STRING);

        // this test will fail on 32bit machines
        $this->assertSame('253402300799', $entry->getValue());
        $this->assertSame('9999:12:31 23:59:59', $entry->getValue(PelEntryTime::EXIF_STRING));
        $this->assertSame($entry->getValue(PelEntryTime::JULIAN_DAY_COUNT), (string) (int) (5373484 + round(86399 / 86400, 2)));

        // Check day roll-over for SF bug #1699489.
        $entry->setValue('2007:04:23 23:30:00', PelEntryTime::EXIF_STRING);
        $t = $entry->getValue(PelEntryTime::UNIX_TIMESTAMP);
        $entry->setValue((int) $t + 3600);

        $this->assertSame('2007:04:24 00:30:00', $entry->getValue(PelEntryTime::EXIF_STRING));
    }

    public function testCopyright(): void
    {
        $entry = new PelEntryCopyright();
        $this->assertSame(PelFormat::ASCII, $entry->getFormat());
        $this->assertSame(PelTag::COPYRIGHT, $entry->getTag());
        $value = $entry->getValueArray();
        $this->assertEquals('', $value[0]);
        $this->assertEquals('', $value[1]);
        $this->assertSame('', $entry->getText(false));
        $this->assertSame('', $entry->getText(true));

        $entry->setValue('A');
        $value = $entry->getValueArray();
        $this->assertEquals('A', $value[0]);
        $this->assertEquals('', $value[1]);
        $this->assertSame('A (Photographer)', $entry->getText(false));
        $this->assertSame('A', $entry->getText(true));
        $this->assertSame($entry->getBytes(PelConvert::LITTLE_ENDIAN), 'A' . chr(0));

        $entry->setValue('', 'B');
        $value = $entry->getValueArray();
        $this->assertEquals('', $value[0]);
        $this->assertEquals('B', $value[1]);
        $this->assertSame('B (Editor)', $entry->getText(false));
        $this->assertSame('B', $entry->getText(true));
        $this->assertSame($entry->getBytes(PelConvert::LITTLE_ENDIAN), ' ' . chr(0) . 'B' . chr(0));

        $entry->setValue('A', 'B');
        $value = $entry->getValueArray();
        $this->assertEquals('A', $value[0]);
        $this->assertEquals('B', $value[1]);
        $this->assertSame('A (Photographer) - B (Editor)', $entry->getText(false));
        $this->assertSame('A - B', $entry->getText(true));
        $this->assertSame($entry->getBytes(PelConvert::LITTLE_ENDIAN), 'A' . chr(0) . 'B' . chr(0));
    }
}
