<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-change-response-name">
            <span class="title">How do I change the name in the response?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-change-response-name" class="inside">
        <p>The easiest way to do this is to use the Translations setting to change the <code><?= __('Response from %s', 'site-reviews'); ?></code> text.</p>
        <p>However, if you need further customisation then you can use a hook to change the name like this:</p>
        <pre><code class="language-php">/**
 * @param string $responseBy
 * @param \GeminiLabs\SiteReviews\Review $review
 * @return string
 */
add_filter('site-reviews/review/build/tag/response/by', function ($responseBy, $review) {
    // Option 1:
    // The user ID of the person who wote the response is stored to the review,
    // so you can get their name like this:
    if ($user = get_userdata($review->meta()->_response_by)) {
        $responseBy = $user->display_name;
    }
    // Option 2:
    // Get the title of the first assigned page like this:
    if ($post = get_post(glsr_get($review->assigned_posts, 0))) {
        $responseBy = $post->post_title;
    }
    // Option 3:
    // Get the author's name of the first assigned page like this:
    if ($post = get_post(glsr_get($review->assigned_posts, 0))) {
        $responseBy = get_the_author($post->ID);
    }
    return $responseBy;
}, 10, 2);</code></pre>
    </div>
</div>
