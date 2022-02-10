<?php

namespace RebelCode\Wpra\Core\Handlers\FeedSources;

use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use WP_Post;

/**
 * Handler for saving feed source meta data submitted from the edit page meta box.
 *
 * @since 4.14
 */
class FeedSourceSaveMetaHandler
{
    /**
     * @since 4.18
     *
     * @var string
     */
    protected $postType;

    /**
     * @since [*some-version*]
     *
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @since [*some-version*]
     *
     * @var bool
     */
    protected $locked;

    /**
     * Constructor.
     *
     * @since [*some-version*]
     *
     * @param string              $postType   The feed source post type.
     * @param CollectionInterface $collection The feed sources collection.
     */
    public function __construct($postType, CollectionInterface $collection)
    {
        $this->postType = $postType;
        $this->collection = $collection;
        $this->locked = false;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke($postId, $post)
    {
        if (!($post instanceof WP_Post)) {
            return;
        }

        // Get the post type object.
        $post_type = get_post_type_object($post->post_type);

        // Check if a valid post type
        if ($post->post_type !== $this->postType) {
            return;
        }

        // If the handler is locked (already running), stop to prevent an infinite loop
        if ($this->locked) {
            return;
        }

        // Verify the nonce to ensure that the data is coming from the feed source edit page
        $nonce = filter_input(INPUT_POST, 'wprss_meta_box_nonce');
        if (!wp_verify_nonce($nonce, 'wpra_feed_source')) {
            return;
        }

        // Stop if doing AJAX, cron or an auto save
        if (wp_doing_ajax() || wp_doing_cron() || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check if the current user has permission to edit the post.
        if (!current_user_can($post_type->cap->edit_post, $postId)) {
            return;
        }

        // Get the submitted post meta
        $meta = filter_input(INPUT_POST, 'wpra_feed', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (empty($meta)) {
            return;
        }

        // Prevent infinite loop
        $this->locked = true;

        // Get the feed source model object
        $feed = $this->collection[$post->ID];
        // Save the meta to the feed
        foreach ($meta as $key => $value) {
            if (isset($feed[$key])) {
                $feed[$key] = $value;
            }
        }

        $this->locked = false;
    }
}
