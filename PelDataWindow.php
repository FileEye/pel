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
 * A container for bytes with a limited window of accessible bytes.
 *
 * @author Martin Geisler <gimpster@users.sourceforge.net>
 * @version $Revision$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @package PEL
 */

/** Class definition of {@link PelException}. */
include_once('PelException.php');
/** Class definition of {@link PelConvert}. */
include_once('PelConvert.php');


/**
 * An exception thrown when an invalid offset is encountered.
 *
 * @package PEL
 */
class PelDataWindowOffsetException extends PelException {}

/**
 * An exception thrown when an invalid window is encountered.
 *
 * @package PEL
 */
class PelDataWindowWindowException extends PelException {}

/**
 * The window.
 *
 * @package PEL
 */
class PelDataWindow {

  /**
   * The data held by this window.
   *
   * The string can contain any kind of data, including binary data.
   *
   * @var string
   */
  private $data = '';

  /**
   * The byte order currently in use.
   *
   * This will be the byte order used when data is read using the for
   * example the {@link getShort} function.  It must be one of {@link
   * PelConvert::LITTLE_ENDIAN} and {@link PelConvert::BIG_ENDIAN}.
   *
   * @var PelByteOrder
   * @see setByteOrder, getByteOrder
   */
  private $order;

  /**
   * The start of the current window.
   *
   * All offsets used for access into the data will count from this
   * offset, effectively limiting access to a window starting at this
   * byte.
   *
   * @var int
   * @see setWindowStart
   */
  private $start  = 0;

  /**
   * The size of the current window.
   *
   * All offsets used for access into the data will be limited by this
   * variable.  A valid offset must be strictly less than this
   * variable.
   *
   * @var int
   * @see setWindowSize
   */
  private $size   = 0;


  /**
   * Construct a new data window with the data supplied.
   *
   * @param string the data that this window will contain.  The data
   * will be copyed into the new data window.
   *
   * @param boolean the initial byte order of the window.  This must
   * be either {@link PelConvert::LITTLE_ENDIAN} or {@link
   * PelConvert::BIG_ENDIAN}.  This will be used when integers are
   * read from the data, and it can be changed later with {@link
   * setByteOrder()}.
   */
  function __construct($d = '', $e = PelConvert::LITTLE_ENDIAN) {
    $this->data  = $d;
    $this->order = $e;
    $this->size  = strlen($d);
  }


  /**
   * Get the size of the data window.
   *
   * @return int the number of bytes covered by the window.  The
   * allowed offsets go from 0 up to this number minus one.
   *
   * @see getBytes()
   */
  function getSize() {
    return $this->size;
  }


  /**
   * Change the byte order of the data.
   *
   * @param PelByteOrder the new byte order.  This must be either
   * {@link Convert::LITTLE_ENDIAN} or {@link Convert::BIG_ENDIAN}.
   */
  function setByteOrder($o) {
    $this->order = $o;
  }


  /**
   * Get the currently used byte order.
   *
   * @return PelByteOrder this will be either {@link
   * Convert::LITTLE_ENDIAN} or {@link Convert::BIG_ENDIAN}.
   */
  function getByteOrder() {
    return $this->order;
  }


  /* Move the start of the window forward.
   *
   * @param int the new start of the window.  All new offsets will be
   * calculated from this new start offset, and the size of the window
   * will shrink to keep the end of the window in place.
   */
  function setWindowStart($start) {
    if ($start < 0 || $start > $this->size)
      throw new PelDataWindowWindowException('Window [%d, %d] ' .
                                          'does not fit in window [0, %d]',
                                          $start, $this->size, $this->size);

    $this->start += $start;
    $this->size  -= $start;
  }


  /**
   * Adjust the size of the window.
   *
   * The size can only be made smaller.
   *
   * @param int the desired size of the window.  If the argument is
   * negative, the window will be shrunk by the argument.
   */
  function setWindowSize($size) {
    if ($size < 0)
      $size += $this->size;

    if ($size < 0 || $size > $this->size)
      throw new PelDataWindowWindowException('Window [0, %d] ' .
                                          'does not fit in window [0, %d]',
                                          $size, $this->size);
    $this->size = $size;
  }
  

  /**
   * Initialize a copy with data.
   *
   * This is used internally by {@link getClone} to make return a copy
   * with the proper initialization.
   *
   * @param string a reference to the data this copy will hold.
   * @param int the start of the data window.
   * @param int the size of the data window.
   * @param PelByteOrder the byte order of the data.
   *
   * @see getClone
   */
  protected function initializeClone(&$data, $start, $size, $order) {
    $this->data  = &$data;
    $this->start = $start;
    $this->size  = $size;
    $this->order = $order;
  }


  /**
   * Make a new data window with the same data as the this window.
   *
   * The new window will read from the same data as this window, so
   * calling this method is significantly faster than using the
   * builtin clone functionality of PHP.
   *
   * @param mixed if an integer is supplied, then it will be the start
   * of the window in the clone.  If left unspecified, then the clone
   * will inherit the start from this object.
   *
   * @param mixed if an integer is suppied, then it will be the size
   * of the window in the clone.  If left unspecified, then the clone
   * will inherit the size from this object.
   *
   * @return PelDataWindow a new window that operates on the same data
   * as this window.
   */
  function getClone($start = false, $size = false) {
    $c = new PelDataWindow();
    $c->initializeClone($this->data, $this->start, $this->size, $this->order);

    if (is_int($start))
      $c->setWindowStart($start);

    if (is_int($size))
      $c->setWindowSize($size);

    return $c;
  }


