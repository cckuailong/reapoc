=== Code Snippets ===
Contributors: bungeshea, ver3
Donate link: https://sheabunge.com/donate
Tags: snippets, functions, code, php, multisite
License: MIT
License URI: license.txt
Stable tag: 2.14.2
Tested up to: 5.8.1

An easy, clean and simple way to run code snippets on your site.

== Description ==

Code Snippets is an easy, clean and simple way to run code snippets on your site. It removes the need to add custom snippets to your theme's `functions.php` file.

A snippet is a small chunk of PHP code that you can use to extend the functionality of a WordPress-powered website; essentially a mini-plugin with less load on your site.
Most snippet-hosting sites tell you to add snippet code to your active theme's `functions.php` file, which can get rather long and messy after a while.
Code Snippets changes that by providing a GUI interface for adding snippets and **actually running them on your site** just as if they were in your theme's `functions.php` file.

Code Snippets provides graphical interface, similar to the Plugins menu, for managing snippets. Snippets can be activated and deactivated, just like plugins. The snippet editor includes fields for a name, a visual editor-enabled description, tags to allow you to categorize snippets, and a full-featured code editor. Snippets can be exported for transfer to another site, either in JSON for later importing by the Code Snippets plugin, or in PHP for creating your own plugin or theme.

If you have any feedback, issues, or suggestions for improvements please leave a topic in the [Support Forum](https://wordpress.org/support/plugin/code-snippets), or [join the community on Facebook](https://facebook.com/groups/codesnippetsplugin).

If you like this plugin, or it is useful to you in some way, please consider reviewing it on [WordPress.org](https://wordpress.org/support/view/plugin-reviews/code-snippets).

If you'd like to contribute to the plugin's code or translate it into another language, you can [fork the plugin on GitHub](https://github.com/sheabunge/code-snippets).

== Translations ==

Code Snippets can be used in these different languages thanks to the following translators:

* Belarusian – [Hrank.com](https://www.hrank.com)
* Brazilian Portuguese – [Bruno Borges](http://brunoborges.info)
* Chinese – [Jincheng Shan](http://shanjincheng.com) and [诗语](https://profiles.wordpress.org/f2010525/)
* Chinese (Taiwan) – [Alex Lion](https://www.alexclassroom.com/) and [Chun-Chih Cheng](https://profiles.wordpress.org/alex1114/)
* Croatian – [Borisa Djuraskovic from Web Hosting Hub](https://www.webhostinghub.com/)
* Czech  – [Lukáš Tesař](mailto:lukastesar03@gmail.com) and [Jakub Humpolec](https://profiles.wordpress.org/jumphy/)
* Danish – [Finn Sommer Jensen](https://profiles.wordpress.org/finnsommer/)
* Dutch – [Sander Spies](https://github.com/sander1), [Peter Smits](https://psmits.com) and [mother.of.code.a11n](https://profiles.wordpress.org/imazed/)
* English (New Zealand) and English (UK) – [webaware](https://profiles.wordpress.org/webaware/)
* English (South Africa) – [webaware](https://profiles.wordpress.org/webaware/) and [Ian Barnes](https://profiles.wordpress.org/ianbarnes/)
* French – [momo-fr](https://www.momofr.net/), [Didier Demory](https://www.wptrads.com/), [Cyrille Sanson](https://100son.net/) and [Shea Bunge](https://sheabunge.com)
* French (Canada) – [Dominic Desbiens](https://www.dominicdesbiens.com/)
* German – [Mario Siegmann](https://web-alltag.de/), [Joerg Knoerchen](https://www.sensorgrafie.de/), [David Decker](https://deckerweb.de) and [Andreas](https://profiles.wordpress.org/perryzelda/)
* Greek – [Konstantinos Megas](https://profiles.wordpress.org/nextdoorpanda/) and [Toni Bishop from Jrop](https://www.jrop.com/)
* Indonesian – [Jordan Silaen from ChameleonJohn.com](https://www.chameleonjohn.com/)
* Italian – [Luisa Ravelli](https://profiles.wordpress.org/darkavenger/) and [ElectricFeet](https://profiles.wordpress.org/electricfeet/)
* Japanese – [mt8](https://mt8.biz/), [Takakazu Nagaya](https://profiles.wordpress.org/tanagaya/), [Naoko Takano](https://naoko.blog/) and [melvas](https://profiles.wordpress.org/melvas/)
* Persian – [Mohammad Novintanon](http://mnovintan.ir/)
* Russian – [Alexander Samsonov](http://www.wordpressplugins.ru/administration/code-snippets.html), [Yui](https://profiles.wordpress.org/fierevere/), [Denis Yanchevskiy](https://denisco.pro/) and [krioteh](https://profiles.wordpress.org/krioteh/)
* Slovak – [Ján Fajčák](https://wp.sk)
* Spanish (Colombia) and Spanish (Ecuador) – [Javier Esteban](https://javieresteban.org/)
* Spanish (Spain) – [Ibidem Group](https://www.ibidemgroup.com), [Javier Esteban](https://javieresteban.org/), [Fernando Tellado](https://ayudawp.com/) and [Juanma Aranda](https://juanmaaranda.com/)
* Spanish (Venezuela) – [Yordan Soares](https://yordansoar.es/)
* Swedish – [Argentum](https://profiles.wordpress.org/argentum/), [Fredrik](https://profiles.wordpress.org/elbogen/) and [Tor-Bjorn Fjellner](https://profiles.wordpress.org/tobifjellner/)
* Urdu – [Samuel Badree](https://mobilemall.pk/)
* Vietnamese – [Tuan Phan](https://profiles.wordpress.org/khunglong/)

== Installation ==

= Automatic installation =

1. Log into your WordPress admin
2. Click __Plugins__
3. Click __Add New__
4. Search for __Code Snippets__
5. Click __Install Now__ under "Code Snippets"
6. Activate the plugin

= Manual installation =

1. Download the plugin
2. Extract the contents of the zip file
3. Upload the contents of the zip file to the `wp-content/plugins/` folder of your WordPress installation
4. Activate the Code Snippets plugin from 'Plugins' page.

Network Activating Code Snippets through the Network Dashboard will enable a special interface for running snippets across the entire network.

== Frequently Asked Questions ==

A full list of our Frequently Asked Questions can be found at [help.codesnippets.pro](https://help.codesnippets.pro/collection/3-faq).

= How do I insert snippets into the post text editor? =
Code Snippets v2 is designed to be used with PHP Function Snippets, which are not intended to be inserted as content, but instead persistently on your site as if they were part of a plugin or your theme's functions.php file.

Code Snippets v3 will bring a new type of snippet, Content Snippets, which are designed to be included in the content of a post or page. Stay tuned for the release!

= How can I recover my site if it is crashed by a buggy snippet? =

You can recover your site by enabling the Code Snippets safe mode feature. Instructions for how to turn it on are available here: https://help.codesnippets.pro/article/12-safe-mode.

= Will I lose my snippets if I change the theme or upgrade WordPress? =
No, the snippets are stored in the WordPress database, independent of the theme and unaffected by WordPress upgrades.

= Can the plugin be completely uninstalled? =
If you enable the 'Complete Uninstall' option on the plugin settings page, Code Snippets will clean up all of its data when deleted through the WordPress 'Plugins' menu. This includes all of the stored snippets. If you would like to preserve the snippets, ensure they are exported first.

= Can I copy snippets that I have created to another WordPress site? =
Yes! You can individually export a single snippet using the link below the snippet name on the 'Manage Snippets' page or bulk export multiple snippets using the 'Bulk Actions' feature. Snippets can later be imported using the 'Import Snippets' page by uploading the export file.

= Can I export my snippets to PHP for a site where I'm not using the Code Snippets plugin? =
Yes. Click the checkboxes next to the snippets you want to export, and then choose **Export to PHP** from the Bulk Actions menu and click Apply. The generated PHP file will contain the exported snippets' code, as well as their name and description in comments.

= Can I run network-wide snippets on a multisite installation? =
You can run snippets across an entire multisite network by **Network Activating** Code Snippets through the Network Dashboard. You can also activate Code Snippets just on the main site, and then individually on other sites of your choice.

= Where are the snippets stored in my WordPress database? =
Snippets are stored in the `wp_snippets` table in the WordPress database. The table name may differ depending on what your table prefix is set to.

= Where can I go for help or suggest new features? =
You can get help with Code Snippets, report bugs or errors, and suggest new features and improvements either on the [WordPress Support Forums](https://wordpress.org/support/plugin/code-snippets) or on [GitHub](https://github.com/sheabunge/code-snippets)

= How can I help contribute to the development of the Code Snippets plugin? =
The best way to do this is to fork the [repository on GitHub](https://github.com/sheabunge/code-snippets) and send a pull request.

== Screenshots ==

1. Managing existing snippets
2. Adding a new snippet
3. Editing a snippet
4. Importing snippets from an export file

== Changelog ==

= 2.14.2 (9 Sep 2021) =
* Fixed: Prevent network snippets table from being created on single-site installs.
* Added translations:
    - Spanish by [Ibidem Group](https://www.ibidemgroup.com)
    - Urdu by [Samuel Badree](https://mobilemall.pk/)
    - Greek by [Toni Bishop from Jrop](https://www.jrop.com/)
* Added: Support for `:class` syntax to the code validator.
* Added: PHP8 support to the code linter.
* Added: Color picker feature to the code editor.
* Added: Failsafe to prevent multiple versions of Code Snippets from running simultaneously.

= 2.14.1 (10 Mar 2021) =
* Added: Czech translation by [Lukáš Tesař](https://github.com/atomicf4ll).
* Fixed: Code validator now supports `function_exists` and `class_exists` checks.
* Fixed: Code validator now supports anonymous functions.
* Fixed: Issue with saving the hidden columns setting.
* Fixed: Replaced the outdated tag-it library with [tagger](https://github.com/jcubic/tagger) for powering the snippet tags editor.
* Added: Code direction setting for RTL users.
* Updated CodeMirror to version 5.59.4.
* Added: Additional action hooks and search API thanks to [@Spreeuw](https://github.com/Spreeuw).

= 2.14.0 (26 Jan 2020) =
* Updated CodeMirror to version 5.50.2.
* Added: Basic error checking for duplicate functions and classes.
* Updated Italian translations to fix display issues – thanks to [Francesco Marino](https://360fun.net).
* Fixed: Ordering snippets in the table by name will now be case-insensitive.
* Added: Additional API options for retrieving snippets.
* Fixed: Code editor will now properly highlight embedded HTML, CSS and JavaScript code.
* Changed the indicator color for inactive snippets from red to grey.
* Fixed a bug preventing the editor theme from being set to default.
* Added: Store the time and date when each snippet was last modified.
* Added: Basic error checking when activating snippets.
* Fixed: Ensure that imported snippets are always inactive.
* Fixed: Check the referer on the import menu to prevent CSRF attacks. Thanks to [Chloe with the Wordfence Threat Intelligence team](https://www.wordfence.com/blog/author/wfchloe/) for reporting.
* Fixed: Ensure that individual snippet action links use proper verification.

= 2.13.3 (13 Mar 2019) =
* Added: Hover effect to activation switches.
* Added: Additional save buttons above snippet editor.
* Added: List save keyboard shortcuts to the help tooltip.
* Added: Change "no items found" message when search filters match nothing.
* Fixed: Calling deprecated code in database upgrade process.
* Fixed: Include snippet priority in export files.
* Fixed: Use Unix newlines in code export file.
* Updated CodeMirror to version 5.44.0.
* Fixed: Correctly register snippet tables with WordPress to prevent database repair errors [[#](https://wordpress.org/support/topic/database-corrupted-4/)]
* Fixed: CodeMirror indentation settings being applied incorrectly

= 2.13.2 (25 Jan 2019) =
* Removed potentially problematic cursor position saving feature

= 2.13.1 (22 Jan 2019) =
* Added: Add menu buttons to settings page for compact menu
* Updated: French translation updated thanks to momo-fr
* Fixed: Split code editor and tag editor scripts into their own files to prevent dependency errors
* Fixed: Handling of single-use shared network snippets
* Fixed: Minor translation template issues
* Added: Help tooltop to snippet editor for keyboard shortcuts, thanks to Michael DeWitt
* Improved: Added button for executing single-use snippets to snippets table
* Added: Sample snippet for ordering snippets table by name by default
* Updated CodeMirror to version 5.43.0

= 2.13.0 (17 Dec 2018) =
* Added: Search/replace functionality to the snippet editor. [See here for a list of keyboard shortcuts.](https://codemirror.net/demo/search.html) [[#](https://wordpress.org/support/topic/feature-request-codemirror-search-and-replace/)]
* Updated CodeMirror to version 5.42.0
* Added: Option to make admin menu more compact
* Fixed: Problem clearing recently active snippet list
* Improved: Integration between plugin and the CodeMirror library, to prevent collisions
* Improved: Added additional styles to editor settings preview
* Added: PHP linter to code editor
* Improved: Use external scripts instead of inline scripts
* Fixed: Missing functionality for 'Auto Close Brackets' and 'Highlight Selection Matches' settings

= 2.12.1 (15 Nov 2018) =
* Improved: CodeMirror updated to version 5.41.0
* Improved: Attempt to create database columns that might be missing after a table upgrade
* Improved: Streamlined upgrade process
* Fixed: Interface layout on sites using right-to-left languages
* Improved: Made search box appear at top of page on mobile [[#](https://wordpress.org/support/topic/small-modification-for-mobile-ux/)]
* Updated screenshots

= 2.12.0 (23 Sep 2018) =
* Fixed: Prevented hidden columns setting from reverting to default
* Improved: Updated import page to improve usability
* Improved: Added Import button next to page title on manage page
* Improved: Added coloured banner indicating whether a snippet is active when editing
* Update CodeMirror to 5.40.0

= 2.11.0 (24 Jul 2018) =
* Added: Ability to assign a priority to snippets, to determine the order in which they are executed
* Improvement: The editor cursor position will be preserved when saving a snippet
* Added: Pressing Ctrl/Cmd + S while writing a snippet will save it
* Added: Shadow opening PHP tag above the code editor
* Improved: Updated the message shown when there are no snippets
* Added: Install sample snippets when the plugin is installed
* Improved: Show all available tags when selecting the tag field
* Added: Filter hook for controlling the default list table view
* Added: Action for cloning snippets

= 2.10.2 (21 Jul 2018) =
* Added: Button to reset settings to their default values
* Improved: Made uninstall cleanup optional through a plugin setting
* Fixed: Applied formatting filters to snippet descriptions in the table
* Improved: Ordered tags by name in the filter dropdown menu
* Fixed: Incorrectly translated strings
* Added: Belarusian translation by Hrank.com
* Improved: Enabled sorting snippets table by tags
* Updated CodeMirror to version 5.39.0

= 2.10.1 (10 Feb 2018) =
* Fixed: Prevent errors when trying to export no snippets
* Fixed: Use wp_json_encode() to encode export data
* Fixed: Check both the file extension and MIME type of uploaded import files

= 2.10.0 (18 Jan 2018) =
* Improved: Added support for importing from multiple export files at once
* Improved: Unbold the titles of inactive snippets for greater visual distinction
* Added: New scope for single-use snippets
* Improved: Don't show network snippets on subsites by default, and only to super admins
* Improved: Export snippets to JSON instead of XML
* Improved: More options for importing duplicate snippets
* Improved: Use strings for representing scopes internally instead of numbers
* Added: Allowed plugin settings to be unified on multisite through Network Settings option
* Fixed: Issue with incorrectly treating network snippets as site-wide for code validation
* Improved: Rename 'Export to PHP' to 'Download', and add button to edit snippet page

**[The full changelog is available on GitHub](https://github.com/sheabunge/code-snippets/blob/master/CHANGELOG.md)**

== Upgrade Notice ==

= 2.14.2 =
New color picker and code validator improvements.

= 2.14.0 =
Security update, last modified dates, and other improvements

= 2.13.1 =
Fixes for single-use snippets and French translation

= 2.11.0 =
Snippet priorities and other small features

= 2.10.0 =
Improvements for multisite and new single-use snippet scope
