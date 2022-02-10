=== Advanced Cron Manager - debug & control ===
Contributors: bracketspace, Kubitomakita
Tags: cron, wp cron, cron jobs, manager, cron manager, crontrol
Requires at least: 3.6
Tested up to: 5.8
Stable tag: 2.4.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

View, pause, remove, edit and add WP Cron events and schedules.

== Description ==

With Advanced Cron Manager you can manage WP Cron events:

* view all registered events
* *search* events
* execute manually any event
* add new events
* *pause* events
* delete (unschedule) events
* ready-to-copy-paste PHP implementation for each event
* bulk actions on events
* block WP Cron spawning and get instructions how to set Server Scheduler

and schedules:

* view all registered schedules
* add new schedules
* edit schedules
* remove schedules

Plugin use AJAX request so you'll need enabled Javascript in order to use it.

= Advanced Cron Manager PRO =

PRO version includes

* Cron Logger - log cron's execution times easily
* Events rescheduling - change event next execution date to control server load
* Error catcher - catch cron task's fatal errors and get them displayed in the log
* Performance stats - see how much time and memory particular event took
* Debug tool - log any useful informations from Cron callback

[Buy now](https://bracketspace.com/downloads/advanced-cron-manager-pro/ "Advanced Cron Manager PRO")

= Information about WP Cron =

Please remember - after deactivation of this plugin added Schedules will be not available. Added Events will still work.

Important - WordPress Cron is depended on the User. WP Cron fires only on the page visit so it can be inaccurate.

= Custom Development =

BracketSpace - the company behind this plugin provides [custom WordPress plugin development services](https://bracketspace.com/custom-development/). We can create any custom plugin for you.

== Installation ==

Download and install this plugin from Plugins -> Add New admin screen.

Plugin's page sits under Tools menu item.

== Frequently Asked Questions ==

= Tasks and schedules will be still working after plugin deactivation/removal? =

Tasks yes. Schedules no.

= How does the pausing/unpausing work =

When you pause an event it's really unscheduled and stored in the wp_option. If you unpause it, it will be rescheduled. All paused events are rescheduled on plugin uninstall.

= What is the Event hook? =

It's used for action. For example if your hook is hook_name you'll need to add in PHP:
`add_action( 'hook_name', 'function_name' )`

= Does this plugin allow to add PHP to events like in WP Crontrol plugin? =

No. This is not safe. You can, however, copy the sample implementation and paste it into you own plugin or theme's function.php file.

= Can this plugin block WP Cron and help hooking it into Server Cron like WP-Cron Control plugin? =

Yes, but WP-Cron Control is quite old and it's tactics is not needed anymore. Advanced Cron Manager can disable spawning WP Cron on site visit and will give you useful informations about added Server Cron task.

= Can you create a plugin for me? =

Yes! We're offering a [custom plugin development](https://bracketspace.com/custom-development/) services. Feel free to contact us to find out how we can help you.

== Screenshots ==

1. Plugin control panel
2. Adding, editing and removing Schedule
3. Adding Event
4. Event actions
5. Search and bulk actions
6. Server Scheduler section

== Changelog ==

= 2.4.1 =
* [Fixed] Composer dev dependencies are now not bundled in the production package
* [Fixed] "nul" typo causing fatal errors on newer PHP versions
* [Changed] Updated composer dependencies

= 2.4.0 =
* [Added] Event columns sorting
* [Fixed] Cron hook sanitizer doesn't allow usage of slashes
* [Fixed] Update list of protected events
* [Fixed] Preserve search when events table rerender
* [Changed] Don't allow to pause protected events

= 2.3.10 =
* [Fixed] A "Trying to get property 'hash' of non-object" warning fix when executed event doesn't exist anymore
* [Added] Action for adding own event row actions

= 2.3.9 =
* [Fixed] "non-numeric value encountered" error with event arguments
* [Fixed] Fatal error when even argument was an object. Now, class name is displayed
* [Changed] Now when event is executed manually, DOING_CRON constant is defined

= 2.3.8 =
* [Fixed] Events table width
* [Changed] ACF PRO download link

= 2.3.7 =
* [Fixed] WordPress <4.7 compatibility

= 2.3.6 =
* [Fixed] PHP 7.2 compatibility

= 2.3.5 =
* [Fixed] Fatal error when event argument was an object
* [Fixed] Notices
* [Fixed] Arguments list in the events table
* [Changed] Composer libraries updated
* [Changed] Node packages updated
* [Added] Plugin action link on Plugins table

= 2.3.4
* [Fixed] wp-hooks script handle, causing the page to not load plugin's JavaScript

= 2.3.3
* [Changed] JavaScript hooks library which was conflicting with Gutenberg

= 2.3.2 =
* [Fixed] i18n of Apply button
* [Added] Scheduled and Uncheduled actions for events

= 2.3.1 =
* [Fixed] Array to string conversion error fix for event arguments
* [Fixed] Missing old plugin file error fix
* [Added] Notification plugin promo box

= 2.3.0 =
* [Changed] Proper compatibility with PHP 5.3
* [Changed] Updated composer libraries
* [Changed] Dice Container is not longer used
* [Fixed] Problem with nested Composer environment, thanks to @v_decadence
* [Fixed] Assets vendor directory

= 2.2.3 =
* [Added] Compatibility with PHP 5.3 with Dice library
* [Changed] PHP 5.6 requirement to PHP 5.3
* [Changed] Moved Container to separate file

= 2.2.2 =
* [Changed] Minimum PHP version to 5.6

= 2.2.1 =
* [Fixed] Delete file where DI52 container was still used

= 2.2.0 =
* [Changed] Updated composer libraries
* [Changed] Changed DI52 Container to Dice in own namespace
* [Added] Server Scheduler section with information about hooking the WP Cron to server scheduler

= 2.1.2 =
* [Changed] Schedules can be registered in the system with 0s interval, thanks to @barryalbert

= 2.1.1 =
* [Changed] Requirements lib has been moved to Composer

= 2.1.0 =
* [Changed] Utilities classes has been moved to separate composer libraries
* [Changed] Requirements checks
* [Changed] date() function to date_i18n()
* [Fixed] Deprecated function has been updated
* [Fixed] Translations. There was few missing gettext functions
* [Added] Schedules dropdown in add new event form now includes schedule's slug
* [Added] Sanitization of Schedule and Event slugs in Add forms

= 2.0.0 =
* [Changed] Pretty much everything. There's new interface and code base.
* [Added] Events search
* [Added] Ability to pause/unpause events
* [Added] Ability to edit schedules
* [Added] Example PHP implementation for each event (action and callback function)
* [Added] Bulk actions

= 1.5 =
* [Fixed] Manual execution of task which is giving an errors

= 1.4.4 =
* [Added] French translation thanks to Laurent Naudier
* [Changed] Promo box from Popslide plugin to Notification

= 1.4.3 =
* Metabox promo update

= 1.4.1 =
* Fixed executing when args are provided

= 1.4 =
* Added hooks for PRO version
* Removed PHP closing tags
* Added settings widget

= 1.3.2 =
* Fixed arguments passed to the action on AJAX request

= 1.3 =
* Added promo metabox
* WordPress 4.1 comatybility check
* Updated translation
* Added plugin icon

= 1.2 =
* Readme improvement
* Added execution button
* Removed debug alert

= 1.1 =
* Fixed Schedules list from other plugins

= 1.0 =
* Plugin relase

== Upgrade Notice ==

= 2.0.0 =
* Plugin has been rebuilt from a scratch.
* If you are using Advanced Cron Manager PRO please upgrade it too!
* It's needed to reactivate the plugin!

= 1.2 =
Removed debug alert and added execution button

= 1.1 =
Fixed Schedules list from other plugins

= 1.0 =
Plugin relase
