<?php

declare(strict_types=1);

namespace lsolesen\pel;

use PHPUnit\Framework\TestCase;
use lsolesen\pel\PelJpegComment;
use lsolesen\pel\PelDataWindow;

class PelJpegCommentTest extends TestCase
{
    /**
     * @dataProvider commentProvider
     */
    public function testConstructAndGetValue(string $initialComment, string $expected): void
    {
        $comment = new PelJpegComment($initialComment);
        $this->assertEquals($expected, $comment->getValue());
    }

    /**
     * @return array<int, mixed>
     */
    public static function commentProvider(): array
    {
        return [
            ['Test comment', 'Test comment'],
            ['', ''],
            ['Another comment', 'Another comment'],
        ];
    }

    /**
     * @dataProvider commentProvider
     */
    public function testSetValue(string $newComment, string $expected): void
    {
        $comment = new PelJpegComment();
        $comment->setValue($newComment);
        $this->assertEquals($expected, $comment->getValue());
    }

    /**
     * @dataProvider commentProvider
     */
    public function testToString(string $initialComment, string $expected): void
    {
        $comment = new PelJpegComment($initialComment);
        $this->assertEquals($expected, (string)$comment);
    }

    public function testLoad(): void
    {
        $dataWindowMock = $this->createMock(PelDataWindow::class);
        $dataWindowMock->method('getBytes')->willReturn('Loaded comment');

        $comment = new PelJpegComment();
        $comment->load($dataWindowMock);

        $this->assertEquals('Loaded comment', $comment->getValue());
    }

    public function testGetBytes(): void
    {
        $comment = new PelJpegComment('Byte comment');
        $this->assertEquals('Byte comment', $comment->getBytes());
    }
}
