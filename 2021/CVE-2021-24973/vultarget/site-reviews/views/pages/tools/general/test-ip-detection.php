<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-ip-detection">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Test IP Address Detection', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-ip-detection" class="inside">
        <p><?= _x('When reviews are submitted on your website, Site Reviews detects the IP address of the person submitting the review and saves it to the submitted review. This allows you to limit review submissions or to blacklist reviewers based on their IP address. The IP address is also used by Akismet (if you have enabled the integration) to catch spam submissions.', 'admin-text', 'site-reviews'); ?></p>
        <p><?= _x('If you are getting an "unknown" value for IP addresses in your reviews, you may use this tool to check the visitor IP address detection.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('detect-ip-address'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="detect-ip-address">
            <button type="submit" class="glsr-button components-button is-secondary" id="detect-ip-address" data-ajax-click data-ajax-scroll>
                <span data-loading="<?= esc_attr_x('Testing, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Test Detection', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
