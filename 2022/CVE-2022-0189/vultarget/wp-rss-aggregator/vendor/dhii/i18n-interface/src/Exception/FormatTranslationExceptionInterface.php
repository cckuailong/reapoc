<?php

namespace Dhii\I18n\Exception;

/**
 * Something that can represent an exception which occurs during or related to translation of string formats.
 *
 * @since 0.1
 */
interface FormatTranslationExceptionInterface extends TranslationExceptionInterface
{
    /**
     * Retrieves params used for interpolation.
     *
     * @since 0.1
     *
     * @return array|null The array of params used, if any.
     */
    public function getParams();

    /**
     * The context of the string.
     *
     * @since 0.1
     *
     * @return Value|null The context of the string, if any.
     */
    public function getContext();
}
