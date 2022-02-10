<?php

namespace Dhii\I18n\Exception;

use Dhii\Data\ValueAwareInterface as Value;

/**
 * Something that can represent an exception which occurs during or related to translation of strings.
 *
 * @since 0.1
 */
interface StringTranslationExceptionInterface extends TranslationExceptionInterface
{
    /**
     * The context of the string.
     *
     * @since 0.1
     *
     * @return Value|null The context of the string, if any.
     */
    public function getContext();
}
