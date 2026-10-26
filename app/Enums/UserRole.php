<?php

namespace App\Enums;

enum UserRole: string
{
    case Participant = 'participant';
    case Admin = 'admin';
}
