<?php

/*  PEL: PHP EXIF Library.  A library with support for reading and
 *  writing all EXIF headers of JPEG images using PHP.
 *
 *  Copyright (C) 2004  Martin Geisler <gimpster@users.sourceforge.net>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program in the file COPYING; if not, write to the
 *  Free Software Foundation, Inc., 59 Temple Place, Suite 330,
 *  Boston, MA 02111-1307 USA
 */

/* $Id$ */


/**
 * Classes used to hold ASCII strings.
 *
 * The classes defined here are to be used for EXIF entries holding
 * ASCII strings, such as {@link PelExifTag::MAKE}, {@link
 * PelExifTag::SOFTWARE}, and {@link PelExifTag::DATE_TIME}.  For
 * entries holding normal textual ASCII strings the class {@link
 * PelExifEntryAscii} should be used, but for entries holding
 * timestamps the class {@link PelExifEntryTime} would be more
 * convenient instead.  Copyright information is handled by the {@link
 * PelExifEntryCopyright} class.
 *
 * @author Martin Geisler <gimpster@users.sourceforge.net>
 * @version $Revision$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public
 * License (GPL)
 * @package PEL
 * @subpackage EXIF
 */

/** Class definition of {@link PelExifEntry}. */
include_once('PelExifEntry.php');

/**
 * Class for holding a plain ASCII string.
 *
 * This class can hold a single ASCII string, and it will be used as in
 * <code>
 * $entry = $ifd->getEntry(PelExifTag::IMAGE_DESCRIPTION);
 * print($entry->getAscii());
 * $entry->setAscii('This is my image.  I like it.');
 * </code>
 *
 * @author Martin Geisler <gimpster@users.sourceforge.net>
 * @package PEL
 * @subpackage EXIF
 */
class PelExifEntryAscii extends PelExifEntry {

  /**
   * The string hold by this entry.
   *
   * This is the string that was given to the {@link __construct
   * constructor} or later to {@link setAscii}, without any final NULL
   * character.
   *
   * @var string
   */
  private $str;


  /**
   * Make a new PelExifEntry that can hold an ASCII string.
   *
   * @param int the tag which this entry represents.  This should be
   * one of the constants defined in {@link PelExifTag}, e.g., {@link
   * PelExifTag::IMAGE_DESCRIPTION}, {@link PelExifTag::MODEL}, or any other
   * tag with format {@link PelExifFormat::ASCII}.
   *
   * @param string the string that this entry will represent.  The
   * string must obey the same rules as the string argument to {@link
   * setAscii}, namely that it should be given without any trailing
   * NULL character and that it must be plain 7-bit ASCII.
   */
  function __construct($tag, $str = '') {
    $this->tag    = $tag;
    $this->format = PelExifFormat::ASCII;
    $this->setAscii($str);
  }


  /**
   * Give the entry a new ASCII value.
   *
   * This will overwrite the previous value.  The value can be
   * retrieved later with the {@link getAscii} method.
   *
   * @param string the new value of the entry.  This should be given
   * without any trailing NULL character.  The string must be plain
   * 7-bit ASCII, the string should contain no high bytes.
   */
  function setAscii($str) {
    // TODO: check that we always use a NULL character to terminate
    // ASCII strings.
    // TODO: check for ASCII string.
    $this->components = strlen($str)+1;
    $this->str        = $str;
    $this->bytes      = $str . chr(0);
  }


  /**
   * Return the ASCII string of the entry.
   *
   * @return string the string held, without any final NULL character.
   * The string will be the same as the one given to {@link setAscii}
   * or to the {@link __construct constructor}.
   */
  function getAscii() {
    return $this->str;
  }


  /**
   * Return the ASCII string of the entry.
   *
   * This methods returns the same as {@link getAscii}.
   *
   * @param boolean not used with ASCII entries.
   *
   * @return string the string held, without any final NULL character.
   * The string will be the same as the one given to {@link setAscii}
   * or to the {@link __construct constructor}.
   */
  function getText($brief = false) {
    return $this->str;      
  }

}


/**
 * Class for holding a UNIX timestamp.
 *
 * This class can hold a single UNIX timestamp, and it will be used as
 * in this example where the time is advanced by one week:
 * <code>
 * $entry = $ifd->getEntry(ExifTag::DATE_TIME_ORIGINAL);
 * $time = $entry->getTime();
 * print('The image was taken on the ' . date($time, 'jS'));
 * $entry->setTime($time + 7 * 24 * 3600);
 * </code>
 *
 * @author Martin Geisler <gimpster@users.sourceforge.net>
 * @package PEL
 * @subpackage EXIF
 */
class PelExifEntryTime extends PelExifEntryAscii {

  /**
   * The UNIX timestamp held by this entry.
   *
   * @var int
   */
  private $timestamp;


