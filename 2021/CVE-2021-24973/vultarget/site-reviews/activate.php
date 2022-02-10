<?php

defined('ABSPATH') || die;

/**
 * Check for minimum system requirments on plugin activation.
 * @version 5.5.0
 */
class GL_Plugin_Check_v5
{
    const MIN_PHP_VERSION = '5.6.20';
    const MIN_WORDPRESS_VERSION = '5.5.0';

    /**
     * @var array
     */
    public $versions;

    /**
     * @var string
     */
    protected $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = realpath($file);
        $versionRequirements = get_file_data($this->file, [
            'php' => 'Requires PHP',
            'wordpress' => 'Requires at least',
        ]);
        $this->versions = wp_parse_args(array_filter($versionRequirements), [
            'php' => static::MIN_PHP_VERSION,
            'wordpress' => static::MIN_WORDPRESS_VERSION,
        ]);
    }

    /**
     * @return bool
     */
    public function canProceed()
    {
        if ($this->isValid()) {
            return true;
        }
        add_action('activated_plugin', [$this, 'deactivate']);
        add_action('admin_notices', [$this, 'deactivate']);
        return false;
    }

    /**
     * @return bool
     */
    public function isPhpValid()
    {
        return version_compare(PHP_VERSION, $this->versions['php'], '>=');
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isPhpValid() && $this->isWpValid();
    }

    /**
     * @return bool
     */
    public function isWpValid()
    {
        global $wp_version;
        return version_compare($wp_version, $this->versions['wordpress'], '>=');
    }

    /**
     * @param string $plugin
     * @return void
     */
    public function deactivate($plugin)
    {
        if ($this->isValid()) {
            return;
        }
        $pluginSlug = plugin_basename($this->file);
        if ($plugin == $pluginSlug) {
            $this->redirect(); //exit
        }
        $pluginData = get_file_data($this->file, ['name' => 'Plugin Name'], 'plugin');
        deactivate_plugins($pluginSlug);
        $this->printNotice($pluginData['name']);
    }

    /**
     * @return array
     */
    protected function getMessages()
    {
        return [
            'notice' => _x('The %s plugin was deactivated.', 'admin-text', 'site-reviews'),
            'php_version' => _x('PHP version', 'admin-text', 'site-reviews'),
            'rollback' => _x('You can use the %s plugin to restore %s to the previous version.', 'admin-text', 'site-reviews'),
            'update_php' => _x('Please contact your hosting provider or server administrator to upgrade the version of PHP on your server (your server is running PHP version %s), or try to find an alternative plugin.', 'admin-text', 'site-reviews'),
            'update_wp' => _x('Update WordPress', 'admin-text', 'site-reviews'),
            'wp_version' => _x('WordPress version', 'admin-text', 'site-reviews'),
            'wrong_version' => _x('This plugin requires %s or greater in order to work properly.', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @param string $pluginName
     * @return void
     */
    protected function printNotice($pluginName)
    {
        $noticeTemplate = '<div id="message" class="notice notice-error error is-dismissible"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>';
        $messages = $this->getMessages();
        $rollbackMessage = sprintf('<strong>'.$messages['rollback'].'</strong>', '<a href="https://wordpress.org/plugins/wp-rollback/">WP Rollback</a>', $pluginName);
        if (!$this->isPhpValid()) {
            printf($noticeTemplate,
                sprintf($messages['notice'], $pluginName),
                sprintf($messages['wrong_version'], $messages['php_version'].' '.$this->versions['php']),
                sprintf($messages['update_php'], PHP_VERSION).'</p><p>'.$rollbackMessage
            );
        } elseif (!$this->isWpValid()) {
            printf($noticeTemplate,
                sprintf($messages['notice'], $pluginName),
                sprintf($messages['wrong_version'], $messages['wp_version'].' '.$this->versions['wordpress']),
                $rollbackMessage.'</p><p>'.sprintf('<a href="%s">%s</a>', admin_url('update-core.php'), $messages['update_wp'])
            );
        }
    }

    /**
     * @return void
     */
    protected function redirect()
    {
        wp_safe_redirect(self_admin_url(sprintf('plugins.php?plugin_status=%s&paged=%s&s=%s',
            filter_input(INPUT_GET, 'plugin_status'),
            filter_input(INPUT_GET, 'paged'),
            filter_input(INPUT_GET, 's')
        )));
        exit;
    }
}
