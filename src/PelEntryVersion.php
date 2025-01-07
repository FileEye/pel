<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class to hold version information.
 *
 * There are three Exif entries that hold version information: the
 * {@link PelTag::EXIF_VERSION}, {@link PelTag::FLASH_PIX_VERSION}, and {@link PelTag::INTEROPERABILITY_VERSION} tags. This class manages
 * those tags.
 *
 * The class is used in a very straight-forward way:
 * <code>
 * $entry = new PelEntryVersion(PelTag::EXIF_VERSION, 2.2);
 * </code>
 * This creates an entry for an file complying to the Exif 2.2
 * standard. It is easy to test for standards level of an unknown
 * entry:
 * <code>
 * if ($entry->getTag() === PelTag::EXIF_VERSION &&
 * $entry->getValue() > 2.0) {
 * echo 'Recent Exif version.';
 * }
 * </code>
 */
class PelEntryVersion extends PelEntry
{
    /**
     * The version held by this entry.
     */
    private float $version;

    /**
     * Make a new entry for holding a version.
     *
     * @param int $tag
     *            This should be one of {@link PelTag::EXIF_VERSION}, {@link PelTag::FLASH_PIX_VERSION},
     *            or {@link PelTag::INTEROPERABILITY_VERSION}.
     * @param float $version
     *            The size of the entries leave room for
     *            exactly four digits: two digits on either side of the decimal
     *            point.
     */
    public function __construct(int $tag, float $version = 0.0)
    {
        $this->tag = $tag;
        $this->format = PelFormat::UNDEFINED;
        $this->setValue($version);
    }

    /**
     * Set the version held by this entry.
     *
     * @param float $version
     *            The size of the entries leave room for
     *            exactly four digits: two digits on either side of the decimal
     *            point.
     */
    public function setValue(mixed $version = 0.0): void
    {
        $this->version = $version;
        $major = floor($version);
        $minor = ($version - $major) * 100;
        $strValue = sprintf('%02.0f%02.0f', $major, $minor);
        $this->components = strlen($strValue);
        $this->bytes = $strValue;
    }

    /**
     * Return the version held by this entry.
     *
     * @return float This will be the same as the value
     *         given to {@link setValue} or {@link __construct the
     *         constructor}.
     */
    public function getValue(): float
    {
        return $this->version;
    }

    /**
     * Return a text string with the version.
     *
     * @param bool $brief
     *            controls if the output should be brief. Brief
     *            output omits the word 'Version' so the result is just 'Exif x.y'
     *            instead of 'Exif Version x.y' if the entry holds information
     *            about the Exif version --- the output for FlashPix is similar.
     *
     * @return string the version number with the type of the tag,
     *         either 'Exif' or 'FlashPix'.
     */
    public function getText(bool $brief = false): string
    {
        $v = $this->version;

        /*
         * Versions numbers like 2.0 would be output as just 2 if we don't
         * add the '.0' ourselves.
         */
        if (floor($this->version) === $this->version) {
            $v .= '.0';
        }

        switch ($this->tag) {
            case PelTag::EXIF_VERSION:
                if ($brief) {
                    return Pel::fmt('Exif %s', $v);
                }
                return Pel::fmt('Exif Version %s', $v);

            case PelTag::FLASH_PIX_VERSION:
                if ($brief) {
                    return Pel::fmt('FlashPix %s', $v);
                }
                return Pel::fmt('FlashPix Version %s', $v);

            case PelTag::INTEROPERABILITY_VERSION:
                if ($brief) {
                    return Pel::fmt('Interoperability %s', $v);
                }
                return Pel::fmt('Interoperability Version %s', $v);
        }

        if ($brief) {
            return (string) $v;
        }
        return Pel::fmt('Version %s', $v);
    }
}
