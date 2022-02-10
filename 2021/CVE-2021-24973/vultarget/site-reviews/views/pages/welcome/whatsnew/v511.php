<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="welcome-v5_11_0">
            <span class="title">Version 5.11</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="welcome-v5_11_0" class="inside">
        <p><em>Initial Release Date &mdash; June 2nd, 2021</em></p>
        <h4>✨ New Features</h4>
        <ul>
            <li>Added a REST API (see the <a data-expand="#fn-glsr_update_review" href="<?= glsr_admin_url('documentation', 'api'); ?>">provided documentation</a> to learn how to use it)</li>
            <li>Added custom capabilities for responding to reviews (<code>respond_to_site-review</code> and <code>respond_to_others_site-review</code>)</li>
            <li>Added filters for categories, assigned posts, and assigned users on the "All Reviews" admin page (enable them in the "Screen Options" on the top-right of the page)</li>
            <li>Added native Elementor widgets</li>
            <li>Added support for &lt;optgroup&gt; in dropdown fields (this may be useful when using the Review Forms add-on to display a category dropdown with parent/child categories). To learn how to enable this, please see the <a data-expand="#faq-enable-optgroup" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> Help page.</li>
            <li>Added the ability to create reviews from the admin</li>
            <li>Added the ability to respond to reviews from the "All Reviews" page</li>
            <li>Added the <code>site-reviews/review/build/tag/response/by</code> hook (see the <a data-expand="#faq-change-response-name" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> to learn how to use it to change the name in the response)</li>
            <li>Added the <code>glsr_update_review</code> helper function (see the <a data-expand="#fn-glsr_update_review" href="<?= glsr_admin_url('documentation', 'functions'); ?>">provided documentation</a> to learn how to use it)</li>
        </ul>
        <h4>🐞 Bugs Fixed</h4>
        <ul>
            <li>Fixed inline styles (overriding the star images should now display correctly in the block editor)</li>
            <li>Fixed summary review counts for 0-star ratings</li>
            <li>Fixed the star rating when resetting the submission form where a default rating is set</li>
        </ul>
    </div>
</div>
