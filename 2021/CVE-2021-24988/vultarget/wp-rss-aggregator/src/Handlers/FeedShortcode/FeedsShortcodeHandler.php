<?php

namespace RebelCode\Wpra\Core\Handlers\FeedShortcode;

use Dhii\Output\TemplateInterface;

/**
 * The feeds shortcode handler.
 *
 * @since 4.13
 */
class FeedsShortcodeHandler
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
     * @param TemplateInterface $template The template to render.
     */
    public function __construct(TemplateInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @since 4.13
     *
     * @param array $args The shortcode arguments.
     *
     * @return string The rendered shortcode result.
     */
    public function __invoke($args = [])
    {
        // Decode HTML entities in the arguments
        $args = is_array($args) ? $args : [];
        $args = array_map('html_entity_decode', $args);
        // Render the template
        $result = $this->template->render($args);

        // Filter the result and return it
        return apply_filters('wprss_shortcode_output', $result);
    }
}
