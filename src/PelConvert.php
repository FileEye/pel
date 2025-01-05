<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Conversion functions to and from bytes and integers.
 *
 * The functions found in this class are used to convert bytes into
 * integers of several sizes ({@link bytesToShort}, {@link bytesToLong}, and {@link bytesToRational}) and convert integers of
 * several sizes into bytes ({@link shortToBytes} and {@link longToBytes}).
 *
 * All the methods are static and they all rely on an argument that
 * specifies the byte order to be used, this must be one of the class
 * constants {@link LITTLE_ENDIAN} or {@link BIG_ENDIAN}. These
 * constants will be referred to as the pseudo type PelByteOrder
 * throughout the documentation.
 */
class PelConvert
{
    /**
     * Little-endian (Intel) byte order.
     *
     * Data stored in little-endian byte order store the least
     * significant byte first, so the number 0x12345678 becomes 0x78
     * 0x56 0x34 0x12 when stored with little-endian byte order.
     */
    public const LITTLE_ENDIAN = true;

    /**
     * Big-endian (Motorola) byte order.
     *
     * Data stored in big-endian byte order store the most significant
     * byte first, so the number 0x12345678 becomes 0x12 0x34 0x56 0x78
     * when stored with big-endian byte order.
     */
    public const BIG_ENDIAN = false;

    /**
     * Convert an unsigned short into two bytes.
     *
     * @param int $value
     *            the unsigned short that will be converted. The lower
     *            two bytes will be extracted regardless of the actual size passed.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return string the bytes representing the unsigned short.
     */
    public static function shortToBytes(int $value, bool $endian): string
    {
        if ($endian === self::LITTLE_ENDIAN) {
            return chr($value) . chr($value >> 8);
        }
        return chr($value >> 8) . chr($value);
    }

    /**
     * Convert a signed short into two bytes.
     *
     * @param int $value
     *            the signed short that will be converted. The lower
     *            two bytes will be extracted regardless of the actual size passed.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return string the bytes representing the signed short.
     */
    public static function sShortToBytes(int $value, bool $endian): string
    {
        /*
         * We can just use shortToBytes, since signed shorts fits well
         * within the 32 bit signed integers used in PHP.
         */
        return self::shortToBytes($value, $endian);
    }

    /**
     * Convert an unsigned long into four bytes.
     *
     * Because PHP limits the size of integers to 32 bit signed, one
     * cannot really have an unsigned integer in PHP. But integers
     * larger than 2^31-1 will be promoted to 64 bit signed floating
     * point numbers, and so such large numbers can be handled too.
     *
     * @param int $value
     *            the unsigned long that will be converted. The
     *            argument will be treated as an unsigned 32 bit integer and the
     *            lower four bytes will be extracted. Treating the argument as an
     *            unsigned integer means that the absolute value will be used. Use
     *            {@link sLongToBytes} to convert signed integers.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return string the bytes representing the unsigned long.
     */
    public static function longToBytes(int $value, bool $endian): string
    {
        /*
         * We cannot convert the number to bytes in the normal way (using
         * shifts and modulo calculations) because the PHP operator >> and
         * function chr() clip their arguments to 2^31-1, which is the
         * largest signed integer known to PHP. But luckily base_convert
         * handles such big numbers.
         */
        $hex = str_pad(base_convert((string) $value, 10, 16), 8, '0', STR_PAD_LEFT);
        if ($endian === self::LITTLE_ENDIAN) {
            return chr((int) hexdec($hex[6] . $hex[7])) . chr((int) hexdec($hex[4] . $hex[5])) . chr((int) hexdec($hex[2] . $hex[3])) . chr((int) hexdec($hex[0] . $hex[1]));
        }
        return chr((int) hexdec($hex[0] . $hex[1])) . chr((int) hexdec($hex[2] . $hex[3])) . chr((int) hexdec($hex[4] . $hex[5])) . chr((int) hexdec($hex[6] . $hex[7]));
    }

