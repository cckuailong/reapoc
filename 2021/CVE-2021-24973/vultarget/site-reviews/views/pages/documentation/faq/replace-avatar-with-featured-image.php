<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-replace-avatar-with-featured-image">
            <span class="title">How do I replace the avatar with the featured image of the assigned post?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-replace-avatar-with-featured-image" class="inside">
        <pre><code class="language-php">/**
 * Replaces the review avatar with the featured image thumbnail of the first assigned page
 * Paste this in your active theme's functions.php file.
 * @param string $value
 * @param \GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAvatarTag $tag
 * @return string
 */
add_filter('site-reviews/review/value/avatar', function ($value, $tag) {
    $size = glsr_get_option('reviews.avatars_size', 40); // 40 is the fallback width/height in pixels
    $postId = glsr_get($tag->review, 'assigned_posts.0'); // get the first assigned post ID
    $avatarUrl = get_the_post_thumbnail_url($postId, [$size, $size]);
    if (empty($avatarUrl)) {
        $avatarUrl = 'https://gravatar.com/avatar/?d=identicon&s=128'; // fallback to a generic avatar image
    }
    return sprintf('&lt;img src="%1$s" height="%2$s" width="%2$s"&gt;', $avatarUrl, $size);
}, 10, 2);</code></pre>
    </div>
</div>
