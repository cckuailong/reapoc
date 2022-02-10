<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-filter-scripts">
            <span class="title">Disable the plugin javascript</span>
            <span class="badge code">site-reviews/assets/js</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-filter-scripts" class="inside">
        <p>Use this hook if you want to disable the plugin javascript from loading on your website.</p>
        <pre><code class="language-php">/**
 * Disables the Site Reviews javascript.
 * Paste this in your active theme's functions.php file.
 * @return bool
 */
add_filter('site-reviews/assets/js', '__return_false');</code></pre>
    </div>
</div>
