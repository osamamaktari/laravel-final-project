<?php

namespace App\Enums;

enum TicketStatus: string
{
    case VALID = 'valid';
    case USED = 'used';
    case CANCELLED = 'cancelled';
}
