<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_get_ratings">
            <span class="title">Get the rating information</span>
            <span class="badge code">glsr_get_ratings()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_get_ratings" class="inside">
        <pre><code class="language-php">/**
 * @return object
 */
glsr_get_ratings(array $args = []);</code></pre>
        <p>This helper function returns an object with the rating information. You can use this function to get the aggregate (combined average) rating if you are generating your own JSON-LD schema.</p>
        <p>The <code>$args</code> variable is optional, but if included it must be an array.</p>
        <p><strong>Default $args array:</strong></p>
        <pre><code class="language-php">$args = [
    'assigned_posts' => '',
    'assigned_terms' => '',
    'assigned_users' => '',
    'author_id' => '',
    'email' => '',
    'ip_address' => '',
    'rating' => '',
    'rating_field' => '',
    'status' => 'approved', // accepted values are "all", "approved", and "unapproved"
    'type' => '',
];</code></pre>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">$ratingInfo = glsr_get_ratings([
    'assigned_posts' => 'post_id',
]);

// OR:

$ratingInfo = apply_filters('glsr_get_ratings', null, [
    'assigned_posts' => 'post_id',
]);</code></pre>
        <p><strong>Helpful Tips:</strong></p>
        <ol>
            <li>
                <p>You can use the <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_debug">glsr_debug</a></code> helper function to print the rating info to the screen:</p>
                <pre><code class="language-php">glsr_debug($ratingInfo);</code></pre>
            </li>
        </ol>
    </div>
</div>
