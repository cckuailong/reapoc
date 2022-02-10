<?php

namespace Dhii\I18n;

use Dhii\I18n\Exception\I18nExceptionInterface;
use Dhii\I18n\Exception\TranslationExceptionInterface;

/**
 * Something that can represent a translator, which can translate a subject.
 *
 * @since 0.1
 */
interface TranslatorInterface
{
    /**
     * Translates a subject.
     *
     * @since 0.1
     *
     * @param mixed $subject The subject to translate.
     *
     * @throws TranslationExceptionInterface If could not translate string.
     * @throws I18nExceptionInterface        If a problem occurs that is not directly related to the translation process.
     *
     * @return mixed The translated subject.
     */
    public function translate($subject);
}
