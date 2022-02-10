<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_get_review">
            <span class="title">Get a single review</span>
            <span class="badge code">glsr_get_review()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_get_review" class="inside">
        <pre><code class="language-php">/**
 * @param int $post_id
 * @return \GeminiLabs\SiteReviews\Review
 */
glsr_get_review($post_id);</code></pre>
        <p>This helper function returns an arrayable Review object.</p>
        <p>The <code>$post_id</code> variable is required and is the $post->ID of the review you want to get. An invalid post ID will return an empty Review object.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">$review = glsr_get_review(13);

// OR:

$review = apply_filters('glsr_get_review', null, 13);</code></pre>
        <p><strong>Helpful Tips:</strong></p>
        <ol>
            <li>
                <p>Print a specific (non-rendered) Review value to the page:</p>
                <pre><code class="language-php">echo $review->author; // OR: echo $review['author'];</code></pre>
            </li>
            <li>
                <p>Print the rendered (HTML) review to the page:</p>
                <pre><code class="language-php">$review->render(); // OR: echo $review;</code></pre>
            </li>
            <li>
                <p>Render (as HTML) all of the review fields and return them as an arrayable object that can be looped through:</p>
                <pre><code class="language-php">$reviewHtml = $review->build();</code></pre>
            </li>
            <li>
                <p>Print a specific rendered (HTML) field to the page:</p>
                <pre><code class="language-php">echo $reviewHtml->author; // OR: echo $reviewHtml['author'];</code></pre>
            </li>
            <li>
                <p>Print the rendered (HTML) review to the page:</p>
                <pre><code class="language-php">echo $reviewHtml; // This is identical to: $review->render();</code></pre>
            </li>
            <li>
                <p>You can also use the <code><a href="<?= glsr_admin_url('documentation', 'functions'); ?>" data-expand="#fn-glsr_debug">glsr_debug</a></code> helper function to print both arrayable objects to the screen:</p>
                <pre><code class="language-php">glsr_debug($review, $reviewHtml);</code></pre>
            </li>
        </ol>
    </div>
</div>
