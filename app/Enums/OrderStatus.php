<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending   = 'in behandeling';
    case Approved  = 'goedgekeurd';
    case Rejected  = 'afgekeurd';
    case Delivered = 'geleverd';
}
