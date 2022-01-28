=== WP Statistics ===
Contributors: mostafa.s1990, mehrshaddarzi, kashani, veronalabs, GregRoss, dedidata
Donate link: https://wp-statistics.com/donate/
Tags: analytics, wordpress analytics, stats, statistics, visit, visitors, hits, chart, browser, today, yesterday, week, month, year, total, post, page, sidebar, google, live visit, search word, agent, google analytics, webmasters, google webmasters, geoip, location
Requires at least: 3.0
Tested up to: 5.7
Stable tag: 13.0.7
Requires PHP: 5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin gives you the complete information on your website's visitors.

== Description ==
= WP statistics: THE #1 WORDPRESS STATISTICS PLUGIN =
Do you need a simple tool to know your website statistics? Do you need to represent these statistics? Are you caring about your users’ privacy while analyzing who are interested in your business or website? With WP Statistics you can know your website statistics without any need to send your users’ data anywhere. You can know how many people visit your personal or business website, where they’re coming from, what browsers and search engines they use, and which of your contents, categories, tags and users get more visits.

All these data are recorded in your server, and YES! WP Statistics is [GDPR compliant](http://bit.ly/2x0AFgT).

= ACT BETTER  BY KNOWING WHAT YOUR USERS ARE LOOKING FOR =
* Visitor Data Records including IP, Referring Site, Browser, Search Engine, OS, Country and City
* Stunning Graphs and Visual Statistics
* Visitor’s Country Recognition
* Visitor’s City Recognition
* The number of Visitors coming from each Search Engine
* The number of Referrals from each Referring Site
* Top 10 common browsers; Top 10 countries with most visitors; Top 10 most-visited pages; Top 10 referring sites
* Hits Time-Based Filtering
* Statistics on Contents based on Categories, Tags, and Writers
* Widget Support for showing Statistics
* Data Export in TSV, XML, and CSV formats
* Statistical Reporting Emails
* [Premium] [Real-time stats](http://bit.ly/2Mj4Nss)
* [Premium] [More Advanced reporting](http://bit.ly/2MjZE3l)
* And much more information represented in graphs & charts along with data filtering

= NOTE =
Some advanced features are Premium, which means you need to buy extra add-ons to unlock those features. You can get [Premium add-ons](http://bit.ly/2x6tGly) here!

= REPORT BUGS =
If you encounter any bug, please create an issue on [Github](https://github.com/wp-statistics/wp-statistics/issues/new) where we can act upon them more efficiently. Since [Github](https://github.com/wp-statistics/wp-statistics) is not a support forum, just bugs are welcomed, and any other request will be closed.

== Installation ==
1. Upload `wp-statistics` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Make sure the Date and Time are set correctly in WordPress.
4. Go to the plugin settings page and configure as required (note this will also include downloading the GeoIP database for the first time).

== Frequently Asked Questions ==
= GDPR Compliant? =
The greatest advantage of WP Statistics is that all the data is saved locally in WordPress.
This helps a lot while implementing the new GDPR restrictions; because it’s not necessary to create a data processing contract with an external company! [Read more about WP Statistics compliance with GDPR](http://bit.ly/2x0AFgT).

= Does WP Statistics support Multisite? =
WP Statistics doesn’t officially support the multisite feature; however, it does have limited functionally associated with it and should function without any issue. However, no support is provided at this time.
Version 8.8 is the first release that can be installed, upgraded and removed correctly on multi-site. It also has some basic support for the network admin menu. This should not be taken as an indication that WP Statistics fully supports the multisite, but only should be considered as a very first step.

= Does WP Statistics work with caching plugins? =
Yes, the cache support added in v12.5.1

If you're using a plugin cache:
* Don't forget to clear your enabled plugin cache.
* You should enable the plugin cache option in the Settings page.
* Making sure the below endpoint registered in your WordPress.
http://yourwebsite.com/wp-json/wpstatistics/v1

To register, go to the Permalink page and update the permalink with press Save Changes.

= What’s the difference between Visits and Visitors? =
Visits is the number of page hits your site has received.
Visitors is the number of unique users which have visited your site.
Visits should always be greater than Visitors (though, there are a few cases when this won’t be true due to having low visits).
The average number of pages a visitor views on your site is Visits/Visitors.

= Are All visitors’ locations set to ‘unknown’? =
Make sure you’ve downloaded the GeoIP database and the GeoIP code is enabled.
Also, if you are running an internal test site with non-routable IP addresses (like 192.168.x.x or 172.28.x.x or 10.x.x.x), these addresses will be always shown as ‘unknown’. You can define a location IP for these IP addresses in the “Country code for private IP addresses” setting.

= I’m using another statistics plugin/service and get different numbers from them, why? =
Probably, each plugin/service is going to give you different statistics on visits and visitors; there are several reasons for this:

* Web crawler detections
* Detection methods (Javascript vs. Server Side PHP)
* Centralized exclusions

Services that use centralized databases for spam and robot detections , such as Google Analytics, have better detection than WP Statistics.

= Not all referrals are showing up in the search words list, why? =
Search Engine Referrals and Words are highly dependent on the search engines providing the information to us. Unfortunately, we can’t do anything about it; we report everything we receive.

= PHP 7 Support? =
WP Statistics is PHP 7 compliant; however, some versions of PHP 7 have bugs that can cause issues. One known issue is that PHP 7.0.4 cause memory exhaustion errors. Newer versions of PHP 7 do not have this issue.
At this time (September, 2018) WP Statistics seems to run fine with PHP 7.2.6. But  you may experience issues that we haven’t found yet. If you do, feel free to report it after you make sure it is not a problem with PHP.

= IPv6 Support? =
WP Statistics supports IPv6 as of version 11.0; however, PHP must be compiled with IPv6 support enabled; otherwise you may see warnings when a visitor from an IPv6 address hits your site.

You can check if IPv6 support is enabled in PHP by visiting the Optimization > Resources/Information->Version Info > PHP IPv6 Enabled section.

If IPv6 is not enabled, you may see an warning like:

	Warning: inet_pton() [function.inet-pton]: Unrecognized address 2003:0006:1507:5d71:6114:d8bd:80c2:1090

== Screenshots ==
1. Overview
2. Browsers Statistics
3. Top Countries
4. Hit Statistics
5. Top pages
6. Category Statistics
7. Search Engine Referral Statistics
8. Last Search Words
9. Dashboard widgets
10. Theme widget

== Upgrade Notice ==

= 13.0 =
**IMPORTANT NOTE**
Welcome to WP-Statistics v13.0, our biggest update!
Thank you for being part of our community. We’ve been working hard for one year to develop this version and make WP-Statistics better for you.
Before updating, make sure you disabled all your add-ons, then after that, try to update add-ons.

If you encounter any bug, please create an issue on [Github](https://github.com/wp-statistics/wp-statistics/issues/new) where we can act upon them more efficiently. Since [Github](https://github.com/wp-statistics/wp-statistics) is not a support forum, just bugs are welcomed, and any other request will be closed.

== Changelog ==
= 13.0.7 =
- Compatibility with WordPress v5.7
- Fixes linking hits page from post meta box
- Support new hooks for email reporting and fix email logging
- Compatibility with Advanced Reporting and fixes tweak issues

= 13.0.6 =
- Improvement the time-out functionality while downloading the GeoIP city database.
- Fixed conflict with custom post-type column.
- Fixed error to passing the wrong argument for implode in WhichBrowser.
- Fixed date range selector in Top Pages.
- Fixed purge cache data after deleting the table.
- Fixed some issues & improvement historical functionality.
- Minor Improvements.

= 13.0.5 =
- Compatibility the ChartJs with some kind of plugins.
- Compatibility with WordPress v5.6
- Improvement error handling with REST API
- Added an option in the Optimization page to optimize & repair the tables.
- Added ability to filter `wp_statistics_get_top_pages()` by post type [#343](https://github.com/wp-statistics/wp-statistics/pull/343)
- Fixed the issue to load Purge class.
- Minor Improvements in SQL queries.

= 13.0.4 =
- Compatibility with PHP v7.2 and adjustment requires PHP version in the Composer to 5.6
- Fixed the issue to get the `Referred::get()` method during the initial plugin.
- Fixed issue to create tables queries in MariaDB v10.3
- Fixed the ChartJs conflict with some plugins.
- Disabled the Cronjob for table optimization in the background process (we're going to create an option on the Optimization page to handle it)
- Minor Improvements.

= 13.0.3 =

**We're very sorry regarding the previous update because we had a lot of changes on v13.0, we worked almost 1 year for this update and considered all situations and many tests, anyway try to update and enjoy the new features!**

- Fixed critical issue when some PHP modules such as bcmath are not enabled. it caused a fatal error, the purpose flag `platform-check` from Composer has been disabled.
- Fixed the "Connect to WordPress RestAPI" message while loading the admin statistics' widgets, the uBlock browser extension was blocking the WP-Statistics's requests.
- Fixed the upgrade process issue, one of the previous action was calling and that caused the issue, that's now disabled.
- Disabled some repair and optimization table queries during the initial request.
- Minor Improvements.

= 13.0.2 =

**New Feature**

- Added error logs system
- Added the ability to change visitors’ data based on WordPress hook
- Added the ability to manage the plugin based on WP-CLI
- Added a link to show user’s location’s coordinates on Google Map based on their IP
- Added advanced filters in the page of WordPress website visitors list
- Added the class of sending standard email reports in the WordPress
- Added the ability to get WordPress users in the database record

**Bug Fix**

- Fixed recording visitors data problem when the cache plugin is installed
- Fixed exclusion problem in Ajax requests mode
- Fixed REST-API requests problem in JavaScript mode without jQuery library
- Fixed the issue of limiting the number of database table records
- Fixed the problem of getting WordPress page type in taxonomy mode
- Fixed display of visitor history for yesterday and today

**Improvement**

- Improved widget information based on REST-API
- Optimized and troubleshot database tables after an interval of one day
- Improved plugin information deleting operation
- Improved receiving country and city visitors information based on WordPress cache IP
- Improved display plugin management menus list in WordPress
- Improved search engine display in the mode of referring users from the search engine to the website
- Improved widgets display and Ajax loading capability
- Improved loading of JS files based on plugin-specific pages

= 12.6.1 =
- Added Whip Package for getting visitor's IP address.
- Fixed get the country code when the Hash or Anonymize IP Addresses is enabled.
- Added database upgrade class for update page type.
- Fixed duplicate page list in report pages.
- Fixed bug to get home page title.
- Improvement Sanitize subject for sending email reporting.
- Improvement jQuery Datepicker UI.
- Improvement visitor's hit when there was a broken file in that request.

= 12.6 =
# Added
- Post/Page Select in statistics page reporting according to post Type.
- Online Users widget, A cool widget to show current online users!
- A new table `visitor_relationship` for saving visitors logs.
- `user_id`, `page_id`, `type` columns to `statistics_useronline` table.
- Visitor count column in Top Country widget.

# Improvement
- Improvement MySQL time query in all functions.
- Improvement online users page UI.
- Improvement Top referrals UI.
- Improvement CSV exporter.
- Improvement pagination in admin pages that used the WordPress `paginate_links`.
- Improvement time filter in admin pages stats.
- Improvement  `admin_url` link in all admin pages.
- Improvement text wrap in all meta boxes.
- Fixed reset number online users list in period time.
- Schedule list in statistical reporting.
- Refer Param in Top Referring Sites page.
- Fix method to get IP addresses.
- Fix Page CSS.
- Fix the error of No page title found in the meta box.
- Fix show number refer link from custom URL.
- Fix update option for Piwik blacklist.

# Deprecated
- Remove `WP_Statistics_Pagination` class.
- Deprecate Top Search Words (30 Days) widget.

= 12.5.7 =
* Added: The Edge To Browser List.
* Added: `date_i18n` function in dates for retrieving localized date.
* Improved: The Browsers charts.
* Improved: Minor issues in GeoIP update function.
* Optimized: All png files. (60% Save).

= 12.5.6 =
* Fixed: Counting stats issue in Cache mode.

= 12.5.5 =
* Improved: The WP-Statistics Metaboxes for Gutenberg!
* Improved: The `params()` method.
* Improved: Referrers URL to be valid.

= 12.5.4 =
* Disabled: Notice cache in all admin pages just enabled in the summary and setting of WP-Statistics pages.
* Improved: Some methods. `params()` and `get_hash_string()`.

= 12.5.3 =
* Added: Option for enabling/disabling the hits meta box chart in the edit of all post types page and that option is disabled by default.
* Improved: The responsive problem of Recent Visitors and Latest Search Words widgets in WP Dashboard.
* Improved: Avoid using jQuery in the inline script to for send request when the cache is enabled.
* Improved: The GeoIP updater.
* Improved: The cache process in the plugin.
* Improved: Get location for Anonymize IP Addresses.
* Improved: The query in the Author Statistics page.

= 12.5.2 =
* Improved: Some issues in php v5.4

= 12.5.1 =
* Added: Cache option for support when the cache enabled in the WordPress.
* Added: Visitor's city name with GeoIP, you can enable the city name in Settings > Externals > GeoIP City
* Added: WP-Statistics shortcode in the TinyMCE editor. you can use the shortcode easily in the posts and pages.
* Added: Qwant search engine in the Search Engine Referrals.
* Added: Referrers to WP-Statistics shortcode attributes. e.g. `[wpstatistics stat=referrer time=today top=10]`
* Added: [WhichBrowser](https://whichbrowser.net/) and [CrawlerDetect](https://crawlerdetect.io/). These libraries give us more help in identifying user agents. the Browscap library removed.
* Improved: The Datepicker in the WP-Statistics pages, supported WordPress custom date format.
* Improved: The pagination class.
* Improved: The assets and fixed conflict ChartJS issue, when the Hit Statistics Meta box was enabled in posts/pages.
* Improved: The responsive summary page.
* Improved: Exclude Ajax requests, now compatible with [Related Post by Jetpack](https://jetpack.com/support/related-posts/).
* Improved: Some issues.
* Updated: Chart.js library to v2.7.3
* Enabled: Hit Statistics in posts/pages. the conflict problem solved.
* Disabled: The setting menu when the current user doesn't access.
* Disabled: Baidu search engine by default after installing.

= 12.4.3 =
* Disabled: The welcome page and Travod widget.

= 12.4.1 =
* Implemented: The `do_welcome()` function.
* Updated: Libraries to latest version.
* Added: `delete_transient()` for deleting transients when uninstalling the plugin.

= 12.4.0 =
* Removed: The Opt-Out removed.
* Added: Anonymize IP addresses option in the Setting > Privacy.

= 12.3.6.4 =
* Updated: Libraries to latest version.
* Enabled: The suggestion notice in the log pages.
* Improvement: Counting non-changing collections with `count()`. Thanks [Daniel Ruf](https://github.com/DanielRuf)

= 12.3.6.3 =
* Disabled: The suggestion notice.

= 12.3.6.2 =
* Tested: With PHP v7.2.4
* Added: Suggestion notice in the log pages.
* Added: New option for enable/disable notices.

= 12.3.6.1 =
* Improvement: I18n strings.
* Improvement: GDPR, Supported for DNT-Header.
* Improvement: GDPR, Added new option for delete visitor data with IP addresses.

= 12.3.6 =
* Note: GDPR, We Updated Our [Privacy Policy](https://wp-statistics.com/privacy-and-policy/).
* Added Privacy tab in the setting page and moved Hash IP Addresses and Store entire user agent in this tab.
* Added Opt-out option in the Setting page -> Privacy for GDPR compliance.
* Updated: Chart.js library to v2.7.2
* Fixed: Issue to build search engine queries.

= 12.3.5 =
* Improvement: Isolation Browscap cache processes to reduce memory usage.
* Improvement: Include `file.php` and `pluggable.php` in GeoIP downloader when is not exists.
* Fixed: GeoIP database update problem. Added an alternative server for download database when impossible access to maxmind.com

= 12.3.4 =
* Updated: Browscap to v3.1.0 and fixed some issues.
* Improvement: Memory usage in the plugin when the Browscap is enabled.
* Improvement: Cache system and update Browscap database.

= 12.3.2 =
* Added: New feature! Show Hits on the single posts/pages.
* Added: Pages Dropdown in the page stats.
* Fixed: Menu bar for both frontend & backend.
* Fixed: Issue to create the object of the main class.
* Fixed: Issue to get page title in empty search words option.
* Fixed: Issue to show date range in the charts.

= 12.3.1 =
* We're sorry about last issues. Now you can update to new version to resolve the problems.
* Updated: Composer libraries.
* Fixed: A minor bug in `get_referrer_link`.
* Improvement: `wp_doing_cron` function, Check before call if is not exist.
* Fixed: Issue to get IP in Hits class.
* Fixed: Issue to get prefix table in searched phrases postbox.
* Fixed: Issue in Browscap, Used the original Browscap library in the plugin.
* If you have any problem, don't forget to send the report to our web site's [contact form](https://wp-statistics.com/contact/).

= 12.3 =
* The new version proves itself more than twice as faster because we had a lot of changes in the plugin.
* Improvement: Management processes and front-end have been separated for more speed.
* Improvement: MySQL Queries and used multi-index for `wp_statistics_pages`.
* Improvement: Top Referring widget in Big data. Used Transient cache to build this widget data.
* Fixed: Issue in checking the Cron request.
* Fixed: Issue in i18n strings. The `load_plugin_textdomain` missed.
* Fixed: issue in generating query string in some state pages.
* Fixed: issue in admin widget. The `id` in label missed and used `get_field_id` method to get a correct id.
* Fixed: Admin bar menu icon.
* Updated: Chart.js library to v2.7.1

= 12.2.1 =
* Fixed: Issue to `add_column` callback.

= 12.2 =
* The new version proves itself more than twice as faster because we had a lot of changes in the plugin.
* Improvement: Many functions converted to classes.
* Improvement: Export data on the optimization page.
* Improvement: Constants, Include files.
* Improvement: Setting/Optimization page stylesheet and removed jQuery UI to generate tabs.
* Added: Top Search Words in the plugin.
* Fixed: Some notices error.
* Removed: Some unused variables.
* Removed: Force English option feature in the plugin.
* Thanks [Farhad Sakhaei](https://dedidata.com/) To help us with these changes.

= 12.1.3 =
* We're sorry about last issues. Now you can update to new version to resolve conflict issues.
* Fixed: Chart conflict issues with other libraries.
* Fixed: Chart height issue in css.
* Fixed: Correct numbering for pages > 1 in Top Referring page. [#22](https://github.com/wp-statistics/wp-statistics/pull/22/files)
* Fixed: Don't run the SQL if `$reffer` is not set. [#21](https://github.com/wp-statistics/wp-statistics/pull/21)
* Fixed: Refferer url scheme. [#24](https://github.com/wp-statistics/wp-statistics/pull/24) Thanks [Farhad Sakhaei](https://github.com/Dedi-Data)
* Fixed: Network menu icon.

= 12.1.0 =
* Added: Awesome charts! The Chartjs library used in the plugin for show charts.
* Updated: Missed flags icons. (Curaçao, Saint Lucia, Turkmenistan, Kosovo, Saint Martin, Saint Barthélemy and Mayotte)
* Updated: Countries code.
* Updated: Settings and Optimization page styles.
* Fixed: Showing data on the Browsers, Platforms and browsers version charts.
* Fixed: Postbox container width in Logs page.
* Removed: `WP_STATISTICS_MIN_EXT` define for load `.min` version in css/js.
* Removed: Additional assets and the assets cleaned up.

= 12.0.12.1 =
* Fixed: PHP syntax error for array brackets when the PHP < 5.4

= 12.0.12 =
* Added: Add-ons page! The Add-ons add functionality to your WP-Statistics. [Click here](https://wp-statistics.com/add-ons/) to see current Add-ons.
* Fixed: Translations issue.
* Updated: GeoIP library to v2.6.0
* Updated: admin.min.css

= 12.0.11 =
* Release Date: August 17, 2017
* Fixed: links issue in the last visitors page.
* Fixed: i18n issues (hardcoded strings, missing or incorrect textdomains).
* Updated: admin CSS style. set `with` for Hits column in posts/pages list.
* Updated: Improve consistency, best practices and correct typos in translation strings.
* Updated: More, Reload and Toggle arrow buttons in metaboxes are consistent with WP core widget metaboxes, with screen-reader-text and key navigation. by [Pedro Mendonça](https://profiles.wordpress.org/pedromendonca/).

= 12.0.10 =
* Release Date: July 24, 2017
* Added: UptimeRobot to the default robots list.
* Fixed: Uses `esc_attr()` for cleaning `$_GET` in referrers page.
* Removed: `screen_icon()` function from the plugin. (This function has been deprecated).

= 12.0.9 =
* Release Date: July 3, 2017
* Fixed: XSS issue with agent and ip in visitors page, Thanks Ryan Dewhurst from Dewhurst Security Team.
* Updated: GeoIP library to v2.5.0
* Updated: Maxmind-db reader library to v1.1.3

= 12.0.8.1 =
* Release Date: July 2, 2017
* Fixed: load languages file. please visit [translations page](https://wp-statistics.com/translations/) to help translation.

= 12.0.8 =
* Release Date: June 29, 2017
* Fixed: SQL Injection vulnerability, thanks John Castro for reporting issue from sucuri.net Team.
* Added: new hook (`wp_statistics_final_text_report_email`) in email reporting.
* Removed: all language files from the language folder. Translations have moved to [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/wp-statistics).

= 12.0.7 =
* Release Date: June 8, 2017
* WordPress 4.8 compatibility
* Updated: WP-Statistics logo! Thanks [Arin Hacopian](http://aringrafix.com/) for design the logo.
* Removed: manual file and moved to [wp-statistics.com/category/documentation](http://wp-statistics.com/category/documentation)
* Fixed: items show issue in referring page.
* Fixed: recent visitor link in dashboard widget.

= 12.0.6 =
* Release Date: April 27, 2017
* Fixed: Additional XSS fixes, thanks Plugin Vulnerabilities Team.

= 12.0.5 =
* Release Date: April 6, 2017
* Fixed: Referrers, that are not search engines, are missing from the referrers widget/page.
* Fixed: Additional XSS fixes, thanks Gen Sato who submitted to JPCERT/CC Vulnerability Handling Team.
* Fixed: Updated CSS definition for widgets to avoid overflow only for WP Statistics widgets instead of all active widgets to avoid conflicts with other plugins.

= 12.0.4 =
* Release Date: April 1, 2017
* Fixed: Additional XSS issue with referrers, thanks Gen Sato who submitted to JPCERT/CC Vulnerability Handling Team.
* Updated: Optimizations for referrers encoding.
* Updated: Logic for detecting invalid referrer types to capture more types.

= 12.0.3 =
* Release Date: March 31, 2017
* Fixed: Additional XSS issue with referrers, thanks Gen Sato who submitted to JPCERT/CC Vulnerability Handling Team.

= 12.0.2 =
* Release Date: March 30, 2017
* Fixed: Top referrer widget was not using the new search table.
* Fixed: On the referrers page, selecting a host would reset the date range.
* Fixed: XSS issue with date range picker, thanks Anon submitter to JPCERT/CC Vulnerability Handling Team.
* Fixed: XSS issue with referrers, thanks Gen Sato who submitted to JPCERT/CC Vulnerability Handling Team.

= 12.0.1 =
* Release Date: March 24, 2017
* Added: Check for BCMath or GMP Math extensions to support newer GeoIP database files.
* Fixed: Robots list not being updated on upgrades properly in some cases.
* Fixed: wp_statistics_get_uri() to handle cases where site and home URI's are different.
* Fixed: wp_statistics_get_uri() to validate what is being removed to make sure we don't remove the wrong things.
* Fixed: Display of individual referring site stats.

= 12.0.0 =
* Release Date: February 18, 2017
* Added: Categories, tags and authors stats pages.
* Added: Option to exclude AJAX calls from the statistics collection.
* Fixed: Removal of settings now uses the defaults and handles a conner case that could cause corrupt settings to be saved during the reset.
* Fixed: URI retrieval of the current page could return an incorrect result in some cases.
* Fixed: Images in the HTML version of the admin manual did not display correctly in Microsoft IE/Edge.
* Fixed: Incorrect variable name on the exclusions page for the robots list.
* Updated: After "removal" the notice on the plugins page is now at the top of the page as an admin notice instead of being embedded in the plugin list.
* Updated: Split change log, form this point forward only the changes for the last two major versions will be included, older entries can be found in the changes.txt file in the plugin root.

= 11.0.3 =
* Release Date: January 13, 2017
* Added: Option to reset plugin options without deleting the data.
* Fixed: If IP hashing as enabled a PHP would be generated during the hashing.
* Fixed: Typo in JavaScript code that would cause some errors not to be displayed.
* Fixed: Make sure the historical table exists before checking the keys on it which would cause extra output to be generated on first install.
* Updated: RTL CSS styles for left/right div's in the admin dashboard, thanks sszdh.

= 11.0.2 =
* Release Date: December 1, 2016
* Fixed: Top visitors page css for date picker.
* Fixed: Incorrect url for link on recent visitors widget.
* Fixed: Make sure the tick intervals are always whole numbers, otherwise the axis ticks won't match up with the data on line charts.
* Fixed: Make sure when looking up a page/post ID for a URL to take the latest visited id instead of the first in case the URI has been reused.
* Fixed: Duplicate display of hit statistics on hits page in some corner cases.

= 11.0.1 =
* Release Date: November 7, 2016
* Fixed: Don't refresh a widget if it's not visible, fixes the widget being replaced by a spinner that never goes away.
* Updated: Minimum PHP version is now 5.4.
* Updated: Additional error checks for new IP code.
* Updated: jqPlot library to version development version and added DST fix.

= 11.0 =
* Release Date: October 28, 2016
* Added: IPv6 Support.
* Added: Time attribute to searches shortcode.
* Added: Basic print styles for the overview and log pages.
* Fixed: Default provider for searches shortcode.
* Fixed: Display of top sites list when the display port is very small would .
* Fixed: CSS for date picker not loading.
* Fixed: Incorrect stats on some pages for date ranges that end in the past.
* Fixed: Date range selector on stats now properly displays a custom range after it has been set.
* Fixed: "Empty" log widget columns could not have widgets added to them.
* Updated: GeoIP library to version 1.1.1.
* Updated: phpUserAgent library to 0.5.2.
* Updated: Language on the front end widget to match the summary widget in the admin.
* Removed: Check for bc math.
* Removed: Last bits of google maps code.

= 10.3 =
* Release Date: August 19, 2016
* Added: Support for minified css/js files and the SCRIPT_DEBUG WordPress define.
* Added: <label> spans around the text for widget fields for easier styling.
* Added: 'AdsBot-Google' to the robots list
* Fixed: Pop up country information on the map dashboard widget will now stay on top of the WordPress dashboard menus.
* Fixed: WP_DEBUG errors in front end widget.
* Updated: JQVMap library to version 1.5.1.
* Updated: jqPlot library to version 1.0.9.
* Updated: GeoIP library to version 2.4.1.

= 10.2 =
* Release Date: August 2, 2016
* Added: Support for use page id in Get_Historical_Data function.
* Updated: jQuery CSS references.
* Fixed: Various WP_DEBUG warnings.
* Fixed: Incorrect URL in quick access widget for some of the totals.
* Fixed: Make sure to escape the post title in the widget otherwise the graph may not be displayed correctly.
* Removed: Google Maps support as Google no longer supports keyless access to the API (http://googlegeodevelopers.blogspot.com.es/2016/06/building-for-scale-updates-to-google.html).

= 10.1 =
* Release Date: April 3, 2016
* Updated: Top pages page to list the stats for the selected date range in the page list.
* Updated: Added check for gzopen() function to the Optimization page as some builds of PHP are broken and do not include it which causes the GeoIP download to fail causing a white screen of death in some cases.
* Updated: Added check to make sure we can write to the upload directory before doing so.
* Updated: User Agent Parser library updated to V0.5.1.
* Updated: MaxMind Reader Library updated to V1.1.
* Fixed: Only display the widgets on the overview page that have their features enabled.
* Fixed: Top pages list failed when there were less than 5 pages to display.
* Fixed: Manual download links did not function.
* Fixed: Typo in function name for purging the database.
* Fixed: Renamed the Czech and Danish translation file names to function correctly.
* Fixed: Ensure we have a valid page id before record the stat to the database to avoid an error being recorded in the PHP error log.

= 10.0.5 =
* Release Date: February 5, 2016
* Fixed: Date range selector display after entering a custom date range.
* Fixed: Date ranges that ended in the past displaying the wrong visit/visitors data.

= 10.0.4 =
* Release Date: January 21, 2016
* Fixed: Recent Visitors widget in the dashboard did not work.
* Fixed: Top Visitors in Overview page would not reload.
* Fixed: Links for yesterday and older visitors count went to wrong page.
* Fixed: Typo in purge code that caused a fatal error.

= 10.0.3 =
* Release Date: January 19, 2016
* Updated: Google map API now always uses https.
* Fixed: Google map error that broken the overview page display of charts and the map.

= 10.0.2 =
* Release Date: January 19, 2016
* Added: Additional error checking on widget load so they will retry if there is a failure.
* Fixed: Added code to flush out invalid widget order user meta.
* Fixed: Include Fatal Error if corrupt data was passed to the ajax widget code.

= 10.0.1 =
* Release Date: January 18, 2016
* Fixed: If you re-ordered the widgets on the overview screen and then reloaded the page, all the widgets would disappear.

= 10.0 =
* Release Date: January 15, 2016
* Added: Widgets now support reloading on overview and dashboard screen.
* Updated: Overview screen now loads widgets dynamically to reduce memory usage.
* Updated: Dashboard widgets now load dynamically.
* Updated: Enabling dashboard widgets now no longer require a page load to display the contents.
* Updated: Replaced the old eye icon and "more..." link on the right of the title on the overview widgets with a new icon on the right beside the open/close icon.
* Fixed: Removed extraneous single quote in SQL statement on referrers page, thanks jhertel.
* Fixed: Order of parameters in referrers page when viewing individual referrers was incorrect and resulted in a blank list.
* Fixed: UpdatedSQL for last post date detection to order by post_date instead of ID as someone could enter a date in the past for their publish date.  Thanks PC1271 for the fix.
* Fixed: The referrers widget would only select the first 100k records due to a limit in PHP/MySQL, it will now select all records.
* Removed: Widget selection and ordering from the settings page, the "Screen Options" tab can now be used on the enabled/disable widgets and drag and drop will remember their location.
* Removed: Overview page memory usage in the optimization page as it is no longer relevant.
