<?php

namespace App\Enums;

enum TicketPriorityEnum: string
{
    case BAIXA = 'baixa';
    case MEDIA = 'media';
    case ALTA = 'alta';
    case CRITICA = 'critica';
}