<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntrySShort;

class NumberSShortTest extends NumberTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->num = new PelEntrySShort(42);
        $this->min = - 32768;
        $this->max = 32767;
    }
}
