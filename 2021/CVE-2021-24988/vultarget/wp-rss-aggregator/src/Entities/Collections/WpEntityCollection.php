<?php

namespace RebelCode\Wpra\Core\Entities\Collections;

use ArrayAccess;
use ArrayIterator;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use InvalidArgumentException;
use OutOfRangeException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\SchemaInterface;
use RebelCode\Entities\Entity;
use RebelCode\Wpra\Core\Data\AbstractDataSet;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Data\EntityDataSet;
use RebelCode\Wpra\Core\Entities\Stores\WpPostStore;
use RuntimeException;
use stdClass;
use Traversable;
use WP_Error;
use WP_Post;

/**
 * An entity collection for WP entities.
 *
 * @since 4.13
 */
class WpEntityCollection extends AbstractDataSet implements CollectionInterface
{
    /* @since 4.13 */
    use NormalizeArrayCapableTrait;

    /* @since 4.13 */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since 4.13 */
    use StringTranslatingTrait;

    /**
     * The post type.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $postType;

    /**
     * The meta query.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $metaQuery;

    /**
     * The entity schema.
     *
     * @since 4.16
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * The ID of the last inserted post.
     *
     * @since 4.13
     *
     * @var int|string
     */
    protected $lastInsertedId;

    /**
     * Optional filter to restrict the collection query.
     *
     * @since 4.13
     *
     * @var array|null
     */
    protected $filter;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string          $postType  The post type.
     * @param SchemaInterface $schema    The entity schema to use for created entities.
     * @param array           $metaQuery The meta query.
     */
    public function __construct($postType, SchemaInterface $schema, $metaQuery = [])
    {
        $this->postType = $postType;
        $this->schema = $schema;
        $this->metaQuery = $metaQuery;
        $this->filter = [];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function get($key)
    {
        if ($key === null && $this->lastInsertedId !== null) {
            return $this->offsetGet($this->lastInsertedId);
        }

        $posts = $this->queryPosts($key);

        if (count($posts) === 0) {
            throw new OutOfRangeException(
                sprintf(__('Post "%s" was not found', 'wprss'), $key)
            );
        }

        return reset($posts);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function has($key)
    {
        if ($key === null) {
            return false;
        }

        $posts = $this->doWpQuery($key);

        return count($posts) === 1;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function set($key, $data)
    {
        if ($key === null) {
            $this->createPost($data);

            return;
        }

        $this->updatePost($key, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function delete($key)
    {
        wp_delete_post($key, true);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function filter($filter)
    {
        if (!is_array($filter)) {
            throw new InvalidArgumentException('Collection filter argument is not an array');
        }

        if (empty($filter)) {
            return $this;
        }

        $currFilter = empty($this->filter) ? [] : $this->filter;
        $newFilter = array_merge_recursive_distinct($currFilter, $filter);

        return $this->createSelfWithFilter($newFilter);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getCount()
    {
        return count($this->doWpQuery(null));
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function clear()
    {
        foreach ($this->doWpQuery() as $post) {
            $this->delete($post->ID);
        }
    }

    /**
     * Creates a new post using the given data.
     *
     * @since 4.13
     *
     * @param array $data The data to create the post with.
     */
    protected function createPost($data)
    {
        $post = $this->getNewPostData($data);
        $result = wp_insert_post($post, true);

        if ($result instanceof WP_Error) {
            throw new RuntimeException($result->get_error_message());
        }

        $this->lastInsertedId = $result;

        // Temporarily disable the filter
        $tFilter = $this->filter;
        $this->filter = [];

        $this->updatePost($result, $data);

        // Restore the filter
        $this->filter = $tFilter;
    }

    /**
     * Updates a post.
     *
     * @since 4.13
     *
     * @param int|string $key  The post's key (ID or slug).
     * @param array      $data The data to update the post with.
     */
    protected function updatePost($key, $data)
    {
        $post = $this->get($key);
        $data = $this->getUpdatePostData($key, $data);

        // Optimization for entities, that can update their properties in bulk
        if ($post instanceof EntityInterface) {
            $post->set($data);

            return;
        }

        foreach ($data as $k => $v) {
            $post[$k] = $v;
        }
    }

    /**
     * Retrieves the data to use for creating a new post.
     *
     * @since 4.13
     *
     * @param array $data The data being used to create the post.
     *
     * @return array The actual data to use with {@link wp_insert_post}.
     */
    protected function getNewPostData($data)
    {
        return [
            'post_type' => $this->postType,
        ];
    }

    /**
     * Retrieves the data to use for updating a post.
     *
     * @since 4.13
     *
     * @param int|string $key  The post key (ID or slug).
     * @param array      $data The data being used to update the post.
     *
     * @return array The actual data to update the post with.
     */
    protected function getUpdatePostData($key, $data)
    {
        return $data;
    }

    /**
     * Normalizes a variable into a post array,
     *
     * @since 4.13
     *
     * @param array|stdClass|Traversable|WP_Post $post Post data array, object or iterable, or a WP_Post instance.
     *
     * @return array The post data array.
     */
    protected function toPostArray($post)
    {
        if ($post instanceof WP_Post) {
            return $post->to_array();
        }

        return $this->_normalizeArray($post);
    }

    /**
     * Recursively patches a subject with every entry in a given patch data array.
     *
     * @since 4.13
     *
     * @param array|ArrayAccess          $subject The subject to patch.
     * @param array|stdClass|Traversable $patch   The data to patch the subject with.
     *
     * @return array|ArrayAccess The patched subject.
     */
    protected function recursivePatch($subject, $patch)
    {
        foreach ($patch as $key => $value) {
            $subject[$key] = $value;
        }

        return $subject;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getIterator()
    {
        return new ArrayIterator($this->queryPosts(null));
    }

    /**
     * Queries posts from the database and creates the data sets.
     *
     * @since 4.16
     *
     * @param int|string|null $key Optional ID or slug which, if not null, narrows down the query to only that post.
     *
     * @return DataSetInterface[] An array of posts objects.
     */
    protected function queryPosts($key = null)
    {
        $posts = $this->doWpQuery($key);

        $results = [];
        foreach ($posts as $post) {
            $results[] = new EntityDataSet($this->createEntity($post));
        }

        return $results;
    }

    /**
     * Creates an entity instance for a given WordPress post.
     *
     * @since 4.16
     *
     * @param WP_Post $post The WordPress post instance.
     *
     * @return EntityInterface The created entity instance.
     */
    protected function createEntity(WP_Post $post)
    {
        return new Entity($this->schema, new WpPostStore($post));
    }

    /**
     * Queries the posts.
     *
     * Not recommended to be called directly. Use only for fast existence checks or counting.
     *
     * @since 4.13
     *
     * @see   WpEntityCollection::queryPosts()
     *
     * @param int|string|null $key Optional ID or slug which, if not null, narrows down the query to only that post.
     *
     * @return WP_Post[] An array of posts objects.
     */
    protected function doWpQuery($key = null)
    {
        $queryArgs = $this->getBasePostQueryArgs();

        if ($key !== null && is_numeric($key)) {
            $queryArgs['p'] = $key;
        }

        if ($key !== null && is_string($key) && !is_numeric($key)) {
            $queryArgs['name'] = $key;
        }

        $filter = is_array($this->filter) ? $this->filter : [];

        foreach ($filter as $fKey => $fVal) {
            $handled = $this->handleFilter($queryArgs, $fKey, $fVal);

            if (!$handled) {
                $queryArgs[$fKey] = $fVal;
            }
        }

        return get_posts($queryArgs);
    }

    /**
     * Retrieves the base (bare minimum) post query args.
     *
     * @since 4.13
     *
     * @return array
     */
    protected function getBasePostQueryArgs()
    {
        return [
            'post_type' => $this->postType,
            'post_status' => array_keys(get_post_statuses()),
            'suppress_filters' => true,
            'cache_results' => false,
            'posts_per_page' => -1,
            'meta_query' => $this->metaQuery,
        ];
    }

    /**
     * Handles the processing of a filter.
     *
     * @since 4.13
     *
     * @param array  $queryArgs The query arguments to modify, passed by reference.
     * @param string $key       The filter key.
     * @param mixed  $value     The filter value.
     *
     * @return bool True if the filter was handled, false if it wasn't.
     */
    protected function handleFilter(&$queryArgs, $key, $value)
    {
        if ($key === 'id') {
            $queryArgs['post__in'] = is_array($value) ? $value : [$value];

            return true;
        }

        if ($key === 's') {
            $queryArgs['s'] = $value;

            return true;
        }

        if ($key === 'num_items') {
            $queryArgs['posts_per_page'] = (!$value) ? -1 : $value;

            return true;
        }

        if ($key === 'page') {
            $queryArgs['paged'] = $value;

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function recursiveUnpackIterators()
    {
        return true;
    }

    /**
     * Creates a new collection of this type with an added filter.
     *
     * @since 4.13
     *
     * @param array $filter The filter for restricting the collection query.
     *
     * @return CollectionInterface
     */
    protected function createSelfWithFilter($filter)
    {
        $instance = clone $this;
        $instance->filter = $filter;

        return $instance;
    }
}

// Alias for the old WpPostCollection class.
class_alias(
    'RebelCode\Wpra\Core\Entities\Collections\WpEntityCollection',
    'RebelCode\Wpra\Core\Data\Collections\WpPostCollection'
);
