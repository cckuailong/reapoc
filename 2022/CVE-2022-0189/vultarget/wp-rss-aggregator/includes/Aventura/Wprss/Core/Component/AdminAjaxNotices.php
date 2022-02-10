<?php

namespace Aventura\Wprss\Core\Component;

use Aventura\Wprss\Core;
use Aventura\Wprss\Core\Model\AdminAjaxNotice\NoticeInterface;
use Interop\Container\ContainerInterface;

/**
 * Component responsible for notices in the backend.
 *
 * @since 4.10
 */
class AdminAjaxNotices extends Core\Plugin\ComponentAbstract
{
    /**
     * @since 4.11
     * @var ContainerInterface
     */
    protected $container;

    public function __construct($data, ContainerInterface $container)
    {
        parent::__construct($data);

        $this->_setContainer($container);
    }

    /**
     * Sets the container that this component will use.
     *
     * @since 4.11
     *
     * @param ContainerInterface $container The container to set.
     * @return AdminAjaxNotices This instance.
     */
    protected function _setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Retrieves the container used by this instance.
     *
     * @since 4.11
     *
     * @return ContainerInterface The container instance.
     */
    protected function _getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function hook()
    {
        parent::hook();
        $this->_hookNotices();
        $this->_hookAssets();
    }

    /**
     * Hooks in a callback that adds existing notices to the controller.
     *
     * @see _addNotices()
     *
     * @since 4.11
     */
    protected function _hookNotices()
    {
        $this->on('!plugins_loaded', array($this, '_addNotices'));
    }

    /**
     * Hooks in a callback that enqueues assets necessary for the notices.
     *
     * @since 4.11
     */
    protected function _hookAssets()
    {
        $me = $this;

        $this->on('!init', function() use (&$me) {
            if (is_admin()) {
                $me->_registerAssets();
            }
        });

        $this->on('admin_notice_add_after', function ($notice) use (&$me) {
            /* @var $notice array Notice data */
            /* @var $me AdminAjaxNotices */
            $me->on('admin_scripts_styles', function () use (&$me) {
                /* @var $me AdminAjaxNotices */
                $me->enqueueAssets();
            });
        });
    }

    /**
     * Enqueues assets of this component.
     *
     * @since 4.11
     *
     * @uses-filter wprss_admin_notice_collection_before_enqueue_scripts To modify list of script handles to enqueue.
     * @uses-action wprss_admin_notice_collection_after_enqueue_scripts To access list of enqueued script handles.
     */
    public function enqueueAssets()
    {
        // Get singleton collection
        $collection = $this->getNoticeCollection();

        // Get script handles via filter
        $script_handles = array('wprss-admin-notifications');
        $this->event('admin_notice_collection_before_enqueue_scripts', array(&$script_handles, $collection));
        // Iterate and enqueue scripts
        foreach ($script_handles as $_idx => $_handle) {
            wp_enqueue_script($_handle);
        }
        wp_enqueue_style( 'wprss-admin-notifications' );
        // Post-enqueueing action
        $this->event('admin_notice_collection_after_enqueue_scripts', array(&$script_handles, $collection));
    }

    /**
     * Registers assets of this component
     *
     * @since 4.11
     */
    public function _registerAssets()
    {
        $version = $this->getPlugin()->getVersion();
        $collection = $this->getNoticeCollection();
        // This handles the client side for WPRSS_Admin_Notices
        wp_register_script( 'wprss-admin-notifications', wprss_get_script_url( 'admin-notifications' ), array('aventura'), $version, true );

        // Frontend settings
        $settings = array(
            'notice_class'                  => $collection->get_notice_base_class(),
            'nonce_class'                   => $collection->get_nonce_base_class(),
            'btn_close_class'               => $collection->get_btn_close_base_class(),
            'action_code'                   => wprss_admin_notice_get_action_code(),
            'dismiss_mode_class_prefix'     => $collection->get_dismiss_mode_class_prefix(),
        );
        $this->event( 'admin_notice_collection_before_localize_vars', array(&$settings, $collection) );
        wp_localize_script( 'aventura', 'adminNoticeGlobalVars', $settings);

        wp_register_style( 'wprss-admin-notifications', WPRSS_CSS . 'admin-notifications.css', array(), $version );
    }

    /**
     * Adds notices to the notice controller.
     *
     * @see \WPRSS_Admin_Notices
     *
     * @since 4.11
     *
     * @return AdminAjaxNotices This instance.
     */
    public function _addNotices($eventData)
    {
        foreach ($this->_getNoticeNamesToAdd() as $_name) {
            $this->addNotice($_name);
        }

        return $this;
    }

    /**
     * Retrieves a list of notice names that should be added to the notice controller.
     *
     * @since 4.11
     *
     * @return array|\Traversable A list of notice names that should be added
     */
    protected function _getNoticeNamesToAdd()
    {
        return array(
            'more_features'
        );
    }

    /**
     * Retrieve the notice collection.
     *
     * @see wprss_admin_notice_get_collection()
     *
     * @since 4.10
     *
     * @return \WPRSS_Admin_Notices The notice collection object.
     */
    public function getNoticeCollection()
    {
        return wprss_admin_notice_get_collection();
    }

    /**
     * Add a notice.
     *
     * @see wprss_admin_notice_add()
     *
     * @param array|string $notice Data of the notice
     *
     * @return bool|WP_Error True if notice added, false if collection unavailable, or WP_Error if something went wrong.
     */
    public function addNotice($notice)
    {
        if (is_string($notice)) {
            $notice = $this->_getNotice($notice);
        }

        if ($notice instanceof Core\DataObjectInterface) {
            $notice = $notice->getData();
        }

        return wprss_admin_notice_add($notice);
    }

    /**
     * Retrieves a notice instance for the given ID.
     *
     * @since 4.11
     *
     * @param string $name The unique notice name.
     * @return NoticeInterface The notice for the name.
     */
    public function getNotice($name)
    {
        return $this->_getNotice($name);
    }

    /**
     * Get a notice instance by its unique identifier.
     *
     * @since 4.11
     *
     * @param string $name Unique name of the notice.
     * @return NoticeInterface The notice.
     */
    protected function _getNotice($name)
    {
        $serviceId = $this->_p($name);

        return $this->_getContainer()->get($serviceId);
    }

    /**
     * Determine whether or not a notice with the specified name exists.
     *
     * @since 4.11
     *
     * @param string $name The unique name of the notice.
     * @return bool True if a notice with the specified name exists; false otherwise.
     */
    protected function _hasNotice($name)
    {
        $id = $this->_p($name);

        return $this->_getContainer()->has($id);
    }

    /**
     * Gets the service ID for a notice name.
     *
     * @since 4.11
     *
     * @param string $noticeName The unique notice name.
     * @return string The service ID that corresponds to the given name.
     */
    protected function _p($noticeName)
    {
        return static::stringHadPrefix($noticeName)
            ? $noticeName
            : WPRSS_NOTICE_SERVICE_ID_PREFIX . $noticeName;
    }
}
