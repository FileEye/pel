<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding a date and time.
 *
 * This class can hold a timestamp, and it will be used as
 * in this example where the time is advanced by one week:
 * <code>
 * $entry = $ifd->getEntry(PelTag::DATE_TIME_ORIGINAL);
 * $time = $entry->getValue();
 * print('The image was taken on the ' . date('jS', $time));
 * $entry->setValue($time + 7 * 24 * 3600);
 * </code>
 *
 * The example used a standard UNIX timestamp, which is the default
 * for this class.
 *
 * But the Exif format defines dates outside the range of a UNIX
 * timestamp (about 1970 to 2038) and so you can also get access to
 * the timestamp in two other formats: a simple string or a Julian Day
 * Count. Please see the Calendar extension in the PHP Manual for more
 * information about the Julian Day Count.
 */
class PelEntryTime extends PelEntryAscii
{
    /**
     * Constant denoting a UNIX timestamp.
     */
    public const UNIX_TIMESTAMP = 1;

    /**
     * Constant denoting a Exif string.
     */
    public const EXIF_STRING = 2;

    /**
     * Constant denoting a Julian Day Count.
     */
    public const JULIAN_DAY_COUNT = 3;

    /**
     * The Julian Day Count of the timestamp held by this entry.
     *
     * This is an integer counting the number of whole days since
     * January 1st, 4713 B.C. The fractional part of the timestamp held
     * by this entry is stored in {@link $seconds}.
     */
    private int $day_count;

    /**
     * The number of seconds into the day of the timestamp held by this
     * entry.
     *
     * The number of whole days is stored in {@link $day_count} and the
     * number of seconds left-over is stored here.
     */
    private int $seconds;

    /**
     * Make a new entry for holding a timestamp.
     *
     * @param int $tag
     *            the Exif tag which this entry represents. There are
     *            only three standard tags which hold timestamp, so this should be
     *            one of the constants {@link PelTag::DATE_TIME}, {@link PelTag::DATE_TIME_ORIGINAL}, or {@link PelTag::DATE_TIME_DIGITIZED}.
     * @param int|string|float $timestamp
     *            the timestamp held by this entry in the correct form
     *            as indicated by the third argument. For {@link UNIX_TIMESTAMP}
     *            this is an integer counting the number of seconds since January
     *            1st 1970, for {@link EXIF_STRING} this is a string of the form
     *            'YYYY:MM:DD hh:mm:ss', and for {@link JULIAN_DAY_COUNT} this is a
     *            floating point number where the integer part denotes the day
     *            count and the fractional part denotes the time of day (0.25 means
     *            6:00, 0.75 means 18:00).
     * @param int $type
     *            the type of the timestamp. This must be one of
     *            {@link UNIX_TIMESTAMP}, {@link EXIF_STRING}, or
     *            {@link JULIAN_DAY_COUNT}.
     */
    public function __construct(int $tag, int|float|string $timestamp, int $type = self::UNIX_TIMESTAMP)
    {
        $this->tag = $tag;
        $this->format = PelFormat::ASCII;
        $this->setValue($timestamp, $type);
    }

    /**
     * Return the timestamp of the entry.
     *
     * The timestamp held by this entry is returned in one of three
     * formats: as a standard UNIX timestamp (default), as a fractional
     * Julian Day Count, or as a string.
     *
     * @param int $type
     *            the type of the timestamp. This must be one of
     *            {@link UNIX_TIMESTAMP}, {@link EXIF_STRING}, or
     *            {@link JULIAN_DAY_COUNT}.
     *
     * @return string the timestamp held by this entry in the correct form
     *         as indicated by the type argument. For {@link UNIX_TIMESTAMP}
     *         this is an integer counting the number of seconds since January
     *         1st 1970, for {@link EXIF_STRING} this is a string of the form
     *         'YYYY:MM:DD hh:mm:ss', and for {@link JULIAN_DAY_COUNT} this is a
     *         floating point number where the integer part denotes the day
     *         count and the fractional part denotes the time of day (0.25 means
     *         6:00, 0.75 means 18:00).
     */
    public function getValue(int $type = self::UNIX_TIMESTAMP): string
    {
        switch ($type) {
            case self::UNIX_TIMESTAMP:
                $seconds = $this->convertJdToUnix($this->day_count);
                if ($seconds === false) {
                    throw new PelInvalidArgumentException('Invalid UNIX_TIMESTAMP (%d), Julian Day Count is outside the range of UNIX timestamp', $this->day_count);
                }
                return (string) ($seconds + $this->seconds);

            case self::EXIF_STRING:
                [$year, $month, $day] = $this->convertJdToGregorian($this->day_count);
                $hours = (int) ($this->seconds / 3600);
                $minutes = (int) ($this->seconds % 3600 / 60);
                $seconds = $this->seconds % 60;
                return sprintf('%04d:%02d:%02d %02d:%02d:%02d', $year, $month, $day, $hours, $minutes, $seconds);
            case self::JULIAN_DAY_COUNT:
                return (string) ($this->day_count + round($this->seconds / 86400, 2));
            default:
                throw new PelInvalidArgumentException('Expected UNIX_TIMESTAMP (%d), EXIF_STRING (%d), or JULIAN_DAY_COUNT (%d) for $type, got %d.', self::UNIX_TIMESTAMP, self::EXIF_STRING, self::JULIAN_DAY_COUNT, $type);
        }
    }

