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

    /**
     * Data read ahead from the file.
     *
     * PEL reads JPEG and TIFF data mostly in single bytes. Serving these
     * from a buffer avoids a seek and a read per byte, which is slow on
     * stream wrappers.
     */
    private string $buffer = '';

    /**
     * File offset of the first byte in the buffer.
     */
    private int $bufferOffset = 0;

    /**
     * Number of bytes read ahead into the buffer.
     *
     * The same as PHP's stream chunk size: the buffer stays alive as long as
     * the PelJpeg or PelTiff object, so it is kept small.
     */
    private const BUFFER_SIZE = 8192;

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

        if ($length < 1) {
            throw new PelException('Invalid length value received: ' . $length);
        }

        if ($offset >= $this->bufferOffset && $offset + $length <= $this->bufferOffset + strlen($this->buffer)) {
            return substr($this->buffer, $offset - $this->bufferOffset, $length);
        }

        if (fseek($this->handle, $offset) === -1) {
            throw new PelException('File stream seek failed for offset ' . $offset);
        }

        /*
         * fread() reads up to $length bytes, and on streams that are not
         * plain files (e.g. stream wrappers) at most one chunk of 8192
         * bytes per call, see https://www.php.net/fread. To get the full
         * length, stream_get_contents() reads until $length or EOF.
         */
        $content = stream_get_contents($this->handle, max($length, self::BUFFER_SIZE));

        if ($content === false) {
            throw new PelException('Failed to read from stream for length ' . $length);
        }

        if ($length > self::BUFFER_SIZE) {
            /* Do not keep large reads, such as the image data, in memory. */
            return $content;
        }

        $this->buffer = $content;
        $this->bufferOffset = $offset;

        return substr($content, 0, $length);
    }

    public function close(): void
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }

        $this->buffer = '';
    }
}
