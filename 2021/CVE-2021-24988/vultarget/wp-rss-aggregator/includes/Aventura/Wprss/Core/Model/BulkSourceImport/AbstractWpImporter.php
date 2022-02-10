<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport;

/**
 * An importer that can import feed sources into WordPress.
 *
 * @since 4.11
 */
abstract class AbstractWpImporter extends AbstractImporter
{

    /**
     * Prepares data using a feed source representation, ready to be converted into a local resource.
     *
     * @since 4.11
     *
     * @param array|\ArrayAccess $source The feed source representation.
     * @return array Data ready for insertion that can represent a feed source.
     */
    protected function _prepareInsertionData($source)
    {
        $data = $this->_getPostDataDefaults($source);
        $post = array(
            'post_title'            => $data['title'],
            'post_status'           => $data['status'],
            'post_type'             => $data['type'],
            'post_site'             => $data['site'],
            'url'                   => $data['url'],
        );

        return $post;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    protected function _createLocalResource($data)
    {
        // The URL does not go into the post, but is meta data
        $url = $data[ImporterInterface::SK_URL];
        unset($data[ImporterInterface::SK_URL]);

        $id = $this->_insertWpPost($data);
        $this->_updateWpPostMeta($id, 'wprss_url', $url);

        return $id;
    }

    /**
     * Inserts a post with the specified data into WordPress.
     *
     * @since 4.11
     *
     * @param type $data
     * @return int The ID of the inserted post.
     *
     * @throws Exception\ImportExceptionInterface If post could not be inserted.
     */
    protected function _insertWpPost($data)
    {
        $id = \wp_insert_post($data, true);
        if (\is_wp_error($id)) {
            throw new Exception\ImportException($id->get_error_message());
        }

        return $id;
    }

    /**
     * Updates a meta value for a WordPress post.
     *
     * @since 4.11
     *
     * @param int $postId The ID of the post.
     * @param string $metaName Name of the meta to update.
     * @param string|int $metaValue Value of the meta to set.
     * @return int|true True if meta value existed, and updated successfully.
     *  If meta value created in the process, the meta ID for it.
     * @throws \RuntimeException If meta value could not be updated.
     */
    protected function _updateWpPostMeta($postId, $metaName, $metaValue)
    {
        if ($result = \update_post_meta($postId, $metaName, $metaValue) === false) {
            throw new \RuntimeException(sprintf('Could not update meta "%2$s" for post #%1$s', $postId, $metaName));
        }

        return $result;
    }

    /**
     * Retrieves default values for posts which represent imported feed sources.
     *
     * @since 4.11
     *
     * @param array $data Additional data to merge into defaults.
     * @return array The defaults, merged with additional data.
     */
    protected function _getPostDataDefaults($data = array())
    {
        $defaults = array(
            'status'        => $this->_getData('default_status'),
            'type'          => $this->_getData('default_type'),
            'site'          => $this->_getData('default_site')
        );

        return $this->_mergeArrays($defaults, $data);
    }
}