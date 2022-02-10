<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-optimise-db-tables">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Optimise Your Database Tables', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-optimise-db-tables" class="inside">
        <?php if (!empty($myisam_tables)) { ?>
            <div class="components-notice is-warning">
                <p class="components-notice__content"><?= sprintf(
                    _x('Please backup your database before running this tool! You can use the %s plugin to do this.', 'admin-text', 'site-reviews'),
                    '<a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a>'
                ); ?></p>
            </div>
            <p><?= _x('The old MyISAM table engine in MySQL was replaced by the InnoDB engine as the default over 10 years ago! If your database tables still use the MyISAM engine, you are missing out on substantial performance and reliability gains that the InnoDB engine provides.', 'admin-text', 'site-reviews'); ?></p>
            <p><?= _x('Site Reviews makes use of specific InnoDB engine features in order to perform faster database queries. However, some of your database tables (shown below) are still using the old MyISAM engine. If you convert these tables to use the InnoDB engine, it will make Site Reviews perform faster.', 'admin-text', 'site-reviews'); ?></p>
            <table class="wp-list-table widefat striped" style="margin-bottom:1em;">
                <thead>
                    <tr>
                        <th scope="col"><strong><?= _x('Table', 'admin-text', 'site-reviews'); ?></strong></th>
                        <th scope="col"><strong><?= _x('Engine', 'admin-text', 'site-reviews'); ?></strong></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myisam_tables as $table) { ?>
                    <tr data-ajax-hide>
                        <td style="vertical-align:middle;"><?= $table; ?></td>
                        <td style="vertical-align:middle;">MyISAM</td>
                        <td style="text-align:right;">
                            <form method="post">
                                <?php wp_nonce_field('convert-table-engine'); ?>
                                <input type="hidden" name="{{ id }}[_action]" value="convert-table-engine">
                                <input type="hidden" name="{{ id }}[table]" value="<?= $table; ?>">
                                <button type="submit" class="glsr-button components-button is-secondary is-small" data-ajax-click>
                                    <span data-loading="<?= esc_attr_x('Converting, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Convert table engine to InnoDB', 'admin-text', 'site-reviews'); ?></span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="components-notice is-success" style="margin-bottom:1em;">
                <p class="components-notice__content"><?= _x('Optimisation is unnecessary because your database tables already use the InnoDB engine!', 'admin-text', 'site-reviews'); ?> ✨</p>
            </div>
        <?php } ?>
    </div>
</div>
