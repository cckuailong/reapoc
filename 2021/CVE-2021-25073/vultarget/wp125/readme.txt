=== Plugin Name ===
Contributors: redwall_hp
Plugin URI: http://www.webmaster-source.com/wp125-ad-plugin-wordpress/
Author URI: http://www.webmaster-source.com
Donate link: http://www.webmaster-source.com/donate/?plugin=wp125
Tags: ads, 125x125, management, advertisement
Requires at least: 2.8
Tested up to: 4.3.1
Stable tag: 1.5.4

Easy management of 125x125 ads on your blog.  Ads can be run for a specified number of days, and will automatically be taken down. Track clicks too.



== Description ==

If you've given up the low-paying and slightly obtrusive ad networks used by many new bloggers, in favor of selling ads directly, you may have been frustrated with the excessive time involved managing your ads. Not only do you have to find advertisers to sponsor your blog, you have to manually edit your template to put the ad in, and then head over to your favorite calendar app to set an alert to remind you when to take the ad down.

Time consuming practices like those are a thing of the past. The WP125 plugin can help you manage your ads more efficiently, leaving you with more time to write new posts. The plugin adds a new "Ads" menu to the WordPress admin, featuring submenus for tweaking display settings and adding and removing ads.

Features include:

* One or two column ad display, and support through template tags to implement your own unconventional design.
* Show as many ads as you want, and in either manual or random order
* Keep track of how many times an ad is clicked
* When creating a new ad, you don't have to calculate the end date yourself. Just input how many days you wish the ad to run for, and the correct date will be applied. The ad will be automatically taken down when the time comes.
* When an ad run is over, the record is archived on the Inactive ads screen, so you can check on the final click count, or revive the ad for another run.
* When an ad slot is empty, a placeholder ad of your choice will be displayed. This could be a "Your Ad Here" image linking to a page with statistics and pricing, or an affiliate link.
* Optionally recieve email notifications when an ad expires. Useful if you send follow-up messages to advertisers, or if you just want to stay in the know.



== Installation ==

1. Download and unzip the package.
2. FTP the entire "wp125" directory to your /wp-content/plugins/ directory on your blog.
3. Activate the plugin on the "Plugins" tab of the administartion panel.
4. Either use the included "WP125: Ads" widget, or place the `<?php wp125_write_ads(); ?>` template tag where you wish your ads to appear.
5. Go to the new "Ads" section of the WordPress admin, where you can tweak settings, such as the maximum number of ads to be shown at once (the default is 6), and how they should be displayed.



== Upgrading ==

You may upgrade the plugin via the automated system in WordPress 2.5 or greater, or "old-style" by downloading the new one and then
1. Deactivating plugin
2. Uploading the updated files
3. Reactivating plugin



== Frequently Asked Questions ==

= What if I don't want to arrange my ads in one OR two columns? =
If you want to arrange your ads in an unconventional manner, you can use the `<?php wp125_single_ad(num); ?>` template tag (replace "num" with the number of an ad slot). The tag will return one ad with minimal formatting (simply <a><img /></a>). You can use multiple instances of the tag in your template to set lay your ad slots out in whatever way you choose.

= One of my ads hit it's expiration date. Where did it go? =
When an ad's time duration is over, it disappears off your site, and is removed from the Active ads page in the WordPress admin. To access the record, just click the "Inactive" link on the Manage screen. The page should update to show all of your inactive ads.

= What if I don't want an ad to be taken down automatically? =
Just select "I'll remove it manually" for the expiration date when you go to create the ad.

= How do I style my ads different than the default? =
First, uncheck the Default Style box on the Settings page. This will remove the default styling. Next, make use of your mad CSS skills to re-style the ads. Here's what the default CSS looks like:

`/* Styles for one-column display */
#wp125adwrap_1c { width:100%; }
#wp125adwrap_1c .wp125ad { margin-bottom:10px; }

/* Styles for two-column display */
#wp125adwrap_2c { width:100%; }
#wp125adwrap_2c .wp125ad { width:125px; float:left; padding:10px; }`

= How do I make the ads open in a new window? =
If you absolutely *must* have your ads open in a new window when clicked, open the wp125.php file and find the `define(...` line near the top and remove the first `//` from the beginning.

