<?php

namespace RebelCode\Wpra\Core\Handlers\FeedTemplates;

use RebelCode\Wpra\Core\Data\DataSetInterface;

/**
 * A handler that re-saves a template by iterating its data and re-setting it.
 *
 * @since 4.13
 */
class ReSaveTemplateHandler
{
    /**
     * The template collection.
     *
     * @since 4.13
     *
     * @var DataSetInterface
     */
    protected $collection;

    /**
     * The default template info.
     *
     * @since 4.17.4
     *
     * @var array
     */
    protected $info;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param DataSetInterface $collection The template collection.
     * @param array            $info       The default template info.
     */
    public function __construct(DataSetInterface $collection, $info)
    {
        $this->collection = $collection;
        $this->info = $info;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        $builtIn = $this->collection->filter([
            'type' => $this->info['type'],
        ]);

        $template = $builtIn[0];

        foreach ($template as $key => $value) {
            $template[$key] = $value;
        }
    }
}
