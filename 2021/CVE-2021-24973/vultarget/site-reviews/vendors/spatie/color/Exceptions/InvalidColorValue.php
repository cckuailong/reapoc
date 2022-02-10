<?php

namespace GeminiLabs\Spatie\Color\Exceptions;

use Exception;

class InvalidColorValue extends Exception
{
    /**
     * @param int $value
     * @return self
     */
    public static function alphaChannelValueNotInRange($value)
    {
        return new static("An alpha values must be a float between 0 and 1, `{$value}` provided.");
    }

    /**
     * @param float $value
     * @param string $name
     * @param float $min
     * @param float $max
     * @return self
     */
    public static function CIELabValueNotInRange($value, $name, $min, $max)
    {
        return new static("CIELab value `{$name}` must be a number between $min and $max");
    }

    /**
     * @param string $value
     * @return self
     */
    public static function hexChannelValueHasInvalidLength($value)
    {
        $length = strlen($value);
        return new static("Hex values must contain exactly 2 characters, `{$value}` contains {$length} characters.");
    }

    /**
     * @param string $value
     * @return self
     */
    public static function hexValueContainsInvalidCharacters($value)
    {
        return new static("Hex values can only contain numbers or letters from A-F, `{$value}` contains invalid characters.");
    }

    /**
     * @param float $value
     * @param string $name
     * @return self
     */
    public static function hslValueNotInRange($value, $name)
    {
        return new static("Hsl value `{$name}` must be a number between 0 and 100");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedCIELabColorString($string)
    {
        return new static("CIELab color string `{$string}` is malformed. A CIELab color contains 3 comma separated values, wrapped in `CIELab()`, e.g. `CIELab(62.91,5.34,-57.73)`.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedColorString($string)
    {
        return new static("Color string `{$string}` doesn't match any of the available colors.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedHexColorString($string)
    {
        return new static("Hex color string `{$string}` is malformed. A hex color string starts with a `#` and contains exactly six characters, e.g. `#aabbcc`.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedHslColorString($string)
    {
        return new static("Hsl color string `{$string}` is malformed. An hsl color contains hue, saturation, and lightness values, wrapped in `hsl()`, e.g. `hsl(300,10%,50%)`.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedHslaColorString($string)
    {
        return new static("Hsla color string `{$string}` is malformed. An hsla color contains hue, saturation, lightness and alpha values, wrapped in `hsl()`, e.g. `hsl(300,10%,50%,0.25)`.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedRgbColorString($string)
    {
        return new static("Rgb color string `{$string}` is malformed. An rgb color contains 3 comma separated values between 0 and 255, wrapped in `rgb()`, e.g. `rgb(0,0,255)`.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedRgbaColorString($string)
    {
        return new static("Rgba color string `{$string}` is malformed. An rgba color contains 3 comma separated values between 0 and 255 with an alpha value between 0 and 1, wrapped in `rgba()`, e.g. `rgb(0,0,255,0.5)`.");
    }

    /**
     * @param string $string
     * @return self
     */
    public static function malformedXyzColorString($string)
    {
        return new static("Xyz color string `{$string}` is malformed. An xyz color contains 3 comma separated values, wrapped in `xyz()`, e.g. `xyz(31.3469,31.4749,99.0308)`.");
    }

    /**
     * @param int $value
     * @param string $channel
     * @return self
     */
    public static function rgbChannelValueNotInRange($value, $channel)
    {
        return new static("An rgb values must be an integer between 0 and 255, `{$value}` provided for channel {$channel}.");
    }

    /**
     * @param float $value
     * @param string $name
     * @param float $min
     * @param float $max
     * @return self
     */
    public static function xyzValueNotInRange($value, $name, $min, $max)
    {
        return new static("Xyz value `{$name}` must be a number between $min and $max");
    }
}
