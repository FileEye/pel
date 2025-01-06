<?php

declare(strict_types=1);

namespace lsolesen\pel;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PelJpegCommentTest extends TestCase
{
    #[DataProvider('commentProvider')]
    public function testConstructAndGetValue(string $initialComment, string $expected): void
    {
        $comment = new PelJpegComment($initialComment);
        $this->assertSame($expected, $comment->getValue());
    }

    /**
     * @return \Iterator<int, mixed>
     */
    public static function commentProvider(): \Iterator
    {
        yield ['Test comment', 'Test comment'];
        yield ['', ''];
        yield ['Another comment', 'Another comment'];
    }

    #[DataProvider('commentProvider')]
    public function testSetValue(string $newComment, string $expected): void
    {
        $comment = new PelJpegComment();
        $comment->setValue($newComment);
        $this->assertSame($expected, $comment->getValue());
    }

    #[DataProvider('commentProvider')]
    public function testToString(string $initialComment, string $expected): void
    {
        $comment = new PelJpegComment($initialComment);
        $this->assertSame($expected, (string) $comment);
    }

    public function testLoad(): void
    {
        $dataWindowMock = $this->createMock(PelDataWindow::class);
        $dataWindowMock->method('getBytes')->willReturn('Loaded comment');

        $comment = new PelJpegComment();
        $comment->load($dataWindowMock);

        $this->assertSame('Loaded comment', $comment->getValue());
    }

    public function testGetBytes(): void
    {
        $comment = new PelJpegComment('Byte comment');
        $this->assertSame('Byte comment', $comment->getBytes());
    }
}
