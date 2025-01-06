<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntryShort;

class NumberShortTest extends NumberTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->num = new PelEntryShort(42);
        $this->min = 0;
        $this->max = 65535;
    }
}
