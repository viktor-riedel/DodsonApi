<?php

namespace App\Enums;

enum CarStatusEnum: string
{
    case VIRTUAL = 'virtual';
    case IN_WORK = 'in work';
    case DISMANTLING = 'dismantling';
    case DISMANTLED = 'dismantled';
    case CAR_FOR_PARTS = 'car for parts';
    case DONE = 'done';

    public function status(): string
    {
        return match ($this) {
            static::VIRTUAL => 'virtual',
            static::IN_WORK => 'in work',
            static::DISMANTLING => 'dismantling',
            static::DISMANTLED => 'dismantled',
            static::CAR_FOR_PARTS => 'car for parts',
            static::DONE => 'done',
        };
    }
}
