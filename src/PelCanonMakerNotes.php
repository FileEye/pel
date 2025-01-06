<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Namespace for functions operating on Exif formats.
 *
 * This class defines the constants that are to be used whenever one
 * has to refer to the format of an Exif tag. They will be
 * collectively denoted by the pseudo-type PelFormat throughout the
 * documentation.
 *
 * All the methods defined here are static, and they all operate on a
 * single argument which should be one of the class constants.
 */
class PelCanonMakerNotes extends PelMakerNotes
{
    /**
     * @var array<int, int>
     */
    private array $undefinedMakerNotesTags = [
        0x0000,
        0x0003,
        0x000a,
        0x000e,
        0x0011,
        0x0014,
        0x0016,
        0x0017,
        0x0018,
        0x0019,
        0x001b,
        0x001c,
        0x001d,
        0x001f,
        0x0020,
        0x0021,
        0x0022,
        0x0023,
        0x0024,
        0x0025,
        0x0031,
        0x0035,
        0x0098,
        0x009a,
        0x00b5,
        0x00c0,
        0x00c1,
        0x4008,
        0x4009,
        0x4010,
        0x4011,
        0x4012,
        0x4013,
        0x4015,
        0x4016,
        0x4018,
        0x4019,
        0x4020,
        0x4025,
        0x4027,
    ];

    /**
     * @var array<int, int>
     */
    private array $undefinedCameraSettingsTags = [
        0x0006,
        0x0008,
        0x0015,
        0x001e,
        0x001f,
        0x0026,
        0x002b,
        0x002c,
        0x002d,
        0x002f,
        0x0030,
        0x0031,
    ];

    /**
     * @var array<int, int>
     */
    private array $undefinedShotInfoTags = [
        0x0001,
        0x0006,
        0x000a,
        0x000b,
        0x000c,
        0x000d,
        0x0011,
        0x0012,
        0x0014,
        0x0018,
        0x0019,
        0x001d,
        0x001e,
        0x001f,
        0x0020,
        0x0021,
        0x0022,
    ];

    /**
     * @var array<int, int>
     */
    private array $undefinedPanoramaTags = [
        0x0001,
        0x0003,
        0x0004,
    ];

    /**
     * @var array<int, int>
     */
    private array $undefinedFileInfoTags = [
        0x0002,
        0x000a,
        0x000b,
        0x0011,
        0x0012,
        0x0016,
        0x0017,
        0x0018,
        0x001a,
        0x001b,
        0x001c,
        0x001d,
        0x001e,
        0x001f,
        0x0020,
    ];

    public function __construct(PelIfd $parent, PelDataWindow $data, int $size, int $offset)
    {
        parent::__construct($parent, $data, $size, $offset);
        $this->type = PelIfd::CANON_MAKER_NOTES;
    }

    public function load(): void
    {
        $this->components = $this->data->getShort($this->offset);
        $this->offset += 2;
        Pel::debug('Loading %d components in maker notes.', $this->components);
        $mkNotesIfd = new PelIfd(PelIfd::CANON_MAKER_NOTES);

        for ($i = 0; $i < $this->components; $i++) {
            $tag = $this->data->getShort($this->offset + 12 * $i);
            $components = $this->data->getLong($this->offset + 12 * $i + 4);
            $data = $this->data->getLong($this->offset + 12 * $i + 8);
            // check if tag is defined
            if (in_array($tag, $this->undefinedMakerNotesTags)) {
                continue;
            }
            match ($tag) {
                PelTag::CANON_CAMERA_SETTINGS => $this->parseCameraSettings($mkNotesIfd, $this->data, $data, $components),
                PelTag::CANON_SHOT_INFO => $this->parseShotInfo($mkNotesIfd, $this->data, $data, $components),
                PelTag::CANON_PANORAMA => $this->parsePanorama($mkNotesIfd, $this->data, $data, $components),
                PelTag::CANON_FILE_INFO => $this->parseFileInfo($mkNotesIfd, $this->data, $data, $components),
                default => $mkNotesIfd->loadSingleValue($this->data, $this->offset, $i, $tag),
            };
        }
        $this->parent->addSubIfd($mkNotesIfd);
    }

