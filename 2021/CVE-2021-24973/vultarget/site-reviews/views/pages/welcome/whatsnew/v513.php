<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_13_0">
            <span class="title">Version 5.13</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_13_0" class="inside">
        <p><em>Initial Release Date &mdash; August 1st, 2021</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a <code>data-rating</code> attribute to the review ratings</li>
            <li>Added the <code>site-reviews/avatar/attributes</code> hook which allows you to modify the attributes on the avatar &lt;img&gt; tag</li>
            <li>Added the <code>$review->author()</code> method on review objects which returns the author name as defined in the settings</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Internal changes to support the upcoming Review Themes add-on</li>
            <li>Renamed the <code>site-reviews/review/response</code> hook to <code>site-reviews/review/responded</code> (see the related FAQ section)</li>
        </ul>
        <h4>🛠 Tweaks</h4>
        <ul>
            <li>Improved the error message when saving add-on licenses to make it more descriptive and helpful.</li>
            <li>Removed the "Discover Premium" button for licensed add-on users</li>
            <li>Updated the "Common Problems and Solutions"</li>
            <li>Updated the "FAQ"</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed invalid schema output on the <code>[site_reviews]</code> shortcode when there are no reviews</li>
            <li>Fixed PHP warning introduced in v5.12.7</li>
            <li>Fixed relative dates to use the GMT date of the review</li>
            <li>Fixed the <code>respond_to_*</code> capabilities, users can now respond to reviews if they are the author of one of the assigned posts</li>
            <li>Fixed the Assigned Posts filter on the All Reviews page</li>
            <li>Fixed the Bulk Edit actions</li>
            <li>Fixed the filters on the "All Reviews" admin page</li>
            <li>Fixed the plugin migration which sets the custom role capabilities in cases where the default WordPress roles have been renamed or removed</li>
        </ul>
    </div>
</div>
