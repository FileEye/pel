<?php

declare(strict_types=1);

namespace Pel\Test;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\Pel;
use lsolesen\pel\PelEntryNumber;
use lsolesen\pel\PelOverflowException;

abstract class NumberTestCase extends TestCase
{

    protected int $min;

    protected int $max;

    protected PelEntryNumber $num;

    /**
     *
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        Pel::setStrictParsing(true);
    }

    public function testOverflow(): void
    {
        $this->num->setValue(0);
        $this->assertSame(0, $this->num->getValue());

        $caught = false;
        try {
            $this->num->setValue($this->min - 1);
        } catch (PelOverflowException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertSame(0, $this->num->getValue());

        $caught = false;
        try {
            $this->num->setValue($this->max + 1);
        } catch (PelOverflowException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertSame(0, $this->num->getValue());

        $caught = false;
        try {
            $this->num->setValue(0, $this->max + 1);
        } catch (PelOverflowException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertSame(0, $this->num->getValue());

        $caught = false;
        try {
            $this->num->setValue(0, $this->min - 1);
        } catch (PelOverflowException $e) {
            $caught = true;
        }
        $this->assertTrue($caught);
        $this->assertSame(0, $this->num->getValue());
    }

    public function testReturnValues(): void
    {
        $this->num->setValue(1, 2, 3);
        $this->assertSame([
            1,
            2,
            3
        ], $this->num->getValue());
        $this->assertSame('1, 2, 3', $this->num->getText());

        $this->num->setValue(1);
        $this->assertSame(1, $this->num->getValue());
        $this->assertSame('1', $this->num->getText());

        $this->num->setValue($this->max);
        $this->assertSame($this->max, $this->num->getValue());
        $this->assertSame((string) $this->max, $this->num->getText());

        $this->num->setValue($this->min);
        $this->assertSame($this->min, $this->num->getValue());
        $this->assertSame((string) $this->min, $this->num->getText());
    }
}
