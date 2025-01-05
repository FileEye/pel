<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for a user comment.
 *
 * This class is used to hold user comments, which can come in several
 * different character encodings. The Exif standard specifies a
 * certain format of the {@link PelTag::USER_COMMENT user comment
 * tag}, and this class will make sure that the format is kept.
 *
 * The most basic use of this class simply stores an ASCII encoded
 * string for later retrieval using {@link getValue}:
 *
 * <code>
 * $entry = new PelEntryUserComment('An ASCII string');
 * echo $entry->getValue();
 * </code>
 *
 * The string can be encoded with a different encoding, and if so, the
 * encoding must be given using the second argument. The Exif
 * standard specifies three known encodings: 'ASCII', 'JIS', and
 * 'Unicode'. If the user comment is encoded using a character
 * encoding different from the tree known encodings, then the empty
 * string should be passed as encoding, thereby specifying that the
 * encoding is undefined.
 */
class PelEntryUserComment extends PelEntryUndefined
{
    /**
     * The user comment.
     */
    private string $comment;

    /**
     * The encoding.
     *
     * This should be one of 'ASCII', 'JIS', 'Unicode', or ''.
     */
    private string $encoding;

    /**
     * Make a new entry for holding a user comment.
     *
     * @param string $comment
     *            the new user comment.
     * @param string $encoding
     *            the encoding of the comment. This should be either
     *            'ASCII', 'JIS', 'Unicode', or the empty string specifying an
     *            undefined encoding.
     */
    public function __construct(string $comment = '', string $encoding = 'ASCII')
    {
        parent::__construct(PelTag::USER_COMMENT);
        $this->setValue($comment, $encoding);
    }

    /**
     * Set the user comment.
     *
     * @param string $comment
     *            the new user comment.
     * @param string $encoding
     *            the encoding of the comment. This should be either
     *            'ASCII', 'JIS', 'Unicode', or the empty string specifying an
     *            unknown encoding.
     */
    public function setValue(mixed $comment = '', string $encoding = 'ASCII'): void
    {
        $this->comment = $comment;
        $this->encoding = $encoding;
        parent::setValue(str_pad($encoding, 8, chr(0)) . $comment);
    }

    /**
     * Returns the user comment.
     *
     * The comment is returned with the same character encoding as when
     * it was set using {@link setValue} or {@link __construct the
     * constructor}.
     *
     * @return string the user comment.
     */
    public function getValue(): string
    {
        return $this->comment;
    }

    /**
     * Returns the encoding.
     *
     * @return string the encoding of the user comment.
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Returns the user comment.
     *
     * @return string the user comment.
     */
    public function getText(bool $brief = false): string
    {
        return $this->comment;
    }
}
