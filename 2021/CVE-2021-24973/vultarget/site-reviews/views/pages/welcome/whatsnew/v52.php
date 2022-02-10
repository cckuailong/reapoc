<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_2_0">
            <span class="title">Version 5.2</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_2_0" class="inside">
        <p><em>Initial Release Date &mdash; November 6th, 2020</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added Notification Template tags for assigned categories, posts, and users</li>
            <li>Added Review Assignment setting</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Changed review assignment in SQL queries to use strict assignments by default (it was previously using loose assignments, use the new "Review Assignment" setting to change this back)</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed addons notice styling and placement</li>
            <li>Fixed Bulk Editing of reviews that are assigned to post types or users</li>
            <li>Fixed compatibility issue with the Elementor Pro Popups</li>
            <li>Fixed Multibyte String support</li>
            <li>Fixed Multisite compatibility</li>
            <li>Fixed pagination URLs when used on the homepage</li>
            <li>Fixed plugin file paths on IIS Windows servers</li>
            <li>Fixed plugin migrations to work better with the W3 Total Cache plugin</li>
            <li>Fixed rating validation when using a custom maximum rating value</li>
            <li>Fixed review limits validation for assigned reviews</li>
            <li>Fixed review migration of invalid 3rd-party reviews (reviews that were previously imported incorrectly)</li>
            <li>Fixed review name and email fallback values to use those of the logged-in user</li>
            <li>Fixed strict standard notices in PHP 5.6</li>
            <li>Fixed the <code>glsr_create_review</code> helper function validation</li>
            <li>Fixed the submission date of reviews, it now uses the timezone offset in the WordPress settings</li>
        </ul>
    </div>
</div>
