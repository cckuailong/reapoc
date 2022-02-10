<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class BcEngine implements EngineInterface
{
    const HEX_CHARS = '0123456789abcdef';
    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function __construct()
    {
        bcscale(0);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function add($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcadd($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function cmp($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bccomp($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function div($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcdiv($a, $b);
    }

    /**
     * Finds inverse number $inv for $num by modulus $mod, such as:
     *     $inv * $num = 1 (mod $mod)
     *
     * @param string $num
     * @param string $mod
     * @return string
     * @access public
     */
    public function invertm($num, $mod)
    {
        $num = $this->input($num);
        $mod = $this->input($mod);

        $x = '1';
        $y = '0';
        $num1 = $mod;

        do {
            $tmp = bcmod($num, $num1);

            $q = bcdiv($num, $num1);

            $num = $num1;

            $num1 = $tmp;

            $tmp = bcsub($x, bcmul($y, $q));

            $x = $y;

            $y = $tmp;

        } while (bccomp($num1, '0'));

        if (bccomp($x, '0') < 0) {
            $x = bcadd($x, $mod);
        }

        if (substr($num, 0, 1) === '-') {
            $x = bcsub($mod, $x);
        }

        return $x;
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function mod($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        if (substr($a, 0, 1) === '-') {
            return bcadd(bcmod($a, $b), $b);
        }

        return bcmod($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function mul($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcmul($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function pow($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcpow($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function sub($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcsub($a, $b);
    }

    public function input($x)
    {
        if (empty($x)) {
            return '0';
        }
        $x = strtolower(trim($x));
        if (preg_match('/^(-?)0x([0-9a-f]+)$/', $x, $matches)) {
            $sign = $matches[1];
            $hex = $matches[2];

            for ($dec = '0', $i = 0; $i < strlen($hex); $i++) {
                $current = strpos('0123456789abcdef', $hex[$i]);
                $dec     = bcadd(bcmul($dec, 16), $current);
            }

            return $sign.$dec;

        } elseif (preg_match('/^-?[0-9]+$/', $x)) {
            return $x;
        } else {
            throw new \Exception("The input must be a numeric string in decimal or hexadecimal (with leading 0x) format.\n".var_export($x, true));
        }

    }

    /**
     * Function to determine if two numbers are
     * co-prime according to the Euclidean algo.
     *
     * @param  string $a First param to check.
     * @param  string $b Second param to check.
     * @return bool   Whether the params are cp.
     */
    public function coprime($a, $b)
    {
        $small = 0;
        $diff  = 0;
        while (bccomp($a, '0') > 0 && bccomp($b, '0') > 0) {
            if (bccomp($a, $b) == -1) {
                $small = $a;
                $diff  = bcmod($b, $a);
            }
            if (bccomp($a, $b) == 1) {
                $small = $b;
                $diff = bcmod($a, $b);
            }
            if (bccomp($a, $b) == 0) {
                $small = $a;
                $diff  = bcmod($b, $a);
            }
            $a = $small;
            $b = $diff;
        }
        if (bccomp($a, '1') == 0) {
            return true;
        }

        return false;
    }
}
