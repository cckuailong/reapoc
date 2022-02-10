<?php

namespace Dhii\I18n;

use InvalidArgumentException;

/**
 * Methods for classes that can have a string translator.
 *
 * @since [*next-version*]
 */
trait StringTranslatorAwareTrait
{
    /**
     * The translator associated with this instance.
     *
     * @since [*next-version*]
     *
     * @var StringTranslatorInterface|null
     */
    protected $translator;

    /**
     * Assigns the translator to be used by this instance.
     *
     * @since [*next-version*]
     *
     * @param StringTranslatorInterface|null $translator The translator.
     *
     * @throws InvalidArgumentException If translator is invalid.
     *
     * @return $this
     */
    protected function _setTranslator($translator)
    {
        if (!(is_null($translator) || $translator instanceof StringTranslatorInterface)) {
            throw new InvalidArgumentException('Invalid translator');
        }

        $this->translator = $translator;

        return $this;
    }

    /**
     * Retrieves the translator associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return StringTranslatorInterface|null The translator.
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }
}
