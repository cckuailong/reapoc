=== Plugin Name ===
Contributors: ice00, lechab
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F5S5PF4QBWU7E
Tags: stats,statistics,widget,admin,sidebar,visits,visitors,pageview,user,agent,referrer,post,posts,spy,statistiche,ip2nation,country
Requires at least: 3.5
Tested up to: 5.6
Stable Tag: 1.3.5

NewStatPress (Statpress plugin fork) is a real-time plugin to manage the visits' statistics about your blog  (without external web analytics).

== Description ==

NewStatPress is a new version of StatPress that was the first real-time plugin dedicated to the management of statistics about blog visits.

It collects information about visitors, spiders, search keywords, feeds, browsers etc.

Once the plugin NewStatPress has been activated it immediately starts to collect statistics information.
Using NewStatPress you could spy your visitors while they are surfing your blog or check which are the preferred pages, posts and categories.
In the Dashboard menu you will find the NewStatPress page where you could look up the statistics (overview or detailed).
NewStatPress also includes a widget one can possibly add to a sidebar (or easy PHP code if you can't use widgets!).

With the new ajax/javascript usage for variables in widget, the plugin is faster for a user being visit your site even with 1GB or more of database to use! (requires the External API be enabled in NewStatPress>Options>API)

IMPORTANT: all previous versions from 1.0.4 are subject to XSS and SQL injection from an old Statpress routine. You have to use at least version 1.0.6 to avoid security issue.
PLEASE UPDATE always to the latest version available.

= Support =

Check at  http://newstatpress.altervista.org

= What's new? =

Simple adding index to database and changes some data fields for better database storing (from here http://www.poundbangwhack.com/2010/07/03/improve-the-performance-of-the-wordpress-plugin-statpress-and-your-blog/ where some modification comes from)

= Ban IP =

You could ban IP list from stats editing def/banips.dat file.

= DB Table maintenance =

NewStatPress can automatically delete older records to allow the insertion of newer records when limited space is present.
This features is left as original StatPress but it will be replaced by the history data instead.

= External API =

External API are a way to gives the collected informations as a web service using a POST call.
With it you can use (for example) your collected data of Wordpress inside a Drupal site.
The API must be enables by check a flag into the option (by default is disabled) and a private KEY must be entered (you can generate a random one).
This KEY is for authenticate the called as a valid allowed client.
Even if the API is for external usage, it will be used internally for speed up page generation using AJAX, so at some point you will need to activate it to continue to see overview and Details pages.

Actually those are the available commands:

Command | Parameters | Description

* version         <none>        gives the Newstatpress version in use
* dashboard       <none>        gives the Newstatpress dashboard overview table

External API is actually used by Multi-NewStatPress (a software than manages data from multiple installation of NewStatPress in different servers).

If you want to use the API you need to pass to POST those values:

* VAR   the variable for the query (like 'Version')
* KEY   the MD5 of date at minute level plus the key you enter into option (e.g in PHP: md5(gmdate('m-d-y H i').key) )
* PAR   the parameter associated with the VAR
* TYP   the type of result: JSON (default) of HTML

into those url: your_site+"/wp-content/plugins/newstatpress/includes/api/external.php"

= NewStatPress Widget / NewStatPress_Print function =

Widget is customizable. These are the available variables:

* %thistotalvisits% - this page, total visits
* %alltotalvisits% - all page, total visits
* %totalpageviews% - total pages view
* %monthtotalpageviews% - total pages view in the month
* %todaytotalpageviews% -  total pages view today
* %since% - Date of the first hit
* %visits% - Today visits
* %yvisits% - Yesterday visits
* %mvisits% - Month visits
* %wvisits% - Week visits
* %totalvisits% - Total visits
* %os% - Operative system
* %browser% - Browser
* %ip% - IP address
* %visitorsonline% - Counts all online visitors
* %usersonline% - Counts logged online visitors
* %toppost% - The most viewed Post
* %topbrowser% - The most used Browser
* %topos% - The most used O.S.
* %topsearch% - The most used search terms

Now you could add these values everywhere! NewStatPress offers a new PHP function *NewStatPress_Print()*.
* i.e. NewStatPress_Print("%totalvisits% total visits.");

New experimental functions: place this command [NewStatPress: xxx] every were in your Wordpress blog pages and you will have the graph about the xxx function.

Available functions are:
* [NewStatPress: Overview]
* [NewStatPress: Top days]
* [NewStatPress: O.S.]
* [NewStatPress: Browser]
* [NewStatPress: Feeds]
* [NewStatPress: Search Engine]
* [NewStatPress: Search terms]
* [NewStatPress: Top referrer]
* [NewStatPress: Languages]
* [NewStatPress: Spider]
* [NewStatPress: Top Pages]
* [NewStatPress: Top Days - Unique visitors]
* [NewStatPress: Top Days - Pageviews]
* [NewStatPress: Top IPs - Pageviews]

== Installation ==

= Fresh Install =
Upload "newstatpress" directory in wp-content/plugins/ . Then just activate it on your plugin management page.
You are ready!!!

Note: you must disable the original StatPress plugin (or other plugins still based onto StatPress) when activating this, as it still use or now the same table of StatPress for storing data in DB (copy the data to another table will be very space consuming for your site, so it was better to use the same table)

= Update =

* Deactivate NewStatPress plugin (no data lost!)
* Backup ALL your data
* Backup your custom DEFs files
* Override "newstatpress" directory in wp-content/plugins/
* Restore your custom DEFs files
* Re-activate it on your plugin management page
* In the Dashboard click "NewStatPress", then "NewStatPressUpdate" and wait until it will add/update db's content

== Frequently Asked Questions ==

= I've a problem. Where can I get help? =

Check at http://newstatpress.altervista.org

== Screenshots ==

Check at http://newstatpress.altervista.org for more details

1.
2.
3.
4.
5.
6.

== Changelog ==

= 1.3.5 =
*Released date: 2021-01-18*

* Improving on preventing potential SQL injection (part 3)
* Os (+3)

= 1.3.4 =
*Released date: 2021-01-16*

* Improving on preventing potential SQL injection (part 2)
* Browsers (+10)

= 1.3.3 =
*Released date: 2021-01-13*

* Improving on preventing potential SQL injection
* Browsers (+30)

= 1.3.2 =
*Released date: 2019-07-20*

* Fix exportation dates

= 1.3.1 =
*Released date: 2019-05-19*

* Update Hungarian language thanks to Pille
* Remove Warning "Address is not a valid IPv4 or IPv6"
* Browsers (+93), OS (+11)

= 1.3.0 =
*Released date: 2018-05-15*

* Fix some PHP 7.2 Warning
* Add browser (+97), OS (+2)

= 1.2.9 =
*Released date: 2017-10-29*

* Fix some PHP Notice

= 1.2.8 =
*Released date: 2017-10-04*

* Fix Os missing images
* Fix undefined index warning
* Update Hungarian language thanks to Pille
* Fix PHP 7.1 specific error

= 1.2.7 =
*Released date: 2017-07-02*

* Fix %thistotalvisits% javascript
* Fix offsets not stores in option after sanitization
* Fix css wordpress problem

= 1.2.6 =
*Released date: 2017-03-23*

* Fix url sanitization that break page detection

= 1.2.5 =
*Released date: 2017-03-20*

IMPORTANT CRITICAL UPDATE
* Fix Stored cross-site scripting vulnerabilities, find by Han Sahin of www.SumOfPwn.nl @sumofpwn
* Avoid direct access to pages 
* Changing path detections (not support anymore WordPress 2.x or lower)
* Fix widget variable list
* Use javascript+ajax for variables API
* Remove the usage of wp_load
* Complete name conversion
* External API via WP Ajax
* Fix missing 2 spider images
* Use NONCE for ajax call in variables API


= 1.2.4 =
*Released date: 2016-06-18*

* Add user message about overview loading (regarding the external api)
* Fix display tab in Option page
* Update Screenshot in en_EN
* Update Ip2nation definitions (16/16/2016)
* Update fr_FR, it_IT, es_ES translation
* Remove "Creating default object from empty value" warming
* Add error images for an Ajax failure
* Fix sender display bug (email notification)

= 1.2.3 =
*Released date: 2016-06-12*

* Change overview loading: used the external api

= 1.2.2 =
*Released date: 2016-05-28*

* Add "wpversion" into External API
* Add Information DB in tool menu
* Update Screenshot
* Update readme info
* Fix Warning message in Dashboard
* Remove "Empty needle" warning
* Add spider (+1)

= 1.2.1 =
*Released date: 2016-04-25*

* Add Overview Meta-boxes (free ordination + open/close)
* Add OS (+76)
* Add Browsers (+160)
* Add Spiders (+25)
* Fix email notification bug (wrong schedule)
* Fix get_currentuserinfo() deprecated function since WP4.5
* Change img display method of OS & Browser definitions
* Add spiders colored agent
* Add Hide/show spiders function in agent
* Add message to news box when def update
* Add technical document about External API inside "doc" folder

= 1.2.0 =
*Released date: 2016-04-17*

* Fix wrong calculation of statistics variation
* Update 'Notification boxes' behavior
* Add OS (+34)
* Add Browsers (+20)

= 1.1.9 =
*Released date: 2016-03-19*

* Avoid the use of JQuery to prevent conflict with other themes/plugin

= 1.1.8 =
*Released date: 2016-03-20*

* Add 'from' option in email notification tab
* Add option for adding a fixed number of visits for how before use another statistic plugin
* Add pickaday for export tool (https://github.com/dbushell/Pikaday)
* Add filename field for export tool
* Add file extension field for export tool
* Add autosave function for export tool
* Add ressources tab in Credits page
* Fix title with spaces visualization in widget
* Fix wrong display in Credits page
* Update fr_FR and it_IT translation

= 1.1.7 =
*Released date: 2016-02-28*

* Added Icons on database tools
* Update CZ translation thanks to Petr Janda
* Update FR translation
* Fix Export dysfunction in database tools ('right' issue)
* Fix Export dysfunction in database tools ('date' issue)

=  1.1.6 =
*Released date: 23/02/2016*

* Fix email notification bug
* [x] Update IT translation

= 1.1.5 =
*Released date: 10/01/2016*

* Double the length of email address
* Changes hooks for emails to avoid possible conflict with other plugins

= 1.1.4 =
*Released date: 09/02/2016*

* Add email notification options
* Add Notice Message Box
* Add OS (+6), Browsers (+5), Spiders (+197)
* Add language flag and status on 'Credits' page
* Add Activation of sum option for variable %mvisits%, %wvisits% and %totalvisits%
* Add Settings link in extensions page
* Add control routine for old WP version
* Add Internalization for shortcode
* Change IP detection for better results behind proxy
* Change 'Options' Page code : major improvements
* Change 'Credits' Page code : use of json
* Update Spiders images (+3)
* Update locale fr_FR,  it_IT, pl-PL, es_ES
* Fix bug for shortcode [NewStatPress: Top days]
* Fix Memory size : >0.5 Mb free
* Fix HTML cleanup
* Fix Readme.txt

= 1.1.3 =
*Released date: 09/01/2016*

* Fix call to % calculation
* Change dashboard/overview call in External API

= 1.1.2 =
*Released date: 07/01/2016*

* Use new Ajax call in Wordpress for dashboard
* Fix missing row titles in dashboard in External API

= 1.1.1 =
*Released date: 06/01/2016*

* Remove included jQuery script that generate activation problem. Dashboard is now to fix.

= 1.1.0 =
*Released date: 06/01/2016*

* Fix API key calculation
* Fix domain.dat manages inside nsp_visits.php (thanks to Gwss)
* Add %monthtotalpageviews% variable (request by th3no0b)
* Add dashboard generation via external API
* Use full index in Dashboard queries for classical method: super speed up generation
* Fix overview bug in total (thanks to Greg Sydney)

= 1.0.9 =
*Released date: 26/09/2015*

* Adequate for translate.wordpress.org
* Update Readme for External API usage

= 1.0.8 =
*Released date: 19/09/2015*

* add jQuery tabs for credit page
* Updated browser definition
* add 2 spiders logo and add missing browser images
* rewrite initialization to avoid Warning about Undefined variable search_phrase and search engine
* Updated Locale fr_FR, it_IT

= 1.0.7 =
*Release Date - 11/07/2015*

* Fix %mvisits% not giving result
* Add %wvisits% week visits
* Fix capability problems created by https://codex.wordpress.org/Multisite_Network_Administration

= 1.0.6 =
*Release Date - 01/07/2015*

* Close a possible Reflected XSS attack (thanks to James H - g0blin Reserch)
* Avoid MySQL error if erroneous input is given (thanks to James H - g0blin Reserch)

= 1.0.5 =
*Release Date - 30/06/2015*

IMPORTANT CRITICAL UPDATE

* Close a XSS and a SQLI Injection involved IMG tag (thanks to James H - g0blin Reserch)

= 1.0.4 =
*Release Date - 30/06/2015*

IMPORTANT CRITICAL UPDATE

* Close a persistent XSS via HTTP-Header (Referer) (no authentication required) (thanks to Michael Kapfer - HSASec-Team)

= 1.0.3 =
*Release Date - 23/06/2015*

* Fix nsp_DecodeURL code cleanup replacement
* Fix NewStatPress_Print missing after cleanup

= 1.0.2 =
*Release Date - 21/06/2015*

 User interface changes:

* Added API key option in option menu
* Added API activation option in option menu
* Implement external API "version" (gives actual version of NewStatPress)
* Added informations tabs in Tools menu ()
* Updated General tab in Option menu ()
* Updated Widgets title
* Updated IP2nation option menu
* Fixed Dashboard widget overflow

 Core changes:

* Fix the plugin menu view for "subscriber"
* Fix IP2nation database installation bug
* Remove IP2nation download function (to be best conform with WP policy)
* Massive code cleaning to avoid conflict with others plugins
* Added bots (+7, thanks to Nahuel)
* Updated Locale fr_FR, it_IT

= 1.0.1 =
*Release Date - 08/06/2015*

IMPORTANT CRITICAL UPDATE

* Close a SQL injection (Thanks to White Fir Design for discover and communicate). Actually the old Statpress search code seems to be sanitized all.


= 1.0.0 =
*Release Date - 29/05/2015*

* Remove %installed% variable

= 0.9.9 =
*Release Date - 20/05/2015*

Note: IMPORTANT CRITICAL UPDATE

Core changes:

* Close a XSS and a SQL injection and possible other more complex to achieve (thanks to Adrián M. F. for discover and communicate them). Those are inside the search routine from Statpress so ALL previous versions of NewStatPress are vulnerable (and maybe they are present in lot of Statpress based plugin and Statpress itself).
* Fix missing browser images
* Add tools for optimize and repair the Statpress table
* Updated Locale it_IT


= 0.9.8 =
*Release Date - 26/04/2015*

Core changes:

* Fix missing routine for update
* Fix cs_CZ translation

= 0.9.7 =
*Release Date - 11/04/2015*

User interface changes:

* Added New option in Overview Tab : overview stats calculation method (global distinct ip OR sum of each day) (Note: online for month at the moment)
* Added New options in General Tab : add capabilities selection to display menus, options menu need to be administrator by default
* Added New information 'Visitors RSS Feeds' in Overview page
* Updated Locale fr_FR, it_IT, cs_CZ

Core changes:

* Updated OS definition
* Updated Browser definition
* Fixed '3 months Purge' issue

= 0.9.6 =
*Release Date - 21/02/2015*

* New Option page with tabs navigation (jquery)
* Fix Search page link
* Various core fix (global definition, function, plugin page names with nsp_ prefix)
* Various debug fix (deprecated function, unset variable)
* Fix thistotalvisit api call

= 0.9.5 =
*Release Date - 18/02/2015*

* Fix PHP compatibility issue on old versions

= 0.9.4 =
*Release Date - 18/02/2015*

* Update of Widget 'NewStatPress' (code re-writed, add variable informations)
* Fix Overview Table (CSS)
* Add Tool page with tab navigation

Note: do not install if you did not have a recent PHP version

= 0.9.3 =
*Release Date - 17/02/2015*

* Add Visits page with tab navigation
* Add tab navigation in Crédits page
* Add 'Donator' tab in Crédits page
* Add 'visits' and 'options' links in Dashboard widget
* Add CSS style to navbar in Visitors page
* Add colored variation in overview table
* Re-writed Overview function
* Fix Duplicate INDEX when User database is updated (function rewrited)
* Fix dashboard 'details' dead link
* Fix navbar dead link in visitors page
* Various code fixing
* Api for variables (10x faster to load page with widget)

Note: refresh your browser cache to take the new CSS settings else if you use javascript in widget where there is a %variable% of newstatpress, please take a look as now it could generate javascript error (neested code).

= 0.9.2 =
*Release Date - 09/02/2015*

* CSS fix :  Overview fix and wp_enqueue_style compatibility fix

= 0.9.1 =
*Release Date - 08/02/2015*

* Fix PHP to be compatible with a old version (all 0.8.9 changes are now activated)

= 0.9.0 =
*Release Date - 07/02/2015*

* Revert modification to 0.8.8 to fix PHP issue for old versions

= 0.8.9 =
*Release Date - 07/02/2015*

* Added Ip2nation download function in option page
* Added plugin homepage link, news feeds link, bouton donation in credit page
* Added variables %yvisits% (yesterday visits) %mvisits% (month visits)
* Added CSS style to stylesheet (./css/style.css), partially done
  *  remove page
  *  update page
  *  credit page
* Optimization of the option page
* Optimization of the credit page
* Optimization of the export page
* Optimization of the remove page
* Optimization of the database update page
* Fixed 'selected sub-menu' bug
* Fixed wrong path to update IP2nation when database is updated (/includes)
* Fixed 5 bots, add 13 new bots
* Update locate : Italian and French

NOTE: not install this version if you have not a recent PHP version. Attend the new 0.9.1 instead.

= 0.8.8 =
*Release Date - 17/01/2015*

* Optimize %alltotalvisits% query (up to 5X faster)
* Optimize %visitorsonline% query (uses more indexes)
* Optimize %usersonline% query () (uses more indexes)

= 0.8.7 =
*Release Date - 21/12/2014*

* Add Browser (+1)
* Add OS (+5)

= 0.8.6 =
*Release Date - 20/09/2014*

* Replace obsolete get_settings() to get_option() (thanks to kjmtsh)
* Add empty check of the query results (thanks to kjmtsh)
* Fix of the query string for dbDelta() function (thanks to kjmtsh)

= 0.8.5 =
*Release Date - 03/08/2014*

* Add Browser (+10)
* Increase referrer from 250 to 512 chars

= 0.8.4 =
*Release Date - 13/07/2014*

* Spy menu reorganization (thanks to Alphonse PHILIPPE)
* Update French translation (thanks to Alphonse PHILIPPE)
* Update Italian translation

= 0.8.3 =
*Release Date - 11/06/2014*

* Remove greetings link as new Wordpress policy

= 0.8.2 =
*Release Date - 07/06/2014*

* Add Ukrainian translation (thanks to Michael Yunat)

= 0.8.1 =
*Release Date - 25/05/2014*

* Add Greek translation (thanks to Boulis Antoniou)

= 0.8.0 =
*Release Date - 17/04/2014*

* Fix spy/new spy/spy bot menu
* Fix 4 missing details
* Replace mysql_real_escape_string with esc_sql for WP 3.9

= 0.7.9 =
*Release Date - 05/04/2014*

* Update French translation (thanks to Alphonse PHILIPPE)

= 0.7.8 =
*Release Date - 14/03/2014*

* Fix global definitions

= 0.7.7 =
*Release Date - 14/03/2014*

* Fix $userdata issue if it is empty (thanks to szaleq)
* Fix plugin directory usage when not standard wp installation is used (thanks to szaleq)
* Add 'Target' to be translated (thanks to ALPPH)
* Add OS (+9), Browser (+1)

= 0.7.6 =
*Release Date - 11/01/2014*

* Change target formula to use the true month's days duration and computes it according even with hours/minutes (thanks to Capitalist)

= 0.7.5 =
*Release Date - 27/12/2013*

* Fix possible security issue (backported from statpress)
* Resolve /? problem for permalink in links

= 0.7.4 =
*Release Date - 01/10/2013*

* Fix POST request for flag API services that needs GET (Thanks to Giuseppe Chiesa)

= 0.7.3 =
*Release Date - 22/09/2013*

* Fix maxxday missing declaration

= 0.7.2 =
*Release Date - 15/09/2013*

* Add option to prune only the spiders
* Add remove menu for deleting all stored data

= 0.7.1 =
*Release Date - 17/08/2013*

* Add Os (+6), Browser (+14)

= 0.7.0 =
*Release Date - 20/07/2013*

* Make tables compatible with qTransalte plugin
* Fix bug id 390 (Graph Legend includes + Sign)

= 0.6.9 =
*Release Date - 30/06/2013*

* Remove old (unused) chart code that was present over the new Google Api

= 0.6.8 =
*Release Date - 29/06/2013*

* Insert timeout in reading the total number of newstatpress installation if you use %installed%

= 0.6.7 =
*Release Date - 29/06/2013*

* Fix not resolve completly, so remove support link

= 0.6.6 =
*Release Date - 29/06/2013*

* Try to fix bug-id 423/434 (needs PHP5)

= 0.6.5 =
*Release Date - 28/05/2013*

* Mask potential error in reading of installed plugin
* Activate installed registration when in admin page

= 0.6.4 =
*Release Date - 26/05/2013*

* Add %installed% that give the number of installed plugin (it is anonymous and experimental,
  read the note)

= 0.6.3 =
*Release Date - 20/05/2013*

* Fix possible warning and line feeds

= 0.6.2 =
*Release Date - 20/05/2013*

* Add missing 2 spider images
* Make configurable the number of elements in summary
* Add random “support” link to all search robots (read the agreement note before updating)

= 0.6.1 =
*Release Date - 04/05/2013*

* Fix isset for referrer

= 0.6.0 =
*Release Date - 02/05/2013*

* Fix missing $ in referrer

= 0.5.9 =
*Release Date - 30/04/2013*

* Avoid possible problem with refferer (use update for sync the old data)

= 0.5.8 =
*Release Date - 27/04/2013*

* Remove Undefined offset in referrer if debug is on
* Add %todaytotalpageviews% - total pages view today
* Add Os (+3)

= 0.5.7 =
*Release Date - 16/03/2013*

* Porting the 3D charts to the new Google API: Piechart.
  Chart is rendered into an iframe with SVG or VML.
  See http://newstatpress.altervista.org/?p=381 for an example

= 0.5.6 =
*Release Date - 14/03/2013*

* Use .html instead of .frame as some browsers did not interpret it correctly

= 0.5.5 =
*Release Date - 14/03/2013*

* Porting the GeoMap chart to the new Google API: Geochart.
  Map is rendered into an iframe with SVG or VML.
  See http://newstatpress.altervista.org/?p=373 for an example

= 0.5.4 =
*Release Date - 02/03/2013*

* Add Hungarian translation (Thanks to Peter Bago)

= 0.5.3 =
*Release Date - 15/02/2013*

* Fix collision of permalinksEnabled with statpress-visitors
* Increase size from 50 to 250 into details visualization
* New Browser (+17),New OS (+2), fix bug id 315

= 0.5.2 =
*Release Date - 12/02/2013*

* Fix inherited bug about CDIR comparison in blocking IP function
* Add %topsearch% for getting the top search term (it implements partially the tracker ID 297 and 243)
* Fix bug ID 299
* Add missing yahoo feedseacker spider image

= 0.5.1 =
*Release Date - 13/01/2013*

* Add Slovak translation  (thanks to Branco - WebHostingGeeks.com http://webhostinggeeks.com/blog/)
* Fix mising msn spider images (thanks to Christian)

= 0.5.0 =
*Release Date - 29/12/2012*

* Add searchengine (+1)

= 0.4.9 =
*Release Date - 27/12/2012*

* Show graphs under the table to stay into column (Google charts will be updated to SVG graphics soon)

= 0.4.8 =
*Release Date - 09/12/2012*

* Add browser (+3), OS (+1)

= 0.4.7 =
*Release Date - 02/11/2012*

* Trace even IPv6 address (note: look at http://newstatpress.altervista.org/?p=261 for more details before apply this update)
* Remove some Strict Standards PHP warnings (Only variables should be passed by reference in...)
* Remove a Notice (Trying to get property of non-object in...)
* Avoid use of $_SERVER['SCRIPT_NAME'] as no all server handle it correctly (tracker id 258)

= 0.4.6 =
*Release Date - 21/10/2012*

* Add browser (+3), OS (+1)

= 0.4.5 =
*Release Date - 23/09/2012*

* Update Russian translation (thanks to godOFslaves)
* update Italian translation

= 0.4.4 =
*Release Date - 01/09/2012*

* Add browser (+2)
* Fix spider images (bug id 236 and 237)

= 0.4.3 =
*Release Date - 22/07/2012*

* Add spiders images (from statpress-visitors)
* Initial porting of spy bot (from statpress-visitors)
* Add browser (+2)

= 0.4.2 =
*Release Date - 15/07/2012*

* Update Simplified Chinese translation (thanks to Christopher Meng)
* Add tab delimiter for export function (thanks to Ruud van der Veen)

= 0.4.1 =
*Release Date - 12/07/2012*

* Improve control before insert (maybe help for bug id 221)

= 0.4.0 =
*Release Date - 02/07/2012*

* Fix total since date bug (tracker id 216)

= 0.3.9 =
*Release Date - 01/07/2012*

* Add the abilities to not monitoring only a given list of names
 (so, administrators can still monitoring authenticated users) for users that are logged (tracker id 201)
* Update Italian locale

= 0.3.8 =
*Release Date - 10/06/2012*

* Code restructuration
* Add browser (+1)

= 0.3.7 =
*Release Date - 02/06/2012*

* Fix introduced bug with previous function added of image visualization (tracker id 192)

= 0.3.6 =
*Release Date - 01/06/2012*

* Add Overview for [NewStatPress: xxx] command (tracker id 166)

= 0.3.5 =
*Release Date - 16/05/2012*

* Updating French translation (thanks to shilom)
* Fix dashboard and overview translation

= 0.3.4 =
*Release Date - 02/05/2012*

* Add Lithuanian translation (thanks to Vincent G)

= 0.3.3 =
*Release Date - 29/04/2012*

* Updating French translation (thanks to shilom)
* Inserting credits table

= 0.3.2 =
*Release Date - 26/04/2012*

* Add new OS (+1), browsers (+7), spiders (+1)

= 0.3.1 =
*Release Date - 21/04/2012*

* Fix *deprecated* register_sidebar_widget (traker ID 142)
* Fix php warning into dashboard widget

= 0.3.0 =
*Release Date - 14/03/2012*

* Add new browser definitions (+1)
* Create credits menu (to be completed)

= 0.2.9 =
*Release Date - 01/03/2012*

* Fix export action not catched (Tracker ID 146)

= 0.2.8 =
*Release Date - 17/02/2012*

* Fix blog redirector in NewStatPressBlog (Tracker ID 137)
* Fix nation image display in spy (thanks to Maurice Cramer)

= 0.2.7 =
*Release Date - 04/02/2012*

* Replace deprecate PHP eregi function with preg_match
* In spy show local nation images (taken from statpress-visitors)
* New spy function taken from statpress-visitors (for beta testing)
* Use image for newstatpress menu in administration of wordpress (taken from statpress-visitors)

= 0.2.6 =
*Release Date - 01/02/2012*

* Fix missing browser image and IE aligment failure in spy section (thanks to Maurice Cramer)
* Add new browser definitions (+2)

= 0.2.5 =
*Release Date - 28/01/2012*

* Fix total since in overwiew (thanks to Maurice Cramer)
* Add in the option the ability to update in a range of date (migrated from stapress-visitors)

= 0.2.4 =
*Release Date - 18/01/2012*

* Add option for show dashboard widget
* Add dashboard widget (thanks to Maurice Cramer). Look into the blog for details.

= 0.2.3 =
*Release Date - 12/01/2012*

* Fixing charaters after closing tags

= 0.2.2 =
*Release Date - 11/01/2012*

* Add source for Simplified Chinese translation (thanks to Christopher Meng)
* Fix nb_NO over no_NO locale
* Lots of php warnings fixed in source

= 0.2.1 =
*Release Date - 21/12/2011*

* Add new OS (+3), browsers (+6)
* Add Simplified Chinese translation (thanks to Christopher Meng)

= 0.2.0 =
*Release Date - 15/10/2011*

* Add new OS (+44), browsers (+52) and spiders (+71) (from statpress-visitors)

= 0.1.9 =
*Release Date - 10/09/2011*

* make all reports in details to have the number of entries you want
* Add [NewStatPress: xxx] experimental function for having report into wordpress page
* Add %totalpageviews% - total pages view

= 0.1.8 =
*Release Date - 23/06/2011*

* Add option for not track given permalinks (from wp_slimstat)

= 0.1.7 =
*Release Date - 29/05/2011*

* Let Search function to works again (thank to Ladislav)

= 0.1.6 =
*Release Date - 15/05/2011*

* Add option for not track given IPs (from wp_slimstat)
* update Italian translation

= 0.1.5 =
*Release Date - 12/05/2011*

* Open link in new tab/window (thanks to Sisko)
* New displays of data for spy function (thanks to Sisko)
* Added %alltotalvisits%

= 0.1.4 =
*Release Date - 24/04/2011*

* Fix fromDate calculation

= 0.1.3 =
*Release Date - 22/04/2011*

* Reactivate visitors/user online with unix timestamp

= 0.1.2 =
*Release Date - 23/03/2011*

* Add images for new browser
* Better polish translation by Pawel Dworniak
* Separate iPhone/iPad/iPod devices

= 0.1.1 =
*Release Date - 22/03/2011*

* Reactivate translactions
* Add more OS (MacOSX variants, Android)
* Add more Browser (Firefox 4, IE 9)

= 0.1.0 =
*Release Date - 19/03/2011*

* Adds index onto Statpress 1.4.1 table for improve velocity
* Changes data type of some fields for saving space
* Let the images to be visible even for relocated blog
* Makes the update of search engine more quick


== Upgrade Notice ==
