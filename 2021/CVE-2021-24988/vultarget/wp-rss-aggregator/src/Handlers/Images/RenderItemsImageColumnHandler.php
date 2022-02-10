<?php

namespace RebelCode\Wpra\Core\Handlers\Images;

use RebelCode\Wpra\Core\Data\DataSetInterface;

/**
 * The handler that renders the contents of the images column in the feed items page.
 *
 * @since 4.14
 */
class RenderItemsImageColumnHandler
{
    /**
     * @since 4.14
     *
     * @var DataSetInterface
     */
    protected $feedItems;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $column;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param DataSetInterface $feedItems
     * @param string           $column
     */
    public function __construct(DataSetInterface $feedItems, $column)
    {
        $this->feedItems = $feedItems;
        $this->column = $column;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke($column, $postId)
    {
        if (!isset($this->feedItems[$postId]) || $column !== $this->column) {
            return;
        }

        $feedItem = $this->feedItems[$postId];
        $ftImageUrl = $feedItem['ft_image_url'];

        if (empty($ftImageUrl)) {
            return;
        }

        printf(
            '<div><img src="%1$s" alt="%2$s" title="%2$s" class="wpra-item-ft-image" /></div>',
            $ftImageUrl,
            __('Feed item image', 'wprss')
        );
    }
}
