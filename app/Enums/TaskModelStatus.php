<?php

namespace App\Enums;

enum TaskModelStatus: string
{
    case Completed = 'completed';
    case Pending = 'pending';
    case Started = 'started';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Completed => 'green',
            self::Pending => 'zinc',
            self::Started => 'blue',
        };
    }
}
