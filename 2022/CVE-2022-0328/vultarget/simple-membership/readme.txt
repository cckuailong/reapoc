=== Simple Membership ===
Contributors: smp7, wp.insider
Donate link: https://simple-membership-plugin.com/
Tags: member, members, members only, membership, memberships, register, WordPress membership plugin, content, content protection, paypal, restrict, restrict access, Restrict content, admin, access control, subscription, teaser, protection, profile, login, login page, bbpress, stripe, braintree
Requires at least: 5.0
Requires PHP: 5.6
Tested up to: 5.8
Stable tag: 4.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple membership plugin adds membership functionality to your site. Protect members only content using content protection easily.

== Description ==

= A flexible, well-supported, and easy-to-use WordPress membership plugin for offering free and premium content from your WordPress site =

The simple membership plugin lets you protect your posts and pages so only your members can view the protected content.

= Unlimited Membership Access Levels =
Set up unlimited membership levels (example: free, silver, gold etc) and protect your posts and pages using the membership levels you create.

= User Friendly Interface for Content Protection = 
When you are editing a post or page in the WordPress editor, you can select to protect that post or page for your members.

Non-members viewing a protected page will be prompted to login or become a member.

= Have Free and Paid Memberships =
You can configure it to have free and/or paid memberships on your site. Paid membership payment is handled securely via PayPal. Membership payment can also be accepted using Stripe or Braintree payment gateways.

Both one time and recurring/subscription payments are supported for PayPal and Stripe.

You can accept one time membership payment via Braintree payment gateway.

There is also option to use PayPal smart button for membership payment.

You can enable email activation or email confirmation for the free memberships.

= Membership Payments Log = 
All the payments from your members are recorded in the plugin. You can view them anytime by visiting the payments menu from the admin dashboard.

= Developer API =

There are lots of action and filter hooks that a developer can use to customize the plugin.

There is also an API that can be used to query, create, update member accounts.

= Member Login Widget on The Sidebar =
You can easily add a member login widget on the sidebar of your site. Simply use the login form shortcode in the sidebar widget.

You can also customize the member login widget by creating a custom template file in your theme (or child theme) folder.

= Documentation =

