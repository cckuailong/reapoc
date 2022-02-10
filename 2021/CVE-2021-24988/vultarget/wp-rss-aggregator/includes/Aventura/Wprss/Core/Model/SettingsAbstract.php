<?php

namespace Aventura\Wprss\Core\Model;

use Aventura\Wprss\Core;

/**
 * Common functionality for objects that handle settings.
 *
 * @since 4.8.1
 */
abstract class SettingsAbstract extends Core\Plugin\ComponentAbstract implements SettingsInterface
{
    const MAIN_OPTION_NAME  = '';
    const TRUE_VALUE = '1';
    const FALSE_VALUE = '0';

    protected $_sectionsFields;
    protected $_fieldRenderers;
    protected $_defaultValues;

    protected $_isValuesLoaded;
    // Purely to avoid recursion during values loading
    protected $_isValuesLoading;

    /**
     * Gets the name of the main option, where the settings are stored.
     * 
     * The settings are stored as a serialized array.
     * The name defaults to '_settings', prefixed with the plugin code.
     *
     * @since 4.8.1
     * @return string The name of the main option in the database.
     */
    public function getMainOptionName()
    {
        return $this->_getDataOrConst('main_option_name',
            sprintf('%1$s_settings', $this->getPluginCode()));
    }

    /**
     * Get the slug of the settings subpage or tab.
     *
     * @since 4.8.1
     * @return string The slug
     */
    public function getTabSlug()
    {
        return $this->_getDataOrConst('tab_slug', $this->getMainOptionName());
    }

    /**
     * Get the values from the database.
     * 
     * No cache used here.
     *
     * @since 4.8.1
     * @return array The array of settings as retrieved from the database.
     */
    public function getValuesDb()
    {
        $default = array();
        $option = static::getOption($this->getMainOptionName(), $default);
        if (is_string($option)) {
            $option = $default;
        }
        
        return $option;
    }

    /**
     * Gets the default values for settings.
     * 
     * The load will only be performed once, fildered with a prefixed `settings_defaults` hook,
     * and then cached.
     *
     * @since 4.8.1
     * @return array An array of default values, where keys correspond to the setting IDs.
     */
    public function getDefaultValues()
    {
        if (is_null($this->_defaultValues)) {
            $this->_defaultValues = $this->event('settings_defaults', array('values' => $this->_getDefaultValues()))
                ->getValues();
        }

        return $this->_defaultValues;
    }

    /**
     * Gets the default values for settings.
     *
     * @since 4.8.1
     * @return array A raw array of default values. Keys should correspond to setting IDs.
     */
    abstract protected function _getDefaultValues();

    /**
     * Loads the values from the database.
     *
     * @since 4.8.1
     * @return array The loaded values.
     */
    protected function _loadValues()
    {
        $this->_isValuesLoading = true;
        $dbValues = $this->getValuesDb();
        $defaults = $this->getDefaultValues();
        $options  = array_merge_recursive_distinct($defaults, $dbValues);
        foreach ($options as $key => $value) {
            $this->setDataUsingMethod($key, $value);
        }

        $this->_isValuesLoaded = true;
        $this->_isValuesLoading = false;
        return $this;
    }

    /**
     * Whether or not the setting values have been loaded.
     *
     * @since 4.8.1
     * @return bool True if values have been loaded; false otherwise.
     */
    public function isValuesLoaded()
    {
        return (bool)$this->_isValuesLoaded;
    }

