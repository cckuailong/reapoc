<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v4_1_0">
            <span class="title">Version 4.1</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v4_1_0" class="inside">
        <p><em>Initial Release Date &mdash; October 16th, 2019</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added optional "Email", "IP Address", and "Response" columns to the reviews table</li>
        </ul>
        <h4>📢 Changes</h4>
        <ul>
            <li>Changed <code>[site_reviews]</code> "count" option name to "display" (i.e. [site_reviews display=10])</li>
            <li>Changed <code>glsr_get_reviews()</code> "count" option name to "per_page" (i.e. glsr_get_reviews(['per_page' => 10]))</li>
            <li>Renamed "Documentation" page to "Help"</li>
        </ul>
        <h4>🛠 Tweaks</h4>
        <ul>
            <li>Updated the "Common Problems and Solutions" help section</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed a HTML5 validation issue in the plugin settings</li>
            <li>Fixed column sorting on the reviews table</li>
            <li>Fixed email template tags</li>
            <li>Fixed IP address detection for servers that do not support IPv6</li>
            <li>Fixed pagination links from triggering in the editor block</li>
            <li>Fixed pagination when using the default count of 5 reviews per page</li>
            <li>Fixed pagination with hidden review fields</li>
            <li>Fixed PHP compatibility issues</li>
            <li>Fixed plugin migration on update</li>
            <li>Fixed plugin uninstall</li>
            <li>Fixed translations for default text that include a HTML link</li>
        </ul>
    </div>
</div>
