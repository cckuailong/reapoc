=== Download Monitor ===
Contributors: wpchill, silkalns, barrykooij, mikejolley
Tags: download, downloads, monitor, hits, download monitor, tracking, admin, count, files, versions, logging, digital, documents, download category, download manager, download template, downloadmanager, file manager, file tree, grid, hits, ip-address, manager, media, monitor, password, protect downloads, tracker, sell, shop, ecommerce, paypal
Requires at least: 5.4
Tested up to: 5.8
Stable tag: 4.4.4
License: GPLv3
Text Domain: -
Requires PHP: 5.6

Download Monitor is a plugin for uploading and managing downloads, tracking downloads, displaying links and selling downloads!

== Description ==

Download Monitor provides an interface for uploading and managing downloadable files (including support for multiple versions), inserting download links into posts, logging downloads and selling downloads!

= Features =

* Add, edit and remove downloads from a familiar WP interface; Your downloads are just like posts.
* Sell your downloads from within your WordPress website!
* 100% Gutenberg compatible, including a new Download Monitor Download Block. Type /download to use it!
* Quick-add panel for adding downloads / files whilst editing posts.
* Add multiple file versions to your downloads each with their own data like download count and file links.
* Define alternative links (mirrors) per download version.
* Categorize, tag, or add other meta to your downloads.
* Display download links on the frontend using shortcodes.
* Change the way download links get displayed via template files.
* Track downloads counts and log user download attempts.
* Member only downloads, requires users to be logged in to download your files.
* Customisable endpoints for showing pretty download links.

