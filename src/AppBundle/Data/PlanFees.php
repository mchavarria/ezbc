<?php

namespace AppBundle\Data;

use AppBundle\Lib\Enum;

/**
 * Class PlanFees
 */
class PlanFees extends Enum
{
    const BASIC_USER = 0.5;
    const PREMIUM_USER = 0.43;
    const EXCLUSIVE_USER = 0.4;
}