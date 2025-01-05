<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding a plain ASCII string.
 *
 * This class can hold a single ASCII string, and it will be used as in
 * <code>
 * $entry = $ifd->getEntry(PelTag::IMAGE_DESCRIPTION);
 * print($entry->getValue());
 * $entry->setValue('This is my image. I like it.');
 * </code>
 */
class PelEntryAscii extends PelEntry
{
    /**
     * The string hold by this entry.
     *
     * This is the string that was given to the {@link __construct
     * constructor} or later to {@link setValue}, without any final NULL
     * character.
     */
    private string $str = '';

    /**
     * Make a new PelEntry that can hold an ASCII string.
     *
     * @param int $tag
     *            the tag which this entry represents. This should be
     *            one of the constants defined in {@link PelTag}, e.g., {@link PelTag::IMAGE_DESCRIPTION}, {@link PelTag::MODEL}, or any other
     *            tag with format {@link PelFormat::ASCII}.
     * @param string $str
     *            the string that this entry will represent. The
     *            string must obey the same rules as the string argument to {@link setValue}, namely that it should be given without any trailing
     *            NULL character and that it must be plain 7-bit ASCII.
     */
    public function __construct(int $tag, string $str = '')
    {
        $this->tag = $tag;
        $this->format = PelFormat::ASCII;
        $this->setValue($str);
    }

    /**
     * {@inheritdoc}
     *
     * @see \lsolesen\pel\PelEntry::setValue()
     */
    public function setValue(mixed $str): void
    {
        $str = (string) $str;

        $this->components = strlen($str) + 1;
        $this->str = $str;
        $this->bytes = $str . chr(0x00);
    }

    /**
     * {@inheritdoc}
     *
     * @see \lsolesen\pel\PelEntry::getValue()
     */
    public function getValue(): string
    {
        return $this->str;
    }

    /**
     * {@inheritdoc}
     *
     * @see \lsolesen\pel\PelEntry::getText()
     */
    public function getText(bool $brief = false): string
    {
        return $this->str;
    }
}
