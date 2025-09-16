<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding signed rational numbers.
 *
 * This class can hold rational numbers, consisting of a numerator and
 * denominator both of which are of type unsigned long. Each rational
 * is represented by an array with just two entries: the numerator and
 * the denominator, in that order.
 *
 * The class can hold either just a single rational or an array of
 * rationals. The class will be used to manipulate any of the Exif
 * tags which can have format {@link PelFormat::SRATIONAL}.
 */
class PelEntrySRational extends PelEntrySLong
{
    /**
     * Make a new entry that can hold a signed rational.
     *
     * @param int $tag
     *   The tag which this entry represents. This should be one of the constants defined in
     *   {@link PelTag}, e.g., {@link PelTag::SHUTTER_SPEED_VALUE}, or any other tag which can
     *   have format {@link PelFormat::SRATIONAL}.
     * @param array{0:int,1:int} ...$value
     *   The rational(s) that this entry will represent. The arguments passed must obey the same
     *   rules as the argument to {@link setValue}, namely that each argument should be an array
     *   with two entries, both of which must be within range of a signed long (32 bit), that is
     *   between -2147483648 and 2147483647 (inclusive). If not, then a
     *   {@link PelOverflowException} will be thrown.
     */
    public function __construct(int $tag, array ...$value)
    {
        $this->tag = $tag;
        $this->format = PelFormat::SRATIONAL;
        $this->dimension = 2;
        $this->min = - 2147483648;
        $this->max = 2147483647;
        $this->setValueArray($value);
    }

    /**
     * Format a rational number.
     *
     * The rational will be returned as a string with a slash '/'
     * between the numerator and denominator. Care is taken to display
     * '-1/2' instead of the ugly but mathematically equivalent '1/-2'.
     *
     * @param int|array<int, mixed> $number
     *            the rational which will be formatted.
     * @param bool $brief
     *            not used.
     *
     * @return string the rational formatted as a string suitable for
     *         display.
     */
    public function formatNumber(int|array $number, bool $brief = false): string
    {
        if (is_int($number)) {
            return (string) $number;
        }

        if ($number[1] < 0) {
            /* Turn output like 1/-2 into -1/2. */
            return (- $number[0]) . '/' . (- $number[1]);
        }
        return $number[0] . '/' . $number[1];
    }

    /**
     * Get the value of an entry as text.
     *
     * The value will be returned in a format suitable for presentation,
     * e.g., rationals will be returned as 'x/y', ASCII strings will be
     * returned as themselves etc.
     *
     * @param bool $brief
     *            some values can be returned in a long or more
     *            brief form, and this parameter controls that.
     *
     * @return string the value as text.
     */
    public function getText(bool $brief = false): string
    {
        if (isset($this->value[0])) {
            $v = $this->value[0];
        } else {
            return parent::getText($brief);
        }

        return match ($this->tag) {
            PelTag::SHUTTER_SPEED_VALUE => Pel::fmt('%.0f/%.0f sec. (APEX: %d)', $v[0], $v[1], sqrt(2) ** ($v[0] / $v[1])),
            PelTag::BRIGHTNESS_VALUE => sprintf('%d/%d', $v[0], $v[1]),
            PelTag::EXPOSURE_BIAS_VALUE => sprintf('%s%.01f', $v[0] * $v[1] > 0 ? '+' : '', $v[0] / $v[1]),
            default => parent::getText($brief),
        };
    }
}
