<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\Sinergi\BrowserDetector\Browser;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use WP_Debug_Data;

class SystemInfo
{
    const PAD = 40;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return string
     */
    public function get()
    {
        $data = $this->data();
        $details = [ // order is intentional
            'plugin' => $this->getPluginDetails(),
            'addon' => $this->getAddonDetails(),
            'reviews' => $this->getReviewDetails(),
            'browser' => $this->getBrowserDetails(),
            'database' => $this->getDatabaseDetails($data),
            'server' => $this->getServerDetails($data),
            'wordpress' => $this->getWordpressDetails($data),
            'mu-plugins' => $this->getMustUsePluginDetails(),
            'network-plugins' => $this->getNetworkActivatedPluginDetails(),
            'activated-plugins' => $this->getActivePluginDetails(),
            'inactive-plugins' => $this->getInactivePluginDetails(),
            'setting' => $this->getPluginSettings(),
        ];
        $systemInfo = array_reduce(array_keys($details), function ($carry, $key) use ($details) {
            if (empty(Arr::get($details[$key], 'values'))) {
                return $carry;
            }
            $hook = 'system/'.Str::dashCase($key);
            $title = strtoupper(Arr::get($details[$key], 'title'));
            $values = glsr()->filterArray($hook, Arr::get($details[$key], 'values'));
            return $carry.$this->implode($title, $values);
        });
        return trim($systemInfo);
    }

    /**
     * @return array
     */
    public function getActivePluginDetails()
    {
        $plugins = get_plugins();
        $active = glsr(OptionManager::class)->getWP('active_plugins', [], 'array');
        $inactive = array_diff_key($plugins, array_flip($active));
        $activePlugins = $this->normalizePluginList(array_diff_key($plugins, $inactive));
        return [
            'title' => 'Activated Plugins',
            'values' => $activePlugins,
        ];
    }

    /**
     * @return array
     */
    public function getAddonDetails()
    {
        $details = glsr()->filterArray('addon/system-info', []);
        ksort($details);
        return [
            'title' => 'Addon Details',
            'values' => $details,
        ];
    }

