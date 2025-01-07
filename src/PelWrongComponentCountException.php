<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Exception indicating that an unexpected number of components was
 * found.
 *
 * Some tags have strict limits as to the allowed number of
 * components, and this exception is thrown if the data violates such
 * a constraint. The documentation for each tag in {@link PelTag}
 * explains the expected number of components.
 */
class PelWrongComponentCountException extends \lsolesen\pel\PelEntryException
{
    /**
     * Construct a new exception indicating a wrong number of
     * components.
     *
     * @param int $type
     *            the type of IFD.
     * @param int $tag
     *            the tag for which the violation was found.
     * @param int $found
     *            the number of components found.
     * @param int $expected
     *            the expected number of components.
     */
    public function __construct(int $type, int $tag, int $found, int $expected)
    {
        parent::__construct('Wrong number of components found for %s tag: %d. Expected %d.', PelTag::getName($type, $tag), $found, $expected);
        $this->tag = $tag;
        $this->type = $type;
    }
}
