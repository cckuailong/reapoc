<?php

namespace RebelCode\Wpra\Core\Twig\Extensions\Date;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Translator for Twig dates.
 *
 * @since 4.17.6
 */
class TwigDateTranslator implements TranslatorInterface
{
    /**
     * The prefix of the $id parameter of the {@link transChoice} method.
     */
    const ID_PREFIX = "diff.";
    /**
     * The length of the {@link ID_PREFIX} constant.
     *
     * This is pre-calculated for performance reasons to avoid recalculating for every date that needs translation.
     */
    const ID_PREFIX_LEN = 5;

    /**
     * @inheritDoc
     *
     * @since 4.17.6
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $id . "TRANS";
    }

    /**
     * @inheritDoc
     *
     * @since 4.17.6
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        if (stripos($id, static::ID_PREFIX) !== 0) {
            return $id;
        }

        // Get the part after the prefix
        $suffix = substr($id, static::ID_PREFIX_LEN);

        // Get the parts
        $parts = explode('.', $suffix);
        if (count($parts) < 2) {
            return $id;
        }

        // The first part is the type ("ago" or "in")
        // The second part is the date unit
        $type = strtolower($parts[0]);
        $unit = strtolower($parts[1]);

        // Translate the unit
        switch ($unit) {
            case 'day':
                $unit = _n('day', 'days', $number, 'wprss');
                break;
            case 'hour':
                $unit = _n('hour', 'hours', $number, 'wprss');
                break;
            case 'minute':
                $unit = _n('minute', 'minutes', $number, 'wprss');
                break;
            case 'second':
                $unit = _n('second', 'seconds', $number, 'wprss');
                break;
            case 'month':
                $unit = _n('month', 'months', $number, 'wprss');
                break;
            case 'year':
                $unit = _n('year', 'years', $number, 'wprss');
                break;
            default:
                return $id;
        }

        // Prepare the format. These strings can and should be made available for translation. The tokens are expected
        // to still be present after translation.
        $format = ($type === 'ago')
            ? _nx('{number} {unit} ago', '{number} {unit} ago', $number, 'Format for past feed item dates, example: "5 hours ago"', 'wprss')
            : _nx('in {number} {unit}', 'in {number} {unit}', $number, 'Format for future feed item dates, example: "in 2 days"', 'wprss');

        // Replace tokens in the format and return the resulting translating and interpolated string
        return strtr($format, [
            '{number}' => $number,
            '{unit}' => $unit,
        ]);
    }

    /**
     * @inheritDoc
     *
     * @since 4.17.6
     */
    public function setLocale($locale)
    {
    }

    /**
     * @inheritDoc
     *
     * @since 4.17.6
     */
    public function getLocale()
    {
        return get_locale();
    }
}
