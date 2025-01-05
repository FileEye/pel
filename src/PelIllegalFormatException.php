<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Exception indicating that an unexpected format was found.
 *
 * The documentation for each tag in {@link PelTag} will detail any
 * constrains.
 */
class PelIllegalFormatException extends PelException
{
    /**
     * Construct a new exception indicating an illegal format.
     *
     * @param int $type
     *            the type of IFD.
     */
    public function __construct(int $type)
    {
        parent::__construct('Unknown format: 0x%X', $type);
    }
}
