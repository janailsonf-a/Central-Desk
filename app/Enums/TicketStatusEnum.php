<?php

namespace App\Enums;

enum TicketStatusEnum: string
{
    case ABERTO = 'aberto';
    case AGUARDANDO_ATENDIMENTO = 'aguardando_atendimento';
    case EM_ANDAMENTO = 'em_andamento';
    case AGUARDANDO_USUARIO = 'aguardando_usuario';
    case RESOLVIDO = 'resolvido';
    case FECHADO = 'fechado';
    case CANCELADO = 'cancelado';
}