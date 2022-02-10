Simple Admin Pages for WordPress
================================

Simple Admin Pages is a very small utility library to easily add new admin
pages to the WordPress admin interface. It collects WordPress' useful
Settings API into reuseable classes and implements a set of simple controls.


## Settings Pages Supported

- Settings sub-page
- Themes sub-page
- Submenu pages for custom menu items

## General Controls Supported

- Text field
- Textarea field
- Image field
- Toggle (checkbox to enable/disable setting)
- Select dropdown with custom options
- Select dropdown of any post type
- Select dropdown of any taxonomy type
- HTML Content (for instructions, links or other inert text)
- WordPress Editor

## Controls Supported for Special Use Cases

- Date and Time Scheduler
- Google Map Address (with GeoLocation)
- Business Opening Hours

## Usage

Here's an example of how you can use this library to create an admin page.

```
	// Instantiate the Simple Admin Library
	require_once( 'path/to/simple-admin-pages/simple-admin-pages.php' );
	$sap = sap_initialize_library(
		array(
			'version'		=> '2.4.0', // Version of the library
			'lib_url'		=> PLUGIN_URL . '/lib/simple-admin-pages/', // URL path to sap library
		)
	);

	// Create a page for the options under the Settings (options) menu
	$sap->add_page(
		'options', 				// Admin menu which this page should be added to
		array(					// Array of key/value pairs matching the AdminPage class constructor variables
			'id'			=> 'basic-settings',
			'title'			=> __( 'Page Title', 'textdomain' ),
			'menu_title'	=> __( 'menu Title', 'textdomain' ),
			'description'	=> '',
			'capability'	=> 'manage_options' // User permissions access level
		)
	);

	// Create a basic details section
	$sap->add_section(
		'basic-settings',		// Page to add this section to
		array(					// Array of key/value pairs matching the AdminPageSection class constructor variables
			'id'			=> 'basic-details',
			'title'			=> __( 'Basic Details', 'textdomain' ),
			'description'	=> __( 'This section includes some basic details for you to configure.', 'textdomain' )
		)
	);

	// Create the options fields
	$sap->add_setting(
		'basic-settings',		// Page to add this setting to
		'basic-details',		// Section to add this setting to
		'select',				// Type of setting (see sapLibrary::get_setting_classname()
		array(
			'id'			=> 'select-field',
			'title'			=> __( 'Select Field', 'textdomain' ),
			'description'	=> __( 'A demonstration of the select field type.', 'textdomain' ),
			'options'		=> array(
				'one' 	=> __( 'Option 1', 'textdomain' ),
				'two' 	=> __( 'Option 2', 'textdomain' ),
				'three' => __( 'Option 3', 'textdomain' )
			)
		)
	);

	// Allow third-party addons to hook into your settings page
	$sap = apply_filters( 'sap_page_setup', $sap );

	// Register all admin pages and settings with WordPress
	$sap->add_admin_menus();
```

Check out the documentation section below for more examples and explanation.

## License

Simple Admin Pages is released under the GNU GPL 2 or later.

## Requirements

Simple Admin Pages has been tested with WordPress versions 3.5 and above.

## Roadmap

- Better documentation
- Support custom top-level admin pages
- More custom data types

## Documentation

### sap_initialize_library()
Instantiate the library by loading the simple-admin-pages.php file and calling sap_initialize_library. You'll do everything with the $sap object that is returned.

**args**
An array of properties to pass to the library.

*version*
(required)

This used to ensure that plugins can play well together even if they use different versions of the library. The version will convert . to _ and append that to the class names which are loaded. If version 1.0 is passed, the library will attempt to load a class named sapAdminPage_1_0.

*lib_url*
(required)

The lib_url is used to print stylesheets or scripts attached to the library.

```
require_once( 'path/to/simple-admin-pages/simple-admin-pages.php' );
$sap = sap_initialize_library(
	$args = array(
		'version'		=> '2.4.0', // Version of the library
		'lib_url'		=> PLUGIN_URL . '/lib/simple-admin-pages/', // URL path to sap library
	)
);
```

