<?php

namespace RebelCode\Wpra\Core\Entities\Collections;

use RebelCode\Entities\Api\SchemaInterface;

/**
 * A collection implementation that is specific to WP RSS Aggregator feed items.
 *
 * @since 4.13
 */
class FeedItemCollection extends WpEntityCollection
{
    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param string          $postType The name of the post type.
     * @param SchemaInterface $schema   The schema for feed item entities.
     */
    public function __construct($postType, SchemaInterface $schema)
    {
        parent::__construct($postType, $schema);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.14
     */
    protected function getBasePostQueryArgs()
    {
        $args = parent::getBasePostQueryArgs();
        $args['post_status'] = 'publish';
        $args['lang'] = ''; // Disble PolyLang's query filtering

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to ensure that the status is "publish".
     *
     * @since 4.13
     */
    protected function getNewPostData($data)
    {
        $post = parent::getNewPostData($data);
        $post['post_status'] = 'publish';

        return $post;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function handleFilter(&$queryArgs, $key, $value)
    {
        $r = parent::handleFilter($queryArgs, $key, $value);

        if ($key === 'feeds') {
            $slugs = $this->_normalizeArray($value);
            $posts = get_posts([
                'post_name__in' => $slugs,
                'post_type' => 'wprss_feed',
                'posts_per_page' => -1,
            ]);
            $ids = array_map(function ($post) {
                return $post->ID;
            }, $posts);

            return $this->handleFilter($queryArgs, 'sources', $ids);
        }

        if ($key === 'sources') {
            $queryArgs['meta_query']['relation'] = 'AND';
            $queryArgs['meta_query'][] = [
                'key' => 'wprss_feed_id',
                'value' => $this->_normalizeArray($value),
                'compare' => 'IN',
            ];

            return true;
        }

        if ($key === 'exclude') {
            $queryArgs['meta_query']['relation'] = 'AND';
            $queryArgs['meta_query'][] = [
                'key' => 'wprss_feed_id',
                'value' => $this->_normalizeArray($value),
                'compare' => 'NOT IN',
            ];

            return true;
        }

        return $r;
    }
}
