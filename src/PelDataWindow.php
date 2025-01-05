<?php

declare(strict_types=1);

namespace lsolesen\pel;

use GdImage;
use Stringable;

/**
 * The window.
 *
 * @package PEL
 */
class PelDataWindow implements Stringable
{
    /**
     * The data held by this window.
     *
     * The string can contain any kind of data, including binary data.
     */
    private string $data = '';

    /**
     * File stream
     */
    private ?PelFileStream $stream = null;

    /**
     * The start of the current window.
     *
     * All offsets used for access into the data will count from this
     * offset, effectively limiting access to a window starting at this
     * byte.
     *
     * @see PelDataWindow::setWindowStart
     */
    private int $start = 0;

    /**
     * The size of the current window.
     *
     * All offsets used for access into the data will be limited by this
     * variable. A valid offset must be strictly less than this
     * variable.
     *
     * @see PelDataWindow::setWindowSize
     */
    private int $size = 0;

    /**
     * Construct a new data window with the data supplied.
     *
     * @param string|GdImage $data
     *            the data that this window will contain. This can
     *            either be given as a string (interpreted literally as a sequence
     *            of bytes) or a PHP image resource handle. The data will be copied
     *            into the new data window.
     * @param bool $order
     *            the initial byte order of the window. This must
     *            be either {@link PelConvert::LITTLE_ENDIAN} or {@link PelConvert::BIG_ENDIAN}. This will be used when integers are
     *            read from the data, and it can be changed later with {@link setByteOrder()}.
     *
     * @throws PelInvalidArgumentException if $data was of invalid type
     */
    public function __construct(string|PelFileStream|GdImage $data = '', private bool $order = PelConvert::LITTLE_ENDIAN)
    {
        if ($data instanceof PelFileStream) {
            $this->stream = $data;
            $this->size = $data->filesize();
            return;
        }

        if (is_string($data)) {
            $this->data = $data;
        } elseif ($data instanceof GdImage) {
            /*
             * The ImageJpeg() function insists on printing the bytes
             * instead of returning them in a more civil way as a string, so
             * we have to buffer the output...
             */
            ob_start();
            imagejpeg($data, null, Pel::getJPEGQuality());
            $dataBytes = ob_get_clean();

            if ($dataBytes === false) {
                throw new PelDataWindowWindowException('Failed to create window object from JPEG');
            }

            $this->data = $dataBytes;
        }
        $this->size = strlen($this->data);
    }

    /**
     * Return a string representation of the data window.
     *
     * @return string a description of the window with information about
     *         the number of bytes accessible, the total number of bytes, and
     *         the window start and stop.
     */
    public function __toString(): string
    {
        if ($this->stream !== null) {
            return Pel::fmt('DataWindow: %d bytes in [%d, %d] of file stream', $this->size, $this->start, $this->start + $this->size);
        }
        return Pel::fmt('DataWindow: %d bytes in [%d, %d] of %d bytes', $this->size, $this->start, $this->start + $this->size, strlen($this->data));
    }

    /**
     * Get the size of the data window.
     *
     * @return int the number of bytes covered by the window. The
     *         allowed offsets go from 0 up to this number minus one.
     *
     * @see getBytes()
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Change the byte order of the data.
     *
     * @param bool $order
     *            the new byte order. This must be either
     *            {@link PelConvert::LITTLE_ENDIAN} or {@link PelConvert::BIG_ENDIAN}.
     */
    public function setByteOrder(bool $order): void
    {
        $this->order = $order;
    }

    /**
     * Get the currently used byte order.
     *
     * @return bool this will be either {@link PelConvert::LITTLE_ENDIAN} or {@link PelConvert::BIG_ENDIAN}.
     */
    public function getByteOrder(): bool
    {
        return $this->order;
    }

    /**
     * Move the start of the window forward.
     *
     * @param int $start
     *            the new start of the window. All new offsets will be
     *            calculated from this new start offset, and the size of the window
     *            will shrink to keep the end of the window in place.
     *
     * @throws PelDataWindowWindowException
     */
    public function setWindowStart(int $start): void
    {
        if ($start < 0 || $start > $this->size) {
            throw new PelDataWindowWindowException('Window [%d, %d] does not fit in window [0, %d]', $start, $this->size, $this->size);
        }
        $this->start += $start;
        $this->size -= $start;
    }

    /**
     * Adjust the size of the window.
     * The size can only be made smaller.
     *
     * @param int $size
     *            the desired size of the window. If the argument is
     *            negative, the window will be shrunk by the argument.
     *
     * @throws PelDataWindowWindowException
     */
    public function setWindowSize(int $size): void
    {
        if ($size < 0) {
            $size += $this->size;
        }
        if ($size < 0 || $size > $this->size) {
            throw new PelDataWindowWindowException('Window [0, %d] does not fit in window [0, %d]', $size, $this->size);
        }
        $this->size = $size;
    }

