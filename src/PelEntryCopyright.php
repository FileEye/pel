<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding copyright information.
 *
 * The Exif standard specifies a certain format for copyright
 * information where the one {@link PelTag::COPYRIGHT copyright
 * tag} holds both the photographer and editor copyrights, separated
 * by a NULL character.
 *
 * This class is used to manipulate that tag so that the format is
 * kept to the standard. A common use would be to add a new copyright
 * tag to an image, since most cameras do not add this tag themselves.
 * This would be done like this:
 *
 * <code>
 * $entry = new PelEntryCopyright('Copyright, Martin Geisler, 2004');
 * $ifd0->addEntry($entry);
 * </code>
 *
 * Here we only set the photographer copyright, use the optional
 * second argument to specify the editor copyright. If there is only
 * an editor copyright, then let the first argument be the empty
 * string.
 */
class PelEntryCopyright extends PelEntryAscii
{
    /**
     * The photographer copyright.
     */
    private string $photographer;

    /**
     * The editor copyright.
     */
    private string $editor;

    /**
     * Make a new entry for holding copyright information.
     *
     * @param string $photographer
     *            the photographer copyright. Use the empty string
     *            if there is no photographer copyright.
     * @param string $editor
     *            the editor copyright. Use the empty string if
     *            there is no editor copyright.
     */
    public function __construct(string $photographer = '', string $editor = '')
    {
        parent::__construct(PelTag::COPYRIGHT);
        $this->setValue($photographer, $editor);
    }

    /**
     * Update the copyright information.
     *
     * @param string $photographer the photographer copyright. Use the empty string
     *            if there is no photographer copyright.
     * @param string $editor the editor copyright. Use the empty string if
     *            there is no editor copyright.
     */
    public function setValue(mixed $photographer = '', string $editor = ''): void
    {
        $this->photographer = $photographer;
        $this->editor = $editor;

        if ($photographer === '' && $editor !== '') {
            $photographer = ' ';
        }

        if ($editor === '') {
            parent::setValue($photographer);
        } else {
            parent::setValue($photographer . chr(0x00) . $editor);
        }
    }

    /**
     * Retrieve the copyright information.
     *
     * The strings returned will be the same as the one used previously
     * with either {@link __construct the constructor} or with {@link setValue}.
     *
     * @return array<int, string> an array with two strings, the photographer and
     *         editor copyrights. The two fields will be returned in that
     *         order, so that the first array index will be the photographer
     *         copyright, and the second will be the editor copyright.
     */
    public function getValueArray(): array
    {
        return [
            $this->photographer,
            $this->editor,
        ];
    }

    /**
     * Return a text string with the copyright information.
     *
     * The photographer and editor copyright fields will be returned
     * with a '-' in between if both copyright fields are present,
     * otherwise only one of them will be returned.
     *
     * @param bool $brief
     *            if false, then the strings '(Photographer)' and
     *            '(Editor)' will be appended to the photographer and editor
     *            copyright fields (if present), otherwise the fields will be
     *            returned as is.
     *
     * @return string the copyright information in a string.
     */
    public function getText(bool $brief = false): string
    {
        if ($brief) {
            $p = '';
            $e = '';
        } else {
            $p = ' ' . Pel::tra('(Photographer)');
            $e = ' ' . Pel::tra('(Editor)');
        }

        if ($this->photographer !== '' && $this->editor !== '') {
            return $this->photographer . $p . ' - ' . $this->editor . $e;
        }

        if ($this->photographer !== '') {
            return $this->photographer . $p;
        }

        if ($this->editor !== '') {
            return $this->editor . $e;
        }

        return '';
    }
}
