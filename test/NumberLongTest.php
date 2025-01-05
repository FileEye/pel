<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntryLong;

class NumberLongTest extends NumberTestCase
{
    /**
     *
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->num = new PelEntryLong(42);
        $this->min = 0;
        $this->max = 4294967295;
    }
}
