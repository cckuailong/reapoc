=== My Calendar ===
Contributors: joedolson
Donate link: http://www.joedolson.com/donate/
Tags: calendar, dates, times, event, events, scheduling, schedule, event manager, event calendar, class, concert, venue, location, box office, tickets, registration
Requires at least: 4.4
Tested up to: 5.7
Requires PHP: 5.6
Text domain: my-calendar
Stable tag: 3.2.17
License: GPLv2 or later

Accessible WordPress event calendar plugin. Show events from multiple calendars on pages, in posts, or in widgets.

== Description ==

My Calendar does WordPress event management with richly customizable ways to display events. The plug-in supports individual event calendars within WordPress Multisite, multiple calendars displayed by categories, locations or author, or simple lists of upcoming events.

Easy to use for anybody, My Calendar provides enormous flexibility for designers and developers needing a custom calendar. My Calendar is built with accessibility in mind, so all your users can get equal access and experience in your calendar.

= Premium Event Management =
Looking for more? [Buy My Calendar Pro](https://www.joedolson.com/my-calendar/pro/), the premium extension for My Calendar to add support for user-submitted events, integration between posting and event creation, and import events from outside sources.

= Selling event tickets? =
Do you sell tickets for your events? [Use My Tickets](https://wordpress.org/plugins/my-tickets/) and sell tickets for My Calendar events. Set prices, ticket availability, and sell multiple events at the same time using My Tickets.

= Features: =

*	Calendar grid and list views of events
*	Monthly, weekly, or daily view.
*	Mini-calendar for compact displays (as widget or as shortcode)
*	Widgets: today's events, upcoming events, compact calendar, event search
*	Custom templates for event output
*	Limit views by categories, location, author, or host
*	Editable CSS styles and JavaScript behaviors
*	Schedule recurring events.
*	Edit single occurrences of recurring events
*	Rich permissions handling to restrict access to parts of My Calendar
*	Email notification to administrator when events are scheduled or reserved
*	Post to Twitter when events are created (using [WP to Twitter](http://wordpress.org/extend/plugins/wp-to-twitter/))
*	Managing locations
*	Fetch events from a remote database. (Sharing events in a network of sites.)
*	Multisite-friendly
*	Integrated help page
*	Shortcode Generator to create customized views of My Calendar

= What's in My Calendar Pro? =

* Let your site visitors submit events to your site (pay to post or free!).
* Let logged-in users edit their events from the front-end.
* Create events when you publish a blog post
* Publish a blog post when you create an event
* Advanced search features
* Responsive mode
* Import events from .ics or .csv formats via file or URL.
* REST API support for sharing events between multiple sites.

= Translations =

Visit [Wordpress Translations](https://translate.wordpress.org/projects/wp-plugins/my-calendar) to check progress or contribute to your language.

Translating my plug-ins is always appreciated. Visit <a href="https://translate.wordpress.org/projects/wp-plugins/my-calendar">WordPress translations</a> to help get your language to 100%!

== Installation ==

1. Upload the `/my-calendar/` directory into your WordPress plugins directory.

2. Activate the plugin on your WordPress plugins page

3. Configure My Calendar using the settings pages in the admin panel:

   My Calendar -> Add New Event
   My Calendar -> Manage Events
   My Calendar -> Event Groups
   My Calendar -> Add New Location
   My Calendar -> Manage Locations
   My Calendar -> Manage Categories
   My Calendar -> Style Editor
   My Calendar -> Script Manager
   My Calendar -> Template Editor
   My Calendar -> Settings
   My Calendar -> Help

4. Visit My Calendar -> Help for assistance with shortcode options or widget configuration.

== Changelog ==

= 3.2.17 =

* Bug fix: Add parameter for required fields handling to ignore during imports.
* Add filter handling calendar URLs when using Polylang or WPML.

= 3.2.16 =

* Bug fix: Check for undefined objects in localization, not for undefined object props.
* Change: Set parameter for location autocomplete switchover to 50 instead of 25 locations.
* Change: Tweak directory removal process slightly.

= 3.2.15 =

* Bug fix: Hide event details section if no fields are visible for section.
* Bug fix: Update localization to correct usage of l10n parameter.
* Bug fix: Location AJAX query executed function that only existed in Pro.

= 3.2.14 =

* Bug fixes: Misc. type casting issues.
* Add filters `mc_filter_events` to filter results of main event queries.
* Add $args array to `mc_searched_events` filter parameters.
* Avoid running My Calendar core functionality through My Calendar's own hooks.
* When using REST API, variables are not submitted in a POST query.
* [Performance] Move custom location query into object creation to reduce DB calls.
* Use try/catch in mc-ajax.js to handle case where href does not contain a full URL.
* Autocomplete support for locations in admin.
* Reset select elements in My Calendar nav to inline.
* Minor refactoring in settings pages.

= 3.2.13 =

* Bug fix: Using embed targets is more complicated than expected; disable by default. Enable with 'mc_use_embed_targets' filter.
* Bug fix: Strip embed from parameters when building links (when embed target enabled.)

= 3.2.12 =

* Bug fix: Don't use embed target link when AJAX disabled.
* Improvement: Add AJAX navigation into browser history & address bar.

= 3.2.11 =

* Bug fix: switching to week view display broken.
* Bug fix: links to template help pointed to old location for help.
* Bug fix: AJAX nav pulled height from first rendered calendar, not current navigating calendar.
* Change: filter to pass custom notices for front end submissions and editing.
* Remove fallback function for is_ssl()
* Improve conflicting event errors when the conflicting event is still unpublished.
* Add custom template to pass a calendar that's embeddable via iframe.
* Bug fix: Multisite environments need to use navigation on current site, not from remote site.

= 3.2.10 =

* Change: Fallback text should have a stylable wrapper.
* Bug fix: Missing translatable string.
* Bug fix: When multiple categories selected, events in more than one category would appear multiple times.
* Bug fix: Missing space in MySQL filters in event manager.
* Bug fix: PHP Notice thrown in location manager.
* Bug fix: Add note to open events link field if no URI configured.
* Layout fix: Ensure there's always a space between date & time.

= 3.2.9 =

* Bug fix: Additional of required fields testing erased error messages generated prior to required fields testing.
* Bug fix: If an individual occurrence title is edited, event permalinks show the single change on all events.
* Bug fix: Prev/next event links don't include unique event IDs.
* Bug fix: Remove irrelevant arguments from prev/next event link generation.
* Bug fix: Ignore templates if no data passed.

= 3.2.8 =

* Bug fix: Extraneous screen-reader-text summary generated in event views.
* Bug fix: Fixes to missing parameters in Schema.org microdata.
* Bug fix: Incorrect type comparison caused custom templates not to render in single event view.
* New feature: Default location.

= 3.2.7 =

* Bug fix: Prevent events from being created without categories.
* Bug fix: Ensure category relationships are deleted when related events are deleted.
* Add handling for seeing & managing events that are invalid.
* Add styles for invalid rows.

= 3.2.6 =

* Added filter to change date format on calendar grid.
* New filter for modifying user selection output.
* Bug fix: only check for get_magic_quotes_gpc() if below PHP 7.4
* Bug fix: invalid query in mc_get_locations() if arguments passed as array.

= 3.2.5 =

* Bug fix: CSV exported text fields contained newline characters.

= 3.2.4 =

* Bug fix: Permissions issue caused by variable type mismatch.

= 3.2.3 =

* Bug fix: 3.2.2 created multiple post types with the same slug, triggering 404 errors.
* Bug fix: Templates could return the name of the template if template empty/missing.

= 3.2.2 =

* Bug fix: Curly brace offset access deprecated
* Bug fix: Make next/prev post link arguments optional.
* Bug fix: Template queries could return an empty template.
* Change: Remove trashed events from default events list.

= 3.2.1 =

* PHP Notice: undefined variable.
* Bug fix: screen options not saving.
* Bug fix: Accidental auto-assigning of first category to events when editing.

= 3.2.0 =

* Auto-toggle admin time format if display time format set to European format.
* Show API endpoint when API enabled.
* Add alternate alias for API endpoint.
* Add style variables with category colors to style output.
* Add color output icon with CSS variables in style editor.
* Add new default stylesheet: Twentytwenty.css
* Move permalink setting to general settings panel.
* Change event timestamps to use a real UTC timestamp for reference.
* Switch from using date() to gmdate().
* Update Pickadate to 3.6.4. Resolves some bugs, but introduces an accessibility issue.
    * Customizations to Pickadate 3.6.4 to repair accessibility
    * don't move focus to picker
    * add 'close' button to time picker.
    * Switch Pickadate to classic theme (modified).
* Improvements to output code layout.
* Eliminate empty HTML wrappers in content.
* New filter: mc_get_users. Use custom arguments to get users.
* New filters: mc_header_navigation, mc_footer_navigation
* New template tags: {userstart}, {userend} - date/time in local users timezone.
* Bug fix: Misc. ARIA/id relationships broken.
* Bug fix: remote locations sometimes pulled from local database.
* Bug fix: Long-standing issues in user input settings.
* Bug fix: Don't duplicate .summary values.
* Bug fix: Only render one close button in mini calendar.
* Collapse 'View Calendar' and 'Add Event' adminbar menus into a single menu.
* Remove upgrade path from 2.2.10.
* Remove .mc-event-visible from style output. Unused since 2011.
* Remove numerous deprecated functions.
* Conformance with latest WordPress PHPCS ruleset.

= 3.1.18 =

* Add filters to 'Add to Google Calendar' link: mc_gcal_location & mc_gcal_description
* Add 'nofollow' to links to past events.
* Include recurrence info in post meta box.
* Change limit on adding occurrences from 20 to 40.
* Minor code refactoring.

= 3.1.17 =

* Bug fix: comma misplaced in mc_list_title_title. 
* Provide special handling in cases where multiple categories are enabled but selector is passed a non-array value.

= 3.1.16 =

* Bug fix: restricting styles & JS to specific pages broken by strict type checks.
* New filter: mc_list_title_title

= 3.1.15 =

* Bug fix: List of access features produced empty UL if no results.
* Bug fix: Always produce a class on event accessibility selection list.
* Bug fix: Docs error in describing custom files directory.
* Add: support passing multiple site IDs into the calendar shortcode in multisite.

= 3.1.14 =

* Bug fix: Unescaped event title in HTML output.
* Bug fix: Improper saving of Edit All Categories permissions in user profile.
* New: [my_calendar_next] shortcode. 

= 3.1.13 =

* Bug fix: If plug-in name is translated, script references were broken.
* Bug fix: If no holiday category assigned, Today's Events widget will return empty when category limits set.
* New filter: allow events post type to be made searchable. (Not recommended.)
* New: Support 'search' parameter in shortcode & URL parameters for main view.
* Remove option to disable max contrast category names.

= 3.1.12 =

* Bug fix: User-specific category permissions didn't handle unset (default) values.
* Bug fix: missing row closure element when weekends not displayed.

= 3.1.11 =

* New filter on mc_user_permissions operated on wrong variable.

= 3.1.10 =

* SECURITY FIX: Unauthenticated XSS scripting vulnerability. Update immediately. Thanks to Andreas Hell.
* Support for defining individual categories as having no category icon. 

= 3.1.9 =

* Undefined variable notice.
* Disable Yoast canonical URL output on single events
* Use same time variable in templates & in main layout.
* Using default title template and empty time text, don't display unneeded colon.

= 3.1.8 =

* Bug fix: 'event_begin' is not always a string, so 'mc_event_date' not always registered correctly.
* Update 'sortable' code to be prepared for My Calendar Pro 1.9.0.
* Add 'mc_date_format()' function to get appropriate date format
* Minor settings design changes.

= 3.1.7 =

* Add meta field '_mc_event_date' for use in My Tickets
* Add option to disable output link using an explicit option.
* Change the JS so popups are only attached to links.
* Better UI with custom & deleted occurrences in recurring events.
* Bug fix: sessions should only be started if a search has been performed.

= 3.1.6 =

* Bug fix: If a category name was blank, it would automatically be filtered to by upcoming events lists.
* Bug fix: Show print view as list if main view is list.
* Bug fix: Strip HTML tags from aria-label attributes
* Bug fix: .details needs position: relative in twentyfifteen stylesheet
* Adjust tested to value to 5.1

= 3.1.5 =

* Bug fix: PHP error checking broken due to session creation

= 3.1.4 =

* Bug fix: typo in category string parameter for ical output

= 3.1.3 =

* New filter: 'mc_list_titles_separator'
* Bug fix: Help support data not displayed.
* Override content overflow in Twentynineteen
* Add support for iCal format in API exports

= 3.1.2 =

* Bug fix: Twentyeighteen styles missing from template directory
* Bug fix: Declare width on th as well as td
* Bug fix: optgroup close element broken
* Bug fix: Shortcode generator fixes.
* Bug fix: Handle case where hidden categories are not an array in event manager.
* Bug fix: If template tag value contains only whitespace, do not render before & after attributes.
* Bug fix: Handle form restrictions in KSES introduced in WP 5.0.1.
* Bug fix: check whether PHP sessions are enabled before attempting to start
* Change: Only render export links in search results if enabled in main settings
* Change: [UI] Move stylesheet selector into sidebar
* Change: Allow target attribute on links.
* Change: Add label to links that open in new tab.

= 3.1.1 =

* Bug fix: unspamming event_ID passed incorrect variable name
* Bug fix: Don't run spam check on users with mc_add_event 
* Bug fix: Users with mc_add_event should not be able to trash other's events.
* Bug fix: Refine permissions; add mc_publish_events allowing users to publish own events without access to others
* Bug fix: Refine permissions; don't display links that users can't use.

= 3.1.0 =

* Add feature (by Josef Fällman): Print & export view for search results.
* New filter: mcs_check_conflicts (impacts Pro only)
* Bug fix: Fix issue causing duplication in some views.
* Bug fix: Time format should be filtered in initial edit view.
* Bug fix: Category relationships not retained when Group editing applied.
* Bug fix: aria-describedby ID mismatch.

= Future Changes =

* Refactor options storage
* Revise month by day input & calculation methods
* Bug: if save generates error, creates ton of notices. [eliminate $submission object and use single object model]
* Add ability to limit by multiple locations (e.g., view all events in Location 1 & Location 2; only on lvalue)
* JS to delete events from front-end when logged-in
* TODO: delete this instance and all subsequent instances

== Frequently Asked Questions ==

= Hey! Why don't you have any Frequently Asked Questions here! =

Because the majority of users end up on my web site asking for help anyway -- and it's simply more work to maintain two copies. Please visit [my web site FAQ](http://www.joedolson.com/my-calendar/faq/) to read my Frequently Asked Questions!

= This plug-in is complicated. Why won't you help me figure out how to use it? =

I will! But not in person. Take a look at my [documentation website for My Calendar](http://docs.joedolson.com/my-calendar/) or [buy the User's Guide](https://www.joedolson.com/my-calendar/users-guide/) before making your request, and consider [making a donation](https://www.joedolson.com/donate/)!

= Can my visitors or members submit events? =

I've written a premium plug-in that adds this feature: My Calendar Pro. [Buy it today](https://www.joedolson.com/my-calendar/pro/)!

= Is there an advanced search feature? =

The search feature in My Calendar is pretty basic; but [buying My Calendar Pro](https://www.joedolson.com/my-calendar/pro/) gives you a richer search feature, where you can narrow by dates, categories, authors, and more to refine your event search.

== Screenshots ==

1. Monthly Grid View
2. List View
3. Event management page
4. Category management page
5. Settings page
6. Location management
7. Style editing
8. Template editing

== Upgrade Notice ==

* 3.2.0 Major release to conform with new WordPress PHP standards.