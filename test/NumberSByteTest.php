<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntrySByte;

class NumberSByteTest extends NumberTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->num = new PelEntrySByte(42);
        $this->min = - 128;
        $this->max = 127;
    }
}
