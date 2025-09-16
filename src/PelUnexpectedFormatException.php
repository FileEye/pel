<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Exception indicating that an unexpected format was found.
 *
 * The documentation for each tag in {@link PelTag} will detail any
 * constrains.
 */
class PelUnexpectedFormatException extends PelEntryException
{
    /**
     * Construct a new exception indicating an invalid format.
     *
     * @param int $type
     *            the type of IFD.
     * @param int $tag
     *            the tag for which the violation was found as defined in {@link PelTag}
     * @param int $found
     *            the format found as defined in {@link PelFormat}
     * @param int $expected
     *            the expected as defined in {@link PelFormat}
     */
    public function __construct(int $type, int $tag, int $found, int $expected)
    {
        parent::__construct('Unexpected format found for %s tag: PelFormat::%s. Expected PelFormat::%s instead.', PelTag::getName($type, $tag), strtoupper(PelFormat::getName($found)), strtoupper(PelFormat::getName($expected)));
        $this->tag = $tag;
        $this->type = $type;
    }
}
