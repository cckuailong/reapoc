<?php

namespace RebelCode\Wpra\Core\Handlers\Images;

use Exception;
use RebelCode\Wpra\Core\Data\DataSetInterface;

/**
 * The handler that deletes attached images for an imported item.
 *
 * @since 4.14
 */
class DeleteImagesHandler
{
    /**
     * @since 4.14
     *
     * @var DataSetInterface
     */
    protected $importedItems;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param DataSetInterface $importedItems The imported items data set.
     */
    public function __construct(DataSetInterface $importedItems)
    {
        $this->importedItems = $importedItems;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke($postId)
    {
        try {
            $item = $this->importedItems[$postId];
        } catch (Exception $e) {
            // Item may not be imported by WPRA or does not exist
            // Here we do a manual post meta check, just to be safe
            if (get_post_meta($postId, 'wprss_feed_id', true) === '') {
                return;
            }
        }

        // Get the attachments
        $attachments = get_children([
            'post_parent' => $postId,
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
        ]);

        // Delete them
        foreach ($attachments as $id => $attachment) {
            wp_delete_post($id);
        }
    }
}
