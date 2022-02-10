<?php

namespace RebelCode\Wpra\Core\Handlers\GutenbergBlock;

use Dhii\Output\RendererInterface;
use RebelCode\Wpra\Core\Wp\Asset\AssetInterface;

/**
 * Class for registering assets for gutenberg block.
 *
 * @since 4.13
 */
class GutenbergBlockAssetsHandler
{
    /**
     * The list of assets required to render the block.
     *
     * @since 4.14
     *
     * @var AssetInterface[]
     */
    protected $assets;

    /**
     * The list of states for the block.
     *
     * @since 4.14
     *
     * @var RendererInterface[]
     */
    protected $states;

    /**
     * GutenbergBlockAssetsHandler constructor.
     *
     * @param AssetInterface[] $assets The list of assets for the block.
     * @param RendererInterface[] $states The list of states for the block.
     */
    public function __construct(array $assets, array $states)
    {
        $this->assets = $assets;
        $this->states = $states;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }

        foreach ($this->states as $state) {
            $state->render();
        }
    }
}
