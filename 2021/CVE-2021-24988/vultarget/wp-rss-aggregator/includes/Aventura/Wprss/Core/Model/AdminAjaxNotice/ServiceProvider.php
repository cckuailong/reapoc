<?php

namespace Aventura\Wprss\Core\Model\AdminAjaxNotice;

use Aventura\Wprss\Core\Plugin\Di\AbstractComponentServiceProvider;
use Aventura\Wprss\Core\Plugin\Di\ServiceProviderInterface;
use Interop\Container\ContainerInterface;
use Aventura\Wprss\Core\Component\AdminAjaxNotices;
use Aventura\Wprss\Core\Block\CallbackBlock;
use Aventura\Wprss\Core\Component\AdminHelper;
use Aventura\Wprss\Core\Model\CommandInterface;

/**
 * Provides services that represent admin notices.
 *
 * @since 4.11
 */
class ServiceProvider extends AbstractComponentServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    protected function _getServiceDefinitions()
    {
        return array(
            $this->_p('admin_ajax_notice_controller')   => array($this, '_createAdminAjaxNoticeController'),
            $this->_p('admin_ajax_notices')             => array($this, '_createAdminAjaxNotices'),
            $this->_p('command.is_wprss_page')          => array($this, '_createCommandIsWprssPage'),

            $this->_pn('more_features')                 => array($this, '_createMoreFeaturesNotice'),
            $this->_pn('deleting_feed_items')           => array($this, '_createDeletingFeedItemsNotice'),
            $this->_pn('bulk_feed_import')              => array($this, '_createBulkFeedImportNotice'),
            $this->_pn('settings_import_success')       => array($this, '_createSettingsImportSuccessNotice'),
            $this->_pn('settings_import_failed')        => array($this, '_createSettingsImportFailedNotice'),
            $this->_pn('debug_feeds_updating')          => array($this, '_createDebugFeedsUpdatingNotice'),
            $this->_pn('debug_feeds_reimporting')       => array($this, '_createDebugFeedsReimportingNotice'),
            $this->_pn('debug_cleared_log')             => array($this, '_createDebugClearedLogNotice'),
            $this->_pn('debug_settings_reset')          => array($this, '_createDebugSettingsResetNotice'),
            $this->_pn('blacklist_item_success')        => array($this, '_createBlacklistItemSuccessNotice'),
            $this->_pn('bulk_feed_activated')           => array($this, '_createBulkFeedActivatedNotice'),
            $this->_pn('bulk_feed_paused')              => array($this, '_createBulkFeedPausedNotice'),

            $this->_pn('addon_empty_license')           => array($this, '_createAddonEmptyLicenseNotice'),
            $this->_pn('addon_inactive_license')        => array($this, '_createAddonInactiveLicenseNotice'),
            $this->_pn('addon_expiring_license')        => array($this, '_createAddonExpiringLicenseNotice'),
            $this->_pn('generic_fallback')              => array($this, '_createGenericFallbackNotice'),
        );
    }

    /**
     * Creates an instance of the admin AJAX notice controller.
     *
     * @uses-filter wprss_admin_notice_collection_before_init To modify collection before initialization.
     * @uses-filter wprss_admin_notice_collection_after_init To modify collection after initialization.
     * @uses-action wprss_admin_exclusive_scripts_styles To enqueue the scripts for the collection.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p Deprecated.
     * @param array $config
     * @return \WPRSS_Admin_Notices
     */
    public function _createAdminAjaxNoticeController(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'setting_code'          => 'wprss_admin_notices',
            'id_prefix'             => 'wprss_',
            'text_domain'           => 'wprss'
        ));
        // Initialize collection
        $controller = new \WPRSS_Admin_Notices($config);
        $controller = apply_filters( \WPRSS_EVENT_PREFIX.'admin_notice_collection_before_init', $controller );
        $controller->init();
        $controller = apply_filters( \WPRSS_EVENT_PREFIX.'admin_notice_collection_after_init', $controller );

        return $controller;
    }

    /**
     * Creates an instance of the admin AJAX notices component.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return Component\AdminAjaxNotices
     */
    public function _createAdminAjaxNotices(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'plugin'            => $c->get($this->_p('plugin'))
        ));
        $service = new AdminAjaxNotices($config, $c);
        $this->_prepareComponent($service);

        return $service;
    }

    /**
     * Normalizes data of a notice.
     *
     * @since 4.11
     *
     * @param ContainerInterface $container DI container that will be used to retrieve notice controller.
     * @param array $data Data to normalize.
     * @return array Normalized data with defaults.
     */
    protected function _normalizeNoticeData(ContainerInterface $container, $data = array())
    {
        if (!isset($data['id']) && isset($data['content'])) {
            $data['id'] = $this->_hashNoticeContent($container, $data['content']);
        }

        return $data;
    }

    /**
     * Creates a notice that informs users about other features.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createMoreFeaturesNotice(ContainerInterface $c, $p = null, $config = null)
    {
        $notice = $this->_createNotice(array(
            'id'                    => 'more_features',
            'notice_type'           => NoticeInterface::TYPE_UPDATED,
            'condition'             => $this->_getCommandIsWprssPage($c),
            'content'               => $this->_autoParagraph($this->__('Did you know that you can get more RSS features? Excerpts, thumbnails, keyword filtering, importing into posts and more... ') .
                                       $this->__(array('Check out the <a target="_blank" href="%1$s"><strong>extensions</strong></a> page.', 'https://www.wprssaggregator.com/extensions')))
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs users that feed items are deleting in the background.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createDeletingFeedItemsNotice(ContainerInterface $c, $p = null, $config = null)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'deleting_feed_items',
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph($this->__('The feed items for this feed source are being deleted in the background.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that tells the user how many feed sources where successfully imported
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createBulkFeedImportNotice(ContainerInterface $c, $p = null, $config)
    {
        $me = $this;
        $import = $c->get($this->_p('bulk_source_import'));
        $notice = $this->_createNotice(array(
            'id'                => 'debug_reset_settings',
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => new CallbackBlock(array(), function() use ($me, $import) {
                return $me->_autoParagraph(
                    sprintf($me->__('Successfully imported <code>%1$s</code> feed sources.'), $import->getImportedSourcesCount())
                );
            }),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the settings import was successful.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createSettingsImportSuccessNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'settings_import_success',
            'notice_type'       => NoticeInterface::TYPE_UPDATED,
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph(__('Your settings were imported successfully', 'wprss')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the settings import failed.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createSettingsImportFailedNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'settings_import_failed',
            'notice_type'       => NoticeInterface::TYPE_ERROR,
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph($this->__('Invalid file or file size too big.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that all feed sources are updating.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createDebugFeedsUpdatingNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'debug_feeds_updating',
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph($this->__('Feeds are being updated in the background.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the feed items have been deleted and are being reimported.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createDebugFeedsReimportingNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'debug_feeds_reimporting',
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph($this->__('Feeds deleted and are being re-imported in the background.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the debug log has been cleared.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createDebugClearedLogNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'debug_cleared_log',
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph($this->__('The error log has been cleared.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the settings have been reset to default.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createDebugSettingsResetNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'debug_settings_reset',
            'condition'         => $this->_getCommandIsWprssPage($c),
            'content'           => $this->_autoParagraph($this->__('The plugin settings have been reset to default.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that an item has been successfully blacklisted.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createBlacklistItemSuccessNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice(array(
            'id'                => 'blacklist_item_success',
            'content'           => $this->_autoParagraph($this->__('The item was deleted successfully and added to the blacklist.')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the selected feed sources have been activated.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createBulkFeedActivatedNotice(ContainerInterface $c, $p = null, $config)
    {
        $helper = $c->get($this->_p('admin_helper'));
        $notice = $this->_createNotice(array(
            'id'                => 'bulk_feed_activated',
            'condition'         => $helper->createCommand(array($helper, 'isWprssPage')),
            'content'           => $this->_autoParagraph($this->__('The feed sources have been activated!')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that the selected feed sources have been paused.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createBulkFeedPausedNotice(ContainerInterface $c, $p = null, $config)
    {
        $helper = $c->get($this->_p('admin_helper'));
        $notice = $this->_createNotice(array(
            'id'                => 'bulk_feed_paused',
            'condition'         => $helper->createCommand(array($helper, 'isWprssPage')),
            'content'           => $this->_autoParagraph($this->__('The feed sources have been paused!')),
            'dismiss_mode'      => NoticeInterface::DISMISS_MODE_FRONTEND,
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that an addon license has been saved empty.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createAddonEmptyLicenseNotice(ContainerInterface $c, $p = null, $config)
    {
        $addonId = isset($config['addon_id'])
            ? $config['addon_id']
            : null;
        $addonName = isset($config['addon_name'])
            ? $config['addon_name']
            : null;
        $licenseSettings = isset($config['settings'])
            ? $config['settings']
            : wprss_licensing_get_settings_controller();

        $helper = $c->get($this->_p('admin_helper'));
        $me = $this;

        $notice = $this->_createNotice(array(
            'id'                => sprintf('addon_empty_license_%s', $addonId),
            'notice_type'       => NoticeInterface::TYPE_ERROR,
            'condition'         => $helper->createCommand(array($licenseSettings, 'emptyLicenseKeyNoticeCondition')),
            'content'           => new CallbackBlock(array(), function() use ($addonName, &$me) {
                return $me->_autoParagraph(
                    sprintf(
                        __(
                            'Remember to <a href="%1$s">enter your license key</a> for the <strong>WP RSS Aggregator - %2$s</strong> add-on to benefit from updates and support.',
                            'wprss'
                        ),
                        esc_attr( admin_url( 'edit.php?post_type=wprss_feed&page=wprss-aggregator-settings&tab=licenses_settings' ) ),
                        $addonName
                    )
                );
            }),
            'addon'             => $addonId
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that an addon license is inactive.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createAddonInactiveLicenseNotice(ContainerInterface $c, $p = null, $config)
    {
        $addonId = isset($config['addon_id'])
            ? $config['addon_id']
            : null;
        $addonName = isset($config['addon_name'])
            ? $config['addon_name']
            : null;
        $licenseSettings = isset($config['settings'])
            ? $config['settings']
            : wprss_licensing_get_settings_controller();

        $helper = $c->get($this->_p('admin_helper'));
        $me = $this;

        $notice = $this->_createNotice(array(
            'id'                => sprintf('addon_saved_inactive_license_%s', $addonId),
            'notice_type'       => NoticeInterface::TYPE_ERROR,
            'condition'         => $helper->createCommand(array($licenseSettings, 'savedInactiveLicenseNoticeCondition')),
            'content'           => new CallbackBlock(array(), function() use ($addonName, &$me) {
                return $me->_autoParagraph(
                    sprintf(
                        __(
                            'The license key for the <strong>WP RSS Aggregator - %2$s</strong> add-on is saved but not activated. In order to benefit from updates and support, it must be <a href="%1$s">activated</a>.',
                            'wprss'
                        ),
                        esc_attr( admin_url( 'edit.php?post_type=wprss_feed&page=wprss-aggregator-settings&tab=licenses_settings' ) ),
                        $addonName
                    )
                );
            }),
            'addon'             => $addonId
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that informs the user that an addon license will soon expire.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return NoticeInterface
     */
    public function _createAddonExpiringLicenseNotice(ContainerInterface $c, $p = null, $config)
    {
        $addonId = isset($config['addon_id'])
            ? $config['addon_id']
            : null;
        $addonName = isset($config['addon_name'])
            ? $config['addon_name']
            : null;
        $licenseSettings = isset($config['settings'])
            ? $config['settings']
            : wprss_licensing_get_settings_controller();
        $year = isset($config['year'])
            ? $config['year']
            : date('Y');

        $helper = $c->get($this->_p('admin_helper'));
        $me = $this;

        $notice = $this->_createNotice(array(
            'id'                => sprintf('addon_empty_license_%1$s_%2$s', $addonId, $year),
            'notice_type'       => NoticeInterface::TYPE_ERROR,
            'condition'         => $helper->createCommand(array($licenseSettings, 'soonToExpireLicenseNoticeCondition')),
            'content'           => new CallbackBlock(array(), function() use ($addonName, &$me) {
                return $me->_autoParagraph(
                    sprintf(
                        __(
                            'The license for the <strong>WP RSS Aggregator - %2$s</strong> add-on is about to expire. <a href="%1$s">Please renew it</a> to keep receiving updates and benefit from support.',
                            'wprss'
                        ),
                        esc_attr( 'https://docs.wprssaggregator.com/renewing-your-license/' ),
                        $addonName
                    )
                );
            }),
            'addon'             => $addonId
        ), $c);

        return $notice;
    }

    /**
     * Creates a notice that can be used to wrap non-DI compliant notices.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p Deprecated
     * @param array $config Configuration for this service.
     * @return NoticeInterface The new notice.
     */
    public function _createGenericFallbackNotice(ContainerInterface $c, $p = null, $config)
    {
        $notice = $this->_createNotice($this->_normalizeConfig($config, array(
            'notice_type'       => NoticeInterface::TYPE_UPDATED,
            'content'           => '',
            'addon'             => '',
        )), $c);

        return $notice;
    }

    /**
     * Crates a new admin notice instance.
     *
     * @since 4.11
     *
     * @return NoticeInterface
     */
    protected function _createNotice($data, ContainerInterface $container)
    {
        $data = $this->_normalizeNoticeData($container, $data);
        $notice = new AdminAjaxNotice($data);

        return $notice;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getServices()
    {
        return $this->_getServices();
    }
    
    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getServiceIdPrefix($id = null)
    {
        return $this->_p($id);
    }

    /**
     * Retrieve the prefix that is used by services that represent notices.
     *
     * @param string|null $id The ID to prefix, if not null.
     *
     * @since 4.11
     */
    protected function _pn($id = null)
    {
        $prefix = $this->_getNoticeServiceIdPrefix();
        return static::stringHadPrefix($id)
            ? $id
            : "{$prefix}{$id}";
    }

    /**
     * Retrieves the prefix applied to IDs of services that represent notices.
     *
     * @since 4.11
     *
     * @return string The prefix.
     */
    protected function _getNoticeServiceIdPrefix()
    {
        return $this->_getDataOrConst('notice_service_id_prefix');
    }

    /**
     * Creates a notice that informs the user that all feed sources are updating.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @return CommandInterface
     */
    public function _createCommandIsWprssPage(ContainerInterface $c)
    {
        $helper = $this->_getAdminHelper($c);
        $command = $this->_createCommand($c, array($helper, 'isWprssPage'));

        return $command;
    }

    /**
     * Creates a command that can be invoked like a function.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c The container which to use while creating the command.
     * @param array $config Data for the command.
     * @return CommandInterface The new command.
     */
    protected function _createCommand(ContainerInterface $c, $config = array())
    {
        $helper = $this->_getAdminHelper($c);
        $config = $this->_normalizeConfig($config, array());
        $command = $helper->createCommand($config);

        return $command;
    }

    /**
     * Retrieves the admin helper from the container.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c The container which has the admin helper.
     * @return AdminHelper The helper.
     */
    protected function _getAdminHelper(ContainerInterface $c)
    {
        return $c->get($this->_p('admin_helper'));
    }

    /**
     * Retrieves the command which can determine whether currently on WPRA page.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c The container which has the command.
     * @return AdminHelper The command.
     */
    protected function _getCommandIsWprssPage(ContainerInterface $c)
    {
        return $c->get($this->_p('command.is_wprss_page'));
    }

    /**
     * {@inheritdoc}
     *
     * Uses the translator retrieved from global container as default.
     *
     * This is because it's not possible to inject a translator created
     * in another service provider into this one, due to the way containers
     * are registered.
     *
     * @since 4.11
     */
    protected function _translate($text, $translator = null)
    {
        if (is_null($translator) && !$this->_getTranslator()) {
            $translator = wprss_wp_container()->get($this->_p('translator'));
        }

        return parent::_translate($text, $translator);
    }

    /**
     * Converts plain text paragraphs to HTML ones.
     *
     * @since 4.11
     *
     * @param string $text The text to add paragraphs to.
     * @return string The text with HTML paragraphs.
     */
    public function _autoParagraph($text)
    {
        return \wpautop($text);
    }

    /**
     * Computes a unique hash of the notice content.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c A container instance.
     * @param string|callable $content The content to hash.
     *
     * @return string The content hash.
     */
    public function _hashNoticeContent(ContainerInterface $c, $content) {
        $helper = $this->_getAdminHelper($c);
        $hash = \is_callable($content)
                ? $helper->hashCallable($content)
                : $helper->hashScalar((string) $content);

        return $hash;
    }
}
