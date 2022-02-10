<?php
/**
 * Form plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Helper
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

/**
 * Form plugin class
 */
class TInvWL_Form
{

	/**
	 * Prefix for id elements
	 *
	 * @var string
	 */
	static $_name;

	/**
	 * Form value for elements
	 *
	 * @var array
	 */
	static $value = array();

	/**
	 * Form option for elements
	 *
	 * @var array
	 */
	static $option = array();

	/**
	 * Set Plugin name
	 *
	 * @param string $plugin_name Plugin name.
	 */
	public static function _init($plugin_name = TINVWL_PREFIX)
	{
		self::$_name = $plugin_name;
	}

	/**
	 * Call method for returm or output
	 *
	 * @param string $name Name function.
	 * @param array $arg Parameter function.
	 *
	 * @return mixed
	 */
	public function __call($name, $arg)
	{
		$_arg = array(
			0 => null,
			1 => null,
			2 => null,
			3 => null,
		);
		foreach (array_keys($_arg) as $key) {
			$_arg[$key] = array_shift($arg);
		}
		$arg = $_arg;
		$glue = '_';
		$method = sprintf('%s%s', $glue, $name);
		if (false === strpos($name, $glue)) {
			if (method_exists(__CLASS__, $method)) {
				$data = call_user_func(array(__CLASS__, $method), $arg[0], $arg[1], $arg[2], $arg[3]);
				echo $data; // WPCS: xss ok.
			}
		} else {
			if (method_exists(__CLASS__, $name)) {
				return call_user_func(array(__CLASS__, $name), $arg[0], $arg[1], $arg[2], $arg[3]);
			}
		}

		return '';
	}

	/**
	 * Call method for returm or output
	 *
	 * @param string $name Name function.
	 * @param array $arg Parameter function.
	 *
	 * @return mixed
	 */
	public static function __callStatic($name, $arg)
	{
		if (empty(self::$_name)) {
			self::$_name = TINVWL_PREFIX;
		}
		$_arg = array(
			0 => null,
			1 => null,
			2 => null,
			3 => null,
		);
		foreach (array_keys($_arg) as $key) {
			$_arg[$key] = array_shift($arg);
		}
		$arg = $_arg;
		$glue = '_';
		$method = sprintf('%s%s', $glue, $name);
		if (false === strpos($name, $glue)) {
			if (method_exists(__CLASS__, $method)) {
				$data = call_user_func(array(__CLASS__, $method), $arg[0], $arg[1], $arg[2], $arg[3]);
				echo $data;  // WPCS: xss ok.
			}
		} else {
			if (method_exists(__CLASS__, $name)) {
				return call_user_func(array(__CLASS__, $name), $arg[0], $arg[1], $arg[2], $arg[3]);
			}
		}

		return '';
	}

	/**
	 * Create input html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _text($data, $value = '', $extra = '')
	{
		$load = true;
		if (is_array($extra)) {
			if (isset($extra['load'])) {
				$load = (bool)$extra['load'];
				unset($extra['load']);
			}
		}
		if ($load) {
			$value = esc_attr(self::getvalue((is_array($data) ? @$data['name'] : $data), $value)); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
		$defaults = array(
			'type' => is_array($data) ? @$data['type'] : 'text',
			'name' => is_array($data) ? @$data['name'] : $data,
			'value' => $value,
		);

		return sprintf('<input %s%s />', self::__parseatr($data, $defaults), self::__atrtostr($extra));
	}

	/**
	 * Create input html element with type number
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param integer|float $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _number($data, $value = 0, $extra = '')
	{
		$class = sprintf(' %s-form-number', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$data['type'] = 'number';

		return self::_text($data, $value, $extra);
	}

	/**
	 * Create color picker
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 * @see colorpicker
	 *
	 */
	public static function _color($data = '', $value = '', $extra = '')
	{
		$class = sprintf(' %s-form-color', self::$_name);
		$load = true;
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
			if (isset($extra['load'])) {
				$load = (bool)$extra['load'];
				unset($extra['load']);
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}
		if ($load) {
			$value = self::getvalue((is_array($data) ? @$data['name'] : $data), $value); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value,
		);

		return sprintf('<div class="tinvwl-color-picker"><div class="tinvwl-input-group tinvwl-no-full"><input %s%s /><div class="tinvwl-input-group-btn"><div class="tinvwl-eyedropper"><a href="javascript:void(0);"><i class="ftinvwl ftinvwl-eyedropper"></i></a></div></div></div></div>', self::__parseatr($data, $defaults), self::__atrtostr($extra));
	}

