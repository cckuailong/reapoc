<?php

namespace Aventura\Wprss\Core\Plugin;

use Aventura\Wprss\Core;

/**
 * A base class for all SpinnerChief add-on components.
 *
 * @since 4.8.1
 */
abstract class ComponentAbstract extends Core\Model\ModelAbstract implements ComponentInterface
{
    /**
     * @var PluginInterface The plugin, to which this component belongs
     * @since 4.8.1
     */
    protected $_plugin;

    /**
     *
     * @since 4.8.1
     * @param PluginInterface|array $data The instance of the
     *  add-on, of which this is to be a component. Alternatively, an array with data, which must have the 'plugin'
     *  index set to that instance.
     */
    public function __construct($data)
    {
        // Allowing specifying parent as the only argument
        if (!is_array($data)) {
            $data = array('plugin' => $data);
        }
        // Making sure the parent is specified
        if (!isset($data['plugin']) || !($data['plugin'] instanceof PluginInterface)) {
            throw $this->exception(array('Could not create component: The "%1$s" index must be a plugin instance'), array(__NAMESPACE__, 'Exception'));
        }
        $plugin = $data['plugin'];
        unset($data['plugin']);

        $this->_plugin = $plugin;
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     * @return PluginInterface
     */
    public function getPlugin()
    {
        return $this->_plugin;
    }

    /**
     * Sets the parent plugin for this component.
     *
     * @since 4.8.1
     * @param PluginInterface $plugin The plugin to set.
     * @return ComponentAbstract This instance.
     */
    protected function _setPlugin(PluginInterface $plugin) {
        $this->_plugin = $plugin;
        return $this;
    }

    /**
     * Get the text domain of this component.
     *
     * @since 4.8.1
     * @return string The text domain.
     */
    public function getTextDomain() {
        return $this->getPlugin()->getTextDomain();
    }

    /**
     * Creates an exception, the root of which is the parent plugin.
     *
     * @since 4.8.1
     * @return Core\Exception
     */
    public function exception($text, $className = null, $translate = null)
    {
        return $this->getPlugin()->exception($text, $className, $translate);
    }

    /**
     * Hooks this component into the environment.
     *
     * Typically, this is done by a factory upon creation, but not necessarily.
     * Override this to hook in your component.
     *
     * @since 4.8.1
     */
    public function hook() {}

    /**
     * Gets a hook name prefix, or a prefixed hook name.
     *
     * A hook prefix is used by all abstracted methods for adding hooks.
     * The prefix defaults to the plugin's prefix. If none set, the plugin code followed by an underscore '_'.
     * If can be additionally defined as the HOOK_PREFIX class constant, and overridden
     * with the 'hook_prefix' data member.
     *
     * @since 4.8.1
     * @param string $code A hook name to prefix.
     * @return string The hook prefix, or prefixed string.
     */
    public function getEventPrefix($code = null)
    {
        $prefix = $this->_getDataOrConst('hook_prefix');
        if (is_null($prefix)) {
            $prefix = $this->getPlugin()->getHookPrefix();
        }
        if (is_null($prefix)) {
            $prefix = sprintf('%1$s_', $this->getPluginCode());
        }
        
        return is_null($code)
            ? $prefix
            : "{$prefix}{$code}";
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     * @see Core\Model\LoggerInterface
     * @param string|int $level
     * @param string $message
     * @param array $context Particularly, the 'source' index should point to the function/method,
     *  where the message is sent from.
     * @return bool True if log entry was processed; false otherwise.
     */
    public function log($level, $message, array $context = array())
    {
        return false;
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

        return $this->getPlugin()->on($name, $listener, $data, $priority, $acceptedArgs);
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

        return $this->getPlugin()->event($name, $data);
    }
}
