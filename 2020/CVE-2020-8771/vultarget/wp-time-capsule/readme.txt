=== Backup and Staging by WP Time Capsule ===

Contributors: dark-prince, WPTimeCapsule
Tags: backup, staging, schedule backup, DB backup, database backup, amazon backup, amazon s3 backup, amazon, auto backup, auto updater, auto updates, automatic updates, back up, backup before update, backup files, backup guard, backup mysql, backup plugin, backup posts, backup tool, backup without zip, backupguard, backups, clone, cloning, cloud backup, complete backup, db backup, db migration, dropbox backup, dropbox, duplicate, full backup, google drive backup, google drive, incremental migrate db, light weight backup, migrate, migration, real-time backup, real-time, restore, remote backups, rollback, s3, scheduled backup, screenshots, visual comparision, site backup, storage, synchronize, time, updates manager, updraft, website backup, wordpress backup, wordpress incremental backup, wasabi, wasabi backup, backup wasabi, wasabisys, wptimecapsule, wp backup, wp time capsule, wptc
Requires at least: 3.9.14
Tested up to: 5.2.2
Stable tag: 1.21.15

WP Time Capsule is an automated incremental backup plugin that backs up your website changes as per your schedule to Dropbox, Google Drive, Amazon S3, and Wasabi.

== Description ==

