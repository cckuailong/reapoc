<?php
/**
 * Capability Manager Admin Features.
 * Hide and block selected Admin Features like toolbar, dashboard widgets etc per-role.
 *
 *    Copyright 2020, PublishPress <help@publishpress.com>
 *
 *    This program is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU General Public License
 *    version 2 as published by the Free Software Foundation.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once(dirname(CME_FILE) . '/includes/features/restrict-admin-features.php');

global $capsman;

$roles        = $capsman->roles;
$default_role = $capsman->get_last_role();


$disabled_admin_items = !empty(get_option('capsman_disabled_admin_features')) ? (array)get_option('capsman_disabled_admin_features') : [];
$disabled_admin_items = array_key_exists($default_role, $disabled_admin_items) ? (array)$disabled_admin_items[$default_role] : [];

$admin_features_elements = PP_Capabilities_Admin_Features::elementsLayout();
?>

    <div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
        <div id="icon-capsman-admin" class="icon32"></div>
        <h2><?php _e('Admin Feature Restrictions', 'capabilities-pro'); ?></h2>

        <form method="post" id="ppc-admin-features-form" action="admin.php?page=pp-capabilities-admin-features">
            <?php wp_nonce_field('pp-capabilities-admin-features'); ?>

            <fieldset>
                <table id="akmin">
                    <tr>
                        <td class="content">

                            <div class="publishpress-headline">
                            <span class="cme-subtext">
                            <span class='pp-capability-role-caption'>
                            <?php
                            _e('Note: You are only restricting access to admin features screens. Some plugins may also add features to other areas of WordPress.',
                                'capabilities-pro');
                            ?>
                            </span>
                            </span>
                            </div>
                            <div class="publishpress-filters">
                                <select name="ppc-admin-features-role" class="ppc-admin-features-role">
                                    <?php
                                    foreach ($roles as $role => $name) :
                                        $name = translate_user_role($name);
                                        ?>
                                        <option value="<?php echo $role; ?>" <?php selected($default_role,
                                            $role); ?>><?php echo $name; ?></option>
                                    <?php
                                    endforeach;
                                    ?>
                                </select> &nbsp;

                                <img class="loading" src="<?php echo $capsman->mod_url; ?>/images/wpspin_light.gif"
                                     style="display: none">

                                <input type="submit" name="admin-features-submit"
                                       value="<?php _e('Save Changes', 'capabilities-pro') ?>"
                                       class="button-primary ppc-admin-features-submit" style="float:right"/>
                            </div>

                            <div id="pp-capability-menu-wrapper" class="postbox">
                                <div class="pp-capability-menus">

                                    <div class="pp-capability-menus-wrap">
                                        <div id="pp-capability-menus-general"
                                             class="pp-capability-menus-content editable-role" style="display: block;">

                                            <table
                                                class="wp-list-table widefat fixed striped pp-capability-menus-select">

                                                <thead>
                                                <tr class="ppc-menu-row parent-menu">

                                                    <td class="restrict-column ppc-menu-checkbox">
                                                        <input id="check-all-item"
                                                               class="check-item check-all-menu-item" type="checkbox"/>
                                                    </td>
                                                    <td class="menu-column ppc-menu-item">
                                                        <label for="check-all-item">
                                                        <span class="menu-item-link check-all-menu-link">
                                                            <strong>
                                                            <?php _e('Toggle all', 'capabilities-pro'); ?>
                                                            </strong>
                                                        </span></label>
                                                    </td>

                                                </tr>
                                                </thead>

                                                <tfoot>
                                                <tr class="ppc-menu-row parent-menu">

                                                    <td class="restrict-column ppc-menu-checkbox">
                                                        <input id="check-all-item-2"
                                                               class="check-item check-all-menu-item" type="checkbox"/>
                                                    </td>
                                                    <td class="menu-column ppc-menu-item">
                                                        <label for="check-all-item-2">
                                                            <span class="menu-item-link check-all-menu-link">
                                                            <strong>
                                                                <?php _e('Toggle all', 'capabilities-pro'); ?>
                                                            </strong>
                                                            </span>
                                                        </label>
                                                    </td>

                                                </tr>
                                                </tfoot>

                                                <tbody>

                                                <?php
                                                $icon_list = (array)PP_Capabilities_Admin_Features::elementLayoutItemIcons();

                                                $sn = 0;
                                                foreach ($admin_features_elements as $section_title => $section_elements) {
                                                    $sn++;
                                                    $section_slug = strtolower(ppc_remove_non_alphanumeric_space_characters($section_title));
                                                    $icon_name    = isset($icon_list[$section_slug]) ? $icon_list[$section_slug] : '&mdash;';
                                                    ?>


                                                    <tr class="ppc-menu-row parent-menu">

                                                        <td class="features-section-header" colspan="2">
                                                            <strong><i
                                                                    class="dashicons dashicons-<?php echo $icon_name ?>"></i> <?php echo $section_title; ?>
                                                            </strong>
                                                        </td>

                                                    </tr>

                                                    <?php
                                                    foreach ($section_elements as $section_id => $section_array) {
                                                        $sn++;
                                                        if (!$section_id) {
                                                            continue;
                                                        }
                                                        $item_name      = $section_array['label'];
                                                        $item_action    = $section_array['action'];
                                                        $restrict_value = $item_action.'||'.$section_id;
                                                        if($item_action === 'ppc_dashboard_widget'){
                                                            $restrict_value .= '||'.$section_array['context'];
                                                        }
                                                        ?>

                                                        <tr class="ppc-menu-row child-menu">

                                                            <td class="restrict-column ppc-menu-checkbox">
                                                                <input
                                                                    id="check-item-<?php echo $sn; ?>"
                                                                    class="check-item" type="checkbox"
                                                                    name="capsman_disabled_admin_features[]"
                                                                    value="<?php echo $restrict_value; ?>"
                                                                    <?php echo (in_array($restrict_value, $disabled_admin_items)) ? 'checked' : ''; ?>/>
                                                            </td>
                                                            <td class="menu-column ppc-menu-item'">

                                                                <label for="check-item-<?php echo $sn; ?>">
                                                                    <span
                                                                        class="menu-item-link<?php echo (in_array($restrict_value,
                                                                            $disabled_admin_items)) ? ' restricted' : ''; ?>">
                                                                    <strong>
                                                                        <?php
                                                                        if ((isset($section_array['step']) && $section_array['step'] > 0) && isset($section_array['parent']) && !empty($section_array['parent'])) {
                                                                            $step_margin = $section_array['step'] * 20;
                                                                            echo '<span style="margin-left: ' . $step_margin . 'px;"></span>';
                                                                            echo ' ' . $section_array['position'] . '. ';
                                                                        } else {
                                                                            if (isset($icon_list[$section_id])) {
                                                                                echo '<i class="dashicons dashicons-' . $icon_list[$section_id] . '"></i>';
                                                                            } else {
                                                                                echo '&mdash;';
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <?php echo $item_name; ?>
                                                                    </strong></span>
                                                                </label>
                                                            </td>

                                                        </tr>

                                                        <?php
                                                    }//end $section_elements subsection loop
                                                }// end $admin_features_elements section loop

                                                ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <input type="submit" name="admin-features-submit"
                                   value="<?php _e('Save Changes', 'capabilities-pro') ?>"
                                   class="button-primary ppc-admin-features-submit"/>
                        </td>
                    </tr>
                </table>

            </fieldset>

        </form>

        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(function($) {

                // -------------------------------------------------------------
                //   reload page for instant reflection if user is updating own role
                // -------------------------------------------------------------
                <?php if(!empty($ppc_page_reload) && (int)$ppc_page_reload === 1){ ?>
                window.location = '<?php echo admin_url('admin.php?page=pp-capabilities-admin-features&role=' . $default_role . ''); ?>'
                <?php } ?>

                // -------------------------------------------------------------
                //   Set form action attribute to include role
                // -------------------------------------------------------------
                $('#ppc-admin-features-form').attr('action', '<?php echo admin_url('admin.php?page=pp-capabilities-admin-features&role=' . $default_role . ''); ?>')

                // -------------------------------------------------------------
                //   Instant restricted item class
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function() {

                    if ($(this).is(':checked')) {
                        //add class if value is checked
                        $(this).closest('tr').find('.menu-item-link').addClass('restricted')

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='capsman_disabled_admin_features[]']").prop('checked', true)
                            $('.menu-item-link').addClass('restricted')
                        } else {
                            $('.check-all-menu-link').removeClass('restricted')
                            $('.check-all-menu-item').prop('checked', false)
                        }

                    } else {
                        //unchecked value
                        $(this).closest('tr').find('.menu-item-link').removeClass('restricted')

                        //toggle all checkbox
                        if ($(this).hasClass('check-all-menu-item')) {
                            $("input[type='checkbox'][name='capsman_disabled_admin_features[]']").prop('checked', false)
                            $('.menu-item-link').removeClass('restricted')
                        } else {
                            $('.check-all-menu-link').removeClass('restricted')
                            $('.check-all-menu-item').prop('checked', false)
                        }

                    }

                })

                // -------------------------------------------------------------
                //   Load selected roles menu
                // -------------------------------------------------------------
                $(document).on('change', '.pp-capability-menus-wrapper .ppc-admin-features-role', function() {

                    //disable select
                    $('.pp-capability-menus-wrapper .ppc-admin-features-role').attr('disabled', true)

                    //hide button
                    $('.pp-capability-menus-wrapper .ppc-admin-features-submit').hide()

                    //show loading
                    $('#pp-capability-menu-wrapper').hide()
                    $('div.publishpress-caps-manage img.loading').show()

                    //go to url
                    window.location = '<?php echo admin_url('admin.php?page=pp-capabilities-admin-features&role='); ?>' + $(this).val() + ''

                })
            })
            /* ]]> */
        </script>

        <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
            cme_publishpressFooter();
        }
        ?>
    </div>
<?php
