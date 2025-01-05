<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class representing content in a JPEG file.
 *
 * A JPEG file consists of a sequence of each of which has an
 * associated {@link PelJpegMarker marker} and some content. This
 * class represents the content, and this basic type is just a simple
 * holder of such content, represented by a {@link PelDataWindow}
 * object. The {@link PelExif} class is an example of more
 * specialized JPEG content.
 */
class PelJpegContent
{
    /**
     * Make a new piece of JPEG content.
     */
    public function __construct(private readonly ?PelDataWindow $data)
    {
    }

    /**
     * Return the bytes of the content.
     *
     * @return string bytes representing this JPEG content. These bytes
     *         will match the bytes given to {@link __construct the
     *         constructor}.
     */
    public function getBytes(): string
    {
        return '' . $this->data?->getBytes();
    }
}
