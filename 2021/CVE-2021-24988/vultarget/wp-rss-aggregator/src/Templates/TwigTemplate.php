<?php

namespace RebelCode\Wpra\Core\Templates;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Output\Exception\TemplateRenderException;
use Dhii\Output\TemplateInterface;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

/**
 * A standard template implementation that renders a Twig template.
 *
 * @since 4.13
 */
class TwigTemplate implements TemplateInterface
{
    /* @since 4.13 */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since 4.13 */
    use StringTranslatingTrait;

    /**
     * The Twig environment.
     *
     * @since 4.13
     *
     * @var Environment
     */
    protected $env;

    /**
     * The path to the Twig file, relative from any registered templates directory.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $path;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param Environment $env  The Twig environment instance.
     * @param string      $path The path to the Twig file, relative from any registered templates directory.
     */
    public function __construct(Environment $env, $path)
    {
        $this->env = $env;
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function render($ctx = null)
    {
        try {
            return $this->env->load($this->path)->render($ctx);
        } catch (LoaderError $loaderEx) {
            throw new TemplateRenderException(
                __('Could not load template', 'wprss'), null, $loaderEx, $this, $ctx
            );
        } catch (SyntaxError $synEx) {
            throw new TemplateRenderException(
                sprintf(
                    __('Syntax error in template at line %d: %s', 'wprss'),
                    $synEx->getTemplateLine(),
                    $synEx->getMessage()
                ),
                null, $synEx, $this, $ctx
            );
        } catch (Exception $ex) {
            throw new TemplateRenderException(
                __('Could not render twig template: ', 'wprss') . $ex->getMessage(),
                null,
                $ex,
                $this,
                $ctx
            );
        }
    }
}
