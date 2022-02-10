<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Defaults\PermissionDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;

/**
 * @property array $addons
 * @property string $capability
 * @property string $cron_event
 * @property array $db_version
 * @property array $defaults
 * @property string $export_key
 * @property string $file
 * @property string $id
 * @property string $languages
 * @property string $name
 * @property string $paged_handle
 * @property string $paged_query_var
 * @property string $post_type
 * @property string $prefix
 * @property array $session
 * @property \GeminiLabs\SiteReviews\Arguments $storage
 * @property string $taxonomy
 * @property string $version
 * @property string $testedTo;
 */
final class Application extends Container
{
    use Plugin;
    use Session;
    use Storage;

    const DB_VERSION = '1.1';
    const EXPORT_KEY = '_glsr_export';
    const ID = 'site-reviews';
    const PAGED_HANDLE = 'pagination_request';
    const PAGED_QUERY_VAR = 'reviews-page'; // filtered
    const POST_TYPE = 'site-review';
    const PREFIX = 'glsr_';
    const TAXONOMY = 'site-review-category';

    /**
     * @var array
     */
    protected $addons = [];

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $addonId
     * @return false|\GeminiLabs\SiteReviews\Addons\Addon
     */
    public function addon($addonId)
    {
        if (isset($this->addons[$addonId])) {
            return $this->addons[$addonId];
        }
        return false;
    }

    /**
     * @param string $capability
     * @param mixed ...$args
     * @return bool
     */
    public function can($capability, ...$args)
    {
        return $this->make(Role::class)->can($capability, ...$args);
    }

    /**
     * @param bool $networkDeactivating
     * @return void
     * @callback register_deactivation_hook
     */
    public function deactivate($networkDeactivating)
    {
        $this->make(Install::class)->deactivate($networkDeactivating);
    }

    /**
     * @param string $page
     * @param string $tab
     * @return string
     */
    public function getPermission($page = '', $tab = 'index')
    {
        $fallback = 'edit_posts';
        $permissions = $this->make(PermissionDefaults::class)->defaults();
        $permission = Arr::get($permissions, $page, $fallback);
        if (is_array($permission)) {
            $permission = Arr::get($permission, $tab, $fallback);
        }
        return empty($permission) || !is_string($permission)
            ? $fallback
            : $permission;
    }

    /**
     * @param string $page
     * @param string $tab
     * @return bool
     */
    public function hasPermission($page = '', $tab = 'index')
    {
        $isAdmin = $this->isAdmin();
        return !$isAdmin || $this->can($this->getPermission($page, $tab));
    }

    /**
     * @return void
     */
    public function init()
    {
        // Ensure the custom database tables exist, this is needed in cases
        // where the plugin has been updated instead of activated.
        $version = get_option(static::PREFIX.'db_version');
        if (empty($version)) {
            $this->make(Install::class)->run();
        } elseif ('1.1' === $version) { // @todo remove this in v5.12.0
            if (!$this->make(SqlSchema::class)->columnExists('ratings', 'terms')) {
                $this->make(Migrate::class)->reset();
                update_option(static::PREFIX.'db_version', '1.0');
            }
        }
        $this->make(Hooks::class)->run();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return (is_admin() || is_network_admin()) && !wp_doing_ajax();
    }

    /**
     * @param object $addon
     * @return void
     */
    public function register($addon)
    {
        try {
            $reflection = new \ReflectionClass($addon); // make sure that the class exists
            $addon = $reflection->getName();
            $this->addons[$addon::ID] = $addon;
            $this->singleton($addon); // this goes first!
            $this->alias($addon::ID, $this->make($addon)); // @todo for some reason we have to link an alias to an instantiated class
            $this->make($addon)->init();
        } catch (\ReflectionException $e) {
            glsr_log()->error('Attempted to register an invalid addon.');
        }
    }

    /**
     * @return void
     */
    public function storeDefaults()
    {
        if (empty($this->defaults)) {
            $defaults = $this->make(DefaultsManager::class)->get();
            $this->defaults = $this->filterArray('get/defaults', $defaults);
        }
        if (empty(get_option(OptionManager::databaseKey()))) {
            update_option(OptionManager::databaseKey(), $this->defaults);
        }
    }
}
