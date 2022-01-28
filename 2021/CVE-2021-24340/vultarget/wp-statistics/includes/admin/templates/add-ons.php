<div id="poststuff" class="wp-statistics-plugins">
    <div id="post-body" class="metabox-holder">
        <div class="wp-list-table widefat widefat plugin-install">
            <div id="the-list">
                <?php
                foreach ($plugins->items as $plugin) : ?>
                    <div class="plugin-card">
                        <?php if ($plugin->is_feature and $plugin->featured_label) : ?>
                            <div class="cover-ribbon">
                                <div class="cover-ribbon-inside"><?php echo $plugin->featured_label; ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="plugin-card-top">
                            <div class="name column-name">
                                <h3>
                                    <a target="_blank" href="<?php echo $plugin->url; ?>" class="thickbox open-plugin-details-modal">
                                        <?php echo $plugin->name; ?>
                                        <img src="<?php echo $plugin->icon; ?>" class="plugin-icon" alt="<?php echo $plugin->name; ?>">
                                    </a>
                                </h3>
                            </div>

                            <div class="desc column-description">
                                <p><?php echo wp_trim_words($plugin->description, 15); ?></p>
                            </div>
                        </div>
                        <div class="plugin-card-bottom">
                            <div class="column-downloaded">
                                <strong><?php _e('Version:', 'wp-statistics'); ?></strong><?php echo ' ' .
                                    $plugin->version; ?>
                                <p><strong><?php _e('Status:', 'wp-statistics'); ?></strong>
                                    <?php
                                    if (is_plugin_active($plugin->slug . '/' . $plugin->slug . '.php')) {
                                        _e('Active', 'wp-statistics');
                                    } else if (file_exists(
                                        WP_PLUGIN_DIR . '/' . $plugin->slug . '/' . $plugin->slug . '.php'
                                    )) {
                                        _e('Inactive', 'wp-statistics');
                                    } else {
                                        _e('Not installed', 'wp-statistics');
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="column-compatibility">
                                <?php if (is_plugin_active($plugin->slug . '/' . $plugin->slug . '.php')) { ?>
                                    <a href="<?php echo WP_STATISTICS\Menus::admin_url('plugins', array('action' => 'deactivate', 'plugin' => $plugin->slug)); ?>" class="button"><?php _e('Deactivate Add-On', 'wp-statistics'); ?></a>
                                <?php } else { ?><?php if (file_exists(
                                    WP_PLUGIN_DIR . '/' . $plugin->slug . '/' . $plugin->slug . '.php'
                                )) { ?>
                                    <a href="<?php echo WP_STATISTICS\Menus::admin_url('plugins', array('action' => 'activate', 'plugin' => $plugin->slug)); ?>" class="button"><?php _e('Activate Add-On', 'wp-statistics'); ?></a>
                                <?php } else { ?>
                                    <div class="column-price">
                                        <strong>$<?php echo $plugin->price; ?></strong>
                                    </div><a target="_blank" href="<?php echo $plugin->url; ?>" class="button-primary"><?php _e('Buy Add-On', 'wp-statistics'); ?></a>
                                <?php } ?><?php } ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>