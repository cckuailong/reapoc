<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="support-common-problems-and-solutions">
            <span class="title">Common Problems and Solutions</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-common-problems-and-solutions" class="inside">

        <h3>After restoring my database from a backup, reviews are no longer being assigned.</h3>
        <p>To fix this problem, deactivate the Site Reviews plugin and then activate it again.</p>

        <h3>Email notifications are not working</h3>
        <p>Site Reviews uses the standard WordPress mail functions to send email. However, this does not guarantee that emails will send successfully if your WordPress settings and server configuration are incorrect.</p>
        <p>To make sure that emails notifications are sent, please verify that you are sending from an email address that uses the same domain as your website. For example, if your website is <code>https://reviews.com</code>, then the email address you are sending from should end with <code>@reviews.com</code>. You can change the email address that notifications are sent from in the <code><a href="<?= glsr_admin_url('settings', 'general'); ?>">settings</a></code>. If the "Send Emails From" email address you have saved in the settings does not share the same domain as your website, you will likely experience issues sending email.</p>
        <p>If your email notifications are still not sending, I recommend that you install the <a href="https://wordpress.org/plugins/check-email/">Check Email</a> plugin to verify that your website is able to correctly send email. See also, <a href="https://www.butlerblog.com/2013/12/12/easy-smtp-email-wordpress-wp_mail/">Easy SMTP email settings for WordPress</a>.</p>

        <h3>The "Site Reviews will automatically migrate your reviews and settings to the latest version" notice keeps appearing.</h3>
        <p>This notice may appear after updating Site Reviews. If it does, please click the "Run Migration" button in the notice. If it continues to appear after reloading your pages:</p>
        <ol>
            <li>
                <p>Check the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> page. If there are any entries that say "Unknown character set", then you will need to check your wp-config.php file to see if it defines <a href="https://wordpress.org/support/article/editing-wp-config-php/#database-character-set" target="_blank">DB_CHARSET</a> or <a href="https://wordpress.org/support/article/editing-wp-config-php/#database-collation" target="_blank">DB_COLLATE</a>. If it does, either remove those entries, or make sure that the values they define are correct.</p>
            </li>
            <li>
                <p>You may be using a caching plugin which is caching the database and preventing Site Reviews from storing the migration status. To fix this, you will need to flush your database cache and/or object cache and then try again.</p>
            </li>
            <li>
                <p>You may have 3rd-party reviews that were not imported correctly. You can verify this by looking for reviews on the <code><a href="<?= glsr_admin_url(); ?>">All Reviews</a></code> page that do not have any stars. To fix this, please delete the invalid reviews, empty the trash, and then try again. Afterwards, use the provided <code><a href="<?= glsr_admin_url('tools', 'general'); ?>" data-expand="#tools-import-reviews">Import Third Party Reviews</a></code> tool to re-import the reviews.</p>
            </li>
            <li>
                <p>You may have reviews that were duplicated with a plugin (i.e. "Duplicate Page" plugin). Duplication plugins will not work correctly with Site Reviews because the review details are stored in a custom database table and then linked to the review; this allows Site Reviews to perform much faster database queries. To fix this, please delete the duplicated reviews, empty the trash, then try again.</p>
            </li>
        </ol>

        <h3>The review form is not working, the submit button just spins.</h3>
        <ol>
            <li>
                <p>Does your website have a SSL certificate? If it does, make sure that your website is configured to always use it by using a SSL plugin such as <a href="https://wordpress.org/plugins/really-simple-ssl/">Really Simple SSL</a>. Site Reviews will use HTTPS to submit a review if possible, but if your site has a valid SSL certificate and you are viewing the website using HTTP (instead of HTTPS) then the browser will detect this as a cross-domain request and prevent the review submission from completing.</p>
            </li>
            <li>
                <p>Have you enabled the reCAPTCHA setting? If you have, make sure that the "Site Key" and "Site Secret" have been entered and that they were generated for the <strong>Invisible reCAPTCHA badge</strong> (Google provides three different types of reCAPTCHA). Also, make sure that you correctly entered your website domain when creating the "Site Key" and "Site Secret".</p>
            </li>
            <li>
                <p>Have you used a security plugin to disable access to <code>/wp-admin/admin-ajax.php</code> on the frontend of your website, or have you disabled <code>/wp-admin/</code> access for non-administrators? If you have, then it's possible this is preventing Site Reviews from submitting reviews.</p>
            </li>
            <li>
                <p>If the review is created but the button keeps spinning, then your server is likely not configured to send email and is preventing WordPress from sending the email notifications. You will need to either disable the notification setting for new reviews, or fix the problem on your server.</p>
            </li>
        </ol>

        <h3>The review form is not working, it just returns an error message.</h3>
        <p>Here is a list of possible errors and what they mean:</p>
        <ol>
            <li>
                <h4 style="font-size:15px;"><?= __('The form could not be submitted. Please notify the site administrator.', 'site-reviews'); ?></h4>
                <p>This is an Ajax error that is triggered by any of the following reasons:</p>
                <ul>
                    <li>The Form Request is missing a Nonce</li>
                    <li>The Form Request failed the Nonce check</li>
                    <li>The Form Request is missing a required action</li>
                    <li>The Form Request is invalid</li>
                </ul>
                <p>To fix the nonce errors, make sure that you are not caching the review page for logged in users because Site Reviews adds a <a href="https://www.bynicolas.com/code/wordpress-nonce/" target="_blank">WordPress Nonce</a> token to the form if the user is logged in. Nonces are a standard WordPress security feature that help to prevent malicious form submissions, but they will not work if your web pages are being cached because the nonce tokens are time-sensitive and their validity expires after 12 hours.</p>
                <p>Alternatively, you may remove the Nonce check as shown in the FAQ page: <code><a data-expand="#faq-remove-nonce-check" href="<?= glsr_admin_url('documentation', 'faq'); ?>">How do I remove the WordPress Nonce check for logged in users?</a></code>.</p>
            </li>
            <li>
                <h4 style="font-size:15px;"><?= __('Your review cannot be submitted at this time.', 'site-reviews'); ?></h4>
                <p>This error is shown when the Blacklist validator prevents the review from being submitted.</p>
            </li>
            <li>
                <h4 style="font-size:15px;"><?= __('This review has been flagged as possible spam and cannot be submitted.', 'site-reviews'); ?></h4>
                <p>This error is shown when the Akismet or Honeypot validator prevents the review from being submitted.</p>
            </li>
            <li>
                <h4 style="font-size:15px;"><?= __('Your review could not be submitted and the error has been logged. Please notify the site administrator.', 'site-reviews'); ?></h4>
                <p>You should almost never see this error. It is usually triggered when WordPress encounters an error when saving the review to the database. It may also be due to an error in a custom function that you have added to your theme's functions.php file that is triggered after a review is created.</p>
                <p>If you encounter this error, please contact support for assistance.</p>
            </li>
            <li>
                <h4 style="font-size:15px;"><?= __('The review submission failed. Please notify the site administrator.', 'site-reviews'); ?></h4>
                <p>This error is shown if you have added your own custom validation which is returning false. You can override this error with your own by returning an error message instead of <code>false</code> in your custom validation logic.</p>
            </li>
            <li>
                <h4 style="font-size:15px;"><?= __('Service Unavailable.', 'site-reviews'); ?></h4>
                <p>If your website is using Cloudflare and you have configured a Firewall rule to block access to <code>wp-admin</code>, then this is likely what is causing the error. Site Reviews uses the <code>/wp-admin/admin-ajax.php</code> file to submit AJAX requests, this is standard practice for WordPress plugins. To learn how to correctly configure Cloudflare to protect your <code>wp-admin</code> without blocking access to "admin-ajax.php", please see: <a href="https://turbofuture.com/internet/Cloudflare-Firewall-Rules-for-Securing-WordPress">Cloudflare Firewall Rules for Securing WordPress</a></p>
            </li>
        </ol>
        <p>Finally, in each case you should also check the <code><a href="<?= glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code> page for any error messages that may have been logged as this will provide you additional information on the error and why it happened.</p>
    </div>
</div>
