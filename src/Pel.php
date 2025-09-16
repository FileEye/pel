<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class with miscellaneous static methods.
 *
 * This class will contain various methods that govern the overall
 * behavior of PEL.
 *
 * Debugging output from PEL can be turned on and off by assigning
 * true or false to {@link Pel::$debug}.
 */
class Pel
{
    /**
     * Flag that controls if dgettext can be used.
     * Is set to true or false at the first access
     */
    private static ?bool $hasdgetext = null;

    /**
     * Flag for controlling debug information.
     *
     * The methods producing debug information ({@link debug()} and
     * {@link warning()}) will only output something if this variable is
     * set to true.
     */
    private static bool $debug = false;

    /**
     * Flag for strictness of parsing.
     *
     * If this variable is set to true, then most errors while loading
     * images will result in exceptions being thrown. Otherwise a
     * warning will be emitted (using {@link Pel::warning}) and the
     * exceptions will be appended to {@link Pel::$exceptions}.
     *
     * Some errors will still be fatal and result in thrown exceptions,
     * but an effort will be made to skip over as much garbage as
     * possible.
     */
    private static bool $strict = false;

    /**
     * Stored exceptions.
     *
     * When {@link Pel::$strict} is set to false exceptions will be
     * accumulated here instead of being thrown.
     *
     * @var array<int, PelException>
     */
    private static array $exceptions = [];

    /**
     * Quality setting for encoding JPEG images.
     *
     * This controls the quality used then PHP image resources are
     * encoded into JPEG images. This happens when you create a
     * {@link PelJpeg} object based on an image resource.
     *
     * The default is 75 for average quality images, but you can change
     * this to an integer between 0 and 100.
     */
    private static int $quality = 75;

    /**
     * Set the JPEG encoding quality.
     *
     * $quality = an integer between 0 and 100 with 75 being
     *            average quality and 95 very good quality.
     */
    public static function setJPEGQuality(int $quality): void
    {
        self::$quality = $quality;
    }

    /**
     * Get current setting for JPEG encoding quality.
     */
    public static function getJPEGQuality(): int
    {
        return self::$quality;
    }

    /**
     * Return list of stored exceptions.
     *
     * When PEL is parsing in non-strict mode, it will store most
     * exceptions instead of throwing them. Use this method to get hold
     * of them when a call returns.
     *
     * Code for using this could look like this:
     *
     * <code>
     * Pel::setStrictParsing(true);
     * Pel::clearExceptions();
     *
     * $jpeg = new PelJpeg($file);
     *
     * // Check for exceptions.
     * foreach (Pel::getExceptions() as $e) {
     * printf("Exception: %s\n", $e->getMessage());
     * if ($e instanceof PelEntryException) {
     * // Warn about entries that couldn't be loaded.
     * printf("Warning: Problem with %s.\n",
     * PelTag::getName($e->getType(), $e->getTag()));
     * }
     * }
     * </code>
     *
     * This gives applications total control over the amount of error
     * messages shown and (hopefully) provides the necessary information
     * for proper error recovery.
     *
     * @return array<int, PelException>
     */
    public static function getExceptions(): array
    {
        return self::$exceptions;
    }

    /**
     * Clear list of stored exceptions.
     *
     * Use this function before a call to some method if you intend to
     * check for exceptions afterwards.
     */
    public static function clearExceptions(): void
    {
        self::$exceptions = [];
    }

    /**
     * Conditionally throw an exception.
     *
     * This method will throw the passed exception when strict parsing
     * in effect (see {@link setStrictParsing()}). Otherwise the
     * exception is stored (it can be accessed with {@link getExceptions()})
     * and a warning is issued (with {@link Pel::warning}).
     */
    public static function maybeThrow(PelException $e): void
    {
        if (self::$strict) {
            throw $e;
        }
        self::$exceptions[] = $e;
        self::warning('%s (%s:%s)', $e->getMessage(), basename($e->getFile()), $e->getLine());
    }

