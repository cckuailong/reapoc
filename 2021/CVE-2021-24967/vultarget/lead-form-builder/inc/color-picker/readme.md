# Alpha Color Picker for WordPress #

Ever wanted to pick an RGBa color using the WordPress color picker? Now you can with this jQuery plugin that extends the stock WP color picker.

Here's what it looks like:

![WordPress Alpha Color Picker](https://github.com/BraadMartin/components/blob/master/demos/alpha-color-picker.png)

This was originally designed as a control for the Customizer. That version can be found [here](https://github.com/BraadMartin/components/tree/master/customizer/alpha-color-picker).

This version is in the form of a jQuery plugin and can be used anywhere in the WordPress admin including settings pages, post edit screens, and widgets.

## Usage ##

First enqueue the Alpha Color Picker's js and css files and your own JS file to trigger the picker:

```php
function xxx_admin_enqueue_scripts() {

	wp_enqueue_script(
		'alpha-color-picker',
		PLUGIN_URL . 'alpha-color-picker/alpha-color-picker.js', // Update to where you put the file.
		array( 'jquery', 'wp-color-picker' ), // You must include these here.
		null,
		true
	);

	wp_enqueue_style(
		'alpha-color-picker',
		PLUGIN_URL . 'alpha-color-picker/alpha-color-picker.css', // Update to where you put the file.
		array( 'wp-color-picker' ) // You must include these here.
	);

	// This is the JS file that will contain the trigger script.
	// Set alpha-color-picker as a dependency here.
	wp_enqueue_script(
		'xxx-admin-js',
		PLUGIN_URL . 'js/admin.js', // Update to where you put the file.
		array( 'alpha-color-picker' ),
		null,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'xxx_admin_enqueue_scripts' );
```

Then output a text input with data attributes to set the options:

```php
// Minimum required attributes. Change 'xxx_color_setting' to your option key.
echo '<input type="text" class="alpha-color-picker" name="xxx_color_setting" value="rgba(20,20,20,0.7)" />';

// All of these data-* attributes are optional. The palette can also be passed as 'true' or 'false'.
echo '<input type="text" class="alpha-color-picker" name="xxx_color_setting" value="#00FF00" data-palette="#222|#444|#00CC22|rgba(72,168,42,0.4)" data-default-color="#222" data-show-opacity="true" />';

/**
 * Helper function for outputting an Alpha Color Picker field.
 *
 * @param  string  $class         The class attribute value.
 * @param  string  $name          The name attribute value.
 * @param  string  $value         The initial color value.
 * @param  string  $palette       The palette of colors to include. Supports 'true' for
 *                                default palette, 'false' for no palette, or a | separated
 *                                list of colors.
 * @param  string  $default       The default color value.
 * @param  string  $show_opacity  Whether to show the opacity number on the slider. Supports
 *                                'true' or 'false'.
 */
function xxx_output_alpha_color_picker_field( $class, $name, $value, $palette = 'true', $default = '#222', $show_opacity = 'true' ) {

	printf(
		'<input type="text" class="%s" name="%s" value="%s" data-palette="%s" data-default-color="%s" data-show-opacity="%s" />',
		esc_attr( $class ),
		esc_attr( $name ),
		esc_attr( $value ),
		esc_attr( $palette ),
		esc_attr( $default ),
		esc_attr( $show_opacity )
	);
}

// Example of outputting the field using the helper function.
xxx_output_alpha_color_picker_field(
	'alpha-color-picker',
	'xxx_color_setting',
	'rgba(20,20,20,0.7)',
	'true',
	'#222',
	'true'
);
```

Then trigger the jQuery plugin in your admin JS file:

```js
jQuery( document ).ready( function( $ ) {
	$( 'input.alpha-color-picker' ).alphaColorPicker();
});
```

Simple as that. The `.alphaColorPicker()` method can operate on a single text input or you can pass a selector that matches multiple inputs and it will correctly handle the collection. It will also return all passed in values so it can be chained with other methods.

## More Information ##

I wrote a post with more information about the original Customizer control [on my blog](http://braadmartin.com/alpha-color-picker-control-for-the-wordpress-customizer/).

Feedback and pull requests are encouraged!

## License ##

This control is licensed under the GPL. Please do anything you want with it. :)
