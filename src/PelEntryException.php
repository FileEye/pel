<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Exception indicating a problem with an entry.
 */
class PelEntryException extends PelException
{
    /**
     * The IFD type (if known).
     */
    protected ?int $type = null;

    /**
     * The tag of the entry (if known).
     */
    protected ?int $tag = null;

    /**
     * Get the IFD type associated with the exception.
     *
     * @return int|null one of {@link PelIfd::IFD0}, {@link PelIfd::IFD1},
     *         {@link PelIfd::EXIF}, {@link PelIfd::GPS}, or {@link PelIfd::INTEROPERABILITY}. If no type is set, null is returned.
     */
    public function getIfdType(): ?int
    {
        return $this->type;
    }

    /**
     * Get the tag associated with the exception.
     *
     * @return int|null the tag. If no tag is set, null is returned.
     */
    public function getTag(): ?int
    {
        return $this->tag;
    }
}
