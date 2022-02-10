<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-import-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Import Third Party Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-import-reviews" class="inside">
        <div class="components-notice is-warning">
            <p class="components-notice__content"><?= sprintf(
                _x('Please backup your database before running this tool! You can use the %s plugin to do this.', 'admin-text', 'site-reviews'),
                '<a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a>'
            ); ?></p>
        </div>
        <p><?= sprintf(
            _x('Here you can import third party reviews from a %s file. The CSV file should include a header row and may contain the following columns:', 'admin-text', 'site-reviews'),
            '<code>*.CSV</code>'
        ); ?></p>
        <p>
            <code>assigned_posts</code> <?= _x('The Posts that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'); ?><br>
            <code>assigned_terms</code> <?= _x('The Categories that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'); ?><br>
            <code>assigned_users</code> <?= _x('The Users that the review is assigned to (separate multiple IDs with a comma)', 'admin-text', 'site-reviews'); ?><br>
            <code>avatar</code> <?= _x('The avatar URL of the reviewer', 'admin-text', 'site-reviews'); ?><br>
            <code>content</code> <?= sprintf('%s (<span class="required">%s</span>)', _x('The review', 'admin-text', 'site-reviews'), _x('required', 'admin-text', 'site-reviews')); ?><br>
            <code>date</code> <?= sprintf('%s (<span class="required">%s</span>)', _x('The review date', 'admin-text', 'site-reviews'), _x('required', 'admin-text', 'site-reviews')); ?><br>
            <code>email</code> <?= _x('The reviewer\'s email', 'admin-text', 'site-reviews'); ?><br>
            <code>ip_address</code> <?= _x('The IP address of the reviewer', 'admin-text', 'site-reviews'); ?><br>
            <code>is_approved</code> <?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?><br>
            <code>is_pinned</code> <?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?><br>
            <code>name</code> <?= _x('The reviewer\'s name', 'admin-text', 'site-reviews'); ?><br>
            <code>rating</code> <?= sprintf('%s (<span class="required">%s</span>)', sprintf(_x('A number from 0-%d', 'admin-text', 'site-reviews'), glsr()->constant('MAX_RATING', 'GeminiLabs\SiteReviews\Modules\Rating')), _x('required', 'admin-text', 'site-reviews')); ?><br>
            <code>response</code> <?= _x('The review response', 'admin-text', 'site-reviews'); ?><br>
            <code>terms</code> <?= _x('TRUE or FALSE', 'admin-text', 'site-reviews'); ?><br>
            <code>title</code> <?= _x('The title of the review', 'admin-text', 'site-reviews'); ?><br>
        </p>
        <p><?= _x('Entries in the CSV file that do not contain required values will be skipped.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post" enctype="multipart/form-data" onsubmit="submit.classList.add('is-busy'); submit.disabled = true;">
            <?php wp_nonce_field('import-reviews'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="import-reviews">
            <p>
                <input type="file" name="import-file" accept="text/csv">
            </p>
            <p>
                <label for="csv_delimiter"><strong><?= _x('Delimiter', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <select name="{{ id }}[delimiter]" id="csv_delimiter" required>
                    <option value=""><?= _x('Select the delimiter used in the CSV file', 'admin-text', 'site-reviews'); ?></option>
                    <option value=","><?= _x('Comma (,)', 'admin-text', 'site-reviews'); ?></option>
                    <option value=";"><?= _x('Semicolon (;)', 'admin-text', 'site-reviews'); ?></option>
                </select>
            </p>
            <p>
                <label for="csv_date_format"><strong><?= _x('Date Format', 'admin-text', 'site-reviews'); ?></strong></label><br>
                <select name="{{ id }}[date_format]" id="csv_date_format" required>
                    <option value=""><?= _x('Select the date format used in the reviews', 'admin-text', 'site-reviews'); ?></option>
                    <option value="d-m-Y">13-01-2021 &nbsp; (d-m-Y)</option>
                    <option value="d/m/Y">13/01/2021 &nbsp; (d/m/Y)</option>
                    <option value="m-d-Y">01-13-2021 &nbsp; (m-d-Y)</option>
                    <option value="m/d/Y">01/13/2021 &nbsp; (m/d/Y)</option>
                    <option value="Y-m-d">2021-01-13 &nbsp; (Y-m-d)</option>
                    <option value="Y/m/d">2021/01/13 &nbsp; (Y/m/d)</option>
                    <option value="d-m-Y H:i">13-01-2021 12:00 &nbsp; (d-m-Y H:i)</option>
                    <option value="d/m/Y H:i">13/01/2021 12:00 &nbsp; (d/m/Y H:i)</option>
                    <option value="m-d-Y H:i">01-13-2021 12:00 &nbsp; (m-d-Y H:i)</option>
                    <option value="m/d/Y H:i">01/13/2021 12:00 &nbsp; (m/d/Y H:i)</option>
                    <option value="Y-m-d H:i">2021-01-13 12:00 &nbsp; (Y-m-d H:i)</option>
                    <option value="Y/m/d H:i">2021/01/13 12:00 &nbsp; (Y/m/d H:i)</option>
                    <option value="d-m-Y H:i:s">13-01-2021 12:00:00 &nbsp; (d-m-Y H:i:s)</option>
                    <option value="d/m/Y H:i:s">13/01/2021 12:00:00 &nbsp; (d/m/Y H:i:s)</option>
                    <option value="m-d-Y H:i:s">01-13-2021 12:00:00 &nbsp; (m-d-Y H:i:s)</option>
                    <option value="m/d/Y H:i:s">01/13/2021 12:00:00 &nbsp; (m/d/Y H:i:s)</option>
                    <option value="Y-m-d H:i:s">2021-01-13 12:00:00 &nbsp; (Y-m-d H:i:s)</option>
                    <option value="Y/m/d H:i:s">2021/01/13 12:00:00 &nbsp; (Y/m/d H:i:s)</option>
                </select>
            </p>
            <button type="submit" name="submit" class="glsr-button components-button is-secondary" id="import-reviews" data-expand="#tools-import-reviews">
                <span data-loading="<?= esc_attr_x('Importing reviews, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Import Reviews', 'admin-text', 'site-reviews'); ?></span>
            </button>
        </form>
    </div>
</div>
