<?php

declare(strict_types=1);

namespace lsolesen\pel;

use Stringable;

/**
 * Class representing JPEG comments.
 */
class PelJpegComment extends PelJpegContent implements Stringable
{
    /**
     * Construct a new JPEG comment.
     *
     * The new comment will contain the string given.
     */
    public function __construct(private string $comment = '')
    {
    }

    /**
     * Return a string representation of this object.
     *
     * @return string the same as {@link getValue()}.
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * Load and parse data.
     *
     * This will load the comment from the data window passed.
     */
    public function load(PelDataWindow $d): void
    {
        $this->comment = $d->getBytes();
    }

    /**
     * Update the value with a new comment.
     *
     * Any old comment will be overwritten.
     *
     * @param string $comment
     *            the new comment.
     */
    public function setValue(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * Get the comment.
     *
     * @return string the comment.
     */
    public function getValue(): string
    {
        return $this->comment;
    }

    /**
     * Turn this comment into bytes.
     *
     * @return string bytes representing this comment.
     */
    public function getBytes(): string
    {
        return $this->comment;
    }
}
