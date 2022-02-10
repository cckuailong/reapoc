<?php

namespace GeminiLabs\Spatie\Color;

class Hex extends Color
{
    /** @var string */
    protected $red;
    protected $green;
    protected $blue;

    /**
     * @param string $red
     * @param string $green
     * @param string $blue
     */
    public function __construct($red, $green, $blue)
    {
        Validate::hexChannelValue($red, 'red');
        Validate::hexChannelValue($green, 'green');
        Validate::hexChannelValue($blue, 'blue');
        $this->red = strtolower($red);
        $this->green = strtolower($green);
        $this->blue = strtolower($blue);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        Validate::hexColorString($string);
        $string = ltrim($string, '#');
        if (3 === strlen($string)) {
            list($red, $green, $blue) = str_split($string);
            $string = $red.$red.$green.$green.$blue.$blue;
        }
        list($red, $green, $blue) = str_split($string, 2);
        return new static($red, $green, $blue);
    }

    /**
     * {@inheritdoc}
     */
    public function red()
    {
        return $this->red;
    }

    /**
     * {@inheritdoc}
     */
    public function green()
    {
        return $this->green;
    }

    /**
     * {@inheritdoc}
     */
    public function blue()
    {
        return $this->blue;
    }

    /**
     * {@inheritdoc}
     */
    public function toCIELab()
    {
        return $this->toRgb()->toCIELab();
    }

    /**
     * {@inheritdoc}
     */
    public function toHex()
    {
        return new self($this->red, $this->green, $this->blue);
    }

    /**
     * {@inheritdoc}
     */
    public function toHsl()
    {
        list($hue, $saturation, $lightness) = Convert::rgbValueToHsl(
            Convert::hexChannelToRgbChannel($this->red),
            Convert::hexChannelToRgbChannel($this->green),
            Convert::hexChannelToRgbChannel($this->blue)
        );
        return new Hsl($hue, $saturation, $lightness);
    }

    /**
     * {@inheritdoc}
     */
    public function toHsla($alpha = 1)
    {
        list($hue, $saturation, $lightness) = Convert::rgbValueToHsl(
            Convert::hexChannelToRgbChannel($this->red),
            Convert::hexChannelToRgbChannel($this->green),
            Convert::hexChannelToRgbChannel($this->blue)
        );
        return new Hsla($hue, $saturation, $lightness, $alpha);
    }

    /**
     * {@inheritdoc}
     */
    public function toRgb()
    {
        return new Rgb(
            Convert::hexChannelToRgbChannel($this->red),
            Convert::hexChannelToRgbChannel($this->green),
            Convert::hexChannelToRgbChannel($this->blue)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toRgba($alpha = 1)
    {
        return $this->toRgb()->toRgba($alpha);
    }

    /**
     * {@inheritdoc}
     */
    public function toXyz()
    {
        return $this->toRgb()->toXyz();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "#{$this->red}{$this->green}{$this->blue}";
    }
}
