<?php

namespace RebelCode\Wpra\Core\Templates\Feeds;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Output\TemplateInterface;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use InvalidArgumentException;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\Templates\Feeds\Types\FeedTemplateTypeInterface;
use RebelCode\Wpra\Core\Util\ParseArgsWithSchemaCapableTrait;
use stdClass;
use Traversable;

/**
 * A template that can render any template type with a list of options, unbound from any post.
 *
 * @since 4.14
 */
class TemplateTypeTemplate implements TemplateInterface
{
    /* @since 4.14 */
    use ParseArgsWithSchemaCapableTrait;

    /* @since 4.14 */
    use NormalizeArrayCapableTrait;

    /* @since 4.14 */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since 4.14 */
    use StringTranslatingTrait;

    /**
     * An associative array of template type instances.
     *
     * @since 4.14
     *
     * @var FeedTemplateTypeInterface[]
     */
    protected $types;

    /**
     * The key of the default template type to use.
     *
     * @since 4.14
     *
     * @var string
     */
    protected $defType;

    /**
     * The collection of feed items to render.
     *
     * @since 4.14
     *
     * @var CollectionInterface
     */
    protected $itemsCollection;

    /**
     * The template to use to render the container.
     *
     * @since 4.14
     *
     * @var TemplateInterface
     */
    protected $containerTemplate;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param string              $defType           The name of the template to use by default.
     * @param array               $templateTypes     The available template types.
     * @param CollectionInterface $itemsCollection   The collection of feed items.
     * @param TemplateInterface   $containerTemplate The template to use for rendering the container.
     */
    public function __construct(
        $defType,
        $templateTypes,
        CollectionInterface $itemsCollection,
        TemplateInterface $containerTemplate
    ) {
        $this->types = $templateTypes;
        $this->defType = $defType;
        $this->itemsCollection = $itemsCollection;
        $this->containerTemplate = $containerTemplate;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function render($argCtx = null)
    {
        // Parse the context
        $ctx = $this->parseContext($argCtx);
        // Get the template type
        $typeKey = $ctx['type'];
        $templateType = array_key_exists($typeKey, $this->types)
            ? $this->types[$typeKey]
            : $this->types[$this->defType];

        $options = $ctx['options'];
        $options['page'] = $ctx['page'];

        $rendered = $templateType->render([
            'options' => $options,
            'items' => $ctx['items'],
        ]);

        return $this->containerTemplate->render([
            'ctx' => base64_encode(json_encode($argCtx)),
            'template' => $rendered,
        ]);
    }

    /**
     * Parses the render context, normalizing it to an array and filtering it against the schema.
     *
     * @since 4.14
     *
     * @param array|stdClass|Traversable $ctx The render context.
     *
     * @return array The parsed context.
     */
    protected function parseContext($ctx)
    {
        try {
            $normCtx = $this->_normalizeArray($ctx);
        } catch (InvalidArgumentException $exception) {
            $normCtx = [];
        }

        // Parse the context, putting all non-schema data in an "options" key
        $schema = $this->getContextSchema();
        $pCtx = $this->parseArgsWithSchema($normCtx, $schema, '/');

        return $pCtx;
    }

    /**
     * Retrieves the standard WP RSS Aggregator template context schema.
     *
     * @since 4.14
     *
     * @see   ParseArgsWithSchemaCapableTrait::parseArgsWithSchema()
     *
     * @return array
     */
    protected function getContextSchema()
    {
        return [
            'type' => [
                'default' => $this->defType,
                'filter' => FILTER_SANITIZE_STRING,
            ],
            'options' => [
                'default' => [],
                'filter' => function ($options) {
                    if (is_array($options)) {
                        return $options;
                    }

                    return [];
                },
            ],
            'items' => [
                'default' => $this->itemsCollection,
                'filter' => function ($items) {
                    if ($items instanceof CollectionInterface) {
                        return $items;
                    }

                    throw new InvalidArgumentException(__('The "items" must be a collection instance', 'wprss'));
                },
            ],
            'page' => [
                'default' => 1,
                'filter' => FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
        ];
    }
}
