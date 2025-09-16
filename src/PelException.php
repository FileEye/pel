<?php

declare(strict_types=1);

namespace lsolesen\pel;

use Exception;

/**
 * Standard PEL printf() capable exception.
 * This class is a simple extension of the standard Exception class in
 * PHP, and all the methods defined there retain their original
 * meaning.
 */
class PelException extends Exception
{
    /**
     * Construct a new PEL exception.
     *
     * @param string $fmt
     *   An optional format string can be given. It will be used as a format string for vprintf().
     *   The remaining arguments will be available for the format string as usual with vprintf().
     * @param mixed ...$args
     *   Any number of arguments to be used with the format string.
     */
    public function __construct(string $fmt, mixed ...$args)
    {
        parent::__construct(vsprintf($fmt, $args));
    }
}
