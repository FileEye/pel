<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding signed bytes.
 *
 * This class can hold bytes, either just a single byte or an array of
 * bytes. The class will be used to manipulate any of the Exif tags
 * which has format {@link PelFormat::BYTE}.
 */
class PelEntrySByte extends PelEntryNumber
{
    /**
     * Make a new entry that can hold a signed byte.
     *
     * The method accept several integer arguments. The {@link getValue}
     * method will always return an array except for when a single
     * integer argument is given here.
     *
     * @param int $tag
     *   The tag which this entry represents. This should be one of the constants defined in
     *   {@link PelTag} which has format {@link PelFormat::BYTE}.
     * @param int ...$value
     *   The byte(s) that this entry will represent. The argument passed must obey the same rules
     *   as the argument to {@link setValue}, namely that it should be within range of a signed
     *   byte, that is between -128 and 127 (inclusive). If not, then a
     *   {@link PelOverflowException} will be thrown.
     */
    public function __construct(int $tag, int ...$value)
    {
        $this->tag = $tag;
        $this->min = - 128;
        $this->max = 127;
        $this->format = PelFormat::SBYTE;
        $this->setValueArray($value);
    }

    /**
     * Convert a number into bytes.
     *
     * @param int $number
     *            the number that should be converted.
     * @param bool $order
     *            one of {@link PelConvert::LITTLE_ENDIAN} and
     *            {@link PelConvert::BIG_ENDIAN}, specifying the target byte order.
     *
     * @return string bytes representing the number given.
     */
    public function numberToBytes(int $number, bool $order): string
    {
        return chr($number);
    }
}
