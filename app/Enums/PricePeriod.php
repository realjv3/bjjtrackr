<?php

namespace App\Enums;

enum PricePeriod: string
{
    case MONTH = 'month';
    case WEEK = 'week';
    case DAY = 'day';
}
