=== Link Library ===
Contributors: jackdewey
Donate link: https://ylefebvre.github.io/wordpress-plugins/link-library/
Tags: link, list, directory, page, library, AJAX, RSS, feeds, inline, search, paging, add, submit, import, batch, pop-up
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 7.2.7

The purpose of this plugin is to add the ability to output a list of link categories and a complete list of links with notes and descriptions.

== Description ==

This plugin is used to be able to create a page on your web site that will contain a list of all of the link categories that you have defined inside of the Links section of the Wordpress administration, along with all links defined in these categories. The user can select a sub-set of categories to be displayed or not displayed. Link Library also offers a mode where only one category is shown at a time, using AJAX or HTML Get queries to load other categories based on user input. It can display a search box and find results based on queries. It can also display a form to accept user submissions and allow the site administrator to moderate them before listing the new entries. Finally, it can generate an RSS feed for your link collection so that people can be aware of additions to your link library.

For links that carry RSS feed information, Link Library can display a preview of the latest feed items inline with the all links or in a separate preview window.

This plugin uses the filter method to add contents to the pages. It also contains a configuration page under the admin tools to be able to configure all outputs. This page allows for an unlimited number of different configurations to be created to display links on different pages of a Wordpress site.

For screenshots showing how to achieve these results, check out my [site](https://github.com/ylefebvre/link-library/wiki)

All pages are generated using different configurations all managed by Link Library. Link Library is compatible with the [Simple Custom Post Order](https://en-ca.wordpress.org/plugins/simple-custom-post-order/) plugin to define category and link ordering.

* [Changelog](http://wordpress.org/extend/plugins/link-library/other_notes/)
* [Support Forum](https://wordpress.org/support/plugin/link-library/)

== Installation ==

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin

To get a basic Link Library list showing on one of your Wordpress pages:<br />
1. In the Wordpress Admin, create a new page and type the following text, where # should be replaced by the Settings Set number:<br />
   [link-library settings=#]

1. To add a list of categories to jump to a certain point in the list, add the following text to your page:<br />
   [link-library-cats settings=#]<br />

1. To add a search box to your Link Library list, add the following text to your page:<br />
   [link-library-search]

1. To add a form for users to be able to submit new links:<br />
   [link-library-addlink settings=#]

In addition to specifying a library, categories to be displayed can be specified using addition keywords. Read the FAQ for more information on this topic.

Further configuration is available under the Link Library Settings panel.

== Changelog ==

= 7.2.7 =
* Increased character limit for user form fields from 255 to 1024 characters

= 7.2.6 =
* Further fixes for hide donation and display of new feature box

= 7.2.5 =
* Fixes around Hide donation option and display of new feature box

= 7.2.4 =
* Added new option in search section to look for results in all categories even if library is configured to display from a subset of categories
* Fix block_categories editor warnings for deprecated filters

= 7.2.3 =
* Fixed issue with displaying library count for library other than #1

= 7.2.2 =
* Fix for list of categories in user-submission form when using hierarchical categories
* Fix for PHP warning when displaying link count

= 7.2.1 =
* Add support for WordPress mshots thumbnail generator

= 7.2.0 =
* Links can now be sorted by number of hits in admin

= 7.1.9 =
* Added new options to only display links that are updated/new and to specify for how many days links should be considered as updated/new

= 7.1.8 =
* Fix to only make clear: both div display after categories when rendering category list using block editor

= 7.1.7 = 
* Fix to make RSS feed only accessible when option to publish it is actually checked

= 7.1.6 =
* Fixed for RSS Feed Checking tool

= 7.1.5 =
* Added new RSS Feed checking tool under Link Checking Tools

= 7.1.4 =
* Remove current page number link from pagination

= 7.1.3 =
* Additional fix for updated tag
* Fix for pagination system

= 7.1.2 =
* Changed updated tag to use publication date to determine if it's displayed

= 7.1.1 =
* Fix in Link Library Broken Link Checker
* Fix for pagination links in HTML GET + Permalink mode

= 7.1.0 =
* Added new [rss-library] shortcode to display a combined RSS library feed from all links
* Enhanced bookmarket to grab selected text as new link description as well as get RSS feed link. If more than one link is found, asks the user which one to use.
* Improved breadcrumb generation code so that the last part of the breakcrumb does not have a link. Also fixed links for sub-categories.
* Fix for PHP installations that don't support short array syntax

= 7.0.8 =
* Add support for taglistoverride parameter in link-library-cats shortcode
* Add parameter to override tag list in Link Library Category block

= 7.0.7 =
* Fix to avoid masonry layout becoming a single column when first item is hidden
* Added a new option to Categories tab in Library Configuration to hide empty categories from category list

= 7.0.6 =
* Fixed [link-library-cats] shortcode to react to tag selection
* Fix for duplicate link checker where it was previous showing items from other post types with same name as links

= 7.0.5 =
* Improved broken link checker to report on the type of redirection that took place

= 7.0.4 =
* Added option to include links in main site RSS feed
* Added option in RSS feed configuration to select displaying the link updated date value or the publication date

= 7.0.3 =
* Corrected problem with duplicate detection when using bookmarklet

= 7.0.2 =
* Added labels in user-submission form for accessibility
* Removed unnecessary file

= 7.0.1 =
* Fix to avoid duplicate media library entries for link images
* Fixed issue with pagination when filtering by link tags
* Fix to show sub-categories in user-form when no top-level categories are selected
* Bump up copyright version
* Add missing styling for block editor warning box
* Fix issue where link tags not displayed if option to suppress empty output is selected

= 7.0 =
* New admin icon for Link Library using dashicons
* Fix for better match of duplicate links entered through bookmarklet
* Updated new feature pop-up

= 6.8.19 =
* Check if has_blocks and parse_blocks functions exist before calling it for pre-5.0 WordPress

= 6.8.18 =
* Added support for Block editor
* Added new Masonry link display methods
* Added a new display mode for categories to allow you to toggle category visibility
* Added two new presets showcasing Masonry layouts
* Added new filter hook link_library_export_all_links to allow filtering when exporting all links

= 6.8.17 =
* Fix for ll_get_title function definition

= 6.8.16 =
* Added global search results customization for links
* Realigned colors in advanced configuration grid

= 6.8.15 =
* Fix for PHP notice in render-link-library-sc.php, line 930

= 6.8.14 =
* Fix for RSS inline items publication date limit
* Added code to translate word ALL in alpha filter function

= 6.8.13 =
* Improved RSS Inline item rendering by using transients
* Updated links to new github site

= 6.8.12 =
* Fix bug with library-specific stylesheet editor

= 6.8.11 =
* Library-specific stylesheets are actually output in pages
* Added import/export functions to global options
* Fix tabs in general options section
* Refreshed some aspects of Link Library GUI to match current WordPress styling

= 6.8.10 =
* Added per-library configutation stylesheet editor
* Added new field to user submission form to select an existing link as reference. Useful to allow users to propose a correction to a link
* Added custom URL, text and list fields to user submissions
* Stylesheet editors now do syntax highlighting

= 6.8.9 =
* Added new options to specify a custom WP Query parameter and a value for this extra parameter to allow for custom post filtering
* Allowed users to reorder fields in user submission form
* Fixes for HTML + Permalink mode when working in sub-directories

= 6.8.8 =
* Category name links now use permalink configuration data if available

= 6.8.7 =
* Removed unnecessary double call to showcategory function
* Add option to Show category name to only show current category or to show all categories assigned to the link
* Add option not to make category name a link even if link is assigned to category

= 6.8.6 =
* Added new link checker tool to find links without any category assigned
* Hide category filter from link list if no categories defined
* Hide tag filter from link list if no tags defined

= 6.8.5 =
* Links can now be ordered by using the values stored in custom text fields
* Inline RSS feed display now shows an error if RSS feed is invalid
* Added new div-based preset and re-wrote preset page code to be more flexible
* Added new parameter for [link-library-cats] shortcode to specify target library (targetlibrary)
* Removed accessibe menu

= 6.8.4 =
* Added new option to most advanced configuration fields to suppress their output if no value is set
* Fixed issue where link hits are not displayed if value is 0 and library configured not to output empty items

= 6.8.3 =
* Fixed issue where a link with a bad RSS feed shows RSS items from the previous link's feed
* Fixed issue where custom url fields are not saved correctly if a link image is present

= 6.8.2 =
* Fixed issue with dates of RSS feed items

= 6.8.1 =
* Fixed issue where Link Library message display in e-mails did not all respect supression setting under General Options

= 6.8 =
* Fixed issues with links export and import functions
* Added ability to export and import custom fields

= 6.7.15 =
* Fixed issue with being able to view links as single page items
* Improvements for bookmarklet existing link verification

= 6.7.14 =
* Added new function where bookmarklet checks for existing link URL and redirects to edit that link if one is found

= 6.7.13 =
* Added ability to specify publication date for imported links

= 6.7.12 =
* Fix bug in generation of target field for web link

= 6.7.11 =
* Add support for lazy loading images

= 6.7.10 =
* Fix to avoid permalinks / rewrite issues on some installations

= 6.7.9 =
* Fixed issues with quotes in HTML fields for custom lists
* Fixed issue with custom text fields

= 6.7.8 =
* Fix for some users experiencing issues with AJAX mode. Extra line breaks were being output on some configurations
* Added custom text fields and custom list fields

= 6.7.7 =
* Reduced the transient time for form submissions to 5 seconds instead of 60
* Fixed warning in category display
* User submission message is now transmitted in transient expiring after 10 seconds

= 6.7.6 =
* Added new options to linkorderoverride parameter when calling [link-library] shortcode to reflect list of choices in interface
* Cleared some PHP warnings related to recent changes
* Fix for show not cat on startup in AJAX mode not working
* Modified link hits logic to disregard visits from administrators to links

= 6.7.5 =
* Fixed to support new option to allow categories to be used to refine search results when displayed in more configurations

= 6.7.4 =
* Fixed warning in admin when activatinguser voting
* Changed mechanism for user-submitted links to repost info if anything is missing or wrong. Now using transients.
* Fixed issues with escaped characters when users submit new links

= 6.7.3 =
* Fixed issues with user-submission form

= 6.7.2 =
* Added new option to allow categories to be used to refine search results when displayed
* Fixed issues with user-submission form

= 6.7.1 =
* Fixed issue with user vote custom label not staying when you upvote links

= 6.7 =
* Added update message for new version
* Removed Accessibe banner ads and moved Accessibe menu to bottom of list
* Fixed issue with New tag displayed before link name not working correctly if link name is not the first field or is in a table

= 6.6.12 =
* Fixed bug with image link not displayed at the right place with new dedicated page option

= 6.6.11 =
* Added two options to link image display. Can now link to dedicated page or not link to any page

= 6.6.10 =
* Added ability to set label of user vote button
* Fixed bugs with user voting system
* When activating user voting system, all existing links will be assigned an initial vote value of 0

= 6.6.9 =
* First implementation of user voting system. New block in Advanced page for User Votes. Ability to sort links by number of user votes. Open to user feedback
* Added new option to display link names only, not as link. Set under source for Link Name field.

= 6.6.8 =
* Added new section under General Options to add custom URL fields to Link Library. Fields can be enabled and named to appear in all relevant places (Link editor, advanced configuration table for links)
* Removed debug code from user submission code to avoid issues with headers submitted too early

= 6.6.7 =
* Revamped user submission section of library configuration to implement table approach
* Added code to fortify LL core
* Added stylesheet rules to make user-submission form look better on mobile
* Added option to delete media attachment or local site file assigned as link URL

= 6.6.6 =
* Added multi select lists in the Moderation screen to allow users to assign one or more tags or categories to user-submitted links, or make changes to the ones assigned by users
* Added ability to upload a file for the link to point to instead of specifying a URL
* Added new option to [link-library-addlink] shortcode to override default category in category list (addlinkdefaultcatoverride)

= 6.6.5 =
* Added button to open media library dialog for link URL selection. Allows you to upload a media item and easily link to it

= 6.6.4 =
* Fixed issue with broken link checker only reporting first error

= 6.6.3 =
* Renamed ll_write_log function to linklibrary_write_log function to avoid potential conflicts with other plugins.

= 6.6.2 =
* Improved broken link checked to identify redirections to new URLs

= 6.6.1 =
* Added new option field for user submission form to specify tooltips to be displayed when user hovers over the fields
* Fixed issue with export all links function not working if no tags are assigned
* Fixed issue with some form fields not being re-displayed is captcha is wrong
* Fixed issue with form validation not working with description field is set to required

= 6.6 =
* Fixed editor issue in WP 5.5

== Frequently Asked Questions ==

= Where can I find documentation for Link Library? =

Visit the [official documentation for Link Library](https://github.com/ylefebvre/link-library/wiki)

= Who are the translators behind Link Library? =

* French Translation courtesy of Luc Capronnier
* Danish Translation courtesy of [GeorgWP](http://wordpress.blogos.dk)
* Italian Translation courtesy of Gianni Diurno
* Serbian Translation courtesy of [Ogi Djuraskovic, firstsiteguide.com](http://firstsiteguide.com)

= Where do I find my category IDs to place in the "Categories to be Displayed" and "Categories to be Excluded" fields? =

The category IDs are numeric IDs. You can find them by going to the page to see and edit link categories, then placing your mouse over a category and seeing its numeric ID in the link that is associated with that name.

= How can I display different categories on different pages? =

If you want all of your link pages to have the same layout, create a single setting set, then specify the category to be displayed when you add the short code to each page. For example: [link-library categorylistoverride="28"]
If the different pages have different styles for different categories, then you should create distinct setting sets for each page and set the categories to be displayed in the "Categories to be Displayed" field in the admin panel.

= After assigning a Link Acknowledgement URL, why do links no longer get added to my database? =

When using this option, the short code [link-library-addlinkcustommsg] should be placed on the destination page.

= How can I override some of the options when using shortcodes in my pages =

To override the settings specified inside of the plugin settings page, the two commands can be called with options. Here is the syntax to call these options:

[link-library-cats categorylistoverride="28"]

Overrides the list of categories to be displayed in the category list

[link-library-cats excludecategoryoverride="28"]

Overrides the list of categories to be excluded in the category list

[link-library categorylistoverride="28"]

Overrides the list of categories to be displayed in the link list

[link-library excludecategoryoverride="28"]

Overrides the list of categories to be excluded in the link list

[link-library notesoverride=0]

Set to 0 or 1 to display or not display link notes

[link-library descoverride=0]

Set to 0 or 1 to display or not display link descriptions

[link-library rssoverride=0]

Set to 0 or 1 to display or not display rss information

[link-library tableoverride=0]

Set to 0 or 1 to display links in an unordered list or a table.

= Can Link Library be used as before by calling PHP functions? =

For legacy users of Link Library (pre-1.0), it is still possible to call the back-end functions of the plugin from PHP code to display the contents of your library directly from a page template.

The main differences are that the function names have been changed to reflect the plugin name. However, the parameters are compatible with the previous function, with a few additions having been made. Also, it is important to note that the function does not output the Link Library content by themselves as they did. You now need to print the return value of these functions, which can be simply done with the echo command. Finally, it is possible to call these PHP functions with a single argument ('AdminSettings1', 'AdminSettings2', 'AdminSettings3', 'AdminSettings4' or 'AdminSettings5') so that the settings defined in the Admin section are used.

Here would be the installation procedure:

1. Download the plugin
1. Upload link-library.php to the /wp-content/plugins/ directory
1. Activate the plugin in the Wordpress Admin
1. Use the following functions in a [new template](http://codex.wordpress.org/Pages#Page_Templates) and select this template for your page that should display your Link Library.

`&lt;?php echo $my_link_library_plugin->LinkLibraryCategories('name', 1, 100, 3, 1, 0, '', '', '', false, '', ''); ?&gt;<br />
`&lt;br /&gt;<br />
&lt;?php echo $my_link_library_plugin->LinkLibrary('name', 1, 1, 1, 1, 0, 0, '', 0, 0, 1, 1, '&lt;td>', '&lt;/td&gt;', 1, '', '&lt;tr&gt;', '&lt;/tr&gt;', '&lt;td&gt;', '&lt;/td&gt;', 1, '&lt;td&gt;', '&lt;/td&gt;', 1, "Application", "Description", "Similar to", 1, '', '', '', false, 'linklistcatname', false, 0, null, null, null, false, false, false, false, '', ''); ?&gt;

== Screenshots ==

1. The Settings Panel used to configure the output of Link Library
2. A sample output page, displaying a list of categories and the links for all categories in a table form.
2. A second sample output showing a list of links with RSS feed icons and RSS preview link.
