<?php

namespace Dhii\I18n;

use Dhii\Data\ValueAwareInterface as Value;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Something that can act as a translator, which can translate whole strings.
 *
 * The {@see translate()} method throws a more specialized
 * {@see Dhii\I18n\Exception\StringTranslationExceptionInterface}.
 *
 * @since 0.1
 */
interface StringTranslatorInterface extends TranslatorInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.1
     *
     * @param string|Stringable $string  The string to translate.
     * @param string|Value|null $context A context for the string, if any.
     *
     * @return string The translated string.
     */
    public function translate($string, $context = null);
}
