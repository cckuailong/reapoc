<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

/**
 * Elliptic curve parameters for secp256k1, see:
 * http://www.secg.org/collateral/sec2_final.pdf
 * also:
 *
 * @see https://en.bitcoin.it/wiki/Secp256k1
 *
 * @package Bitcore
 */
class Secp256k1 implements CurveParameterInterface
{
    const A = '00';
    const B = '07';
    const G = '0479BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';
    const H = '01';
    const N = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
    const P = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';

    public function aHex()
    {
        return '0x' . strtolower(self::A);
    }

    public function bHex()
    {
        return '0x' . strtolower(self::B);
    }

    public function gHex()
    {
        return '0x' . strtolower(self::G);
    }

    public function gxHex()
    {
        return '0x' . substr(strtolower(self::G), 0, 64);
    }

    public function gyHex()
    {
        return '0x' . substr(strtolower(self::G), 66, 64);
    }

    public function hHex()
    {
        return '0x' . strtolower(self::H);
    }

    public function nHex()
    {
        return '0x' . strtolower(self::N);
    }

    public function pHex()
    {
        return '0x' . strtolower(self::P);
    }
}
