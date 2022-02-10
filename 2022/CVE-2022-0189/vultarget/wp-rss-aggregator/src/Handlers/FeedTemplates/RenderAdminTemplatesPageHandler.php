<?php

namespace RebelCode\Wpra\Core\Handlers\FeedTemplates;

use Dhii\Output\RendererInterface;
use RebelCode\Wpra\Core\Wp\Asset\AssetInterface;

/**
 * The handler that renders the admin feed templates page.
 *
 * @since 4.13
 */
class RenderAdminTemplatesPageHandler
{
    /**
     * The list of assets required to render the page.
     *
     * @since 4.14
     *
     * @var AssetInterface[]
     */
    protected $assets;

    /**
     * RenderAdminTemplatesPageHandler constructor.
     *
     * @param AssetInterface[] $assets The list of assets required to render the page.
     */
    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    /**
     * @since 4.13
     */
    public function __invoke()
    {
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }

        echo wprss_render_template('admin/templates-page.twig', [
            'title' => 'Templates',
            'subtitle' => 'Follow these introductory steps to get started with WP RSS Aggregator.',
        ]);
    }
}
