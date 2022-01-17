<?php


namespace App\Enum;


class UserRoles extends Enum
{
    const ADMIN = 'ROLE_ADMIN';
    const MANAGER = 'ROLE_MANAGER';
    const WORKER = 'ROLE_WORKER';
    const USER = 'ROLE_USER';
}