    /**
     * Update the timestamp held by this entry.
     *
     * @param int|float|string $timestamp
     *            the timestamp held by this entry in the correct form
     *            as indicated by the third argument. For {@link UNIX_TIMESTAMP}
     *            this is an integer counting the number of seconds since January
     *            1st 1970, for {@link EXIF_STRING} this is a string of the form
     *            'YYYY:MM:DD hh:mm:ss', and for {@link JULIAN_DAY_COUNT} this is a
     *            floating point number where the integer part denotes the day
     *            count and the fractional part denotes the time of day (0.25 means
     *            6:00, 0.75 means 18:00).
     * @param int $type
     *            the type of the timestamp. This must be one of
     *            {@link UNIX_TIMESTAMP}, {@link EXIF_STRING}, or
     *            {@link JULIAN_DAY_COUNT}.
     *
     * @throws PelInvalidArgumentException
     */
    public function setValue(mixed $timestamp, int $type = self::UNIX_TIMESTAMP): void
    {
        if ($type === self::UNIX_TIMESTAMP) {
            if (is_string($timestamp)) {
                if (is_numeric($timestamp)) {
                    $timestamp = (int) $timestamp;
                } else {
                    throw new PelInvalidArgumentException('Expected numeric value for $type, got "%s"', $timestamp);
                }
            }
            $this->day_count = (int) $this->convertUnixToJd($timestamp);
            $this->seconds = $timestamp % 86400;
        } elseif ($type === self::EXIF_STRING) {
            /*
             * Clean the timestamp: some timestamps are broken other
             * separators than ':' and ' '.
             */
            $d = preg_split('/[^0-9]+/', (string) $timestamp);

            if ($d === false) {
                throw new PelInvalidArgumentException('Invalid string value received, got %s', $timestamp);
            }

            for ($i = 0; $i < 6; $i++) {
                if (! isset($d[$i])) {
                    $d[$i] = 0;
                }
            }
            $this->day_count = $this->convertGregorianToJd((int) $d[0], (int) $d[1], (int) $d[2]);
            $this->seconds = (int) $d[3] * 3600 + (int) $d[4] * 60 + (int) $d[5];
        } elseif ($type === self::JULIAN_DAY_COUNT) {
            if (is_string($timestamp)) {
                if (is_numeric($timestamp)) {
                    $timestamp = (int) $timestamp;
                } else {
                    throw new PelInvalidArgumentException('Expected numeric value for $type, got "%s"', $timestamp);
                }
            }
            $this->day_count = (int) floor($timestamp);
            $this->seconds = (int) (86400 * ($timestamp - floor($timestamp)));
        } else {
            throw new PelInvalidArgumentException('Expected UNIX_TIMESTAMP (%d), EXIF_STRING (%d), or JULIAN_DAY_COUNT (%d) for $type, got %d.', self::UNIX_TIMESTAMP, self::EXIF_STRING, self::JULIAN_DAY_COUNT, $type);
        }

        // finally update the string which will be used when this is turned into bytes.
        parent::setValue($this->getValue(self::EXIF_STRING));
    }

    // The following four functions are used for converting back and
    // forth between the date formats. They are used in preference to
    // the ones from the PHP calendar extension to avoid having to
    // fiddle with timezones and to avoid depending on the extension.
    //
    // See http://www.hermetic.ch/cal_stud/jdn.htm#comp for a reference.

    /**
     * Converts a date in year/month/day format to a Julian Day count.
     *
     * @param int $year
     *            the year.
     * @param int $month
     *            the month, 1 to 12.
     * @param int $day
     *            the day in the month.
     *
     * @return int the Julian Day count.
     */
    public function convertGregorianToJd(int $year, int $month, int $day): int
    {
        // Special case mapping 0/0/0 -> 0
        if ($year === 0 || $month === 0 || $day === 0) {
            return 0;
        }

        $m1412 = $month <= 2 ? - 1 : 0;
        return (int) (floor(1461 * ($year + 4800 + $m1412) / 4) + floor(367 * ($month - 2 - 12 * $m1412) / 12) - floor(3 * floor(($year + 4900 + $m1412) / 100) / 4) + $day - 32075);
    }

    /**
     * Converts a Julian Day count to a year/month/day triple.
     *
     * @param int $jd
     *            the Julian Day count.
     *
     * @return array<int, int|float> an array with three entries: year, month, day.
     */
    public function convertJdToGregorian(int $jd): array
    {
        // Special case mapping 0 -> 0/0/0
        if ($jd === 0) {
            return [
                0,
                0,
                0,
            ];
        }

        $l = $jd + 68569;
        $n = floor(4 * $l / 146097);
        $l = $l - floor((146097 * $n + 3) / 4);
        $i = floor(4000 * ($l + 1) / 1461001);
        $l = $l - floor(1461 * $i / 4) + 31;
        $j = floor(80 * $l / 2447);
        $d = $l - floor(2447 * $j / 80);
        $l = floor($j / 11);
        $m = $j + 2 - (12 * $l);
        $y = 100 * ($n - 49) + $i + $l;
        return [
            $y,
            $m,
            $d,
        ];
    }

    /**
     * Converts a UNIX timestamp to a Julian Day count.
     *
     * @param int|float $timestamp
     *            the timestamp.
     *
     * @return float the Julian Day count.
     */
    public function convertUnixToJd(int|float $timestamp): float
    {
        return floor($timestamp / 86400) + 2440588;
    }

    /**
     * Converts a Julian Day count to a UNIX timestamp.
     *
     * @param int|float $jd
     *            the Julian Day count.
     *
     * @return int|false $timestamp the integer timestamp or false if the
     *         day count cannot be represented as a UNIX timestamp.
     */
    public function convertJdToUnix(int|float $jd): int|false
    {
        if ($jd > 0) {
            $timestamp = ($jd - 2440588) * 86400;
            if ($timestamp >= 0) {
                return (int) $timestamp;
            }
        }
        return false;
    }
}
