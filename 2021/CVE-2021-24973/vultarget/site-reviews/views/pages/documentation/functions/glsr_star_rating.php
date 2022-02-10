<div id="fn-glsr_star_rating" class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_star_rating">
            <span class="title">Generate the stars for a rating</span>
            <span class="badge code">glsr_star_rating()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_star_rating" class="inside">
        <p>This helper function allows you to build a 1-5 star rating HTML string.</p>
        <pre><code class="language-php">/**
 * @param int $rating (a number from 1-5)
 * @param int $count (the number of reviews that this rating applies to)
 * @return void
 */
glsr_star_rating($rating, $count = 0);</code></pre>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">echo glsr_star_rating(4);

// OR:

echo apply_filters('glsr_star_rating', null, 4);</code></pre>
    </div>
</div>
