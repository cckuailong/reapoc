=== MapPress Maps for WordPress ===
Contributors: chrisvrichardson
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4339298
Tags: maps, google maps, map, map markers, google map, leaflet maps, leaflet map plugin, google maps plugin, wp google maps, wp google map, map plugin, store locator, google map plugin, map widget,
Requires at least: 5.3
Requires PHP: 5.6
Tested up to: 5.9
Stable tag: 2.73.3

== Description ==
MapPress is the easiest way to add beautiful interactive Google and Leaflet maps to WordPress.

Create unlimited maps and map markers using Gutenberg blocks or the classic editor.  The popup map editor makes creating and editing maps easy!

Upgrade to [MapPress Pro](https://mappresspro.com/mappress) for even more features, including custom markers, searchable mashups, clustering, and much more.  See it in action on the [MapPress Home Page](https://mappresspro.com/mappress) or test it yourself with a [Free Demo Site](https://mappresspro.com/demo)!

[Home Page](https://mappresspro.com/mappress)
[What's New](https://mappresspro.com/whats-new)
[Documentation](https://mappresspro.com/mappress-documentation)
[FAQ](https://mappresspro.com/mappress-faq)
[Support](https://mappresspro.com/mappress-faq)

== Screenshots ==
1. MapPress settings page
2. Map Library in Gutenberg
3. Creating a map
4. Creating a mashup

= Key Features =
* Best Google Maps plugin for WordPress
* Unlimited maps and markers
* Leaflet maps, no API key needed
* Gutenberg editor map blocks
* Classic editor support
* Styled maps
* Marker clustering
* Add maps to any post, page or custom post type
* Responsive maps
* Size maps by pixels, percent or viewport
* Popups with custom text, photos, images, and links
* Google overlays for traffic, bicycling and transit
* Directions from Google Maps
* Geocoders from Google, Nominatim, and Mapbox
* KML map overlays
* Draw polygons, circles, and lines
* Generate maps using PHP
* WPML compatible
* MultiSite compatible

= Pro Version Features =
* Get [MapPress Pro](https://mappresspro.com/mappress) for additional functionality
* Custom markers upload
* Marker editor with thousands of icons, shapes and colors
* Gutenberg "mashup" block for searchable maps and store locators
* Filter locations by taxonomies, tags and categories
* Map widget and mashup widget
* Customizable templates for markers and lists
* Generate maps automatically from custom fields
* Assign marker icons by taxonomy, tag, or category
* Advanced Custom Fields (ACF) integration

= Localization =
Please [Contact me](https://mappresspro.com/chris-contact) if you'd like to provide a translation or an update.  Many thanks to all the folks who have created and udpated translations.

== Installation ==

See full [installation intructions and Documentation](https://mappresspro.com/mappress-documentation)
1. Install and activate the plugin through the 'Plugins' menu in WordPress
1. You should now see a MapPress meta box in in the 'edit posts' screen

[Home Page](https://mappresspro.com/mappress)
[Documentation](https://mappresspro.com/mappress-documentation)
[FAQ](https://mappresspro.com/mappress-faq)
[Support](https://mappresspro.com/forums)

== Frequently Asked Questions ==
Please see the plugin documentation pages:

[Home Page](https://mappresspro.com/mappress)
[Documentation](https://mappresspro.com/mappress-documentation)
[FAQ](https://mappresspro.com/mappress-faq)
[Support](https://mappresspro.com/forums)

== Upgrade ==

1. Deactivate your old MapPress version
1. Delete your old MapPress version (don't worry, the maps are saved in the database)
1. Follow the installation instructions to install the new version

== Changelog ==

= 2.73.3 =
* Fixed: PHP error when loading filters template

= 2.73.2 =
* Fixed: possible PHP error on settings screen
* Fixed: box-sizing added to layout CSS, directions made max width in mini view

= 2.73.1 =
* Fixed: directions not displaying

= 2.73 =
* Important: filters CSS has been updated, please update any custom filter forms to match
* Added: better popup panning and sizing
* Added: new custom JSON styles can be created in the style editor
* Added: setting for filter position (search box or POI list)
* Added: new filter editor in MapPress settings
* Added: post count in filter dropdown
* Added: new filter types: post type and text box
* Added: user-defined labels for filters
* Added: filter display formats (select/checkbox/radio)
* Added: include or exclude specific terms (tags, categories,...) for filters
* Fixed: filters size better in mini mode
* Fixed: POI body not showing in Firefox when thumbnails on left/right
* Fixed: control for attaching posts to maps now shows the correct custom post type
* Fixed: mashup block not updating when query parameters change
* Fixed: Gutenberg boolean attributes defaulting to false when converting classic blocks
* Fixed: settings screen not displaying on some wordpress hosted sites

= 2.72.5 =
* Fixed: list toggle not working

= 2.72.4 =
* Fixed: directions link not working if no POI list present

= 2.72.3 =
* Increment version

= 2.72.2 =
* Changed: allow DOM events to bubble out of the map container

= 2.72.1 =
* Fixed: POI drag and drop sorting not working in editor
* Fixed: shortcodes in AJAX calls now include scripts with map/mashup output

= 2.72 =
* Changed: mashup queries now use a single SQL statement, for hosts that limit SQL size
* Fixed: youtube videos inside popups did not play full screen
* Fixed: [mashup query="current"] now displays current posts correctly

= 2.71.1 =
* Added: option for POI list page size
* Added: option for POI list open/closed when map is loaded
* Fixed: directions not working on Android

= 2.71 =
* Added: enable search for individual maps
* Added: classic editor button updated for compatibility with Enfold theme
* Changed: remove initialOpenDirections parameter
* Changed: speed up Nominatim autocomplete
* Changed: internal updates to ES6 JS for options and maps

= 2.70.1 =
* Changed: clearer highlighting in map list
* Changed: remove beta version
* Changed: remove IE11 support

= 2.70 =
* Added: maps can now be trashed or restored

= 2.69.9 =
* Fixed: spinner prevented map panning

= 2.69.8 =
* Added: improve license checking on settings screen

= 2.69.7 =
* Added: snackbar notifications instead of dialog boxes
* Added: new spinner

= 2.69.6 =
* Fixed: automatic icons not showing in filters dropdown

= 2.69.5 =
* Fixed: allow map display if some map containers are missing
* Fixed: use default style if current style does not exist

= 2.69.4 =
* Fixed: maps crash if center is set

= 2.69.3 =
* Added: iframes now support inline lists, modal popup POIs, and alignment options
* Changed: remove Algolia geocoder
* Fixed: mashup search button hidden by GT editor
* Fixed: KML POIs not working

= 2.69.2 =
* Fixed: iframe does not display when height has no 'px' suffix

= 2.69.1 =
* Changed: improved error reporting
* Changed: improved setup wizard
* Changed: 'post types' setting moved to geocoding section (it only applies to geocoding)
* Fixed: disabled radio buttons not visible in settings

= 2.69 =
* Added: console messages now display in map layout
* Added: search/add button on search bar
* Changed: default size changed to 100%/350px and fixed 425px/350px size removed
* Changed: improved settings to prevent loading Google API twice
* Fixed: make post date and post type available for mashup permalinks

= 2.68.2 =
* Changed: expand styled maps availability

= 2.68.1 =
* Fixed: custom icons missing white outline

= 2.68 =
* Added: icon editor now supports Google Material Icons, foreground color, and font sizes
* Added: icon editor now supports bulk delete
* Added: icon file name can be edited

= 2.67.6 =
* Fixed: error opening map editor when using classic editor and tinyMCE is disabled
* Fixed: warning about block category in map library and classic editor

= 2.67.5 =
* Fixed: error when saving some custom icons with no symbol
* Fixed: ACF integration option not appearing in settings
* Fixed: error preventing saving empty geocoding keys

= 2.67.4 =
* Fixed: invalid image when when reverting to standard red pin icon

= 2.67.3 =
* Fixed: error when using mashup query="current"
* Fixed: clustering max zoom not working for Leaflet when set to 'none'

= 2.67.2 =
* Fixed: unmounted component error from WP widgets screen unounting map
* Fixed: warning about invalid block category on map library screen
* Fixed: warning about toolbargroup vs toolbar
* Fixed: empty mashup filters not saving, and warning about boolean count setting
* Fixed: error from trimArray when saving settings

= 2.67.1 =
* Fixed: error on settings screen

= 2.67 =
* Added: ACF support for geocoding ACF map fields
* Added: setting to include ACF map fields directly in mashups
* Added: Leaflet scrollWheel setting to enable/disable mouse zooming on map
* Added: settings to control sorting and term counts for mashup filters
* Added: filter 'mappress_filter_label' to allow custom html for filter term labels
* Changed: filter values displayed as flex; see mappress.css for info on changing to grid

= 2.66.5 =
* Fixed: console error in theme customizer

= 2.66.4 =
* Fixed: wrong mapid in Gutenberg sidebar

= 2.66.3 =
* Added: MapPress can now read Advanced Custom Fields (ACF) map fields, for use with front-end maps
* Fixed: insert map not working from Gutenberg sidebar

= 2.66.2 =
* Fixed: error in 2.58 widgets screen

= 2.66.1 =
* Added: Gutenberg sidebar document panel listing maps attached to current post/page

= 2.66 =
* Added: settings for marker clustering, to control max zoon & spiderfy
* Added: search bounding box
* Added: warnings about geocoders, Algolia deprecation notice

= 2.65.1 =
* Fixed: mashups not displaying if "compatibility" setting was active

= 2.65 =
* Added: setting to display user location on mashup maps
* Added: setting for mashup minimum search radius
* Added: standard icons can now be overriden, just create an icon with the same name and any extension (gif/png/jpg)
* Added: compatibility setting to output maps in iframes (and prevent theme/plugin compatibility issues)
* Changed: mashup searches are now biased to viewport for all geocoders, not just Google
* Changed: update filter dropdown CSS to use flexbox
* Changed: add output class to term counts in filters dropdown
* Changed: updated widgets for WP 5.8
* Fixed: custom style json not saving

= 2.64.2 =
* Changed: mashup filters now hide terms that are not assigned to any posts
* Changed: update google marker clusterer to v.1.2
* Fixed: missing logo

= 2.64.1 =
* Fixed: JS libraries were not compiled properly

= 2.64 =
* Added: welcome guide and support page
* Fixed: post modal content not shown for logged-out users

= 2.63.2 =
* Fixed: unable to save default custom style

= 2.63.1 =
* Fixed: missing POIs in list
* Changed: menu removed for Leaflet maps
* Changed: deactivation form updated

= 2.63 =
* Added: new map control and modal for editing custom map styles
* Added: new templates for map list items - please update custom templates to match
* Added: filters now support multiple filter taxonomies
* Added: POI list now has pagination controls
* Fixed: icon scale of 0 should be blank (auto scale)

= 2.62.13 =
* Fixed: popup text not sizing to featured image
* Fixed: directions server and POI body settings not saving properly
* Fixed: mapbox default style not applying for maps saved without a map type
* Changed: updated filters 'mappress_post_query' and 'mappress_pre_filter'

= 2.62.12 =
* Fixed: mapbox style preview URL not working
* Added: workaround for Jetpack Infinite Scroll bug that prevents inline scripts

= 2.62.11 =
* Fixed: default POI zoom setting not saving

= 2.62.10 =
* Changed: pagination moved to map list toolbar
* Fixed: compatibility fix for autoptimize

= 2.62.9 =
* Added: map list is now paged
* Fixed: enter key in classic editor title not publishing post
* Fixed: opening map library slow because of 'attach' control
* Fixed: blank settings screen for missing icons widget

= 2.62.8 =
* Fixed: Infobox mouse event bleeding through to map
* Fixed: Mapbox geocoder not setting POI title for new POIs
* Fixed: POI list position setting not working
* Fixed: custom Google styles saving with extra slashes
* Added: better CSS styling for custom map styles table in MapPress settings screen

= 2.62.7 =
* Fixed: alert for missing drawing API

= 2.62.6 =
* Fixed: error with missing layout option for sites upgrading from old versions

= 2.62.5 =
* Fixed: error in geolocate control for non-SSL sites using Leaflet
* Fixed: error when opening Mapbox style settings
* Fixed: some themes hide centered maps with opacity:0

= 2.62.4 =
* Added: Gutenberg map/mashup blocks now support 'wide' and 'full' alignment
* Added: Gutenberg map/mashup blocks should now better support 'left', 'right' and 'center' alignement
* Added: A new 'your location' geolocation control has been added over the map +/- zoom controls
* Removed: the 'your location' control has been removed from the search box
* Changed: POI list is now enabled by default

= 2.62.3 =
* Fixed: settings screen not displayed in free version

= 2.62.2 =
* Fixed: infobox anchor incorrect if icon scale is empty
* Fixed: license check button JS error

= 2.62.1 =
* Added: POI click settings for mashups now include showing the entire post in a full-screen popup
* Changed: POI list widened to 300px
* Fixed: hover effect not deselecting when new POI hovered
* Fixed: error in infobox when POI opened on hover and map hasn't initialized yet

= 2.62 =
* Added: new icon generator to create icons dynamically
* Added: new color picker
* Added: new icon picker

= 2.61.1 =
* Fixed: error "class Mappress_Pro_Settings not found"

= 2.61 =
* Added: default sizes are now responsive (%) based, pixel and viewport sizes are still supported
* Added: default sizes are now editable and sortable
* Added: new settings have been added for users of the free version
* Added: support for the new block-based widget editor
* Changed: plugin setttings screen is now React-based
* Changed: POI editor icon picker and color picker have been converted to React
* Fixed: POI list not updating when POI is changed
* Fixed: conflict with Yoast preview slug temporarily appearing blank
* Fixed: Gutenberg editor setting map alignment on settings screen prevents blocks from being selected
* Fixed: React error for duplicate keys in multiselect

= 2.60 =
* Added: updated flex-based mashup list & popup templates with better thumbnail support - please update any custom template
* Added: updated map popup template and CSS - please update any custom templates
* Added: for classic editor: put the cursor on a shortcode to open that map in editor
* Added: better infobox panning and sizing
* Added: mashup thumbnail position can now be set in MapPress settings
* Added: setting to open POIs on mouse hover
* Added: map library now supports column sorting by mapid, map title, and post title
* Added: window.location is now passed as argument ($args->url) to filter 'mappress_pre_query'
* Added: classic editor is now React-based

= 2.59 =
* Fixed: JS files were not compiling for IE11.  Note that IE11 is NOT a supported browser.
* Fixed: Leaflet icons were not setting scale properly, interfering with marker spiderfier
* Fixed: error when saving map center of 0,0
* Added: new marker class to enable effects
* Added: map data can be recovered after deletion
* Added: highlighting on mouse over for list and marker hover
* Added: better z-indexing for hovered marker

= 2.58.3 =
* Added: selected icon can now be highlighted with a special icon or a circle
* Fixed: error preventing KML files from being added to map

= 2.58.2 =
* Fixed: jQuery warning was causing maps to not display in older WP versions

= 2.58.1 =
* Fixed: places strim trim() not working for maps with center specified by lat/lng
* Fixed: better error message for sites with obsolete jQuery and jQuery UI
* Fixed: admin notices not showing correctly on settings page
* Fixed: added warning for expired license
* Fixed: errors for invalid KML files were not displaying

= 2.58 =
* Added: new infobox with better panning and sizing
* Added: infobox can now be used with Leaflet, in addition to Google
* Added: standard Leaflet popups now fit to the map dimensions
* Added: oembed has been enabled for popups, POIs can now include Youtube videos, music, etc
* Added: maps can be attached/detached from posts in the map list and map library
* Added: query by specific post IDs added for Gutenberg mashup blocks
* Added: map type (style) added to the settings for Gutenberg mashup blocks
* Added: filter 'mappress_poi_excerpt' can be used to control excerpts in map POIs
* Changed: bigger popups for POI editor
* Fixed: warning message for mashup widget in new widget editor
* Fixed: Gutenberg plugin caused blank taxonomy names in mashup blocks

= 2.57.2 =
* Fixed: remove extra translation json file

= 2.57.1 =
* Fixed: language texts were not being picked up for JavaScript texts

= 2.57 =
* Added: new map editor for WordPress Classic editor
* Added: 'add media' button in map POI editor opens WordPress media library to insert images, etc. into POIs
* Added: enabled POI list toggle in editors
* Changed: leaflet popups can now size larger and not overflow the map area
* Changed: editor popups are now larger
* Changed: better positioning of popup pointer over map markers

= 2.56.11 =
* Changed: move WPML settings to free version

= 2.56.10 =
* Added: when using the WPML language plugin, maps are copied when duplicating a post from the original language

= 2.56.9 =
* Changed: Google infoBox popups resize better to fit large content
* Changed: exclude MapPress from Autoptmize to prevent "wp is not defined" errors in WordPress i18n scripts

= 2.56.8 =
* Fixed: mashup markers linking to home page instead of individual posts

= 2.56.7 =
* Changed: CSS improvements in map library
* Changed: Nominatim PHP gecodoer now sleeps for 1 sec to comply with their usage limits
* Changed: improved Nominatim address title parsing

= 2.56.6 =
* Fixed: typo in initial open directions

= 2.56.5 =
* Fixed: fixed version incompatibility in plugin header

= 2.56.4 =
* Changed: enabled Gutenberg blocks for blogs that did not use beta versions

= 2.56.3 =
* Fixed: directions form not working
* Fixed: fatal error for missing icon upload

= 2.56.2 =
* Changed: map font color set to black
* Fixed: Next Gen Gallery plugin interferes with template output

= 2.56.1 =
* Added: refresh button for Gutenberg mashup block
* Added: workaround for Gutenberg 'additional classes' bug (https://core.trac.wordpress.org/ticket/45882)
* Fixed: POI title was not set for POIs added using Nominatim geocoder
* Fixed: autocomplete CSS styles are not applied
* Fixed: Remove console log message

= 2.56.0 =
* Release beta changes from 2.55

= 2.55.2 =
* Added: map library, available in the MapPress settings
* Changed: map 'picker' dialog now saves scroll position

= 2.55.1 =
* Added: Gutenberg map and mashup blocks added
* Added: mini mode setting (width at which the left POI list is collapsed)

= 2.55 =
* Added: clustering is now supported, enable it in the MapPress settings screen
* added: maps with POI list on the left go into 'mini' mode when small: list is hidden and buttons can be used to toggle map or list
* Added: custom icons can now be uploaded directly in the MapPress settings screen
* Added: a new widget is available to display a single map, in addition to the mashup widget
* Added: .jpg icons are now supported in addition to .png and .gif
* Added: mashup filters dropdown includes post counts for each term
* Added: filter 'mappress_options' allows global options to be changed before maps are displayed
* Added: mashup queries now only read post title and body if 'POI content' setting is set to 'Post title + post excerpt'
* Added: new PHP filters are available for mashup queries: mappress_pre_filter($filters), mappress_pre_query($query) and mappress_post_query($map)
* Changed: algolia autocomplete has been removed and replaced with jquery autocomplete
* Changed: because of data discrepancies, algolia places no longer provides autocomplete results for Nominatim/Mapbox geocoders
* Changed: Nominatim/Mapquest autocomplete requests will affect quotas with those services, only Algolia is "free"
* Changed: for the 'left' map layout, directions are displayed in the sidebar like the POI list
* Changed: mashups with blank query now show ALL posts: [mashup] is now equivalent to [mashup query="all"]
* Changed: to show current posts, use [mashup query="current"] instead of leaving query blank
* Changed: PHP templates have been converted to JavaScript templates (for example, file 'map.php')
* Changed: mashup filter CSS has been converted to a grid layout
* Changed: Leaflet updated to version 1.7.1
* Changed: JS map 'close' and 'open' methods changed to 'poiOpen' and 'poiClose'
* Changed: like Google, the Leaflet API is now downloaded from CDN rather than the plugin directory
* Changed: action 'mappress_map_save' receives entire $map object, not just $mapid
* Fixed: some geocoders were not properly using the country/language parameters
* Fixed: color picker not positioning correctly when opened
* Fixed: popup not positioning correctly when opened

= 2.53.6 =
* Changed: additional CSS changes to migrate layout to flex
* Changed: map font switched to sans-serif (overriding theme - change mappress.css if needed)
* Fixed: warning in settings when switching map engine type
* Fixed: warning in PHP log when displaying empty mashups
* Fixed: mashup sometimes deselected current POI in list for small maps

= 2.53.5 =
* Added: curly braces can now be used in mashup queries to pass array parameters
* Changed: map layout switched to CSS flex
* Fixed: exclude mashup shortcodes from Gutenberg REST requests

= 2.53.4 =
* Added: 'dragging' and 'keyboard' shortcode attributes for Leaflet maps
* Fixed: map shortcode not working in archive text widget
* Fixed: a few themes/plugins trigger wp_footer too early, preventing templates from loading before scripts

= 2.53.3 =
* Changed: removed space in version string because of conflict with some CDNs
* Changed: editor made slightly (25px) taller
* Changed: restored mashup option to open post in same tab
* Changed: internal changes to remove correctedAddress property

= 2.53.2 =
* Fixed: custom styles stopped working after MapBox URL change
* Fixed: updated line unused method for PHP 7.2 compatibility checker

= 2.53.1 =
* Changed: minor internal updates to geocoders
* Fixed: centering not working on maps from old versions of plugin

= 2.53 =
* Added: Algolia, Nominatim and MapBox geocoders can be selected on MapPress settings screen
* Changed: updated Algolia Places to latest version
* Changed: updated Leaflet to 1.4.0
* Fixed: added missing left float for mashup thumbnails (to modify it, see '.mapp-body .wp-post-image' in mappress.css)
* Fixed: dead directions link if setting 'none' was imported from prior versions

= 2.52.5 =
* Added: setting to display KML POIs in mashup maps
* Fixed: conflict with 2017 theme and Leaflet zoom buttons
* Fixed: maps output in Gutenberg REST requests when option to load scripts in header is selected

= 2.52.4 =
* Added: a 'check now' button has been added to the settings screen to force license check
* Fixed: priority was too high for default 'mappress_poi_props' filter

= 2.52.3 =
* Added: geocoding errors are now shown on the settings screen

= 2.52.2 =
* Fixed: map controls language code not saving/displaying correctly

= 2.52.1 =
* Fixed: ajax error when opening map for edit

= 2.52 =
* Fixed: prevent enter press in map list search from publishing post
* Changed: updated map editor search/filter function
* Changed: internal function Mappress::ssl() renamed Mappress::is_ssl() - please update any custom directions.php or search.php to use the new name

= 2.51 =
* Added: mashups with a center but no zoom will perform a radius search
* Fixed: KML markers showing POI text instead of text from KML file
* Fixed: mashup initial center and zoom have been improved
* Fixed: mashups not displaying on some servers; updated gzip detection
* Changed: internal mashup query and layers control changes

= 2.50.10 =
* Fixed: PHP 7.2 notice on widgets_init
* Fixed: blank map when initial centering for mashups using Google engine

= 2.50.9 =
* Added: support for high-resolution MapBox tiles on high DPI (retina) devices
* Changed: mashups with multiple POIs will now honor shortcode zoom
* Changed: Leaflet updated to 1.3.4
* Fixed: notice on multisite settings screen when user is not super-admin

= 2.50.8 =
* Added: dropdowns for language/country codes
* Fixed: blank map occurs if other plugins trigger window resize before map is initialized
* Fixed: only print mashup templates in Pro version
* Fixed: continue execution if map container is missing

= 2.50.7 =
* Fixed: Google maps not displaying in editor when no styles defined

= 2.50.6 =
* Fixed: search toolbar not hidden when editing map

= 2.50.5 =
* Fixed: 2nd style bug preventing map display

= 2.50.4 =
* Fixed: bug in styles could prevent map from displaying

= 2.50.3 =
* Added: Mapbox token can be set in wp-config.php for multisite with: define('MAPPRESS_APIKEY_MAPBOX')
* Fixed: editor now shows ALL results when searching for maps
* Fixed: Google styles were not being applied

= 2.50.2 =
* Added: easy entry of Mapbox Studio style names in the MapPress settings screen
* Fixed: custom styles are now retained when switching engines
* Fixed: initialopeninfo parameter was not working for Leaflet maps
* Fixed: better CSS for search box in firefox

= 2.50.1 =
* Fixed: updater was not correctly checking major versions, e.g. 2.50 vs 2.50.1

= 2.50 =
* Added: updated editor map list in the map editor now allows searching across all posts
* Fixed: POI list was not refreshing when filtering in some installations

= 2.49.8 =
* Added: error message when places API not loaded
* Added: enter points into map editor as "lat,lng"
* Changed: mashups should now show all POIs
* Changed: mashups with a single POI will honor shortcode zoom
* Fixed: map not resizing when a tab is displayed

= 2.49.7 =
* Added: filters to set MapBox studio styles and custom map tiles
* Changed: mapping engine now defaults to leaflet
* Fixed: removed links pointing to beta documentation
* Fixed: KML popup could appear multiple times when editing multiple maps
* Fixed: unable to drag markers in editor when using Leaflet

= 2.49.6 =
* Fixed: autocomplete conflict with WordPress tag search fields

= 2.49.5 BETA =
* Fixed: missing leaflet layer control images

= 2.49.4 BETA =
* Fixed: incorrect documentation URLs

= 2.49.3 BETA =
* Fixed: missing files for algolia/leaflet in build version

= 2.49.2 BETA =
* Fixed: bug preventing new POIs using Google engine
* Fixed: mashup POI list not refreshing on first zoom/pan (Google)
* Fixed: map dialog appearing under 'hamburger' menu
* Fixed: removed 'mapp-static' class
* Fixed: notice on some sites during settings save
* Fixed: duplicate texts in .POT translation file

= 2.49.1 BETA =
* Fixed: mashups were using the map-popup and map-item templates instead of mashup-popup and mashup-item
* Fixed: removed extra 'mapp-iw' tags from popup templates
* Fixed: javascript error when centering on invalid place (leaflet only)
* Fixed: removed CSS min-width for leaflet popup
* Fixed: notice in admin plugins search screen (updater was not setting 'plugin' field in version response)
* Fixed: error for leaflet KML layers (setZindexOffset)
* Fixed: maximum mashup POIs was locked at 5

= 2.49 BETA =
* Added: support added for Leaflet, MapBox, and Algolia search
* Added: mashups are now searchable - see the search options on the MapPress settings screen
* Added: client-side templating system and template editor on MapPress settings screen
* Changed: filter fields now output more data (type, meta_type, etc)
* Changed: the 'mini' map mode (with list/map toggles) has been temporarily removed
* Changed: Pro updater is now available by default

= 2.48.7 =
* Fixed: restored inline directions form
* Changed: updated 'directions.php' template file and CSS
* Changed: updated plugin with new mappresspro.com URL
* Changed: re-added use of inline script functions for themes that match tags inside content (json) data

= 2.48.6 =
* Fixed: geocoding bug in editor
* Removed: drawingmanager

= 2.48.5 =
* Fixed: shake in editor caused by Google API update
* Fixed: API compatibility setting not applied in admin screens
* Changed: removed inline directions, shape editor

= 2.48.4 =
* Added: maps now support full-screen control: [mappress fullscreenControl="true"]
* Fixed: map center was changing when resizing
* Fixed: mashups were ignoring custom POI list template
* Changed: mashup filter now shows term names instead of term slugs

= 2.48.3 =
* Added: dismissible notices
* Fixed: incorrect sorting for mashups with 'orderby' clause in query
* Fixed: incorrect CSS in settings screen
* Changed: use gzip only for mashups, not for maps, added checks for PHP libraries

= 2.48.2 =
* Fixed: POI list was being hidden on small maps with 'inline' vertical layout
* Fixed: empty 'ghost' link if post thumbnail is absent

= 2.48.1 =
* Updated version number

= 2.48 =
* Updated version number

= 2.47.10 =
* Fixed: centering error for fixed centers

= 2.47.9 =
* Fixed: PHP 7 error on 'break' statement

= 2.47.8 =
* Fixed: bug in free version prevented editing

= 2.47.7 =
* Fixed: internal javascript error

= 2.47.6 =
* Added: prevent javascript caching when upgrading from free to Pro
* Added: enable gzip compression for AJAX data (depends on server settings)
* Added: 'compatibility' setting to prevent loading maps API by other plugins/themes
* Added: new layout with POIs on left.  Use settings or [mashup layout="left"] for a single map
* Added: shortcode 'center' can be a place or 'user' to geolocate, for example: [mashup center="new york"] or [mashup center="user"]
* Changed: mashup query updated to improve performance
* Changed: faster excerpts for mashup POIs
* Changed: Pro update settings are now enabled by default
* Changed: automatic centering zooms out less whenever possible (viewport padding reduced to zero)
* Changed: clickableIcons defaulted to false (prevent clicks on Google landmarks)
* Changed: removed extra code for xhtml validity checkers
* Changed: updates to directions and template 'map-directions.php'
* Changed: detection for Jetpack infinite scroll improved

= 2.47.5 =
* Added: filter labels can now include an icon in braces, for example [blue-dot]
* Added: filter 'mappress_query_filter' for post-query filtering
* Fixed: workaround for older versions of WordPress which have error in underscore library

= 2.47.4 =
* Added: setting to open POIs in a new tab/window.  For shortcodes use [mashup mashupClick="postnew"]
* Fixed: mashups for custom post types were displaying all post types
* Fixed: maps saved from custom fields in older versions were not auto-centering properly

= 2.47.3 =
* Fixed: Pro version updater bug fixes

= 2.47.2 =
* Fixed: Pro version automatic updater was not notifying about new updates (it may be necessary to update to the current version manually).

= 2.47.1 =
* Changed: the settings for mashup POI title and body display have been combined.  Select either poi title + body or post title + excerpt.  For shortcodes use [mashup mashupbody="poi"] for poi title + body, or [mashup mashupbody="post"] for post title + excerpt

= 2.47 =
* Added: filter dropdown now includes icons from the 'automatic icons' setting (for use as a map legend)
* Fixed: editor spawner multiple icon color pickers, so popups were sometimes in the wrong position
* Changed: 'directions' and 'mashupClick' (POI click behavior for mashups) are now global, set them from the settings screen instead of the shortcode
* Changed: templates 'map.php' and 'map-list.php' were updated, please update any custom templates
* Changed: list CSS class 'mapp-pois'/'mapp-poi' were changed to 'mapp-items'/'mapp-item'

= 2.46.10 =
* Changed: pro updater cache name changed to 'mappress_updater_[action]'

= 2.46.9 =
* Fixed: POI editor error if tinyMCE is disabled in user settings

= 2.46.8 =
* Fixed: default style not being applied
* Fixed: infoWindow displaying even when type = 'none'

= 2.46.7 =
* Fixed: maps in Jquery tabs control not automatically resizing

= 2.46.6 =
* Fixed: sorting in editor not working after first re-sort

= 2.46.5 =
* Fixed: javascript error on settings screen
* Fixed: improved check for multiple API keys
* Fixed: custom template not applied for mashup POIs

= 2.46.4 =
* Fixed: mashup poilist not shown if shortcode enabled and global setting disabled
* Fixed: default map style not always applied

= 2.46.3 =
* Fixed: editor not loading properly in 2.46.2
* Fixed: minZoom not working in shortcodes

= 2.46.2 =
* Fixed: Pro version automatic updater communications errors
* Fixed: icon picker was not working on MapPress settings page

= 2.46.1 =
* Added: updated French translation, thank you to Serge
* Fixed: directions were showing 'null' if empty

= 2.46 =
* Added: NEW TEMPLATES - this release includes all new template files - please update any custom templates to match the new versions.
* Added: mashup results can now be filtered by taxonomies, see the MapPress settings screen
* Added: setting 'Automatic updates' allows automatic updates for the Pro version
* Added: new layout with POIs on the left instead of under map, enable with: [mappresss layout="left"]
* Added: check for multiple Google Maps API loads
* Changed: template names now use hyphens instead of underscores and some names have changed: map_layout.php => map.php, map_poi_list.php => map-list.php
* Changed: CSS class names, for example for the POI list class '.mapp-poi-list' is now '.mapp-list'.  Please update any custom CSS.
* Changed: use the 'hamburger' menu in the map editor to set a map's center & zoom (previously a checkbox was shown in the map editor)
* Changed: mashups now ignore any center/zoom settings and automatically center to show all POIs
* Changed: directions now have a Google Maps link.  Transportation modes have been removed
* Changed: 'my location' shown only for SSL or localhost sites (Google has forbidden geolocation on non-secure sites)
* Changed: maps automatically recenter on screen resize (previously this was the 'adaptive' setting)
* Changed: scripts are now loaded in the header if JetPack Infinite Scroll is enabled

= 2.45.4 =
* Fixed: default custom style not applied when displaying map

= 2.45.3 =
* Fixed: bug when saving quotes (such as image tags) in POI body
* Fixed: custom map styles not displayed properly

= 2.45.2 =
* Removed: directions settings 'from' and 'to'
* Removed: 'adaptive' setting

= 2.45.1 =
* Fixed: maps were not saving attributes correctly including title, size and map type
* Fixed: maps with one POI were not setting zoom correctly
* Fixed: setting for default poi zoom was not saving
* Fixed: setting for POI click ('mashupClick') was ignored if used in shortcode
* Changed: directions 'to' is added by default for all POIs
* Removed: setting for no directions (directions="none")
* Removed: POI links ('poiLinks') setting removed

= 2.45 =
* Added: a new checkbox in the map editor allows you to choose whether to save the center and zoom.  If unchecked, the map will auto-center when displayed.
* Added: POI list sorting can now be set via the settings screen, the default is no sort
* Added: POI list can now be clicked anywhere to select a POI (not just the POI title)
* Added: a new 'hamburger' menu on the map provides map functions including centering and the bicycling, traffic, and transit layers
* Added: a new 'layers' shortcode attribute enables bicycling/transit/traffic layers when map is initially displayed, for example [mappress layers="bicycling"]
* Changed: autoicons function simplified: only 1 rule type is allowed, unfortunately you must *re-enter* any existing autoicons settings.
* Changed: mashup settings are now global including infowindow type ('iwtype'), POI body ('mashupBody'), and POI title ('mashupTtitle') - use the settings screen to set them, NOT the shortcode
* Changed: obsolete map control settings have been removed, and settings have been simplified to match Google defaults
* Changed: when centering a map, if the map has a saved center/zoom the viewport will be reset to that center/zoom, otherwise it will autocenter
* Changed: the 'initialopeninfo' shortcode attribute now accepts only true or false, not a POI index
* Changed: POI 'directions' links have been removed from the POI list (but still show in the POIs).  This allows display of more POIs in the list.
* Removed: mashup link ('mashuplink') setting, POI titles now always link to underlying post in mashups
* Removed: settings 'draggable', 'keyboardshortcuts', 'maptypecontrol', 'maptypecontrolstyle', 'maptypeids', 'overviewmapcontrol', 'overviewmapcontrolopened', 'pancontrol', 'rotatecontrol', 'scalecontrol', 'scrollwheel', 'streetviewcontrol', 'tilt','tooltips','zoomcontrol', 'zoomcontrolstyle'
* Removed: settings 'template', 'templatedirections', 'templatepoi', 'templatepoilist'
* Removed: settings 'bicycling', 'traffic', 'transit', 'initialBicycling', 'initialTraffic', 'initialTransit'
* Removed: 'bigger map' and POI 'zoom' functions
* Removed: the 'mapLinks' setting is removed (these functions have been replaced by the new map menu)