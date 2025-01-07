<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * File stream for reading file.
 */
class PelFileStream
{
    /**
     * File handle resource
     */
    private mixed $handle = null;

    public function __construct(private readonly string $filename)
    {
        if (! file_exists($filename)) {
            throw new PelException('File does not exist "%s"', $filename);
        }

        $handle = fopen($this->filename, 'rb');

        if ($handle === false) {
            throw new PelException('Can not open file "%s"', $this->filename);
        }

        $this->handle = $handle;
    }

    public function filesize(): int
    {
        $filesize = filesize($this->filename);

        if ($filesize === false) {
            throw new PelException('Failed to get file size for "%s"', $this->filename);
        }

        return $filesize;
    }

    public function read(int $offset, int $length): string
    {
        if ($this->handle === null) {
            throw new PelException('File handle is null, can not read content');
        }

        if (fseek($this->handle, $offset) === -1) {
            throw new PelException('File stream seek failed for offset ' . $offset);
        }

        if ($length < 1) {
            throw new PelException('Invalid length value received: ' . $length);
        }

        $content = fread($this->handle, $length);

        if ($content === false) {
            throw new PelException('Failed to read from stream for length ' . $length);
        }

        return $content;
    }

    public function close(): void
    {
        if ($this->handle !== null) {
            fclose($this->handle);
        }
    }
}
