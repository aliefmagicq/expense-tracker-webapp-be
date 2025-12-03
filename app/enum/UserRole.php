<?php

namespace App\enum;

enum UserRole: string
{
    case user = 'user';
    case supervisor = 'supervisor';
}
