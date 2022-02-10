<?php

namespace GeminiLabs\Spatie\Color;

class Hsla extends Color
{
    /** @var float */
    protected $hue;
    protected $saturation;
    protected $lightness;
    protected $alpha;

     /**
     * @param float $hue
     * @param float $saturation
     * @param float $lightness
     * @param float $alpha
     */
   public function __construct($hue, $saturation, $lightness, $alpha = 1.0)
    {
        Validate::hslValue($saturation, 'saturation');
        Validate::hslValue($lightness, 'lightness');
        Validate::alphaChannelValue($alpha);
        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->lightness = $lightness;
        $this->alpha = $alpha;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        Validate::hslaColorString($string);
        $matches = null;
        preg_match('/hsla\( *(\d{1,3}) *, *(\d{1,3})%? *, *(\d{1,3})%? *, *([0-1](\.\d{1,2})?) *\)/i', $string, $matches);
        return new static($matches[1], $matches[2], $matches[3], $matches[4]);
    }

    /**
     * @return float
     */
    public function hue()
    {
        return $this->hue;
    }

    /**
     * @return float
     */
    public function saturation()
    {
        return $this->saturation;
    }

    /**
     * @return float
     */
    public function lightness()
    {
        return $this->lightness;
    }

    /**
     * {@inheritdoc}
     */
    public function red()
    {
        return Convert::hslValueToRgb($this->hue, $this->saturation, $this->lightness)[0];
    }

    /**
     * {@inheritdoc}
     */
    public function green()
    {
        return Convert::hslValueToRgb($this->hue, $this->saturation, $this->lightness)[1];
    }

    /**
     * {@inheritdoc}
     */
    public function blue()
    {
        return Convert::hslValueToRgb($this->hue, $this->saturation, $this->lightness)[2];
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
        return new Hex(
            Convert::rgbChannelToHexChannel($this->red()),
            Convert::rgbChannelToHexChannel($this->green()),
            Convert::rgbChannelToHexChannel($this->blue())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toHsla($alpha = 1)
    {
        return new self($this->hue(), $this->saturation(), $this->lightness(), $alpha);
    }

    /**
     * {@inheritdoc}
     */
    public function toHsl()
    {
        return new Hsl($this->hue(), $this->saturation(), $this->lightness());
    }

    /**
     * {@inheritdoc}
     */
    public function toRgb()
    {
        return new Rgb($this->red(), $this->green(), $this->blue());
    }

    /**
     * {@inheritdoc}
     */
    public function toRgba($alpha = 1)
    {
        return new Rgba($this->red(), $this->green(), $this->blue(), $alpha);
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
        $hue = round($this->hue);
        $saturation = round($this->saturation);
        $lightness = round($this->lightness);
        $alpha = round($this->alpha, 2);
        return "hsla({$hue},{$saturation}%,{$lightness}%,{$alpha})";
    }
}
