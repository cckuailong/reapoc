<?php

namespace GeminiLabs\Spatie\Color;

class Rgb extends Color
{
    /** @var int */
    protected $red;
    protected $green;
    protected $blue;

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function __construct($red, $green, $blue)
    {
        Validate::rgbChannelValue($red, 'red');
        Validate::rgbChannelValue($green, 'green');
        Validate::rgbChannelValue($blue, 'blue');
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        Validate::rgbColorString($string);
        $matches = null;
        preg_match('/rgb\( *(\d{1,3} *, *\d{1,3} *, *\d{1,3}) *\)/i', $string, $matches);
        $channels = explode(',', $matches[1]);
        list($red, $green, $blue) = array_map('trim', $channels);
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
        return $this->toXyz()->toCIELab();
    }

    /**
     * {@inheritdoc}
     */
    public function toHex()
    {
        return new Hex(
            Convert::rgbChannelToHexChannel($this->red),
            Convert::rgbChannelToHexChannel($this->green),
            Convert::rgbChannelToHexChannel($this->blue)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toHsl()
    {
        list($hue, $saturation, $lightness) = Convert::rgbValueToHsl(
            $this->red,
            $this->green,
            $this->blue
        );
        return new Hsl($hue, $saturation, $lightness);
    }

    /**
     * {@inheritdoc}
     */
    public function toHsla($alpha = 1)
    {
        list($hue, $saturation, $lightness) = Convert::rgbValueToHsl(
            $this->red,
            $this->green,
            $this->blue
        );
        return new Hsla($hue, $saturation, $lightness, $alpha);
    }

    /**
     * {@inheritdoc}
     */
    public function toRgb()
    {
        return new self($this->red, $this->green, $this->blue);
    }

    /**
     * {@inheritdoc}
     */
    public function toRgba($alpha = 1)
    {
        return new Rgba($this->red, $this->green, $this->blue, $alpha);
    }

    /**
     * {@inheritdoc}
     */
    public function toXyz()
    {
        list($x, $y, $z) = Convert::rgbValueToXyz(
            $this->red,
            $this->green,
            $this->blue
        );
        return new Xyz($x, $y, $z);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "rgb({$this->red},{$this->green},{$this->blue})";
    }
}
