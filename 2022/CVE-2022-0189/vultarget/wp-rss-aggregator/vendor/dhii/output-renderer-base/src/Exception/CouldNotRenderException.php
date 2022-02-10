<?php

namespace Dhii\Output\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Output\RendererInterface;
use Dhii\Output\RendererAwareTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;

/**
 * An exception that occurs when a renderer cannot produce output.
 *
 * @since [*next-version*]
 */
class CouldNotRenderException extends RootException implements CouldNotRenderExceptionInterface
{
    /*
     * Adds internal renderer awareness.
     *
     * @since [*next-version*]
     */
    use RendererAwareTrait;

    /*
     * Adds internal i18n capabilities.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /*
     * Adds internal factory for creating invalid argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception, if any.
     * @param RendererInterface|null $renderer The associated renderer, if any.
     */
    public function __construct($message = null, $code = null, RootException $previous = null, RendererInterface $renderer = null)
    {
        parent::__construct((string) $message, (int) $code, $previous);
        $this->_setRenderer($renderer);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRenderer()
    {
        return $this->_getRenderer();
    }
}
