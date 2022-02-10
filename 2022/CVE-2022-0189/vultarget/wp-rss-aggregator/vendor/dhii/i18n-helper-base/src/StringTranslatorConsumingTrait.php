<?php

namespace Dhii\I18n;

/**
 * Methods for classes that use a string translator.
 *
 * @since [*next-version*]
 */
trait StringTranslatorConsumingTrait
{
    use StringTranslatingTrait;

    /**
     * Translates a string in the specified context.
     *
     * @since [*next-version*]
     *
     * @param string     $string  The string to translate.
     * @param mixed|null $context The context to be used for translation.
     *
     * @return string The translated string.
     */
    protected function _translate($string, $context = null)
    {
        if (!(($translator = $this->_getTranslator()) instanceof StringTranslatorInterface)) {
            return $string;
        }

        return $translator->translate($string, $context);
    }

    /**
     * Retrieves the string translator associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return StringTranslatorInterface The translator.
     */
    abstract protected function _getTranslator();
}
