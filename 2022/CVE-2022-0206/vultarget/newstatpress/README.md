# Newstatpress development roadmap

**SOME IDEA**
- [ ] add a page help somewhere (the help for the different %variable%,...)
- [ ] tab construction for overview page
- [ ] irihdate function in double with newstatpress_hdate => to clean
- [ ] Fix number of dot of navbar in Visitors page
- [ ] Add bot rss https://github.com/feedjira/feedjira/tree/master
- [ ] update options use of foreach
- [ ] change the name of widget dashboard
- [ ] Big problem on search function
- [ ] add Select definitions to update
- [ ] Add a 'unique visitors' row in the overview chart
- [ ] Add options to (de)activate single chart/graphs of overview and details
- [ ] Generate external API interfaces
- [ ] Add 'version' in external API
- [ ] OPTIMIZATION : Serialize some NewStatPress options
- [ ] Add dialog box 'database update' when plugin is updated
- [ ] Change graph system on 'details' page
- [ ] Add JS/Jquery on 'overview' page
- [ ] Add database migration -> nsp_database (unique name)
- [ ] Export database > dump sql (bkp)
- [ ] Add option (limit results) for export function (for small memory server)
- [ ] Add SQL format for export function ?
- [ ] Add export data to PDF for export function
- [ ] Add monthly overview
- [ ] Add number of visitors online in the overview page
- [ ] Change the email notification activation control (according enabled/disabled)
- [ ] Add https://github.com/piwik/referrer-spam-blacklist
- [ ] Add jQuery Vector Maps v1.5.0

## 1.3.5
*Released date: 2021-01-18*

- [x] Improving on preventing potential SQL injection
- [x] Os (+3)

## 1.3.4
*Released date: 2021-01-16*

- [x] Improving on preventing potential SQL injection
- [x] Browsers (+10)

## 1.3.3
*Released date: 2021-01-20*

- [x] Improving on preventing potential SQL injection
- [x] Browsers (+30)

## 1.3.2
*Released date: 2019-07-20*

- [x] Fix exportation dates

## 1.3.1
*Released date: 2019-05-19*

- [x] Update Hungarian language thanks to Pille
- [x] Remove Warning "Address is not a valid IPv4 or IPv6"
- [x] Browsers (+93), OS (+11)

## 1.3.0
*Released date: 2018-05-15*

- [x] Fix some PHP 7.2 Warning
- [x] Add browser (+97), OS (+2)

## 1.2.9
*Released date: 2017-10-29*

- [x] Fix some PHP Notice

## 1.2.8
*Released date: 2017-10-04*

- [x] Fix Os missing images
- [x] Fix undefined index warning
- [x] Update Hungarian language thanks to Pille
- [x] Fix PHP 7.1 specific error

## 1.2.7
*Released date: 2017-07-02*

- [x] Fix %thistotalvisits% javascript
- [x] Fix offsets not stores in option after sanitization
- [x] Fix css wordpress problem

## 1.2.6
*Released date: 2017-03-23*

- [x] Fix url sanitization that break page detection

## 1.2.5
*Released date: 2017-03-20*

IMPORTANT CRITICAL UPDATE

- [x] Fix Stored cross-site scripting vulnerabilities, find by Han Sahin of www.SumOfPwn.nl @sumofpwn
- [x] Avoid direct access to pages
- [x] Changing path detections (not support anymore WordPress 2.x or lower)
- [x] Fix widget variable list
- [x] Use javascript+ajax for variables API
- [x] Remove the usage of wp_load
- [x] Complete name conversion
- [x] External API via WP Ajax
- [x] Fix missing 2 spider images
- [x] Use NONCE for ajax call in variables API

## 1.2.4
*Released date: 2016-06-18*

- [x] Add user message about overview loading (regarding the external api)
- [x] Add User information and fix display tab in Options page
- [x] Update Screenshot
- [x] Update Ip2nation definitions (16/16/2016)
- [x] Update fr_FR,it_IT, es_ES translation
- [x] Remove "Creating default object from empty value" warming
- [x] Add error images for an Ajax failure
- [x] Fix sender display bug (email notification)

## 1.2.3
*Released date: 2016-06-12*

- [x] Change overview loading: used the external api

## 1.2.2
*Released date: 2016-05-28*

- [x] Add "wpversion" into External API
- [x] Add Information DB in tool menu
- [x] Update Screenshot
- [x] Update readme info
- [x] Fix Warning message in Dashboard
- [x] Remove "Empty needle" warning
- [x] Add spider (+1)

## 1.2.1
*Released date: 2016-04-25*

- [x] Add Overview Meta-boxes (free ordination + open/close)
- [x] Add OS (+76)
- [x] Add Browsers (+160)
- [x] Add Spiders (+25)
- [x] Fix email notification bug (wrong schedule)
- [x] Fix get_currentuserinfo() deprecated function since WP4.5
- [x] Change img display method of OS & Browser definitions
- [x] Add spiders colored agent
- [x] Add Hide/show spiders function in agent
- [x] Add message to news box when def update

