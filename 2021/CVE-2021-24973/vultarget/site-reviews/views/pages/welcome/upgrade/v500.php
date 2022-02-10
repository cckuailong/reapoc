<div class="glsr-card postbox is-fullwidth open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="upgrade-v5_0_0">
            <span class="title">Version 5.0.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v5_0_0" class="inside">
        <h2>Shortcode option changes</h2>
        <p><em>Likelihood Of Impact: <span class="required">High</span></em></p>
        <p>The changes to the shortcode options introduced in v5.0.0 are backwards compatible with version 4, meaning the old shortcode options will continue to work; however, if you are using these options then you should still update them as they may be removed in a future version.</p>
        <p>Affected shortcode options:</p>
        <ul>
            <li><strong>assigned_to</strong> (renamed to "assigned_posts")</li>
            <li><strong>assign_to</strong> (renamed to "assigned_posts")</li>
            <li><strong>category</strong> (renamed to "assigned_terms")</li>
            <li><strong>hide</strong> ("assigned_to" hide value has been renamed to "assigned_links")</li>
        </ul>
        <p>This is how it was done in version 4:</p>
        <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">assigned_to</span>=<span class="attr-value">"post_id"</span> <span class="attr-name">category</span>=<span class="attr-value">"13"</span><span class="tag">]</span>
<span class="tag">[site_reviews</span> <span class="attr-name">assigned_to</span>=<span class="attr-value">"post_id"</span> <span class="attr-name">category</span>=<span class="attr-value">"13"</span> <span class="attr-name">hide</span>=<span class="attr-value">"assigned_to,title"</span><span class="tag">]</span>
<span class="tag">[site_reviews_form</span> <span class="attr-name">assign_to</span>=<span class="attr-value">"post_id"</span> <span class="attr-name">category</span>=<span class="attr-value">"13"</span><span class="tag">]</span>
</code></pre>
        <p>And this is how it is done in version 5:</p>
        <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">assigned_posts</span>=<span class="attr-value">"post_id"</span> <span class="attr-name">assigned_terms</span>=<span class="attr-value">"13"</span><span class="tag">]</span>
<span class="tag">[site_reviews</span> <span class="attr-name">assigned_posts</span>=<span class="attr-value">"post_id"</span> <span class="attr-name">assigned_terms</span>=<span class="attr-value">"13"</span> <span class="attr-name">hide</span>=<span class="attr-value">"assigned_links,title"</span><span class="tag">]</span>
<span class="tag">[site_reviews_form</span> <span class="attr-name">assigned_posts</span>=<span class="attr-value">"post_id"</span> <span class="attr-name">assigned_terms</span>=<span class="attr-value">"13"</span><span class="tag">]</span>
</code></pre>

        <h2>Action and Filter Hook changes</h2>
        <p><em>Likelihood Of Impact: Medium</em></p>
        <ol>
            <li>
                <p><strong>The <code>site-reviews/config/forms/submission-form</code> filter hook has been deprecated.</strong></p>
                <p>If you were previously using this hook to add custom form fields, you should change it to: <code>site-reviews/config/forms/review-form</code>.</p>
            </li>
            <li>
                <p><strong>The <code>site-reviews/rating/average</code> filter hook argument order has changed.</strong></p>
                <p>If you were previously using this hook to customise the average rating, you should change the argument order to match the following:</p>
                <pre><code class="language-php">add_filter('site-reviews/rating/average', function ($roundedAverage, $average, $ratingCounts) {
    return $roundedAverage;
}, 10, 3);
</code></pre>
            </li>
            <li>
                <p><strong>The <code>site-reviews/reviews/reviews-wrapper</code> filter hook has been removed.</strong></p>
                <p>If you were previously using this hook, it was likely to add a Bootstrap ".row" class to the reviews wrapper so that you could display the reviews in columns. This is no longer needed as you can now add the class directly in the <code>reviews.php</code> template.</p>
            </li>
            <li>
                <p><strong>The <code>site-reviews/submission-form/order</code> filter hook has been deprecated.</strong></p>
                <p>If you were previously using this hook to set a custom order to custom form fields, you should change it to: <code>site-reviews/review-form/order</code>.</p>
            </li>
        </ol>

        <h2>Helper function changes</h2>
        <p><em>Likelihood Of Impact: Medium</em></p>
        <ol>
            <li>
                <p><strong>The <code>glsr_calculate_ratings()</code> has been removed.</strong></p>
                <p>This function is no longer needed so it has been removed. If you are using this function in your PHP code, you should remove it as otherwise it will generate a PHP deprecated notice in your PHP Error log.</p>
            </li>
            <li>
                <p><strong>The <code><a data-expand="#fn-glsr_create_review" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_create_review()</a></code> arguments have been updated to use the new shortcode options.</strong></p>
                <p>If you are using this function in your PHP code, you should update the key names used in the function argument array. The old key names will continue to work, but this may change in a future version.</p>
            </li>
            <li>
                <p><strong>The <code><a data-expand="#fn-glsr_get_option" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_get_option()</a></code> function now allows you to automatically cast the returned value to a specific type.</strong></p>
                <p>Please refer to the documentation for more details.</p>
            </li>
            <li>
                <p><strong>The <code><a data-expand="#fn-glsr_get_review" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_get_review()</a></code> function now allows you to iterate over custom field data.</strong></p>
                <pre><code class="language-php">$review = glsr_get_review(13);
foreach ($review->custom as $key =&gt; $value) {
    glsr_debug($key, $value);
}
// You may also do this:
glsr_debug($review->custom->custom_field_key);
</code></pre>
            </li>
            <li>
                <p><strong>The <code><a data-expand="#fn-glsr_get_reviews" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_get_reviews()</a></code> arguments have been updated to use the new shortcode options.</strong></p>
                <p>If you are using this function in your PHP code, you should update the key names used in the function argument array. The old key names will continue to work, but this may change in a future version.</p>
            </li>
            <li>
                <p><strong>The <code>glsr_get_rating()</code> has been renamed to <code><a data-expand="#fn-glsr_get_ratings" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_get_ratings()</a></code>.</strong></p>
                <p>This function has been renamed to better reflect it's purpose; its function arguments also use the new shortcode options. If you are using this function in your PHP code, you should update it as otherwise it will generate a PHP deprecated notice in your PHP Error log. You should also verify how you are using the value that is returned as this has also been updated:</p>
                <pre><code class="language-php">[ // this is the array value that is returned by the function
    'average' => '', // this contains the average rating number
    'maximum' => '', // this contains the maximum rating number that is allowed
    'minimum' => '', // this contains the minimum rating number that is allowed
    'ranking' => '', // this contains the calculated bayesian raking
    'ratings' => '', // this contains an array with the number of reviews for each rating
    'reviews' => '', // this contains the total number of reviews used
];
</code></pre>
            </li>
        </ol>

        <h2>Review meta data</h2>
        <p><em>Likelihood Of Impact: Low</em></p>
        <p>Site Reviews now saves review values in a custom database table and they are no longer stored as meta data; however, this does not apply to custom form field data which is still stored as meta data.</p>
        <p>To access your review data with PHP (including custom field data), use the provided <code><a data-expand="#fn-glsr_get_review" href="<?= glsr_admin_url('documentation', 'functions'); ?>">glsr_get_review()</a></code> helper function instead of the <code>get_post_meta()</code> function. </p>
    </div>
</div>
