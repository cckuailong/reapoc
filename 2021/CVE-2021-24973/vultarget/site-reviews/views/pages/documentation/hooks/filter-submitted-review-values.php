<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-filter-submitted-review-values">
            <span class="title">Modify the submitted review before it is saved</span>
            <span class="badge code">site-reviews/create/review-values</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-filter-submitted-review-values" class="inside">
        <p>Use this hook if you want to modify the values of a submitted review before it is created.</p>
        <pre><code class="language-php">/**
 * Modifies the review values before they are saved
 * Paste this in your active theme's functions.php file.
 * @param array $values
 * @return array
 */
add_filter('site-reviews/create/review-values', function ($values) {
    // modify the $values array here
    return $values;
});</code></pre>
    </div>
</div>
