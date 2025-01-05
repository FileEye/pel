<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntrySLong;

class NumberSLongTest extends NumberTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->num = new PelEntrySLong(42);
        $this->min = - 2147483648;
        $this->max = 2147483647;
    }
}
