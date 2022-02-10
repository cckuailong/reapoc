=== Asgaros Forum ===
Contributors: Asgaros, qualmy91
Donate link: https://www.asgaros.de/donate/
Tags: forum, forums, discussion, multisite, community, bulletin, board, asgaros, support
Requires at least: 4.9
Tested up to: 5.8
Requires PHP: 5.2
Stable tag: 1.15.20
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Asgaros Forum is the best forum-plugin for WordPress! It comes with dozens of features in a beautiful design and stays simple and fast.

== Description ==
Asgaros Forum is the perfect WordPress plugin if you want to extend your website with a lightweight and feature-rich discussion board. It is easy to set up, super fast and perfectly integrated into WordPress.

= Support, Demo & Documentation =
* [Support & Demo](https://www.asgaros.de/support/)
* [Documentation](https://www.asgaros.de/docs/)

= Features =
* Simple Content Management
* Profiles & Members List
* Notifications & Feeds
* Powerful Editor
* SEO-friendly
* Reactions
* Uploads
* Search
* Polls
* Widgets
* Statistics
* Ads Management
* Guest Postings
* Approval, Banning & Reporting
* Moderators, Permissions & Usergroups
* Customizable Responsive Theme
* Multilingualism
* Multiple Instances
* Multisite Compatibility
* myCRED Integration

= Installation =
* A new forum-page is automatically created during the installation
* Add this page to your menu so your users can access your forum
* Thats all!

== Installation ==
* Download `Asgaros Forum`
* Activate the plugin via the `Plugins` screen in WordPress
* A new forum-page is automatically created during the installation
* You can also add a forum to a page manually by adding the `[forum]` shortcode to it
* Add this page to your menu so your users can access your forum
* On the left side of the administration area you will find a new menu called `Forum` where you can change the settings and create new categories & forums
* Thats all!

== Frequently Asked Questions ==
= I cant see content or modifications I made to the forum =
If you are using some third-party plugin for caching (WP Super Cache for example) and disable caching for the forum-page, everything should work fine again.
= I cant upload my files =
By default only files of the following filetype can be uploaded: jpg, jpeg, gif, png, bmp, pdf. You can modify the allowed filetypes inside the forum administration.
= Where can I add moderators or ban users? =
You can ban users or ad moderators via the user edit screen in the WordPress administration interface.
= How can I show a specific post/topic/forum/category on a page? =
You can extend the shortcodes with different parameters to show specific content only. For example: `[forum post="POSTID"]`, `[forum topic="TOPICID"]`, `[forum forum="FORUMID"]`, `[forum category="CATEGORYID"]` or `[forum category="CATEGORYID1,CATEGORYID2"]`.
= How can I add a captcha to the editor for guests? =
To extend your forum with a captcha you have to use one of the available third-party captcha-plugins for WordPress and extend your themes functions.php file with the checking-logic via the available hooks and filters by your own. For example you can use the plugin [Really Simple CAPTCHA](https://wordpress.org/plugins/really-simple-captcha/) and extend your themes functions.php file with this code:
[https://gist.github.com/Asgaros/6d4b88b1f5013efb910d9fcd01284698](https://gist.github.com/Asgaros/6d4b88b1f5013efb910d9fcd01284698).
= I want help to translate Asgaros Forum =
You can help to translate Asgaros Forum on this site:
[https://translate.wordpress.org/projects/wp-plugins/asgaros-forum](https://translate.wordpress.org/projects/wp-plugins/asgaros-forum).
Please only use this site and dont send me your own .po/.mo files because it is hard to maintain if I get multiple translation-files for a language.
= Please approve my translations =
You can approve translations by yourself if you are a Project Translation Editor (PTE). Please contact me in the forums if you are a native speaker and want to become a PTE.
= How can I add my own theme? =
You can add own themes for your forum in the `/wp-content/themes-asgarosforum` directory (for example: `/wp-content/themes-asgarosforum/my-theme`). All themes in the `/wp-content/themes-asgarosforum` can be activated in the forum options. Each theme must have the following files: `style.css`, `widgets.css` and `editor.css`.
= Which hooks and filters are available? =
You can find a list of available hooks and filters on this site:
[https://www.asgaros.de/support/?view=thread&id=407](https://www.asgaros.de/support/?view=thread&id=407).

== Screenshots ==
1. The forum overview
2. The topic overview
3. The topic view
4. Creating a new topic
5. Manage forums in the administration area
6. Manage general options

== Changelog ==
= 1.15.20 =
* Fixed: Broken links when URLs contain special characters
* Fixed: Add missing escaping for output data
= 1.15.19 =
* Fixed: Add missing escaping for output data
= 1.15.18 =
* Fixed: Add missing sanitizing for input data
* Fixed: Add missing escaping for output data
* Fixed: Warnings related to the currently implemented security-improvements
* Fixed: Forum-icons could not get saved correctly
* Fixed: Correctly save allowed html-tags for signatures in forum-settings
* Removed: [Advertising functionality](https://www.asgaros.de/support/topic/removal-of-advertising-functionality/)
* Performance improvements and code optimizations
= 1.15.17 =
* Fixed: Add missing sanitizing for input data
* Fixed: Broken output due to wrong escape-functions
= 1.15.16 =
* Fixed: Display and calculation-issues related to time and timezones
* Fixed: Ensure safe local redirects within the forum
* Fixed: Add missing sanitizing for input data
* Fixed: Add missing escaping for output data
* Changed: Added some clarifying comments for translators
* Performance improvements and code optimizations
= 1.15.15 =
* Fixed: SQL injection vulnerability in the approval-functionality
* Fixed: Prevent cross-site request forgery when creating, editing or deleting posts and topics
* Fixed: Add missing sanitizing for input data
* Fixed: Use sanitizing instead of escaping functions for input data
* Fixed: Add missing escaping for output data
= 1.15.14 =
* Fixed: Escape forum-name properly in backend
= 1.15.13 =
* Fixed: Multiple Unauthenticated SQL Injection vulnerabilities in the subscription-logic
= 1.15.12 =
* Added: asgarosforum_filter_before_post_submit filter
* Added: asgarosforum_filter_before_edit_post_submit filter
* Added: asgarosforum_filter_before_topic_submit filter
* Fixed: Warning in permalink-logic when a deleted post contains the forum-shortcode
* Fixed: Display issues with some themes
* Improved compatibility with Sassy Social Share
* Compatibility with WordPress 5.8
= 1.15.11 =
* Added: Option to limit the number of awarded points for likes in MyCred integration
* Added: title-attribute to spoiler-shortcode
* Added: asgarosforum_filter_username filter
* Added: asgarosforum_filter_error filter
* Added: asgarosforum_filter_forum_status_options filter
* Fixed: Hide empty paragraphs for topics created via WordPress posts
* Fixed: pre and code-tags were breaking the layout
* Fixed: Answer-options in polls were ordered randomly on some server-configurations
* Fixed: Added missing translation strings
* Updated: Font Awesome version 5.15.3
* Compatibility with WordPress 5.7
= 1.15.10 =
* Added: Option to show usernames in reactions
* Added: Option to hide site-admins in memberslist
* Added: Option to change format for activity-timestamps
* Added: asgarosforum_filter_profile_row filter
* Fixed: Make it possible to delete empty topics
* Fixed: Errors when a topic is empty due to problems during post-creation
* Fixed: Display issues with some themes
* Changed: Moved post-counter and report-button to the top
* Performance improvements and code optimizations
= 1.15.9 =
* Added: asgarosforum_filter_show_header filter
* Added: asgarosforum_filter_upload_folder filter
* Fixed: Show bulk-actions for user-roles/groups to administrators only
* Fixed: Added missing context for some translation strings
* Performance improvements and code optimizations
* Improved compatibility with All In One SEO Pack
* Compatibility with WordPress 5.6
= 1.15.8 =
* Added: asgarosforum_filter_meta_post_type filter
* Fixed: Broken TinyMCE-editor in the administration-area
* Performance improvements and code optimizations
= 1.15.7 =
* Fixed: PHP warning during initialization of REST-routes
= 1.15.6 =
* Added: Option to hide "last seen" status from profiles and the members list
* Added: Links to login/register notification
* Added: asgarosforum_signature filter
* Added: asgarosforum_filter_header_menu filter
* Fixed: Display issues with some themes
* Fixed: Minor display issues
* Updated: Font Awesome version 5.14.0
* Compatibility with WordPress 5.5
= 1.15.5 =
* Added: Option to change title-separator
* Added: CSS view-class to body-classes
* Added: asgarosforum_title_separator filter
* Added: asgarosforum_filter_profile_header_image filter
* Fixed: Do not execute a search-query when no categories are accessible
* Fixed: Minor display issues
* Changed: Misleading strings
* Improved compatibility with All In One SEO Pack
* Improved compatibility with Rank Math SEO
* Improved compatibility with Yoast SEO
= 1.15.4 =
* Fixed: Fatal error when multibyte-extension is not installed
= 1.15.3 =
* Added: Show number of forum-posts for every user in administration area
* Fixed: Prevent banned users from reacting to posts
* Fixed: Allow line-breaks in biographical info of profile
* Fixed: SQL syntax error when opening a non-existent topic
* Fixed: Rare string-cutting issues
* Fixed: Misleading strings
* Fixed: Display issues with some themes
* Fixed: Display issues on mobile devices
* Improved compatibility with Rank Math SEO
* Performance improvements and code optimizations
* Compatibility with WordPress 5.4
= 1.15.2 =
* Added: Option to hide newest member
* Added: Option to let users open their own topics
* Added: Option to let users close their own topics
* Fixed: Only show meta-box for creating topics when user can publish a page/post
* Fixed: Errors in Google Search Console for breadcrumbs
* Updated: Font Awesome version 5.11.2
* Minor design changes
* Compatibility with WordPress 5.3
= 1.15.1 =
* Added: Widget option to filter output by forums
* Added: Meta box to create topics for WordPress pages and posts
* Changed: asgarosforum_widget_recent_posts_custom_content is now a filter
* Changed: asgarosforum_widget_recent_topics_custom_content is now a filter
* Updated: Font Awesome version 5.10.2
* Minor design changes
* Performance improvements and code optimizations
= 1.15.0 =
* Added: myCRED Integration
* Added: Reputation system based on amount of posts
* Added: Option to change login URL
* Added: Option to change register URL
* Added: asgarosforum_profile_row action
* Added: asgarosforum_after_add_reaction action
* Added: asgarosforum_after_remove_reaction action
* Added: asgarosforum_after_update_reaction action
* Fixed: Missing users in suggestions if their display name contains special characters
* Fixed: Missing users in suggestions if their display name is not equal to their unique name
* Fixed: Prevent that users react to their own posts
* Fixed: Display issues with some themes
* Updated: Font Awesome version 5.10.1
* Performance improvements and code optimizations
= 1.14.15 =
* Fixed: Compatibility issues with certain editor plugins
* Fixed: Display issues with some themes
* Changed: Combined approval and closed options into a forum status option
* Updated: Font Awesome version 5.10.0
* Improved compatibility with Enlighter
* Minor design changes
* Performance improvements and code optimizations
= 1.14.14 =
* Added: Option to define general forum description
* Fixed: Dont cut meta-tag description in the middle of a word
* Fixed: Compatibility issues with certain editor plugins
* Performance improvements and code optimizations
= 1.14.13 =
* Fixed: Problems when saving ads
* Fixed: Fatal PHP error when processing mentionings
* Changed: Default editor-buttons
* Removed: Option for minimalistic editor
= 1.14.12 =
* Added: Options to hide certain filters from memberslist
* Added: Code editor for custom-css and ad-code
* Added: Time to elements in unread-view
* Fixed: Display issues with Font Awesome icons
* Minor design changes
* Performance improvements and code optimizations
* The required minimum WordPress version is now 4.9
= 1.14.11 =
* Fixed: Fatal PHP error in memberslist
= 1.14.10 =
* Added: Suggestions for mentioning-functionality
* Added: Show uploaded files in notification-mails
* Added: Option to define if poll-results are visible without vote
* Added: asgarosforum_enqueue_css_js action
* Fixed: PHP warnings during mentioning-processing when HTML 5 tags are used in posts
* Fixed: Display issues with some themes
* Fixed: Correctly embed iframe-media
* Updated: Font Awesome version 5.9.0
* Minor design changes
* Extensive performance improvements in memberslist and online-logic
* Performance improvements and code optimizations
= 1.14.9 =
* Added: Option to define number of activities per page
* Added: asgarosforum_reactions filter
* Added: asgarosforum_execution_check action
* Fixed: JavaScript ReferenceError when leaving a page which does not contain an instance of the TinyMCE editor
* Fixed: Display issues with some themes
* Changed: Use AJAX for reactions to prevent reload of page
* Improved compatibility with Toolset
* Improved compatibility with Permalink Manager
* Performance improvements and code optimizations
= 1.14.8 =
* Added: Option to let users delete their own topics
* Added: Option to set time limitation for deleting topics
* Added: Option to let users delete their own posts
* Added: Option to set time limitation for deleting posts
* Added: Option to disable post editing
* Added: Option to change location of subforums
* Added: Warning when an user leaves a page with unsaved changes in the editor
* Fixed: Division by zero warning when viewing the results of a poll without votes
* Fixed: Added missing translation strings
* Fixed: Display issues with some themes
* Changed: Select ad randomly if multiple ads are defined for a specific location
* Changed: Restructuring of settings
* Updated: Font Awesome version 5.8.2
* Minor design changes
* Performance improvements and code optimizations
= 1.14.7 =
* Fixed: Search-engines could not index the forum if profiles were not accessible for guests
= 1.14.6 =
* Added: Option to change the indicator color for read and unread items
* Fixed: Wrong stylings when using custom colors
= 1.14.5 =
* Added: Options to change URL-slugs for views
* Added: Option to set icon for usergroup
* Added: Generate Open Graph image-tag for topics
* Added: asgarosforum_seo_trailing_slash filter
* Fixed: Search-engines cannot longer index profiles if they are not accessible for guests
* Fixed: Display issues with Font Awesome icons
* Fixed: Display issues with some themes
* Minor design changes
* Performance improvements and code optimizations
= 1.14.4 =
* Added: Option to define who can use signatures
* Fixed: Properly escape content of posts
* Fixed: Strip slashes inside of polls
* Fixed: Escape HTML inside of polls
* Compatibility with WordPress 5.2
= 1.14.3 =
* Added: Caption titles to topic-icons
* Fixed: Display issues with Font Awesome icons
* Fixed: Display issues with some themes
= 1.14.2 =
* Fixed: Load Font Awesome v4 compatibility library to fix display-issues with icons
= 1.14.1 =
* Fixed: Database error when creating table for poll-answers
= 1.14.0 =
* Added: Poll functionality
* Added: Font Awesome icons
* Added: Option to change URL mode (slug, ID) for SEO-friendly URLs
* Added: Option to disable spoiler-functionality
* Added: asgarosforum_after_topic_approve hook
* Fixed: Dont send notifications to users who got mentioned inside of quotes
* Fixed: Broken layout with certain links
* Fixed: Wrong avatar-size in certain configurations
* Fixed: Display issues with some themes
* Improved compatibility with Rank Math SEO
* Minor design changes
* Updated design for the administration area
* Performance improvements and code optimizations
= 1.13.3 =
* Added: Option to disable avatars
* Added: Show received likes in profiles
* Added: Functionality to reassign forum posts when deleting users
* Design changes
* Performance improvements and code optimizations
= 1.13.2 =
* Fixed: Names of administrators/moderators not highlighted when using custom link-colors
= 1.13.1 =
* Added: Global stickies
* Added: Option to disable automatic embedding of content in posts
* Added: Option to show excerpt in recent topics/posts widget
* Added: asgarosforum_widget_excerpt_length filter
* Fixed: In some cases usergroup could not get removed from user using bulk-actions
* Fixed: Wrong stylings when using custom colors
* Fixed: Display issues with some themes
* Changed: Moderators have access to reports
* Changed: Report management has been moved to the frontend
* Changed: Dont show HTML tags in report preview
* Changed: Show enabled register-link even when user registration is temporarily disabled
* Improved RTL support
* Improved compatibility with Yoast SEO
* Minor design changes
* Performance improvements and code optimizations
* Compatibility with WordPress 5.1
= 1.13.0 =
* Added: Approval functionality for topics
* Added: Spoiler functionality
* Added: Option to define days of activity to show
* Added: Option to define receivers of administrative notifications
* Fixed: Display issues in the administration-area of Asgaros Forum when notices of WordPress or other plugins are shown
* Fixed: Broken forum if settings could not get loaded from database
* Fixed: Height of editor to small in certain configurations
* Fixed: It is not longer possible to quote posts from other topics
* Fixed: It is not longer possible to quote posts from inaccessible topics
* Fixed: It is not longer possible for guests to post when topics are inaccessible for guests
* Fixed: Search-engines cannot longer index inaccessible areas
* Fixed: Dont leak content via meta-tags in inaccessible areas
* Fixed: Performance issues in forums which consist of many topics
* Fixed: PHP-error in notifications-processing when a receiver-mail does not belong to a WordPress-user
* Fixed: Wrong stylings when using custom colors
* Fixed: Display issues with some themes
* Changed: Show all editor-buttons when the minimalistic editor is not used
* Changed: Dont notify users about a new post or topic when they already receive a mail because they got mentioned
* Minor design changes
* Performance improvements and code optimizations
* The required minimum WordPress version is now 4.8
= 1.12.1 =
* Fixed: Ad code containing JavaScript could not be edited
* Fixed: Allow activity-feed when using shortcode-parameters for categories
* Fixed: Only show accessible topics in unread-view
* Fixed: Only show accessible topics in unread-view when using shortcode-parameters
* Fixed: Only show accessible topics in post-history when using shortcode-parameters
* Minor design changes
* Performance improvements and code optimizations
= 1.12.0 =
* Added: Ads Management
* Added: Option to add custom css
* Added: Option to hide posts from logged-out users
* Added: Mark all read-button to unread-view
* Added: Show location of unread topics
* Added: asgarosforum_content_top hook
* Added: asgarosforum_content_header hook
* Added: asgarosforum_after_category hook
* Added: asgarosforum_after_forum hook
* Added: asgarosforum_after_topic hook
* Added: asgarosforum_after_post hook
* Added: asgarosforum_content_bottom hook
* Added: asgarosforum_add_admin_submenu_page hook
* Fixed: Rare javascript-errors with some themes
* Removed: asgarosforum_after_first_post hook
* Minor design changes
* Minor design changes in the administration area
* Performance improvements and code optimizations
= 1.11.3 =
* Fixed: Possible infinite-loop during database-updates
* Fixed: Prevent creation of indexes if they already exist
* Fixed: Possible PHP-errors in overview when there are empty forums/subforums
= 1.11.2 =
* Added: Option for recent-posts-widget to group posts by topic
* Added: asgarosforum_user_replacements filter
* Fixed: Delayed database-updates after plugin-update
* Fixed: Performance issues in forums which consist of many topics
* Fixed: Rare PHP-error in unread-logic
* Fixed: It was not possible to use HTML-attributes in mail-templates
* Fixed: Display issues with some themes
* Performance improvements and code optimizations
= 1.11.1 =
* Fixed: Wrong avatar-sizes with certain themes and plugins
= 1.11.0 =
* Added: New view to show unread topics
* Added: Filter users in memberslist by their role/usergroup
* Added: Mail-templates for notifications
* Added: Show groups of user inside the memberslist
* Added: Bulk-actions to assign roles
* Added: Filter users in backend by their role
* Added: Option to change link-color
* Added: Option to change light text-color
* Added: Option to change second background-color
* Added: asgarosforum_prepare hook
* Added: asgarosforum_usergroup_{ID}_add_user hooks
* Added: asgarosforum_usergroup_{ID}_remove_user hooks
* Added: asgarosforum_breadcrumbs_{current_view} hooks
* Fixed: Only show accessible posts inside post-histories to the current user
* Fixed: Broken forum-role selector in backend-profile
* Fixed: Broken banning-functionality in frontend when using plain URL-structure
* Fixed: Various problems which prevents the application of custom appearance-modifications
* Fixed: Hidden filters for usergroups in backend user-overview when there were many usergroups
* Fixed: A couple of display issues
* Fixed: Display issues with some themes
* Changed: Dont group posts in "Recent Forum Posts"-widget by topic
* Changed: Show IDs of usergroups in the backend
* Changed: Usergroup-tags now link to memberslist showing all users of that group
* Changed: Show username of notification-receiver in mail
* Minor design changes
* Minor design changes in the administration area
* Improved application of custom appearance-settings
* Mobile-theme improvements
* Improve first-time installation-process
* Screen-reader accessibility improvements
* Performance improvements and code optimizations
* Compatibility with WordPress 5.0
= 1.10.1 =
* Added: Forum administrator role
* Added: Show forum role in backend user overview
* Added: Moderators can now ban users
* Added: Its now possible to ban users from their profile
* Fixed: Rare PHP-error in rewrite-logic when post-object is not set
* Fixed: Performance issues in forums which consist of many topics
* Fixed: Broken links in posts pointing to other forum topics
* Fixed: Wrong default values for dates in database
* Changed: Users can no longer be moderators and banned at the same time
* Minor design changes
* Performance improvements and code optimizations
= 1.10.0 =
* Added: RSS feeds for topics and forums
* Added: Option to change main forum title
* Added: Unread indicator to activity feed
* Added: Show location of search results
* Added: asgarosforum_wp_head hook
* Added: asgarosforum_bottom_navigation hook
* Added: asgarosforum_filter_editor_buttons filter
* Fixed: Broken cookies when using SEO-friendly URLs
* Fixed: Render allowed HTML-tags in signatures inside the profile
* Fixed: Display issues with some themes
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.9.6 =
* Added: Option to disable counting of topic views
* Added: Option to allow HTML tags in signatures
* Added: Option to define allowed HTML tags for signatures
* Fixed: Use correct background color in profile when using a custom background color
* Changed: Remove HTML tags from signatures completely when HTML tags are not allowed
= 1.9.5 =
* Added: asgarosforum_custom_profile_menu hook
* Fixed: Broken post history view when SEO-friendly URLs are disabled
= 1.9.4 =
* Added: Post history of users
* Added: Option to change accent color
* Fixed: Display issues with some themes
* Changed: Minor design changes
* Changed: Mobile theme improvements
* Performance improvements and code optimizations
= 1.9.3 =
* Fixed: Users cannot post in closed topics anymore
* Fixed: Users cannot create topics in closed forums anymore
* Changed: Show registration date in last seen-status when user was not online yet
= 1.9.2 =
* Fixed: Broken seo-friendly URLs in combination with certain server protocol configurations
= 1.9.1 =
* Fixed: Broken usernames when URL contains certain special characters
* Fixed: Broken profile URLs when usernames are numeric
* Fixed: Broken URLs when using certain custom permalink structures
* Fixed: Dont show last seen-status in profiles/memberslist when who is online-functionality is disabled
* Changed: Allow HTML in forum descriptions
* Added: asgarosforum_widget_recent_posts_custom_content hook
* Added: asgarosforum_widget_recent_topics_custom_content hook
= 1.9.0 =
* Added: SEO-friendly URLs
* Added: User option to disable mentioning notifications
* Added: Option to add new users to specific usergroups automatically
* Added: Show last post info on small screens
* Fixed: Display issues with some themes
* Fixed: Display issues on small screens
* Changed: Group activity by time
* Changed: Minor design changes
* Changed: Minor design changes in the administration area
* Performance improvements and code optimizations
= 1.8.4 =
* Added: Activity feed functionality
* Added: Option to hide specific usergroups from public
* Added: Option to set time limitation for editing posts
* Added: Show visible usergroups of user in posts
* Changed: Always use defined usergroup color when output an usergroup
* Changed: Improved descriptions in subscriptions overview
* Changed: Minor design changes
* Performance improvements and code optimizations
* Improved compatibility with Autoptimize
= 1.8.3 =
* Fixed: Serious performance issues in the notifications logic
* Fixed: Broken subscriptions functionality for administrators in categories which are accessible for moderators only
* Fixed: Broken subscription checkbox in editor
* Removed: asgarosforum_filter_subscribers_query_new_post filter
* Removed: asgarosforum_filter_subscribers_query_new_topic filter
* Changed: Minor design changes
* Changed: Mobile theme improvements
* Changed: Dont show subscription checkbox in editor when user is subscribed to all topics
* Performance improvements and code optimizations
= 1.8.2 =
* Added: Option to subscribe to all topics and posts
* Added: Possibility to unsubscribe from subscriptions inside the subscription overview
* Added: Option to hide category name in breadcrumbs
* Changed: Moved user subscription settings to the subscription overview
* Changed: Minor design changes
* Changed: Mobile theme improvements
* Changed: Moved mobile css rules into style.css file
* Fixed: Display issues on small screens
* Performance improvements and code optimizations
= 1.8.1 =
* Added: Time when an user was last seen to profiles and members list
* Added: Name of the person who mentioned an user to notification mail
* Added: asgarosforum_filter_notify_mentioned_user_message filter
* Fixed: Display issues in the administration area on small screens
* Fixed: Display issues with some themes
= 1.8.0 =
* Added: Mentioning functionality
* Added: Functionality to move forums
* Fixed: Reactions not saved correctly in some cases
* Fixed: Display issues with some themes
* Changed: Improved mobile navigation
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.7.2 =
* Fixed: Parse error when using some older versions of PHP
= 1.7.1 =
* Added: The search functionality now also checks topic titles
* Added: Include currently active guests in the statistics-area
* Fixed: Broken usernames in reports when users dont exist
* Fixed: Display issues with some themes
* Removed: Read more-button from editor
* Changed: New profile design
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.7.0 =
* Added: Reaction functionality
* Added: Reporting functionality
* Added: Search Widget
* Added: Option to limit file uploads to moderators
* Added: Option to change font
* Added: Option to change font size
* Added: Edit profile-link to profile
* Added: Possibility to toggle truncated quotes with a click
* Added: asgarosforum_prepare_{current_view} hooks
* Added: asgarosforum_filter_get_sticky_topics_order filter
* Fixed: Mark all read not working when using category-parameters in shortcode
* Fixed: Visited topics not marked as read in certain cases
* Fixed: Wrong author names for automatically created topics of scheduled blog posts
* Fixed: Wrong titles when using certain SEO plugins
* Fixed: Broken search when using certain special characters
* Fixed: Some strings could not get translated
* Fixed: Display issues with some themes
* Changed: Apply additional validation rules before saving options
* Changed: All theme and color options are now available in the appearance area
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.6.8 =
* Added: Option to change the border color
* Added: Option to hide members list for guests
* Added: Option to define number of members per page in members list
* Added: Show user role in members list
* Added: asgarosforum_filter_widget_avatar_size filter
* Fixed: Parse error when using some older versions of PHP
* Fixed: Wrong stylings when using custom colors
* Fixed: Display issues on mobile devices
* Changed: Design changes for the administration area
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.6.7 =
* Added: Members list
* Added: asgarosforum_filter_members_link filter
* Added: asgarosforum_filter_automatic_topic_title filter
* Added: asgarosforum_filter_automatic_topic_content filter
* Fixed: Only create automatic topics for new blog posts instead for all post types
* Performance improvements and code optimizations
= 1.6.6 =
* Fixed: Do additional error checks during database upgrade to prevent some errors during an update
= 1.6.5 =
* Added: Categories for usergroups
* Fixed: Administrators didnt get notifications in some cases when using usergroups
* Fixed: Hide topics and forums from subscription list when a user has no access to it
* Fixed: Display issues with some themes
* Changed: Show number of users for each usergroup in the user overview
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.6.4 =
* Added: Option to automatically create topics for new blog posts
* Fixed: Broken subscriptions/profile-view when using shortcode with category-parameter
* Changed: Truncate long quotes
* Changed: Minor design changes
* Changed: Renamed asgarosforum_after_add_thread_submit hook into asgarosforum_after_add_topic_submit
* Performance improvements and code optimizations
= 1.6.3 =
* Added: Pagination in topic-overview
* Added: Category name to breadcrumbs
* Added: Usergroups to profile
* Added: Biographical info to profile
* Added: Signature to profile
* Added: asgarosforum_filter_forum_menu filter
* Added: asgarosforum_filter_topic_menu filter
* Added: asgarosforum_filter_post_menu filter
* Changed: Minor design changes
* Performance improvements and code optimizations
* Compatibility with WordPress 4.9
* Added link to the official support forum to the administration area
= 1.6.2 =
* Added: Options to hide login/logout/register buttons
* Added: asgarosforum_custom_header_menu hook
* Fixed: Broken search when using plain permalink structure
* Fixed: Styling issues with highlighted usernames
* Fixed: Display issues with some themes
* Changed: Prevent indexing of the following views: addtopic, movetopic, addpost, editpost, search
* Changed: Show page number in meta title
* Changed: Minor design changes
= 1.6.1 =
* Fixed: Broken guest-posting functionality
= 1.6.0 =
* Added: Profile functionality
* Fixed: Subscriptions view not working with certain shortcode parameters
* Fixed: Rare PHP-notices
* Changed: Login/Logout/Register links are now accessible everywhere
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.5.9 =
* Added: Option to hide author posts counter
* Fixed: Prevent cross-site request forgery in settings
= 1.5.8 =
* Fixed: Prevent non-admin users from modifying settings
* Fixed: PHP errors when updating user profile
* Changed: Enqueue stylesheets
* Performance improvements and code optimizations
= 1.5.7 =
* Added: Subscription overview
* Fixed: Strip slashes when showing the description inside of a forum
* Changed: Highlight quotes in the editor
* Changed: Minor design changes
= 1.5.6 =
* Fixed: Broken structure administration
* Performance improvements and code optimizations
= 1.5.5 =
* Added: Option to show description in forum
* Added: asgarosforum_filter_avatar_size filter
* Fixed: Dont show last posts/topics in widgets when user cant access any categories
* Fixed: Limit maximum characters in a couple of input fields
* Fixed: Display issues in administration with small screen resolutions
* Fixed: Wrong stylings when using custom colors
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.5.4 =
* Added: Register links to some error messages
* Fixed: Huge performance drops when the forum has a lot of posts/topics
* Fixed: Broken editor with some editor-configurations
* Fixed: Wrong stylings when using custom colors
* Fixed: Display issues with some themes
* Changed: Increased page-navigation size on mobile devices
* Performance improvements and code optimizations
= 1.5.3 =
* Added: Option to change icon of forums
* Fixed: Missing data when using shortcode parameter for specific post
* Performance improvements and code optimizations
= 1.5.2 =
* Added: Option to set notification sender name
* Added: Option to set notification sender mail
* Fixed: Display issues with some themes
* Fixed: JavaScript warning when editing the forum structure
* Changed: Set forum cookies only on forum page
* Changed: Show possible error messages when uploading files
* Changed: Minor design changes
* Performance improvements and code optimizations
* Compatibility with WordPress 4.8
= 1.5.1 =
* Fixed: Fatal PHP error on some versions of PHP
= 1.5.0 =
* Added: Usergroups functionality
* Added: Show newest member in overview
* Added: Show names of online users in overview
* Added: Show who edited a post
* Added: asgarosforum_after_first_post hook
* Added: asgarosforum_custom_forum_column hook
* Added: asgarosforum_custom_topic_column hook
* Fixed: Dont remove paragraphs from quotes
* Fixed: Some strings could not get translated
* Changed: Show possible error messages when updating the structure
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.4.5 =
* Added: Widget option to hide avatars
* Added: Reply counter to recent forum topics widget
* Added: Show author in notification mails
* Added: asgarosforum_before_delete_post hook
* Added: asgarosforum_before_delete_topic hook
* Added: asgarosforum_after_delete_post hook
* Added: asgarosforum_after_delete_topic hook
* Fixed: Huge performance drops when the forum has a lot of posts/topics
* Fixed: Prevent creation of hidden content
* Fixed: Broken/missing links when the filename of uploads contains special characters
* Fixed: Do not try to generate thumbnails for pdf uploads
* Fixed: Dont show HTML-entities in the subject of notification mails
* Changed: Indent subforums in forums-list when moving topics
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.4.4 =
* Added: Avatars to widgets
* Added: asgarosforum_filter_widget_title_length filter
* Fixed: Strip slashes of forum names/descriptions in structure-administration
* Fixed: Warnings on multisite installations
* Changed: Highlight administrators/moderators in widgets
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.4.3 =
* Added: Open Graph tags
* Fixed: Display issues with some themes
* Changed: Minor design changes
* Mobile Theme Improvements
* Search Engine Optimizations
* Performance improvements and code optimizations
= 1.4.2 =
* Revised structure administration
* Changed: Minor design changes
* Removed: asgarosforum_action_add_category_form_fields hook
* Removed: asgarosforum_action_edit_category_form_fields hook
* Removed: asgarosforum_action_save_category_form_fields hook
* Removed: asgarosforum_filter_manage_columns filter
* Removed: asgarosforum_filter_manage_custom_columns filter
= 1.4.1 =
* Added: Option to show thumbnails for uploads
* Fixed: Correct escaping of keywords in search results view
* Changed: Keep keywords in search input field
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.4.0 =
* Added: Option to show who is online
* Added: Shortcode extension to show a specific post
* Added: Shortcode extension to show a specific topic
* Added: Shortcode extension to show a specific forum
* Added: Shortcode extension to show one or more specific categories
* Added: Option to hide breadcrumbs
* Added: Show statistics in the mobile view
* Added: Cancel button to editor
* Added: Show IDs of forums/categories inside the administration area
* Fixed: Sort categories correctly in the forum-overview
* Fixed: Load stylesheets and scripts only on forum page
* Fixed: Wrong labels in forum configuration
* Fixed: Display issues with some themes
* Changed: Hide new post/topic buttons when editor is active
* Changed: Show pagination under search results
* Changed: Show full breadcrumbs when moving topics
* Changed: Structure of the forum configuration
* Changed: Minor design changes
* Search Engine Optimizations
* Performance improvements and code optimizations
= 1.3.10 =
* Fixed: Filter [Forum] shortcodes from posts
* Fixed: Remove filtered shortcodes from post content only
* Fixed: Display issues with some themes
* Changed: Show editor for new posts in the lower area
* Performance improvements and code optimizations
* The required minimum WordPress version is now 4.7
= 1.3.9 =
* Fixed: Dont show error to logged-out users when the guest-posting functionality is disabled
* Fixed: Display issues with some themes
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.3.8 =
* Fixed: Notifications about new topics were sent to all users who subscribed to specific forums
* Fixed: Status change of topics was not working with some WordPress configurations
* Fixed: Scroll to the correct editor position when creating new posts
* Fixed: Display issues with some themes
* Changed: Small adjustment to the editor location
= 1.3.7 =
* Added: Possibility to add multiple quotes at once
* Fixed: Private/pending/draft pages can now be set as the forum location
* Fixed: Display issues with some themes
* Changed: Show editor at the same page when adding posts or topics
* Changed: Show all numbers in a format based on the used locale
* Changed: Always show all forum options
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.3.6 =
* Fixed: Save deactivated options in the administration area correctly
* Changed: Minor design changes
= 1.3.5 =
* Added: Option to limit number of uploads per post
* Added: Option to limit file size of uploads
* Fixed: Broken mark all read-button
* Fixed: Site administrators could not moderate topics/posts on multisite installations
* Fixed: Dont let post-footer hide post-content
* Fixed: PHP notices during creation or editing of content
* Fixed: Reload scripts and stylesheets in administration after plugin update
* Minor usability improvements
* Performance improvements and code optimizations
= 1.3.4 =
* Added: Signature functionality
* Fixed: Reload scripts and stylesheets after plugin update
* Fixed: Display issues with some themes
* Performance improvements and code optimizations
= 1.3.3 =
* Fixed: Parse error when using some older versions of PHP
= 1.3.2 =
* Added: Statistics functionality
* Added: asgarosforum_statistics_custom_element hook
* Added: asgarosforum_statistics_custom_content_bottom hook
* Fixed: Display issues with some themes
* Changed: Minor design changes
= 1.3.1 =
* Added: Subscriptions for specific forums
* Fixed: Group search results by topic to avoid duplicates
* Fixed: Sort search results correctly by relevance and date
* Fixed: Wrong stylings when using custome colors
* Fixed: Display issues when visiting the forum with a mobile device
* Fixed: Display issues with some themes
* Changed: Minor design changes
= 1.3.0 =
* Added: Search functionality
* Added: asgarosforum_filter_error_message_require_login filter
* Changed: Dont shorten topic titles
* Search Engine Optimizations
* Revised design
= 1.2.9 =
* Fixed: Broken widgets with some WordPress configurations
* Fixed: Dont send notifications about new posts/topics in restricted categories to all users
* Fixed: Dont send notifications to banned users
* Added: asgarosforum_{current_view}_custom_content_top hooks
* Added: asgarosforum_{current_view}_custom_content_bottom hooks
* Added: asgarosforum_filter_subscribers_query_new_topic filter
* Added: asgarosforum_filter_subscribers_query_new_post filter
* Added: asgarosforum_subscriber_mails_new_topic filter
* Added: asgarosforum_subscriber_mails_new_post filter
* Changed: Minor design changes
* Performance improvements and code optimizations
* Compatibility with WordPress 4.7
= 1.2.8 =
* Fixed: Broken link-generation with some WordPress configurations
= 1.2.7 =
* Fixed: Broken read/unread-logic
* Fixed: Remove cookies for guests correctly when mark all forums as read
* Changed: Try to determine widget-links when forum-location is not set correctly
= 1.2.6 =
* Fixed: Only show widgets when the forum is configured correctly
* Fixed: Show filtered login-message only when necessary
* Fixed: Rare PHP-notices
* Changed: Moved location-selection from widgets to forum-settings
* Setup improvements
* Performance improvements and code optimizations
* The required minimum WordPress version is now 4.6
= 1.2.5 =
* Fixed: Never highlight guests as topic-authors
* Added: Database-driven read/unread-logic across topics
* Added: Widget for recent forum topics
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.2.4 =
* Fixed: Various fixes in the read/unread-logic
* Added: Option to highlight thread authors
* Added: Option in user profiles to get notifications on new topics
* Added: asgarosforum_filter_subject_before_insert filter
* Added: asgarosforum_filter_content_before_insert filter
* Changed: Read/Unread icons are now better recognizable
* Changed: Renamed asgarosforum_filter_notify_administrator_message filter into asgarosforum_filter_notify_global_topic_subscribers_message
* Performance improvements and code optimizations
= 1.2.3 =
* Fixed: Remove slashes in the forum description
* Fixed: Escape HTML in the forum description
* Fixed: Display issues with some themes
* Changed: Links in notification mails are now clickable
* Changed: Added amount of posts to the asgarosforum_after_post_author hook
= 1.2.2 =
* Fixed: Remove tables on multisite installations correctly
* Fixed: Dont hide widget when there are no recent posts
* Fixed: Display issues with some themes
* Added: Option to allow uploads from guests
* Added: asgarosforum_filter_login_message filter
* Performance improvements and code optimizations
= 1.2.1 =
* Fixed: Prevent generation of wrong canonical links
* Fixed: Rare PHP-warning when using notifications
* Added: Multisite compatibility
* Changed: Show login-links at all pages
* Search Engine Optimizations
* Performance improvements and code optimizations
= 1.2.0 =
* Fixed: Correct escaping in notification mails
* Fixed: Display issues in notification mails with some characters
* Fixed: Resize external iframe-content (e.g. YouTube videos) correctly
* Fixed: Display issues with some themes
* Fixed: Added missing translation strings
* Fixed: Misleading strings
* Added: Option to allow guest postings
* Added: Option to allow shortcodes in posts
* Added: Option to hide uploads from guests
* Added: asgarosforum_editor_custom_content_bottom hook
* Added: asgarosforum_filter_insert_custom_validation filter
* Performance improvements and code optimizations
= 1.1.6 =
* Fixed: HTML is now rendered correctly in notification-mails
* Fixed: Correct escaping of URLs
* Fixed: Prevent modification of topic-subject
* Fixed: Prevent submitting the same form multiple times
* Fixed: Redirect to the current forum-page after login
* Added: Post number to the bottom of posts
* Added: asgarosforum_filter_notify_administrator_message filter
* Added: asgarosforum_filter_notify_topic_subscribers_message filter
* Changed: Revised editor
* Changed: Improved error handling
* Changed: Post number is linking to the post now instead of the date
* Changed: Added post ID to asgarosforum_after_post_message hook
* Changed: Renamed asgarosforum_after_thread_submit hook into asgarosforum_after_add_thread_submit
* Changed: Renamed asgarosforum_after_post_submit hook into asgarosforum_after_add_post_submit
* Changed: Renamed asgarosforum_after_edit_submit hook into asgarosforum_after_edit_post_submit
* Changed: Improved compatibility with some third-party plugins
* Performance improvements and code optimizations
= 1.1.5 =
* Fixed: Correct filtering of posts inside the widget
* Fixed: Hide post-counter for deleted users
* Fixed: The notification-text in mails is now translatable
* Fixed: Rare PHP-notice in categories-configuration
* Fixed: Display issues with some themes
* Added: Subscribe checkbox in editor for the current topic
* Added: asgarosforum_after_post_message hook
* Added: asgarosforum_filter_get_posts filter
* Added: asgarosforum_filter_get_posts_order filter
* Performance improvements and code optimizations
= 1.1.4 =
* Fixed: The names of some users were not shown correctly
= 1.1.3 =
* Fixed: Correct sanitizing of URL parameters
* Fixed: Removed unnecessary hyphen from username
* Added: Option to disable the minimal-configuration of the editor
= 1.1.2 =
* Fixed: PHP parse-error when using a PHP version less than 5.3
* Fixed: Display issues with some themes
* Added: Notifications functionality
* Performance improvements and code optimizations
= 1.1.1 =
* Fixed: PHP-Warning in theme-manager
= 1.1.0 =
* Fixed: Categories were not sorted correctly
* Fixed: Display issues with some themes
* Fixed: Prevent accessing some PHP-files directly
* Fixed: Added missing translation strings
* Added: Sub-forum functionality
* Added: Banning functionality
* Added: Theme manager functionality (thanks to Hisol)
* Added: Color picker for the text
* Added: Color picker for the background
* Changed: Administrators cant be set to forum-moderators anymore
* Changed: Subject in last-post-view links to the topic
* Changed: Revised forum management
* Changed: Minor design changes
* Changed: Provide translation files via WordPress Updater only
* Performance improvements and code optimizations
= 1.0.14 =
* Fixed: Display issues with some themes
* Added: Option to modify allowed filetypes for uploads
* Changed: Only the following filetypes can be uploaded by default: jpg, jpeg, gif, png, bmp, pdf
* Changed: Hide page-navigation when there is only one page
* Changed: Provide spanish translation updates via WordPress Updater
* Performance improvements and code optimizations
= 1.0.13 =
* Fixed: Closed forums were not saved correctly
* Fixed: Display issues with some themes
* Added: asgarosforum_filter_post_username filter
* Changed: Show moderator buttons only at the beginning of threads
* Changed: Minor design changes
* Performance improvements and code optimizations
= 1.0.12 =
* Fixed: Broken link of uploaded file when filename contains umlaute
* Fixed: Display issues with some themes
* Added: Option to close forums
* Changed: Categories are now ordered in the administration area
* Changed: Use default WordPress icons instead of own icon pack
* Changed: Minor design changes
* Changed: Provide portuguese (Portugal) translation updates via WordPress Updater
* Performance improvements and code optimizations
= 1.0.11 =
* Fixed: Missing page titles with some themes
* Fixed: Display issues when using apostrophes and backslashes
* Fixed: Wrong HTML escaping
* Fixed: Display issues with some themes
* Added: Portuguese (Portugal) translation (thanks to Sylvie & Bruno)
= 1.0.10 =
* Fixed: PHP errors when using a PHP version less than 5.3
* Fixed: Display issues with big post images in Internet Explorer
* Added: asgarosforum_after_thread_submit hook
* Added: asgarosforum_after_post_submit hook
* Added: asgarosforum_after_edit_submit hook
* Changed: Minor design changes
* Changed: Provide russian translation updates via WordPress Updater
* Performance improvements and code optimizations
= 1.0.9 =
* Fixed: Broken thread titles when using multi-byte characters
* Fixed: Display issues with some themes
* Added: Category access permissions
* Added: Filter asgarosforum_filter_editor_settings
* Changed: Improved compatibility with some third-party plugins
* Performance improvements and code optimizations
= 1.0.8 =
* Fixed: Insert forum at the correct shortcode position
* Fixed: Broken URLs with some third-party plugins
* Fixed: Display issues with some themes
* Added: Moderator functionality
* Added: Filter asgarosforum_filter_get_threads
* Added: Filter asgarosforum_filter_get_threads_order
* Added: Spanish translation (thanks to QuqurUxcho)
= 1.0.7 =
* Fixed: Prevent the creation of empty content
* Fixed: Hide widget for guests when access is limited to logged in users
* Fixed: Some PHP notices
* Fixed: Display issues with some themes
* Added: Option to hide the edit date
* Added: Filter hook asgarosforum_filter_post_content
* Changed: Editor error messages are now shown on the editor page
* Changed: Minor design changes
= 1.0.6 =
* Fixed: Wrong word wrap
* Fixed: Display issues with some themes
* Added: "Last edited" info to posts
* Changed: Provide hungarian translation updates via WordPress Updater
* Changed: Added author_id to asgarosforum_after_post_author action hook
* Performance improvements and code optimizations
= 1.0.5 =
* Added: Option to easily change the forum color
* Added: Option to limit access to logged in users
* Added: Action hook asgarosforum_after_post_author
* Added: Danish translation (thanks to crusie)
* Changed: Minor design changes
* Changed: Provide german translation updates via WordPress Updater
= 1.0.4 =
* Fixed: Display issues with some themes
* Fixed: Error messages were not translated
* Added: Option to highlight administrator names
* Added: Notice when user is not logged in
* Added: "Go back" link on error pages
* Added: Hungarian translation (thanks to zsebtyson)
* Changed: Permalink accessible via date instead of icon
* Updated: English and german translation
* Performance improvements and code optimizations
= 1.0.3 =
* Fixed: Icons not visible in some WordPress themes
* Fixed: Broken images inside quoted posts
* Fixed: Display issues with big images in posts
* Fixed: Prevent accessing PHP-files directly
* Added: Recent Forum Posts Widget
* Added: Bosnian translation (thanks to AntiDayton)
* Removed: Image captions
* Updated: English, german and russian translation
= 1.0.2 =
* Fixed: Dont modify page titles outside of the forum
* Fixed: Added missing translation strings
* Added: Editor button for adding images to posts
* Added: CSS design rules for better mobile device compatibility
* Added: Finnish translation (thanks to juhani.honkanen)
* Added: French translation (thanks to thomasroy)
* Added: Russian translation (thanks to ironboys)
* Changed: Minor design changes
* Updated: English and german translation
* Performance improvements and code optimizations
= 1.0.1 =
* Fixed: Added missing translation strings
* Fixed: Display issues with some default themes
* Changed: Minor design changes
* Changed: Translation slug from asgarosforum to asgaros-forum
* Updated: German translation
* Performance improvements and code optimizations
= 1.0.0 =
* First initial release
