<?php

namespace RebelCode\Wpra\Core\Entities\Stores;

use OutOfBoundsException;
use RebelCode\Entities\Api\StoreInterface;
use WP_Post;

/**
 * An implementation of a store that uses a WordPress post instance for storage. Supports both post data and meta data.
 *
 * @since 4.16
 */
class WpPostStore implements StoreInterface
{
    /**
     * @since 4.16
     *
     * @var array
     */
    protected $post;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param WP_Post $wpPost The post instance.
     */
    public function __construct(WP_Post $wpPost)
    {
        $this->post = $wpPost->to_array();
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }

        $meta = get_post_meta($this->post['ID'], $key);

        if (is_array($meta) && count($meta) > 0) {
            return reset($meta);
        }

        throw new OutOfBoundsException(sprintf('Post "%s" has no "%s" property or meta data', $this->post['ID'], $key));
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function has($key)
    {
        return array_key_exists($key, $this->post) || count(get_post_meta($this->post['ID'], $key)) > 0;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function set(array $data)
    {
        $update = [];
        $meta = [];

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->post)) {
                $update[$key] = $value;
                continue;
            }

            update_post_meta($this->post['ID'], $key, $value);
        }

        if (!empty($update)) {
            $update['ID'] = $this->post['ID'];
            wp_update_post($update);
        }

        $instance = clone $this;
        $instance->post = get_post($this->post['ID'])->to_array();

        return $instance;
    }
}
