<?php

namespace AppBundle\Data;

use AppBundle\Lib\Enum;

/**
 * Class BcTxError
 */
class BcTxError extends Enum
{
    const USER_DISABLED = 100;
    const API_DISABLED = 200;
    const WALLET_DISABLED = 300;
    const WALLET_INSUFFICIENT_FUNDS = 310;
    const WALLET_CONFIG = 320;
    const TX_GENERIC_ERROR = 900;
}