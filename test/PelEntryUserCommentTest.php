<?php

declare(strict_types=1);

namespace Pel\Test;

use lsolesen\pel\PelEntryUserComment;
use PHPUnit\Framework\TestCase;

class PelEntryUserCommentTest extends TestCase
{
    public function testUsercomment(): void
    {
        $entry = new PelEntryUserComment();
        $this->assertSame(8, $entry->getComponents());
        $this->assertSame('', $entry->getValue());
        $this->assertSame('ASCII', $entry->getEncoding());

        $entry->setValue('Hello!');
        $this->assertSame(14, $entry->getComponents());
        $this->assertSame('Hello!', $entry->getValue());
        $this->assertSame('ASCII', $entry->getEncoding());
    }
}
