<?php
if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('PackageInfo')) {
    class PackageInfo extends WP_Widget
    {
        /** constructor */
        function __construct()
        {

            parent::__construct(false, 'WPDM Packages Info');
        }

        /** @see WP_Widget::widget */
        function widget($args, $instance)
        {

            if (!is_singular('wpdmpro')) return;

            global $post;
            extract($args);
            $title = apply_filters('widget_title', $instance['title']);
            $package_info = isset($instance['pinfo']) ? $instance['pinfo'] : array();
            $package_info_labels = array(
                'download_count' => __("Total Downloads", "download-manager"),
                'view_count' => __("Total Views", "download-manager"),
                'create_date' => __("Publish Date", "download-manager"),
                'update_date' => __("Last Updated", "download-manager"),
                'package_size' => __("Size", "download-manager"),
                'version' => __("Version", "download-manager"),
            );

            $package_info_icons = array(
                'download_count' => 'arrow-alt-circle-down',
                'view_count' => 'eye',
                'package_size' => 'hdd',
                'version' => 'layer-group',
            );
            $download_link = "";
            if (isset($package_info['download_link'])) {
                unset($package_info['download_link']);
                if (isset($package_info['download_link_ext']))
                    $download_link = WPDM()->package->downloadLink(get_the_ID(), 1, array('popstyle' => 'popup'));
                else
                    $download_link = WPDM()->package->downloadLink(get_the_ID(), 0, array('popstyle' => 'popup'));
            }


            ?>
            <?php echo $before_widget; ?>
            <?php if (isset($title) & $title != '')
            echo $before_title . $title . $after_title;
            if (isset($instance['table']) && $instance['table'] == 1) {
                echo "<div class='w3eden'><table class='table table-striped table-bordered' style='font-size: 9pt'>";

                if (is_array($package_info)) {
                    foreach ($package_info as $index => $v) {
                        if ($index == 'create_date')
                            echo "<tr><td>{$package_info_labels[$index]}</td><td>" . get_the_date() . "</td></tr>";
                        else if ($index == 'update_date')
                            echo "<tr><td>{$package_info_labels[$index]}</td><td>" . get_the_modified_date() . "</td></tr>";
                        else if ($index != 'download_link_ext')
                            echo "<tr><td>{$package_info_labels[$index]}</td><td>" . get_post_meta(get_the_ID(), '__wpdm_' . $index, true) . "</td></tr>";
                    }
                }
                if ($download_link != '')
                    $download_link = "<tr><td colspan='2' class='text-center'>" . $download_link . "</td>";
                echo "{$download_link}</table></div>";
            } else {
                echo "<div class='w3eden'><div class='list-group package-info-list'>";

                if (is_array($package_info)) {
                    foreach ($package_info as $index => $v) {
                        if ($index == 'create_date')
                            echo "<div class='list-group-item'><div class='media'><div class='pull-left mr-3'><i class='fa fa-calendar'></i></div><div class='media-body'><strong>{$package_info_labels[$index]}</strong><br/>" . get_the_date() . "</div></div></div>";
                        else if ($index == 'update_date')
                            echo "<div class='list-group-item'><div class='media'><div class='pull-left mr-3'><i class='fa fa-calendar-check'></i></div><div class='media-body'><strong>{$package_info_labels[$index]}</strong><br/>" . get_the_modified_date() . "</div></div></div>";
                        else if ($index != 'download_link_ext' && isset($package_info_labels[$index]))
                            echo "<div class='list-group-item'><div class='media'><div class='pull-left mr-3'><i class='fa fa-{$package_info_icons[$index]}'></i></div><div class='media-body'><strong>{$package_info_labels[$index]}</strong><br/>" . get_post_meta(get_the_ID(), '__wpdm_' . $index, true) . "</div></div></div>";
                    }
                }

                echo "<div class='list-group-item'>{$download_link}</div></div></div>";
            }


            echo $after_widget;

            if (is_array($package_info) && isset($package_info['qrcode'])) {
                echo $before_widget;
                echo "<img class='wpdm-qr-code wpdm-qr-code-widget' style='max-width: 100%' src='https://chart.googleapis.com/chart?cht=qr&chs=450x450&choe=UTF-8&chld=H|0&chl=" . get_permalink(get_the_ID()) . "' alt='" . get_the_title() . "' />";
                echo $after_widget;
            }
            wp_reset_query();
        }

        /** @see WP_Widget::update */
        function update($new_instance, $old_instance)
        {
            $instance = $new_instance;
            return $instance;
        }

        /** @see WP_Widget::form */
        function form($instance)
        {
            if (isset($instance['title']))
                $title = esc_attr($instance['title']);
            else $title = '';
            if (isset($instance['pinfo']))
                $package_info = $instance['pinfo'];


            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('scat'); ?>"><?php _e("Fields to Show:", "download-manager"); ?></label>

            <ul>
                <li><label><input type="checkbox"
                                  value="download_count" <?php checked(isset($package_info['download_count']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[download_count]"> <?php _e("Download Count", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox"
                                  value="view_count" <?php checked(isset($package_info['view_count']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[view_count]"> <?php _e("View Count", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox"
                                  value="create_date" <?php checked(isset($package_info['create_date']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[create_date]"> <?php _e("Publish Date", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox"
                                  value="update_date" <?php checked(isset($package_info['update_date']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[update_date]"> <?php _e("Update Date", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox" value="version" <?php checked(isset($package_info['version']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[version]"> <?php _e("Version", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox"
                                  value="download_link" <?php checked(isset($package_info['package_size']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[package_size]"> <?php _e("Package Size", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox"
                                  value="download_link" <?php checked(isset($package_info['download_link']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[download_link]"> <?php _e("Download Link", "download-manager"); ?>
                    </label></li>
                <li><label><input type="checkbox"
                                  value="download_link_ext" <?php checked(isset($package_info['download_link_ext']), 1) ?>
                                  name="<?php echo $this->get_field_name('pinfo'); ?>[download_link_ext]"> <?php _e("Embed Download Options", "download-manager"); ?>
                    </label></li>
                <li>
                    <hr/><?php _e("Style", "download-manager"); ?>:<br/><label style="font-weight: 900"><input
                                type="checkbox" value="1" <?php checked(isset($instance['table']), 1) ?>
                                name="<?php echo $this->get_field_name('table'); ?>"> <?php _e("Tabular View", "download-manager"); ?>
                    </label></li>
                <li>
                    <hr/>
                    <label style="font-weight: 900"><input type="checkbox"
                                                           value="1" <?php checked(isset($package_info['qrcode']), 1) ?>
                                                           name="<?php echo $this->get_field_name('pinfo'); ?>[qrcode]"> <?php _e("Show QR Code", "download-manager"); ?>
                    </label></li>
            </ul>


            </p>


            <?php
        }

    }
}
register_widget('PackageInfo');
