<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_get_reviews">
            <span class="title">Get multiple reviews</span>
            <span class="badge code">glsr_get_reviews()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_get_reviews" class="inside">
        <pre><code class="language-php">/**
 * @return \GeminiLabs\SiteReviews\Reviews
 */
glsr_get_reviews(array $args = []);</code></pre>
        <p>This helper function returns an arrayable Reviews object with super-powers!</p>
        <p>The <code>$args</code> variable is optional, but if included it must be an array.</p>
        <p><strong>Default $args array:</strong></p>
        <pre><code class="language-php">$args = [
    'assigned_posts' => '',
    'assigned_terms' => '',
    'assigned_users' => '',
    'date' => '', // value can be a date string, or an array (see example below)
    'email' => '',
    'ip_address' => '',
    'offset' => '',
    'order' => 'DESC', // value can be "ASC" or "DESC"
    'orderby' => 'date', // value can be "author", "date", "ID", "random" or "random"
    'page' => 1,
    'pagination' => false,
    'per_page' => 10,
    'post__in' => [],
    'post__not_in' => [],
    'rating' => '',
    'rating_field' => '',
    'status' => 'approved', // value can be "all", "approved", or "unapproved"
    'type' => '',
    'user__in' => [],
    'user__not_in' => [],
];</code></pre>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">$reviews = glsr_get_reviews([
    'assigned_posts' => 'post_id',
    'date' => [
        'after' => '2020-12-01', // all reviews after this date (or if using the "inclusive" option, all reviews after and on this date)
        'before' => '2020-12-31',  // all reviews before this date (or if using the "inclusive" option, all reviews before and on this date)
        'inclusive' => true,
    ],
    'user__in' => [get_current_user_id()],
    'rating' => 3,
]);

// OR:

$reviews = apply_filters('glsr_get_reviews', [], [
    'assigned_posts' => 'post_id',
    'author_id' => get_current_user_id(),
    'date' => [
        'after' => '2020-12-01',
        'before' => '2020-12-31',
        'inclusive' => true,
    ],
    'user__in' => [get_current_user_id()],
    'rating' => 3,
]);</code></pre>
        <p><strong>Helpful Tips:</strong></p>
        <ol>
            <li>
                <p>Print (as HTML) ALL reviews, including pagination (if included in the $args) to the page:</p>
                <pre><code class="language-php">echo $reviews; // This is identical to: $reviews->render();</code></pre>
            </li>
            <li>
                <p>Loop through all reviews and handle each review as needed. Each <code>$review</code> is identical to what the <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_get_review">glsr_get_review</a></code> helper function returns, so make sure to read the "Helpful Tips" from that section above for more information.</p>
                <pre><code class="language-php">foreach ($reviews as $review) {
    echo $review;
};</code></pre>
            </li>
            <li>
                <p>Use the "max_num_pages" value when creating your own custom navigation:</p>
                <pre><code class="language-php">$totalPages = $reviews->max_num_pages;</code></pre>
            </li>
            <li>
                <p>Render (as HTML) all reviews, including pagination, and return them as an arrayable object. You can then loop through this object like an array, the object also contains the pagination HTML:</p>
                <pre><code class="language-php">$reviewsHtml = $reviews->build();
foreach ($reviewsHtml as $reviewHtml) {
    echo $reviewHtml;
}
echo $reviewsHtml->pagination;</code></pre>
            </li>
            <li>
                <p>You can also use the <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_debug">glsr_debug</a></code> helper function to print both arrayable objects to the screen:</p>
                <pre><code class="language-php">glsr_debug($reviews, $reviewsHtml);</code></pre>
            </li>

        </ol>
    </div>
</div>