    /**
     * Whether or not values are currently loading.
     * 
     * This is needed to avoid recursion while loading values.
     *
     * @since 4.8.1
     * @return bool True if the values are currently loading; false otherwise.
     */
    protected function _isValuesLoading()
    {
        return (bool)$this->_isValuesLoading;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     * @param type $key
     * @param type $index
     * @return type
     */
    public function getData($key = '', $index = null)
    {
        if (!$this->isValuesLoaded() && !$this->_isValuesLoading()) {
            $this->_loadValues();
        }

        return parent::getData($key, $index);
    }

    /**
     * Gets the data for a settings tab.
     *
     * @since 4.8.1
     * @return array Data for the settings tab.
     */
    public function getTabData()
    {
        return array(
            'label'     => $this->getPlugin()->getName(),
            'slug'      => $this->getTabSlug()
        );
    }


    /**
     * A hook handler, typically intended for `wprss_options_tabs`.
     *
     * Adds the data about this settings page's tab to the list of tabs.
     *
     * @since 4.8.1
     * @param  array $tabs An array of registered tabs.
     * @return array       The array of registered tabs, now also containing an entry for this addon's tab.
     */
    public function addTab($tabs)
    {
        $tabs[$this->getPluginCode()] = $this->getTabData();
        return $tabs;
    }

    /**
     * Does the registration of the main setting.
     *
     * @since 4.8.1
     * @return SettingsAbstract
     */
    protected function _registerSetting()
    {
        $optionName = $this->getMainOptionName();
        register_setting(
            $optionName, // Settings group name
            $optionName, // Name of setting to save in db and sanitize
            array($this, 'validate') // Validation callback
        );

        return $this;
    }

    /**
     * Registeres a section and its settings.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $section Section data.
     *  Should contain information about the section, as well as the array of fields in the 'fields' index.
     */
    protected function _registerSection($section)
    {
        $fields = null;
        if ($section instanceof Core\DataObjectInterface) {
            $fields = $section->getFields();
            $section = $section->getData();
        }

        add_settings_section(
            $this->getSectionId($section['id']),
            $section['label'],
            $this->createCommand(array($this, '_renderSectionHeader'), array($section)),
            $this->getMainOptionName()
        );

        if (is_null($fields) && isset($section['fields'])) {
            $fields = $section['fields'];
        }

        if (!is_array($fields)) {
            $fields = array();
        }

        foreach ($fields as $_fieldId => $_field) {
            if (!is_array($_field)) {
                $_field = array('label' => $_field);
            }
            $_field['id'] = $_fieldId;
            $_field['section_id'] = $section['id'];
            $this->_registerField($_field);
        }
    }

    /**
     * Registers a single field.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $field Data of a field.
     */
    protected function _registerField($field)
    {
        if ($field instanceof Core\DataObjectInterface) {
            $field = $field->getData();
        }

        /**
         * @var This will be passed to the field callback as the only argument
         * @see http://codex.wordpress.org/Function_Reference/add_settings_field#Parameters
         */
        $callbackArgs = $field;
        if (!isset($callbackArgs['label'])) {
            $callbackArgs['label'] = null;
        }
        if (!isset($callbackArgs['tooltip'])) {
            $callbackArgs['tooltip'] = null;
        }
        if (!isset($callbackArgs['value'])) {
            $callbackArgs['value'] = $this->createLocalDataSource($field['id']);
        }

        add_settings_field(
            $this->getIdPrefix($field['id']),
            $field['label'],
            array($this, 'renderField'),
            $this->getMainOptionName(),
            $this->getSectionId($field['section_id']),
            $callbackArgs
        );
    }

    /**
     * Registers the settings page for these settings.
     *
     * @since 4.8.1
     */
    abstract protected function _registerSettingsPage();

    /**
     * Renders a header of a section.
     * 
     * This is intended to be a callback for {@see add_settings_section()}.
     *
     * @since 4.8.1
     * @param array $data Has 3 elmenets: 'id', 'title', 'callback'.
     */
    public function _renderSectionHeader($data)
    {
        echo isset($data['header'])
            ? $this->resolveDataSource($data['header'])
            : '';
    }

    /**
     * Renders and outputs a field.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $field Data of a field.
     */
    public function renderField($field)
    {
        echo $this->getFieldHtml($field);
    }

    /**
     * Renders and gets HTML of a field.
     *
     * The output will depend on the data of the field, and also on its 'type'.
     *
     * @since 4.8.1
     * @see getFieldRenderers()
     * @param array|Core\DataObjectInterface $field Data of a field.
     * @return string The output of the rendered field.
     * @throws \Aventura\Wprss\SpinnerChief\Exception If no renderer is defined for the field type.
     */
    public function getFieldHtml($field)
    {
        if ($field instanceof Core\DataObjectInterface) {
            $field = $field->getData();
        }

        // Defaults
        if (!isset($field['type'])) {
            $field['type'] = 'text';
        }
        // Get actual data
        if (isset($field['data'])) {
            $field['data'] = $this->resolveDataSource($field['data']);
        }
        if (isset($field['value'])) {
            $field['value'] = $this->resolveDataSource($field['value']);
        }

        // Choose rendering algo
        if (!($renderer = $this->getFieldRenderers($field['type']))) {
            throw $this->exception(array('Could not render field "%1$s": A renderer for field type "%1$s" must be defined', $field['id'], $field['type']));
        }

        return $this->resolveDataSource($renderer, array($field));
    }

    /**
     * Gets one or all field type renderers.
     *
     * Essentially, a renderer is a callback that receives information about a field,
     * and returns the output of that field rendered.
     *
     * The renderers will be loaded once, then filtered with a prefixed `settings_field_renderers` hook,
     * and cached.
     *
     * @since 4.8.1
     * @param string $type A type, for which to get the renderer.
     * @return array|callable All field renderers available, or one renderer.
     */
    public function getFieldRenderers($type = null)
    {
        if (is_null($this->_fieldRenderers)) {
            $this->_fieldRenderers = $this->event('settings_field_renderers', array('renderers' => $this->_getFieldRenderers()))
                ->getRenderers();
        }

        if (is_null($type)) {
            return $this->_fieldRenderers;
        }

        return isset($this->_fieldRenderers[$type])
            ? $this->_fieldRenderers[$type]
            : null;
    }

    /**
     * Gets all available field renderers.
     * 
     * Override this method to add more renderers to an instance from within itself.
     *
     * @since 4.8.1
     * @return array An array of callables, each of which is a field renderer.
     */
    protected function _getFieldRenderers()
    {
        return array(
            'text'          => $this->createCommand(array($this, 'renderTextField')),
            'checkbox'      => $this->createCommand(array($this, 'renderCheckboxField')),
            'select'        => $this->createCommand(array($this, 'renderSelectField')),
            'number'        => $this->createCommand(array($this, 'renderNumberField')),
        );
    }

    /**
     * Renders a text field.
     * 
     * Normally, the output would be an <input> element of type 'text'.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $field Data of a field.
     * @return string The text field HTML.
     */
    public function renderTextField($field)
    {
        if ($field instanceof Core\DataObjectInterface) {
            $field = $field->getData();
        }
        
        $id = $field['id'];
        $htmlAttributes = array(
            'id'            => $this->getIdPrefix($id),
            'name'          => static::getNameHtml(array($this->getMainOptionName(), $id)),
        );
        $field = array_merge_recursive_distinct($field, $htmlAttributes);
        return static::getTextHtml($field['value'], $field) .
        $this->getPlugin()->getTooltips()->doTooltip($id);
    }

    /**
     * Renders a number field.
     *
     * Normally, the output would be an <input> element of type 'number'.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $field Data of a field.
     * @return string The number field HTML.
     */
    public function renderNumberField($field)
    {
        if ($field instanceof Core\DataObjectInterface) {
            $field = $field->getData();
        }
        
        $id = $field['id'];
        $htmlAttributes = array(
            'id'            => $this->getIdPrefix($id),
            'name'          => static::getNameHtml(array($this->getMainOptionName(), $id)),
        );
        $field = array_merge_recursive_distinct($field, $htmlAttributes);
        return static::getNumberHtml($field['value'], $field) .
        $this->getPlugin()->getTooltips()->doTooltip($id);
    }

    /**
     * Renders a checkbox field.
     *
     * Normally, the output would be an <input> element of type 'checkbox'.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $field Data of a field.
     * @return string The checkbox field HTML.
     */
    public function renderCheckboxField($field)
    {
        if ($field instanceof Core\DataObjectInterface) {
            $field = $field->getData();
        }
        
        $id = $field['id'];
        $trueValue = isset($field['true_value'])
            ? $field['true_value']
            : static::getTrueValue();
        $falseValue = isset($field['false_value'])
            ? $field['false_value']
            : static::getFalseValue();
        return static::getCheckboxHtml($field['value'], array(
            'id'            => $this->getIdPrefix($id),
            'name'          => static::getNameHtml(array($this->getMainOptionName(), $id)),
            'value'         => $trueValue,
            'false_value'   => $falseValue
        )) .
        $this->getPlugin()->getTooltips()->doTooltip($id);
    }

    /**
     * Renders a checkbox field.
     *
     * Normally, the output would be an <select> element.
     *
     * @since 4.8.1
     * @param array|Core\DataObjectInterface $field Data of a field.
     * @return string The select field HTML.
     */
    public function renderSelectField($field)
    {
        if ($field instanceof Core\DataObjectInterface) {
            $field = $field->getData();
        }
        
        $options = $field['data'];
        $id = $field['id'];
        return static::getSelectHtml( array_combine($options, $options), array(
            'id'            => $this->getIdPrefix($id),
            'name'          => static::getNameHtml(array($this->getMainOptionName(), $id)),
            'selected'      => $field['value']
        )) .
        $this->getPlugin()->getTooltips()->doTooltip($id);
    }


    /**
     * Add settings fields and sections
     *
     * @since 4.8.1
     * @param string $activeTab The slug of the active wprss settings tab.
     */
    public function _renderSettingsPage($activeTab)
    {
        $optionName = $this->getMainOptionName();
        if ($activeTab !== $this->getTabSlug()) return;
        // Render all sections for this page
        settings_fields($optionName);
        do_settings_sections($optionName);
    }

    /**
     * Gets the ID prefix, or a prefixed ID.
     *
     * IDs are something that can be used in HTML's "id" attributes, or
     * other internal IDs.
     *
     * This can be set via the ID_PREFIX class constant, overridden with the 'id_prefix' data member,
     * and defaults to the plugin code, followed by an underscore '_'.
     *
     * @since 4.8.1
     * @param string|null $id An ID to prefix.
     * @return string The prefix, or prefixed ID.
     */
    public function getIdPrefix($id = null)
    {
        $prefix = $this->_getDataOrConst('id_prefix', sprintf('%1$s_', $this->getPluginCode()));
        return is_null($id)
            ? $prefix
            : "{$prefix}{$id}";
    }


    /**
     * Gets the slug prefix, or a prefixed slug.
     *
     * Slugs are something that can be used in HTML's "class" or other attributes, or URLs.
     *
     * This can be set via the SLUG_PREFIX class constant, overridden with the 'slug_prefix' data member,
     * and defaults to the plugin code, followed by a dash underscore '-'.
     *
     * @since 4.8.1
     * @param string|null $id A slug to prefix.
     * @return string The prefix, or prefixed slug.
     */
    public function getSlugPrefix($id = null)
    {
        $prefix = $this->_getDataOrConst('slug_prefix', sprintf('%1$s-', $this->getPluginCode()));
        return is_null($id)
            ? $prefix
            : "{$prefix}{$id}";
    }

    /**
     * Gets a section ID from a section code.
     * 
     * A section code is something that uniquely identifies a section internally.
     * A section ID, on the other hand, may contain other characters, and is typically
     * used on the frontend.
     *
     * @since 4.8.1
     * @param string $code The section code.
     * @return string The section ID.
     */
    public function getSectionId($code)
    {
        $pluginCode = $this->getPluginCode();
        return "settings_{$pluginCode}_{$code}_section";
    }

    /**
     * Gets the code of the plugin.
     *
     * @since 4.8.1
     * @see Core\Plugin\PluginInterface::getCode()
     * @return string The plugin code.
     */
    public function getPluginCode()
    {
        return $this->getPlugin()->getCode();
    }

    /**
     * Registers the settings section and fields.
     *
     * This should be hooked to something in WP.
     *
     * @since 4.8.1
     */
    public function register()
    {
        $this->_registerSetting();
        $sections = $this->getSectionsFields();

        foreach ($sections as $sectionId => $section) {
            $section['id'] = $sectionId;
            $this->_registerSection($section);
        }

        $this->_registerSettingsPage();
    }

    /**
     * Retrieves all raw data for all sections.
     *
     * @since 4.8.1
     * @return array All sections, together with their fields.
     */
    abstract protected function _getSectionsFields();

    /**
     * Retrieves all data for all sections, and their fields.
     *
     * This is done once, filtered with prefixed 'settings_fields', and cashed.
     *
     * @since 4.8.1
     * @return array All sections, together with their fields.
     */
    public function getSectionsFields()
    {
        if (is_null($this->_sectionsFields)) {
            $this->_sectionsFields = $this->event('settings_fields', array('sections' => $this->_getSectionsFields()))
                ->getSections();
        }

        return $this->_sectionsFields;
    }

    /**
     * A shortcut for creating callables to use as data sources.
     *
     * @since 4.8.1
     * @param callable $callable The callback that the command represents.
     * @param array $args The arguments for the callback.
     * @return \Aventura\Wprss\Core\Model\CommandInterface A new command.
     */
    public function createCommand($callable, $args = array())
    {
        return new Command(array(
            'function'      => $callable,
            'args'          => $args
        ));
    }

    /**
     * Resolves a command by executing it, and returning the result.
     *
     * @since 4.8.1
     * @param CommandInterface $command The command to resolve.
     * @return mixed Result of the command.
     * @throws \Aventura\Wprss\SpinnerChief\Exception
     */
    public function resolveCommand($command, $args = array())
    {
        if (!is_callable($command)) {
            throw $this->exception('Cannot resolve command: Command must be callable');
        }

        return call_user_func_array($command, $args);
    }

    /**
     * Retrieves the value of a datasource, or if not a datasource just returns it.
     *
     * @since 4.8.1
     * @param callable $source The datasource.
     * @param array $args Additional arguments for the datasource.
     * @return mixed The resolved datasource value.
     */
    public function resolveDataSource($source, $args = array())
    {
        if (is_callable($source)) {
            return $this->resolveCommand($source, $args);
        }

        return $source;
    }

    /**
     * Cretes a datasource for retrieving data from this instance.
     *
     * @since 4.8.1
     * @param string $key The key of the data member to retrieve.
     * @return CommandInterface A command that retrieves data from this instance.
     */
    public function createLocalDataSource($key)
    {
        return $this->createCommand(array($this, 'getData'), array($key));
    }

    /**
     * Generates HTML of a checkbox based on the passed parameters.
     *
     * @since 4.8.1
     * @see PRSS_FTP_Utils::boolean_to_checkbox()
     * @param bool|mixed $isChecked Whether or not this checkbox should be ticked.
     * @param array $args Additional checkbox params.
     * @param bool $autoEval If true, the first value will be evaluated and compared to known 'true' values.
     * @return string The HTML of a checkbox.
     */
    static public function getCheckboxHtml($isChecked, $args, $autoEval = true)
    {
        if ($autoEval) {
            $isChecked = static::isTrue($isChecked);
        }
        return \WPRSS_FTP_Utils::boolean_to_checkbox($isChecked, $args);
    }

    /**
     * Get the HTML output of a <select> element.
     *
     * @since 4.8.1
     * @param array $values The select element's options.
     * @param array $args Data for the select element.
     * @return string The select element HTML.
     */
    static public function getSelectHtml($values, $args = array())
    {
        return \WPRSS_FTP_Utils::array_to_select($values, $args);
    }

    /**
     * Get the HTML output of an <input> element of type 'text' or 'password'.
     *
     * @since 4.8.1
     * @param string|int $value The input element's value.
     * @param array $args Data for the input element.
     * @return string The input element HTML.
     */
    static public function getTextHtml($value, $args = array())
    {
        $defaults = array(
            'type'          => 'text',
            'class'         => '',
            'placeholder'   => isset($args['label']) ? $args['label'] : ''
        );
        $args = array_merge_recursive_distinct($defaults, $args);

        return sprintf('<input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s" placeholder="%6$s" />',
            esc_attr($args['id']),
            $args['type'],
            $args['name'],
            esc_attr($value),
            $args['class'],
            $args['placeholder']
        );
    }

    /**
     * Get the HTML output of an <input> element of type 'number'.
     *
     * @since 4.8.1
     * @param string|int $value The input element's value.
     * @param array $args Data for the input element.
     * @return string The input element HTML.
     */
    static public function getNumberHtml($value, $args = array())
    {
        $defaults = array(
            'type'          => 'number',
            'class'         => '',
            'placeholder'   => isset($args['label']) ? $args['label'] : '',
            'min'           => 0,
            'max'           => '',
            'step'          => 1
        );
        $args = array_merge_recursive_distinct($defaults, $args);

        return sprintf('<input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%5$s" placeholder="%6$s" min="%7$s" max="%8$s" step="%9$s" />',
            esc_attr($args['id']),
            $args['type'],
            $args['name'],
            esc_attr($value),
            $args['class'],
            $args['placeholder'],
            $args['min'],
            $args['max'],
            $args['step']
        );
    }

    /**
     * Gets a value for HTML elements' "name" attribute.
     *
     * This attribute can have multiple nested names, or levels, such as:
     * name[subName][subSubName]
     *
     * Given all the levels, it will return the correct value for the attribute.
     *
     * @since 4.8.1
     * @param string|array $name A name with levels separated by '/', or an array of levels.
     * @return string A value used in HTML elements' "name" attribute.
     */
    static public function getNameHtml($name)
    {
        if (!is_array($name)) {
            $name = explode('/', $name);
        }

        $mainName = array_shift($name);
        return $mainName . '[' . implode('][', $name) . ']';
    }

    /**
     * Checks whether a value is considered to be 'true' by this class.
     *
     * @since 4.8.1
     * @param mixed $value The value to check.
     * @return bool True if the value is considered by this class to represent 'true'; false otherwise.
     */
    static public function isTrue($value)
    {
        return \WPRSS_FTP_Utils::multiboolean($value) || (trim($value) === static::getTrueValue());
    }

    /**
     * Get a value that is considered by this class to be 'true'.
     *
     * @since 4.8.1
     * @return string Returns a string representation of a value that is considered to be 'true' by this class.
     */
    static public function getTrueValue()
    {
        return static::TRUE_VALUE;
    }

    /**
     * Get a value that is considered by this class to be 'false'.
     *
     * @since 4.8.1
     * @return string Returns a string representation of a value that is considered to be 'false' by this class.
     */
    static public function getFalseValue()
    {
        return static::FALSE_VALUE;
    }

    /**
     * Retrieve a raw option by name from the database.
     *
     * @since 4.8.1
     * @param string $name The name of the option.
     * @param bool|mixed $default What to return if the option is not found.
     * @return mixed The option value.
     */
    static public function getOption($name, $default = false)
    {
        return get_option($name, $default);
    }
}