[WP Time Capsule](https://wptimecapsule.com/ "Incremental Backup for WordPress") was created to ensure peace of mind with WP updates and put the fun back into WordPress. It uses the cloud apps' native file versioning system to detect  changes and backs up just the changed files and db entries to your account.
<br>
**With WP Time Capsule you can**<br>
<br>
**Backup in real-time**
You can backup your site in real-time which means you will now be able to revert your site to how it was just an hour ago. Also, you can change the backup interval to every 6 hours or 12 hours or daily. <br>
<br>
**Staging**
With a single click staging it's a breeze to test any change you are planning to do on your site. Test your updates on staging with a single click<br>
<br>
**Auto backup and update**
We automatically backup before each update, if an update causes any undesired change. You are a click away from restoring.<br>
<br>
**Encrypted DB backups & GDPR Compatible**
You can encrypt your DB backups to add an extra layer of security and makes your backup GDPR compatible. <br>
<br>
**Clone/Migrate**
You can now use the WPTC backup to clone or migrate your site to a new location at ease.<br>
<br>
**Screenshot Comparision**
WPTC will take screenshots before an update and compare it with the screenshots taken after the update and will send you an email if there are any visible changes on the site.<br>

**How is WP Time Capsule different than other backup plugins?**
<br>
WPTC is unique in 4 ways -<br>
1. It backs up and restores only the changed files & DB and not the entire site every time.<br>
2. The files & DB are stored in your cloud storage app - Amazon S3, Wasabi, Dropbox or Google Drive.
3. We have used the cloud apps' native file versioning system to detect changes and maintain file versions. So backups and restores are as reliable as they get.
4. Most importantly, you can backup your site in real-time which means you will now be able to revert your site to how it was just an hour ago. Also, you can change the backup interval to every 6 hours or 12 hours or daily. 
<br><br>
**How does it work?**
<br>
1. Install the plugin and login with your wptimecapsule.com account.<br>
2. Next, connect the cloud app where you want to store the backup files. You can use Amazon S3, Wasabi, Dropbox or Google Drive.<br>
3. Once you connect the cloud app, we will automatically begin backing up your complete website to your cloud app account.<br><br>
After the first full backup is taken, you can schedule a time for WPTC to backup your websites. We will take care of your backups from here on.
This being done you will officially be *disaster-ready*. :)
<br><br>
**Backup**: Looks for files & DB changes since the last backup and uploads only the changes. The data is then stored securely in your cloud app account.
<br>
**Restore**: Checks revision history and displays the same. You can restore the site to any point in time or restore specific files & DB.
<br><br>
**How is it better?**<br>
BACKUP METHOD<br>
Traditionally - Backups are compressed and zipped. The Bad: Heavy server resource consumption.<br>
WPTC - No zipping. Changed files are directly dropped into your cloud account. The Good: ***Uses considerably less server resources***
<br><br>
BACKUP FILE<br>
Traditionally - Multiple zip files are created every time you backup. The Bad: Precious storage space is wasted.<br>
WPTC - Backs up incrementally. No multiple copies of files. The Good: ***Uses far less disk space***
<br><br>
RESTORE<br>
Traditionally - Unzip backup and restore the whole site. The Bad: Consumes time and server resource.<br>
WPTC - Restores only selected files. The Good: ***Faster restore and you can restore even if you don't have access to your site or plugin.***
<br><br>
Check out our [Knowledge Base](https://docs.wptimecapsule.com/)
Visit us at [wptimecapsule.com](https://wptimecapsule.com/ "Incremental Backup for WordPress")

Credits: Michael De Wildt for his WordPress Backup to Dropbox plugin based on which this plugin is being developed.

== Installation ==
= Minimum Requirements =
 * PHP version 5.3.1 or greater (recommended: PHP 5.4 or greater)
 * MySQL version 5.0.15 or greater (recommended: MySQL 5.5 or greater)

= Installation =
Installing WP Time Capsule is simple and easy. Install it like any other WordPress plugin.
<ol>
  <li>Login to your WordPress dashboard, under Plugins click Add New</li>
  <li>In the plugin repository search for 'WP Time Capsule' or upload the plugin zip file and install it</li>
  <li>After installation, login with your wptimecapsule.com account</li>
  <li>Then, connect with the cloud app that you want to use to backup your site</li>
  <li>Once the cloud app is connected, you can schedule your backup time and we will begin backing up the website to your cloud app according to schedule.</li>
</ol>

== Screenshots ==

1. **Backup calendar view** - You can view and restore files + database from a calendar view.
2. **Restore specific files** - View a list of files that have changed and been backed up and selectively restore them.
3. **Warp back your site in time** - You can restore the complete site back to a specific point in time.
4. **One click staging** - Set up your staging site in a single click.
5. **Backup Before Update** - Configure Auto and Backup Before Updates for your plugins, themes, and core to update automatically on the scheduled time.
6. **Database Encyption** - Encrypt your Database backup to add an extra security layer.
7. **Cloning** - Use the meta SQL file from your cloud storage to clone/migrate your site to a new location.

== Changelog ==

= 1.21.15 =
*Release Date - 23 Dec 2019*

* Fix : Update in Staging not working in few cases.

= 1.21.14 =
*Release Date - 13 Dec 2019*

* Fix : Wasabi US east bucket issue during restore.
* Fix : Auto update failure, in some cases.

= 1.21.13 =
*Release Date - 04 Nov 2019*

* Improvement : Major improvements in restore logic, to speed up the restore process.
* Improvement : Warning notice will be displayed, when the query recorder table gets big during realtime backup.

* Fix : Secure Auth Cookie warning is displayed in some sites, when IWP is installed.

= 1.21.12 =
*Release Date - 30 Sep 2019*

* Improvement : Excluding View tables by default during real-time backups.
* Improvement : Exclude files by size option will not allow setting less than 10MB.
* Improvement : Purple admin bar for the staging site front-end.
* Improvement : Enable admin login is now disabled by default on the staging settings.

* Fix: Rollback was not working properly when two plugins have simialr names.
* Fix: Excluding the plugins, themes update automatically after update failure was not working properly.
* Fix: DB Rows Per Batch settings save was not working properly.

= 1.21.11 =
*Release Date - 27 Aug 2019*

* Improvement : Google Drive retry mechanism improved.
* Improvement : Support for new S3 endpoints.

* Fix: IWP plugin updates were not handled properly.

= 1.21.10 =
*Release Date - 19 Aug 2019*

* Improvement : Plugin-Server communication method had been improved and security patches have been applied.

* Fix: Sweet alert modal conflict with other plugins.

= 1.21.9 =
*Release Date - 03 Jul 2019*

* Fix: Replace path error during staging in some cases.
* Fix: MainWP child site connection error, when WPTC is active.

= 1.21.8 =
*Release Date - 27 Jun 2019*

* Fix: Fatal error during WPTC plugin update.

= 1.21.7 =
*Release Date - 26 Jun 2019*

* Improvement: File iterator is optimized to take less load on the server.
* Improvement: Reduced unnecessary API calls during Google Drive and Dropbox uploads.

* Fix: DB backup failed on upper case WP prefix on windows server.
* Fix: Include/Exclude settings did not work correctly for upper case WP prefix.
* Fix: Update now button did not work as expected when a backup is already running.

= 1.21.6 =
*Release Date - 17 May 2019*

* Improvement: Plugin name is modified now.
* Improvement: Amazon and Wasabi are the recommended storage options now.

* Fix: No. of Trigger tables count check did not exclude staging tables.
* Fix: Update now button did not trigger backup, even when Always setting is ON, when not on Agency plan.
* Fix: Version mismatch error during WP core update, on multi-site installs.
* Fix: Automatic clearing of wptc_query_recorder table did not work as intended.
* Fix: Complete fix for dropbox api server load error.

= 1.21.5 =
*Release Date - 17 Apr 2019*

* Feature: Logout link on plan page.

* Improvement: After perform migration using WPTC backup, the site will be set as new.
* Improvement: Removed the replace site option, when a new site is created from clone using other plugins.
* Improvement: Support for new Wasabi endpoints.
* Improvement: Automatic resume of stopped restore process.
* Improvement: Dropbox API calls improved.
* Improvement: Automatic clearing of wptc_query_recorder table when it grows for more than a day.

* Fix: Trigger table limitation for real-time backups did not consider excluded tables.
* Fix: Update from IWP panel did not work when IWP's Client plugin branding was ON.
* Fix: Update in staging button was visible even when whitelabel staging restriction is ON.
* Fix: Backup Before Update option was not working as expected.
* Fix: JQuery file upload plugin updated.
* Fix: Migration to other site failed when sql file is more than 5MB.

= 1.21.4 =
*Release Date - 27 Mar 2019*

* Fix: Stop the Backup when Dropbox API limit is reached.

= 1.21.3 =
*Release Date - 13 Mar 2019*

* Fix: Dropbox API increased the server load.

= 1.21.2 =
*Release Date - 8 Jan 2019*

* Fix: Fatal error on multisite staging site.

= 1.21.1 =
*Release Date - 16 Dec 2018*

* Fix: Fatal error on PHP 5.4.

= 1.21.0 =
*Release Date - 14 Dec 2018*

* Feature: Support for Wasabi Cloud Storage.
* Feature: You can configure the initial setup in bulk.
* Feature: WPTC staging plugin will be updated to the latest version automatically.
* Feature: Live site white-label settings are applied to your staging site.

* Improvement: By default, WP Redis cache plugin files are excluded during the staging creation. 

* Fix: DB rows per batch limit value didn't get updated after making the changes.
* Fix: Staging Database tables are not deleted while deleting staging the site.
* Fix: Plugin screenshot settings were hidden in a few scenarios.

= 1.20.7 =
*Release Date - 5 Dec 2018*

* Fix: Metadata backup did not happen when PHP dump is used for DB backup.

= 1.20.6 =
*Release Date - 8 Nov 2018*

* Improvement: Subdomain multisite setup didn't work for staging.
* Improvement: Staging with modified wp-content folder support.
* Improvement: Restore improved for sites with a significant number of DB Tables.

* Fix: Backup Before Update button "always" setting not working.
* Fix: Backup Before Update not working when the update is triggered by InfiniteWP.
* Fix: Unwanted triggers will be deleted with this update.
* Fix: Trigger creation problem with table names and column names with a hyphen.
* Fix: Backups will be delayed on few sites when it's scheduled for 12 AM.
* Fix: Admin notices didn't come on a few scenarios.
* Fix: Restore improved for 500 error.
* Fix: Bridge restore and migration improved. 
* Fix: Issues with the Resume button are fixed.

= 1.20.5 =
*Release Date - 12 Oct 2018*

* Improvement: Multisite restore process revamped.
* Improvement: WP_CACHE is set False by default, during staging (wp-rocket).
* Improvement: Resume mechanism in Restore, Staging, and Staging to live.
* Improvement: WPTC account login is now restricted on child sites.
* Improvement: Support for Dropbox EVH - 365 days revision limit.
* Improvement: Excluded user.ini, WP rocket plugin, and Swift performance plugin files during staging.

* Fix: Conflicts with Imagify plugin during staging.
* Fix: Conflicts with Convert plus plugin while saving the settings.
* Fix: Staging excludes extensions took from Backup exclude settings.
* Fix: On 12-hour backup, Triggers are not deleted, so the SQL file size increased.
* Fix: Staging exclude tables setting didn't work correctly.
* Fix: Staging to live breaks image on subdirectory installation.
* Fix: After the restore, Child site is redirected to the network site  
* Fix: Updates notification are not coming on the staged site.
* Fix: BBU button was not showing on a few sites and "Backup Always" settings didn't work.

= 1.20.4 =
*Release Date - 26 Sep 2018*

* Fix: Issues while activating the plugin due to the duplicated folder "trunk".

= 1.20.3 =
*Release Date - 25 Sep 2018*

* Improvement: Upload handler moved.

= 1.20.2 =
*Release Date - 7 Sep 2018*

* Fix: Plans page radio button did not work.
* Fix: PHP Warning:  array_values() expects parameter 1 to be array, object given.
* Fix: A SQL syntax error on table wp_wptc_processed_iterator.
* Improvement: Support for showing general tab on white label settings.

= 1.20.1 =
*Release Date - 27 Aug 2018*

* Fix: DB backup improved.
* Fix: Real-time and 6-hour backup options are disabled when DB tables are large in number.
* Improvement: Support for new plans.
* Improvement: New UI for initial backup.

= 1.20.0 =
*Release Date - 9 Aug 2018*

* Feature: Whitelabel feature has been completely revamped. Now, you can hide/show any specific options on your WPTC plugin.
* Feature: Rollback! You can rollback your plugin/theme update to the nearest previous version.
* Fix: If auto-update list is empty, plugin server communication is broken on a few occasions.
* Fix: Staging login settings not saved.
* Fix: File copying process on staging is improved.
* Fix: Issues with URL changing on a sub-directory installation.
* Fix: Reduced the default file copy count to 200 to minimize the server utilization.


= 1.19.2 =
*Release Date - 9 Jul 2018*

* WordPress org caches the old plugin version. (No File/DB changes)


= 1.19.1 =
*Release Date - 6 Jul 2018*

* Fix: Adding plugins/theme to auto updates was not working.


= 1.19.0 =
*Release Date - 6 Jul 2018*

* Feature: Full support for Multisite Installations.
* Improvement: Support for custom login URL in staging site.
* Improvement: Permalink settings will be changed to default, and the .htaccess file will be rewritten during the staging site creation.
* Improvement: Now, non admin users can view the staging sites.
* Improvement: Added an option to connect your Amazon S3 bucket to WPTC plugin manually for those who are not willing to give full access to WPTC.
* Improvement: Added an option to automatically include any newly installed plugins/themes to auto updates list.
* Improvement: You can now disable the "exclude files more than a specific size" option.
* Fix: Staging site wp-admin page redirecting to the staging site.
* Fix: Compatibility issues with Theme My Login plugin. (login link not working)
* Fix: Error while upgrading from 1.17.x to 1.18.0+ on activity log. Error: v1.18.0 wptc_inc_exc_contents table creation failed.You have an error in your SQL syntax.


= 1.18.0 =
*Release Date - 18 Jun 2018*

* Feature: Migration. Now, you can use your WPTC plugin to migrate your WordPress site from one location to another.
* Feature: Separate Include and Exclude control for Live to staging and Staging to live.
* Feature: WPTC now supports other types of WordPress installations such as rename wp-content, move wp-content outside root directory, install WordPress on sub-directory.
* Improvement: Reduced wptc tables to 8.
* Improvement: Use analyze files as unbuffered queries (restore memory leak for larger sites)
* Improvemnt: Added a new exclude path - uploads/bb-press
* Fix: enable maintenance mode on your live site while doing the restore and copying the changes from your staging to live.
* Fix: On few occasions, vulnerable checks blocked the backup calls.

= 1.17.2 =
*Release Date - 31 May 2018*

* Fix: Staging process will replace all kind of links.
* Fix: Conflict with BulletProof Security.
* Fix: Updraft plus plugin not updating when WP Time Capsule plugin is active.
* Fix: Downloading decrypted database file is not working on few servers.

= 1.17.1 =
*Release Date - 25 May 2018*

* Fix: Syntax error on php v5.4.

= 1.17.0 =
*Release Date - 25 May 2018*

* Feature: Now you can encrypt your WPTC database backups.
* Feature: Fully automated S3 connection to WPTC with more hardened security.
* Feature: 365 days restore points depends on the plan you purchase.
* Improvement: You can select the restore window as per your need.
* Fix: Plugin activation throws an incorrect error if mu-plugin is not created.
* Fix: Backing up a large number of tables caused a few issues like timeout, etc., is fixed.
* Fix: While building the staging site, some links are failed to replace.
* Fix: On few occasions, Staging to live throws 500 error.

= 1.16.3 =
*Release Date - 04 May 2018*

* Improvement: Register WPTC as the must-use plugin.
* Improvement: Removed sentry library from the plugin.
* Improvement: Settings page improved.
* Fix: Restore failed in some scenarios on windows servers.
* Fix: Conflicts with plugins using Amazon PHP SDK.
* Fix: A few JS warnings fixed.

= 1.16.2 =
*Release Date - 19 Apr 2018*

* Fix: Updates check failed on few sites.

= 1.16.1 =
*Release Date - 18 Apr 2018*

* Fix: Plugin server communication failed on few sites while doing the updates check.
* Fix: WPML tables were automatically excluded in backups.
* Fix: Conflict with Advanced Custom Fields plugin.

= 1.16.0 =
*Release Date - 17 Apr 2018*

* Feature: Now WP Time Capsule supports premium plugin & theme backup before updates and auto updates.
* Feature: Now, you can schedule auto updates on your WPTC plugin.
* Improvement: Auto updates flow revamped entirely making auto updates more reliable than ever.
* Improvement: Automatically exclude only failed updates from the Auto-update settings rather than turning off entirely.
* Improvement: Now Backup before and Auto update will retry the failed updates.
* Improvement: Precise error messages on activity log if any updates get flunked.
* Fix: Clicking "stop staging" gave a wrong alert message.

= 1.15.10 =
*Release Date - 2 Apr 2018*

* Improvement: Google Drive upload and cache mechanism wholly rewritten. Now Google Drive backups is 2x faster than before.
* Improvement: Cleared few caches on WPTC tables which will reduce the WPTC tables size for users using Google Drive as their cloud storage.
* Improvement: Now WPTC server will understand your WordPress server configuration automatically and reduces the number of calls hitting your WP server ensuring your server is not overloaded.
* Improvement: Configure Table Query limit via Settings > Backup.
* Improvement: Plan choosing flow improved.
* Improvement: Sweet alert UI changed.
* Fix: Behavioural change in esc_sql() affects v4.3.15 and other few versions.
* Fix: Show Google authentication alert only when the site limit is reached.
* Fix: "Something went wrong" error on login fixed.

= 1.15.9 =
*Release Date - 14 Mar 2018*

* Fix: For some PHP errors, Restore halts during the copy process.
* Fix: Error during restore, "There is not enough free space on your server to restore."
* Fix: PHP Warning: A non-numeric value encountered.
* Fix: PHP Warning: rtrim() expects parameter 1 to be a string, array given.

= 1.15.8 =
*Release Date - 13 Mar 2018*

* Improvement: Realtime backup improved flow and realiblity using triggers.
* Fix: The First backup would not work while connecting a new Google Drive account.

= 1.15.7 =
*Release Date - 6 Mar 2018*

* Improvement: Restore process has speeded up
* Fix: Restore breaks when files count is massive.
* Fix: Deleted revisions are incorrectly applied while restoring the site.

= 1.15.6 =
*Release Date - 21 Feb 2018*

* Fix: "A region must be specified when using signature version 4 - set region" error for those who have created bucket on US standard region.

= 1.15.5 =
*Release Date - 20 Feb 2018*

* Improvement: Supports all new Amazon S3 bucket regions.
* Improvement: Instigated Select All button for selecting Auto and Vulns update plugin/theme list.
* Improvement: Login flow improved.
* Improvement: If a folder is not readable, Plugin will automatically add the folder to excludes list and resumes the backup.
* Improvement: Added these extensions .db .ds_store .tmp .log .zip .wpress to default excludes list.
* Improvement: Auto updates combined as a single backup.
* Improvement: Removed temporary connection issue notices on the wp-admin page.
* Improvement: Activity log view improved.
* Improvement: Alert will be given to the user while changing the cloud storage.
* Fix: Appropriate errors will be displayed when staging process fails.
* Fix: ftell() function was throwing warnings on PHP/MySQL dump creation.
* Fix: Issues with Multisite backup and update button.
* Fix: md5_file failed to open stream: No such file or directory warning is fixed.
* Fix: strpos() expects parameter 1 to be string, object given warning is fixed.
* Fix: filesize(): stat failed for unreadable files warning is fixed.
* Fix: php_uname() has been disabled for security reasons warning is fixed.

= 1.15.4 =
*Release Date - 5 Feb 2018*

* Feature: Database changes are backed up then and there as it happens.
* Improvement: Real-time backup will only backup the changed database queries.
* Fix: While restoring, database search takes a long time on MySQL v5.5.

= 1.15.3 =
*Release Date - 29 Jan 2018*

* Fix: Incorrect version of files are downloaded on certain scenarios.

= 1.15.2 =
*Release Date - 24 Jan 2018*

* Fix: PHP database dump had some issues in backing up binary and blob data.
* Fix: When Cloud Auth revoked on the plugin, the user is not redirected to the Cloud Auth page.

= 1.15.1 =
*Release Date - 17 Jan 2018*

* Improvement: Bridge restore downloads the latest version of WP Time Capsule.
* Fix: Staging would fail when WP Super cache or WordFence installed.
* Fix: Staging site login link redirects to the wrong URL.
* Fix: If wp-config.php has spaces between database credentials, Staging would fail.
* Fix: WP Time Capsule menu not shown on few multisite installations.
* Fix: Some plugins will not update if there are plugins with the same prefix.
* Fix: Dashboard activity removed on page reload.
* Fix: Improved error messages on update failures.
* Fix: Changed Backup before Manual update options to checkbox.
* Fix: Removed some auto update files.
* Fix: Add warning notice above auto-update settings, if auto updates are disabled in wp-config.php or other plugins.
* Fix: Log all cloud revokes on activity log.
* Fix: Migration alert notice would not work on certain scenarios.


= 1.15.0 =
*Release Date - 11 Jan 2018*

* Feature: Restore to Staging - Test your restores on your staging site.
* Feature: Upload backup data on every scheduled backup. So hereafter you don't need to worry even when your database is entirely wiped out. All your backup data are safe.
* Feature: Restore your site with WPTC bridge file even when all your site root files and database are deleted.
* Improvement: Files iterator performance improved.
* Improvement: Plugin and Server communication improved.
* Improvement: Login error messages improved.
* Improvement: Send automated email once Cloud storage is full.
* Fix: WPTC throws warnings when the file name has a single quote.
* Fix: Files iterator failed when symbolic link applied to a specific folder and open_basedir is enabled.
* Fix: Connect google drive via IPV4 when IVP6 not set correctly.
* Fix: WordPress admin page loads slowly in browsers on few servers.
* Fix: Site goes blank when curl_multi_exec() is not enabled when using amazon s3.
* Fix: Staging is not working well with unique subdirectory installations.
* Fix: Staging to live not working well on multisite installation

= 1.14.10 =
*Release Date - 8 Jan 2018*

* Fix: Incorrect version of files are downloaded while restoring a backup on specific scenarios for users who came from the plugin version below 1.14.0
* Fix: Restore messages are not displayed appropriately during the restore process.

= 1.14.9 =
*Release Date - 24 Dec 2017*

* Fix: Fixed a bug where it deleted a file which was excluded using file extensions.

= 1.14.8 =
*Release Date - 21 Dec 2017*

* Improvement: Restore flow has been improved for more reliability.

= 1.14.7 =
*Release Date - 20 Dec 2017*

* Improvement: Plugin and server communication is improved.
* Fix: Issues with downloading the purged files from dropbox during the restore process.

= 1.14.6 =
*Release Date - 14 Dec 2017*

* Fix: Scheduled backups got postponed whenever the site was unreachable.

= 1.14.5 =
*Release Date - 12 Dec 2017*

* Fix: Issues with downloading the purged files from cloud during the restore process.

= 1.14.4 =
*Release Date - 5 Dec 2017*

* Fix: State maintenance file would not backup in certain scenarios.
* Fix: Plan selection would show wrong interval for life time users.

= 1.14.3 =
*Release Date - 29 Nov 2017*

* Improvement: Full file system scanning for real-time backups (Changes made outside WordPress also will be backed up)
* Improvement: State maintenance, restore process will never bring back old files again.
* Improvement: Now you can exclude content for specific tables avoid uploading log tables on every backup. (backups up only the structure of that specific table)
* Improvement: Analyze button, Now you can check and take actions for large tables with a single click.
* Improvement: Show all Excluded Files button, Now you can check and take actions for the excluded files in a single click.
* Improvement: Added new log tables to the default exclude list.(excludes only the content)
* Improvement: Now user can define a value to exclude files more than the specified MB.
* Improvement: Added new extensions to the default exclude list to avoid unwanted files getting backed up on the primary backup.
* Fix: Incorrect Vulnerable email alert for WordPress updates.
* Fix: Database esc_sql() function affects the database backup on WordPress sites with version 4.8.3 and above (Thanks to Wim Peters for reporting the issue and providing the fix)
* Fix: Auto update false positive messages in the activity log.
* Fix: Restore downloads wrong file in some cases.
* Fix: Unable to re-auth when Amazon when bucket name is entered incorrectly.

= 1.14.2 =
*Release Date - 16 Nov 2017*

* Fix: Uploading huge files failed in fresh sites in certain cases.

= 1.14.1 =
*Release Date - 08 Nov 2017*

* Feature: Perform updates on staging without performing new staging.
* Improvement: Show processed files count while processing the files.
* Improvement: Auto update improved to eliminate fail cases.
* Improvement: Sending report issue flow improved.
* Improvement: Common settings for pushing the changes from live to staging and staging to live.
* Improvement: Staging settings change takes effect on your staging site immediately.
* Fix: File iteration failed in some instances on the open_basedir enabled server.
* Fix: Sweet alert conflicts with other plugins.
* Fix: Migration alert notice did not close even when the issue is fixed.
* Fix: Plugin database update failed on specific servers.
* Fix: Staging would throw a seeking exception on certain servers.
* Fix: Multiple emails were sent when staging site is completed.
* Fix: Notify backup failures through notices.

= 1.14.0 =
*Release Date - 1 Nov 2017*

* Improvement: New files iterator method to speed up all the processes like Backup, Staging and Restore.
* Improvement: All paths are changed to relative path.
* Improvement: All files with wp_ prefix on the root folder of site will be included by default.
* Improvement: SQL Queries and WPTC table structure optimized to reduce the server load and to speed up Backup and Restore.
* Improvement: Restore flow is changed and unwanted queries are removed to make the Restore process more reliable and faster.
* Improvement: Option to take actions when a site is migrated to a different Domain.
* Improvement: Bridge Restore is greatly improved.
* Improvement: Reduced WPTC service requests.
* Improvement: Staging settings content are improved.
* Fix: Empty Plugins/Themes folders are deleted during Restore.
* Fix: Issues in non UTF-8 files during Backup/Restore on certain servers.
* Fix: Not able to create Log folders on certain servers.
* Fix: Staging huge table cloning was not working on certain servers.
* Fix: SQL shell dump became corrupted in certain cases.
* Fix: json_encode warnings on server having PHP < 5.4.
* Fix: Wrong revision days count was shown in settings for free users.

= 1.13.1 =
*Release Date - 6 Oct 2017*

* Fix: Few unhandled exceptions on Dropbox API are fixed.

= 1.13.0 =
*Release Date - 26 Sep 2017*

* Feature: Real-time backups.
* Improvement: SQL files are compressed to save space on your cloud storage.
* Improvement: Enabled shell availability check before initiating any backup process.
* Improvement: Backups view sorted.
* Improvement: Introduced control backup revisions limit under settings.
* Improvement: Sweet alerts introduced.
* Fix: Few PHP warnings and query duplication warnings are suppressed.

= 1.12.5 =
*Release Date - 12 Sep 2017*

* Fix: Exceptions are thrown on certain servers while fetching Amazon S3 life cycle.

= 1.12.4 =
*Release Date - 11 Sep 2017*

* Feature: 120 days restore points for Amazon S3 and Dropbox.
* Improvement: Plugin and server communication is improved.
* Improvement: Staging requests are changed to Ajax for reliability.
* Fix: Few plugin files are not backed up correctly.
* Fix: Auto update not happening on certain servers.
* Fix: set_time_limit() function causing issues in certain servers.
* Fix: Restore process doesn't show any PHP related error.

= 1.12.3 =
*Release Date - 4 Sep 2017*

* Fix: Backup would stuck in certain server.

= 1.12.2 =
*Release Date - 30 Aug 2017*

* Improvement: Staging support for sites installed on windows server.
* Fix: Normalize paths on windows servers.

= 1.12.1 =
*Release Date - 28 Aug 2017*

* Fix: Revert live site's permalink on staging to live process.

= 1.12.0 =
*Release Date - 28 Aug 2017*

* Feature: Push changes made on your staging site to your live site in a single click.
* Feature: Ability to lock your staging site from public view.
* Feature: Customize vulnerability email alert.
* Improvement: Incremental backup and restore for larger sites more reliable and faster.
* Improvement: You will not be logged out from your WPTC account when you deactivate your WPTC plugin.
* Fix: Few files were uploaded to your cloud storage even if it's selected in the excluded list.

= 1.11.1 =
*Release Date - 1 Aug 2017*

* Fix: Few issues with query parser while restoring the Database backups.
* Fix: Incorrect emails were sent as "Unable to read" for the files which doesn't exist.
* Fix: Notification email sent for few plugin where no Vulnerability updates are available.

= 1.11.0 =
*Release Date - 26 Jul 2017*

* Improvement: Restore mechanism has been revamped for more reliability.
* Improvement: Communication had been improved, and security patches have been applied.
* Improvement: Any failures in uploading large files will be retried.
* Improvement: wp-salt and gd_config.php will be included by default in the first backup.
* Fix: Rigorous cURL status tests during requirements check.
* Fix: http_build query returns wrong data on few occasions.
* Fix: Staging page becomes empty in rare cases.
* Fix: Calendar page is not displayed correctly when a site name has single quotes.
* Fix: White-labeling is not working well on multisite installations.
* Fix: WPTC Account is logged out automatically from the plugin in a particular scenario.

= 1.10.2 =
*Release Date - 7 Jul 2017*

* Fix: Backup has been getting stuck and never completed for few Dropbox users.
* Fix: Backups will be stopped automatically when Cloud authentication is failed preventing the backup to run endlessly.

= 1.10.1 =
*Release Date - 27 June 2017*

* Fix: Schedule backup wouldn't run in certain scenarios.

= 1.10.0 =
*Release Date - 22 June 2017*

* Feature: Whitelabling is now available.
* Feature: Vulnerable plugins and WordPress updates are updated automatically.
* Improvement: Updated logo on sub-menu.
* Improvement: Dropbox will show authentication page on adding a Dropbox account on multiple sites.
* Fix: Initial setup requirement check would fail in certain scenarios.

= 1.9.5 =
*Release Date - 14 June 2017*

* Fix: jQuery of WPTC would conflict with other plugins.

= 1.9.4 =
*Release Date - 13 June 2017*

* Improvement: Dropbox SDK migrated to API V2 from API V1.
* Improvement: Settings page has been revamped.
* Improvement: Auto-whitelist WPTC IP in some security plugins installed on your sites.
* Improvement: Old activity log and WPTC database table clean up to run automatically
* Improvement: Checking minimum requirements during WPTC installation.
* Fix: Restore from Google drive would encounter failures in certain scenarios.
* Fix: For certain users, Updating a plugin/theme via InfiniteWP caused issues in WPTC.
* Fix: Other minor fixes.

= 1.9.3 =
*Release Date - 24 May 2017*

* Feature: WP Time Capsule will capture IWP updates now.
* Feature: WP Time Capsule will support Premium Themes and Plugins update now.
* Improvement: Restore flow is improved.
* Improvement: Loading mechanism of our plugin is optimized.
* Improvement: Plugins and Themes updating mechanism is improved and exact error message will be shown if update get failed.
* Improvement: Naming of Backups is improved.
* Improvement: Google analytics files will be included by default.
* Fix: Shell SQL dump threw warning in some servers.
* Fix: Report issue was sent from WP admin email instead of WP Time capsule email.

= 1.9.2 =
*Release Date - 10 May 2017*

* Improvement: PHP-MySQL Dump has been revamped.
* Fix: ZeroClipboard was causing issue in FireFox fixed.

= 1.9.1 =
*Release Date - 28 Apr 2017*

* Fix: Discourage search engines from indexing this site option gets enabled for live sites in certain scenarios.

= 1.9.0 =
*Release Date - 28 Apr 2017*

* Feature: Multisite compatible when staging on same server.
* Feature: Subfolder installations are compatible with staging on same server.
* Feature: Compatibility with Windows Server added.
* Improvement: When staging on same server permalinks are reset to avoid htaccess redirection.
* Improvement: Search engines will no longer index staged sites.
* Improvement: Color of WP admin bar changed to differentiate between live and staging site.
* Improvement: Credentials transfer during bridge restore has been revamped.
* Improvement: Use existing tokens for backing up new sites to Google Drive.
* Improvement: Default backup will now exclude files larger than 2GB.
* Fix: Restore would fail in subfolder WordPress installations.
* Fix: Filenames starting with @ would fail to upload to Dropbox.
* Fix: Database will be uploaded to Dropbox with hash in filename in certain scenarios.
* Fix: Include / Exclude file tree does not work when open_basedir is enabled.
* Fix: Dropbox authentication would fail in certain scenarios.
* Fix: Footer div and settings page of other plugins / themes gets removed in certain scenario.
* Fix: In some servers temp dir fails to create on plugin activation.
* Fix: FTP credentials were shown upon plugin activation for some users.
* Fix: jQuery of WPTC would conflict with other plugins.
* Fix: Site slows down when WPTC is installed.

= 1.8.6 =
*Release Date - 11 Apr 2017*

* Fix: Include/Exclude contents not working in free plan sites.
* Fix: Different server staging completed confirmation email had wrong staging url.

= 1.8.5 =
*Release Date - 10 Apr 2017*

* Improvement: Include/Exclude contents refactored for better performance.
* Improvement: WP Time Capsule plugin files loading optimized.

= 1.8.4 =
*Release Date - 6 Apr 2017*

* Improvement: Users can now control the number of files and db to be copied per loop during staging.
* Fix: Verification of auto-updates fail in certain scenarios.

= 1.8.3 =
*Release Date - 5 Apr 2017*

* Improvement: Auto update failure email will be sent to users.
* Improvement: Emails will be sent when staging on the same server.
* Fix: Files having a size of zero bytes fail to upload to Google Drive.
* Fix: Pop-ups generated via WPTC are not visible when certain themes are applied.
* Fix: Plugin activation throws function ‘admin_notice_on_dashboard’ not found warning.
* Fix: Certain servers were not supporting MySQL dump.

= 1.8.2 =
*Release Date - 30 Mar 2017*

* Improvement : Hashing method improved for faster restores.
* Fix: Serialized links would not update in the internal staging.
* Fix: Htaccess is modified to respect permalinks during staging.
* Fix: Staging folders were created with wrong permission.
* Fix: Activating plugin causes infinite redirects in certain scenarios.
* Fix: AJAX calls were conflicting with some plugins in certain scenarios.
* Fix: Database Dump has been introduced for faster DB backups.
* Fix: Login was not working with special characters in the password.
* Fix: WPTC was throwing some warnings on multisite.
* Fix: Added some cache/log files in the default excluding list.
* Fix: Other minor fixes.

= 1.8.1 =
*Release Date - 23 Mar 2017*

* Fix: Intermittent cURL errors causes the WordPress dashboard to go down.
* Fix: Cloud authentication is revoked when API throws an internal server error.
* Fix: Plugin would not proceed past the initial setup if temp file could not be created.

= 1.8.0 =
*Release Date - 14 Mar 2017*

* Feature : Auto Update Plugin/Theme manager launched.
* Feature : Stage an Update launched.
* Feature : Staging is now possible in the same server as the live site.
* Improvement : Backup before update has been fully reconfigured.
* Improvement : Progress bar is redesigned to reduce CPU overload.
* Improvement : Admin status bar with backup status has been removed.
* Fix : The plugin was conflicting with buddypress on earlier versions.
* Fix : Backup before update was affecting wordpress core functionalites.

= 1.7.2 =
*Release Date - 16 Feb 2017*

* Improvement : Revisioning system is modified to ensure plugin doesn’t backup data older than 30 days.
* Improvement : Old data revisions will be deleted from the database to free up space.
* Fix : Dropbox was conflicting with other plugins.
* Fix : Scheduled backup timing was configured incorrectly on certain servers.
* Fix : Files having a size of zero bytes would not be backed up.
* Fix : Backup before update model pop up would show during plugin installation instead of plugin update.
* Fix : Translation updates would fail during the backup.
* Fix : Dropbox authentication would fail on some servers.
* Fix : While staging sites, changing db prefix would fail or not update correctly.
* Fix : While staging sites on the same server, the staging process would fail.
* Fix : While staging sites, metadata upload would fail for larger sites.

= 1.7.1 =
*Release Date - 23 Jan 2017*

* Fix : Creating temp directory was unsuccessful in certain scenarios.

= 1.7.0 =
*Release Date - 20 Jan 2017*

* Feature : Introduced Staging, Now you can clone your wordpress site right from the WP Time Capsule plugin.
* Fix : When installing plugin from wordpress dashboard, the dialog box for “Backup before updates” was triggered.
* Fix : Updating WP Time Capsule plugin from third party services did not work as expected.

= 1.6.1 =
*Release Date - 9 Jan 2017*

* Fix : Restore did not start in certain scenario.

= 1.6.0 =
*Release Date - 9 Jan 2017*

* Feature : Introduced Backup Before Update which backs up your site automatically before each update.
* Fix : If the WPTC plugin is updated when a current backup is running, the backup gets stalled.
* Fix : Users were unable to see the Restore pop-up while restoring their sites.
* Fix : For certain users, uploading files to Amazon S3 would encounter failures.
* Fix : When open_basedir is enabled, CURL calls throw warnings.

= 1.5.3 =
*Release Date - 22 Nov 2016*

* Improvement : Improved Hashing for tracking file changes.
* Improvement : New files and folders are included in default exclude files list.
* Improvement : Error reporting has been improved.
* Fix : Excluding default cache files would stuck in certain scenarios for larger sites.
* Fix : Memory leak during backup is handled.
* Fix : Getting wrong home dir in exclude/include file tree.

= 1.5.2 =
*Release Date - 9 Nov 2016*

* Fix: Plugin doesn't work for WordPress sites running PHP version lower than 5.4

= 1.5.1 =
*Release Date - 7 Nov 2016*

* Improvement : Plugin-Server communication method had been improved and security patches have been applied.
* Improvement : Backups have been optimized to ensure files are handled better.
* Improvement: Added a few cache files and folders to the default exclude list.
* Fix : Restore would fail while creating recursive folders. (Thanks to Donna Cavalier for helping us fix this issue)
* Fix : Data for revisions of files larger than 5MB was logged incorrectly in the db.
* Fix : File monitor would generate incorrect results for certain scenarios and logging has been improved.
* Fix : Anonymous data was set to ON by default. (We respect our user's privacy, thanks to M Asif Rahman for bringing this to our notice)

= 1.5.0 =
*Release Date - 27 Oct 2016*

* Improvement : Calendar page showing backups is much responsive for smaller screens than before.
* Improvement : Calendar page now shows restore points instead of backup count for better understanding.
* Improvement : Initial setup flow and UI has been revamped.
* Fix : Backup wouldn’t run when files didn’t have sufficient read permissions. (Thanks to Pierre Sudarovich and Dennis Spengler for helping us fix this error)
* Fix : In certain scenarios while including or excluding files, the file size would be incorrect.
* Fix : Expanding the file tree when including or excluding files would display a load error. (Thanks to Pierre Sudarovich and Piet Bos for helping us fix this error)
* Fix : MySQL warning would be generated in certain scenarios. (Thanks to Doug Rider for helping us fix this error)
* Fix : Non Admin users were able to view the backup status on the Admin bar earlier.


= 1.4.6 =
*Release Date - 7 Oct 2016*

* Feature : File Change Monitor - When one or more files get backed up continuously during 3 consecutive backups, you will be notified of such activity.
* Improvement : Plugin compatibility for new and improved Dashboard
* Improvement : Files and database can be included / excluded before initiating first backup.
* Improvement :  File and database size will be shown for included items.
* Improvement : Sitemap.xml and Favicon.ico files will be backed up along with default files.
* Fix : SSL communication issues with cURL has been fixed.
* Fix : The server would ping the sites for backup even after being deactivated.
* Fix : WP-Content folder was excluded by default in certain scenarios.
* Fix : Backups would get stuck midway due to conflict with some excluded files.

= 1.4.5 =
*Release Date - 16 Sep 2016*

* Fix : Before scheduling the backup time and timezone, the backup would get started on Google Drive and Dropbox.

= 1.4.4 =
*Release Date - 16 Sep 2016*

* Improvement : Plugin will now support Network Admin mode.
* Improvement : Restore mechanism will now ignore warnings.
* Fix : Exclude / Include option did not work well for new files in certain scenarios.
* Fix : There were memory leaks while showing the backup status.
* Fix : Schedule backup was not happening in certain scenarios.

= 1.4.3 =
*Release Date - 6 Sep 2016*

* Fix : The default excluded files would get backed up in certain scenarios.

= 1.4.2 =
*Release Date - 6 Sep 2016*

* Improvement : Customized wp-content folder is now supported.
* Improvement : Backup stuck during a scheduled backup will be smartly removed.
* Improvement : Performance enhancement for Exclude/Include file and folder operations.
* Fix : Exclude/Include file tree would get stuck when the file has insufficient permissions.
* Fix : Backup would freeze when a table is removed from the database during the backup.
* Fix : When Visiting Site from WP Dashboard, checking backup status indicator remains static.
* Fix : Certain files when specifically included in the backup do not get backed up.

= 1.4.1 =
*Release Date - 24 Aug 2016*

* Improvement : Notification is shown when the minimum PHP requirement is not met for cloud repositories.
* Fix : The status bar would show incorrect backup tables count in certain scenarios.
* Fix : Few tables were missing from the database backup in earlier versions.
* Fix : If a new table gets added to the database during backup, the backup would get stuck midway.
* Fix : Google drive would conflict with other plugins using the same files / library as WPTC.

= 1.4.0 =
*Release Date - 18 Aug 2016*

* Improvement : Plugin-Server communication method had been changes to make backups reliable than before.
* Improvement : Backups stuck midway will be cleared in the subsequent scheduled backup call.
* Improvement : If the site goes unreachable during backup, it can be manually resumed from the Settings menu.
* Improvement : Plugin authentication mechanism has been improved.
* Fix : Bridge restore file would show incorrect backup date in earlier versions.

= 1.3.1 =
*Release Date - 9 Aug 2016*

* Improvement : Metadata is backed up only when the plugin needs it.
* Improvement : Schedule backup time zone will be auto-populated from time zone set on the WordPress site for new users.
* Improvement : Notice message for communication failure between Plugin and server has been updated.
* Improvement : Security patches have been applied for sites backed up to Amazon S3 storage.
* Fix : Activity log was showing incorrect schedule time for backups.
* Fix : During a backup, the plugin would check for database changes before every call which is unwanted.
* Fix : Files having size greater than 2GB could not be uploaded to Google Drive in certain scenarios.

= 1.3.0 =
*Release Date - 4 Aug 2016*

* Feature: File Tree view is now available when Including & Excluding files and folders.
* Feature: You can now include and exclude WordPress tables from backups.
* Improvement : The plugin will backup only the WordPress core files, folders & tables by default.

= 1.2.0 =
*Release Date - 22 Jul 2016*

* Improvement : Restore operation will be retried when some exceptions occur.
* Improvement : Plugin and server communication has been improved for restore operation.
* Improvement : Memory optimization is done to avoid memory leak during restore.
* Fix : Dropbox had compatibility issues with servers running PHP 7.
* Fix : Plugin authorization with Dropbox was failing intermittently.
* Fix : Scheduled backups taken on a particular day would show the previous day on the calendar.
* Fix : Scheduled and manual backups were not working after a failed restore operation.
* Fix : Backups would get stuck on 100% due to file permission issues on the plugin.
* Fix : Exclusion of files and folders was not working on some servers.
* Fix : Restore process popup would throw a 404 error out of nowhere.

= 1.1.4 =
*Release Date - 15 Jul 2016*

* Fix : Incorrect data was being sent during the restore process.

= 1.1.3 =
*Release Date - 15 Jul 2016*

* Improvement : Communication between plugin and the server has been greatly improved.
* Fix : Email gets triggered twice on certain scenarios
* Fix : If a backup is stopped manually before completion, a first backup completed email is triggered
* Fix : A backup is stopped when any changes are made on the settings menu.

= 1.1.2 =
*Release Date - 5 Jul 2016*

* Fix : Progress would get stalled in the middle of a backup.

= 1.1.1 =
*Release Date - 4 Jul 2016*

* Improvement: Cron code has been changed to make Cron more reliable than before.

= 1.1.0 =
*Release Date - 29 Jun 2016*

* Improvement: Backup time for Google Drive has been greatly reduced.
* Fix: Earlier version had a few SQL backup bugs.

= 1.0.0 =
*Release Date - 23 Jun 2016*

* Improvement: Migrated from WordPress cron service to custom WP Time Capsule cron service.
* Improvement: Confirmation pop up before performing restore.
* Improvement: Backup Stability has been improved.
* Improvement: Initial Setup has been improved.
* Improvement: Minor UI changes, bug fixes and improvements

= 1.0.0RC3 =
*Release Date - 9 Jun 2016*

* Improvement: Smarter system implemented for AJAX calls to reduce frequent calls.
* Improvement: Lazy load implemented for the activity log and overall loading time improved.
* Improvement: Status bar will now show the last successful backup time.
* Improvement: During backup, the status bar will now show the file count in process.
* Improvement: Users will now be able to exclude files and schedule backup time on initial setup itself.
* Improvement: Error messages shown in the Amazon S3 UI have been improved.
* Improvement: Minor bugs have been fixed to improve the overall UI.
* Fix: Existing backup data would not get deleted when authorizing a different account within the same cloud service.
* Fix: Backups would stop midway due to fatal errors on other third party plugins.
* Fix: During backup, the status bar was showing incorrect data during DB sync for some users.
* Fix: Error messages were not displayed during restore via bridge file.
* Fix: The bridge file restore process would get stuck when temp folder was missing.
* Fix: Backups older than 30 days were not automatically deleted.

= 1.0.0RC2 =
*Release Date - 19 May 2016*

* Fix: Error messages populated by Amazon S3 would show multiple times.
* Fix: Scheduled backups would get stuck randomly when the WP site is not kept open.

= 1.0.0RC1 =
*Release Date - 13 May 2016*

* Improvement: Scheduled backup is the default backup option replacing auto-backup.
* Improvement: File and folder exclusions are possible during backup.
* Improvement: Lazy load has been implemented in the Calendar View.
* Improvement: Restore Method has been optimized for low memory consumption.
* Improvement: Cron is faster than before with reduced interval times.
* Improvement: Users will be notified of errors encountered during backups via email.
* Improvement: Backups disconnected midway can be continued from the same point using a link sent via email.
* Improvement: Progress details for DB is shown during DB backups instead of overall progress.
* Improvement: Metadata of backups is now stored in the Cloud.
* Improvement: Google Oauth is improved, Users can use their email to connect to Google Drive.
* Fix: Minor bug fixes to improve speed and accuracy of backups.

= 1.0.0beta5.2 =
*Release Date - 28 Apr 2016*

* Fix: A Memory leak was found.

= 1.0.0beta5.1 =
*Release Date - 25 Apr 2016*

* Improvement: Auto backup will not scan all files, instead it will look for Media file changes, Database changes and Plugin & Theme changes.
* Improvement: Backup Method optimized for low memory consumption.
* Improvement: Backup process now exculdes few other backup plugins' backups.

= 1.0.0beta4.4 =
*Release Date - 23 Feb 2016*

* Fix: Auto backup halted if the plugin was updated when a backup was running.

= 1.0.0beta4.3 =
*Release Date - 22 Feb 2016*

* Improvement: CPU usage optimized.
* Fix: More than one backup process was triggered by cron at the same time.
* Fix: Backup calls were running for a long period without closing on timeout.

= 1.0.0beta4.2 =
*Release Date - 18 Feb 2016*

* Fix: "Expecting a file upload" error for PHP 5.6 and above.

= 1.0.0beta4.1 =
*Release Date - 16 Feb 2016*

* Fix: Specified key was too long bug.

= 1.0.0beta4 =
*Release Date - 12 Feb 2016*

* Improvement: Support for Amazon S3.
* Improvement: We have used WordPress default collation for creating new db tables.

= 1.0.0beta3.1 =
*Release Date - 5 Feb 2016*

* Fix: Stopping a running backup by clicking Stop Backup link in Settings page disabled the cron.
* Fix: Removing a site from account page removed the cron from the server, but didn't get re-enabled when logging in the plugin.
* Fix: When logged out and in from the plugin, cron got registered two or more times.
* Fix: Google Drive error didn't get removed.
* Improvement: Improved calendar view.
* Improvement: Displaying last sync'd time when hovering over "Backups are up to date" in admin bar.

= 1.0.0beta3 =
*Release Date - 1 Feb 2016*

* Improvement: Support for Google Drive.
* Improvement: Backups are now fully automated. All changes are continuously backed up.
* Improvement: During the initial backup, the plugin screen is not blocked now. You can close the tab. We will email you once the backup is done.
* Fix: Bug fixes

= 1.0.0beta2 =
*Release Date - 13 May 2015*

* Improvement: UI improvements
* Fix: Bug fixes

= 1.0.0beta1 =
*Release Date - 27 Apr 2015*

* Beta release

= 1.0.0alpha5 =
*Release Date - 1 Jan 2015*

* Feature: Backup scheduling added
* Improvement: Report sending added
* Improvement: Activity log added
* Improvement: UI improvements
* Fix: Bug fixes

= 1.0.0alpha4 =
*Release Date - 08 Dec 2014*

* Improvement: Background backup process
* Fix: Bug fixes

= 1.0.0alpha3 =
*Release Date - 11 Nov 2014*

* Initial release.
