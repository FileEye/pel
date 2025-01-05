<?php

declare(strict_types=1);

namespace lsolesen\pel;

use Stringable;

/**
 * Common ancestor class of all {@link PelIfd} entries.
 *
 * As this class is abstract you cannot instantiate objects from it.
 * It only serves as a common ancestor to define the methods common to
 * all entries. The most important methods are {@link getValue()} and
 * {@link setValue()}, both of which is abstract in this class. The
 * descendants will give concrete implementations for them.
 *
 * If you have some data coming from an image (some raw bytes), then
 * the static method {@link newFromData()} is helpful --- it will look
 * at the data and give you a proper decendent of {@link PelEntry}
 * back.
 *
 * If you instead want to have an entry for some data which take the
 * form of an integer, a string, a byte, or some other PHP type, then
 * don't use this class. You should instead create an object of the
 * right subclass ({@link PelEntryShort} for short integers, {@link PelEntryAscii} for strings, and so on) directly.
 */
abstract class PelEntry implements Stringable
{
    /**
     * Type of IFD containing this tag.
     *
     * This must be one of the constants defined in {@link PelIfd}:
     * {@link PelIfd::IFD0} for the main image IFD, {@link PelIfd::IFD1}
     * for the thumbnail image IFD, {@link PelIfd::EXIF} for the Exif
     * sub-IFD, {@link PelIfd::GPS} for the GPS sub-IFD, or {@link PelIfd::INTEROPERABILITY} for the interoperability sub-IFD.
     */
    protected int $ifd_type = PelIfd::IFD0;

    /**
     * The bytes representing this entry.
     *
     * Subclasses must either override {@link getBytes()} or, if
     * possible, maintain this property so that it always contains a
     * true representation of the entry.
     */
    protected string $bytes = '';

    /**
     * The {@link PelTag} of this entry.
     */
    protected int $tag;

    /**
     * The {@link PelFormat} of this entry.
     */
    protected int $format;

    /**
     * The number of components of this entry.
     */
    protected int $components;

    /**
     * Turn this entry into a string.
     *
     * @return string a string representation of this entry. This is
     *         mostly for debugging.
     */
    public function __toString(): string
    {
        $str = Pel::fmt("  Tag: 0x%04X (%s)\n", $this->tag, PelTag::getName($this->ifd_type, $this->tag));
        $str .= Pel::fmt("    Format    : %d (%s)\n", $this->format, PelFormat::getName($this->format));
        $str .= Pel::fmt("    Components: %d\n", $this->components);
        if ($this->getTag() !== PelTag::MAKER_NOTE && $this->getTag() !== PelTag::PRINT_IM) {
            $str .= Pel::fmt("    Value     : %s\n", print_r($this->getValue(), true));
        }
        $str .= Pel::fmt("    Text      : %s\n", $this->getText());
        return $str;
    }

    /**
     * Return the tag of this entry.
     *
     * @return int the tag of this entry.
     */
    public function getTag(): int
    {
        return $this->tag;
    }

    /**
     * Return the type of IFD which holds this entry.
     *
     * @return int one of the constants defined in {@link PelIfd}:
     *         {@link PelIfd::IFD0} for the main image IFD, {@link PelIfd::IFD1}
     *         for the thumbnail image IFD, {@link PelIfd::EXIF} for the Exif
     *         sub-IFD, {@link PelIfd::GPS} for the GPS sub-IFD, or {@link PelIfd::INTEROPERABILITY} for the interoperability sub-IFD.
     */
    public function getIfdType(): int
    {
        return $this->ifd_type;
    }

    /**
     * Update the IFD type.
     *
     * @param int $type
     *            must be one of the constants defined in {@link PelIfd}: {@link PelIfd::IFD0} for the main image IFD, {@link PelIfd::IFD1} for the thumbnail image IFD, {@link PelIfd::EXIF}
     *            for the Exif sub-IFD, {@link PelIfd::GPS} for the GPS sub-IFD, or
     *            {@link PelIfd::INTEROPERABILITY} for the interoperability
     *            sub-IFD.
     */
    public function setIfdType(int $type): void
    {
        $this->ifd_type = $type;
    }

    /**
     * Return the format of this entry.
     *
     * @return int the format of this entry.
     */
    public function getFormat(): int
    {
        return $this->format;
    }

    /**
     * Return the number of components of this entry.
     *
     * @return int the number of components of this entry.
     */
    public function getComponents(): int
    {
        return $this->components;
    }

    /**
     * Turn this entry into bytes.
     *
     * @param bool $o
     *            the desired byte order, which must be either
     *            {@link Convert::LITTLE_ENDIAN} or {@link Convert::BIG_ENDIAN}.
     *
     * @return string bytes representing this entry.
     */
    public function getBytes(bool $o): string
    {
        return $this->bytes;
    }

    /**
     * Get the value of this entry as text.
     *
     * The value will be returned in a format suitable for presentation,
     * e.g., rationals will be returned as 'x/y', ASCII strings will be
     * returned as themselves etc.
     *
     * @param bool $brief
     *            some values can be returned in a long or more
     *            brief form, and this parameter controls that.
     *
     * @return string the value as text.
     */
    abstract public function getText(bool $brief = false): string;

    /**
     * Get the value of this entry.
     *
     * The value returned will generally be the same as the one supplied
     * to the constructor or with {@link setValue()}. For a formatted
     * version of the value, one should use {@link getText()} instead.
     *
     * @return mixed the unformatted value.
     */
    abstract public function getValue(): mixed;

    /**
     * Set the value of this entry.
     *
     * The value should be in the same format as for the constructor.
     *
     * @param mixed $value
     *            the new value.
     *
     * @abstract
     */
    abstract public function setValue(mixed $value): void;
}