= Why is the Slot dropdown missing when I try to add a new banner? =
Turn off AdBlock. It indiscriminately zaps page elements containing the word "ad," which means you're going to have a bad time managing ads...

= My ads don't appear in two columns, even though I have the option set! How do I fix this? =
The parent DIV that the ad code is inside probably isn't wide enough. You really need a *minimum* of 250px of horizontal space to have two ad columns, more if you use the default CSS. You could try reducing the CSS padding around the ads from 10px to something lower.

`#wp125adwrap_2c .wp125ad { padding:4px; }`



== Screenshots ==
1. A few 125x125 ads, in two-column format.
2. The Add/Edit ads screen.



== Known Issues ==
* If you have WP Super Cache installed on your blog, it may conflict with WP125's click tracking feature. To fix this, add "index.php" on a new line in the "Rejected URLs" field of the WP Super Cache options page. This will disable caching for yourblog.com/index.php. If someone goes to yourblog.com, they will still get the cached version, but since WP125's click tracker URLs look like "/index.php?adclick=1," they will avoid the cache.



== Support ==
If you're having a problem with the plugin, try posting on the official WordPress forum at http://wordpress.org/support/ (be sure to use the tag "WP125"!). I, or another user of the plugin, will hopefully be able to answer your questions. Or send me an email via the contact form on Webmaster-Source.com.



== Translation Credits ==
* Danish Translation: [Georg S. Adamsen](http://wordpress.blogos.dk/)
* French Translation: Alexandre Cloquet
* Italian Translation: [Gianni Diurno](http://gidibao.net/index.php/portfolio/)
* Russian Translation: [M. Comfi](http://www.comfi.com)
* Simplified Chinese Translation: [Sam Zuo](http://bwskyer.com/)
* Dutch Translation: Jackey van Melis
* Brasilian Portuguese Translation: José de Menezes Filho
* German Translation: Simon Kraft
* Romanian Translation: [Web Hosting Geeks](http://webhostinggeeks.com/)
* Slovak Translation: Branco Radenovich, [Web Hosting Geeks](http://webhostinggeeks.com/)
* Ukrainian Translation: Michael Yunat, [GetVOIP.com](http://getvoip.com/blog)



== Changelog ==
* 1.0.0 - Initial release
* 1.1.x - Some security and performance fixes, adds several new customization options and a few major features.
* 1.2.x - Added new features: Recieve email notifications in advance of ads expiring, alternating CSS classes on ads, and open ads in new windows if you absolutely *must.*
* 1.3.x - Localization support (with French and Spanish to start), iCalendar subscription of ad expirations, WP Dashboard widget. Fixed errant "ADLINK_EXTRA"s in Single ads, fixed "no ads, no placeholders" bug, etc..
* 1.3.7 - Added Russian translation.
* 1.3.8 - Added zh_CN translation
* 1.3.9 - Changed SQL query on 131 of adminmenus.php to fix bug where ads are not inserted in some situations. Added Dutch and Brasilian Portuguese translation files.
* 1.4.0 - Now with WordPress 3.0 support!
* 1.4.1 - Added German translation and fixed a stylesheet enqueue bug for WP 3.3.
* 1.4.2 - Now with support for multiple widgets!
* 1.4.3 - Added Romanian translation.
* 1.4.4 - Fixed a race condition that could cause many duplicate expiration emails to be sent on high-traffic sites.
* 1.4.5 - Added uploader, plus fixed some potential vulnerabilities. (Thanks to Charlie Eriksen via Secunia SVCRP.)
* 1.4.6 - Fix for duplicate ad expiration emails, part II: The Bug Strikes Back.
* 1.4.7 - Added uninstaller to properly remove WP125's database tables when removing the plugin.
* 1.4.8 - Translation update.
* 1.4.9 - Some changes to the default CSS. Added a clearfix to the ads in two-column mode (finally!). Also, updated some buttons to match changes in WordPress.
* 1.5.0 - Fixed a potential CSRF vulnerability. (Minor security fix.)
* 1.5.1 - Added Czech translation.
* 1.5.3 - Added Ukrainian translation
* 1.5.4 - Updated to support WordPress 4.3
