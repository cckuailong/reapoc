<?php

namespace GeminiLabs\Spatie\Color;

use GeminiLabs\Spatie\Color\Exceptions\InvalidColorValue;

class Validate
{
    /**
     * @param float $value
     * @param string $name
     */
    public static function CIELabValue($value, $name)
    {
        if ($name === 'l' && ($value < 0 || $value > 100)) {
            throw InvalidColorValue::CIELabValueNotInRange($value, $name, 0, 100);
        }
        if (($name === 'a' || $name === 'b') && ($value < -110 || $value > 110)) {
            throw InvalidColorValue::CIELabValueNotInRange($value, $name, -110, 110);
        }
    }

    /**
     * @param string $name
     */
    public static function CIELabColorString($string)
    {
        if (! preg_match('/^ *CIELab\( *\d{1,3}\.?\d+? *, *-?\d{1,3}\.?\d+? *, *-?\d{1,3}\.?\d+? *\) *$/i', $string)) {
            throw InvalidColorValue::malformedCIELabColorString($string);
        }
    }

    /**
     * @param int $value
     * @param string $channel
     */
    public static function rgbChannelValue($value, $channel)
    {
        if ($value < 0 || $value > 255) {
            throw InvalidColorValue::rgbChannelValueNotInRange($value, $channel);
        }
    }

    /**
     * @param float $value
     */
    public static function alphaChannelValue($value)
    {
        if ($value < 0 || $value > 1) {
            throw InvalidColorValue::alphaChannelValueNotInRange($value);
        }
    }

    /**
     * @param string $value
     */
    public static function hexChannelValue($value)
    {
        if (strlen($value) !== 2) {
            throw InvalidColorValue::hexChannelValueHasInvalidLength($value);
        }
        if (! preg_match('/[a-f0-9]{2}/i', $value)) {
            throw InvalidColorValue::hexValueContainsInvalidCharacters($value);
        }
    }

    /**
     * @param float $value
     * @param string $name
     */
    public static function hslValue($value, $name)
    {
        if ($value < 0 || $value > 100) {
            throw InvalidColorValue::hslValueNotInRange($value, $name);
        }
    }

    /**
     * @param string $string
     */
    public static function rgbColorString($string)
    {
        if (! preg_match('/^ *rgb\( *\d{1,3} *, *\d{1,3} *, *\d{1,3} *\) *$/i', $string)) {
            throw InvalidColorValue::malformedRgbColorString($string);
        }
    }

    /**
     * @param string $string
     */
    public static function rgbaColorString($string)
    {
        if (! preg_match('/^ *rgba\( *\d{1,3} *, *\d{1,3} *, *\d{1,3} *, *([0-1]|0?\.\d{1,2}) *\) *$/i', $string)) {
            throw InvalidColorValue::malformedRgbaColorString($string);
        }
    }

    /**
     * @param string $string
     */
    public static function hexColorString($string)
    {
        if (! preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $string)) {
            throw InvalidColorValue::malformedHexColorString($string);
        }
    }

    /**
     * @param string $string
     */
    public static function hslColorString($string)
    {
        if (! preg_match('/^ *hsl\( *-?\d{1,3} *, *\d{1,3}%? *, *\d{1,3}%? *\) *$/i', $string)) {
            throw InvalidColorValue::malformedHslColorString($string);
        }
    }

    /**
     * @param string $string
     */
    public static function hslaColorString($string)
    {
        if (! preg_match('/^ *hsla\( *\d{1,3} *, *\d{1,3}%? *, *\d{1,3}%? *, *[0-1](\.\d{1,2})? *\) *$/i', $string)) {
            throw InvalidColorValue::malformedHslaColorString($string);
        }
    }

    /**
     * @param float $value
     * @param string $name
     */
    public static function xyzValue($value, $name)
    {
        if ($name === 'x' && ($value < 0 || $value > 95.047)) {
            throw InvalidColorValue::xyzValueNotInRange($value, $name, 0, 95.047);
        }
        if ($name === 'y' && ($value < 0 || $value > 100)) {
            throw InvalidColorValue::xyzValueNotInRange($value, $name, 0, 100);
        }
        if ($name === 'z' && ($value < 0 || $value > 108.883)) {
            throw InvalidColorValue::xyzValueNotInRange($value, $name, 0, 108.883);
        }
    }

    /**
     * @param string $string
     */
    public static function xyzColorString($string)
    {
        if (! preg_match('/^ *xyz\( *\d{1,2}\.?\d+? *, *\d{1,3}\.?\d+? *, *\d{1,3}\.?\d+? *\) *$/i', $string)) {
            throw InvalidColorValue::malformedXyzColorString($string);
        }
    }
}