    /**
     * Convert a signed long into four bytes.
     *
     * @param int $value
     *            the signed long that will be converted. The argument
     *            will be treated as a signed 32 bit integer, from which the lower
     *            four bytes will be extracted.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return string the bytes representing the signed long.
     */
    public static function sLongToBytes(int $value, bool $endian): string
    {
        /*
         * We can convert the number into bytes in the normal way using
         * shifts and modulo calculations here (in contrast with
         * longToBytes) because PHP automatically handles 32 bit signed
         * integers for us.
         */
        if ($endian === self::LITTLE_ENDIAN) {
            return chr($value) . chr($value >> 8) . chr($value >> 16) . chr($value >> 24);
        }
        return chr($value >> 24) . chr($value >> 16) . chr($value >> 8) . chr($value);
    }

    /**
     * Extract an unsigned byte from a string of bytes.
     *
     * @param string $bytes
     *            the bytes.
     * @param int $offset
     *            The byte found at the offset will be
     *            returned as an integer. The must be at least one byte available
     *            at offset.
     *
     * @return int $offset the unsigned byte found at offset, e.g., an integer
     *         in the range 0 to 255.
     */
    public static function bytesToByte(string $bytes, int $offset): int
    {
        $string = $bytes[$offset];
        return self::convertToByte($string);
    }

    /**
     * Extract an unsigned byte from a stream.
     *
     * @param PelFileStream $stream
     *            the file stream
     * @param int $offset
     *            The byte found at the offset will be
     *            returned as an integer. The must be at least one byte available
     *            at offset.
     *
     * @return int $offset the unsigned byte found at offset, e.g., an integer
     *         in the range 0 to 255.
     */
    public static function streamToByte(PelFileStream $stream, int $offset): int
    {
        $string = $stream->read($offset, 1);
        return self::convertToByte($string);
    }

    /**
     * Convert a string to byte representation
     */
    public static function convertToByte(string $string): int
    {
        return ord($string);
    }

    /**
     * Extract a signed byte from bytes.
     *
     * @param string $bytes
     *            the bytes.
     * @param int $offset
     *            the offset. The byte found at the offset will be
     *            returned as an integer. The must be at least one byte available
     *            at offset.
     *
     * @return int the signed byte found at offset, e.g., an integer in
     *         the range -128 to 127.
     */
    public static function bytesToSByte(string $bytes, int $offset): int
    {
        $n = self::bytesToByte($bytes, $offset);

        return self::convertToSByte($n);
    }

    /**
     * Extract a signed byte from a stream.
     *
     * @param PelFileStream $stream
     *            the file stream
     * @param int $offset
     *            the offset. The byte found at the offset will be
     *            returned as an integer. The must be at least one byte available
     *            at offset.
     *
     * @return int the signed byte found at offset, e.g., an integer in
     *         the range -128 to 127.
     */
    public static function streamToSByte(PelFileStream $stream, int $offset): int
    {
        $n = self::streamToByte($stream, $offset);

        return self::convertToSByte($n);
    }

    /**
     * Convert a string to signed byte representation
     */
    public static function convertToSByte(int $n): int
    {
        if ($n > 127) {
            return $n - 256;
        }
        return $n;
    }

    /**
     * Extract an unsigned short from bytes.
     *
     * @param string $bytes
     *            the bytes.
     * @param int $offset
     *            the offset. The short found at the offset will be
     *            returned as an integer. There must be at least two bytes
     *            available beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the unsigned short found at offset, e.g., an integer
     *         in the range 0 to 65535.
     */
    public static function bytesToShort(string $bytes, int $offset, bool $endian): int
    {
        if ($endian === self::LITTLE_ENDIAN) {
            return ord($bytes[$offset + 1]) * 256 + ord($bytes[$offset]);
        }
        return ord($bytes[$offset]) * 256 + ord($bytes[$offset + 1]);
    }

