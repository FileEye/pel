<?php

declare(strict_types=1);

namespace lsolesen\pel;

use Stringable;

/**
 * Class for handling TIFF data.
 *
 * Exif data is actually an extension of the TIFF file format. TIFF
 * images consist of a number of {@link PelIfd Image File Directories}
 * (IFDs), each containing a number of {@link PelEntry entries}. The
 * IFDs are linked to each other --- one can get hold of the first one
 * with the {@link getIfd()} method.
 *
 * To parse a TIFF image for Exif data one would do:
 *
 * <code>
 * $tiff = new PelTiff($data);
 * $ifd0 = $tiff->getIfd();
 * $exif = $ifd0->getSubIfd(PelIfd::EXIF);
 * $ifd1 = $ifd0->getNextIfd();
 * </code>
 *
 * Should one have some image data of an unknown type, then the
 * {@link PelTiff::isValid()} function is handy: it will quickly test if the
 * data could be valid TIFF data. The {@link PelJpeg::isValid()}
 * function does the same for JPEG images.
 */
class PelTiff implements Stringable
{
    /**
     * TIFF header.
     *
     * This must follow after the two bytes indicating the byte order.
     */
    public const TIFF_HEADER = 0x002A;

    /**
     * The first Image File Directory, if any.
     *
     * If set, then the type of the IFD must be {@link PelIfd::IFD0}.
     */
    private ?PelIfd $ifd = null;

    /**
     * Construct a new object for holding TIFF data.
     *
     * The new object will be empty (with no {@link PelIfd}) unless an
     * argument is given from which it can initialize itself. This can
     * either be the filename of a TIFF image or a {@link PelDataWindow}
     * object.
     *
     * Use {@link setIfd()} to explicitly set the IFD.
     *
     * @param bool|string|PelDataWindow $data;
     */
    public function __construct(bool|string|PelDataWindow $data = false)
    {
        if ($data === false) {
            return;
        }
        if (is_string($data)) {
            Pel::debug('Initializing PelTiff object from %s', $data);
            $this->loadFile($data);
        } elseif ($data instanceof PelDataWindow) {
            Pel::debug('Initializing PelTiff object from PelDataWindow.');
            $this->load($data);
        } else {
            throw new PelInvalidArgumentException('Bad type for $data: %s', gettype($data));
        }
    }

    /**
     * Return a string representation of this object.
     *
     * @return string a string describing this object. This is mostly useful
     *         for debugging.
     */
    public function __toString(): string
    {
        $str = Pel::fmt("Dumping TIFF data...\n");
        if ($this->ifd !== null) {
            $str .= $this->ifd->__toString();
        }

        return $str;
    }

    /**
     * Load TIFF data.
     *
     * The data given will be parsed and an internal tree representation
     * will be built. If the data cannot be parsed correctly, a
     * {@link PelInvalidDataException} is thrown, explaining the problem.
     *
     * @param PelDataWindow $d
     *            the data from which the object will be
     *            constructed. This should be valid TIFF data, coming either
     *            directly from a TIFF image or from the Exif data in a JPEG image.
     */
    public function load(PelDataWindow $d): void
    {
        Pel::debug('Parsing %d bytes of TIFF data...', $d->getSize());

        /*
         * There must be at least 8 bytes available: 2 bytes for the byte
         * order, 2 bytes for the TIFF header, and 4 bytes for the offset
         * to the first IFD.
         */
        if ($d->getSize() < 8) {
            throw new PelInvalidDataException('Expected at least 8 bytes of TIFF data, found just %d bytes.', $d->getSize());
        }

        /* Byte order */
        if ($d->strcmp(0, 'II')) {
            Pel::debug('Found Intel byte order');
            $d->setByteOrder(PelConvert::LITTLE_ENDIAN);
        } elseif ($d->strcmp(0, 'MM')) {
            Pel::debug('Found Motorola byte order');
            $d->setByteOrder(PelConvert::BIG_ENDIAN);
        } else {
            throw new PelInvalidDataException('Unknown byte order found in TIFF data: 0x%2X%2X', $d->getByte(0), $d->getByte(1));
        }

        /* Verify the TIFF header */
        if ($d->getShort(2) !== self::TIFF_HEADER) {
            throw new PelInvalidDataException('Missing TIFF magic value.');
        }
        /* IFD 0 offset */
        $offset = $d->getLong(4);
        Pel::debug('First IFD at offset %d.', $offset);

        if ($offset > 0) {
            /*
             * Parse the first IFD, this will automatically parse the
             * following IFDs and any sub IFDs.
             */
            $this->ifd = new PelIfd(PelIfd::IFD0);
            $this->ifd->load($d, $offset);
        }
    }

