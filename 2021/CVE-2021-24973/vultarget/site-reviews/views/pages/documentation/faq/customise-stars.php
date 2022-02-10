<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="faq-customise-stars">
            <span class="title">How do I customise the stars?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="faq-customise-stars" class="inside">
        <p>To customise the star images used by the plugin, use the <code><a data-expand="#hooks-filter-star-images" href="<?= glsr_admin_url('documentation', 'hooks'); ?>">site-reviews/config/inline-styles</a></code> filter hook in your theme's <code>functions.php</code> file.</p>
        <p>Here is an example:</p>
        <pre><code class="language-php">/**
 * Customises the stars used by Site Reviews.
 * Simply change and edit the URLs to match those of your custom images.
 * Paste this in your active theme's functions.php file.
 * @param array $config
 * @return array
 */
add_filter('site-reviews/config/inline-styles', function ($config) {
    $config[':star-empty'] = 'https://your-website.com/images/star-empty.svg';
    $config[':star-error'] = 'https://your-website.com/images/star-error.svg';
    $config[':star-full'] = 'https://your-website.com/images/star-full.svg';
    $config[':star-half'] = 'https://your-website.com/images/star-half.svg';
    return $config;
});</code></pre>
        <p>If all you need to do is change the colour of the stars:<p>
        <ol>
            <li>Copy the SVG images to your Desktop, the stars can be found here: <code>/wp-content/plugins/site-reviews/assets/images/</code></li>
            <li>Open the SVG images that you copied with a text editor</li>
            <li>Change the <a target="_blank" href="https://www.hexcolortool.com">hex colour code</a> to the one you want</li>
            <li>Install and activate the <a target="_blank" href="https://wordpress.org/plugins/safe-svg/">Safe SVG</a> plugin</li>
            <li>Upload the edited SVG images to your <a href="<?= admin_url('upload.php'); ?>">Media Library</a></li>
            <li>Copy the File URL of the uploaded SVG images and paste them into the snippet above</li>
        </ol>
    </div>
</div>
