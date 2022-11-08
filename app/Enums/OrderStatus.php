<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Ongoing = 'ongoing';
    case Payment = 'payment';
    case Complete = 'complete';
}
