<?php

namespace AppBundle\Data;

use AppBundle\Lib\Enum;

class EzWallet extends Enum
{
    const ETHEREUM_TESTNET = '0xc095f4e5913dA8be66890b406C08BC13E3b2708D';
    const ETHEREUM_MAINNET = '0xAd840A8B6ae5D67330bfE9c792c9C7a4C7ba2D87';
    const RSK_TESTNET = '0x9e409f125e442427823089fd2626a9fe7a28f1c4';
    const RSK_MAINNET = '0xD2B8D0303970DA678A140b9AdbE18a903dF43563';
}