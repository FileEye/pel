<?php

declare(strict_types=1);

namespace lsolesen\pel;

/**
 * Class for holding data of any kind.
 *
 * This class can hold bytes of undefined format.
 */
class PelEntryUndefined extends PelEntry
{
    /**
     * Make a new PelEntry that can hold undefined data.
     *
     * @param int $tag
     *            which this entry represents. This
     *            should be one of the constants defined in {@link PelTag},
     *            e.g., {@link PelTag::SCENE_TYPE}, {@link PelTag::MAKER_NOTE} or any other tag with format {@link PelFormat::UNDEFINED}.
     * @param string $data
     *            the data that this entry will be holding. Since
     *            the format is undefined, no checking will be done on the data. If no data are given, a empty string will be stored
     */
    public function __construct(int $tag, string $data = '')
    {
        $this->tag = $tag;
        $this->format = PelFormat::UNDEFINED;
        $this->setValue($data);
    }

    /**
     * Set the data of this undefined entry.
     *
     * @param string $data
     *            the data that this entry will be holding. Since
     *            the format is undefined, no checking will be done on the data.
     */
    public function setValue(mixed $data): void
    {
        $this->components = strlen($data);
        $this->bytes = $data;
    }

    /**
     * Get the data of this undefined entry.
     *
     * @return string the data that this entry is holding.
     */
    public function getValue(): string
    {
        return $this->bytes;
    }

    /**
     * Get the value of this entry as text.
     *
     * The value will be returned in a format suitable for presentation.
     *
     * @param bool $brief
     *            some values can be returned in a long or more
     *            brief form, and this parameter controls that.
     *
     * @return string the value as text.
     */
    public function getText(bool $brief = false): string
    {
        switch ($this->tag) {
            case PelTag::FILE_SOURCE:
                // CC (e->components, 1, v);
                return match (ord($this->bytes[0])) {
                    0x03 => 'DSC',
                    default => sprintf('0x%02X', ord($this->bytes[0])),
                };
            case PelTag::SCENE_TYPE:
                // CC (e->components, 1, v);
                return match (ord($this->bytes[0])) {
                    0x01 => 'Directly photographed',
                    default => sprintf('0x%02X', ord($this->bytes[0])),
                };
            case PelTag::COMPONENTS_CONFIGURATION:
                // CC (e->components, 4, v);
                $v = '';
                for ($i = 0; $i < 4; $i++) {
                    match (ord($this->bytes[$i])) {
                        0 => $v .= '-',
                        1 => $v .= 'Y',
                        2 => $v .= 'Cb',
                        3 => $v .= 'Cr',
                        4 => $v .= 'R',
                        5 => $v .= 'G',
                        6 => $v .= 'B',
                        default => $v .= 'reserved',
                    };
                    if ($i < 3) {
                        $v .= ' ';
                    }
                }
                return $v;
            case PelTag::MAKER_NOTE:
                // TODO: handle maker notes.
                return $this->components . ' bytes unknown MakerNote data';
            default:
                return '(undefined)';
        }
    }
}