	/**
	 * Create date input
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 * @see jquery-ui-datepicker
	 *
	 */
	public static function _date($data = '', $value = '', $extra = '')
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$extra_js = '';
		$value = self::getvalue($data['name'], $value);
		$class = sprintf(' %s-date', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}
		if (is_array($extra)) {
			if (isset($extra['date'])) {
				$extra_js = $extra['date'];
				unset($extra['date']);
			}
		}

		$data['id'] = self::__createid($data['name']);

		return sprintf("%s<script type=\"text/javascript\">jQuery(document).ready(function($){ $('#%s').datepicker({%s})});</script>", self::_text($data, $value, $extra), $data['id'], self::__atrtostrjs($extra_js));
	}

	/**
	 * Create select html element with time period
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param intger $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _time($data = '', $value = '', $extra = '')
	{
		$class = sprintf(' %s-time', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
		} else {
			$extra .= sprintf(' class="%s"', $class);
		}
		$options = array();
		for ($i = 0; $i < 24; $i++) {
			$options[$i] = sprintf('%02d:00', $i);
		}

		return self::_select($data, intval($value), $extra, $options);
	}

	/**
	 * Create textarea html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _textarea($data = '', $value = '', $extra = '')
	{
		$value = self::getvalue((is_array($data) ? @$data['name'] : $data), $value); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '20',
		);

		return sprintf('<textarea %s%s>%s</textarea>', self::__parseatr($data, $defaults), self::__atrtostr($extra), esc_textarea($value));
	}

	/**
	 * Output wysiwyg editor
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param array $extra Styling or Custom variable.
	 */
	public static function editor($data = '', $value = '', $extra = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$value = self::getvalue($data['name'], $value);
		$data['id'] = self::__createid($data['name']);
		$extra['textarea_name'] = $data['name'];
		wp_editor($value, $data['id'], $extra);
	}

	/**
	 * Create input html element with type checkbox
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param boolean $checked Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param string $value Value for form.
	 *
	 * @return string
	 */
	public static function _checkbox($data = '', $checked = false, $extra = '', $value = '')
	{
		$load = true;
		if (is_array($extra)) {
			if (isset($extra['load'])) {
				$load = (bool)$extra['load'];
				unset($extra['load']);
			}
		}
		if ($load) {
			$checked = self::getvalue((is_array($data) ? @$data['name'] : $data), $checked); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			$value = self::getoption((is_array($data) ? @$data['name'] : $data), $value); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
		if (is_array($value)) {
			$value = array_shift($value);
		}
		if (!is_bool($checked)) {
			$checked = ($checked == $value) ? true : false; // WPCS: loose comparison ok.
		}

		$defaults = array(
			'type' => 'checkbox',
			'name' => (!is_array($data) ? $data : ''),
			'value' => esc_html($value),
		);
		if (is_array($data) && array_key_exists('checked', $data)) {
			$checked = $data['checked'];

			if (false == $checked) { // WPCS: loose comparison ok.
				unset($data['checked']);
			} else {
				$data['checked'] = 'checked';
			}
		}
		if (true == $checked) { // WPCS: loose comparison ok.
			$defaults['checked'] = 'checked';
		} else {
			unset($defaults['checked']);
		}

		return sprintf('<input %s%s />', self::__parseatr($data, $defaults), self::__atrtostr($extra));
	}

	/**
	 * Create input html element with type radio
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param boolean $checked Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param string $value Value for form.
	 *
	 * @return string
	 */
	public static function _radio($data = '', $checked = false, $extra = '', $value = '')
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$data['type'] = 'radio';

		return self::_checkbox($data, $checked, $extra, $value);
	}

	/**
	 * Create select html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param mixed $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param array $options Options for form.
	 *
	 * @return string
	 */
	public static function _select($data = '', $value = array(), $extra = '', $options = array())
	{
		$defaults = array();
		if (!is_array($data)) {
			$data = array('name' => $data);
			$defaults = array('name' => $data);
		}
		$load = true;
		if (is_array($extra)) {
			if (isset($extra['load'])) {
				$load = (bool)$extra['load'];
				unset($extra['load']);
			}
		}
		if ($load) {
			$value = self::getvalue($data['name'], $value);
		}
		$options = self::getoption($data['name'], $options);
		if (!is_array($value)) {
			$value = array($value);
		}
		if (!is_array($options)) {
			$options = array($options);
		}

		$extra = self::__atrtostr($extra);
		$multiple = (count($value) > 1 && false === stripos($extra, 'multiple')) ? ' multiple="multiple"' : '';

		if ($multiple || strpos($extra, 'multiple') > -1) {
			$data['name'] = $data['name'] . '[]';
		}
		$form = '';
		foreach ($options as $key => $val) {
			$key = (string)$key;
			if (is_array($val)) {
				if (empty($val)) {
					continue;
				}
				$opt = '';
				foreach ($val as $opt_key => $opt_val) {
					$sel = in_array($opt_key, $value) ? ' selected="selected"' : ''; // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
					$opt .= sprintf('<option value="%s"%s>%s</option>', esc_attr($opt_key), $sel, esc_html($opt_val));
				}
				$form .= sprintf('<optgroup label="%s" >%s</optgroup>', esc_attr($key), esc_html($opt));
			} else {
				$sel = in_array($key, $value) ? ' selected="selected"' : ''; // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
				$form .= sprintf('<option value="%s"%s>%s</option>', esc_attr($key), $sel, esc_html($val));
			}
		}

		return sprintf('<select %s %s%s>%s</select>', rtrim(self::__parseatr($data, $defaults)), $extra, $multiple, $form);
	}

	/**
	 * Create select html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param array $options Options for form.
	 *
	 * @return string
	 */
	public static function _previewselect($data = '', $value = '', $extra = '', $options = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$class = sprintf(' %s-form-preview-select', self::$_name);
		$extra_select = array('class' => 'form-control');
		$extra_button = array();
		$extra_url = '';
		$load = true;
		if (is_array($extra)) {
			if (isset($extra['load'])) {
				$load = (bool)$extra['load'];
				unset($extra['load']);
			}
		}
		if ($load) {
			$value = self::getvalue($data['name'], $value);
		}
		if (is_array($extra)) {
			if (isset($extra['select'])) {
				$extra_select = $extra['select'];
				unset($extra['select']);
			}
			if (isset($extra['button'])) {
				$extra_button = $extra['button'];
				unset($extra['button']);
			}
			if (isset($extra['url'])) {
				$extra_url = (string)$extra['url'];
				unset($extra['url']);
			}
		} else {
			$extra_select = $extra_button = $extra;
		}
		$extra_url = str_replace('%25', '%', $extra_url);
		if (is_array($extra_select)) {
			$extra_select['onchange'] = 'jQuery(this).next().find(\'.tinvwl-btn\').attr(\'href\', \'' . esc_attr($extra_url) . '\'.replace(/\%s/i, jQuery(this).val().trim()));';
		} else {
			$extra_select .= ' onchange="jQuery(this).next().find(\'.tinvwl-btn\').attr(\'href\', \'' . esc_attr($extra_url) . '\'.replace(/\%s/i, jQuery(this).val().trim()));"';
		}
		if (is_array($extra_button)) {
			$extra_button['class'] = 'tinvwl-btn smaller';
			$extra_button['href'] = sprintf($extra_url, $value);
		} else {
			$extra_button .= ' class="tinvwl-btn smaller" href="' . sprintf($extra_url, $value);
		}

		return sprintf('<div class="tinvwl-input-group %s">%s<div class="tinvwl-input-group-btn">%s</div></div>', $class, self::_select($data, $value, $extra_select, $options), self::_button($data, __('Preview', 'ti-woocommerce-wishlist'), $extra_button));
	}

	/**
	 * Create upload file elements
	 *
	 * @param string $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _uploadfile($data = '', $value = '', $extra = '')
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$value = self::getvalue($data['name'], $value);
		$extra_field = '';
		$extra_button = '';
		$value_button = '';
		$mimefiles = '';
		if (is_array($extra)) {
			if (isset($extra['type'])) {
				$mimefiles = $extra['type'];
				unset($extra['type']);
			}
			if (isset($extra['field'])) {
				if (is_array($extra['field'])) {
					$extra_field = $extra['field'];
				}
				unset($extra['field']);
			}
			if (isset($extra['button'])) {
				$extra_button = $extra['button'];
				unset($extra['button']);
			}
			if (is_array($extra_field)) {
				foreach ($extra as $key => $val) {
					$extra_field[$key] = $val;
				}
			} else {
				$extra_field .= self::__atrtostr($extra);
			}
			if (is_array($extra_button)) {
				if (isset($extra_button['value'])) {
					$value_button = $extra_button['value'];
					unset($extra_button['value']);
				} elseif (isset($extra_button['name'])) {
					$value_button = $extra_button['name'];
					unset($extra_button['name']);
				}
				foreach ($extra as $key => $val) {
					$extra_button[$key] = $val;
				}
			} else {
				$extra_button .= self::__atrtostr($extra);
			}
		} else {
			$extra_button = $extra_field = $extra;
		} // End if().
		if (!is_array($mimefiles)) {
			$mimefiles = array($mimefiles);
		}
		$mimefiles = array_filter($mimefiles);
		$mimefiles = wp_json_encode($mimefiles);

		$data['type'] = 'text';

		wp_enqueue_media();

		return sprintf("<div class='tinvwl-input-group'>%s%s<div class='tinvwl-input-group-btn'>%s</div></div><script type=\"text/javascript\">jQuery(document).ready(function($){var nn='%s';" . (empty($value) ? "$('.' + nn + '-preview').hide();" : "") . "$('input[name=\"'+nn+'-btn\"]').click(function(e){e.preventDefault();var i=wp.media({multiple:false, library:{type:{$mimefiles}}}).open().on('select',function(e){var u=i.state().get('selection').first();var iu=u.toJSON().url;$('input[name=\"'+nn+'\"]').val(iu);$('.' + nn + '-preview').show();$('.' + nn + '-preview span img').attr('src', iu);});});});</script>", // @codingStandardsIgnoreLine Squiz.Strings.DoubleQuoteUsage.NotRequired
			'<div class="' . $data['name'] . '-preview tinvwl-input-group-btn"><div class="tinvwl-icon-preview"><span><img src="' . $value . '" /></span></div></div>', self::_text($data, $value, $extra_field), self::_text(array(
				'name' => $data['name'] . '-btn',
				'type' => 'button',
				'class' => 'tinvwl-btn white smaller',
			), $value_button, $extra_button),
			$data['name']
		);
	}

	/**
	 * Create select html element with attribute multiple
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param mixed $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param array $options Options for form.
	 *
	 * @return string
	 */
	public static function _multiselect($data = '', $value = array(), $extra = '', $options = array())
	{
		$extra = self::__atrtostr($extra);
		if (stripos($extra, 'multiple') === false) {
			$extra .= ' multiple="multiple"';
		}

		return self::_select($data, $value, $extra, $options);
	}

	/**
	 * Create group input html element with type checkbox
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param mixed $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param array $options Options for form.
	 *
	 * @return string
	 */
	public static function _multicheckbox($data = '', $value = array(), $extra = '', $options = array())
	{
		$class = sprintf(' %s-multicheckbox', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}

		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$value = self::getvalue($data['name'], $value);
		$options = self::getoption($data['name'], $options);
		if (!is_array($value)) {
			$value = array($value);
		}
		$value = array_filter($value);
		if (!is_array($options)) {
			$options = array($options);
		}
		$before = '';
		$after = '';
		if (is_array($extra)) {
			if (isset($extra['before'])) {
				$before = sprintf('<div class="%s-before">%s</div>', self::$_name, $extra['before']);
				unset($extra['before']);
			}
			if (isset($extra['after'])) {
				$after = sprintf('<div class="%s-after">%s</div>', self::$_name, $extra['after']);
				unset($extra['after']);
			}
		}
		$i = 0;
		$name = $data['name'];
		foreach ($options as $key => $_data) {
			$data['name'] = $name . '[' . $i . ']';
			$i++;
			$data['id'] = self::__createid($data['name'] . $key);
			$_form = self::_checkbox($data, in_array($key, $value), array('load' => false), esc_html($key)); // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$options [$key] = self::_label($data['id'], $_data, array(
				'before' => $_form,
			));
		}
		$glue = '</li><li>';

		return sprintf('<div %s >%s<ul class="list"><li>%s</li></ul>%s</div>', self::__atrtostr($extra), $before, implode($glue, $options), $after);
	}

	/**
	 * Create group input html element with type radio
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param mixed $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param array $options Options for form.
	 *
	 * @return string
	 */
	public static function _multiradio($data = '', $value = '', $extra = '', $options = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$value = self::getvalue($data['name'], $value);
		$options = self::getoption($data['name'], $options);
		if (!is_array($value)) {
			$value = array($value);
		}

		if (!is_array($options)) {
			$options = array($options);
		}
		$separator = ' ';
		if (is_array($extra)) {
			if (isset($extra['separator'])) {
				$separator = $extra['separator'];
				unset($extra['separator']);
			}
			$extra['load'] = false;
		} else {
			$extra = array('load' => false);
		}
		$form = '';
		foreach ($options as $key => $_data) {
			$data['id'] = self::__createid($data['name'] . $key);
			$_form = self::_radio($data, in_array($key, $value), $extra, esc_html($key)); // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$form .= self::_label($data['id'], $_data, array(
				'before' => $_form,
			));
			$form .= $separator;
		}

		return $form;
	}

	/**
	 * Create group select html element with time period
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param array $value Value.
	 * @param array $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _timeperiod($data = '', $value = array(), $extra = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$label = array('', '');
		$label_extra = array('', '');
		$value = (array)self::getvalue($data['name'], $value);
		$separator = ' ';
		if (is_array($extra)) {
			if (isset($extra['separator'])) {
				$separator = $extra['separator'];
				unset($extra['separator']);
			}
			if (isset($extra['label'])) {
				$label_extra = $extra['label'];
				unset($extra['label']);
				for ($i = 0; $i < count($label); $i++) { // @codingStandardsIgnoreLine Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
					if (isset($label_extra[$i]['text'])) {
						$label[$i] = $label_extra[$i]['text'];
						unset($label_extra[$i]['text']);
					}
				}
			}
			$extra['load'] = false;
		} else {
			$extra = array('load' => false);
		}
		$form = array();
		for ($i = 0; $i < count($label); $i++) { // @codingStandardsIgnoreLine Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
			$_data = $data;
			$_data['name'] .= "[$i]";
			$label_extra[$i]['after'] = self::_time($_data, (isset($value[$i]) ? $value[$i] : ''), $extra);
			$form[] = self::_label($_data['name'], $label[$i], $label_extra[$i]);
		}
		$form = sprintf('<div class="%s-timeperiod">%s</div>', self::$_name, implode($separator, $form));

		return $form;
	}

	/**
	 * Create group input html element with date period
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param array $value Value.
	 * @param array $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _dateperiod($data = '', $value = array(), $extra = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$label = array('', '');
		$label_extra = array('', '');
		$value = (array)self::getvalue($data['name'], $value);
		$separator = ' ';
		if (is_array($extra)) {
			if (isset($extra['separator'])) {
				$separator = $extra['separator'];
				unset($extra['separator']);
			}
			if (isset($extra['label'])) {
				$label_extra = $extra['label'];
				unset($extra['label']);
				for ($i = 0; $i < count($label); $i++) { // @codingStandardsIgnoreLine Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
					if (isset($label_extra[$i]['text'])) {
						$label[$i] = $label_extra[$i]['text'];
						unset($label_extra[$i]['text']);
					}
				}
			}
			$extra['load'] = false;
		} else {
			$extra = array('load' => false);
		}
		$form = array();
		for ($i = 0; $i < count($label); $i++) { // @codingStandardsIgnoreLine Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
			$_data = $data;
			$_data['name'] .= "[$i]";
			$_data['id'] = self::__createid($data['name'] . '[' . ($i ? 0 : 1) . ']');
			$extra['date']['onClose'] = sprintf("function(selectedDate){ $('#%s').datepicker('option','%sDate',selectedDate);}", $_data['id'], ($i ? 'max' : 'min'));
			$label_extra[$i]['after'] = self::_date($_data, (isset($value[$i]) ? $value[$i] : ''), $extra);
			$form[] = self::_label($_data['name'], $label[$i], $label_extra[$i]);
		}
		$form = sprintf('<div class="%s-dateperiod">%s</div>', self::$_name, implode($separator, $form));

		return $form;
	}

	/**
	 * Create HTML field
	 *
	 * @param string $data Name field or array attributes.
	 * @param string $html HTML text.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _html($data = '', $html = '', $extra = '')
	{
		if (is_array($extra)) {
			foreach ($extra as $key => $value) {
				$html = str_replace('{' . $key . '}', (string)$value, $html);
			}
		}

		return $html;
	}

	/**
	 * Create button html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return type
	 */
	public static function _button($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'button',
			'name' => is_array($data) ? '' : $data,
			'value' => esc_attr($value),
		);

		return sprintf('<button %s%s>%s</button>', self::__parseatr($data, $defaults), self::__atrtostr($extra), $value);
	}

	/**
	 * Create quick submit button html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return type
	 */
	public static function _button_submit_quick($data = '', $value = '', $extra = '')
	{
		return sprintf('<div class="%s-quick-btns">%s</div>', self::$_name, self::_button_submit($data, $value, $extra));
	}

	/**
	 * Create submit button html element
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param string $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return type
	 */
	public static function _button_submit($data = '', $value = '', $extra = '')
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$data['type'] = 'submit';

		return self::_button($data, $value, $extra);
	}

	/**
	 * Create label html element
	 *
	 * @param string $data Name field or array attributes.
	 * @param string $value Value.
	 * @param array $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _label($data = '', $value = '', $extra = array())
	{
		$attr = '';
		if (!empty($data)) {
			$attr .= ' for="' . self::__createid($data) . '"';
		}
		$before = '';
		if (isset($extra['before'])) {
			$before = $extra['before'];
			unset($extra['before']);
		}
		$after = '';
		if (isset($extra['after'])) {
			$after = $extra['after'];
			unset($extra['after']);
		}
		if (is_array($extra) && count($extra) > 0) {
			foreach ($extra as $key => $val) {
				if ('for' == $key) { // WPCS: loose comparison ok.
					continue;
				}
				$attr .= sprintf(' %s="%s"', $key, $val);
			}
		}

		return sprintf('<label %s>%s%s%s</label>', $attr, $before, $value, $after);
	}

	/**
	 * Create tag id for html elements by using name
	 *
	 * @param string $name Name field.
	 * @param string $separator Separator name.
	 *
	 * @return string
	 */
	private static function __createid($name = '', $separator = '_')
	{ // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidFunctionName.MethodDoubleUnderscore
		$name = preg_replace('/[^A-Za-z0-9_-]{1}/i', $separator, $name);
		if (false === strpos($name, self::$_name)) {
			$name = self::$_name . $separator . $name;
		}

		return $name;
	}

	/**
	 * Helper function for this Class.
	 * Merge attribute array and create attribute string for html element
	 *
	 * @param mixed $attributes New attributes for element.
	 * @param array $default Default attributes for element.
	 *
	 * @return string
	 */
	private static function __parseatr($attributes, $default)
	{ // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidFunctionName.MethodDoubleUnderscore
		if (is_array($attributes)) {
			foreach ($default as $key => $val) {
				if (isset($attributes[$key])) {
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0) {
				$default = array_merge($default, $attributes);
			}
		}
		if (!isset($default['name'])) {
			$default['name'] = self::__rndmane();
		}

		$default['id'] = self::__createid((!isset($default['id']) ? $default['name'] : $default['id']));

		$att = '';

		foreach ($default as $key => $val) {
			if (is_array($val)) {
				$val = implode(', ', $val);
			}
			$att .= sprintf('%s="%s" ', $key, $val);
		}

		return $att;
	}

	/**
	 * Helper function for this Class.
	 * Create attribute string for html element
	 *
	 * @param mixed $attributes New attributes for element.
	 *
	 * @return string
	 */
	static function __atrtostr($attributes)
	{ // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidFunctionName.MethodDoubleUnderscore
		if (empty($attributes)) {
			return '';
		}
		if (is_string($attributes)) {
			return sprintf('%s ', $attributes);
		}
		if (is_object($attributes)) {
			$attributes = (array)$attributes;
		}
		if (is_array($attributes)) {
			$atts = '';
			foreach ($attributes as $key => $val) {
				if (is_array($val)) {
					$val = implode(', ', $val);
				}
				$atts .= sprintf('%s="%s" ', $key, $val);
			}

			return $atts;
		}

		return '';
	}

	/**
	 * Helper function for this Class.
	 * Create attribute string for javascript object.
	 *
	 * @param mixed $attributes New attributes for element.
	 *
	 * @return string
	 */
	static function __atrtostrjs($attributes)
	{ // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidFunctionName.MethodDoubleUnderscore
		if (empty($attributes)) {
			return '';
		}
		if (is_string($attributes)) {
			return sprintf('%s ', $attributes);
		}
		if (is_object($attributes)) {
			$attributes = (array)$attributes;
		}
		if (is_array($attributes)) {
			$atts = '';
			foreach ($attributes as $key => $val) {
				if (is_array($val)) {
					$val = implode(', ', $val);
				}
				$atts .= sprintf('%s: %s,', $key, $val);
			}

			return $atts;
		}

		return '';
	}

	/**
	 * Generate random name if thies empty
	 *
	 * @return string
	 */
	private static function __rndmane()
	{ // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidFunctionName.MethodDoubleUnderscore
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$data = '';
		$length = rand(4, 10);
		for ($i = 0; $i < $length; $i++) {
			$data .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $data;
	}

	/**
	 * Using for get value for element
	 *
	 * @param string $data Name value or array values.
	 * @param mixed $value Value.
	 *
	 * @return mixed
	 */
	static function getvalue($data, $value = '')
	{
		if (isset(self::$value[$data])) {
			return self::$value[$data];
		} else {
			$_value = filter_input(INPUT_POST, $data);
			if (is_null($_value)) {
				return $value;
			} else {
				return $_value;
			}
		}
	}

	/**
	 * Set value for form element
	 *
	 * @param string $data Name value or array values.
	 * @param mixed $value Value.
	 *
	 * @return mixed
	 */
	public static function setvalue($data, $value = '')
	{
		$_value = $value;
		if (is_array($data)) {
			foreach (array_keys(self::$value) as $key) {
				if (isset($data[$key])) {
					self::$value[$key] = $data[$key];
					unset($data[$key]);
				}
			}
			if (count($data) > 0) {
				self::$value = array_merge(self::$value, $data);
			}
		} else {
			self::$value[$data] = $value;
		}

		return $_value;
	}

	/**
	 * Remove value for element
	 *
	 * @param string $data Name value or array values.
	 * @param void $value Value.
	 *
	 * @return mixed
	 */
	public static function removevalue($data, $value = '')
	{
		if (is_array($data)) {
			foreach ($data as $val) {
				if (isset(self::$value[$val])) {
					unset(self::$value[$val]);
				}
			}
		} else {
			unset(self::$value[$data]);
		}

		return $value;
	}

	/**
	 * Using for get options for element
	 *
	 * @param string $data Name value or array values.
	 * @param array $option Value.
	 *
	 * @return array
	 */
	static function getoption($data, $option = array())
	{
		if (isset(self::$option[$data])) {
			return self::$option[$data];
		}

		return $option;
	}

	/**
	 * Set options for form element
	 *
	 * @param string $data Name value or array values.
	 * @param array $option Value.
	 *
	 * @return array
	 */
	public static function setoptions($data, $option = array())
	{
		$_option = $option;
		if (is_array($data)) {
			foreach (array_keys(self::$option) as $key) {
				if (isset($data[$key])) {
					self::$option[$key] = $data[$key];
					unset($data[$key]);
				}
			}
			if (count($data) > 0) {
				self::$option = array_merge(self::$option, $data);
			}
		} else {
			self::$option[$data] = $option;
		}

		return $_option;
	}

	/**
	 * Remove options for element
	 *
	 * @param string $data Name value or array values.
	 * @param void $option Value.
	 *
	 * @return mixed
	 */
	public static function removeoptions($data, $option = array())
	{
		if (is_array($data)) {
			foreach ($data as $val) {
				if (isset(self::$option[$val])) {
					unset(self::$option[$val]);
				}
			}
		} else {
			unset(self::$option[$data]);
		}

		return $option;
	}

	/**
	 * Create input html element with type checkbox and class on/off
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param boolean $checked Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param string $value Value for form.
	 *
	 * @return string
	 */
	public static function _checkboxonoff($data = '', $checked = false, $extra = '', $value = 'on')
	{
		$class = sprintf(' %s-form-onoff', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}

		return self::_checkbox($data, $checked, $extra, $value);
	}

	/**
	 * Create group input html element with type radio and class box
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param mixed $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 * @param array $options Options for form.
	 *
	 * @return string
	 */
	public static function _multiradiobox($data = '', $value = '', $extra = '', $options = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$extra_input = '';
		$class = sprintf(' %s-form-multirbox', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] .= $class;
			} else {
				$extra['class'] = $class;
			}
			if (isset($extra['input'])) {
				$extra_input = $extra['input'];
				unset($extra['input']);
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}

		return sprintf('<div id="%s" %s >%s</div>', self::__createid($data['name']), self::__atrtostr($extra), self::_multiradio($data, $value, $extra_input, $options));
	}

	/**
	 * Create input html element with type range
	 *
	 * @param mixed $data Name field or array attributes.
	 * @param integer|float $value Value.
	 * @param mixed $extra Styling or Custom variable.
	 *
	 * @return string
	 */
	public static function _numberrange($data = '', $value = 0, $extra = array())
	{
		if (!is_array($data)) {
			$data = array('name' => $data);
		}
		$data['type'] = 'range';
		$class = sprintf(' %s-form-range', self::$_name);
		if (is_array($extra)) {
			if (isset($extra['class'])) {
				$extra['class'] = $class;
			} else {
				$extra['class'] = $class;
			}
		} else {
			$extra .= sprintf(' class="%s" ', $class);
		}

		return self::_text($data, $value, $extra);
	}
}
