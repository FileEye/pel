<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntryByte;

class NumberByteTest extends NumberTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->num = new PelEntryByte(42);
        $this->min = 0;
        $this->max = 255;
    }
}