    /**
     * Load data from a file into a TIFF object.
     *
     * @param string $filename
     *            the filename. This must be a readable file.
     */
    public function loadFile(string $filename): void
    {
        $stream = new PelFileStream($filename);

        $this->load(new PelDataWindow($stream));
    }

    /**
     * Set the first IFD.
     *
     * @param PelIfd $ifd
     *            the new first IFD, which must be of type {@link PelIfd::IFD0}.
     */
    public function setIfd(PelIfd $ifd): void
    {
        if ($ifd->getType() !== PelIfd::IFD0) {
            throw new PelInvalidDataException('Invalid type of IFD: %d, expected %d.', $ifd->getType(), PelIfd::IFD0);
        }
        $this->ifd = $ifd;
    }

    /**
     * Return the first IFD.
     *
     * @return PelIfd|null the first IFD contained in the TIFF data, if any.
     *         If there is no IFD null will be returned.
     */
    public function getIfd(): ?PelIfd
    {
        return $this->ifd;
    }

    /**
     * Turn this object into bytes.
     *
     * TIFF images can have {@link PelConvert::LITTLE_ENDIAN
     * little-endian} or {@link PelConvert::BIG_ENDIAN big-endian} byte
     * order, and so this method takes an argument specifying that.
     *
     * @param bool $order
     *            the desired byte order of the TIFF data.
     *            This should be one of {@link PelConvert::LITTLE_ENDIAN} or {@link PelConvert::BIG_ENDIAN}.
     *
     * @return string the bytes representing this object.
     */
    public function getBytes(bool $order = PelConvert::LITTLE_ENDIAN): string
    {
        if ($order === PelConvert::LITTLE_ENDIAN) {
            $bytes = 'II';
        } else {
            $bytes = 'MM';
        }

        /* TIFF magic number --- fixed value. */
        $bytes .= PelConvert::shortToBytes(self::TIFF_HEADER, $order);

        if ($this->ifd !== null) {
            /*
             * IFD 0 offset. We will always start IDF 0 at an offset of 8
             * bytes (2 bytes for byte order, another 2 bytes for the TIFF
             * header, and 4 bytes for the IFD 0 offset make 8 bytes
             * together).
             */
            $bytes .= PelConvert::longToBytes(8, $order);

            /*
             * The argument specifies the offset of this IFD. The IFD will
             * use this to calculate offsets from the entries to their data,
             * all those offsets are absolute offsets counted from the
             * beginning of the data.
             */
            $bytes .= $this->ifd->getBytes(8, $order);
        } else {
            $bytes .= PelConvert::longToBytes(0, $order);
        }

        return $bytes;
    }

    /**
     * Save the TIFF object as a TIFF image in a file.
     *
     * @param string $filename
     *            the filename to save in. An existing file with the
     *            same name will be overwritten!
     *
     * @return int|FALSE The number of bytes that were written to the
     *         file, or FALSE on failure.
     */
    public function saveFile(string $filename): int|false
    {
        return file_put_contents($filename, $this->getBytes());
    }

    /**
     * Check if data is valid TIFF data.
     *
     * This will read just enough data from the data window to determine
     * if the data could be a valid TIFF data. This means that the
     * check is more like a heuristic than a rigorous check.
     *
     * @param PelDataWindow $d
     *            the bytes that will be examined.
     *
     * @return bool true if the data looks like valid TIFF data,
     *         false otherwise.
     *
     * @see PelJpeg::isValid()
     */
    public static function isValid(PelDataWindow $d): bool
    {
        /* First check that we have enough data. */
        if ($d->getSize() < 8) {
            return false;
        }

        /* Byte order */
        if ($d->strcmp(0, 'II')) {
            $d->setByteOrder(PelConvert::LITTLE_ENDIAN);
        } elseif ($d->strcmp(0, 'MM')) {
            Pel::debug('Found Motorola byte order');
            $d->setByteOrder(PelConvert::BIG_ENDIAN);
        } else {
            return false;
        }

        /* Verify the TIFF header */
        return $d->getShort(2) === self::TIFF_HEADER;
    }
}
