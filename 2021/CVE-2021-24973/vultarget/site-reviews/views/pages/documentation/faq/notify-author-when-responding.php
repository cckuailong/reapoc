<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-notify-author-when-responding">
            <span class="title">How do I send a notification to the author when responding to a review?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-notify-author-when-responding" class="inside">
        <pre><code class="language-php">/**
 * Send an email notification to the review author after responding to a review
 * This snippet assumes that your review form includes the email field
 * Paste this code in your theme's functions.php file.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param string $response
 * @return void
 */
add_action('site-reviews/review/responded', function ($review, $response) {
    $hasResponse = !empty($review->response);
    $hasResponseUserId = !empty(get_post_meta($review->ID, '_response_by', true));
    if (empty($response) || $hasResponse || $hasResponseUserId) {
        return; // only send an email if the response is not empty and there is no previous response
    }
    $email = glsr('Modules\Email')->compose([
        'to' => $review->email,
        'subject' => 'We have responded to your review!',
        'message' => $response,
    ]);
    $email->send();
}, 10, 2);</code></pre>
    </div>
</div>
