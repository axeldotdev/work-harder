<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum Day: string
{
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';
    case Saturday = 'saturday';
    case Sunday = 'sunday';

    public static function all(): Collection
    {
        return collect([
            self::Monday,
            self::Tuesday,
            self::Wednesday,
            self::Thursday,
            self::Friday,
            self::Saturday,
            self::Sunday,
        ])->sort()->values();
    }

    public static function weekdays(): Collection
    {
        return collect([
            self::Monday,
            self::Tuesday,
            self::Wednesday,
            self::Thursday,
            self::Friday,
        ])->sort()->values();
    }

    public static function weekend(): Collection
    {
        return collect([
            self::Saturday,
            self::Sunday,
        ])->sort()->values();
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function shortLabel(): string
    {
        return mb_substr($this->label(), 0, 1);
    }
}
