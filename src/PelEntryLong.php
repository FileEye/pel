<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding unsigned longs.
 *
 * This class can hold longs, either just a single long or an array of
 * longs. The class will be used to manipulate any of the Exif tags
 * which can have format {@link PelFormat::LONG} like in this
 * example:
 * <code>
 * $w = $ifd->getEntry(PelTag::IMAGE_WIDTH);
 * $w->setValue($w->getValue() / 2);
 * $h = $ifd->getEntry(PelTag::IMAGE_LENGTH);
 * $h->setValue($h->getValue() / 2);
 * </code>
 * Here the width and height is updated to 50% of their original
 * values.
 */
class PelEntryLong extends PelEntryNumber
{
    /**
     * Make a new entry that can hold an unsigned long.
     *
     * The method accept its arguments in two forms: several integer
     * arguments or a single array argument. The {@link getValue}
     * method will always return an array except for when a single
     * integer argument is given here, or when an array with just a
     * single integer is given.
     *
     * This means that one can conveniently use objects like this:
     * <code>
     * $a = new PelEntryLong(PelTag::EXIF_IMAGE_WIDTH, 123456);
     * $b = $a->getValue() - 654321;
     * </code>
     * where the call to {@link getValue} will return an integer instead
     * of an array with one integer element, which would then have to be
     * extracted.
     *
     * @param int $tag
     *   The tag which this entry represents. This should be one of the constants defined in
     *   {@link PelTag}, e.g., {@link PelTag::IMAGE_WIDTH}, or any other tag which can have format
     *   {@link PelFormat::LONG}.
     * @param int ...$value
     *   The long(s) that this entry will represent or an array of longs. The argument passed must
     *   obey the same rules as the argument to {@link setValue}, namely that it should be within
     *   range of an unsigned long (32 bit), that is between 0 and 4294967295 (inclusive). If not,
     *   then a {@link PelExifOverflowException} will be thrown.
     */
    public function __construct(int $tag, int ...$value)
    {
        $this->tag = $tag;
        $this->min = 0;
        $this->max = 4294967295;
        $this->format = PelFormat::LONG;
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
        return PelConvert::longToBytes($number, $order);
    }
}
