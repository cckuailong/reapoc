<?php defined('ABSPATH') || die; ?>

<form method="post" class="glsr-form-sync glsr-status">
    <?php $selected = key($services); ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <td class="check-column glsr-radio-column"><span class="dashicons-before dashicons-update"></span></td>
                <th scope="col" class="column-primary"><?= _x('Service', 'admin-text', 'site-reviews'); ?></th>
                <th scope="col" class="column-total_fetched"><?= _x('Reviews', 'admin-text', 'site-reviews'); ?></th>
                <th scope="col" class="column-last_sync"><?= _x('Last Sync', 'admin-text', 'site-reviews'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($services as $slug => $details) : ?>
            <tr class="service-<?= $slug; ?>">
                <th scope="row" class="check-column">
                    <input type="radio" name="{{ id }}[service]" value="<?= $slug; ?>" <?php checked($slug, $selected); ?>>
                </th>
                <td class="column-primary has-row-actions">
                    <strong><?= $details['name']; ?></strong>
                    <div class="row-actions">
                        <span><a href="<?= glsr_admin_url('settings', 'addons'); ?>"><?= _x('Settings', 'admin-text', 'site-reviews'); ?></a> | </span>
                        <span><a href="<?= glsr_admin_url('settings', 'licenses'); ?>"><?= _x('License', 'admin-text', 'site-reviews'); ?></a> | </span>
                        <span><a href="<?= glsr_admin_url('documentation', 'addons'); ?>"><?= _x('Documentation', 'admin-text', 'site-reviews'); ?></a></span>
                    </div>
                    <button type="button" class="toggle-row">
                        <span class="screen-reader-text"><?= _x('Show more details', 'admin-text', 'site-reviews'); ?></span>
                    </button>
                </td>
                <td class="column-total_fetched" data-colname="<?= esc_attr_x('Reviews', 'admin-text', 'site-reviews'); ?>">
                    <a href="<?= $details['reviews_url']; ?>"><?= $details['reviews_count']; ?></a>
                </td>
                <td class="column-last_sync" data-colname="<?= esc_attr_x('Last Sync', 'admin-text', 'site-reviews'); ?>">
                    <?= $details['last_sync']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="no-items" style="display:table-cell!important;">
                    <div class="glsr-progress" data-active-text="<?= esc_attr_x('Please wait...', 'admin-text', 'site-reviews'); ?>">
                        <div class="glsr-progress-bar" style="width: 0%;">
                            <span class="glsr-progress-status"><?= _x('Inactive', 'admin-text', 'site-reviews'); ?></span>
                        </div>
                        <div class="glsr-progress-background">
                            <span class="glsr-progress-status"><?= _x('Inactive', 'admin-text', 'site-reviews'); ?></span>
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="tablenav bottom">
        <button type="submit" class="glsr-button button" id="sync-reviews">
            <span data-loading="<?= esc_attr_x('Syncing...', 'admin-text', 'site-reviews'); ?>"><?= _x('Sync Reviews', 'admin-text', 'site-reviews'); ?></span>
        </button>
    </div>
</form>
