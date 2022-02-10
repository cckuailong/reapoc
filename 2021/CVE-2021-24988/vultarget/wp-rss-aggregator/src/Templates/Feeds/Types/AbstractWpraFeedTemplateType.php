<?php

namespace RebelCode\Wpra\Core\Templates\Feeds\Types;

use Dhii\Output\TemplateInterface;
use RebelCode\Wpra\Core\Data\ArrayDataSet;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Util\ParseArgsWithSchemaCapableTrait;
use RebelCode\Wpra\Core\Util\SanitizeCommaListCapableTrait;

/**
 * Abstract implementation of a standard WP RSS Aggregator feed template type.
 *
 * This partial implementation sets the standard for most of the templates provided by WP RSS Aggregator.
 * This template type has a set of core standard options pertaining to which feed items to render and pagination.
 *
 * By default, this implementation loads templates from a template collection using keys that follow the pattern:
 * `feeds/<key>/main.twig`. The key represents the path to a Twig template file relative to the WP RSS Aggregator
 * templates directory, and the collection is expected to return a {@link TemplateInterface} instance for that twig
 * template file.
 *
 * Twig template files will have access to an "options" variable which contains the template's options, an "items"
 * variable which contains the items to be rendered and a "self" variable containing information about the template.
 * The "self.dir" variable may be useful for requiring other template files located in the same directory as the
 * main template file.
 *
 * @since 4.13
 */
abstract class AbstractWpraFeedTemplateType extends AbstractFeedTemplateType
{
    /* @since 4.13 */
    use ParseArgsWithSchemaCapableTrait;

    /* @since 4.13 */
    use SanitizeCommaListCapableTrait;

    /**
     * The name of the root templates directory.
     *
     * @since 4.13
     */
    const ROOT_DIR_NAME = 'feeds';

    /**
     * The name of the main template file to load.
     *
     * @since 4.13
     */
    const MAIN_FILE_NAME = 'main.twig';

    /**
     * The templates data set.
     *
     * @since 4.13
     *
     * @var DataSetInterface
     */
    protected $templates;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param DataSetInterface $templates The templates data set.
     */
    public function __construct(DataSetInterface $templates)
    {
        $this->templates = $templates;
    }

    /**
     * Retrieves the template to render.
     *
     * @since 4.13
     *
     * @return TemplateInterface The template instance.
     */
    protected function getTemplate()
    {
        return $this->templates[$this->getTemplatePath()];
    }

    /**
     * Retrieves the path to the template directory.
     *
     * @since 4.13
     *
     * @return string The path to the template directory, relative to a registered WPRA template path.
     */
    protected function getTemplateDir()
    {
        return static::ROOT_DIR_NAME . DIRECTORY_SEPARATOR . $this->getKey() . DIRECTORY_SEPARATOR;
    }

    /**
     * Retrieves the path to the template main file.
     *
     * @since 4.13
     *
     * @return string The path to the template main file, relative to a registered WPRA template path.
     */
    protected function getTemplatePath()
    {
        return $this->getTemplateDir() . static::MAIN_FILE_NAME;
    }

    /**
     * {@inheritdoc}
     *
     * Overrides the parent method to process the standard WPRA options, filter the feed items collection and add the
     * template info to the context.
     *
     * @since 4.13
     */
    protected function prepareContext($ctx)
    {
        $pCtx = parent::prepareContext($ctx);
        /* @var $pOpts array */
        $pOpts = $pCtx['options'];
        /* @var $pItems CollectionInterface */
        $pItems = $pCtx['items'];

        // Parse the standard options
        $stdOpts = $this->parseArgsWithSchema($pOpts, $this->getStandardOptions());
        // Filter the items and count them
        $items = $pItems->filter($stdOpts['filters']);
        $count = $items->getCount();
        // Paginate the items
        $items = $items->filter($stdOpts['pagination']);
        // Calculate the total number of pages and items per page
        $perPage = empty($stdOpts['pagination']['num_items']) ? 0 : $stdOpts['pagination']['num_items'];
        $numPages = ($perPage > 0) ? ceil($count / $perPage) : 1;
        $page = empty($stdOpts['pagination']['page']) ? 1 : $stdOpts['pagination']['page'];

        // Parse the template-type's own options
        $ttOpts = $this->parseArgsWithSchema($pOpts, $this->getOptions());

        // Get all options by merging std options with template type options
        $allOpts = ( isset($stdOpts['options']) && !empty($stdOpts['options']) )
            ? array_merge_recursive($stdOpts['options'], $ttOpts)
            : $ttOpts;

        return [
            'items' => $items,
            'options' => $allOpts,
            'pagination' => [
                'page' => $page,
                'total_num_items' => $count,
                'items_per_page' => $perPage,
                'num_pages' => $numPages,
            ],
            'self' => [
                'slug' => $stdOpts['template'],
                'type' => $this->getKey(),
                'path' => $this->getTemplatePath(),
                'dir' => $this->getTemplateDir(),
            ],
        ];
    }

    /**
     * Retrieves the WPRA-standard template type options.
     *
     * @since 4.13
     *
     * @return array
     */
    protected function getStandardOptions()
    {
        return [
            'template' => [
                'default' => '',
                'filter' => FILTER_DEFAULT,
            ],
            'source' => [
                'key' => 'filters/sources',
                'default' => [],
                'filter' => function ($value) {
                    return $this->sanitizeIdCommaList($value);
                },
            ],
            'feeds' => [
                'key' => 'filters/feeds',
                'filter' => function ($value, $args) {
                    return $this->sanitizeCommaList($value);
                },
            ],
            'sources' => [
                'key' => 'filters/sources',
                'filter' => function ($value, $args) {
                    return $this->sanitizeIdCommaList($value);
                },
            ],
            'exclude' => [
                'key' => 'filters/exclude',
                'default' => [],
                'filter' => function ($value) {
                    return $this->sanitizeIdCommaList($value);
                },
            ],
            'limit' => [
                'key' => 'pagination/num_items',
                'default' => 15,
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1],
            ],
            'page' => [
                'key' => 'pagination/page',
                'default' => 1,
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 1],
            ],
            'pagination' => [
                'key' => 'pagination/enabled',
                'filter' => FILTER_VALIDATE_BOOLEAN,
            ],
            'links_behavior' => [
                'key' => 'options/links_open_behavior',
                'filter' => 'enum',
                'options' => ['self', 'blank', 'lightbox'],
                'default' => 'blank',
            ],
            'links_nofollow' => [
                'key' => 'options/links_rel_nofollow',
                'filter' => function ($val) {
                    return $val === 'no_follow' || filter_var($val, FILTER_VALIDATE_BOOLEAN);
                },
                'default' => true,
            ],
            'link_to_embed' => [
                'key' => 'options/link_to_embed',
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'default' => false,
            ],
            'custom_css_classname' => [
                'key' => 'options/custom_css_classname',
                'filter' => FILTER_DEFAULT,
                'default' => '',
            ],
            'className' => [
                'key' => 'options/className',
                'filter' => FILTER_DEFAULT,
                'default' => '',
            ],
        ];
    }

    /**
     * Creates the data set instance for a given template render context.
     *
     * @since 4.13
     *
     * @param array $ctx The render context.
     *
     * @return DataSetInterface The created data set instance.
     */
    protected function createContextDataSet(array $ctx)
    {
        return new ArrayDataSet($ctx);
    }
}