  /**
   * Validate an offset against the current window.
   *
   * @param int the offset to be validated.  If the offset is negative
   * or if it is greater than or equal to the current window size,
   * then a {@link PelDataWindowOffsetException} is thrown.
   *
   * @return void if the offset is valid nothing is returned, if it's
   * invalid a new {@link PelDataWindowOffsetException} is thrown.
   */
  private function validateOffset($o) {
    if ($o < 0 || $o >= $this->size)
      throw new PelDataWindowOffsetException('Offset %d not within [%d, %d]',
                                          $o, 0, $this->size-1);
  }


  /**
   * Return some or all bytes visible in the window.
   *
   * This method works just like the standard {@link substr()}
   * function in PHP with the exception that it works within the
   * window of accible bytes and does strict range checking.
   * 
   * @param int the offset to the first byte returned.  If a negative
   * number is given, then the counting will be from the end of the
   * window.
   *
   * @param int the size of the sub-window.  If a negative number is
   * given, then that many bytes will be omitted from the result.
   *
   * @return string a subset of the bytes in the window.  This will
   * always return no more than {@link getSize()} bytes.
   */
  function getBytes($start = false, $size = false) {
    if (is_int($start)) {
      if ($start < 0)
        $start += $this->size;
      
      $this->validateOffset($start);
    } else {
      $start = 0;
    }
    
    if (is_int($size)) {
      if ($size <= 0)
        $size += $this->size - $start;
      
      $this->validateOffset($start+$size);
    } else {
      $size = $this->size;
    }

    return substr($this->data, $this->start + $start, $size);
  }


  /**
   * Return a byte from the data.
   *
   * @param int the offset into the data.  An offset of zero will
   * return the first byte in the current allowed window.  The last
   * valid offset is equal {@link getSize()}-1.
   *
   * @return  byte  the byte at offset.
   */
  function getByte($o = 0) {
    /* Validate the offset --- this throws an exception if offset is
     * out of range. */
    $this->validateOffset($o);

    /* Translate the offset into an offset into the data. */
    $o += $this->start;
    
    /* Return a byte. */
    return ord($this->data{$o});
  }

  function getShort($o = 0) {
    /* Validate the offset+1 to see if we can safely get two bytes ---
     * this throws an exception if offset is out of range. */
    // TODO: validate offset too?
    $this->validateOffset($o);
    $this->validateOffset($o+1);

    /* Translate the offset into an offset into the data. */
    $o += $this->start;

    /* Return a short. */
    return PelConvert::bytesToShort($this->data, $o, $this->order);
//     if ($this->order == self::LITTLE_ENDIAN)
//       return (ord($this->data{$o+1}) << 8 |
//               ord($this->data{$o}));
//     else
//       return (ord($this->data{$o})   << 8 |
//               ord($this->data{$o+1}));
  }

  function getLong($o = 0) {
    /* Validate the offset+3 to see if we can safely get four bytes
     * --- this throws an exception if offset is out of range. */
    // TODO: validate offset $o too?
    $this->validateOffset($o);
    $this->validateOffset($o+3);
   
    /* Translate the offset into an offset into the data. */
    $o += $this->start;

    /* Return a long. */
    return PelConvert::bytesToLong($this->data, $o, $this->order);
//     if ($this->endian == self::LITTLE_ENDIAN)
//       return (ord($this->data{$o+3}) << 24 |
//               ord($this->data{$o+2}) << 16 |
//               ord($this->data{$o+1}) <<  8 |
//               ord($this->data{$o}));
//     else
//       return (ord($this->data{$o})   << 24 |
//               ord($this->data{$o+1}) << 16 |
//               ord($this->data{$o+2}) <<  8 |
//               ord($this->data{$o+3}));
  }


  function getRational($o = 0) {
    return array($this->getLong($o), $this->getLong($o+4));
  }

  // TODO: is long good enough for slong?
  function getSRational($o = 0) {
    return array($this->getLong($o), $this->getLong($o+4));
  }

  
  function strcmp($o, $str) {
    /* Validate the offset of the final character we might have to
     * check. */
    $s = strlen($str);
    $this->validateOffset($o + $s - 1);

    /* Translate the offset into an offset into the data. */
    $o += $this->start;
  
    /* Check each character, return as soon as the answer is known. */
    for ($i = 0; $i < $s; $i++) {
      if ($this->data{$o + $i} != $str{$i})
        return false;
    }
    
    return true;
  }


  function __toString() {
    return sprintf('DataWindow: %d bytes in [%d, %d] of %d bytes',
                   $this->size,
                   $this->start, $this->start + $this->size,
                   strlen($this->data));
  }

  function clear() {
    $this->data = '(cleared)';
  }

}

?>