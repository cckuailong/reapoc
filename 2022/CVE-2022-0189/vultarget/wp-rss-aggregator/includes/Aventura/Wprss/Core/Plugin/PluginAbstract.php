<?php

namespace Aventura\Wprss\Core\Plugin;

use Aventura\Wprss\Core;
use Dhii\Di\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * The base class for all WP plugins.
 *
 * @since 4.8.1
 */
class PluginAbstract extends Core\Model\ModelAbstract implements PluginInterface
{
    const CODE = '';
    const VERSION = '';

    /**
     * @deprecated 4.11
     * @since 4.8.1
     */
    protected $_factory;
    /** @since 4.8.1 */
    protected $_logger;
    /** @since 4.8.1 */
    protected $_eventManager;
    /** @since 4.11 */
    protected $container;
    /** @since 4.11 */
    protected $factory;

    /**
     *
     * @param array|string $data Data that describes the plugin.
     *  The following indices are required:
     *      * `basename`            - The plugin basename, or full path to plugin's main file. See {@see getBasename()}.
     *  Other indices explicitly handled by this class:
     *      * `component_factory`   - Instance or name of a component factory class.
     *      * `text_domain`         - The text domain used for translation by this plugin. See {@see getTextDomain}.
     *      * `name`                - The human-readable name of the plugin. See {@see getName()}.
     * Any other data will just be added to this instances internal data.
     * @param mixed Deprecated since 4.11.
     * @param ContainerInterface $container The DI container that will be used by this plugin to resolve dependencies.
     * @param FactoryInterface $factory The factory that will be used by this plugin to create generate new instances.
     *
     * @throws Exception If required fields are not specified.
     */
    public function __construct(
            $data,
            $_factory = null, // Deprecated; kept for BC
            ContainerInterface $container = null,
            FactoryInterface $factory = null
    ){
        if (!is_array($data)) {
            $data = array('basename' => $data);
        }

        // Handling basename
        if (!isset($data['basename'])) {
            throw $this->exception('Could not create plugin instance: "basename" must be specified', array(__NAMESPACE__, 'Exception'));
        }
        $data['basename'] = static::standardizeBasename($data['basename']);

        // Normalizing and setting component factory
        if (is_null($_factory) && isset($data['component_factory'])) {
            $_factory = $data['component_factory'];
        }

        if ($_factory) {
            $this->setFactory($_factory);
        }

        if ($factory) {
            $this->_setFactory($factory);
        }

        if ($container) {
            $this->_setContainer($container);
        }

        parent::__construct($data);
    }

    /**
     * Sets the service factory.
     *
     * @since 4.8.1
     * @param FactoryInterface $factory The factory.
     * @return $this This instance.
     */
    protected function _setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Gets the service factory.
     *
     * @since 4.11
     *
     * @return FactoryInterface
     */
    protected function _getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the DI container.
     *
     * @since 4.11
     *
     * @param ContainerInterface $container The container.
     * @return $this This instance.
     */
    protected function _setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Gets the DI container.
     *
     * @since 4.11
     *
     * @return ContainerInterface
     */
    protected function _getContainer()
    {
        /**
         * This is necessary because extensions still don't know about the new
         * DI container mechanics, and no container is being passed in those
         * extensions to the constructor of this class.
         *
         * @todo Remove when add-ons refactored (4.11).
         */
        if (is_null($this->container)) {
            return wprss_wp_container();
        }

        return $this->container;
    }

    /**
     * Gets the service ID prefix, or prefixes the given ID with it.
     *
     * @since 4.11
     *
     * @param string|null $id The service ID to prefix, if not null.
     * @return string The prefix, or potentially prefixed ID.
     */
    protected function _getServiceIdPrefix($id = null)
    {
        $prefix = $this->_getServiceIdPrefixRaw();
        return static::stringHadPrefix($id)
            ? $id
            : "{$prefix}{$id}";
    }

