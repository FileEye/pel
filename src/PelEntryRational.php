<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding unsigned rational numbers.
 *
 * This class can hold rational numbers, consisting of a numerator and
 * denominator both of which are of type unsigned long. Each rational
 * is represented by an array with just two entries: the numerator and
 * the denominator, in that order.
 *
 * The class can hold either just a single rational or an array of
 * rationals. The class will be used to manipulate any of the Exif
 * tags which can have format {@link PelFormat::RATIONAL} like in this
 * example:
 *
 * <code>
 * $resolution = $ifd->getEntry(PelTag::X_RESOLUTION);
 * $resolution->setValue([1, 300]);
 * </code>
 *
 * Here the x-resolution is adjusted to 1/300, which will be 300 DPI,
 * unless the {@link PelTag::RESOLUTION_UNIT resolution unit} is set
 * to something different than 2 which means inches.
 */
class PelEntryRational extends PelEntryLong
{
    /**
     * Make a new entry that can hold an unsigned rational.
     *
     * @param int $tag
     *   The tag which this entry represents. This should be one of the constants defined in
     *   {@link PelTag}, e.g., {@link PelTag::X_RESOLUTION}, or any other tag which can have
     *   format {@link PelFormat::RATIONAL}.
     * @param array{0:int,1:int} ...$value
     *   The rational(s) that this entry will represent. The arguments passed must obey the same
     *   rules as the argument to {@link setValue}, namely that each argument should be an array
     *   with two entries, both of which must be within range of an unsigned long (32 bit), that
     *   is between 0 and 4294967295 (inclusive). If not, then a {@link PelOverflowException} will
     *   be thrown.
     *
     * @throws PelOverflowException
     */
    public function __construct(int $tag, array ...$value)
    {
        $this->tag = $tag;
        $this->format = PelFormat::RATIONAL;
        $this->dimension = 2;
        $this->min = 0;
        $this->max = 4294967295;
        $this->setValueArray($value);
    }

    /**
     * Format a rational number.
     *
     * The rational will be returned as a string with a slash '/'
     * between the numerator and denominator.
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
        return is_int($number) ? (string) $number : $number[0] . '/' . $number[1];
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
            // TODO: Not sure, if this is the correct path; maybe throw an exception?
            return '';
        }

        switch ($this->tag) {
            case PelTag::FNUMBER:
                // CC (e->components, 1, v);
                return Pel::fmt('f/%.01f', $v[0] / $v[1]);

            case PelTag::APERTURE_VALUE:
                // CC (e->components, 1, v);
                // if (!v_rat.denominator) return (NULL);
                return Pel::fmt('f/%.01f', 2 ** ($v[0] / $v[1] / 2));

            case PelTag::FOCAL_LENGTH:
                // CC (e->components, 1, v);
                // if (!v_rat.denominator) return (NULL);
                return Pel::fmt('%.1f mm', $v[0] / $v[1]);

            case PelTag::SUBJECT_DISTANCE:
                // CC (e->components, 1, v);
                // if (!v_rat.denominator) return (NULL);
                return Pel::fmt('%.1f m', $v[0] / $v[1]);

            case PelTag::EXPOSURE_TIME:
                // CC (e->components, 1, v);
                // if (!v_rat.denominator) return (NULL);
                if ($v[0] / $v[1] < 1) {
                    return Pel::fmt('1/%d sec.', $v[1] / $v[0]);
                }
                return Pel::fmt('%d sec.', $v[0] / $v[1]);

            case PelTag::GPS_LATITUDE:
            case PelTag::GPS_LONGITUDE:
                $degrees = $v[0] / $v[1];
                $minutes = $this->value[1][0] / $this->value[1][1];
                $seconds = $this->value[2][0] / $this->value[2][1];

                return sprintf('%s° %s\' %s" (%.2f°)', $degrees, $minutes, $seconds, $degrees + $minutes / 60 + $seconds / 3600);

            default:
                return parent::getText($brief);
        }
    }
}
