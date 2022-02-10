<?php

namespace RebelCode\Wpra\Core\Handlers\FeedSources;

use Dhii\Output\TemplateInterface;

/**
 * The handler that renders a feed source's content.
 *
 * @since 4.13
 */
class RenderFeedSourceContentHandler
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
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke($content)
    {
        if (get_post_type() === 'wprss_feed' && !is_feed()) {
            return $this->template->render([
                'query_source' => get_the_ID(),
            ]);
        }

        return $content;
    }
}