[Read more about Download Monitor](https://www.download-monitor.com/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-after-features).

> #### Download Monitor Extensions
> Extend the core Download Monitor plugin with it's powerful extensions. All extensions come with one year of updates and support.<br />
>
> Some of our popular extensions include: [Page Addon](https://www.download-monitor.com/extensions/page-addon/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-block-page-addon), [Email Lock](https://www.download-monitor.com/extensions/email-lock/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-block-email-lock), [CSV Importer](https://www.download-monitor.com/extensions/csv-importer/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-block-csv-importer) and [Gravity Forms Lock](https://www.download-monitor.com/extensions/gravity-forms/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-block-gravity-forms-lock).
>
> Want to see more? [Browse All Extensions](https://www.download-monitor.com/extensions/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-block-browse-all)

= Documentation =
We have a large Knowledge Base on our [Download Monitor website](https://www.download-monitor.com/kb/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation) that contains documentation about how to how to setup and use Download Monitor.

Are you a new Download Monitor user? Read these articles on how to get your files ready for download with Download Monitor:

1. [How to install Download Monitor](https://www.download-monitor.com/kb/installation/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation)
2. [How to add your first download in Download Monitor](https://www.download-monitor.com/kb/adding-downloads/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation)
3. [How to list your first download on your website with the download shortcode](https://www.download-monitor.com/kb/shortcode-download/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation)

More advanced topics that a lot of people find interesting:

1. [Learn more about the different ways you can style your download buttons](https://www.download-monitor.com/kb/content-templates/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation)
2. [Learn more about how to customize your download buttons](https://www.download-monitor.com/kb/overriding-content-templates/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation)
3. [Learn more about what actions and filters are available in Download Monitor](https://www.download-monitor.com/kb/action-and-filter-reference/?utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=description-documentation)

= Contributing and reporting bugs =

You can contribute code to this plugin via GitHub: [https://github.com/download-monitor/download-monitor](https://github.com/download-monitor/download-monitor)

You can contribute localizations via Transifex [https://www.transifex.com/projects/p/download-monitor/](https://www.transifex.com/projects/p/download-monitor/)

= Support =

Use the WordPress.org forums for community support. If you spot a bug, you can of course log it on [Github](https://github.com/download-monitor/download-monitor) instead where we can act upon it more efficiently.

Unfortunately we can't offer you help with a customisation. Please consider hiring a developer for your website's customizations.

== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't even need to leave your web browser. To do an automatic install, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Download Monitor" and click Search Plugins. Once you've found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by clicking _Install Now_.

= Manual installation =

The manual installation method involves downloading the plugin and uploading it to your webserver via your favourite FTP application.

* Download the plugin file to your computer and unzip it
* Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's `wp-content/plugins/` directory.
* Activate the plugin from the Plugins menu within the WordPress admin.

== Frequently Asked Questions ==

= Can I upload .xxx filetype using the uploader? =

Download Monitor uses the WordPress uploader for uploading files. By default these formats are supported:

* Images - .jpg, .jpeg, .png, .gif
* Documents - .pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx
* Music - .mp3, .m4a, .ogg, .wav
* Video - .mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2

To add more you can use a plugin, or filters. This post is a good resource for doing it with filters: [Change WordPress Upload Mime Types](http://www.paulund.co.uk/change-wordpress-upload-mime-types).

= Can I link to external downloads? =

Yes, you can use both local paths and external URLs.

= My Download links 404 =

Download links are powered by endpoints. If you find them 404'ing, go to Settings > Permalinks and save. This will flush the permalinks and allow our endpoints to be added.

= Download counts are not increasing when I download something =

Admin hits are not counted, log out and try!

More documentation can be found in our [Knowledge Base](https://www.download-monitor.com/kb/).

== Screenshots ==

1. Easily add downloads to your website with our Gutenberg block!
2. The main admin screen lists your downloads using familiar WordPress UI.
3. Easily add file information and multiple versions.
4. The quick add panel can be opened via a link about the post editor. This lets you quickly add a file and insert it into a post.

== Changelog ==

= 4.4.4: October 19, 2020 =
* Tweak: Fixed PHP 7.4 notices.
* Tweak: Check if download variable is set in content-download template. This prevents a fatal error in odd case no download is given.
* Tweak: Now passing variables to actions to version.php admin view file.
* Tweak: Bump websocket-extensions from 0.1.3 to 0.1.4

= 4.4.3: June 8, 2020 =
* Tweak: Small minor fixes and tweaks.

= 4.4.2: April 3, 2019 =
* Tweak: Fixed an relative inclusion bug that sometimes caused bootstrap not to properly load. (Undefined function dlm_is_shop_enabled()).

= 4.4.1: March 27, 2019 =
* Tweak: Added proper html escaping to order overview page, props Nam.Dinh.

= 4.4.0: March 8, 2019 =
* Feature: Added a new product post type to create a clear separation between downloads and the products you can sell. Read more about this here: https://www.download-monitor.com/kb/products/
* Feature: Shop products now have a basic detail (single) page. This will be improved in future updates.
* Feature: Added "Paid Only" option to downloads. This allows you to require users to have a purchase linked to the download before file can be downloaded.
* Feature: Versions are now passed through the no-access page.
* Tweak: dlm_buy shortcode now takes a product id, instead of a download ID. These need to be updated manually.
* Tweak: Shop feature has been more isolated from non-shop. To enable shop, go to general Download Monitor settings and check "Enable Shop".
* Tweak: PayPal gateway is now enabled by default.

= 4.3.0: February 27, 2019 =
* Feature: Added Shop (Beta) feature. You can now sell your downloads via Download Monitor! More addition to shop (like taxes and discounts) coming soon.
* Feature: Added PayPal integration for shop feature.
* Feature: Added an onboarding screen, helping the user set up the plugin.
* Tweak: Fixed an error when changing dates in reports on Safari.
* Tweak: Added download ID to Reports overview table.
* Tweak: Increased spacing of download title on reports summary block.
* Tweak: We're no longer automatically creating the No Access page. This is now done via the onboarding screen.
* Tweak: We're no longer supporting PHP 5.2. If you upgrade and are still running PHP 5.2, the plugin will not load but display an upgrade notice instead. More info: https://www.download-monitor.com/kb/minimum-required-php-version/
* Tweak: Updated translations.

= 4.2.1: January 31, 2019 =
* Tweak: Correctly set default template of Gutenberg block on frontend of website when no specific template is set.

= 4.2.0: January 24, 2019 =
* Feature: Added Gutenberg download block. Type /download in your post screen to see it in action!

= 4.1.1: September 12, 2018 =
* Tweak: Fixed a bug that incorrectly included featured downloads by default in [downloads].
* Tweak: Added 'dlm_get_template_part_args' filter that allows argument filtering on Download Monitor templates.

= 4.1.0: May 21, 2018 =
* Feature: Added a new option that allows site-owners if and how they wish to track IP addresses of users.
* Feature: Added a new option that allows site-owners to decide if they wish to track user agent of users.
* Tweak: Fixed an issue where title of log dates had incorrect date.
* Tweak: Added compatibility for 'Post Types Order' plugin. The dashboard widget no longer is affected by their custom order.
* Tweak: Added dlm_frontend_scripts filter, allows user to not include DLM frontend assets.
* Tweak: No longer load jQuery UI CSS from Google CDN, file is now included in plugin.
* Tweak: No longer loading jQuery images from Google CDN, images are now included in plugin.

= 4.0.8: May 3, 2018 =
* Tweak: Fixed a bug in the legacy upgrader that caused versions with empty dates not to added.
* Tweak: Fixed the use of getTimeStamp() because PHP 5.2 doesn't support this.

= 4.0.7: April 13, 2018 =
* Tweak: Fixed a bug that caused certain months in the reports filter to crash for non English languages.
* Tweak: Fixed a bug that caused the 10 download limit on the Dashboard Widget to be ignored.
* Tweak: Added "dlm_remove_dashboard_popular_downloads" filter that allows for not loading of dashboard widget.
* Tweak: Moved 'dlm_log_item' filter to within the WordPressLogItemRepository::persist() method. This way the filter will always be called upon a log persist.
* Tweak: Filter 'dlm_log_item' 2nd and 3rd argument changed from DLM_Download & DLM_Download_Version type to int (ID's of both download and version).
* Tweak: Moved 'dlm_downloading_log_item_added' filter to within the WordPressLogItemRepository::persist() method. This way the filter will always be called upon a log persist.
* Tweak: Filter 'dlm_downloading_log_item_added' 2nd and 3rd argument changed from DLM_Download & DLM_Download_Version type to int (ID's of both download and version).
* Tweak: Added filter 'dlm_reports_page_start' to add content on top of admin reports page.
* Tweak: Added filter 'dlm_reports_page_end' to add content on bottom of admin reports page.
* Tweak: Added download id and download object to 'dlm_placeholder_image_src' filter, props [James Golovich](https://github.com/jamesgol).
* Tweak: Added filter 'dlm_download_get_versions' on return of DLM_Download::get_versions(), props [James Golovich](https://github.com/jamesgol).
* Tweak: Added $atts to various shortcode filters, props [James Golovich](https://github.com/jamesgol).

= 4.0.6: March 8, 2018 =
* Tweak: Fixed a bug in the version-list template, correct version links are now displayed.

= 4.0.5: February 21, 2018 =
* Tweak: Fixed a bug that caused the "Add file" button to not appear on the Add New Download screen.
* Tweak: Fixed a bug that caused the crc32b hash not to be saved when adding a download via the 'Quick-add Download' option.
* Tweak: WordPressVersionRepository no longer explicitly sets 'post_content' and 'post_excerpt' database fields of dlm_download_version to empty strings. Props [Erin Morelli](https://github.com/ErinMorelli).

= 4.0.4: February 19, 2018 =
* Tweak: Fixed a bug where versions of draft and pending review downloads were not displayed in the backend.

= 4.0.3: February 9, 2018 =
* Tweak: We now cache if we need to upgrade legacy downloads. This prevents us from checking if we need to upgrade on every pageload, improving performance and preventing constant sql warning when legacy table doesn't exist.
* Tweak: Fixed SQL error in has_ip_downloaded_version() call ('type' does not exist).

= 4.0.2: February 2, 2018 =
* Tweak: Moved no cache headers up in download process, improving cache prevention.
* Tweak: Added new log item meta data methods, making it easier to add meta data.
* Tweak: Added new action 'dlm_downloading_log_item_added'. Is triggered after log item is added on download request.
* Tweak: Added $download and $version arguments to 'dlm_log_item' filter.

= 4.0.1: January 25, 2018 =
* Tweak: Fixed an issue that caused widget limit to not work.
* Tweak: Fixed an count() warning in PHP7.2 on extension page.
* Tweak: Fixed a passed by reference notice in lower PHP versions in get_visitor_ip() call.
* Tweak: Added 'dlm_download_count' filter to fitler get_download_count() return value.
* Tweak: Downloads now return an empty version if no version is added. This prevents mulitple possible fatal errors when external scripts don't check if get_version() returns null.
* Tweak: Added an extra check to if website needs upgrading. If there are new downloads in the database, no upgrade is recommended.
* Tweak: Added an extra warning on the upgrade page to users that navigate to the page while we think no upgrade is needed.
* Tweak: Requesting a non existing download no longer triggers a fatal error (exception is now properly handled).
* Tweak: Correct exception handling when trying to save meta boxes of non existing download.

= 4.0.0: January 22, 2018 =
* Feature: Added reports page for download statistics.
* Feature: Added hash values to version blocks.
* Feature: Added option to include downloads in WordPress default search results, props [Kurt Zenisek](https://github.com/KZeni).
* Feature: Added SHA256 hash.
* Feature: Added support for regex patterns in user agent blacklist, props [Matt Mower](https://github.com/mdmower).
* Feature: Added user filter to logs, props [neptuneweb](https://github.com/neptuneweb).
* Feature: Log columns in log table are now sortable.
* Feature: Added an option for the user to remove all transients.
* Feature: Added the ability to exclude tags from the [downloads] shortcode.
* Feature: Added search option in "Insert Download" overlay.
* Feature: Added builtin legacy upgrade tool to help users move their downloads from legacy to current version.
* Tweak: Added 'dlm_file_path' filter to filter file_path in download request.
* Tweak: We're now only serving downloads when requested over the GET or POST HTTP method. This can be filtered via filter dlm_accepted_request_methods.
* Tweak: Added Download Title to download log CSV export file.
* Tweak: Added 'dlm_shortcode_total_downloads' filter to output of [total_downloads] shortcode, props [Joel James](https://github.com/Joel-James).
* Tweak: Added support for Apache 2.4 and up in generated .htaccess file.
* Tweak: Added 'dlm_download_use_version_transient' filter to allow website to not use cache transients.
* Tweak: Downloads need to be published in order to exist, draft downloads can no longer be downloaded.
* Tweak: Fixed plugin links on plugin overview page.
* Tweak: Optimization rewrite of DLM_Download class.
* Tweak: Optimization rewrite of DLM_Download_Version class.
* Tweak: Introduction of Factory and Repository design patterns for Downloads and Versions.
* Tweak: Complete rewrite of setting fields.
* Tweak: Updated template files to use new download methods.
* Tweak: Introduced new filter "dlm_setting_field_TYPE", allowing third party programs to add custom field types to settings.
* Tweak: Implemented Composer autoloader.
* Tweak: Download Categories and Download Tags label name now contains 'Download'.
* Tweak: Download Widget now uses default template output set in settings when no output template is set.
* Tweak: Download Widget CSS tweaks.
* Tweak: Download category and download tags are now excluded from Yoast SEO sitemap.
* Tweak: 'Delete Logs' button now requires a confirmation.
* Tweak: Placeholders for download thumbnails in media library list view are now also properly replaced.
* Tweak: Minor UX improvement on Download Information fields making it easier to copy these, props [Danny van Kooten](https://github.com/dannyvankooten).
* Tweak: Replaced double underscore prefixed functions with single underscore prefix to comply with PHP reserved method naming rules.

= 1.9.9: October 18, 2017 =
* Tweak: Fixed an issue with 'No Access' page not saving correctly.

= 1.9.8: October 6, 2017 =
* Tweak: Add option to allow HTTP header X_FORWARD_FOR. Allowing Download Monitor to use the X_FORWARDED_FOR HTTP header set by proxies as the IP address.
* Tweak: Download files that are added via 'Quick-add download' are now properly added to WP media library.
* Tweak: Introduced 'lazy select' option for Download Monitor settings. Options of these select elements are only loaded on setting pages, increasing overall plugin performance.
* Tweak: We're now filtering attachment thumbnails in media library for files in dlm_uploads. This solves 403 errors on thumbnails in the protected folder.

= 1.9.7: May 5, 2017 =
* Tweak: Added capability checks to log export and delete functionality. Props [Pritect](http://www.pritect.net/).
* Tweak: We're now redirecting users to home on empty download request. Behavior can be changed via filters. See https://www.download-monitor.com/kb/empty-download-request-redirection/

= 1.9.6: February 28, 2017 =
* Tweak: Fix display for unknown user in exported log, props [Matt Mower](https://github.com/mdmower).
* Tweak: Settings screen hash tweaks.
* Tweak: Display correct tab on settings save, props [Matt Mower](https://github.com/mdmower).
* Tweak: Fixed issue with some dismissible notices.
* Tweak: Add Portuguese (pt_PT) translation, props [Pedro Mendonça](https://github.com/pedro-mendonca).
* Tweak: Included various language tweaks via Transifex. Help out over at [Transifex](https://www.transifex.com/barrykooijplugins/download-monitor/).

= 1.9.5: August 23, 2016 =
* Tweak: Fixed a bug where Download Options couldn't be checked off in quick edit.
* Tweak: Updated settings screen description for custom templates.
* Tweak: Download Information input fields are now readonly since these fields are informational only. Props [kraftner](https://github.com/kraftner).
* Tweak: Removed code that triggered PHP7 incompatibility false positives in PHP7 compatibility scans.
* Tweak: Removed old JSON library since default JSON functions are available from PHP 5.2 and up.

= 1.9.4: May 2, 2016 =
* Tweak: Various cookie tweaks to prevent incorrect double logging entries.
* Tweak: Added a Cookie Manager class to centralize cookie related tasks.

= 1.9.3: April 11, 2016 =
* Tweak: Small rework of [downloads] loop. Downloads now filterable per download via dlm_shortcode_downloads_loop_download.
* Tweak: We now report missing versions for removed downloads in logs, props [Matt Mower](https://github.com/mdmower).
* Tweak: Updated Danish translation, props [Georg Adamsen](https://github.com/GSAdev).

= 1.9.2: March 27, 2016 =
* Tweak: Fixed bug where 'version' and 'version_id' were ignored in [download].
* Tweak: Fixed a bug that caused the file upload overlay to append file URL to wrong version, props [kraftner](https://github.com/kraftner).
* Tweak: Optimized [download] shortcode code.
* Tweak: Flush rewrites in admin settings on shutdown instead of during page load, props [sybrew](https://github.com/sybrew).
* Tweak: Added extra checks to blacklist checks to prevent stristr empty needle notices, props [Matt Mower](https://github.com/mdmower).
* Tweak: Removed DS_Store backups, props [Matt Mower](https://github.com/mdmower).
* Tweak: Added Ukrainian translation, props [Fremler](https://www.transifex.com/user/profile/Fremler/).
* Tweak: Added Croatian translation, props [molekula](https://www.transifex.com/user/profile/molekula/).
* Tweak: Updated Dutch translation.
* Tweak: Updated Portuguese (Brazil) translation.

= 1.9.1: December 1, 2015 =
* Tweak: Check if $visitor_ua isn't empty to prevent stristr warnings.
* Tweak: Added wmv filetype icon support
* Tweak: Correctly populate data on quick edit of download.
* Tweak: Settings tab now loads tab of set URL hash.

= 1.9.0: September 15, 2015 =
* Feature: We added a separate 'No Access' page that includes the following features:
* No Access Page : Added [dlm_no_access] shortcode that displays the no access content.
* No Access Page : Added new option in Access settings tab to set No Access Page.
* No Access Page : Added new template file for no access page.
* No Access Page : We now redirect to set No Access page (if set) when user has no access to download.
* Feature: Added ability to remove log entries.
* Feature: Added browser detection for IE 11 and up.
* Feature: Added OS detection for Windows 8.1
* Feature: Added OS detection for Windows 10
* Feature: Added Featured download, Members only and Redirect to file to bulk edit options.
* Feature: Added Featured download, Members only and Redirect to file to quick edit options.

= 1.8.1: August 21, 2015 =
* Tweak: Small tweak to make download count fit better in box template.
* Tweak: Fixed a zero file size bug.
* Tweak: Fixed featured image disappears bug, props [Ricardo](https://wordpress.org/support/profile/ricardopires).
* Tweak: Search template file in custom path before in plugin path.

= 1.8.0: July 10, 2015 =
* Feature: Added option to only count downloads and add logs from unique ip addresses, props [Matt Mower](https://github.com/mdmower).
* Feature: It's now possible to display downloads with the downloads shortcode that are in all given categories (AND instead of OR) by using + (plus_ instead of , (comma).
* Feature: Display nginx rules if server is running nginx.
* Feature: Added Multisite / Network compatibility.
* Feature: Redone blacklist IP feature, now available in the 'Access' tab.
* Feature: Redone blacklist user agent feature, now available in the 'Access' tab.
* Tweak: Added icon support for Office X Excel & PPT extensions.
* Tweak: Run thumbnail compatibility method later to allowed themes to register first.
* Tweak: Prefixed admin CSS classes to prevent plugin conflicts.
* Tweak: Fix checking shortcodes for empty version strings, props [Matt Mower](https://github.com/mdmower).
* Tweak: Fixed a call of trigger() in DLM_Download_Handler, props [Matt Mower](https://github.com/mdmower).
* Tweak: Remove trailing space from downloads count, props [Matt Mower](https://github.com/mdmower).
* Tweak: Directory browser items are now always in alphabetical order, props [Matt Mower](https://github.com/mdmower).
* Tweak: The 60 seconds download counter increment cool down is now set to version ID instead of download ID, props [Matt Mower](https://github.com/mdmower).
* Tweak: Created and implemented local independent basename fixing issues with Cyrillic alphabets.
* Tweak: Made user agents regexes filterable: dlm_ua_parser_regexes.
* Tweak: Optimized log status icons, also fixes WP emoji conflict.
* Tweak: Members Only check now only does check if requester can still download.
* Tweak: Replaced PHP4 constructors in widget.
* Tweak: Added Danish translation.
* Tweak: Updated Dutch translation.
* Tweak: Updated German translation.

= 1.7.2: April 29, 2015 =
* Tweak: Fixed a bug that caused logs not to be displayed in WP 4.2.

= 1.7.1: April 17, 2015 =
* Tweak: Pass third arg to add_query_arg to prevent XSS.

= 1.7.0: March 22, 2015 =
* Feature: Added 'Download Information' meta box to edit download screen that displays useful download information.
* Feature: Error message shown when visitor has no access to download is now an option.
* Tweak: Fixing a bug where versions with spaces did not work, versions now are checked on a sanitized title.
* Tweak: Viewing logs now needs custom capability: dlm_manage_logs (automatically added to administrators).
* Tweak: Improved hotlink prevention check.
* Tweak: Extension page tweaks.
* Tweak: Added $download_id argument to dlm_hotlink_redirect filter.
* Tweak: Moved hash settings to their own tab.
* Tweak: Moved 'X-Accel-Redirect / X-Sendfile' and 'Prevent hotlinking' settings to General tab.
* Tweak: Optimized the Insert Download button.
* Tweak: Introduced a multi-byte-safe pathinfo so we can handle 'special' filenames.
* Tweak: Also set the post_date_gmt value for version dates.
* Tweak: Updated French translation. Props Li-An.
* Tweak: Updated German translation. Props maphy-psd.
* Tweak: Updated Swedish translation. Props EyesX.
* Tweak: Update Slovakian translation. Props attitude.
* Tweak: Added Dutch translation.

= 1.6.4: March 8, 2015 =
* Removed unused library jqueryFileTree.
* dlm_shortcode_download_content filter now also includes $atts.
* Fixed small parse file parse error because of whitespace.
* Changed some admin menu hook priorities.

= 1.6.3: January 18, 2015 =
* Fixed an undefined method call 'get_filesize'.
* Allow third party extensions to hijack [downloads] shortcode with filter dlm_shortcode_download_content.
* Made 'wp_dlm_downloading' cookie only accessible through the HTTP protocol, props [Matt Mower](https://github.com/mdmower).

= 1.6.2: January 11, 2015 =
* Fixed a bug that caused translations not to load.
* Fixed a bug that prevented download versions from being removed.
* Fixed a pagination in 'insert download' shortcode bug.
* Fixed a bug in the template loader when used with a custom directory, a slug and no custom template.
* Removed assigning by reference, fixed strict notice when deleting downloads.
* Tweaked template loader to accept arguments.
* Allow downloads shortcode WP_Query arguments to be filtered with 'dlm_shortcode_downloads_args'.

= 1.6.1: January 9, 2015 =
* Fixed an extension activation error.
* Fixed a bug that caused the featured image to disappear in some themes.
* Tweak: In multisite only users that are a member of the blog can download 'member only' downloads.

= 1.6.0: January 8, 2015 =
* Plugin is now initiated at plugins_loaded.
* Implemented auto loader.
* Classes are no longer initiated at bottom of class file but whenever an object is needed.
* Code standards corrections.
* Introduced Template_Handler. Loading of template parts should be done through this class.
* Removed $GLOBALS['dlm_logging'] global.
* Removed $GLOBALS['DLM_Download_Handler'] global.
* Removed internal use of $download_monitor global.
* Moved all inline JavaScript to separate JavaScript files.
* Moved all install related code to installer class.
* Moved main plugin class to it's own file.
* Deprecated 'dlm_create_log' function.
* Redone extensions page.
* Fixed a bug in shortcode download where orderby=download_count wasn't working.
* Fixed a bug where downloads didn't work with default WP permalink structure.
* Delete dlm_file_version_ids_ transient on save.
* Added dlm_download_headers filter.
* Added dlm_get_template_part filter.

= 1.5.1 =
* Fallback for JSON_UNESCAPED_UNICODE to fix accented characters on < PHP 5.4.
* Changed default orderby for downloads shortcode to date, desc.

= 1.5.0 =
* JSON_UNESCAPED_UNICODE for files to fix unicode chars when json encoded. Fix needs PHP 5.4+ to work, but won't break lower versions.
* Style filetype-docx
* Update get_version_id to work with non-numeric versions.
* Fix shortcode arg booleans.
* Add transient cache for get_file_version_ids.
* Moved all translations to Transifex - https://www.transifex.com/projects/p/download-monitor/
* Changed text domain from download_monitor to download-monitor.
* Added Grunt.
* Added options to generate file hashes DISABLED BY DEFAULT as they can cause performance issues with large files.

= 1.4.4 =
* Use home_dir instead of site_dir - fixes hot-linking protections against own site (when not in root dir)
* Replace hardcoded WP_CONTENT_DIR and WP_CONTENT_URL with wp_upload_dir to work when UPLOADS and UPLOADS_URL constants are set.
* Added some filters for hotlink protection customisation.

= 1.4.3 =
* Add password form to download page when required
* Run shortcodes in excerpt/short desc
* Various hook additions
* pr_br and zh_cn translation
* Sort download count by meta_value_num
* Store URLs in JSON format to allow easier search/replace
* Fix dashboard sorting
* Option for basic referer checking to prevent hotlinking.
* Only get file hashes on save as they are resource heavy.
* Disable remote file hash generation, but can be enabled with filter dlm_allow_remote_hash_file
* Radio buttons instead of select (with pagination) in popup to improve performance.

= 1.4.2 =
* Fix for site_url -> abspath
* Check if hash functions are supported before use.

= 1.4.1 =
* Fix file_exists error in download handlers

= 1.4.0 =
* MP6/3.8 admin styling. Requires 3.8.
* Polish translation.
* Turkish translation.
* Change capability required to view dashboard widget.
* Don't show "insert download" when editing a download.
* Allow pagination for the [downloads] shortcode. Simply add paginate=true to the shortcode.
* Reverted flush change in download handler to reduce memory usage on some hosting envrionments
* changed download handlers and fixed corruption when resuming files
* Calculate md5/sha1/crc32 hashes for files. Obtainable via methods or download_data, e.g. [download_data id="86" data="md5"]
* Added file_date data

= 1.3.2 =
* Cleaned up log table queries
* Tweaked download handler headers
* Tweaked logging
* Limit UA to 200
* Setcookie to prevent double logging
* Addons page (disable using add_filter( 'dlm_show_addons_page', '__return_false' ); )

= 1.3.1 =
* Added some new hooks
* FR and SR_RS updates

= 1.3.0 =
* Fix 0kb downloads in some hosting enviroments
* Added button to delete logs
* Fixed log page when no logs are present
* FR and HU updates
* Added dropdown for the default template option to make available templates more obvious
* Added version-list and title templates

= 1.2.0 =
* Option to redirect to files only (do not force)
* Fixed textdomains
* HU translation by Győző Farkas
* Fix dlm_upload folder when not using month/day upload folders.
* Fix IP lookup
* Resumable download support
* Tweaked download handler

= 1.1.2 =
* HTTPS headers for IE fix
* Italian locale

= 1.1.1 =
* Specify error statuses on wp_die messages e.g. 404 for missing files.
* Moved DONOTCACHEPAGE

= 1.1.0 =
* Fixed admin notices
* Added download link to admin 'file' column for copying and pasting
* Farsi localisation
* Wrapping content in a [download] shortcode will wrap it in a simple link.

= 1.0.6 =
* Hide taxonomies from nav menus
* Fix categories in download_data method.

= 1.0.5 =
* When do_not_force is enabled, still replace abspath with home_url
* Exclude dlm_download from search and disable query var
* Added category_include_children option for downloads shortcode
* Fixed logs time offset.

= 1.0.4 =
* Tweak admin page detection to work when no downloads exist.
* Fix dashboard widget warning.
* Add filters to logs and export csv function.
* Added extra columns to CSV.

= 1.0.3 =
* Fix config page to work with multibyte tab names.
* Japanese locale by hide92795
* Admin CSS/script conditonally loaded
* Versions are now strtolower to be compatible with version_compare and to standardise numbers.

= 1.0.2 =
* Only use wp_remote_head to get fielsize on remote files. Prevents timeouts when a file doesn't exist.
* If a filesize cannot be found, set to -1 to prevent re-tries.
* Insert button added to all CPT except downloads.
* French locale by Jean-Michel MEYER.

= 1.0.1 =
* Update blockui
* Workaround root relative URLS

= 1.0.0 =
* Complete rewrite of the plugin making use of custom post types and other best practices. Fresh start version '1' to prevent auto-updates (legacy importer needs to be used to migrate from old versions).

== Upgrade Notice ==