Read the [setup documentation](https://simple-membership-plugin.com/simple-membership-documentation/) after you install the plugin to get started.

= Plugin Support =

If you have any issue with this plugin, please visit the plugin site and post it on the support forum or send us a contact:
https://simple-membership-plugin.com/

You can create a free forum user account and ask your questions.

= Miscellaneous =

* Works with any WordPress theme.
* Ability to protect photo galleries.
* Ability to protect attachment pages.
* Show teaser content to convert visitors into members.
* Comments on your protected posts will also be protected automatically.
* There is an option to enable debug logging so you can troubleshoot membership payment related issues easily (if any).
* Ability to customize the content protection message that gets shown to non-members.
* Ability to partially protect post or page content.
* You can apply protection to posts and pages in bulk.
* Ability to use merge vars in the membership email notification.
* Membership management side is handled by the plugin.
* Ability to manually approve your members.
* Ability to import WordPress users as members.
* Search for a member's profile in your WP admin dashboard.
* Filter members list by account status.
* Filter members list by membership level.
* Can be translated to any language.
* Hide the admin toolbar from the frontend of your site.
* Allow your members to delete their membership accounts.
* Send quick notification email to your members.
* Customize the password reset email for members.
* Use Google reCAPTCHA on your member registration form.
* Use Google reCAPTCHA on your member login and password reset form.
* The login and registration widgets will be responsive if you are using a responsive theme.
* Ability to restrict the commenting feature on your site to your members only.
* Front-end member registration page.
* Front-end member profiles.
* Front-end member login page.
* Option to configure after login redirection for members.
* Option to configure after registration redirect for members.
* Option to configure after logout redirection for members.
* Option force the members to use strong password.
* Option to make the users agree to your terms and conditions before they can register for a member account.
* Option to make the users agree to your privacy policy before they can register for a member account.
* Option to automatically logout the members when they close the browser.
* Ability to forward the payment notification to an external URL for further processing.

= Language Translations =

The following language translations are already available:

* English
* German
* French
* Spanish
* Spanish (Venezuela)
* Chinese
* Portuguese (Brazil)
* Portuguese (Portugal)
* Swedish
* Macedonian
* Polish
* Turkish
* Russian
* Dutch (Netherlands)
* Dutch (Belgium)
* Romanian
* Danish
* Lithuanian
* Serbian
* Japanese
* Greek
* Latvian
* Indonesian
* Hebrew
* Catalan
* Hungarian
* Bosnian (Bosnia and Herzegovina)
* Slovak
* Italian
* Norwegian
* Mexican
* Arabic
* Czech
* Finnish

You can translate the plugin using the language [translation documentation](https://simple-membership-plugin.com/translate-simple-membership-plugin/).

== Installation ==

Do the following to install the membership plugin:

1. Upload the 'simple-wp-membership.zip' file from the Plugins->Add New page in the WordPress administration panel.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

None.

== Screenshots ==

Please visit the memberhsip plugin page to view screenshots:
https://simple-membership-plugin.com/

== Changelog ==

= 4.0.7 =
- Stripe sca subscriptions enhancement: restore the custom field value from the original webhook notification (when available).
- Custom fields data (if available) is also saved in the swpm_transactions custom post type after a transaction.
- Updated the Dutch language file.
- Integration with the [WP Express Checkout plugin](https://wordpress.org/plugins/wp-express-checkout/).
- WordPress 5.8 compatibility.

= 4.0.6 =
- Added an option in the [swpm_paypal_subscription_cancel_link] shortcode to allow opening the window in a new tab.
- Added an option in the [swpm_paypal_subscription_cancel_link] shortcode to add CSS class for customization purpose.
- Added a new shortcode to display the total number of members (just display the total number). This shortcode is available in the free Miscellaneous Shortcodes addon.
- Fixed a calculation in the auto delete pending email activation data function. Thanks to @satoshi for pointing it out.
- Spelling fixes for some of the description field and error messages. Thanks to @Ronaldo for pointing it out.
- Regenerated the language translation POT file.
- Updated the Spanish language file.
- Updated the Czech language file.

= 4.0.5 =
- Added a new filter hook to allow overriding of the display_name field when adding a member via the admin interface.
- Added a new filter hook to allow overriding of the account status value when a subscription renewal payment comes in. The filter name is: swpm_account_status_for_subscription_start_date_update
- Added slovenian language translation file to the plugin.
- The {membership_level_name} email merge tag will now work for the "Notify User" feature that can be used when editing a member's profile (from the admin dashboard).
- Updated the Dutch translation file.
- Fixed the positioning of the validation result message for the username field in the "Add New Member" interface.

= 4.0.4 = 
- Added a new filter to allow overriding the auth cookie value for when the "Logout Member on Browser Close" feature is enabled.
- Updated the Swedish translation file.
- Added more sanitization to the search query of members and membership level menu in the admin interface. Thanks to @Martin Vierula for pointing it out.

= 4.0.3 =
- WP 5.6 compatibility update with jQuery script. This will fix an issue whereby error messages on registration form weren't showing correctly.

= 4.0.2 =
- Added a new filter "swpm_admin_registration_add_user_template_path"
- Added a new filter "swpm_admin_registration_edit_user_template_path"
- Added an option to auto downgrade expired members to a free level. This feature is handled via the following addon:
https://simple-membership-plugin.com/simple-membership-miscellaneous-shortcodes-addon/
- Fixed a typo in a variable name.
- The "Gender" value in the members menu is translatable. Thanks to @Th0masL for making this improvement.
- Updated the French language translation file.
- The "Bulk Account Activate & Notify" option now sends the email one by one to all the selected members (instead of a batch email). To prevent email issue when used with SMTP solution.
- Added more filters in the registration, edit profile and login forms.
- The Stripe SDK library has been updated to latest. Minimum PHP version required for it is PHP5.6
- Added new shortcode for Stripe subscription cancellation. The new shortcode is: [swpm_stripe_subscription_cancel_link]
- Added a check to prevent duplicate IPN notification creating duplicate entries. Thanks to @Th0masL for making this improvement.

= 4.0.1 =
- Added the "Button Image URL" field for the Stripe Buy Now type buttons (to allow button image customization).
- The user role options for the "Admin Dashboard Access Permission" settings field will show the translated values.
- The settings menu update capability will also respects the "Role" permission set in the "Admin Dashboard Access Permission" settings.
- Added a check to see if an username exists in the create_wp_user() function.
- Stripe SCA Subscription button configuration interface - renamed the label from "Stripe Plan ID" to "Stripe API ID" (to match with Stripe's recent interface changes).

= 4.0.0 =
- Removed the "Use WordPress Timezone" settings option from the advanced settings menu. This option can conflict with some events management type plugins.
- The plugin will now record the date values according to your WordPress timezone settings (by default).
- The debug log file will record timestamp values in the format ['Y/m/d H:i:s']. Example Value: [2020/07/24 11:58:39]
- Added help text to the "Admin Dashboard Access Permission" field to explain that it should not be used with the "Disable Access to WP Dashboard" option. 
- Added a note for when a user enables conflicting options in the advanced settings.
- Completed testing on WP 5.5 version.

= 3.9.9 =
- Added an enhancement to accept empty address value and force updating (when admin updates the address of a member profile from admin interface).

= 3.9.8 = 
- Added a new action hook 'swpm_validate_login_hash_mismatch'
- Ability to manually add a transaction record in the Payments menu of the plugin
- Added a new feature to hide the registration from to logged-in members. The new option is available in the Advanced settings menu.

= 3.9.7 =
- Added minor improvements to the get_current_page_url() function to increase compatibility with some servers.
- The mini login shortcode will also work with the "Enable Redirect to Last Page" feature from the after login redirection addon.
- Updated the Japanese language translation file.
- Minor Stripe SCA button related enhancements (added filter).
- Stripe buy now transactions (One-time payments) will now have a link to the user "profile" in the "payments" menu.
- Chinese language file name changed from zh_Hans to zh_HK.

= 3.9.6 =
- Added a new global settings for Stripe API keys in the "Payment Settings" tab. It can be used to enter your API keys (instead of individual buttons each time).
- Added a new filter to allow customization of the email activation message (if the email activation feature is enabled).
- The subsequent payments for stripe subscription will also be captured in the payments menu. Need to add the "invoice.payment_succeeded" to the webhook events monitoring. 

= 3.9.5 =
- Added a new filter (swpm_mini_login_output). It can be used to override the [swpm_mini_login] shortcode's output.
- The "Edit" link in the members menu has been renamed to "Edit/View" to make it more user-friendly.
- Updated the German language file.
- The members listing in the members menu can now be sorted by the "Access Starts" column.
- Fixed an issue with Stripe SCA buttons whereby duplicate "incomplete" entries were being created. This started happening recently from a new change that Stripe made.

= 3.9.4 =
- Commented out call to date_default_timezone_set() function for WP5.3.
- Updated some comments in the SwpmAjax class.
- Added an extra content protection check for post preview URL.

= 3.9.3 =
- Added the option to enable HTML email in the email settings menu of the plugin.
- The Stripe subscription updated event is now handled by the plugin.
- A new shortcode to create a PayPal subscription cancellation link that a member can use to view the subscription from their account and cancel.

= 3.9.2 =
- Spanish translation language files updated.
- Added more debug logging statement in the IPN handling script for easier troubleshooting.
- Fixed an issue with the new SCA stripe subscription cancellation webhook.

= 3.9.1 =
- Fixed excessive debug log output in the log file when the New SCA compatible Stripe subscription button is used.
- Stripe library is only loaded if another plugin hasn't loaded it already.

= 3.9.0 =
- Added new Stripe SCA button types. You can now go to the Payments -> Create New Button interface to create SCA compatible Stripe payment buttons.
- Please note that we have tested the new SCA compatible buttons. However, it may have some compatibility issues that we will be addressing over the next few days as we receive feedback from the users.

= 3.8.9 =
- Added a new feature in the email settings menu to allow disabling of the "Account Upgraded" email notification.

= 3.8.8 =
- The expiry date in the login widget now shows the translated date value for Non-English installs.
- Updated the German language translation files.
- Integration with the Super Socializer plugin for social login.
  https://simple-membership-plugin.com/social-login-plugin-simple-membership/

= 3.8.7 =
- Removed a PHP warning in the wp_password_reset_hook(). Thanks to John Wick for pointing this out.
- Small improvement to the PayPal subscription IPN handling script.

= 3.8.6 =
- Added nonce check to the "Addons settings" tab.

= 3.8.5 =
- Fixed CSRF issue in the Bulk Operation menu tab.
- Fixed Braintree payment issue that could occur if customer pays via PayPal.
- Fixed Stripe library conflict if other Stripe plugin is installed.
- Added support for the coupons addon.
- Added current_user_can() check to the admin menu handling function.
- Added nonce check to wp_ajax.

= 3.8.4 =
- More strings from the settings admin interface of the plugin are translatable.
- The strong password validation error message is now translatable (if you are using this feature).
- Minor enhancement in the PayPal IPN handling code.
- Fixed an issue with some profile data not updating when password is also updated at the same time.

= 3.8.3 =
- Updated Braintree PHP SDK to prevent deprecation notice when using PHP 7+.
- The "Expiry Date" of a member is now shown in the member's profile of the admin dashboard.
- Compatibility with Wordfence plugin's captcha feature.
- German translation file updated.
- Japanese translation file updated.

= 3.8.2 =
- Added membership level and account status filter in the member search function (Members menu of admin dashboard).
- Updated the Polish language translation.
- Added a filter hook in the get_current_page_url() function.

= 3.8.1 =
- [Important Note] If you are using the Braintree gateway, please take a backup before updating. Do a test transaction using Braintree gateway on live mode to make sure the new 3D Secure changes are working fine. 
- Added 3D Secure support for Braintree payment gateway buttons. It automatically tries to detect if 3DS is enabled, then shows the additional steps.
- Added note for email activation feature regarding temporary passwords storage.
- Added "swpm_email_activation_data" filter to modify user email activation data.

= 3.8.0 =
- Email activation's temporary data is now stored in an encrypted format.
- Fixed email activation data leftovers removal in the DB.
- Updated some translation strings.

= 3.7.9 =
- Added new shortcode [swpm_show_after_login_page_link] via the swpm misc shortcodes addon.
- More characters are now allowed in the "username" field.
- Fixed a minor bug with the plugin not finding the corresponding member's profile when a subscritpion is canceled.

= 3.7.8 =
- Added a new feature to allow forwarding of the payment notification to an external URL. This option can be found in the "Advanced Settings" of the plugin.
- The "Forgot Password?" translation string in the login form will allow the "?" character to be translated/customized.
- Fixed a PHP7 related warning.
- Updated some translation strings.
- Corrected an spelling mistake.

= 3.7.7 =
- Added a new filter hook that can be used to override the account status of the email activation feature. swpm_activation_feature_override_account_status
- Added email activation support for Form Builder.

= 3.7.6 =
- Updated the DB version number.
- Updated the German language file.

= 3.7.5.1 =
- Fixed a minor bug with the new email activation feature.
- Changed Stripe plan name to use the nickname.

= 3.7.5 =
- Added a new feature to enable email activation/confirmation. Useful if you want to enable this for your free membership level.
- Username can only contain: letters, numbers and .-*@. This is so the username field accepts what is allowed by WordPress for that field.
- Added a new utility function.
- Added a function to show formatted expiry date.

= 3.7.4 =
- Stripe Subscription now considers plan trial period settings.
- Added CSS class names to the fields in the admin add/edit members interface.
- Added more translatable strings to the POT file.
- WordPress 5.0 compatibility

= 3.7.3 =
- Created a new free addon to offer full page style protection. https://simple-membership-plugin.com/full-page-protection-addon-simple-membership/
- The mini login shortcode output is now translatable
- Fixed Smart Checkout buttons were not working in live mode under some circumstances
- Fixed minor display issues for PayPal Smart Checkout buttons

= 3.7.2 =
- Added a new feature that allows you to automatically logout the users when they close the browser.
- Added support for Two-Factor Authentication addon.
- Added a new utility function.
- Improved the social login functionality.

= 3.7.1 =
- Moved the IPN handling code from "init" hook to "wp_loaded" hook for better compatibility.
- The configuration fields for "Publishable" and "Secret" keys for Stripe has been swapped. This will align them better with how you get the info from your Stripe account.

= 3.7.0 =
- Added PayPal smart checkout button option. https://simple-membership-plugin.com/creating-paypal-smart-checkout-buttons-for-membership-payment/
- Added a new filter hook swpm_edit_profile_form_before_username
- Added a new filter hook swpm_edit_profile_form_before_submit

= 3.6.9 =
- Added a new feature that allows you to configure an after logout redirect URL. This new feature is available in the "Advanced Settings" tab of the plugin.

= 3.6.8 =
- Fixed an Warning: count(): Parameter must be an array or an object that implements Countable.

= 3.6.7 =
- Captcha addon has the "Light" or "Dark" theme options. It also has the compact captcha option.
- German language translation file updated. Thanks to Herbert Heupke.
- Membership level update action will update the member's wp user role (if specified in the membership level).
- Fixed rare issue when selected currency for Stripe buttons was ignored.
- Fixed typo in configuration parameter for Stripe buttons that was producing warning in browser console.
- Stripe is now tries to detect visitor's language and use it in payment pop-up.

= 3.6.6 =
- Added a new feature to show a terms and conditon checkbox. Users must agree to your terms before they can register for an account. Useful for GDPR.
- Added a new feature to show a privacy policy checkbox. Users must agree to your privacy policy before they can register for an account. Useful for GDPR.
- Last login date of the members are now shown in the members listing page in the admin dashboard.
- Added a feature in the tools menu of the plugin to re-create the required pages of the membership plugin.
- Fixed a typo in the country name "Colombia".

= 3.6.5 =
- Updated the Swedish translation file. Thanks to Andreas Damberg for submitting the translation file.
- Developer API to query, update, create member accounts.
- Added a new feature in the advanced settings to allow automatic member creation for WP users that get created by other plugins.
- Added a couple of utility functions in the membership level class.

= 3.6.4 = 
- Added a new shortcode to show a mini login form. This can be useful to show a mini login form on the sidebar, header or footer area of the site.
- Fixed an issue with the auto login after registration feature when used with form builder addon.

= 3.6.3 =
- Added a new feature to enable auto login after registration for the members.

= 3.6.2 =
- Added a new feature to enforce strong password on the password field. When this option is enabled in the advanced settings, the members will be required to use a strong password.

= 3.6.1 =
- Langauge POT file regenerated.
- Added a new filter so the registration complete email body can be overriden and the email can be disabled using a small tweak.
- The member gets logged out of the wp user session when the password is changed from profile edit page.
- Force logout is called when bad login hash is detected.
- Fixed a bug in the tools menu (prompt to complete registration email sending).

= 3.6.0 =
- Added a new feature to allow configuration of an after registration redirect. The advanced settings tab has the new option.
- Added extra help text in the email settings menu.
- The subscription cancellation code will now use the subscriber ID.
- Changed the "Edit Membership Level" button label to say "Save Membership Level"
- Updated the local copy of the German language file. Thanks to Herbert Heupke.
- Created a new free addon to handle bulk member import from a CSV file.
- Added a new filter to allow overriding of the after registration redirection URL.

= 3.5.9 =
- Japanese Yen currency fix for Stripe subscription.
- Added a new email merge tag for membership level name {membership_level_name}
- There is a new option called "Force WP User Synchronization" in the advanced settings menu of the plugin.
- Fixed an warning that can appear sometime when updating the advanced settings interface.

= 3.5.8 =
- Changed the "Edit Member" button text to "Save Data" in the admin member edit interface.
- Added a new function to logout the user from the swpm system if the corresponding wp user session is logged out.
- The company name field will now be shown in the edit profile form.
- The stripe button configuration allows you to enable an option to collect billing address
- The country field is now a dropdown option.
- Added a filter hook for the paypal email address in the payment button.

= 3.5.7 =
- Added updated German translation file.
- Fixed a permissions check bug (thanks to Neb).
- Fixed a potential XSS vulnerability.

= 3.5.6 =
- Russian Ruble (₽) currency added for PayPal Buy Now and Subscription buttons. 
- "Return URL" and "Button Image URL" options for Stripe Subscription button are now properly saved and handled.
- Stripe Subscription is now automatically cancelled when a member deletes his account. Will not work for members registered prior to this update.

= 3.5.5 =
- Updated the language text domain from "swpm" to "simple-membership". This will make it easy for the translation to be handled via https://translate.wordpress.org/

= 3.5.4 =
- Added a check for PHP5.4 to detect if a server is using very old version of PHP and show an appropriate warning message. This will prevent an error on server's using old PHP version.
- Added a new feature in the paypal button configuration so you can specify a custom checkout page header/logo.

= 3.5.3 =
- The login widget now shows a link to the edit profile page (for the logged in members).
- Applied a fix in the validation JS code that was preventing paid membership registration to be completed properly if the email field was skipped.
- Added Stripe subscription checkout option.

= 3.5.2 =
- There is a new feature to apply protection to posts and pages in bulk. The following documentation explains how to use this feature:
https://simple-membership-plugin.com/apply-protection-posts-pages-bulk/

- Added a new utility function in the SwpmMemberUtils class.

= 3.5.1 =
- Added a new action hook (swpm_before_login_request_is_processed) that can be used to check stuff before the login request is processed by the plugin.
- Stripe button: zero-decimal currencies (like JPY) are no longer multiplied by 100.
- Turned off autocomplete for the email input field in the registration and edit profile forms.

= 3.5.0 =
- Updated the Spanish language translation file.
- Added translation for Spanish (Venezuela). Translation was done by Santos Guerra.
- Improved the current page URL check for the renewal page.
- The {member_since} and {subscription_starts} email merge tags will output a formatted date value.
- Turned off autocomplete in the edit profile's password field.

= 3.4.9 =
- Fixed a member profile edit issue that was introduced in the previous version from JavaScript library update.

= 3.4.8 =
- The password reset form will be hidden after a successful reset request.
- Added a new utility function to write array content to the debug log file.
- Added apply_filters() for all email subjects and bodies. This should be useful for a multi-lingual site.
- Updated the validation JS library to the latest.
- Updated the French language translation file.
- Updated the Spanish language translation file.
- Added Czech language translation to the plugin. The translation was submitted by Novakovska Eva.
- Added Finnish language translation to the plugin. The translation was submitted by Lars Timberg.
- The password field in the edit profile page has been changed to a "password" type field.

= 3.4.7 = 
- There is a new feature for applying partial or section protection to posts and pages. This feature is available via a free addon.
- Removed bundled jquery.tools18.min.js, switched to built-in WP jQuery UI.
- Fixed a typo in the manage content protection menu tab.
- Created a free addon for misc shortcodes.

= 3.4.6 = 
- Added Arabic language translation to the plugin. The translation was submitted by Hanin Fatani.
- Added an email tag {primary_address} that can be used in the notification email when address field is used in the form builder addon.
- Removed the unnecessary $_SERVER["REQUEST_URI"] value from the post action.
- Added reCAPTCHA support on the password reset form (for reCAPTCHA addon).
- Added an option to specify a custom CSS class for Stripe and Braintree buttons to customize the button style. (The new shortcode parameter is "class").

= 3.4.5 =
- Added a new action hook that gets triggered when a member is added via the "Add Member" menu of admin dashboard.
- The mailchimp addon will now add users to a list when added via the admin dashboard.
- The paypal checkout custom field value will be encoded.
- Fixed warning - Non-static method SimpleWpMembership::deactivate() should not be called statically.

= 3.4.4 =
- Updated the Spanish language translation file.
- The {password} email merge tag will work in the admin notification email when a member submits the registration form.
- Excluded the disable dashboard feature check from AJAX request.
- Added a new filter to allow overriding of the registration complete email dynamically using custom code.
- Added a placeholder text message for the password field in the "Edit Profile" page.
- Added a new settings field to allow customization of the "Admin Notification Email Subject".
- The manage members menu can be sorted using first name and last name.
- Minor typo fix.

= 3.4.3 =
- Improved the formatting for the content that appears before the "more" tag on a more tag protected post.
- Added a new feature to disable wp dashboard access for non-admin wp users. You can find this option under the general settings tab.
- Added Mexican language translation file. The translation was submitted by Enrique alfonso.
- Re-added the local copy of the Spanish translation files (Someone submitted incorrect translation to the translate.wordpress.org site overwriting the good translation)

= 3.4.2 = 
- Fixed an issue with some sites getting a blank screen for the members menu due to a PHP short tag usage.
- Added a new action hook that gets triggered after a member edits the profile from the edit profile page.
- The edit_profile_front_end() function now returns true or false based on if the form was submitted successfully or not.
- Added extra comment in the IPN handling code.

= 3.4.1 =
- Added an option to bulk update the "Membership Level" value of a group of members.
- Added an option to bulk update the "Access Starts" date value of a group of members.
- Added Norwegian language translation file. The translation was submitted by Tom Nordstrønen.

= 3.4.0 =
- Updated the Italian language file. Thanks to Nicolò Monili for updating the translation.
- Deleted the German language files from the plugin folder so it can pull the language from translate.wordpress.org
- Improved the member search functionality when used with pagination.
- Added more sanitization on the registration form.
- Added a few utility functions to the membership level utility class.
- Google reCAPTCHA addon updated to enable captcha on the login form.
- Stripe Checkout: The plugin now sets the "receipt_email" parameter for Stripe checkout so a receipt gets sent from Stripe.

= 3.3.9 =
- Deleted the Spanish language files from the plugin folder so it can pull the language from translate.wordpress.org
- WordPress 4.7 compatibility.
- Regenerated the POT file.
- The after login redirection now uses home_url() instead of site_url(). The URL also gets passed via a filter.
- Added a new filter for the after logout redirection URL.
- Renamed the swpm-ja_JP language filename to swpmp-ja
- Added the Braintree payment gateway so you can accept membership payments using Braintree. Details in the following documentation:
  https://simple-membership-plugin.com/create-braintree-buy-now-button-for-membership-payment/

= 3.3.8 =
- The account renewal payment will take into account any remaining time (when the user's level is using a duration type expiry).
- The members can now user their email address (instead of username) and password to log into the site. The username field of the member login form will accept either the email address or the username.
- The set_user_role action hook will not be triggered by the plugin as the wp_update_user() function will take care of it automatically.

= 3.3.7 =
- Added Italian language translation file. The translation was submitted by Roberto Paura.
- Improved the paypal refund handling.
- The subscription payment cancellation sequence/code has been improved. Details in the following documentation:
https://simple-membership-plugin.com/what-happens-when-paypal-subscription-cancelled/

= 3.3.6 =
- Added a new option so the admin notification email content can be customized from the email settings menu of the plugin.

= 3.3.5 =
- Added nonce check on the edit profile form.
- Added an extra check for the membership level data on the registration form.
- Minimum WordPress version requirement updated to v4.0.

= 3.3.4 =
- If you are editing the post protection settings of a post that belongs to a protected category, it will now show a message in the protection settings box to let you know.
- Improved nonce check with the protection settings saving functionality.

= 3.3.3 =
- Improvements for a recurring payment received transaction. It will update the profile even if the membership level setting is using a duration type value.
- Fixed CSRF vulnerabilies.
- Added nonce verification check in various admin side actions.
- Added is_admin() check for various admin side actions.
- Added current_user_can() check for various admin side actions.

= 3.3.2 =
- You can now view a member's last accessed date and time value by editing the member's profile from the admin dashboard.
- The "Registration Successful" message can now be customized using the custom messages addon.
- The edit profile template file can now also be overridden using the swpm_load_template_files filter.
- Updated the Dutch language translation file.
- Added Estonian language translation file.
- Updated the Stripe payment gateway library to the latest version.

= 3.3.1 =
- Added an option in the advanced settings menu to use the timezone value specified in your WordPress General Settings interface.
- WordPress 4.6 compatibility.

= 3.3.0 =
- Updated the Hungarian language file.
- Improved input sanitization.

= 3.2.9 =
- Lowered the priority of "the_content" filter processing (this should be helpful for compatibility with some of the content builder type plugins).
- Added Slovak language translation file. The translation was submitted by Marek Kucak.
- XSS vulnerability fix for page request parameter.

= 3.2.8 =
- Added Stripe Buy Now option for membership payment.
  Stripe payment usage documentation: https://simple-membership-plugin.com/create-stripe-buy-now-button-for-membership-payment/
- Added a notice in the admin interface to notify you when you keep the sandbox payment mode enabled.
- Added a check in the authentication system to stop login request processing if the user is already logged into the site as ADMIN.
- The payment button shortcode will now check to make sure you entered a valid button ID in the shortcode.
- Fixed a couple of minor debug notice warnings.
- Bugfix: Admin Dashboard Access Permission setting not saving correctly.

= 3.2.7 =
- Added a new option in the plugin settings so you can specify other WP user role (example: editor) to be able to use/see the plugin's admin interface.
- Added a "user profile delete" option in the admin profile edit interface of the plugin. Admins can use it to delete a user record while in the member edit interface.
- Added a new option so the member registration complete email notification can be sent to multiple site admins.
- Added Bosnian language translation file. The translation was submitted by Rejhan Puskar.
- Updated the Japanese language file.
- Updated the Dutch language file. Thanks to R.H.J. Roelofsen.

= 3.2.6 =
- Added Hungarian language translation file. The translation was submitted by Laura Szitar.
- Improved the members menu navigation menu so the tabs are always visible (even when you go to the add or edit members screen).
- Added 2 new action hooks (They are triggered when subscription is cancelled and when a recurring payment is received).
- Improved the membership levels navigation menu tabs.
- The "Edit Member" interface now shows the member ID of the currently editing member.

= 3.2.5 =
- Added a new feature to enable redirection to the last page after login (where they clicked the login link). 
This new option is available in the after login redirection addon.
https://wordpress.org/plugins/simple-membership-after-login-redirection/

= 3.2.4 =
- Fixed a bug with attachment protection showing an error message.

= 3.2.3 =
- Added a new option so you can configure a membership account renewal page in the plugin.
- The account expiry message will include the renewal page link (if you configure the renewal page).
- Removed login link from the comment protection message. You can customize the comment protection message using the custom message addon.
- Updated the Russian language file. Thanks to @dimabuko for updating the language file.
- Updated the Portuguese language file. Thanks to @Juan for updating the language file.
- Added a new addon for better custom post type protection.
- Made an improvement to the wp user delete function.
- More tag protection check improvements.
- Account with "inactive" status can also log into the site if the "Allows expired login" feature is enabled.
- Updated the PayPal IPN validation code so it is compatible with the upcoming PayPal changes.

= 3.2.2 =
- New feature to only allow the members of the site to be able to post a comment.
- Moved the "Allow Account Deletion" option to the Advanced Settings tab of the plugin.
- Moved the "Auto Delete Pending Account" option to the Advanced Settings tab of the plugin.
- WordPress 4.5 compatibility.

= 3.2.1 =
- Added a new filter (swpm_transactions_menu_items_per_page) that can be used to customize the number of items that is listed in the transactions menu.
- Added more sorting option in the transactions table.
- Added sanitization for the sort inputs in the member transactions table.
- Fixed an issue with the auto delete pending account settings.
- Changed admin heading structure from h2 to h1.

= 3.2.0 =
- Added Catalan language translation file. The translation was submitted by Josep Ramon.
- Custom post type categories are also listed in the category protection menu.
- Added a new filter (swpm_members_menu_items_per_page) that can be used to customize the number of items that is listed in the members menu.
- The default number of items listed in the members menu by default has been increased to 50.
- Comment protection fix for posts using "more" tag.
- Comments of protected posts are also protected.
- Added CSS classes for all the field rows in the standard membership registration form.
- Added CSS classes for all the field rows in the edit profile form.

= 3.1.9 =
- Added new merge vars that can be used in the registration complete email. These are {member_id}, {account_state}, {email}, {member_since}
- Added trailingslashit() to the after logout redirect URL.
- Created a new extension to show member info. [usage documentation](https://simple-membership-plugin.com/simple-membership-addon-show-member-info/)
- A new cookie is dropped when a member logs into the site. It can be used for caching plugin compatibility.
- Added a new function to load the template for login widget and password reset form. This will allow customization of the login widget by adding the custom template to the theme folder.

= 3.1.8 =
- Improved the members and payments menu rendering for smaller screen devices.
- Added a utility function to easily output a formatted date in the plugin according to the WordPress's date format settings.
- Fixed a bug in the wp username and email validation functionality. Thanks to Klaas van der Linden for pointing it out.
- The membership password reset form has been restructured (the HTML table has been removed).

= 3.1.7 =
- Added debug logging for after a password is reset successfully.
- The plugin will prevent WordPress's default password reset email notification from going out when a member resets the password.
- Added a new bulk action item. Activate account and notify members in bulk. Customize the activation email from the email settings menu of the plugin.
- Added validation in the bulk operation function to check and make sure that multiple records were selected before trying the bulk action.
- Updated the Portuguese (Brazil) language translation file. The translation was updated by Fernando Telles.
- Updated the Tools interface of the plugin.
- The members list can now be filtered by account status (from the members interface)
- The members list now shows "incomplete" keyword in the username field for the member profiles that are incomplete.
- Added an "Add Member" tab in the members menu.

= 3.1.6 =
- Added a new feature to show the admin toolbar to admin users only.
- Added CSS for membership buy buttons to force their width and height to be auto.
- Added a few utility functions to retrieve a member's record from custom PHP code (useful for developers).
- Added the free Google recaptcha addon for registration forms.

= 3.1.5 =
- Added a new shortcode [swpm_show_expiry_date] to show the logged-in member's expiry details.
- The search feature in the members menu will search the company name, city, state, country fields also.
- The subscription profile ID (if any) for subscription payment is now shown in the "payments" interface of the plugin.
- Added new filter hook so additional fields can be added to the payment button form (example: specify country or language code).
- Updated the language POT file.

= 3.1.4 =
- Added an option in the "Payments" menu to link a payment to the corresponding membership profile (when applicable).
- Fixed an issue with the subscriber ID not saving with the member profile (for PayPal subscription payments).
- Added Hebrew language translation file. The translation was submitted by Merom Harpaz.

= 3.1.3 =
- Added Indonesian language translation file. The translation was submitted by Hermanudin.
- Removed a couple of "notice" warnings from the installer.
- Added option to bulk change members account status.
- Updated the CSS class for postbox h3 elements.
- The member search feature (in the admin side) can now search the list based on email address.

= 3.1.2 =
- Added more sortable columns in the members menu.
- Adjusted the CSS for the registration and edit profile forms so they render better in small screen devices.
- Changed the "User name" string to "Username"

= 3.1.1 =
- Fix for some special characters in the email not getting decoded correctly.
- Updated the membership upgrade email header to use the "from email address" value from the email settings.

= 3.1.0 =
- Fixed an email validation issue for when the plugin is used with the form builder addon.

= 3.0.9 =
- Updated the Spanish language translation file.
- Updated the POT file for language translation.
- Added Dutch (Belgium) language translation file. The translation was submitted by Johan Calu.
- Fixed an email validation issue.

= 3.0.8 =
- Added Latvian language translation file. The translation was submitted by Uldis Kalnins.
- Updated the POT file for language translation.
- Added a placeholder get_real_ip_addr() function for backwards compatibility.

= 3.0.7 =
- Fixed a typo in the password reset message.
- Removed the get_real_ip_addr() function (using get_user_ip_address() from the "SwpmUtils" class).
- Simplified the message class interaction.
- Added CSS classes to the registration, edit profile and login submit buttons.
- Added confirmation in the member's menu bulk operation function.
- Fixed the bulk delete and delete functionality in the members list menu.
- Fixed the category protection confirmation message.
- Added Greek language translation file. The translation was submitted by Christos Papafilopoulos.

= 3.0.6 =
- Corrected the Danish language file name.
- Fixed an issue with the profile update success message sticking.

= 3.0.5 =
- Added a fix to prevent an error from showing when a member record is edited from the admin side.

= 3.0.4 =
- Added a new utility function so a member's particular info can be retrieved using this function.
- Added extra guard to prevent the following error "Call to member function get () on a non object".
- Updated the langguage POT file.

= 3.0.3 =
- Increased the database character limit size of the user_name field.
- Refactored the 'swpm_registration_form_override' filter.
- Added integration with iDevAffiliate.
- Added integration with Affiliate Platform plugin.

= 3.0.2 =
- Added a new shortcode that can be used on your thank you page. This will allow your users to complete paid registration from the thank you page after payment.
- The last accessed from IP address of a member is shown to the admin in the member edit screen.
- The debug log (if enabled) for authentication request is written to the "log-auth.txt" file.
- Fixed a bug with the bulk member delete option from the bottom bulk action form.
- Fixed a bug with the bulk membership level delete option from the bottom bulk action form.

= 3.0.1 =
- Added a new CSS class to the registration complete message.
- Added Portuguese (Portugal) language translation file. The translation was submitted by Edgar Sprecher.
- Replaced mysql_real_escape_string() with esc_sql()
- Members list in the admin is now sorted by member_id by default.
- Added a new filter in the registration form so Google recaptcha can be added to it.

= 3.0 =
- Updated the swedish langauge translation
- Added a new option to enable opening of the PayPal buy button in a new window (using the "new_window" parameter in the shortcode).
- You can now create and configure PayPal Subscription button for membership payment from the payments menu.

= 2.2.9 =
- Added a new feature to customize the password reset email.
- Added a new feature to customize the admin notification email address.
- Improved the help text for a few of the email settings fields.
- Updated the message that gets displayed after a member updates the profile.

= 2.2.8 =
- Updated the swedish language translation file.
- Code refactoring: moved all the init hook tasks to a separate class.
- Increased the size of admin nav tab menu items so they are easy to see.
- Made all the admin menu title size consistent accross all the menus.
- Updated the admin menu dashicon icon to a nicer looking one.
- You can now create and configure PayPal buy now button for membership payment from the payments menu.

= 2.2.7 =
- Added Japanese language translation to the plugin. The translation was submitted by Mana.
- Added Serbian language translation to the plugin. The translation was submitted by Zoran Milijanovic.
- All member fields will be loaded in the edit page (instead of just two).

= 2.2.6 =
- Fixed an issue with the category protection menu after the class refactoring work.
- Fixed the unique key in the DB table

= 2.2.5 =
- Refactored all the class names to use the "swpm" slug to remove potential conflict with other plugins with similar class names.

= 2.2.4 =
- Fixed an issue with not being able to unprotect the category protection.
- Minor refactoring work with the classes.

= 2.2.3 =
- Updated the category protection interface to use the get_terms() function.
- Added a new Utility class that has some helpful functions (example: check if a member is logged into the site). 

= 2.2.2 =
- All the membership payments are now recorded in the payments table.
- Added a new menu item (Payments) to show all the membership payments and transactions.
- Added Lithuanian language translation to the plugin. The translation was submitted by Daiva Pakalne.
- Fixed an invalid argument error.

= 2.2.1 =
- Added a new table for logging the membership payments/transactions in the future.
- Made some enhancements in the installer class so it can handle both the WP Multi-site and single site setup via the same function.

= 2.2 =
- Added a new feature to allow expired members to be able to log into the system (to allow easy account renewal).
- The email address value of a member is now editable from the admin dashboard and in the profile edit form.
- Added CSS classes around some of the messages for styling purpose.
- Some translation updates.

= 2.1.9 =
- Improved the password reset functionality.
- Improved the message that gets displayed after the password reset functionality is used.
- Updated the Portuguese (Brazil) language file.
- Improved the user login handling code.

= 2.1.8 =
- Improved the after logout redirection so it uses the home_url() value.
- Fixed a bug in the member table sorting functionality.
- The members table can now be sorted using ID column.


= 2.1.7 =
- Added a new feature to automatically delete pending membership accounts that are older than 1 or 2 months.
- Fixed an issue with the send notification to admin email settings not saving.

= 2.1.6 =
- Fixed a bug with new membership level creation with a number of days or weeks duration value.

= 2.1.5 =
- Improved the attachment protection so it doesn't protect when viewing from the admin side also.
- Removed a dubug dump statement.

= 2.1.4 =
- Improved the login authentication handler logic.
- Fixed the restricted image icon URL.
- Updated the restricted attachment icon to use a better one.

= 2.1.3 =
- Added a new feature to allow the members to delete their accounts.

= 2.1.2 =
- Updated the membership subscription payment cancellation handler and made it more robust.
- Added an option in the settings to reset the debug log files.

= 2.1.1 =
- Enhanced the username exists function query.
- Updated one of the notice messages.

= 2.1 =
- Changed the PHP short tags to the standard tags
- Updated a message in the settings to make the usage instruction clear.
- Corrected a version number value.

= 2.0 =
- Improved some of the default content protection messages.
- Added Danish language translation to the plugin. The translation was submitted by Niels Boje Lund.

= 1.9.9 =
- WP Multi-site network activation error fix.

= 1.9.8 =
- Fixed an issue with the phone number not saving.
- Fixed an issue with the new fixed membership expiry date feature.

= 1.9.7 =
- Minor UI fix in the add new membership level menu.

= 1.9.6 =
- Added a new feature to allow fixed expiry date for membership levels.
- Added Russian language translation to the plugin. The translation was submitted by Vladimir Vaulin.
- Added Dutch language translation to the plugin. The translation was submitted by Henk Rostohar.
- Added Romanian language translation to the plugin. The translation was submitted by Iulian Cazangiu.
- Some minor code refactoring.

= 1.9.5 =
- Added a check to show the content of a protected post/page if the admin is previewing the post or page.
- Fixed an issue with the quick notification email feature not filtering the email shortcodes.
- Improved the login form's HTML and CSS.

= 1.9.4 =
- Added a new feature to send an email notification to a member when you edit a user's record. This will be helpful to notify members when you activate their account.
- Fixed an issue with "pending" member account getting set to active when the record is edited from admin side.

= 1.9.3 =
- Fixed an issue with the featured image not showing properly for some protected blog posts.

= 1.9.2 =
- Fixed the edit link in the member search interface.

= 1.9.1 =
- Added Turkish language translation to the plugin. The translation was submitted by Murat SEYISOGLU.
- WordPrss 4.1 compatibility.

= 1.9.0 =
- Fixed a bug in the default account setting option (the option to do manual approval for membership).
- Added Polish language translation to the plugin. The translation was submitted by Maytki.
- Added Macedonian language translation to the plugin. The translation was submitted by I. Ivanov.

= 1.8.9 =
- Added a new feature so you can set the default account status of your members. This can useful if you want to manually approve members after they signup.

= 1.8.8 =
- Fixed an issue with the account expiry when it is set to 1 year.

= 1.8.7 =
- Updated the registration form validation code to not accept apostrophe character in the username field.
- Added a new tab for showing addon settings options (some of the addons will be able to utilize this settings tab).
- Added a new action hook in the addon settings tab.
- Moved the plugin's main class initialization code outside of the plugins_loaded hook.

= 1.8.6 =
- Fixed an email validation issue with paid membership registration process.
- Added a new free addon to customize the protected content message.

= 1.8.5 =
- Added category protection feature under the membership level menu.
- Fixed a bug with paid membership paypal IPN processing code.

= 1.8.4 =
- The Password field won't use the browser's autofill option in the admin interface when editing a member info.

= 1.8.3 =
- Added Swedish language translation to the plugin. The translation was submitted by Geson Perry.
- There is now a cronjob in the plugin to expire the member profiles in the background.
- Released a new addon - https://simple-membership-plugin.com/simple-membership-registration-form-shortcode-generator/
- Added a menu called "Add-ons" for listing all the extensions of this plugin.

= 1.8.2 =
- Updated the members expiry check code at the time of login and made it more robust.

= 1.8.1 =
- MySQL database character set and collation values are read from the system when creating the tables.
- Added German language translation file to the plugin.
- Some code refactoring work.
- Added a new feature to allow admins to create a registration form for a particular membership level.

= 1.8.0 =
- Added a new feature called "more tag protection" to enable teaser content. Read the [teaser content documentation](https://simple-membership-plugin.com/creating-teaser-content-membership-site/) for more info.
- Added Portuguese (Brazil) language translation to the plugin. The translation was submitted by Rachel Oakes.
- Added cookiehash definition check (in case it is not defined already).

= 1.7.9 =
- Added Spanish language translation to the plugin. The translation was submitted by David Sanchez.
- Removed some hardcoded path from the auth class.
- WordPress 4.0 compatibility

= 1.7.8 =
- Architecture improvement for the [WP User import addon](https://simple-membership-plugin.com/import-existing-wordpress-users-simple-membership-plugin/)
- Updated the POT file with the new translation strings

= 1.7.7 =
- The plugin will now show the member account expiry date in the login widget (when a user is logged into the site).
- Added a couple of filters to the plugin.

= 1.7.6 =
- Fixed an issue with hiding the admin-bar. It will never be shown to non-members.
- Renamed the chinese language file to correct the name.
- Removed a lot of fields from the front-end registration form (after user feedback). The membership registration form is now a lot simpler with just a few fields.
- Fixed a bug with the member search option in the admin dashboard.
- Added a few new action hooks and filters.
- Fixed a bug with the media attachment protection.

= 1.7.5 = 
- Fixed an issue with language file loading.

= 1.7.4 =
- Added capability to use any of the shortcodes (example: Login widget) in the sidebar text widget.

= 1.7.3 =
- Added french language translation to the plugin. The translation was submitted by Zeb.
- Fixed a few language textdomain issue.
- Fixed an issue with the the registration and login page shortcode (On some sites the registration form wasn't visible.)
- Added simplified Chinese language translation to the plugin. The translation was submitted by Ben.

= 1.7.2 =
- Added a new hook after the plugin's admin menu is rendered so addons can hook into the main plugin menu.
- Fixed another PHP 5.2 code compatibility issue.
- Fixed an issue with the bulk member delete functionality.

= 1.7.1 =
- Fixed another PHP 5.2 code compatibility issue.
- Updated the plugin's language file template.

= 1.7 = 
- Tweaked code to make it compatible with PHP 5.2 (previously PHP 5.3 was the requirement).
- Added checks for checking if a WP user account already exists with the chosen username (when a member registers).
- Fixed a few translation strings.

= 1.6 =
- Added comment protection. Comments on your protected posts will also be protected automatically.
- Added a new feature to hide the admin toolbar for logged in users of the site.
- Bug fix: password reset email not sent correctly
- Bug fix: page rendering issue after the member updates the profile.

= 1.5.1 = 
- Compatibility with the after login redirection addon:
http://wordpress.org/plugins/simple-membership-after-login-redirection/

= 1.5 =
- Fixed a bug with sending member email when added via admin dashboard.
- Fixed a bug with general settings values resetting.
- Added a few action hooks to the plugin.

= 1.4 =
- Refactored some code to enhance the architecture. This will help us add some good features in the future.
- Added debug logger to help troubleshoot after membership payment tasks.
- Added a new action hook for after paypal IPN is processed.

= 1.3 =
- Fixed a bug with premium membership registration.

= 1.2 =
- First commit to WordPress repository.

== Upgrade Notice ==
If you are using the form builder addon, then that addon will need to be upgraded to v1.1 also.

== Arbitrary section ==
None
