<?php

namespace GeminiLabs\Spatie\Color;

use GeminiLabs\Spatie\Color\Exceptions\InvalidColorValue;

class Factory
{
    /**
     * @param string $string
     * @return Color
     */
    public static function fromString($string)
    {
        $colorClasses = static::getColorClasses();
        foreach ($colorClasses as $colorClass) {
            try {
                return $colorClass::fromString($string);
            } catch (InvalidColorValue $e) {
                // Catch the exception but never throw it.
            }
        }
        throw InvalidColorValue::malformedColorString($string);
    }

    /**
     * @return array
     */
    protected static function getColorClasses()
    {
        return [
            CIELab::class,
            Hex::class,
            Hsl::class,
            Hsla::class,
            Rgb::class,
            Rgba::class,
            Xyz::class,
        ];
    }
}