    /**
     * Extract an unsigned short from a stream.
     *
     * @param PelFileStream $stream
     *            the file stream
     * @param int $offset
     *            the offset. The short found at the offset will be
     *            returned as an integer. There must be at least two bytes
     *            available beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the unsigned short found at offset, e.g., an integer
     *         in the range 0 to 65535.
     */
    public static function streamToShort(PelFileStream $stream, int $offset, bool $endian): int
    {
        $n = self::streamToByte($stream, $offset);
        $n1 = self::streamToByte($stream, $offset + 1);

        if ($endian === self::LITTLE_ENDIAN) {
            return $n1 * 256 + $n;
        }

        return $n * 256 + $n1;
    }

    /**
     * Extract a signed short from bytes.
     *
     * @param int $offset
     *            The short found at offset will be returned
     *            as an integer. There must be at least two bytes available
     *            beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the signed byte found at offset, e.g., an integer in
     *         the range -32768 to 32767.
     */
    public static function bytesToSShort(string $bytes, int $offset, bool $endian): int
    {
        $n = self::bytesToShort($bytes, $offset, $endian);
        return self::convertToSShort($n);
    }

    /**
     * Extract a signed short from a stream.
     *
     * @param PelFileStream $stream
     *            the file stream
     * @param int $offset
     *            The short found at offset will be returned
     *            as an integer. There must be at least two bytes available
     *            beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the signed byte found at offset, e.g., an integer in
     *         the range -32768 to 32767.
     */
    public static function streamToSShort(PelFileStream $stream, int $offset, bool $endian): int
    {
        $n = self::streamToShort($stream, $offset, $endian);
        return self::convertToSShort($n);
    }

    /**
     * Convert a short to signed short
     */
    public static function convertToSShort(int $n): int
    {
        if ($n > 32767) {
            return $n - 65536;
        }
        return $n;
    }

    /**
     * Extract an unsigned long from bytes.
     *
     * @param int $offset
     *            The long found at offset will be returned
     *            as an integer. There must be at least four bytes available
     *            beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the unsigned long found at offset, e.g., an integer
     *         in the range 0 to 4294967295.
     */
    public static function bytesToLong(string $bytes, int $offset, bool $endian): int
    {
        if ($endian === self::LITTLE_ENDIAN) {
            return ord($bytes[$offset + 3]) * 16777216 + ord($bytes[$offset + 2]) * 65536 + ord($bytes[$offset + 1]) * 256 + ord($bytes[$offset]);
        }
        return ord($bytes[$offset]) * 16777216 + ord($bytes[$offset + 1]) * 65536 + ord($bytes[$offset + 2]) * 256 + ord($bytes[$offset + 3]);
    }

    /**
     * Extract an unsigned long from a stream.
     *
     * @param PelFileStream $stream
     *            the file stream
     * @param int $offset
     *            The long found at offset will be returned
     *            as an integer. There must be at least four bytes available
     *            beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the unsigned long found at offset, e.g., an integer
     *         in the range 0 to 4294967295.
     */
    public static function streamToLong(PelFileStream $stream, int $offset, bool $endian): int
    {
        $n = self::streamToByte($stream, $offset);
        $n1 = self::streamToByte($stream, $offset + 1);
        $n2 = self::streamToByte($stream, $offset + 2);
        $n3 = self::streamToByte($stream, $offset + 3);

        if ($endian === self::LITTLE_ENDIAN) {
            return $n3 * 16777216 + $n2 * 65536 + $n1 * 256 + $n;
        }
        return $n * 16777216 + $n1 * 65536 + $n2 * 256 + $n3;
    }

    /**
     * Extract a signed long from bytes.
     *
     * @param int $offset
     *            The long found at offset will be returned
     *            as an integer. There must be at least four bytes available
     *            beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the signed long found at offset, e.g., an integer in
     *         the range -2147483648 to 2147483647.
     */
    public static function bytesToSLong(string $bytes, int $offset, bool $endian): int
    {
        $n = self::bytesToLong($bytes, $offset, $endian);
        return self::convertToSLong($n);
    }

