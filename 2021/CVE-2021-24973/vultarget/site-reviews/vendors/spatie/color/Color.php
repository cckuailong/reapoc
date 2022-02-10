<?php

/**
 * - Added analysis utilities (isDark, isLight)
 * - Added manipulation utilities (darken, desaturate, grayscale, invert, lighten, mix, rotate, saturate)
 * - Added support for 3-digit hex colors
 * - Changed Color interface to abstract class
 * - Fixed Convert::xyzValueToRgb to round RGB values
 * - Fixed Rgba validation to allow alpha decimal values without a zero prefix
 * - Removed trailing zeros from rgba alpha value
 * 
 * @package spatie/color v1.3.0
 */

namespace GeminiLabs\Spatie\Color;

use ReflectionClass;

abstract class Color
{
    /**
     * @param Color $color
     * @return Color
     */
    public function fromColor(Color $color)
    {
        $method = 'to'.(new ReflectionClass($this))->getShortName();
        return call_user_func([$color, $method]);
    }

    /**
     * @param string $string
     * @return static
     */
    abstract public static function fromString($string);

    /**
     * @return int
     */
    abstract public function red();

    /**
     * @return int
     */
    abstract public function green();

    /**
     * @return int
     */
    abstract public function blue();

    /**
     * @return CIELab
     */
    abstract public function toCIELab();

    /**
     * @return Hex
     */
    abstract public function toHex();

    /**
     * @return Hsl
     */
    abstract public function toHsl();

    /**
     * @param float $alpha
     * @return Hsla
     */
    abstract public function toHsla($alpha = 1);

    /**
     * @return Rgb
     */
    abstract public function toRgb();

    /**
     * @param float $alpha
     * @return Rgba
     */
    abstract public function toRgba($alpha = 1);

    /**
     * @return Xyz
     */
    abstract public function toXyz();

    /**
     * @param int $amount
     * @return self
     */
    public function darken($amount = 10)
    {
        return $this->lighten(-$amount);
    }

    /**
     * @param int $amount
     * @return self
     */
    public function desaturate($amount = 10)
    {
        return $this->saturate(-$amount);
    }

    /**
     * @return self
     */
    public function grayscale()
    {
        return $this->desaturate(100);
    }

    /**
     * @return self
     */
    public function invert()
    {
        $rgba = $this->toRgba();
        $red = 255 - $rgba->red();
        $green = 255 - $rgba->green();
        $blue = 255 - $rgba->blue();
        $color = new Rgba($red, $green, $blue, $rgba->alpha());
        return $this->fromColor($color);
    }

    /**
     * @param int $amount
     * @return self
     */
    public function lighten($amount = 10)
    {
        $hsla = $this->toHsla();
        $lightness = max(0, min(100, $hsla->lightness() + $amount));
        $color = new Hsla(
            $hsla->hue(),
            $hsla->saturation(),
            $lightness,
            $hsla->alpha()
        );
        return $this->fromColor($color);
    }

    /**
     * @param string $withColor
     * @param float $ratio
     * @return self
     */
    public function mix($withColor, $ratio = 0)
    {
        $lab1 = $this->toCIELab();
        $lab2 = Factory::fromString($withColor)->toCIELab();
        $ratio = max(0, min(1, $ratio));
        $l = ($lab1->l() * (1 - $ratio) + $lab2->l() * $ratio);
        $a = ($lab1->a() * (1 - $ratio) + $lab2->a() * $ratio);
        $b = ($lab1->b() * (1 - $ratio) + $lab2->b() * $ratio);
        // CIE Lightness values less than 0% must be clamped to 0%.
        // Values greater than 100% are permitted for forwards compatibility with HDR.
        $l = max(0, min(400, $l));
        $color = new CIELab($l, $a, $b);
        return $this->fromColor($color);
    }

    /**
     * @param int $amount
     * @return self
     */
    public function rotate($amount = 180)
    {
        $hsla = $this->toHsla();
        $hue = round($hsla->hue() + $amount);
        $color = new Hsla(
            $hue,
            $hsla->saturation(),
            $hsla->lightness(),
            $hsla->alpha()
        );
        return $this->fromColor($color);
    }

    /**
     * @param int $amount
     * @return self
     */
    public function saturate($amount = 10)
    {
        $hsla = $this->toHsla();
        $saturation = max(0, min(100, $hsla->saturation() + $amount));
                $color = new Hsla(
            $hsla->hue(),
            $saturation,
            $hsla->lightness(),
            $hsla->alpha()
        );
        return $this->fromColor($color);
    }

    /**
     * @return bool
     */
    public function isDark()
    {
        return !$this->isLight();
    }

    /**
     * @return bool
     */
    public function isLight()
    {
        $rgb = $this->toRgb();
        return (($rgb->red() * 299 + $rgb->green() * 587 + $rgb->blue() * 114) / 1000 / 255) >= 0.5;
    }

    /**
     * @return string
     */
    abstract public function __toString();
}
