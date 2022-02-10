<?php

namespace Aventura\Wprss\Core\Block\Html;
use Aventura\Wprss\Core\Block;

/**
 * Something that generates HTML output.
 *
 * @since 4.9
 */
abstract class AbstractHtml extends Block\AbstractBlock
{
    const QUOTE_SINGLE = "'";
    const QUOTE_DOUBLE = '"';

    /**
     * Get the quote that this class will use to surround attribute values.
     *
     * @since 4.9
     * @return string The string that will surround an HTML attribute value.
     */
    public static function getAttributeQuote()
    {
        return static::QUOTE_DOUBLE;
    }

    /**
     * Generate an attribute-value pair string.
     *
     * This string is intended to be part of an HTML tag.
     *
     * @since 4.9
     * @param string $name The attribute name.
     * @param string $value The attribute value.
     * @param null|string $surround The quote symbol to use to surround the
     *  attribute value.
     * @return string A string that represents an HTML tag's attribute-value
     *  pair.
     */
    public static function getAttributePairString($name, $value = '', $surround = null)
    {
        $surround = static::defaultParam($surround, static::getAttributeQuote());
        return sprintf('%2$s=%1$s%3$s%1$s', $surround, $name, $value);
    }

    /**
     * Generate a string of attribute-value pairs.
     *
     * Intended to be used in an HTML tag.
     *
     * @since 4.9
     * @see getAttributeQuote()
     * @see getAttributeStringsArray()
     * @param array $attributes An array of attributes, where key is attribute
     *  name, and value is attribute value.
     * @param string|bool $escape If array, the values of only those attributes,
     *  the names of which are listed here, will be escaped.
     *  Othwewise, whether or not all attribute values will be escaped.
     * @param string $surround The quote to use for surrounding the attribute
     *  value.
     *  One of the QUOTE_* constants.
     *  Default: What this class's getAttributeQuote() returns.
     * @return string A string that consists of attribute-value pairs with
     *  spaces in between.
     */
    public static function getAttributesStringFromArray($attributes = array(), $escape = true, $surround = null)
    {
        $surround = static::defaultParam($surround, static::getAttributeQuote());
        return implode(' ', static::getAttributeStringsArray($attributes, $escape, $surround));
    }

    /**
     * Generates an array of attribute-value pair strings, by attribute name.
     *
     * @since 4.9
     * @see getAttributeQuote()
     * @see getAttributePairString()
     * @see sanitizeAttributeName()
     * @param array $attributes An array of attributes, where key is attribute
     *  name, and value is attribute value.
     * @param string|bool $escape If array, the values of only those attributes,
     *  the names of which are listed here, will be escaped.
     *  Othwewise, whether or not all attribute values will be escaped.
     * @param string $surround The quote to use for surrounding the attribute
     *  value.
     *  One of the QUOTE_* constants.
     *  Default: What this class's getAttributeQuote() returns.
     * @return array An array, where keys are attribute names, and values are
     *  attribute-value pair strings, e.g. 'name="value"';
     */
    public static function getAttributeStringsArray($attributes = array(), $escape = true, $surround = null)
    {
        $surround = static::defaultParam($surround, static::getAttributeQuote());

        // Allow a single attribute
        if (is_string($escape)) {
            $escape = (array) $escape;
        }

        $attrStrings = array();
        foreach( $attributes as $_key => $_value ) {
            $key = $_key;

            // Should we standardize?
            $isEscape = is_array($escape)
                    ? in_array($_key, $escape) // Array with keys to standardize
                    : (bool) $escape; // Same for all

            // Cleaning attribute name and value
            if ($isEscape) {
                $key = static::sanitizeAttributeName($_key);
                $_value = static::escapeAttributeValue($_value, $surround);
            }

            $attrStrings[$key] = static::getAttributePairString($key, $_value, $surround);
        }

        return $attrStrings;
    }

    /**
     * Sanitizes an HTML tag's attribute name.
     *
     * Result will not contain white space before or after the name.
     *
     * @since 4.9
     * @param string $attribute The attribute name to sanitize.
     * @return string A valid attribute name.
     */
    public static function sanitizeAttributeName($attribute)
    {
        return trim($attribute);
    }

    /**
     * Escapes an HTML tag's attribute value.
     *
     * If single quote is used to surround the value, only single quotes in the
     * value will be escaped.
     * Othwewise, all quotes in the value will be escaped.
     *
     * @since 4.9
     * @see htmlspecialchars()
     * @param type $attribute
     * @param string $quote The quote that is used for surrounding the attribute
     *  value.
     *  Default: What this class's getAttributeQuote() returns.
     * @return string An attribute value that is valid for insertion into an
     *  HTML document.
     */
    public static function escapeAttributeValue($attribute, $quote = null)
    {
        $quote = static::defaultParam($quote, static::getAttributeQuote());

        $quoteStyle = $quote === static::QUOTE_SINGLE
                ? ENT_QUOTES
                : ENT_COMPAT;
        return htmlspecialchars($attribute, $quoteStyle);
    }
}