  /**
   * Make a new entry for holding a timestamp.
   *
   * @param int the EXIF tag which this entry represents.  There are
   * only three standard tags which hold timestamp, so this should be
   * one of the constants {@link PelExifTag::DATE_TIME}, {@link
   * PelExifTag::DATE_TIME_ORIGINAL}, or {@link
   * PelExifTag::DATE_TIME_DIGITIZED}.
   *
   * @param int the UNIX timestamp held by this entry.
   */
  function __construct($tag, $timestamp = false) {
    if (!is_int($timestamp))
      $timestamp = time();

    // TODO: use gmdate() instead, or how to deal with timezones? Use
    // the TimeZoneOffset tag 0x882A?
    parent::__construct($tag);
    $this->setTime($timestamp);
  }

  
  /**
   * Return the UNIX timestamp of the entry.
   *
   * @return int the timestamp held.  This will be a standard UNIX
   * timestamp (counting the number of seconds since 00:00:00 January
   * 1st, 1970 UTC).  This will be the same as the one given to {@link
   * setTime} or to the {@link __construct constructor}.
   */
  function getTime() {
    return $this->timestamp;
  }


  /**
   * Update the UNIX timestamp held by this entry.
   *
   * @param int the new timestamp to be held by this entry.  This
   * should be a standard UNIX timestamp (counting the number of
   * seconds since 00:00:00 January 1st, 1970 UTC).  The old timestamp
   * will be overwritten, retrive it first with {@link getTime} if
   * necessary.
   */
  function setTime($timestamp) {
    $this->timestamp = $timestamp;
    $this->setAscii(date('Y:m:d H:i:s', $timestamp));
  }
}


/**
 * Class for holding copyright information.
 *
 * The EXIF standard speficies a certain format for copyright
 * information where the one {@link PelExifTag::COPYRIGHT copyright
 * tag} holds both the photographer and editor copyrights, separated
 * by a NULL character.
 *
 * This class is used to manipulate that tag so that the format is
 * kept to the standard.  A common use would be to add a new copyright
 * tag to an image, since most cameras don't add this tag themselves.
 * This would be done like this:
 *
 * <code>
 * $entry = new PelExifEntryCopyright('Copyright, Martin Geisler, 2004');
 * $ifd0->addEntry($entry);
 * </code>
 *
 * Here we only set the photographer copyright, use the optional
 * second argument to specify the editor copyright.  If there's only
 * an editor copyright, then let the first argument be the empty
 * string.
 *
 * @author Martin Geisler <gimpster@users.sourceforge.net>
 * @package PEL
 * @subpackage EXIF
 */
class PelExifEntryCopyright extends PelExifEntryAscii {

  /**
   * The photographer copyright.
   *
   * @var string
   */
  private $photographer;

  /**
   * The editor copyright.
   *
   * @var string
   */
  private $editor;


  /**
   * Make a new entry for holding copyright information.
   *
   * @param string the photographer copyright.  Use the empty string
   * if there's no photographer copyright.
   *
   * @param string the editor copyright.  Use the empty string if
   * there's no editor copyright.
   */
  function __construct($photographer = '', $editor = '') {
    parent::__construct(PelExifTag::COPYRIGHT);
    $this->setCopyright($photographer, $editor);
  }
  

  /**
   * Update the copyright information.
   *
   * @param string the photographer copyright.  Use the empty string
   * if there's no photographer copyright.
   *
   * @param string the editor copyright.  Use the empty string if
   * there's no editor copyright.
   */
  function setCopyright($photographer = '', $editor = '') {
    $this->photographer = $photographer;
    $this->editor       = $editor;

    if ($photographer == '' && $editor != '')
      $photographer = ' ';

    if ($editor == '')
      $this->setAscii($photographer);
    else
      $this->setAscii($photographer . chr(0x00) . $editor);
  }


  /**
   * Retrive the copyright information.
   *
   * The strings returned will be the same as the one used previously
   * with either {@link __construct the constructor} or with {@link
   * setCopyright}.
   *
   * @return array an array with two strings, the photographer and
   * editor copyrights.  The two fields will be returned in that
   * order, so that the first array index will be the photographer
   * copyright, and the second will be the editor copyright.
   */
  function getCopyright() {
    return array($this->photographer, $this->editor);
  }


  /**
   * Return a text string with the copyright information.
   *
   * The photographer and editor copyright fields will be returned
   * with a '-' inbetween if both copyright fields are present,
   * otherwise only one of them will be returned.
   *
   * @param boolean if false, then the strings '(Photographer)' and
   * '(Editor)' will be appended to the photographer and editor
   * copyright fields (if present), otherwise the fields will be
   * returned as is.
   *
   * @return string the copyright information in a string.
   */
  function getText($brief = false) {
    if ($brief) {
      $p = '';
      $e = '';
    } else {
      $p = ' (Photographer)';
      $e = ' (Editor)';
    }

    if ($this->photographer != '' && $this->editor != '')
      return $this->photographer . $p . ' - ' . $this->editor . $e;
    
    if ($this->photographer != '')
      return $this->photographer . $p;

    if ($this->editor != '')
      return $this->editor . $e;

    return '';
  }
}

?>