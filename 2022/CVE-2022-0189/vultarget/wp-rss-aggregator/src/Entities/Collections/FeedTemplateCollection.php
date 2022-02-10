<?php

namespace RebelCode\Wpra\Core\Entities\Collections;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\SchemaInterface;
use RebelCode\Entities\Entity;
use WP_Post;

/**
 * A posts collection for WP RSS Aggregator feed templates.
 *
 * @since 4.13
 */
class FeedTemplateCollection extends WpEntityCollection
{
    /**
     * The default template's type.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $defType;

    /**
     * @since 4.16
     *
     * @var callable
     */
    protected $builtInStoreFactory;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string          $postType            The name of the post type.
     * @param string          $defType             The default template's type.
     * @param SchemaInterface $schema              The schema for feed template entities.
     * @param callable        $builtInStoreFactory The factory that creates stores for builtin templates.
     */
    public function __construct($postType, SchemaInterface $schema, $defType, callable $builtInStoreFactory)
    {
        parent::__construct($postType, $schema);

        $this->defType = $defType;
        $this->builtInStoreFactory = $builtInStoreFactory;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to ensure that the title is set (for auto slug generation) and the status is "publish".
     *
     * @since 4.13
     */
    protected function getNewPostData($data)
    {
        $post = parent::getNewPostData($data);
        $post['post_title'] = isset($data['name']) ? $data['name'] : '';
        $post['post_status'] = 'publish';

        return $post;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to ensure that the slug updates with the title.
     *
     * @since 4.13
     */
    protected function getUpdatePostData($key, $data)
    {
        $post = parent::getUpdatePostData($key, $data);
        // Clear the slug so WordPress re-generates it
        $post['slug'] = '';

        return $post;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to sort the templates by title, with built in templates at the very top of the list.
     *
     * @since 4.13
     */
    protected function queryPosts($key = null)
    {
        $posts = parent::queryPosts($key);

        usort($posts, function (EntityInterface $a, EntityInterface $b) {
            if ($a->get('type') === '__built_in') {
                return -1;
            }

            if ($b->get('type') === '__built_in') {
                return 1;
            }

            return strcmp($a->get('name'), $b->get('name'));
        });

        return $posts;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function handleFilter(&$queryArgs, $key, $value)
    {
        $r = parent::handleFilter($queryArgs, $key, $value);

        if ($key === 'type') {
            $subQuery = [
                'relation' => 'or',
                [
                    'key' => 'wprss_template_type',
                    'value' => $value,
                ],
            ];
            if ($value === 'list') {
                $subQuery[] = [
                    'key' => 'wprss_template_type',
                    'value' => $this->defType,
                ];
            }

            $queryArgs['meta_query'][] = $subQuery;

            return true;
        }

        return $r;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    protected function createEntity(WP_Post $post)
    {
        $type = get_post_meta($post->ID, 'wprss_template_type', true);

        if ($type === $this->defType) {
            $store = call_user_func_array($this->builtInStoreFactory, [$post]);

            return new Entity($this->schema, $store);
        }

        return parent::createEntity($post);
    }
}
