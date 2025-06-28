<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Canceled = 'canceled';
    case Completed = 'completed';
    case Pending = 'pending';
    case Reprogrammed = 'reprogrammed';
    case Started = 'started';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Canceled => 'red',
            self::Completed => 'green',
            self::Pending => 'zinc',
            self::Reprogrammed => 'yellow',
            self::Started => 'blue',
        };
    }
}
