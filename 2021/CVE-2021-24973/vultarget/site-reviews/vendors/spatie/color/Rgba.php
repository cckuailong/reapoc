<?php

namespace GeminiLabs\Spatie\Color;

class Rgba extends Color
{
    /** @var int */
    protected $red;
    protected $green;
    protected $blue;

    /** @var float */
    protected $alpha;

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param float $alpha
     */
    public function __construct($red, $green, $blue, $alpha)
    {
        Validate::rgbChannelValue($red, 'red');
        Validate::rgbChannelValue($green, 'green');
        Validate::rgbChannelValue($blue, 'blue');
        Validate::alphaChannelValue($alpha);
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        Validate::rgbaColorString($string);
        $matches = null;
        preg_match('/rgba\( *(\d{1,3} *, *\d{1,3} *, *\d{1,3} *, *([0-1]|0?\.\d{1,2})) *\)/i', $string, $matches);
        $channels = explode(',', $matches[1]);
        list($red, $green, $blue, $alpha) = array_map('trim', $channels);
        return new static($red, $green, $blue, $alpha);
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
     * @return float
     */
    public function alpha()
    {
        return $this->alpha;
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
        return $this->toRgb()->toHex();
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
        return new Rgb($this->red, $this->green, $this->blue);
    }

    /**
     * {@inheritdoc}
     */
    public function toRgba($alpha = 1)
    {
        return new self($this->red, $this->green, $this->blue, $alpha);
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
        $alpha = number_format($this->alpha, 2);
        $alpha = preg_replace('/\.?0+$/', '', $alpha);
        return "rgba({$this->red},{$this->green},{$this->blue},{$alpha})";
    }
}