    /**
     * Make a new data window with the same data as the this window.
     *
     * @param int|null $start
     *            if an integer is supplied, then it will be the start
     *            of the window in the clone. If left unspecified, then the clone
     *            will inherit the start from this object.
     * @param int|null $size
     *            if an integer is supplied, then it will be the size
     *            of the window in the clone. If left unspecified, then the clone
     *            will inherit the size from this object.
     *
     * @return PelDataWindow a new window that operates on the same data
     *         as this window, but (optionally) with a smaller window size.
     */
    public function getClone(?int $start = null, ?int $size = null): PelDataWindow
    {
        if ($this->stream !== null) {
            $c = new PelDataWindow($this->stream, $this->order);
        } else {
            $c = new PelDataWindow($this->data, $this->order);
        }

        $c->setWindowStart($this->start);
        $c->setWindowSize($this->size);

        if ($start !== null) {
            $c->setWindowStart($start);
        }
        if ($size !== null) {
            $c->setWindowSize($size);
        }

        return $c;
    }

    /**
     * Return some or all bytes visible in the window.
     *
     * This method works just like the standard {@link substr()}
     * function in PHP with the exception that it works within the
     * window of accessible bytes and does strict range checking.
     *
     * @param int|null $start
     *            the offset to the first byte returned. If a negative
     *            number is given, then the counting will be from the end of the
     *            window. Invalid offsets will result in a {@link PelDataWindowOffsetException} being thrown.
     * @param int|null $size
     *            the size of the sub-window. If a negative number is
     *            given, then that many bytes will be omitted from the result.
     *
     * @return string a subset of the bytes in the window. This will
     *         always return no more than {@link getSize()} bytes.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getBytes(?int $start = null, ?int $size = null): string
    {
        if (is_int($start)) {
            if ($start < 0) {
                $start += $this->size;
            }

            $this->validateOffset($start);
        } else {
            $start = 0;
        }

        if (is_int($size)) {
            if ($size <= 0) {
                $size += $this->size - $start;
            }

            $this->validateOffset($start + $size);
        } else {
            $size = $this->size - $start;
        }

        if ($this->stream !== null) {
            $bytes = $this->stream->read($this->start + $start, $size);
        } else {
            $bytes = substr($this->data, $this->start + $start, $size);
        }

        return $bytes;
    }

    /**
     * Return an unsigned byte from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first byte in the current allowed window. The last
     *            valid offset is equal to {@link getSize()}-1. Invalid offsets
     *            will result in a {@link PelDataWindowOffsetException} being
     *            thrown.
     *
     * @return int the unsigned byte found at offset.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getByte(int $offset = 0): int
    {
        /*
         * Validate the offset --- this throws an exception if offset is
         * out of range.
         */
        $this->validateOffset($offset);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Return an unsigned byte. */
        if ($this->stream !== null) {
            return PelConvert::streamToByte($this->stream, $offset);
        }
        return PelConvert::bytesToByte($this->data, $offset);
    }

    /**
     * Return a signed byte from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first byte in the current allowed window. The last
     *            valid offset is equal to {@link getSize()}-1. Invalid offsets
     *            will result in a {@link PelDataWindowOffsetException} being
     *            thrown.
     *
     * @return int the signed byte found at offset.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getSByte(int $offset = 0): int
    {
        /*
         * Validate the offset --- this throws an exception if offset is
         * out of range.
         */
        $this->validateOffset($offset);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Return a signed byte. */
        if ($this->stream !== null) {
            return PelConvert::streamToSByte($this->stream, $offset);
        }
        return PelConvert::bytesToSByte($this->data, $offset);
    }

    /**
     * Return an unsigned short read from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first short available in the current allowed window.
     *            The last valid offset is equal to {@link getSize()}-2. Invalid
     *            offsets will result in a {@link PelDataWindowOffsetException}
     *            being thrown.
     *
     * @return int the unsigned short found at offset.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getShort(int $offset = 0): int
    {
        /*
         * Validate the offset+1 to see if we can safely get two bytes ---
         * this throws an exception if offset is out of range.
         */
        $this->validateOffset($offset);
        $this->validateOffset($offset + 1);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Return an unsigned short. */
        if ($this->stream !== null) {
            return PelConvert::streamToShort($this->stream, $offset, $this->order);
        }
        return PelConvert::bytesToShort($this->data, $offset, $this->order);
    }

    /**
     * Return a signed short read from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first short available in the current allowed window.
     *            The last valid offset is equal to {@link getSize()}-2. Invalid
     *            offsets will result in a {@link PelDataWindowOffsetException}
     *            being thrown.
     *
     * @return int the signed short found at offset.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getSShort(int $offset = 0): int
    {
        /*
         * Validate the offset+1 to see if we can safely get two bytes ---
         * this throws an exception if offset is out of range.
         */
        $this->validateOffset($offset);
        $this->validateOffset($offset + 1);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Return a signed short. */
        if ($this->stream !== null) {
            return PelConvert::streamToSShort($this->stream, $offset, $this->order);
        }
        return PelConvert::bytesToSShort($this->data, $offset, $this->order);
    }

    /**
     * Return an unsigned long read from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first long available in the current allowed window.
     *            The last valid offset is equal to {@link getSize()}-4. Invalid
     *            offsets will result in a {@link PelDataWindowOffsetException}
     *            being thrown.
     *
     * @return int the unsigned long found at offset.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getLong(int $offset = 0): int
    {
        /*
         * Validate the offset+3 to see if we can safely get four bytes
         * --- this throws an exception if offset is out of range.
         */
        $this->validateOffset($offset);
        $this->validateOffset($offset + 3);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Return an unsigned long. */
        if ($this->stream !== null) {
            return PelConvert::streamToLong($this->stream, $offset, $this->order);
        }
        return PelConvert::bytesToLong($this->data, $offset, $this->order);
    }

    /**
     * Return a signed long read from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first long available in the current allowed window.
     *            The last valid offset is equal to {@link getSize()}-4. Invalid
     *            offsets will result in a {@link PelDataWindowOffsetException}
     *            being thrown.
     *
     * @return int the signed long found at offset.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getSLong(int $offset = 0): int
    {
        /*
         * Validate the offset+3 to see if we can safely get four bytes
         * --- this throws an exception if offset is out of range.
         */
        $this->validateOffset($offset);
        $this->validateOffset($offset + 3);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Return a signed long. */
        if ($this->stream !== null) {
            return PelConvert::streamToSLong($this->stream, $offset, $this->order);
        }
        return PelConvert::bytesToSLong($this->data, $offset, $this->order);
    }

    /**
     * Return an unsigned rational read from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first rational available in the current allowed
     *            window. The last valid offset is equal to {@link getSize()}-8.
     *            Invalid offsets will result in a {@link PelDataWindowOffsetException} being thrown.
     *
     * @return array<int, int> the unsigned rational found at offset. A rational
     *         number is represented as an array of two numbers: the enumerator
     *         and denominator. Both of these numbers will be unsigned longs.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getRational(int $offset = 0): array
    {
        return [
            $this->getLong($offset),
            $this->getLong($offset + 4),
        ];
    }

    /**
     * Return a signed rational read from the data.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will
     *            return the first rational available in the current allowed
     *            window. The last valid offset is equal to {@link getSize()}-8.
     *            Invalid offsets will result in a {@link PelDataWindowOffsetException} being thrown.
     *
     * @return array<int, int> the signed rational found at offset. A rational
     *         number is represented as an array of two numbers: the enumerator
     *         and denominator. Both of these numbers will be signed longs.
     *
     * @throws PelDataWindowOffsetException
     */
    public function getSRational(int $offset = 0): array
    {
        return [
            $this->getSLong($offset),
            $this->getSLong($offset + 4),
        ];
    }

    /**
     * String comparison on substrings.
     *
     * @param int $offset
     *            the offset into the data. An offset of zero will make
     *            the comparison start with the very first byte available in the
     *            window. The last valid offset is equal to {@link getSize()}
     *            minus the length of the string. If the string is too long, then
     *            a {@link PelDataWindowOffsetException} will be thrown.
     * @param string $str
     *            the string to compare with.
     *
     * @return bool true if the string given matches the data in the
     *         window, at the specified offset, false otherwise. The comparison
     *         will stop as soon as a mismatch if found.
     *
     * @throws PelDataWindowOffsetException
     */
    public function strcmp(int $offset, string $str): bool
    {
        /*
         * Validate the offset of the final character we might have to
         * check.
         */
        $s = strlen($str);
        $this->validateOffset($offset);
        $this->validateOffset($offset + $s - 1);

        /* Translate the offset into an offset into the data. */
        $offset += $this->start;

        /* Check each character, return as soon as the answer is known. */
        for ($i = 0; $i < $s; $i++) {
            if ($this->stream !== null) {
                $chr = $this->stream->read($offset + $i, 1);
            } else {
                $chr = $this->data[$offset + $i];
            }

            if ($chr !== $str[$i]) {
                return false;
            }
        }

        /* All characters matches each other, return true. */
        return true;
    }

    /**
     * Validate an offset against the current window.
     *
     * @param int $offset
     *            the offset to be validated. If the offset is negative
     *            or if it is greater than or equal to the current window size,
     *            then a {@link PelDataWindowOffsetException} is thrown.
     *
     * @return void if the offset is valid nothing is returned, if it is
     *         invalid a new {@link PelDataWindowOffsetException} is thrown.
     *
     * @throws PelDataWindowOffsetException
     */
    private function validateOffset(int $offset): void
    {
        if ($offset < 0 || $offset >= $this->size) {
            throw new PelDataWindowOffsetException('Offset %d not within [%d, %d]', $offset, 0, $this->size - 1);
        }
    }
}
