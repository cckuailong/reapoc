<?php

namespace Dhii\I18n;

/**
 * Methods for classes which can translate.
 *
 * @since [*next-version*]
 */
trait StringTranslatingTrait
{
    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     * @see _translate()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    protected function __($string, $args = array(), $context = null)
    {
        $string = $this->_translate($string, $context);
        array_unshift($args, $string);
        $result = call_user_func_array('sprintf', $args);

        return $result;
    }

    /**
     * Translates a string.
     *
     * @since [*next-version*]
     *
     * @param string $string The string to translate.
     *
     * @return string The translated string.
     */
    protected function _translate($string, $context = null)
    {
        return $string;
    }
}
