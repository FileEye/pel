<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Exception thrown when an invalid marker is found.
 *
 * This exception is thrown when PEL expects to find a {@link PelJpegMarker} and instead finds a byte that isn't a known marker.
 */
class PelJpegInvalidMarkerException extends PelException
{
    /**
     * Construct a new invalid marker exception.
     * The exception will contain a message describing the error,
     * including the byte found and the offset of the offending byte.
     *
     * @param int $marker
     *            the byte found.
     * @param int $offset
     *            the offset in the data.
     */
    public function __construct(int $marker, int $offset)
    {
        parent::__construct('Invalid marker found at offset %d: 0x%2X', $offset, $marker);
    }
}
