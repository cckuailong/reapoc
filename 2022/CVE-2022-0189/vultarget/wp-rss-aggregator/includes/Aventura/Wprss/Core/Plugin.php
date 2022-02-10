<?php

namespace Aventura\Wprss\Core;

use Aventura\Wprss\Core\Component;

/**
 * The Core instance of WP RSS Aggregator.
 *
 * @since 4.8.1
 */
class Plugin extends Plugin\PluginAbstract
{
    const CODE = \WPRSS_PLUGIN_CODE;
    const VERSION = \WPRSS_VERSION;

    const POST_TYPE_FEED_SOURCE = \WPRSS_POST_TYPE_FEED_SOURCE;

    /**
     * Hooks the rest of the functionality of this class.
     *
     * @since 4.9
     */
    public function hook()
    {
        $this->on('!plugins_loaded', array($this, 'delayedHook'));
    }

    /**
     * Hooks in functionality after all plugins are loaded.
     *
     * @since 4.9
     */
    public function delayedHook()
    {
        $this->on('!plugin_row_meta', array($this, '_addPluginRowMeta'), null, 10, 2);
    }

    /**
     * Returns all meta members that appear below a plugin row in the backend.
     *
     * Handles `plugin_row_meta` WP native filter.
     *
     * @since 4.9
     * @param type $meta
     * @param type $pluginBasename
     * @return array Numeric array, where each element is a meta information
     *  piece (usually link).
     */
    public function _addPluginRowMeta($meta, $pluginBasename)
    {
        if ($pluginBasename !== $this->getBasename()) {
            return $meta;
        }

        $meta = array_merge($meta, array_values($this->getPluginRowMeta()));
        return $meta;
    }

    /**
     * Returns a list of meta members for this plugin.
     *
     * Raises plugin-specific event `plugin_row_meta`.
     *
     * @since 4.9
     * @return array An array of meta members for this plugin, by key.
     */
    public function getPluginRowMeta()
    {
        return $this->event('plugin_row_meta', array('links' => array(
            'getting_started'           => $this->getAnchor(array(
                'target'                    => '_blank',
                'href'                      => 'https://docs.wprssaggregator.com/category/getting-started/'
            ), $this->__('Getting Started')),
            'extensions'                => $this->getAnchor(array(
                'target'                    => '_blank',
                'href'                      => 'https://www.wprssaggregator.com/extensions/'
            ), $this->__('Extensions'))
        )))->getData('links');
    }

    /**
     * Get a new anchor block instance.
     *
     * @since 4.9
     * @param array|string $attributes Keys are attribute names; values are attribute
     *  values. These will become the attributes of the anchor tag.
     *  If string, this will be treated as the value of the 'href' attribute.
     * @param string $content Content for the anchor tag. Usually text.
     * @return Block\Html\TagInterface An anchor block instance.
     */
    public function getAnchor($attributes = array(), $content = '')
    {
        if (is_string($attributes)) {
            $attributes = array('href' => $attributes);
        }
        $block = $this->createAnchorBlock()
            ->setAttributes($attributes)
            ->setContent($content);

        return $block;
    }

    /**
     * Anchor block factory.
     *
     * @since 4.9
     * @return Aventura\Wprss\Core\Block\Html\TagInterface
     */
    public function createAnchorBlock()
    {
        return new \Aventura\Wprss\Core\Block\Html\Anchor();
    }

    /**
     * Create an AJAX response instance that contains error data.
     *
     * @since 4.9
     * @param \Exception|string $error An exception, or error message.
     * @return Http\Message\Ajax\Response
     */
    public function createAjaxErrorResponse($error)
    {
            return $error instanceof \Exception
                    ? Model\AjaxResponse::createFromException($error)
                    : Model\AjaxResponse::createFromError($error);
    }

    /**
     * Creates an instance of an AJAX response.
     *
     * @since 4.9
     * @param type $data
     * @return Http\Message\Ajax\Response
     */
    public function createAjaxResponse($data = array())
    {
        $response = new Model\AjaxResponse();
        $response->setAjaxData($data);

        return $response;
    }

    /**
     * Retrieve the post type of feed sources.
     *
     * @since 4.10
     *
     * @return string The post type string of the feed source post type.
     */
    public function getFeedSourcePostType()
    {
        return static::POST_TYPE_FEED_SOURCE;
    }

    /**
     * Retrieve the admin AJAX notices component singleton.
     *
     * @since 4.10
     *
     * @return Component\AdminAjaxNotices
     */
    public function getAdminAjaxNotices()
    {
        return $this->_getContainer()->get($this->_getServiceIdPrefix('admin_ajax_notices'));
    }

    /**
     * Retrieve the "Leave Review" notification component singleton.
     *
     * @since 4.10
     *
     * @return Component\LeaveReviewNotification
     */
    public function getLeaveReviewNotification()
    {
        return $this->_getContainer()->get($this->_getServiceIdPrefix('leave_review'));
    }

    /**
     * Retrieve the "Leave Review" notification component singleton.
     *
     * @since 4.10
     *
     * @return Component\AdminHelper
     */
    public function getAdminHelper()
    {
        return $this->_getContainer()->get($this->_getServiceIdPrefix('admin_helper'));
    }
}
