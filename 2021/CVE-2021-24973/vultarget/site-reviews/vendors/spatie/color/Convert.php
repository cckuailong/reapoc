<?php

namespace GeminiLabs\Spatie\Color;

class Convert
{
    /**
     * @param float $l
     * @param float $a
     * @param float $b
     * @return array
     */
    public static function CIELabValueToXyz($l, $a, $b)
    {
        $y = ($l + 16) / 116;
        $x = $a / 500 + $y;
        $z = $y - $b / 200;
        if (pow($y, 3) > 0.008856) {
            $y = pow($y, 3);
        } else {
            $y = ($y - 16 / 116) / 7.787;
        }
        if (pow($x, 3) > 0.008856) {
            $x = pow($x, 3);
        } else {
            $x = ($x - 16 / 116) / 7.787;
        }
        if (pow($z, 3) > 0.008856) {
            $z = pow($z, 3);
        } else {
            $z = ($z - 16 / 116) / 7.787;
        }
        $x = round(95.047 * $x, 4);
        $y = round(100.000 * $y, 4);
        $z = round(108.883 * $z, 4);
        if ($x > 95.047) {
            $x = 95.047;
        }
        if ($y > 100) {
            $y = 100;
        }
        if ($z > 108.883) {
            $z = 108.883;
        }
        return [$x, $y, $z];
    }

    /**
     * @param string $hexValue
     * @return int
     */
    public static function hexChannelToRgbChannel($hexValue)
    {
        return hexdec($hexValue);
    }

    /**
     * @param int $rgbValue
     * @return string
     */
    public static function rgbChannelToHexChannel($rgbValue)
    {
        return str_pad(dechex($rgbValue), 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param float $hue
     * @param float $saturation
     * @param float $lightness
     * @return array
     */
    public static function hslValueToRgb($hue, $saturation, $lightness)
    {
        $h = (360 + ($hue % 360)) % 360;  // hue values can be less than 0 and greater than 360. This normalises them into the range 0-360.
        $c = (1 - abs(2 * ($lightness / 100) - 1)) * ($saturation / 100);
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = ($lightness / 100) - ($c / 2);
        if ($h >= 0 && $h <= 60) {
            return [round(($c + $m) * 255), round(($x + $m) * 255), round($m * 255)];
        }
        if ($h > 60 && $h <= 120) {
            return [round(($x + $m) * 255), round(($c + $m) * 255), round($m * 255)];
        }
        if ($h > 120 && $h <= 180) {
            return [round($m * 255), round(($c + $m) * 255), round(($x + $m) * 255)];
        }
        if ($h > 180 && $h <= 240) {
            return [round($m * 255), round(($x + $m) * 255), round(($c + $m) * 255)];
        }
        if ($h > 240 && $h <= 300) {
            return [round(($x + $m) * 255), round($m * 255), round(($c + $m) * 255)];
        }
        if ($h > 300 && $h <= 360) {
            return [round(($c + $m) * 255), round($m * 255), round(($x + $m) * 255)];
        }
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return array
     */
    public static function rgbValueToHsl($red, $green, $blue)
    {
        $r = $red / 255;
        $g = $green / 255;
        $b = $blue / 255;
        $cmax = max($r, $g, $b);
        $cmin = min($r, $g, $b);
        $delta = $cmax - $cmin;
        $hue = 0;
        if (0 != $delta) {
            if ($r === $cmax) {
                $hue = 60 * fmod(($g - $b) / $delta, 6);
            }
            if ($g === $cmax) {
                $hue = 60 * ((($b - $r) / $delta) + 2);
            }
            if ($b === $cmax) {
                $hue = 60 * ((($r - $g) / $delta) + 4);
            }
        }
        $lightness = ($cmax + $cmin) / 2;
        $saturation = 0;
        if ($lightness > 0 && $lightness < 1) {
            $saturation = $delta / (1 - abs((2 * $lightness) - 1));
        }
        return [$hue, min($saturation, 1) * 100, min($lightness, 1) * 100];
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return array
     */
    public static function rgbValueToXyz($red, $green, $blue)
    {
        $red = $red / 255;
        $green = $green / 255;
        $blue = $blue / 255;
        if ($red > 0.04045) {
            $red = pow((($red + 0.055) / 1.055), 2.4);
        } else {
            $red = $red / 12.92;
        }
        if ($green > 0.04045) {
            $green = pow((($green + 0.055) / 1.055), 2.4);
        } else {
            $green = $green / 12.92;
        }
        if ($blue > 0.04045) {
            $blue = pow((($blue + 0.055) / 1.055), 2.4);
        } else {
            $blue = $blue / 12.92;
        }
        $red = $red * 100;
        $green = $green * 100;
        $blue = $blue * 100;
        $x = round($red * 0.4124 + $green * 0.3576 + $blue * 0.1805, 4);
        $y = round($red * 0.2126 + $green * 0.7152 + $blue * 0.0722, 4);
        $z = round($red * 0.0193 + $green * 0.1192 + $blue * 0.9505, 4);
        if ($x > 95.047) {
            $x = 95.047;
        }
        if ($y > 100) {
            $y = 100;
        }
        if ($z > 108.883) {
            $z = 108.883;
        }
        return [$x, $y, $z];
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     * @return array
     */
    public static function xyzValueToCIELab($x, $y, $z)
    {
        $x = $x / 95.047;
        $y = $y / 100.000;
        $z = $z / 108.883;
        if ($x > 0.008856) {
            $x = pow($x, 1 / 3);
        } else {
            $x = (7.787 * $x) + (16 / 116);
        }
        if ($y > 0.008856) {
            $y = pow($y, 1 / 3);
        } else {
            $y = (7.787 * $y) + (16 / 116);
        }
        if ($y > 0.008856) {
            $l = (116 * $y) - 16;
        } else {
            $l = 903.3 * $y;
        }
        if ($z > 0.008856) {
            $z = pow($z, 1 / 3);
        } else {
            $z = (7.787 * $z) + (16 / 116);
        }
        $l = round($l, 2);
        $a = round(500 * ($x - $y), 2);
        $b = round(200 * ($y - $z), 2);
        return [$l, $a, $b];
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     * @return array
     */
    public static function xyzValueToRgb($x, $y, $z)
    {
        $x = $x / 100;
        $y = $y / 100;
        $z = $z / 100;
        $r = $x * 3.2406 + $y * -1.5372 + $z * -0.4986;
        $g = $x * -0.9689 + $y * 1.8758 + $z * 0.0415;
        $b = $x * 0.0557 + $y * -0.2040 + $z * 1.0570;
        if ($r > 0.0031308) {
            $r = 1.055 * pow($r, (1 / 2.4)) - 0.055;
        } else {
            $r = 12.92 * $r;
        }
        if ($g > 0.0031308) {
            $g = 1.055 * pow($g, (1 / 2.4)) - 0.055;
        } else {
            $g = 12.92 * $g;
        }
        if ($b > 0.0031308) {
            $b = 1.055 * pow($b, (1 / 2.4)) - 0.055;
        } else {
            $b = 12.92 * $b;
        }
        $r = round(max(0, min(255, $r * 255)));
        $g = round(max(0, min(255, $g * 255)));
        $b = round(max(0, min(255, $b * 255)));
        return [$r, $g, $b];
    }
}