    /**
     * Gets the prefix for service IDs.
     *
     * @since 4.11
     *
     * @return type
     */
    protected function _getServiceIdPrefixRaw()
    {
        /**
         * This is necessary because extensions still don't know about the new
         * DI container mechanics, and no container is being passed in those
         * extensions to the constructor of this class.
         *
         * @todo Remove when add-ons refactored (4.11).
         */
        $default = \WPRSS_SERVICE_ID_PREFIX;

        return $this->_getDataOrConst('service_id_prefix', $default);
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->getData('basename');
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function getTextDomain()
    {
        return $this->getData('text_domain');
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getDataOrConst('code');
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_getDataOrConst('version');
    }

    /**
     * @todo Change to return the interop factory once extensions are refactored (4.11).
     * @since 4.8.1
     * @return ComponentFactoryInterface
     */
    public function getFactory()
    {
        return $this->_factory;
    }

    /**
     * @deprecated 4.11 Factory can no longer be set after construction.
     * @since 4.8.1
     *
     * @return string
     */
    public function setFactory(ComponentFactoryInterface $factory)
    {
        $this->_factory = $factory;
        return $this;
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function isActive()
    {
        return static::isPluginActive($this);
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function deactivate()
    {
        static::deactivatePlugin($this);
        return $this;
    }

    /**
     * Checks if a plugin is active.
     *
     * @since 4.8.1
     * @param PluginInterface|string $plugin A plugin instance or basename.
     * @return bool True if the plugin is active; false otherwise.
     */
    static public function isPluginActive($plugin)
    {
        static::_ensurePluginFunctionsExist();

        if ($plugin instanceof PluginInterface) {
            $plugin = $plugin->getBasename();
        }

        return is_plugin_active($plugin);
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    static public function deactivatePlugin($plugin)
    {
        static::_ensurePluginFunctionsExist();

        if ($plugin instanceof PluginInterface) {
            $plugin = $plugin->getBasename();
        }

        deactivate_plugins($plugin);
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    static protected function _ensurePluginFunctionsExist()
    {
        // Making sure there are the functions we need
		if (!function_exists( 'is_plugin_active' )) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
    }

    /**
     * Translates some text.
     *
     * @since 4.8.1
     * @param string $text The text to translate.
     * @param string|null The text domain to use for translation.
     *  Defaults to this plugin's text domain.
     * @return string Translated text
     */
    protected function _translate($text, $translator = null)
    {
        if (!is_null($translator)) {
            $translator = $this->getTextDomain();
        }

        return __($text, $translator);
    }

    /**
     * Gets a plugin basename from its absolute path.
     *
     * @since 4.8.1
     * @param string $path Absolute path to a plugin's main file.
     * @return string The path to the plugin's main file, relative to the plugins directory.
     */
    public static function getPluginBasename($path) {
        return plugin_basename($path);
    }

    /**
     * Gets the logger instance used by this plugin.
     *
     * @since 4.8.1
     * @return Core\Model\LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->_getContainer()->get($this->_getServiceIdPrefix('logger'));
    }

    /**
     * Sets the logger instance to be used by this plugin.
     *
     * @deprecated 4.11 Logger can no longer be set, but is retrieved from container.
     * @since 4.8.1
     * @param Core\Model\LoggerInterface $logger
     * @return Core\Plugin\PluginAbstract
     */
    public function setLogger(Core\Model\LoggerInterface $logger)
    {
        $this->_logger = $logger;
        return $this;
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function log($level, $message, array $context = array())
    {
        return false;
    }

    /**
     * @since 4.8.1
     *
     * @return string
     */
    public function logObject($level, $object, array $context = array())
    {
        if (empty($object)) {
            ob_start();
            var_dump($object);
            $dump = ob_get_contents();
            ob_end_clean();
        }
        else {
            $dump = print_r($object, true);
        }

        return $this->log($level, $dump, $context);
    }

    /**
     * A default no-op implementation. Does nothing. Override in descendants.
     *
     * @since 4.8.1
     */
    public function hook() {}

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    protected function _getEventPrefix($name = null)
    {
        $prefix = $this->hasData('event_prefix')
            ? $this->getData('event_prefix')
            : (($code = $this->getCode())
                    ? sprintf('%1$s_', $code)
                    : '');

        return string_had_prefix($name, $this->getPrefixOverride())
            ? $name
            : "{$prefix}{$name}";
    }

    /**
     * Sets the event manager for this instance.
     *
     * @deprecated 4.11
     * @since 4.8.1
     * @param Core\Model\Event\EventManagerInterface $manager An event manager.
     * @return PluginAbstract This instance.
     */
    public function setEventManager(Core\Model\Event\EventManagerInterface $manager)
    {
        $this->_eventManager = $manager;
        return $this;
    }

    /**
     * Retrieves this instance's event manager.
     *
     * @since 4.8.1
     * @return Core\Model\Event\EventManagerInterface|null The event manager of this instance, or null if not set.
     */
    public function getEventManager()
    {
        return $this->_getContainer()->get($this->_getServiceIdPrefix('event_manager'));
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function on($name, $listener, $data = null, $priority = null, $acceptedArgs = null)
    {
        if (is_string($listener) && !is_object($listener)) {
            $listener = array($this, $listener);
        }

        if ($events = $this->getEventManager()) {
            $name = $this->getEventPrefix($name);
            return $events->on($name, $listener, $data, $priority, $acceptedArgs);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function event($name, $data = array())
    {
        if (!isset($data['caller'])) {
            $data['caller'] = $this;
        }

        if ($events = $this->getEventManager()) {
            $name = $this->getEventPrefix($name);
            return $events->event($name, $data);
        }

        return null;
    }

    /**
     * Converts all directory separators into Unix-style ones.
     *
     * @since 4.9
     * @param string $path A filesystem path.
     * @return The path with standardized directory separators, and trimmed
     *  whitespace.
     */
    public static function standardizeDirectorySeparators($path)
    {
        return trim(str_replace(array('\\', '/'), '/', $path));
    }

    /**
     * Will standardize a plugin basename.
     *
     * A standard plugin basename is a path to the main plugin file relative
     * to the plugins directory, and with Unix directory separators if
     * applicable.
     *
     * @since 4.9
     * @see standardizeDirectorySeparators()
     * @param string $path An absolute or relative path to a plugin main file.
     * @return string A standardized plugin basename.
     */
    public static function standardizeBasename($path)
    {
        $path = static::standardizeDirectorySeparators($path);

        // Account for full path to main file.
        if (substr($path, 0, 1) === '/' || substr_count($path, '/') >= 2) {
            $path = static::getPluginBasename($path);
        }

        return $path;
    }
}