    /**
     * Enable/disable strict parsing.
     *
     * If strict parsing is enabled, then most errors while loading
     * images will result in exceptions being thrown. Otherwise a
     * warning will be emitted (using {@link Pel::warning}) and the
     * exceptions will be stored for later use via {@link getExceptions()}.
     *
     * Some errors will still be fatal and result in thrown exceptions,
     * but an effort will be made to skip over as much garbage as
     * possible.
     *
     * @param bool $flag
     *            use true to enable strict parsing, false to
     *            disable.
     */
    public static function setStrictParsing(bool $flag): void
    {
        self::$strict = $flag;
    }

    /**
     * Get current setting for strict parsing.
     *
     * @return bool true if strict parsing is in effect, false
     *         otherwise.
     */
    public static function getStrictParsing(): bool
    {
        return self::$strict;
    }

    /**
     * Enable/disable debugging output.
     *
     * @param bool $flag
     *            use true to enable debug output, false to
     *            disable.
     */
    public static function setDebug(bool $flag): void
    {
        self::$debug = $flag;
    }

    /**
     * Get current setting for debug output.
     *
     * @return bool true if debug is enabled, false otherwise.
     */
    public static function getDebug(): bool
    {
        return self::$debug;
    }

    /**
     * Conditionally output debug information.
     *
     * This method works just like printf() except that it always
     * terminates the output with a newline, and that it only outputs
     * something if the {@link Pel::$debug} is true.
     *
     * @param string $format the format string.
     * @param mixed ...$args [optional]
     *            any number of arguments can be given. The
     *            arguments will be available for the format string as usual with
     *            sprintf().
     */
    public static function debug(string $format, mixed ...$args): void
    {
        if (self::$debug) {
            vprintf($format . "\n", $args);
        }
    }

    /**
     * Conditionally output a warning.
     *
     * This method works just like printf() except that it prepends the
     * output with the string 'Warning: ', terminates the output with a
     * newline, and that it only outputs something if the PEL_DEBUG
     * defined to some true value.
     *
     * @param string $format the format string.
     * @param mixed ...$args [optional]
     *            any number of arguments can be given. The
     *            arguments will be available for the format string as usual with
     *            sprintf().
     */
    public static function warning(string $format, mixed ...$args): void
    {
        if (self::$debug) {
            vprintf('Warning: ' . $format . "\n", $args);
        }
    }

    /**
     * Translate a string.
     *
     * This static function will use Gettext to translate a string. By
     * always using this function for static string one is assured that
     * the translation will be taken from the correct text domain.
     * Dynamic strings should be passed to {@link fmt} instead.
     *
     * @param string $str
     *            the string that should be translated.
     *
     * @return string the translated string, or the original string if
     *         no translation could be found.
     */
    public static function tra(string $str): string
    {
        return self::dgettextWrapper('pel', $str);
    }

    /**
     * Translate and format a string.
     *
     * This static function will first use Gettext to translate a format
     * string, which will then have access to any extra arguments. By
     * always using this function for dynamic string one is assured that
     * the translation will be taken from the correct text domain. If
     * the string is static, use {@link tra} instead as it will be
     * faster.
     *
     * @param string $format
     *            the format string. This will be translated
     *            before being used as a format string.
     * @param mixed ...$args [optional]
     *            any number of arguments can be given. The
     *            arguments will be available for the format string as usual with
     *            sprintf().
     *
     * @return string the translated string, or the original string if
     *         no translation could be found.
     */
    public static function fmt(string $format, mixed ...$args): string
    {
        return vsprintf(self::dgettextWrapper('pel', $format), $args);
    }

    /**
     * Wrapper for dgettext.
     * The untranslated stub will be return in the case that dgettext is not available.
     */
    private static function dgettextWrapper(string $domain, string $str): string
    {
        if (self::$hasdgetext === null) {
            self::$hasdgetext = function_exists('dgettext');
            if (self::$hasdgetext === true) {
                bindtextdomain('pel', __DIR__ . '/locale');
            }
        }
        if (self::$hasdgetext) {
            return dgettext($domain, $str);
        }
        return $str;
    }
}
