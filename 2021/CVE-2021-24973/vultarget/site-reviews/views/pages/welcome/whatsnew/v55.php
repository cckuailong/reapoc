<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_5_0">
            <span class="title">Version 5.5</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_5_0" class="inside">
        <p><em>Initial Release Date &mdash; January 26th, 2021</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added <code>data-field</code> attribute to form fields; this should make it easier to build custom CSS layouts</li>
            <li>Added <code>date</code>, <code>user__in</code>, and <code>user__not_in</code> parameters to the <code>glsr_get_reviews()</code> function</li>
            <li>Added <code>glsr_trace()</code> helper function</li>
            <li>Added plugin style for the "Twenty Twenty-One" theme</li>
            <li>Added "Restrict Limits To" setting which allows you to choose which assignments are used in the review limits.</li>
            <li>Added sub-sections for add-on settings</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Changed the <code>site-reviews/review/build/&lt;tag&gt;</code> hooks to <code>site-reviews/review/build/tag/&lt;tag&gt;</code></li>
            <li>Upgraded the <a href="https://github.com/pryley/star-rating.js" target="_blank">star-ratings.js</a> library to v4 in preparation for the "Review Themes" add-on</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed Add-on support notice</li>
            <li>Fixed CSV importing when header values contain trailing spaces</li>
            <li>Fixed email and IP address review limits to include all reviews (not just approved reviews)</li>
            <li>Fixed line breaks in excerpts</li>
            <li>Fixed links in review responses to allow the "rel" attribute</li>
            <li>Fixed Migration support for PHP 8.0 Named Arguments</li>
        </ul>
    </div>
</div>
