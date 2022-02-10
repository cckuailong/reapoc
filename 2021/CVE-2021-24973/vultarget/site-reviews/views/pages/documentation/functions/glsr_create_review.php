<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_create_review">
            <span class="title">Create a review</span>
            <span class="badge code">glsr_create_review()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_create_review" class="inside">
        <div class="components-notice is-warning">
            <p class="components-notice__content">This function uses basic validation on the provided values. If validation fails, the function will return false and the validation errors will be logged to the <a href="<?= glsr_admin_url('tools', 'console'); ?>">Plugin Console</a>.</p>
        </div>
        <br>
        <pre><code class="language-php">/**
 * Default values in the $reviewValues array:
 * - 'assigned_posts' => '',
 * - 'assigned_terms' => '',
 * - 'assigned_users' => '',
 * - 'avatar' => '',
 * - 'content' => '',
 * - 'date' => '',
 * - 'email' => '',
 * - 'ip_address' => '',
 * - 'is_pinned' => false,
 * - 'name' => '',
 * - 'rating' => '',
 * - 'terms' => true,
 * - 'title' => '',
 * - 'url' => '',
 * @return \GeminiLabs\SiteReviews\Review|false
 */
glsr_create_review(array $reviewValues = []);</code></pre>
        <p><strong>Example Usage:</strong></p>
        <p>Any custom keys that are added to the $reviewValues array will be saved into the <code>$review->custom</code> array of the created review.</p>
        <pre><code class="language-php">$review = glsr_create_review([
    'assigned_posts' => $post->ID,
    'content' => 'This is my review.',
    'date' => '2018-06-13',
    'email' => 'jane@doe.com',
    'name' => 'Jane Doe',
    'rating' => 5,
    'title' => 'Fantastic plugin!',
    'xyz' => 'This is a custom field!'
]);

// OR:

$review = apply_filters('glsr_create_review', false, [
    'assigned_posts' => $post->ID,
    'content' => 'This is my review.',
    'date' => '2018-06-13',
    'email' => 'jane@doe.com',
    'name' => 'Jane Doe',
    'rating' => 5,
    'title' => 'Fantastic plugin!',
    'xyz' => 'This is a custom field!'
]);</code></pre>
        <p><strong>Helpful Tip:</strong></p>
        <p>You can use the <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_debug">glsr_debug</a></code> helper function to view the review object that is returned.</p>
        <pre><code class="language-php">glsr_debug($review);</code></pre>
    </div>
</div>