### sapLibrary::add_page()
Create a new page with the library by calling the add_page() method. You can attach the page to the options (Settings) or themes (Appearance) menus or any custom menu.

**type**
(required)

What type of admin menu page to create. Accepts:

- "options" - A subpage of the Settings menu
- "themes" - A subpage of the Appearance menu
- "submenu" - A subpage of any top-level menu item

**args**

An array of properties to pass to the page.

*id*
(required)

All settings attached to this page will be stored with the page ID and can be retrieved with get_options( $page_id ).

*title*
(required)

This will be displayed at the top of the page.

*menu_title*
(required)

The title to display in the menu.

*description*
(optional)

Actually, I think this one isn't used at the moment.

*capability*
(required)

The user permissions access level (capability in WP terms) required to access and edit this page.

*default_tab*
(optional)

If your page will have multiple tabs, you need to specify a default tab to display when the page is initially loaded. This must match the ID used in the add_section() method. *Leave this parameter out if you don't need any tabs.*

```
$sap->add_page(
	$type,
	$args = array(
		'id'			=> 'my-settings',
		'title'			=> __( 'Page Title', 'textdomain' ),
		'menu_title'	=> __( 'menu Title', 'textdomain' ),
		'description'	=> '',
		'capability'	=> 'manage_options'
		'default_tab'	=> 'tab-one',
	)
);
```

### sapLibrary::add_section()
Create a new section to attach it to an existing page.

Sections can act as Tabs or as internal sections within the normal settings flow of a Tab or Page. In other words, you can define a section as a tab, attach a section to another section which is acting as a tab, or ignore tabs altogether to display all of your sections at once. The example below adds a tab and then adds a sub-section to that tab.

**page_id**

Page this section should be attached to. Must match the id passed in add_page().

**args**

An array of properties to pass to the section.

*id*

(required)
Unique slug to which settings will be attached.

*title*
(required)

This will be displayed at the top of the settings section.

*description*
(optional)

An optional description to display below the title.

*is_tab*
(optional)

Set this to true if this section should act like a tab.

*tab*
(optional)

Use this to attach a section to an existing tab.

#### Add a section to a page with no tabs:
```
$sap->add_section(
	$page_id,
	$args = array(
		'id'            => 'basic-details-section',
		'title'         => __( 'Basic Details', 'textdomain' ),
		'description'   => __( 'This section includes some basic details for you to configure.', 'textdomain' )
	)
);
```

#### Add a tab and a sub-section to a page with tabs:
```
/**
 * Create a section that acts as a tab with the is_tab parameter.
 */
$sap->add_section(
	$page_id,
	$args = array(
		'id'            => 'tab-one',
		'title'         => __( 'Tab One', 'textdomain' ),
		'description'   => __( 'This tab includes some settings for you to configure.', 'textdomain' ),
		'is_tab'		=> true,
	)
);

/**
 * Create a sub-section of the tab we just created with the tab parameter.
 */
$sap->add_section(
	$page_id,
	$args = array(
		'id'            => 'section-one-under-tab-one',
		'title'         => __( 'Section One', 'textdomain' ),
		'description'   => __( 'This section includes some settings for you to configure.', 'textdomain' ),
		'tab'			=> 'tab-one',
	)
);
```

### sapLibrary::add_setting()
Create a new setting and attach to an existing section.

There are several types of settings, each with their own input arguments. I'll try to document it more in the future. For now, check out the AdminPageSetting.*.class.php files in /classes/.

**page_id**
(required)

Page this setting should be attached to. Must match the id passed in add_page().

**section_id**
(required)

Section this setting should be attached to. Must match the id passed in add_setting().

**type**
(required)

Type of setting to add. There are currently several types supported and you can extend the library with your own. I'll try to document this further. For now, you can see all the types supported by default at sapLibrary::get_setting_classname().

**args**

An array of properties to pass to the setting.

*id*
(required)

Unique slug under which the setting will be saved. You would then retrieve the setting with:

```
$options = get_option( $page_id );
$options[$setting_id];
```