## 1.2.0
*Released date: 2016-04-17*

- [x] Fix wrong calculation of statistics variation
- [x] Update 'Notification boxes' behavior
- [x] Add OS (+34)
- [x] Add Browsers (+20)

## 1.1.9
*Released date: 2016-03-21*

- [x] Avoid the use of JQuery to prevent conflict with other themes/plugin

## 1.1.8
*Released date: 2016-03-20*

- [x] Add 'from' option in email notification tab
- [x] Add option for adding a fixed number of visits for how before use another statistic plugin
- [x] Add pickaday for export tool (https://github.com/dbushell/Pikaday)
- [x] Add filename field for export tool
- [x] Add file extension field for export tool
- [x] Add autosave function for export tool
- [x] Add ressources tab in Credits page
- [x] Fix title with spaces visualization in widget
- [x] Fix wrong display in Credits page
- [x] Update fr_FR and it_IT translation

## 1.1.7
*Released date: 2016-02-28*

- [x] Add Icons on database tools
- [x] Update cz_CZ translation thanks to Petr Janda
- [x] Update fr_FR translation
- [x] Fix Export dysfunction in database tools ('right' issue)
- [x] Fix Export dysfunction in database tools ('date' issue)

## 1.1.6
*Released date: 2016-02-23*
- [x] Fix email notification bug
- [x] Update it_IT translation

## 1.1.5
*Released date: 2016-02-10*

- [x] Double the length of email address
- [x] Changes hooks for emails to avoid possible conflict with other plugins

## 1.1.4
*Released date: 2016-02-09*

- [x] Add email notification options
- [x] Add Notice Message Box
- [x] Add OS (+6), Browsers (+5), Spiders (+197)
- [x] Add language flag and status on 'Credits' page
- [x] Add Activation of sum option for variable %mvisits%, %wvisits% and %totalvisits%
- [x] Add Settings link in extensions page
- [x] Add control routine for old WP version
- [x] Add Internalization for shortcode
- [x] Change IP detection for better results behind proxy
- [x] Change 'Options' Page code : major improvements
- [x] Change 'Credits' Page code : use of json
- [x] Update Spiders images (+3)
- [x] Update locale fr_FR, it_IT, pl-PL, es_ES
- [x] Fix bug for shortcode [NewStatPress: Top days]
- [x] Fix Memory size : >0.5 Mb free
- [x] Fix HTML cleanup
- [x] Fix Readme.txt

## 1.1.3
*Released date: 2016-01-09*

- [x] Fix call to % calculation
- [x] Change dashboard/overview call in External API

## 1.1.2
*Released date: 2016-01-07*

- [x] Use new Ajax call in Wordpress for dashboard
- [x] Fix missing row titles in dashboard in External API

## 1.1.1
*Released date: 2016-01-06*

- [x] Remove included jQuery script that generate activation problem. Dashboard is now to fix.

## 1.1.0
*Released date: 2016-01-06*

- [x] Fix API key calculation
- [x] Fix domain.dat manages inside nsp_visits.php (thanks to Gwss)
- [x] Add %monthtotalpageviews% variable (request by th3no0b)
- [x] Add dashboard generation via external API
- [x] Use full index in Dashboard queries for classical method: super speed up generation
- [x] Fix overview bug in total (thanks to Greg Sydney)

## 1.0.9
*Released date: 2015-09-26*

- [x] Adeguate for translate.wordpress.org
- [x] Update Readme for External API usage

## 1.0.8
*Released date: 2015-09-19*

- [x] Add jquery tabs for credit page
- [x] Updated browser definition
- [x] Add 2 spiders logo add missing browser images
- [x] Rewrite initialization to avoid Warning about Undefined variable search_phrase and searchengine
- [x] Updated Locale fr_FR, it_IT

## 1.0.7
*Released date: 2015-07-11*

- [x] Fix %mvisits% not giving result
- [x] Add %wvisits% week visits
- [x] Fix capability problems created by https://codex.wordpress.org/Multisite_Network_Administration

## 1.0.6
*Released date: 2015-07-01*

IMPORTANT SECURITY UPDATE

- [x] Close a possible Reflected XSS attack (thanks to James H - g0blin Reserch)
- [x] Avoid MySQL error if erroneous input is given (thanks to James H - g0blin Reserch)

## 1.0.5
*Released date: 2015-06-30*

IMPORTANT CRITICAL UPDATE
- [x] Close a XSS and a SQLI Injection involeved IMG tag (thanks to James H - g0blin Reserch)

## 1.0.4
*Released date: 2015-06-30*

IMPORTANT CRITICAL UPDATE
- [x] Close a persistent XSS via HTTP-Header (Referer) (no authentication required) (thanks to Michael Kapfer - HSASec-Team)

## 1.0.3
*Released date: 2015-06-23*

- [x] Fix nsp_DecodeURL code cleanup replacement
- [x] Fix NewStatPress_Print missing after cleanup

## 1.0.2
*Released date: 2015-06-21*

User interface changes:
- [x] Added API key option in option menu
- [x] Added API activation option in option menu
- [x] Implement external API "version" (gives actual version of NewStatPress)
- [x] Added informations tabs in Tools menu ()
- [x] Updated General tab in Option menu ()
- [x] Updated Widgets title
- [x] Updated IP2nation option menu
- [x] Fixed Dashboard widget overflow

Core changes:
- [x] Fix the plugin menu view for "subscriver"
- [x] Fix IP2nation database installation bug
- [x] Remove IP2nation download function (to be more conform with WP policy)
- [x] Massive code cleaning to avoid conflict with others plugins
- [x] Added bots (+7, thanks to Nahuel)
- [x] Updated Locale fr_FR, it_IT

## 1.0.1
*Released date: 2015-06-08*

IMPORTANT CRITICAL UPDATE
- [x] Close a SQL injection (Thanks to White Fir Design for discover and communicate). Actually the old Statpress search code seems to be sanitized all.

## 1.0.0
*Released date: 2015-05-29*

Core changes:
- [x] Remove %installed% variable

## 0.9.9
Released date: 2015-05-20

IMPORTANT CRITICAL UPDATE
- [x] Close a XSS and a SQL injection and possible other more complex to achieve (thanks to Adrián M. F. for discover and communicate them). Those are inside the search routine from Statpress so ALL previous versions of Newstatpress are vulnerable (and maybe they are present in lot of Statpress based plugin and Statpress itself).
- [x] Fix missing browser images
- [x] Add tools for optimize and repair the statpress table
- [x] Updated Locale it_IT

## 0.9.8
Released date: 2015-04-26

- [x] Fix missing routine for update
- [x] Fix cs_CZ translation

## 0.9.7
Released date: 2015-04-11

User interface changes:
- [x] Added New option in Overview Tab : overview stats calculation method (global distinct ip OR sum of each day) (Note: online for month at the moment)
- [x] Added New options in General Tab : add capabilities selection to display menus, options menu need to be administrator by default
- [x] Added New information 'Visitors RSS Feeds' in Overview page
- [x] Updated Locale fr_FR, it_IT, cs_CZ

Core changes:
- [x] Updated OS definition
- [x] Updated Browser definition
- [x] Fixed '3 months Purge' issue


## 0.9.6
Released date: 2015-02-21

User interface changes:
- [x] Added Option page with tab navigation (use jQuery and idTabs)
- [x] Fixed Search page link
- [x] Updated Locale fr_FR, it_IT

Core changes:
- [x] Various fixes (global definition, function, plugin page names with nsp_ prefix, code spliting)
- [x] Various debug fixes (deprecated function, unset variable)
- [x] Fixed %thistotalvisit% in API call


## 0.9.5
Released date: 18/02/2015

- [x] Fixed PHP compatibility issue on old versions (tools page)

## 0.9.4
Released date: 18/02/2015

User interface changes:
- [x] Added Tool page with tab navigation
- [x] Added variable informations in Widget 'NewStatPress'
- [x] Fix Overview Table (CSS)
- [x] Updated Locale fr_FR, it_IT

Core changes:
- [x] Update of Widget 'NewStatPress' : code re-writed


## 0.9.3
Released date: 17/02/2015

- [x] Add Visits page with tab navigation
- [x] Add tab navigation in Crédits page
- [x] Add 'Donator' tab in Crédits page
- [x] Add 'visits' and 'options' links in Dashboard widget
- [x] Add CSS style to navbar in Visitors page
- [x] Add colored variation in overview table
- [x] Re-writed Overview function
- [x] Fix Duplicate INDEX when User database is updated (function rewrited)
- [x] Fix dashboard 'details' dead link
- [x] Fix navbar dead link in visitors page
- [x] Various code fixing
- [x] Api for variables (10x faster to load page with widget)
- [x] Changelog sort by last version
- [x] Update: locale fr_FR, it_IT

## 0.9.2
Released date: 09/02/2015

- [x] CSS fix, Overview fix and wp_enqueue_style compatibility fix

## 0.9.1
Released date: 08/02/2015

- [x] Activate changes of 0.8.9 in version 0.9.1 with PHP fixes

## 0.9.0
Released date: 07/02/2015

- [x] Revert to version 0.8.8 for problems with old PHP version

## 0.8.9
Released date: 07/02/2015

Development:
- [x] Add Ip2nation download function in option page
- [x] Add plugin homepage link, news feeds link, bouton donation in credit page
- [x] Add CSS style to stylesheet (./css/style.css), partially done
  - [x] remove page
  - [x] update page
  - [x] credit page
- [x] Optimization of the option page
- [x] Optimization of the credit page
- [x] Optimization of the export page
- [x] Optimization of the remove page
- [x] Optimization of the database update page
- [x] Fixed 'selected sub-menu' bug
- [x] Fixed wrong path to update IP2nation when database is updated (/includes)
- [x] Add variables %yvisits% (yesterday visits) %mvisits% (month visits)
- [x] Fix 5 bots, add 13 new bots


Translation Update:
- [x] fr_FR
- [x] it_IT
