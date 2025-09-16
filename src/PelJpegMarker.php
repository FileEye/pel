<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Classes for dealing with JPEG markers.
 *
 * This class defines the constants to be used whenever one refers to
 * a JPEG marker. All the methods defined are static, and they all
 * operate on one argument which should be one of the class constants.
 * They will all be denoted by PelJpegMarker in the documentation.
 */
class PelJpegMarker
{
    /**
     * Encoding (baseline)
     */
    public const SOF0 = 0xC0;

    /**
     * Encoding (extended sequential)
     */
    public const SOF1 = 0xC1;

    /**
     * Encoding (progressive)
     */
    public const SOF2 = 0xC2;

    /**
     * Encoding (lossless)
     */
    public const SOF3 = 0xC3;

    /**
     * Define Huffman table
     */
    public const DHT = 0xC4;

    /**
     * Encoding (differential sequential)
     */
    public const SOF5 = 0xC5;

    /**
     * Encoding (differential progressive)
     */
    public const SOF6 = 0xC6;

    /**
     * Encoding (differential lossless)
     */
    public const SOF7 = 0xC7;

    /**
     * Extension
     */
    public const JPG = 0xC8;

    /**
     * Encoding (extended sequential, arithmetic)
     */
    public const SOF9 = 0xC9;

    /**
     * Encoding (progressive, arithmetic)
     */
    public const SOF10 = 0xCA;

    /**
     * Encoding (lossless, arithmetic)
     */
    public const SOF11 = 0xCB;

    /**
     * Define arithmetic coding conditioning
     */
    public const DAC = 0xCC;

    /**
     * Encoding (differential sequential, arithmetic)
     */
    public const SOF13 = 0xCD;

    /**
     * Encoding (differential progressive, arithmetic)
     */
    public const SOF14 = 0xCE;

    /**
     * Encoding (differential lossless, arithmetic)
     */
    public const SOF15 = 0xCF;

    /**
     * Restart 0
     */
    public const RST0 = 0xD0;

    /**
     * Restart 1
     */
    public const RST1 = 0xD1;

    /**
     * Restart 2
     */
    public const RST2 = 0xD2;

    /**
     * Restart 3
     */
    public const RST3 = 0xD3;

    /**
     * Restart 4
     */
    public const RST4 = 0xD4;

    /**
     * Restart 5
     */
    public const RST5 = 0xD5;

    /**
     * Restart 6
     */
    public const RST6 = 0xD6;

    /**
     * Restart 7
     */
    public const RST7 = 0xD7;

    /**
     * Start of image
     */
    public const SOI = 0xD8;

    /**
     * End of image
     */
    public const EOI = 0xD9;

    /**
     * Start of scan
     */
    public const SOS = 0xDA;

    /**
     * Define quantization table
     */
    public const DQT = 0xDB;

    /**
     * Define number of lines
     */
    public const DNL = 0xDC;

    /**
     * Define restart interval
     */
    public const DRI = 0xDD;

    /**
     * Define hierarchical progression
     */
    public const DHP = 0xDE;

    /**
     * Expand reference component
     */
    public const EXP = 0xDF;

    /**
     * Application segment 0
     */
    public const APP0 = 0xE0;

    /**
     * Application segment 1
     *
     * When a JPEG image contains Exif data, the data will normally be
     * stored in this section and a call to {@link PelJpeg::getExif()}
     * will return a {@link PelExif} object representing it.
     */
    public const APP1 = 0xE1;

    /**
     * Application segment 2
     */
    public const APP2 = 0xE2;

    /**
     * Application segment 3
     */
    public const APP3 = 0xE3;

    /**
     * Application segment 4
     */
    public const APP4 = 0xE4;

    /**
     * Application segment 5
     */
    public const APP5 = 0xE5;

    /**
     * Application segment 6
     */
    public const APP6 = 0xE6;

    /**
     * Application segment 7
     */
    public const APP7 = 0xE7;

    /**
     * Application segment 8
     */
    public const APP8 = 0xE8;

