<?php

namespace App\Enums;

enum MealType: string
{
    case AfternoonSnack = 'afternoon_snack';
    case Breakfast = 'breakfast';
    case Dinner = 'dinner';
    case Lunch = 'lunch';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
