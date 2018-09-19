<?php

namespace AppBundle\Data;

use AppBundle\Lib\Enum;

/**
 * Class UserTypes
 */
class UserTypes extends Enum
{
    const FREE_USER = 'free';
    const BASIC_USER = 'basic';
    const PREMIUM_USER = 'premium';
    const ADMIN_USER = 'admin';
}