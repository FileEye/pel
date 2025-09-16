<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding signed longs.
 *
 * This class can hold longs, either just a single long or an array of
 * longs. The class will be used to manipulate any of the Exif tags
 * which can have format {@link PelFormat::SLONG}.
 */
class PelEntrySLong extends PelEntryNumber
{
    /**
     * Make a new entry that can hold a signed long.
     *
     * The method accept its arguments in two forms: several integer
     * arguments or a single array argument. The {@link getValue}
     * method will always return an array except for when a single
     * integer argument is given here, or when an array with just a
     * single integer is given.
     *
     * @param int $tag
     *   The tag which this entry represents. This should be one of the constants defined in
     *   {@link PelTag} which have format {@link PelFormat::SLONG}.
     * @param int ...$value
     *   The long(s) that this entry will represent or an array of longs. The argument passed
     *   must obey the same rules as the argument to {@link setValue}, namely that it should be
     *   within range of a signed long (32 bit), that is between -2147483648 and 2147483647
     *   (inclusive). If not, then a {@link PelOverflowException} will be thrown.
     */
    public function __construct(int $tag, int ...$value)
    {
        $this->tag = $tag;
        $this->min = - 2147483648;
        $this->max = 2147483647;
        $this->format = PelFormat::SLONG;
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
        return PelConvert::sLongToBytes($number, $order);
    }
}
