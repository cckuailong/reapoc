<?php

namespace RebelCode\Wpra\Core\Handlers\FeedTemplates;

use Dhii\Output\TemplateInterface;

/**
 * The handler for rendering a feeds template.
 *
 * @since 4.13
 */
class AjaxRenderFeedsTemplateHandler
{
    /**
     * The template to render.
     *
     * @since 4.13
     *
     * @var TemplateInterface
     */
    protected $template;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param TemplateInterface $template the template to render.
     */
    public function __construct(TemplateInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @since 4.13
     */
    public function __invoke()
    {
        $args = filter_input(
            INPUT_GET,
            'wprss_render_args',
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY | FILTER_NULL_ON_FAILURE
        );
        $args = is_array($args) ? $args : [];

        echo json_encode([
            'render' => $this->template->render($args),
        ]);

        die;
    }
}
