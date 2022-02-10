<?php

namespace Aventura\Wprss\Core\Model;

use Aventura\Wprss\Core;

/**
 * Something that can be used as an assets controller.
 *
 * @since 4.8.1
 */
abstract class AssetsAbstract extends Core\Plugin\ComponentAbstract implements AssetsInterface
{
    /** @since 4.8.1 */
    const HANDLE_PREFIX = '';

    /** @since 4.8.1 */
    const ASSET_TYPE_STYLE = 'style';
    /** @since 4.8.1 */
    const ASSET_TYPE_SCRIPT = 'script';

    /** @since 4.8.1 */
    protected static $_assetTypes = array(
        self::ASSET_TYPE_STYLE      => self::ASSET_TYPE_STYLE,
        self::ASSET_TYPE_SCRIPT     => self::ASSET_TYPE_SCRIPT
    );

    /** @since 4.8.1 */
    protected $_assets = array();

    /**
     * @since 4.8.1
     */
    public function hook()
    {
        $this->_hook();
        parent::hook();
    }

    /**
     * @since 4.8.1
     */
    protected function _hook() {
        $this->on('!wp_enqueue_scripts', array($this, 'enqueuePublicStyles'));
        $this->on('!wp_enqueue_scripts', array($this, 'enqueuePublicScripts'));
        $this->on('!admin_enqueue_scripts', array($this, 'enqueueAdminStyles'));
        $this->on('!admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
        return $this;
    }

    /**
     * Regisger a stylesheet.
     *
     * @since 4.8.1
     * @see wp_enqueue_style()
     * @param string $handle The resource handle of the style.
     * @param string $url The stylesheet URL. If not absolute will be relative to the base CSS URI;
     *  See {@see getCssUri()}.
     * @param array|null $dependencies One or many handles of other resources, on which this style depends.
     * @param string|null $version The version number of this stylesheet. Changing version number will cause the
     *  user agents to flush the resource's cache.
     *  Default: The version of the plugin, to which this component belongs.
     * @param string|null $media The media, on which this stylesheed should have effect.
     *  Default: 'all'.
     * @return AssetsAbstract
     */
    protected function _registerStyle($handle, $url, $dependencies = null, $version = null, $media = null, $allowOverwrite = false)
    {
        return $this->register(self::ASSET_TYPE_STYLE, array(
            'handle'        => $handle,
            'version'       => $version,
            'uri'           => $url,
            'media'         => $media
        ), $dependencies, $allowOverwrite);
    }

    /**
     * Register a script.
     *
     * @since 4.8.1
     * @see wp_enqueue_script()
     * @param string $handle The resource handle of the script.
     * @param string $url The script URL. If not absolute will be relative to the base JS URI;
     *  See {@see getJsUri()}.
     * @param array|null $dependencies One or many handles of other resources, on which this script depends.
     * @param string|null $version The version number of this script. Changing version number will cause the
     *  user agents to flush the resource's cache.
     *  Default: The version of the plugin, to which this component belongs.
     * @param bool|null $inFooter Whether or not the script should be in the footer.
     * @param bool $allowOverwrite Whether or not the asset should still be enqueued even if this handle is already registered.
     * @return AssetsAbstract
     */
    protected function _registerScript($handle, $url, $dependencies = null, $version = null, $inFooter = false, $allowOverwrite = false)
    {
        return $this->register(self::ASSET_TYPE_SCRIPT, array(
            'handle'        => $handle,
            'version'       => $version,
            'uri'           => $url,
            'in_footer'     => $inFooter
        ), $dependencies, $allowOverwrite);
    }

    /**
     * Registers an asset.
     *
     * @since 4.8.1
     * @param string $type The type of the asset.
     * @param array $data Data of the asset. Following keys supported:
     *  'handle'*, 'uri'*, 'version', 'in_footer' (SCRIPT), 'media'(STYLE)
     *   The 'handle' value will be prefixed, unless overridden/
     *   The 'uri' value will be made absolute, unless already absolute.
     * @param array|null $dependencies Handles of asset that this asset depends on, if any.
     * @throws Core\Exception If invalid 'type' value, or 'handle' or 'uri' not supplied.
     */
    public function register($type, $data, $dependencies = null, $allowOverwrite = false)
    {
        $type = trim($type);
        if (!static::hasAssetType($type)) {
            throw $this->exception(array('Could not register asset of type "%1$s": Type is invalid, please use one of [%2$s]',
                $type,
                implode(', ', static::getAssetTypes())));
        }

        // Default version
        if (is_null($data['version'])) {
            $data['version'] = $this->getPlugin()->getVersion();
        }
        // Default dependencies
        if (is_null($dependencies)) {
            $dependencies = array();
        }
        $dependencies = (array)$dependencies;
        // Must provide handle
        if (!isset($data['handle'])) {
            throw $this->exception(array('Could not register asset of type "%1$s": Handle must be provided', $type));
        }
        // Must provide uri
        if (!isset($data['uri'])) {
            throw $this->exception(array('Could not register asset "%2$s" of type "%1$s": URI must be provided', $type, $data['handle']));
        }

        // Normalizing handle
        if (!static::stringHadPrefix($data['handle'])) {
            $data['handle'] = $this->getHandlePrefix($data['handle']);
        }
        // Normalizing stylesheet
        if (!static::isUriAbsolute($data['uri'])) {
            switch ($type) {
                case static::ASSET_TYPE_STYLE:
                    $data['uri'] = $this->getCssUri($data['uri']);
                    break;

                case static::ASSET_TYPE_SCRIPT:
                    $data['uri'] = $this->getJsUri($data['uri']);
                    break;
            }
        }

        return $this->_register($type, $data, $dependencies, $allowOverwrite);
    }

    /**
     * Registers an asset.
     *
     * @since 4.8.1
     * @param string $type The type of the asset.
     * @param array $data Data of the asset. Following keys supported:
     *  'handle'*, 'uri'*, 'version', 'in_footer' (SCRIPT), 'media'(STYLE)
     *   The 'handle' value will be prefixed, unless overridden/
     *   The 'uri' value will be made absolute, unless already absolute.
     * @param array|null $dependencies Handles of asset that this asset depends on, if any.
     * @throws Core\Exception If invalid 'type' value, or 'handle' or 'uri' not supplied.
     */
    protected function _register($type, $data, array $dependencies, $allowOverwrite = false)
    {
        switch ($type) {
            case static::ASSET_TYPE_SCRIPT:
                // Default value
                if (!isset($data['in_footer'])) {
                    $data['in_footer'] = false;
                }

                return $this->_addAsset($type, $data, $dependencies, $allowOverwrite);
                break;

            case static::ASSET_TYPE_STYLE:
                // Default value
                if (!isset($data['media'])) {
                    $data['media'] = 'all';
                }

                return $this->_addAsset($type, $data, $dependencies, $allowOverwrite);
                break;
        }

        return null;
    }

    /**
     * Add an asset to asset list.
     *
     * @param string $type The type of the asset.
     * @param array $data The asset data.
     * @param array $dependencies The dependencies of the asset, if any.
     * @param bool $allowOverride If true, and the handle already registered, it will be replaced.
     * @return string The handle of the added asset.
     */
    protected function _addAsset($type, $data, array $dependencies, $allowOverride = false)
    {
        if (isset($this->_assets[$type]) && $allowOverride) {
            return null;
        }
        
        $handle = isset($data['handle']) ? $data['handle'] : $this->_getUniqueHandle();
        $data['type']  = $type;
        $data['dependencies'] = $dependencies;
        $this->_assets[$handle] = $data;
        return $handle;
    }

    /**
     * Generate a unique asset handle.
     *
     * @since 4.8.1
     * @return string A unique, prefixed asset handle.
     */
    protected function _getUniqueHandle($uri = null)
    {
        if (is_null($uri)) {
            return uniqid($this->getHandlePrefix(), true);
        }

        return $this->getHandlePrefix(md5($uri));
    }

    /**
     * Retrieve asset data by handle.
     *
     * @since 4.8.1
     * @param string $handle The handle of the asset to get.
     * @return array The asset data if exists, null otherwise.
     */
    public function getAsset($handle)
    {
        return $this->hasAsset($handle) ? $this->_assets[$handle] : null;
    }

    /**
     * Get all registered assets.
     *
     * @since 4.8.1
     * @return array All assets, by handle.
     */
    public function getAssets() {
        return $this->_assets;
    }

    /**
     * Whether or not an asset is registered.
     *
     * @since 4.8.1
     * @param string $handle An asset handle.
     * @return bool True if asset with specified handle exists; false otherwise.
     */
    public function hasAsset($handle)
    {
        return isset($this->_assets[$handle]);
    }

    /**
     * Register an asset.
     *
     * @since 4.8.1
     * @param array $asset An array with asset data.
     * @return bool True if registered, false otherwise.
     */
    protected function _registerAsset($asset)
    {
            
        $type = $asset['type'];
        switch ($type) {
            case static::ASSET_TYPE_SCRIPT:
                wp_register_script(
                    (string)$asset['handle'],
                    (string)$asset['uri'],
                    (array)$asset['dependencies'],
                    (string)$asset['version'],
                    (bool)$asset['in_footer']
                );
                return true;
                break;

            case static::ASSET_TYPE_STYLE:
                wp_register_style(
                    (string)$asset['handle'],
                    (string)$asset['uri'],
                    (array)$asset['dependencies'],
                    (string)$asset['version'],
                    (string)$asset['media']
                );
                return true;
                break;
        }

        return false;
    }

    /**
     * Enqueue an asset by handle.
     *
     * @since 4.8.1
     * @param string $asset The asset handle.
     * @return bool True if enqueued, false otherwise.
     * @throws Core\Exception If no handle provided, or no asset registered for handle.
     */
    protected function _enqueueAsset($asset)
    {
        if(!is_string($asset)) {
            throw $this->exception('Cannot enqueue asset: An asset handle must be provided');
        }
        
        if (!($asset = $this->getAsset($asset))) {
            throw $this->exception(array('Could not enqueue asset "%1$s": No asset registered with that handle'));
        }
        $type = $asset['type'];

        switch ($type) {
            case static::ASSET_TYPE_SCRIPT:
                wp_enqueue_script(
                    (string)$asset['handle'],
                    (string)$asset['uri'],
                    (array)$asset['dependencies'],
                    (string)$asset['version'],
                    (bool)$asset['in_footer']
                );
                return true;
                break;

            case static::ASSET_TYPE_STYLE:
                wp_enqueue_style(
                    (string)$asset['handle'],
                    (string)$asset['uri'],
                    (array)$asset['dependencies'],
                    (string)$asset['version'],
                    (string)$asset['media']
                );
                return true;
                break;
        }

        return false;
    }

    /**
     * Register a stylesheet.
     *
     * @since 4.8.1
     * @param string $handle The unique asset handle.
     * @param string $uri The URI of the asset. If relative, it will be appended to the value of {@see getCssUri()}.
     * @param array|null $dependencies List of dependencies for the assed. If null, empty array will be used.
     * @param string|null $version The version of the asset. If null, the plugin version will be used.
     * @param string|null $media The CSS media type.
     *  If null, 'all' will be used.
     * @param bool|null $allowOverwrite If true, and the handle already exists, it will be overwritten.
     * @return string|null The asset handle if regisered; otherwise null.
     */
    public function registerStyle($handle, $uri, $dependencies = null, $version = null, $media = null, $allowOverwrite = false)
    {
        return $this->_registerStyle($handle, $uri, $dependencies, $version, $media, $allowOverwrite);
    }

    /**
     * Enqueue a registered style by handle.
     *
     * @since 4.8.1
     * @param string $handle The handle of the style to enqueue.
     * @return AssetsAbstract This instance.
     * @throws Core\Exception If the handle isn't registered, the handle type is not registered or is not of a style.
     */
    public function enqueueStyle($handle)
    {
        // Normalizing handle
        if (!static::stringHadPrefix($handle)) {
            $handle = $this->getHandlePrefix($handle);
        }
        if (!($asset = $this->getAsset($handle))) {
            throw $this->exception(array('Could not enqueue script "%1$s": Register the script first', $handle));
        }
        if (!$this->hasAssetType($asset['type'])) {
            throw $this->exception(array('Could not enqueue style "%1$s": "%2$s" is not a registered asset type', $handle, $asset['type']));
        }
        if ($asset['type'] !== static::ASSET_TYPE_STYLE) {
            throw $this->exception(array('Could not enqueue style "%1$s": "%2$s" is not a style type', $handle, $asset['type']));
        }
        $this->_enqueueAsset($handle);

        return $this;
    }

    /**
     * Register a script.
     *
     * @since 4.8.1
     * @param string $handle The unique asset handle.
     * @param string $uri The URI of the asset. If relative, it will be appended to the value of {@see getJSUri()}.
     * @param array|null $dependencies List of dependencies for the assed. If null, empty array will be used.
     * @param string|null $version The version of the asset. If null, the plugin version will be used.
     * @param bool $inFooter Whether or not the script should go in the footer.
     * @param bool|null $allowOverwrite If true, and the handle already exists, it will be overwritten.
     * @return string|null The asset handle if regisered; otherwise null.
     */
    public function registerScript($handle, $uri, $dependencies = null, $version = null, $inFooter = false, $allowOverwrite = false)
    {
        $this->_registerScript($handle, $uri, $dependencies, $version, $inFooter, $allowOverwrite);
    }

    /**
     * Enqueue a registered script by handle.
     *
     * @since 4.8.1
     * @param string $handle The handle of the script to enqueue.
     * @return AssetsAbstract This instance.
     * @throws Core\Exception If the handle isn't registered, the handle type is not registered or is not of a script.
     */
    public function enqueueScript($handle)
    {
        // Normalizing handle
        if (!static::stringHadPrefix($handle)) {
            $handle = $this->getHandlePrefix($handle);
        }
        if (!($asset = $this->getAsset($handle))) {
            throw $this->exception(array('Could not enqueue script "%1$s": Register the script first', $handle));
        }
        if (!$this->hasAssetType($asset['type'])) {
            throw $this->exception(array('Could not enqueue script "%1$s": "%2$s" is not a registered asset type', $handle, $asset['type']));
        }
        if ($asset['type'] !== static::ASSET_TYPE_SCRIPT) {
            throw $this->exception(array('Could not enqueue style "%1$s": "%2$s" is not a script type', $handle, $asset['type']));
        }
        $this->_enqueueAsset($handle);

        return $this;
    }

    /**
     * Get all asset types of this class.
     *
     * @since 4.8.1
     * @return array The asset type, where keys are the type code.
     */
    public static function getAssetTypes()
    {
        return static::$_assetTypes;
    }

    /**
     * Whether or not an asset type exists.
     *
     * @since 4.8.1
     * @param string $type The asset type.
     * @return bool True if exists; false otherwise.
     */
    public static function hasAssetType($type)
    {
        $types = static::getAssetTypes();
        return isset($types[$type]);
    }

    /**
     * Gets the optionally suffixed prefix for resource handles configured for this instance.
     *
     * A resource handle is a unique ID that identifies resources enqueued with functions such as `wp_enqueue_script()`
     * and `wp_enqueue_style()`.
     * 
     * @since 4.8.1
     * @param string|null $handle If speficied, the prefix will be suffixed with this.
     * @return string|null The prefix for resource handles that is configured for this instance, optionally suffixed
     *  with $handle.
     */
    public function getHandlePrefix($handle = null)
    {
        $prefix = $this->_getHandlePrefix();
        return is_null($handle)
            ? $prefix
            : (static::stringHadPrefix($handle) ? $handle : "{$prefix}{$handle}");
    }

    /**
     * Gets the prefix for resource handles configured for this instance.
     * 
     * A resource handle is a unique ID that identifies resources enqueued with functions such as `wp_enqueue_script()`
     * and `wp_enqueue_style()`.
     * 
     * If the 'handle_prefix' data member is not set, falls back to the `HANDLE_PREFIX` class constant, then to `null`.
     *
     * @since 4.8.1
     * @return string|null The prefix for resource handles that is configured for this instance.
     */
    public function _getHandlePrefix()
    {
        $pluginCode = $this->getPlugin()->getCode();
        return $this->_getDataOrConst('handle_prefix', $pluginCode ? sprintf('%1$s-', $pluginCode) : '');
    }

    /**
     * Gets and optionally suffixes the base CSS URI for this instance.
     *
     * @since 4.8.1
     * @param string|null $path If specified and not null, the base CSS UR will be suffixed with this.
     * @return string The base CSS URI configured for this instance, optionally suffixed with the $path.
     */
    public function getCssUri($path = null)
    {
        $base = untrailingslashit($this->_getDataOrConst('css_uri'));
        return is_null($path)
            ? $base
            : "{$base}/{$path}";
    }

    /**
     * Gets and optionally suffixes the base JS URI for this instance.
     *
     * @since 4.8.1
     * @param string|null $path If specified and not null, the base JS UR will be suffixed with this.
     * @return string The base JS URI configured for this instance, optionally suffixed with the $path.
     */
    public function getJsUri($path = null)
    {
        $base = untrailingslashit($this->_getDataOrConst('js_uri'));
        return is_null($path)
            ? $base
            : "{$base}/{$path}";
    }

    /**
     * Gets the base CSS URI configured for this instance.
     * 
     * If the 'css_uri' data member is not set, falls back to the `CSS_URL` class constant, then to `null`.
     *
     * @since 4.8.1
     * @return string|null The base CSS URI configured for this instance.
     */
    protected function _getCssUri()
    {
        // Allowing override via data
        $key = 'css_uri';
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        // Falling back to constant
        $class = get_class($this);
        $const = "{$class}::CSS_URI";
        if (defined($const)) {
            return constant($const);
        }

        return null;
    }

    /**
     * Checks if a URI is absolute.
     *
     * @since 4.8.1
     * @see uri_is_absolute()
     */
    public static function isUriAbsolute($uri)
    {
        return uri_is_absolute($uri);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function enqueuePublicStyles()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function enqueuePublicScripts()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function enqueueAdminStyles()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function enqueueAdminScripts()
    {
        return $this;
    }
}