    /**
     * Extract a signed long from a stream.
     *
     * @param PelFileStream $stream
     *            the file stream
     * @param int $offset
     *            The short found at offset will be returned
     *            as an integer. There must be at least two bytes available
     *            beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return int the signed byte found at offset, e.g., an integer in
     *         the range -32768 to 32767.
     */
    public static function streamToSLong(PelFileStream $stream, int $offset, bool $endian): int
    {
        $n = self::streamToLong($stream, $offset, $endian);
        return self::convertToSLong($n);
    }

    /**
     * Convert a long to signed long
     */
    public static function convertToSLong(int $n): int
    {
        if ($n > 2147483647) {
            return $n - 4294967296;
        }
        return $n;
    }

    /**
     * Extract an unsigned rational from bytes.
     *
     * @param int $offset
     *            The rational found at offset will be
     *            returned as an array. There must be at least eight bytes
     *            available beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return array<int, int> the unsigned rational found at offset, e.g., an
     *         array with two integers in the range 0 to 4294967295.
     */
    public static function bytesToRational(string $bytes, int $offset, bool $endian): array
    {
        return [
            self::bytesToLong($bytes, $offset, $endian),
            self::bytesToLong($bytes, $offset + 4, $endian),
        ];
    }

    /**
     * Extract an unsigned rational from a stream.
     *
     * @param int $offset
     *            The rational found at offset will be
     *            returned as an array. There must be at least eight bytes
     *            available beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return array<int, int> the unsigned rational found at offset, e.g., an
     *         array with two integers in the range 0 to 4294967295.
     */
    public static function streamToRational(PelFileStream $stream, int $offset, bool $endian): array
    {
        return [
            self::streamToLong($stream, $offset, $endian),
            self::streamToLong($stream, $offset + 4, $endian),
        ];
    }

    /**
     * Extract a signed rational from bytes.
     *
     * @param int $offset
     *            The rational found at offset will be
     *            returned as an array. There must be at least eight bytes
     *            available beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return array<int, int> the signed rational found at offset, e.g., an array
     *         with two integers in the range -2147483648 to 2147483647.
     */
    public static function bytesToSRational(string $bytes, int $offset, bool $endian): array
    {
        return [
            self::bytesToSLong($bytes, $offset, $endian),
            self::bytesToSLong($bytes, $offset + 4, $endian),
        ];
    }

    /**
     * Extract a signed rational from bytes.
     *
     * @param int $offset
     *            The rational found at offset will be
     *            returned as an array. There must be at least eight bytes
     *            available beginning at the offset given.
     * @param bool $endian
     *            one of {@link LITTLE_ENDIAN} and {@link BIG_ENDIAN}.
     *
     * @return array<int, int> the signed rational found at offset, e.g., an array
     *         with two integers in the range -2147483648 to 2147483647.
     */
    public static function streamToSRational(PelFileStream $stream, int $offset, bool $endian): array
    {
        return [
            self::streamToSLong($stream, $offset, $endian),
            self::streamToSLong($stream, $offset + 4, $endian),
        ];
    }

    /**
     * Format bytes for dumping.
     *
     * This method is for debug output, it will format a string as a
     * hexadecimal dump suitable for display on a terminal. The output
     * is printed directly to standard out.
     *
     * @param string $bytes
     *            the bytes that will be dumped.
     * @param int $max
     *            the maximum number of bytes to dump. If this is left
     *            out (or left to the default of 0), then the entire string will be
     *            dumped.
     */
    public static function bytesToDump(string $bytes, int $max = 0): void
    {
        $s = strlen($bytes);

        if ($max > 0) {
            $s = min($max, $s);
        }
        $line = 24;

        for ($i = 0; $i < $s; $i++) {
            printf('%02X ', ord($bytes[$i]));

            if (($i + 1) % $line === 0) {
                echo "\n";
            }
        }
        echo "\n";
    }
}