    private function parseCameraSettings(PelIfd $parent, PelDataWindow $data, int $offset, int $components): void
    {
        $type = PelIfd::CANON_CAMERA_SETTINGS;
        Pel::debug('Found Canon Camera Settings sub IFD at offset %d', $offset);
        $size = $data->getShort($offset);
        $offset += 2;
        $elemSize = PelFormat::getSize(PelFormat::SSHORT);

        if ($components === 0) {
            throw new PelMakerNotesMalformedException('Invalid Canon Camera Settings - no components found.');
        }

        if ($size / $components !== $elemSize) {
            throw new PelMakerNotesMalformedException('Size of Canon Camera Settings does not match the number of entries.');
        }

        $camIfd = new PelIfd($type);

        for ($i = 0; $i < $components; $i++) {
            // check if tag is defined
            if (in_array($i + 1, $this->undefinedCameraSettingsTags)) {
                continue;
            }
            $camIfd->loadSingleMakerNotesValue($type, $data, $offset, $size, $i, PelFormat::SSHORT);
        }
        $parent->addSubIfd($camIfd);
    }

    private function parseShotInfo(PelIfd $parent, PelDataWindow $data, int $offset, int $components): void
    {
        $type = PelIfd::CANON_SHOT_INFO;
        Pel::debug('Found Canon Shot Info sub IFD at offset %d', $offset);
        $size = $data->getShort($offset);
        $offset += 2;
        $elemSize = PelFormat::getSize(PelFormat::SHORT);
        if ($size / $components !== $elemSize) {
            throw new PelMakerNotesMalformedException('Size of Canon Shot Info does not match the number of entries.');
        }
        $shotIfd = new PelIfd($type);

        for ($i = 0; $i < $components; $i++) {
            // check if tag is defined
            if (in_array($i + 1, $this->undefinedShotInfoTags)) {
                continue;
            }
            $shotIfd->loadSingleMakerNotesValue($type, $data, $offset, $size, $i, PelFormat::SHORT);
        }
        $parent->addSubIfd($shotIfd);
    }

    private function parsePanorama(PelIfd $parent, PelDataWindow $data, int $offset, int $components): void
    {
        $type = PelIfd::CANON_PANORAMA;
        Pel::debug('Found Canon Panorama sub IFD at offset %d', $offset);
        $size = $data->getShort($offset);
        $offset += 2;
        $elemSize = PelFormat::getSize(PelFormat::SHORT);
        if ($size / $components !== $elemSize) {
            throw new PelMakerNotesMalformedException('Size of Canon Panorama does not match the number of entries.');
        }
        $panoramaIfd = new PelIfd($type);

        for ($i = 0; $i < $components; $i++) {
            // check if tag is defined
            if (in_array($i + 1, $this->undefinedPanoramaTags)) {
                continue;
            }
            $panoramaIfd->loadSingleMakerNotesValue($type, $data, $offset, $size, $i, PelFormat::SHORT);
        }
        $parent->addSubIfd($panoramaIfd);
    }

    private function parseFileInfo(PelIfd $parent, PelDataWindow $data, int $offset, int $components): void
    {
        $type = PelIfd::CANON_FILE_INFO;
        Pel::debug('Found Canon File Info sub IFD at offset %d', $offset);
        $size = $data->getShort($offset);
        $offset += 2;
        $elemSize = PelFormat::getSize(PelFormat::SSHORT);
        if ($size === $elemSize * ($components - 1) + PelFormat::getSize(PelFormat::LONG)) {
            throw new PelMakerNotesMalformedException('Size of Canon File Info does not match the number of entries.');
        }
        $fileIfd = new PelIfd($type);

        for ($i = 0; $i < $components; $i++) {
            // check if tag is defined
            if (in_array($i + 1, $this->undefinedFileInfoTags)) {
                continue;
            }
            $format = PelFormat::SSHORT;
            if ($i + 1 === PelTag::CANON_FI_FILE_NUMBER) {
                $format = PelFormat::LONG;
            }
            $fileIfd->loadSingleMakerNotesValue($type, $data, $offset, $size, $i, $format);
        }
        $parent->addSubIfd($fileIfd);
    }
}
