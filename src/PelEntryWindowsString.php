<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class used to manipulate strings in the format Windows XP uses.
 *
 * When examining the file properties of an image in Windows XP one
 * can fill in title, comment, author, keyword, and subject fields.
 * Filling those fields and pressing OK will result in the data being
 * written into the Exif data in the image.
 *
 * The data is written in a non-standard format and can thus not be
 * loaded directly --- this class is needed to translate it into
 * normal strings.
 *
 * It is important that entries from this class are only created with
 * the {@link PelTag::XP_TITLE}, {@link PelTag::XP_COMMENT}, {@link PelTag::XP_AUTHOR},
 * {@link PelTag::XP_KEYWORD}, and {@link PelTag::XP_SUBJECT} tags.
 *
 * If another tag is used the data will no
 * longer be correctly decoded when reloaded with PEL. (The data will
 * be loaded as an {@link PelEntryByte} entry, which isn't as useful.)
 *
 * This class is to be used as in
 * <code>
 * $title = $ifd->getEntry(PelTag::XP_TITLE);
 * print($title->getValue());
 * $title->setValue('My favorite cat');
 * </code>
 * or if no entry is present one can add a new one with
 * <code>
 * $title = new PelEntryWindowsString(PelTag::XP_TITLE, 'A cute dog.');
 * $ifd->addEntry($title);
 * </code>
 */
class PelEntryWindowsString extends PelEntry
{
    /**
     * Two zero characters
     */
    public const ZEROES = "\x0\x0";

    /**
     * The string hold by this entry.
     *
     * This is the string that was given to the {@link __construct
     * constructor} or later to {@link setValue}, without any extra NULL
     * characters or any such nonsense.
     */
    private string $str = '';

    /**
     * Make a new PelEntry that can hold a Windows XP specific string.
     *
     * @param int $tag
     *            the tag which this entry represents. This should be
     *            one of {@link PelTag::XP_TITLE}, {@link PelTag::XP_COMMENT},
     *            {@link PelTag::XP_AUTHOR}, {@link PelTag::XP_KEYWORD}, and
     *            {@link PelTag::XP_SUBJECT} tags. If another tag is used, then this
     *            entry will be incorrectly reloaded as a {@link PelEntryByte}.
     * @param string $str
     *            the string that this entry will represent. It will
     *            be passed to {@link setValue} and thus has to obey its
     *            requirements.
     * @param bool $from_exif
     *            internal use only, tells that string is UCS-2LE encoded, as PHP fails to detect this encoding
     */
    public function __construct(int $tag, string $str = '', bool $from_exif = false)
    {
        $this->tag = $tag;
        $this->format = PelFormat::BYTE;
        $this->setValue($str, $from_exif);
    }

    /**
     * Give the entry a new value.
     *
     * This will overwrite the previous value. The value can be
     * retrieved later with the {@link getValue} method.
     *
     * @param string $str
     *            the new value of the entry.
     * @param bool $from_exif
     *            internal use only, tells that string is UCS-2LE encoded, as PHP fails to detect this encoding
     */
    public function setValue(mixed $str, bool $from_exif = false): void
    {
        $zlen = strlen(self::ZEROES);
        if ($from_exif !== false) {
            $s = $str;
            if (substr($str, - $zlen, $zlen) === self::ZEROES) {
                $str = substr($str, 0, - $zlen);
            }
            $str = mb_convert_encoding($str, 'UTF-8', 'UCS-2LE');
        } else {
            $s = mb_convert_encoding($str, 'UCS-2LE', 'auto');
        }

        if (substr($s, - $zlen, $zlen) !== self::ZEROES) {
            $s .= self::ZEROES;
        }
        $l = strlen($s);

        $this->components = $l;
        $this->str = $str;
        $this->bytes = $s;
    }

    /**
     * Return the string of the entry.
     *
     * @return string the string held, without any extra NULL
     *         characters. The string will be the same as the one given to
     *         {@link setValue} or to the {@link __construct constructor}.
     */
    public function getValue(): string
    {
        return $this->str;
    }

    /**
     * Return the string of the entry.
     *
     * This methods returns the same as {@link getValue}.
     *
     * @param bool $brief
     *            not used.
     *
     * @return string the string held, without any extra NULL
     *         characters. The string will be the same as the one given to
     *         {@link setValue} or to the {@link __construct constructor}.
     */
    public function getText(bool $brief = false): string
    {
        return $this->str;
    }
}
