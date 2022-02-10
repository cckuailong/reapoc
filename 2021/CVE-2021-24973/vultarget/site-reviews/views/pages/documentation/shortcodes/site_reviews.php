<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews">
            <span class="title">Display the reviews</span>
            <span class="badge code">[site_reviews]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews" class="inside">
        <h3>This shortcode displays your most recently submitted reviews.</h3>
        <div class="components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                trailingslashit(__DIR__).'site_reviews/assigned_posts.php',
                trailingslashit(__DIR__).'site_reviews/assigned_terms.php',
                trailingslashit(__DIR__).'site_reviews/assigned_users.php',
                trailingslashit(__DIR__).'site_reviews/class.php',
                trailingslashit(__DIR__).'site_reviews/display.php',
                trailingslashit(__DIR__).'site_reviews/fallback.php',
                trailingslashit(__DIR__).'site_reviews/hide.php',
                trailingslashit(__DIR__).'site_reviews/id.php',
                trailingslashit(__DIR__).'site_reviews/offset.php',
                trailingslashit(__DIR__).'site_reviews/pagination.php',
                trailingslashit(__DIR__).'site_reviews/rating.php',
                trailingslashit(__DIR__).'site_reviews/rating_field.php',
                trailingslashit(__DIR__).'site_reviews/schema.php',
                trailingslashit(__DIR__).'site_reviews/terms.php',
            ];
            $filename = pathinfo(__FILE__, PATHINFO_FILENAME);
            $options = glsr()->filterArrayUnique('documentation/shortcodes/'.$filename, $options);
            foreach ($options as $option) {
                include $option;
            }
        ?>
    </div>
</div>
