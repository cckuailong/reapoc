<?php

namespace GeminiLabs\Spatie\Color;

class Xyz extends Color
{
    /** @var float */
    protected $x;
    protected $y;
    protected $z;

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     */
    public function __construct($x, $y, $z)
    {
        Validate::xyzValue($x, 'x');
        Validate::xyzValue($y, 'y');
        Validate::xyzValue($z, 'z');
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        Validate::xyzColorString($string);
        $matches = null;
        preg_match('/xyz\( *(\d{1,2}\.?\d+? *, *\d{1,3}\.?\d+? *, *\d{1,3}\.?\d+?) *\)/i', $string, $matches);
        $channels = explode(',', $matches[1]);
        list($x, $y, $z) = array_map('trim', $channels);
        return new static($x, $y, $z);
    }

    /**
     * @return float
     */
    public function x()
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function y()
    {
        return $this->y;
    }

    /**
     * @return float
     */
    public function z()
    {
        return $this->z;
    }

    /**
     * {@inheritdoc}
     */
    public function red()
    {
        return $this->toRgb()->red();
    }

    /**
     * {@inheritdoc}
     */
    public function blue()
    {
        return $this->toRgb()->blue();
    }

    /**
     * {@inheritdoc}
     */
    public function green()
    {
        return $this->toRgb()->green();
    }

    /**
     * {@inheritdoc}
     */
    public function toCIELab()
    {
        list($l, $a, $b) = Convert::xyzValueToCIELab(
            $this->x,
            $this->y,
            $this->z
        );
        return new CIELab($l, $a, $b);
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
        return $this->toRgb()->toHSL();
    }

    /**
     * {@inheritdoc}
     */
    public function toHsla($alpha = 1)
    {
        return $this->toRgb()->toHsla($alpha);
    }

    /**
     * {@inheritdoc}
     */
    public function toRgb()
    {
        list($red, $green, $blue) = Convert::xyzValueToRgb(
            $this->x,
            $this->y,
            $this->z
        );
        return new Rgb($red, $green, $blue);
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
        return new self($this->x, $this->y, $this->z);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "xyz({$this->x},{$this->y},{$this->z})";
    }
}
