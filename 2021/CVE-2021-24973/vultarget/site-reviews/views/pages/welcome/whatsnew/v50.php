<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_0_0">
            <span class="title">Version 5.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_0_0" class="inside">
        <p><em>Initial Release Date &mdash; October 22nd, 2020</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added <code>assigned_posts</code> shortcode option, this <strong>replaces</strong> the <code>assign_to</code> and <code>assigned_to</code> options and allows you to assign reviews to multiple Post IDs</li>
            <li>Added <code>assigned_terms</code> shortcode option, this <strong>replaces</strong> the <code>category</code> option and allows you to assign reviews to multiple Categories</li>
            <li>Added <code>assigned_users</code> shortcode option, this allows you to assign reviews to multiple User IDs</li>
            <li>Added <em>Delete data on uninstall</em> option to selectively delete plugin data when removing the plugin</li>
            <li>Added <em>Import Third Party Reviews</em> tool</li>
            <li>Added <em>Send Emails From</em> option to send notifications from a custom email address</li>
            <li>Added <em>Test IP Address Detection</em> tool</li>
            <li>Added <a href="https://wordpress.org/support/article/wordpress-privacy/#privacy-policy-editing-helper">suggested privacy policy content</a></li>
            <li>Added <a href="https://wordpress.org/support/article/wordpress-privacy/#erase-personal-data-tool">WordPress Personal Data Eraser</a> integration</li>
            <li>Added <a href="https://wordpress.org/support/article/wordpress-privacy/#export-personal-data-tool">WordPress Personal Data Exporter</a> integration</li>
            <li>Added <a href="https://wordpress.org/support/article/revisions/">WordPress Revisions</a> integration</li>
            <li>Site Reviews now uses custom database tables, however you may still use the WordPress Export/Import tools to export and import your reviews as before</li>
            <li>The Review Details metabox now allows you to modify any value</li>
            <li>The <code>site-reviews/after/submission</code> javascript event now contains the submitted review</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Changed the settings to use the WordPress "Disallowed Comment Keys" by default</li>
            <li>Increased the minimum PHP version to 5.6.20</li>
            <li>Increased the minimum WordPress version to 5.5</li>
            <li>Refreshed the stars SVG images</li>
            <li>Renamed the <code>assigned_to</code> <strong>hide option</strong> which hides assigned links in reviews to <code>assigned_links</code> (i.e. [site_reviews hide="assigned_links"])</li>
            <li>Renamed the <code>glsr_get_rating()</code> helper function to <code>glsr_get_ratings()</code></li>
            <li>Replaced the <code>assign_to</code> and <code>assigned_to</code> shortcode options with the <code>assigned_posts</code> option</li>
            <li>Replaced the <code>category</code> shortcode option with <code>assigned_terms</code> option</li>
            <li>Review limit validation now performs strict checking for assigned posts, categories and users (AND instead of OR).</li>
            <li>The <code>site-reviews/rating/average</code> filter hook argument order has changed (see the <a data-expand="#upgrade-v5_0_0" href="<?= admin_url('index.php?page='.glsr()->id.'-welcome&tab=upgrade-guide'); ?>">Upgrade Guide</a>).</li>
            <li>The Translations Settings search results are now restricted to public text that is actually shown on your website, if you would like to change plugin text shown in the WordPress admin, you should use the Loco Translate plugin instead.</li>
        </ul>
        <h4>🛠 Tweaks</h4>
        <ul>
            <li>Added the <code>loading="lazy"</code> attribute to avatars</li>
            <li>Drastically improved plugin performance with thousands of reviews</li>
            <li>Improved console logging</li>
            <li>Improved documentation</li>
            <li>Improved translation settings</li>
            <li>Refreshed the blocks to visually match the WordPress 5.5 editor style</li>
            <li>The Terms checkbox in the review form should now align correctly with the text</li>
        </ul>
        <h4>🚫 Removed</h4>
        <ul>
            <li>Removed the <code>glsr_calculate_ratings()</code> helper function</li>
            <li>Removed the <em>Recalculate Summary Counts</em> tool</li>
            <li>Removed the <code>site-reviews/config/forms/submission-form</code> filter hook (see the Upgrade Guide)</li>
            <li>Removed the <code>site-reviews/reviews/reviews-wrapper</code> filter hook (see the Upgrade Guide)</li>
            <li>Removed the <code>site-reviews/submission-form/order</code> filter hook (see the Upgrade Guide)</li>
            <li>Removed the Trustalyze integration, it is now an add-on</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed compatibility with the Divi theme and Divi Builder plugin</li>
            <li>Fixed compatibility with the Elementor Pro plugin popups</li>
            <li>Fixed compatibility with the GeneratePress Premium plugin</li>
            <li>Fixed compatibility with the Hummingbird Performance plugin</li>
            <li>Fixed compatibility with the Members plugin</li>
            <li>Fixed compatibility with the WP-Optimize plugin</li>
            <li>Fixed compatibility with the WP Super Cache plugin</li>
            <li>Fixed review summary bars in IE11</li>
            <li>Fixed Welcome page permissions</li>
        </ul>
    </div>
</div>
