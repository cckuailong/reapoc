<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="hooks-filter-star-images">
            <span class="title">Customise the star images</span>
            <span class="badge code">site-reviews/config/inline-styles</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="hooks-filter-star-images" class="inside">
        <p>Use this hook to customise the star images used by Site Reviews.</p>
        <p>See the <code><a data-expand="#faq-customise-stars" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a></code> for a detailed example of how to use this hook.</p>
        <pre><code class="language-php">/**
 * Customises the stars used by Site Reviews.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/inline-styles', function ($config) {
    // modify the star URLs in the $config array here
    return $config;
});</code></pre>
    </div>
</div>
