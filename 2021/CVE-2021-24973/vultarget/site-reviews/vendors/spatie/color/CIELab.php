<?php

namespace GeminiLabs\Spatie\Color;

class CIELab extends Color
{
    /** @var float */
    protected $l;
    protected $a;
    protected $b;

    /**
     * @param float $l
     * @param float $a
     * @param float $b
     */
    public function __construct($l, $a, $b)
    {
        Validate::CIELabValue($l, 'l');
        Validate::CIELabValue($a, 'a');
        Validate::CIELabValue($b, 'b');
        $this->l = $l;
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        Validate::CIELabColorString($string);
        $matches = null;
        preg_match('/CIELab\( *(\d{1,3}\.?\d+? *, *-?\d{1,3}\.?\d+? *, *-?\d{1,3}\.?\d+?) *\)/i', $string, $matches);
        $channels = explode(',', $matches[1]);
        list($l, $a, $b) = array_map('trim', $channels);
        return new static($l, $a, $b);
    }

    /**
     * @return float
     */
    public function l()
    {
        return $this->l;
    }

    /**
     * @return float
     */
    public function a()
    {
        return $this->a;
    }

    /**
     * @return float
     */
    public function b()
    {
        return $this->b;
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
        return new self($this->l, $this->a, $this->b);
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
        return $this->toXyz()->toRgb();
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
        list($x, $y, $z) = Convert::CIELabValueToXyz(
            $this->l,
            $this->a,
            $this->b
        );
        return new Xyz($x, $y, $z);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "CIELab({$this->l},{$this->a},{$this->b})";
    }
}
