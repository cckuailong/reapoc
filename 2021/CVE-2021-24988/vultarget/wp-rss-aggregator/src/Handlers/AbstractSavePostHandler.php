<?php

namespace RebelCode\Wpra\Core\Handlers;

use Exception;
use RebelCode\Wpra\Core\Util\NormalizeWpPostCapableTrait;
use RebelCode\Wpra\Core\Util\ParseArgsWithSchemaCapableTrait;
use WP_Post;

/**
 * Abstract handler for saving posts and their meta data, with included error handling and displaying.
 *
 * @since 4.13
 */
abstract class AbstractSavePostHandler
{
    /* @since 4.13 */
    use NormalizeWpPostCapableTrait;

    /* @since 4.13 */
    use ParseArgsWithSchemaCapableTrait;

    /**
     * The type of post to save.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $postType;

    /**
     * Indicates if the handler is being invoked - used to prevent self-triggering and infinite loops.
     *
     * @since 4.13
     *
     * @var bool
     */
    protected $saving = false;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string $postType The type of post to save.
     */
    public function __construct($postType)
    {
        $this->postType = $postType;

        $this->init();
    }

    /**
     * Initializes the instance by registering the notice message handlers.
     *
     * @since 4.13
     */
    protected function init()
    {
        // If there are errors, don't show any success messages
        add_filter('post_updated_messages', function ($messages) {
            $errors = $this->getSaveErrors();
            $postType = $this->postType;
            if (count($errors) > 0) {
                $messages[$postType] = [];
            }

            return $messages;
        });

        // Show error messages as notices
        add_action('admin_notices', function () {
            $errors = $this->getSaveErrors();

            foreach ($errors as $error) {
                printf('<div class="notice notice-error"><p>%s</p></div>', $error);
            }

            $this->clearSaveErrors();
        });
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke($postOrId)
    {
        // Stop if already saving, or the post ID is empty
        if ($this->saving || empty($postOrId)) {
            return;
        }

        // Normalize arg to a WP_Post instance
        $post = $this->normalizeWpPost($postOrId);
        // If the post type is not what we expect, stop
        if ($post->post_type !== $this->postType) {
            return;
        }

        // Mark as saving
        $this->saving = true;

        // Parse the meta
        $metaData = $this->parseArgsWithSchema($_REQUEST, $this->getMetaSchema($post));
        // Check if the post is an auto draft
        $autoDraft = ($post->post_status === 'auto-draft');

        try {
            // Save the post and the meta
            $errors = $this->savePost($post, $metaData, $autoDraft);
            // Save any errors that occurred to show as notices on the next page load
            $this->setSaveErrors($errors);
        } catch (Exception $exception) {
            // Add the "on save" error to the list of errors to show on next page load
            $this->setSaveErrors([$exception->getMessage()]);
        }

        // Mark as not saving
        $this->saving = false;
    }

    /**
     * Retrieves the errors that occurred during the last save.
     *
     * @since 4.13
     *
     * @return string[]
     */
    protected function getSaveErrors()
    {
        $errors = get_transient($this->getErrorsTransient());
        $errors = empty($errors) ? [] : (array) $errors;

        return $errors;
    }

    /**
     * Sets the error messages for the last post save.
     *
     * @since 4.13
     *
     * @param string[] $errors The errors that occurred during the last saving process.
     */
    protected function setSaveErrors($errors)
    {
        set_transient($this->getErrorsTransient(), $errors, 30);
    }

    /**
     * Clears the save errors.
     *
     * @since 4.13
     */
    protected function clearSaveErrors()
    {
        delete_transient($this->getErrorsTransient());
    }

    /**
     * Retrieves the name of the transient that is used to store the save errors.
     *
     * @since 4.13
     *
     * @return string
     */
    protected function getErrorsTransient()
    {
        return sprintf('on_save_errors:%s:%d', $this->postType, get_current_user_id());
    }

    /**
     * Saves the post and the meta data.
     *
     * @since 4.13
     *
     * @param WP_Post $post      The post instance.
     * @param array   $meta      The parsed meta data in the request.
     * @param bool    $autoDraft Whether or not the post being saved is an auto-draft.
     *
     * @return string[]|void Any errors that occurred during the saving process.
     */
    abstract protected function savePost(WP_Post $post, $meta, $autoDraft);

    /**
     * Retrieves the schema for the meta data in the request.
     *
     * @see   ParseArgsWithSchemaCapableTrait
     *
     * @since 4.13
     *
     * @return array The schema.
     */
    abstract protected function getMetaSchema(WP_Post $post);
}
