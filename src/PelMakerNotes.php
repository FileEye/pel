<?php

declare(strict_types=1);

namespace lsolesen\pel;

abstract class PelMakerNotes
{
    protected ?int $type = null;

    protected int $components;

    public function __construct(protected PelIfd $parent, protected PelDataWindow $data, protected int $size, protected int $offset)
    {
        $this->components = 0;
        Pel::debug('Creating MakerNotes with %d bytes at offset %d.', $this->size, $this->offset);
    }

    public static function createMakerNotesFromManufacturer(string $manufacturer, PelIfd $parent, PelDataWindow $data, int $size, int $offset): ?PelCanonMakerNotes
    {
        return match ($manufacturer) {
            'Canon' => new PelCanonMakerNotes($parent, $data, $size, $offset),
            default => null,
        };
    }

    abstract public function load(): void;
}
