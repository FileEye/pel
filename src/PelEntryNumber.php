<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding numbers.
 *
 * This class can hold numbers, with range checks.
 */
abstract class PelEntryNumber extends PelEntry
{
    /**
     * The value held by this entry.
     *
     * @var array<int|string, mixed>
     */
    protected array $value = [];

    /**
     * The minimum allowed value.
     *
     * Any attempt to change the value below this variable will result
     * in a {@link PelOverflowException} being thrown.
     */
    protected int $min;

    /**
     * The maximum allowed value.
     *
     * Any attempt to change the value over this variable will result in
     * a {@link PelOverflowException} being thrown.
     */
    protected int $max;

    /**
     * The dimension of the number held.
     *
     * Normal numbers have a dimension of one, pairs have a dimension of
     * two, etc.
     */
    protected int $dimension = 1;

    /**
     * Change the value.
     *
     * This method can change both the number of components and the
     * value of the components. Range checks will be made on the new
     * value, and a {@link PelOverflowException} will be thrown if the
     * value is found to be outside the legal range.
     *
     * The method accept several number arguments. The {@link getValue}
     * method will always return an array except for when a single
     * number is given here.
     *
     * @param int|array{0:int,1:int} ...$value
     *   The new value(s). This can be zero or more numbers, that is, either integers or arrays.
     *   The input will be checked to ensure that the numbers are within the valid range. If not,
     *   then a {@link PelOverflowException} will be thrown.
     *
     * @see PelEntryNumber::getValue
     */
    public function setValue(mixed ...$value): void
    {
        $this->setValueArray($value);
    }

    /**
     * Change the value.
     *
     * This method can change both the number of components and the
     * value of the components. Range checks will be made on the new
     * value, and a {@link PelOverflowException} will be thrown if the
     * value is found to be outside the legal range.
     *
     * @param array<int|string, mixed> $values
     *            the new values. The array must contain the new
     *            numbers.
     *
     * @see PelEntryNumber::getValue
     */
    public function setValueArray(array $values): void
    {
        foreach ($values as $v) {
            $this->validateNumber($v);
        }

        $this->components = count($values);
        $this->value = $values;
    }

    /**
     * Return the numeric value held.
     *
     * @return int|array<int|string, mixed> this will either be a single number if there is
     *         only one component, or an array of numbers otherwise.
     */
    public function getValue(): int|array
    {
        if ($this->components === 1) {
            return $this->value[0];
        }
        return $this->value;
    }

    /**
     * Validate a number.
     *
     * This method will check that the number given is within the range
     * given my {@link getMin()} and {@link getMax()}, inclusive. If
     * not, then a {@link PelOverflowException} is thrown.
     *
     * @param int|array<int, mixed> $n
     *            the number in question.
     *
     * @return void nothing, but will throw a {@link PelOverflowException} if the number is found to be outside the
     *         legal range and {@link Pel::$strict} is true.
     */
    public function validateNumber(int|array $n): void
    {
        if ($this->dimension === 1 || is_scalar($n)) {
            if (is_int($n) && ($n < $this->min || $n > $this->max)) {
                Pel::maybeThrow(new PelOverflowException((int) $n, $this->min, $this->max));
            }
        } else {
            for ($i = 0; $i < $this->dimension; $i++) {
                if (! isset($n[$i])) {
                    continue;
                }
                if ($n[$i] < $this->min || $n[$i] > $this->max) {
                    Pel::maybeThrow(new PelOverflowException($n[$i], $this->min, $this->max));
                }
            }
        }
    }

    /**
     * Add a number.
     *
     * This appends a number to the numbers already held by this entry,
     * thereby increasing the number of components by one.
     *
     * @param int|array<int, mixed> $n
     *            the number to be added.
     */
    public function addNumber(int|array $n): void
    {
        $this->validateNumber($n);
        $this->value[] = $n;
        $this->components++;
    }

    /**
     * Convert a number into bytes.
     *
     * The concrete subclasses will have to implement this method so
     * that the numbers represented can be turned into bytes.
     *
     * The method will be called once for each number held by the entry.
     *
     * @param int $number
     *            the number that should be converted.
     * @param bool $order
     *            one of {@link PelConvert::LITTLE_ENDIAN} and
     *            {@link PelConvert::BIG_ENDIAN}, specifying the target byte order.
     *
     * @return string bytes representing the number given.
     */
    abstract public function numberToBytes(int $number, bool $order): string;

    /**
     * Turn this entry into bytes.
     *
     * @param bool $o
     *            the desired byte order, which must be either
     *            {@link PelConvert::LITTLE_ENDIAN} or {@link PelConvert::BIG_ENDIAN}.
     *
     * @return string bytes representing this entry.
     */
    public function getBytes(bool $o): string
    {
        $bytes = '';
        for ($i = 0; $i < $this->components; $i++) {
            if ($this->dimension === 1) {
                $bytes .= $this->numberToBytes($this->value[$i], $o);
            } else {
                for ($j = 0; $j < $this->dimension; $j++) {
                    $bytes .= $this->numberToBytes($this->value[$i][$j], $o);
                }
            }
        }
        return $bytes;
    }

    /**
     * Format a number.
     *
     * This method is called by {@link getText} to format numbers.
     * Subclasses should override this method if they need more
     * sophisticated behavior than the default, which is to just return
     * the number as is.
     *
     * @param int|array<int, mixed> $number
     *            the number which will be formatted.
     * @param bool $brief
     *            it could be that there is both a verbose and a
     *            brief formatting available, and this argument controls that.
     *
     * @return string the number formatted as a string suitable for
     *         display.
     */
    public function formatNumber(int|array $number, bool $brief = false): string
    {
        return is_int($number) ? (string) $number : implode(', ', $number);
    }

    /**
     * Get the numeric value of this entry as text.
     *
     * @param bool $brief
     *            use brief output? The numbers will be separated
     *            by a single space if brief output is requested, otherwise a space
     *            and a comma will be used.
     *
     * @return string the numbers(s) held by this entry.
     */
    public function getText(bool $brief = false): string
    {
        if ($this->components === 0) {
            return '';
        }

        $str = $this->formatNumber($this->value[0]);
        for ($i = 1; $i < $this->components; $i++) {
            $str .= ($brief ? ' ' : ', ');
            $str .= $this->formatNumber($this->value[$i]);
        }

        return $str;
    }
}
