<?php

namespace RebelCode\Wpra\Core\Templates\Feeds;

use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Templates\Feeds\Types\ListTemplateType;

/**
 * A fully generic WP RSS Aggregator feed template type, based on the core list template type.
 *
 * @since 4.13
 */
class FeedTemplateType extends ListTemplateType
{
    /**
     * The template's ID.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $id;

    /**
     * The template's name.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $name;

    /**
     * The path to the twig template file, relative from a registered Twig directory.
     *
     * @since 4.13
     *
     * @var string|null
     */
    protected $path;

    /**
     * The default options.
     *
     * @since 4.13
     *
     * @var DataSetInterface
     */
    protected $defaults;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string           $id       The template's ID.
     * @param string           $name     The template's name.
     * @param string           $path     The path to the twig template file.
     * @param DataSetInterface $defaults The default template options.
     */
    public function __construct($id, $name, $path, DataSetInterface $defaults)
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
        $this->defaults = $defaults;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getKey()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getName()
    {
        $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getTemplateDir()
    {
        return dirname($this->path) . DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getTemplatePath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getOptions()
    {
        $schema = [];

        foreach ($this->defaults as $key => $value) {
            $schema[$key] = [
                'filter' => FILTER_DEFAULT,
            ];
            if (isset($this->defaults[$key])) {
                $schema[$key]['default'] = $this->defaults[$key];
            }
        }

        return $schema;
    }
}
