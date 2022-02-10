<?php

namespace Aventura\Wprss\Core\Model;

/**
 * A model that has translation capabilities via an external translator.
 *
 * @since 4.11
 */
class AbstractTranslatingModel extends ModelAbstract
{
    /**
     * @since 4.11
     * @var callable
     */
    protected $translator;

    /**
     * Sets the translator to be used by this instance.
     *
     * @since 4.11
     *
     * @param callable $translator The translator.
     * @return $this This instance.
     * @throws \Exception If translator not valid.
     */
    protected function _setTranslator($translator)
    {
        if (!is_callable($translator)) {
            throw $this->exception('Could not set translator: translator must be callable');
        }

        $this->translator = $translator;

        return $this;
    }

    /**
     * Retrieves the translator used by this instance.
     *
     * @since 4.11
     *
     * @return callable The translator.
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }

    /**
     * {@inheritdoc}
     *
     * Translates text using
     *
     * If translator is not specified, and one is set for this instance,
     * that translator will be used instead.
     *
     * @since 4.11
     */
    protected function _translate($text, $translator = null) {
        if (is_null($translator)) {
            $translator = $this->_getTranslator();
        }

        if (!is_callable($translator)) {
            return parent::_translate($text);
        }

        return $translator($text);
    }
}