    /**
     * Application segment 9
     */
    public const APP9 = 0xE9;

    /**
     * Application segment 10
     */
    public const APP10 = 0xEA;

    /**
     * Application segment 11
     */
    public const APP11 = 0xEB;

    /**
     * Application segment 12
     */
    public const APP12 = 0xEC;

    /**
     * Application segment 13
     */
    public const APP13 = 0xED;

    /**
     * Application segment 14
     */
    public const APP14 = 0xEE;

    /**
     * Application segment 15
     */
    public const APP15 = 0xEF;

    /**
     * Extension 0
     */
    public const JPG0 = 0xF0;

    /**
     * Extension 1
     */
    public const JPG1 = 0xF1;

    /**
     * Extension 2
     */
    public const JPG2 = 0xF2;

    /**
     * Extension 3
     */
    public const JPG3 = 0xF3;

    /**
     * Extension 4
     */
    public const JPG4 = 0xF4;

    /**
     * Extension 5
     */
    public const JPG5 = 0xF5;

    /**
     * Extension 6
     */
    public const JPG6 = 0xF6;

    /**
     * Extension 7
     */
    public const JPG7 = 0xF7;

    /**
     * Extension 8
     */
    public const JPG8 = 0xF8;

    /**
     * Extension 9
     */
    public const JPG9 = 0xF9;

    /**
     * Extension 10
     */
    public const JPG10 = 0xFA;

    /**
     * Extension 11
     */
    public const JPG11 = 0xFB;

    /**
     * Extension 12
     */
    public const JPG12 = 0xFC;

    /**
     * Extension 13
     */
    public const JPG13 = 0xFD;

    /**
     * Comment
     */
    public const COM = 0xFE;

    /**
     * Values for marker's short names
     *
     * @var array<int, string>
     */
    protected static array $jpegMarkerShort = [
        self::SOF0 => 'SOF0',
        self::SOF1 => 'SOF1',
        self::SOF2 => 'SOF2',
        self::SOF3 => 'SOF3',
        self::SOF5 => 'SOF5',
        self::SOF6 => 'SOF6',
        self::SOF7 => 'SOF7',
        self::SOF9 => 'SOF9',
        self::SOF10 => 'SOF10',
        self::SOF11 => 'SOF11',
        self::SOF13 => 'SOF13',
        self::SOF14 => 'SOF14',
        self::SOF15 => 'SOF15',
        self::SOI => 'SOI',
        self::EOI => 'EOI',
        self::SOS => 'SOS',
        self::COM => 'COM',
        self::DHT => 'DHT',
        self::JPG => 'JPG',
        self::DAC => 'DAC',
        self::RST0 => 'RST0',
        self::RST1 => 'RST1',
        self::RST2 => 'RST2',
        self::RST3 => 'RST3',
        self::RST4 => 'RST4',
        self::RST5 => 'RST5',
        self::RST6 => 'RST6',
        self::RST7 => 'RST7',
        self::DQT => 'DQT',
        self::DNL => 'DNL',
        self::DRI => 'DRI',
        self::DHP => 'DHP',
        self::EXP => 'EXP',
        self::APP0 => 'APP0',
        self::APP1 => 'APP1',
        self::APP2 => 'APP2',
        self::APP3 => 'APP3',
        self::APP4 => 'APP4',
        self::APP5 => 'APP5',
        self::APP6 => 'APP6',
        self::APP7 => 'APP7',
        self::APP8 => 'APP8',
        self::APP9 => 'APP9',
        self::APP10 => 'APP10',
        self::APP11 => 'APP11',
        self::APP12 => 'APP12',
        self::APP13 => 'APP13',
        self::APP14 => 'APP14',
        self::APP15 => 'APP15',
        self::JPG0 => 'JPG0',
        self::JPG1 => 'JPG1',
        self::JPG2 => 'JPG2',
        self::JPG3 => 'JPG3',
        self::JPG4 => 'JPG4',
        self::JPG5 => 'JPG5',
        self::JPG6 => 'JPG6',
        self::JPG7 => 'JPG7',
        self::JPG8 => 'JPG8',
        self::JPG9 => 'JPG9',
        self::JPG10 => 'JPG10',
        self::JPG11 => 'JPG11',
        self::JPG12 => 'JPG12',
        self::JPG13 => 'JPG13',
    ];

