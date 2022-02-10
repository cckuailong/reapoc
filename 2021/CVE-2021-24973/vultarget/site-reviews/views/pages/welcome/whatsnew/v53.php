<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_3_0">
            <span class="title">Version 5.3</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_3_0" class="inside">
        <p><em>Initial Release Date &mdash; December 13th, 2020</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added <a href="<?= glsr_admin_url('addons'); ?>">Site Reviews Premium</a></li>
            <li>Added the <a href="<?= glsr_admin_url('addons'); ?>">Review Forms</a> add-on</li>
            <li>Added debug logging for validation errors</li>
            <li>Added error logging for database table creation errors</li>
            <li>Added support for PHP 8</li>
            <li>Added the Category and Review IDs to the action row in the admin tables</li>
            <li>Added the <code>{{ assigned_posts }}</code>, <code>{{ assigned_users }}</code>, and <code>{{ assigned_terms }}</code> template tags</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed block attributes</li>
            <li>Fixed line-breaks in review excerpts</li>
            <li>Fixed MariaDB support (removed subqueries from the SQL)</li>
            <li>Fixed migration of imported settings</li>
            <li>Fixed pagination URLs for servers that do not use REQUEST_URI</li>
            <li>Fixed shortcode examples in documentation; Copy/pasting a shortcode example into the classic editor will now paste as plain text instead of as HTML code.</li>
            <li>Fixed support for older custom fields using <code>assign_to</code> or <code>category</code> as names</li>
            <li>Fixed System Info details to always be in English</li>
            <li>Fixed the <code>post__in</code> and <code>post__not_in</code> options of the glsr_get_reviews() helper function</li>
            <li>Fixed the Backtrace used when logging entries to the Console</li>
            <li>Fixed the Console on sites that have been duplicated but still have the upload dir cached to the old path
            <li>Fixed the PHP multibyte fallback when the iconv extension is missing</li>
        </ul>
    </div>
</div>