*title*
(required)

Title of the setting. Typically acts as the field label.

*description*
(optional)

An optional description to display with the setting. Useful for instructions.

*...*

Several setting types have additional parameters. I'll try to document them further.

```
$sap->add_setting(
	$page_id,
	$section_id,
	$type,
	array(
		'id'            => 'my-first-setting',
		'title'         => __( 'My First Setting', 'textdomain' ),
		'description'	=> __( 'A demonstration of my first setting', 'textdomain' );
		...
	)
);
```

### sapLibrary::add_admin_menus()
Once everything is configured, run this method to register the pages with WordPress.

```
	// Before you run add_admin_menus, filter the whole library so that
	// third-party addons can hook into your settings page to add new settings
	// or adjust existing ones.
	$sap = apply_filters( 'sap_page_setup', $sap );

	$sap->add_admin_menus();
```

### Backwards Compatibility
Version 2.0 introduced changes which break backwards compatibility due to the way that the library now stores data in the database. If you are upgrading the version of this library used in your plugin or theme, you must call ```$sap->port_data(2);``` **after** you have declared all of your settings but **before** you call ```$sap->add_admin_menus();```.

*Note: to ensure all of the old options are found and ported, you shouldn't change any of the structure or ids of your settings. Just drop this method into your existing flow.*

This changes the way that your settings are stored in the database. Previously, each setting was stored as its own option. Now all the settings on a page are stored in one row.

You will need to update your plugin to retrieve the settings from their new location. If you previously accessed a setting this way:

```
$my_setting = get_option( $my_setting_id );
```

You should now access the setting this way:

```
$all_page_settings = get_option( $settings_page_id );
$all_page_settings[ $my_setting_id ];
```

## Changelog

- 2.4.0 - 2020-12-04
	- Updating to make this a more global library that can be used by multiple plugins.

- 2.3.0 - 2019-03-18
	- Update pickadate.js to 3.6.1 to fix regression in Chromium
		https://github.com/amsul/pickadate.js/issues/1138

- 2.1.1 - 2018-09-26
	- Allow address geolookup field to receive a Google Maps API key
	- Use https when requesting google maps lookup

- 2.1 - 2017-04-21
	- Add an Image setting type

- 2.0.1 - 2017-03-14
	- Allow settings to receive $args passed to add_settings_field

- 2.0 - 2015-10-28
	- Allow page capability to be modified after the page class is instantiated

- 2.0.a.10 - 2015-08-19
	- Use h1 tags for page titles, in line with WP 4.3 changes
	- Check for has_position before calling on a setting, in case of custom third-party settings being loaded
	- Update pickadate.js lib

- 2.0.a.9 - 2014-11-12
	- SelectPost: Use WP_Query instead of get_posts() so that filters can effect the list
	- Require translateable strings to be declared when adding the setting so the library can conform to the upcoming single textdomain best practice in the .org repos

- 2.0.a.7 - 2014-08-20
	- Only enqueue assets on appropriate admin pages to prevent version conflicts and be a good citizen
	- Enforce stored date/time formats so date format is reliable

- 2.0.a.6 - 2014-08-12
	- Add Google Map Address component
	- Custom settings loaded through the extension path should not use versions

- 2.0.a.5 - 2014-05-15
	- Fix a bug with the Textarea component callback

- 2.0.a.4 - 2014-05-15
	- Only load assets when component is called
	- Revert adding version number to script handles
	- Fix localized script handler for Scheduler
	- Fix pickadate CSS rule specificity

- 2.0.a.3 - 2014-05-14
	- Fix undefined function error in Scheduler javascript when using Firefox
	- Add version number to style and script handles so different versions will be enqueued

- 2.0.a.2 - 2014-05-11
	- Add support for top-level menus
	- Support line breaks in textarea components

- 2.0.a.1 - 2014-04-03
	- Save all data on a page as one row in wp_options

- 1.1 - never released
	- Support themes pages
	- Support submenu pages for custom menu items

- 1.0 - 2013-11-20
	- Initial release