    /**
     * @return array
     */
    public function getBrowserDetails()
    {
        $browser = new Browser();
        $name = esc_attr($browser->getName());
        $userAgent = esc_attr($browser->getUserAgent()->getUserAgentString());
        $version = esc_attr($browser->getVersion());
        return [
            'title' => 'Browser Details',
            'values' => [
                'Browser Name' => sprintf('%s %s', $name, $version),
                'Browser UA' => $userAgent,
            ],
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getDatabaseDetails($data)
    {
        $database = Arr::get($data, 'wp-database');
        $engines = glsr(SqlSchema::class)->tableEngines($removeDbPrefix = true);
        foreach ($engines as $engine => $tables) {
          $engines[$engine] = sprintf('%s (%s)', $engine, implode('|', $tables));
        }
        return [
            'title' => 'Database Details',
            'values' => [
                'Charset' => Arr::get($database, 'database_charset'),
                'Collation' => Arr::get($database, 'database_collate'),
                'Extension' => Arr::get($database, 'extension'),
                'Table Engines' => implode(', ', $engines),
                'Version (client)' => Arr::get($database, 'client_version'),
                'Version (server)' => Arr::get($database, 'server_version'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getInactivePluginDetails()
    {
        $active = glsr(OptionManager::class)->getWP('active_plugins', [], 'array');
        $inactive = $this->normalizePluginList(array_diff_key(get_plugins(), array_flip($active)));
        $networkActivated = $this->getNetworkActivatedPluginDetails();
        $networkActivated = Arr::consolidate(Arr::get($networkActivated, 'values'));
        $inactivePlugins = Helper::ifTrue(empty($networkActivated), $inactive, array_diff($inactive, $networkActivated));
        return [
            'title' => 'Inactive Plugins',
            'values' => $inactivePlugins,
        ];
    }

    /**
     * @return array
     */
    public function getMustUsePluginDetails()
    {
        $plugins = get_mu_plugins();
        $muplugins = Helper::ifTrue(empty($plugins), [], function () use ($plugins) {
            return $this->normalizePluginList($plugins);
        });
        return [
            'title' => 'Must-Use Plugins',
            'values' => $muplugins,
        ];
    }

    /**
     * @return array
     */
    public function getNetworkActivatedPluginDetails()
    {
        $plugins = Arr::consolidate(get_site_option('active_sitewide_plugins', []));
        if (!is_multisite() || empty($plugins)) {
            return [];
        }
        $networkPlugins = $this->normalizePluginList(array_intersect_key(get_plugins(), $plugins));
        return [
            'title' => 'Network Activated Plugins',
            'values' => $networkPlugins,
        ];
    }

    /**
     * @return array
     */
    public function getPluginDetails()
    {
        require_once ABSPATH.'/wp-admin/includes/plugin.php';
        return [
            'title' => 'Plugin Details',
            'values' => [
                'Console Level' => glsr(Console::class)->humanLevel(),
                'Console Size' => glsr(Console::class)->humanSize(),
                'Database Version' => glsr(OptionManager::class)->getWP(glsr()->prefix.'db_version'),
                'Last Migration Run' => glsr(Date::class)->localized(glsr(OptionManager::class)->get('last_migration_run'), 'unknown'),
                'Network Activated' => Helper::ifTrue(is_plugin_active_for_network(plugin_basename(glsr()->file)), 'Yes', 'No'),
                'Version' => sprintf('%s (%s)', glsr()->version, glsr(OptionManager::class)->get('version_upgraded_from')),
            ],
        ];
    }

    /**
     * @return array
     */
    public function getPluginSettings()
    {
        $settings = glsr(OptionManager::class)->getArray('settings');
        $settings = Arr::flatten($settings, true);
        $settings = $this->purgeSensitiveData($settings);
        ksort($settings);
        $details = [];
        foreach ($settings as $key => $value) {
            if (Str::startsWith('strings', $key) && Str::endsWith('id', $key)) {
                continue;
            }
            $value = htmlspecialchars(trim(preg_replace('/\s\s+/u', '\\n', $value)), ENT_QUOTES, 'UTF-8');
            $details[$key] = $value;
        }
        return [
            'title' => 'Plugin Settings',
            'values' => $details,
        ];
    }

    /**
     * @return array
     */
    public function getReviewDetails()
    {
        $ratings = $this->ratingCounts();
        $reviews = $this->reviewCounts();
        $values = array_merge($ratings, $reviews);
        ksort($values);
        return [
            'title' => 'Review Details',
            'values' => $values,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getServerDetails($data)
    {
        $media = Arr::get($data, 'wp-media');
        $server = Arr::get($data, 'wp-server');
        return [
            'title' => 'Server Details',
            'values' => [
                'cURL Version' => Arr::get($server, 'curl_version'),
                'Display Errors' => Helper::ifEmpty($this->getIni('display_errors'), 'No'),
                'File Uploads' => Arr::get($media, 'file_uploads'),
                'GD version' => Arr::get($media, 'gd_version'),
                'Ghostscript version' => Arr::get($media, 'ghostscript_version'),
                'Host Name' => $this->getHostName(),
                'ImageMagick version' => Arr::get($media, 'imagemagick_version'),
                'Intl' => Helper::ifEmpty(phpversion('intl'), 'No'),
                'IPv6' => var_export(defined('AF_INET6'), true),
                'Max Effective File Size' => Arr::get($media, 'max_effective_size'),
                'Max Execution Time' => Arr::get($server, 'time_limit'),
                'Max File Uploads' => Arr::get($media, 'max_file_uploads'),
                'Max Input Time' => Arr::get($server, 'max_input_time'),
                'Max Input Variables' => Arr::get($server, 'max_input_variables'),
                'Memory Limit' => Arr::get($server, 'memory_limit'),
                'Multibyte' => Helper::ifEmpty(phpversion('mbstring'), 'No'),
                'Permalinks Supported' => Arr::get($server, 'pretty_permalinks'),
                'PHP Version' => Arr::get($server, 'php_version'),
                'Post Max Size' => Arr::get($server, 'php_post_max_size'),
                'SAPI' => Arr::get($server, 'php_sapi'),
                'Sendmail' => $this->getIni('sendmail_path'),
                'Server Architecture' => Arr::get($server, 'server_architecture'),
                'Server Software' => Arr::get($server, 'httpd_software'),
                'SUHOSIN Installed' => Arr::get($server, 'suhosin'),
                'Upload Max Filesize' => Arr::get($server, 'upload_max_filesize'),
            ],
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getWordpressDetails($data)
    {
        $constants = Arr::get($data, 'wp-constants');
        $wordpress = Arr::get($data, 'wp-core');
        return [
            'title' => 'WordPress Configuration',
            'values' => [
                'Email Domain' => substr(strrchr(glsr(OptionManager::class)->getWP('admin_email'), '@'), 1),
                'Environment' => Arr::get($wordpress, 'environment_type'),
                'Hidden From Search Engines' => Arr::get($wordpress, 'blog_public'),
                'Home URL' => Arr::get($wordpress, 'home_url'),
                'HTTPS' => Arr::get($wordpress, 'https_status'),
                'Language (site)' => Arr::get($wordpress, 'site_language'),
                'Language (user)' => Arr::get($wordpress, 'user_language'),
                'Multisite' => Arr::get($wordpress, 'multisite'),
                'Page For Posts ID' => glsr(OptionManager::class)->getWP('page_for_posts'),
                'Page On Front ID' => glsr(OptionManager::class)->getWP('page_on_front'),
                'Permalink Structure' => Arr::get($wordpress, 'permalink'),
                'Post Stati' => implode(', ', get_post_stati()),
                'Remote Post' => glsr(Cache::class)->getRemotePostTest(),
                'SCRIPT_DEBUG' => Arr::get($constants, 'SCRIPT_DEBUG'),
                'Show On Front' => glsr(OptionManager::class)->getWP('show_on_front'),
                'Site URL' => Arr::get($wordpress, 'site_url'),
                'Theme (active)' => sprintf('%s v%s', Arr::get($data, 'wp-active-theme.name'), Arr::get($data, 'wp-active-theme.version')),
                'Theme (parent)' => Arr::get($data, 'wp-parent-theme.name', 'No'),
                'Timezone' => Arr::get($wordpress, 'timezone'),
                'User Count' => Arr::get($wordpress, 'user_count'),
                'Version' => Arr::get($wordpress, 'version'),
                'WP_CACHE' => Arr::get($constants, 'WP_CACHE'),
                'WP_DEBUG' => Arr::get($constants, 'WP_DEBUG'),
                'WP_DEBUG_DISPLAY' => Arr::get($constants, 'WP_DEBUG_DISPLAY'),
                'WP_DEBUG_LOG' => Arr::get($constants, 'WP_DEBUG_LOG'),
                'WP_MAX_MEMORY_LIMIT' => Arr::get($constants, 'WP_MAX_MEMORY_LIMIT'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function data()
    {
        $data = glsr(Cache::class)->getSystemInfo();
        array_walk($data, function (&$section) {
            $fields = Arr::consolidate(Arr::get($section, 'fields'));
            array_walk($fields, function (&$values) {
                $values = Arr::get($values, 'value');
            });
            $section = $fields;
        });
        return $data;
    }

    /**
     * @return string
     */
    protected function getHostName()
    {
        return sprintf('%s (%s)', $this->detectWebhostProvider(), Helper::getIpAddress());
    }

    /**
     * @param string $name
     * @param string $disabledValue
     * @return string
     */
    protected function getIni($name, $disabledValue = 'ini_get() is disabled.')
    {
        return Helper::ifTrue(!function_exists('ini_get'), $disabledValue, function () use ($name) {
            return ini_get($name);
        });
    }

    /**
     * @param string $title
     * @return string
     */
    protected function implode($title, array $details)
    {
        $strings = ['['.$title.']'];
        $padding = max(array_map('strlen', array_keys($details)));
        $padding = max([$padding, static::PAD]);
        foreach ($details as $key => $value) {
            $pad = $padding - (mb_strlen($key, 'UTF-8') - strlen($key)); // handle unicode characters
            $strings[] = is_string($key)
                ? sprintf('%s : %s', str_pad($key, $pad, '.'), $value)
                : ' - '.$value;
        }
        return implode(PHP_EOL, $strings).PHP_EOL.PHP_EOL;
    }

    /**
     * @return string
     */
    protected function detectWebhostProvider()
    {
        $checks = [
            '.accountservergroup.com' => 'Site5',
            '.gridserver.com' => 'MediaTemple Grid',
            '.inmotionhosting.com' => 'InMotion Hosting',
            '.ovh.net' => 'OVH',
            '.pair.com' => 'pair Networks',
            '.stabletransit.com' => 'Rackspace Cloud',
            '.stratoserver.net' => 'STRATO',
            '.sysfix.eu' => 'SysFix.eu Power Hosting',
            'bluehost.com' => 'Bluehost',
            'DH_USER' => 'DreamHost',
            'Flywheel' => 'Flywheel',
            'ipagemysql.com' => 'iPage',
            'ipowermysql.com' => 'IPower',
            'localhost:/tmp/mysql5.sock' => 'ICDSoft',
            'mysqlv5' => 'NetworkSolutions',
            'PAGELYBIN' => 'Pagely',
            'secureserver.net' => 'GoDaddy',
            'WPE_APIKEY' => 'WP Engine',
        ];
        foreach ($checks as $key => $value) {
            if (!$this->isWebhostCheckValid($key)) {
                continue;
            }
            return $value;
        }
        return implode(',', array_filter([DB_HOST, filter_input(INPUT_SERVER, 'SERVER_NAME')]));
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isWebhostCheckValid($key)
    {
        return defined($key)
            || filter_input(INPUT_SERVER, $key)
            || Str::contains($key, filter_input(INPUT_SERVER, 'SERVER_NAME'))
            || Str::contains($key, DB_HOST)
            || Str::contains($key, php_uname());
    }

    /**
     * @return array
     */
    protected function normalizePluginList(array $plugins)
    {
        $plugins = array_map(function ($plugin) {
            return sprintf('%s v%s', Arr::get($plugin, 'Name'), Arr::get($plugin, 'Version'));
        }, $plugins);
        natcasesort($plugins);
        return array_flip($plugins);
    }

    /**
     * @return array
     */
    protected function purgeSensitiveData(array $settings)
    {
        $keys = glsr()->filterArray('addon/system-info/purge', [
            'licenses.' => 8,
            'submissions.recaptcha.key' => 0,
            'submissions.recaptcha.secret' => 0,
        ]);
        array_walk($settings, function (&$value, $setting) use ($keys) {
            foreach ($keys as $key => $preserve) {
                if (!is_string($key)) { // @compat for older addons
                    $key = $preserve;
                    $preserve = 0;
                }
                if (Str::startsWith($key, $setting) && !empty($value)) {
                    $preserve = Cast::toInt($preserve);
                    $value = substr($value, -$preserve, $preserve);
                    $value = str_pad($value, 13, '*', STR_PAD_LEFT);
                    break;
                }
            }
        });
        return $settings;
    }

    /**
     * @return array
     */
    protected function ratingCounts()
    {
        $ratings = glsr(Query::class)->ratings();
        $results = [];
        foreach ($ratings as $type => $counts) {
            if (is_array($counts)) {
                $label = sprintf('Type: %s', $type);
                $results[$label] = array_sum($counts).' ('.implode(', ', $counts).')';
                continue;
            }
            glsr_log()->error('$ratings is not an array, possibly due to incorrectly imported reviews.')
                ->debug($counts)
                ->debug($ratings);
        }
        if (empty($results)) {
            return ['Type: local' => 'No reviews'];
        }
        return $results;
    }

    /**
     * @return array
     */
    protected function reviewCounts()
    {
        $reviews = array_filter((array) wp_count_posts(glsr()->post_type));
        $counts = array_sum($reviews);
        foreach ($reviews as $status => &$num) {
            $num = sprintf('%s: %d', $status, $num);
        }
        $results = $counts.' ('.implode(', ', $reviews).')';
        return ['Reviews' => $results];
    }
}
