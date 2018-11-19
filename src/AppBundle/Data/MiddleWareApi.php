<?php

namespace AppBundle\Data;

use AppBundle\Lib\Enum;

/**
 * Class MiddleWareApi
 *
 * Todos tienen /red/modo_red/
 */
class MiddleWareApi extends Enum
{
    // wallet_to/wallet_from/private_key/hash_to_save/comision
    const METHOD_SEND_TRANSACTION = 'https://ez-blockchain-middleware.herokuapp.com/%s/%s/sendtransaction/%s/%s/%s/%s/%i';
    // wallet_id
    const METHOD_GET_BALANCE = 'https://ez-blockchain-middleware.herokuapp.com/%s/%s/getbalance/%s';
    // wallet_id
    const METHOD_GET_WALLET_INFO = 'https://ez-blockchain-middleware.herokuapp.com/%s/%s/getwalletinfo/%s';
    // red
    const METHOD_GET_RATIOS = 'https://ez-blockchain-middleware.herokuapp.com/sendtransaction/%s';
}