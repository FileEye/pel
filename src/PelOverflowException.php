<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Exception cast when numbers overflow.
 */
class PelOverflowException extends PelException
{
    /**
     * Construct a new overflow exception.
     *
     * @param int $v
     *            the value that is out of range.
     * @param int $min
     *            the minimum allowed value.
     * @param int $max
     *            the maximum allowed value.
     */
    public function __construct(int $v, int $min, int $max)
    {
        parent::__construct('Value %.0f out of range [%.0f, %.0f]', $v, $min, $max);
    }
}
