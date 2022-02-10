<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_summary">
            <span class="title">Display the reviews summary</span>
            <span class="badge code">[site_reviews_summary]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_summary" class="inside">
        <h3>This shortcode displays a summary of your reviews.</h3>
        <div class="components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                trailingslashit(__DIR__).'site_reviews_summary/assigned_posts.php',
                trailingslashit(__DIR__).'site_reviews_summary/assigned_terms.php',
                trailingslashit(__DIR__).'site_reviews_summary/assigned_users.php',
                trailingslashit(__DIR__).'site_reviews_summary/class.php',
                trailingslashit(__DIR__).'site_reviews_summary/hide.php',
                trailingslashit(__DIR__).'site_reviews_summary/id.php',
                trailingslashit(__DIR__).'site_reviews_summary/labels.php',
                trailingslashit(__DIR__).'site_reviews_summary/rating.php',
                trailingslashit(__DIR__).'site_reviews_summary/rating_field.php',
                trailingslashit(__DIR__).'site_reviews_summary/schema.php',
                trailingslashit(__DIR__).'site_reviews_summary/terms.php',
                trailingslashit(__DIR__).'site_reviews_summary/text.php',
            ];
            $filename = pathinfo(__FILE__, PATHINFO_FILENAME);
            $options = glsr()->filterArrayUnique('documentation/shortcodes/'.$filename, $options);
            foreach ($options as $option) {
                include $option;
            }
        ?>
    </div>
</div>
