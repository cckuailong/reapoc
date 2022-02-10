<?php

namespace RebelCode\Wpra\Core\Handlers\FeedTemplates;

use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;

/**
 * The handler that auto creates the default feed template.
 *
 * @since 4.13
 */
class CreateDefaultFeedTemplateHandler
{
    /**
     * The collection of feed templates.
     *
     * @since 4.13
     *
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * The data to use for creating the default feed template.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param CollectionInterface $collection The feed templates collection.
     * @param array               $data       The data to use for creating the default feed template.
     */
    public function __construct($collection, $data)
    {
        $this->collection = $collection;
        $this->data = $data;
    }

    /**
     * @since 4.13
     */
    public function __invoke()
    {
        $builtInTemplates = $this->collection->filter([
            'type' => $this->data['type']
        ]);

        if ($builtInTemplates->getCount() === 0) {
            $this->collection[] = $this->data;
        }
    }
}