    /**
     * Values for marker's descriptions names.
     *
     * @var array<int|string, string>
     */
    protected static array $jpegMarkerDescriptions = [
        self::SOF0 => 'Encoding (baseline)',
        self::SOF1 => 'Encoding (extended sequential)',
        self::SOF2 => 'Encoding (progressive)',
        self::SOF3 => 'Encoding (lossless)',
        self::SOF5 => 'Encoding (differential sequential)',
        self::SOF6 => 'Encoding (differential progressive)',
        self::SOF7 => 'Encoding (differential lossless)',
        self::SOF9 => 'Encoding (extended sequential, arithmetic)',
        self::SOF10 => 'Encoding (progressive, arithmetic)',
        self::SOF11 => 'Encoding (lossless, arithmetic)',
        self::SOF13 => 'Encoding (differential sequential, arithmetic)',
        self::SOF14 => 'Encoding (differential progressive, arithmetic)',
        self::SOF15 => 'Encoding (differential lossless, arithmetic)',
        self::SOI => 'Start of image',
        self::EOI => 'End of image',
        self::SOS => 'Start of scan',
        self::COM => 'Comment',
        self::DHT => 'Define Huffman table',
        self::JPG => 'Extension',
        self::DAC => 'Define arithmetic coding conditioning',
        'RST' => 'Restart %d',
        self::DQT => 'Define quantization table',
        self::DNL => 'Define number of lines',
        self::DRI => 'Define restart interval',
        self::DHP => 'Define hierarchical progression',
        self::EXP => 'Expand reference component',
        'APP' => 'Application segment %d',
        'JPG' => 'Extension %d',
    ];

    /**
     * Check if a byte is a valid JPEG marker.
     * If the byte is recognized true is returned, otherwise false will be returned.
     *
     * @param int $marker
     *            the marker as defined in {@link PelJpegMarker}
     */
    public static function isValid(int $marker): bool
    {
        return $marker >= self::SOF0 && $marker <= self::COM;
    }

    /**
     * Turn a JPEG marker into bytes.
     * This will be a string with just a single byte since all JPEG markers are simply single bytes.
     *
     * @param int $marker
     *            the marker as defined in {@link PelJpegMarker}
     */
    public static function getBytes(int $marker): string
    {
        return chr($marker);
    }

    /**
     * Return the short name for a marker, e.g., 'SOI' for the Start
     * of Image marker.
     *
     * @param int $marker
     *            the marker as defined in {@link PelJpegMarker}
     */
    public static function getName(int $marker): string
    {
        if (array_key_exists($marker, self::$jpegMarkerShort)) {
            return self::$jpegMarkerShort[$marker];
        }
        return Pel::fmt('Unknown marker: 0x%02X', $marker);
    }

    /**
     * Returns a description of a JPEG marker.
     *
     * @param int $marker
     *            the marker as defined in {@link PelJpegMarker}
     */
    public static function getDescription(int $marker): string
    {
        if (array_key_exists($marker, self::$jpegMarkerShort)) {
            if (array_key_exists($marker, self::$jpegMarkerDescriptions)) {
                return self::$jpegMarkerDescriptions[$marker];
            }
            $splitted = preg_split("/(\d+)/", self::$jpegMarkerShort[$marker], - 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            if ($splitted !== false && count($splitted) === 2 && array_key_exists($splitted[0], self::$jpegMarkerDescriptions)) {
                return Pel::fmt(self::$jpegMarkerDescriptions[$splitted[0]], $splitted[1]);
            }
        }
        return Pel::fmt('Unknown marker: 0x%02X', $marker);
    }
}
