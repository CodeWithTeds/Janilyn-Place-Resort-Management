<?php

namespace App\Enums;

enum UnitCleaningStatus: string
{
    case CLEAN = 'clean';
    case DIRTY = 'dirty';
    case CLEANING = 'cleaning';
    case INSPECTION_READY = 'inspection_ready';
    case DO_NOT_DISTURB = 'do_not_disturb';

    public function label(): string
    {
        return match ($this) {
            self::CLEAN => 'Clean',
            self::DIRTY => 'Dirty',
            self::CLEANING => 'Cleaning',
            self::INSPECTION_READY => 'Inspection Ready',
            self::DO_NOT_DISTURB => 'Do Not Disturb',
        };
    }
}
