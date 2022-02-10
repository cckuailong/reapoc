<?php

namespace Dhii\I18n\Exception;

use Dhii\I18n\TranslatorInterface;

/**
 * Something that can represent an exception which occurs during or related to translation.
 *
 * @since 0.1
 */
interface TranslationExceptionInterface extends I18nExceptionInterface
{
    /**
     * Retrieves the subject which is being translated.
     *
     * @since 0.1
     *
     * @return mixed The subject being translated.
     */
    public function getSubject();

    /**
     * Retrieves the translator doing the translation.
     *
     * @since 0.1
     *
     * @return TranslatorInterface The translator instance.
     */
    public function getTranslator();
}
