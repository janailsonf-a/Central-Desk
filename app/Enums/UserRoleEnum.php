<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case GESTOR = 'gestor';
    case TECNICO = 'tecnico';
    case SOLICITANTE = 'solicitante';
}