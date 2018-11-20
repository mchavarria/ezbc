<?php

namespace AppBundle\Data;

use AppBundle\Lib\Enum;

/**
 * Class PlanFees
 */
class PlanFees extends Enum
{
    const FREE = 0;
    const BASIC = 0.5;
    const PREMIUM = 0.43;
    const EXCLUSIVE = 0.4;
    const ADMIN = 0;
}