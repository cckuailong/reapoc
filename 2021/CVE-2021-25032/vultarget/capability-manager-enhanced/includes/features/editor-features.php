<?php
/**
 * Capability Manager Edit Posts Permission.
 * Edit Posts permission and visibility per roles.
 *
 *    Copyright 2021, PublishPress <help@publishpress.com>
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

require_once (dirname(CME_FILE) . '/includes/features/restrict-editor-features.php');

global $capsman;
$roles = $capsman->roles;

$default_role = $capsman->get_last_role();

$classic_editor = pp_capabilities_is_classic_editor_available();
?>
<div class="wrap publishpress-caps-manage pressshack-admin-wrapper pp-capability-menus-wrapper">
    <div id="icon-capsman-admin" class="icon32"></div>
    <h2><?php _e('Editor Feature Restriction', 'capsman-enhanced'); ?></h2>

    <form method="post" id="ppc-editor-features-form"
            action="admin.php?page=pp-capabilities-editor-features">
        <?php wp_nonce_field('pp-capabilities-editor-features'); ?>

        <table id="akmin">
            <tr>
                <td class="content">

                    <div class="publishpress-headline">
                        <span class="cme-subtext">
                        <span class='pp-capability-role-caption'>
                        <?php
                        _e('Select editor features to remove. Note that this screen cannot be used to grant additional features to any role.', 'capabilities-pro');
                        ?>
                        </span>
                        </span>
                    </div>

                    <div class="publishpress-filters">
                        <select name="ppc-editor-features-role" class="ppc-editor-features-role">
                            <?php
                            foreach ($roles as $role => $name) :
                                $name = translate_user_role($name);
                                ?>
                                <option value="<?php echo $role;?>" <?php selected($default_role, $role);?>><?php echo $name;?></option>
                            <?php
                            endforeach;
                            ?>
                        </select> &nbsp;

                        <img class="loading" src="<?php echo $capsman->mod_url; ?>/images/wpspin_light.gif" style="display: none">

                        <input type="submit" name="editor-features-submit"
                            value="<?php _e('Save Changes', 'capabilities-pro') ?>"
                            class="button-primary ppc-editor-features-submit" style="float:right" />

                        <input type="hidden" name="ppc-tab" value="<?php echo (!empty($_REQUEST['ppc-tab'])) ? sanitize_key($_REQUEST['ppc-tab']) : 'gutenberg';?>" />
                    </div>

                    <script type="text/javascript">
                    /* <![CDATA[ */
                    jQuery(document).ready(function($) {
                        $('li.gutenberg-tab').click(function() {
                            $('div.publishpress-filters input[name=ppc-tab]').val('gutenberg');
                        });

                        $('li.classic-tab').click(function() {
                            $('div.publishpress-filters input[name=ppc-tab]').val('classic');
                        });
                    });
                    /* ]]> */
                    </script>

                    <?php if ($classic_editor) { ?>
                        <ul class="nav-tab-wrapper">
                            <li class="editor-features-tab gutenberg-tab nav-tab <?php if (empty($_REQUEST['ppc-tab']) || ('gutenberg' == $_REQUEST['ppc-tab'])) echo 'nav-tab-active';?>"
                                data-tab=".editor-features-gutenberg"><a href="#"><?php _e('Gutenberg', 'capsman-enhanced') ?></a></li>

                            <li class="editor-features-tab classic-tab nav-tab <?php if (!empty($_REQUEST['ppc-tab']) && ('classic' == $_REQUEST['ppc-tab'])) echo 'nav-tab-active';?>"
                                data-tab=".editor-features-classic"><a href="#"><?php _e('Classic', 'capsman-enhanced') ?></a></li>
                        </ul>
                    <?php } ?>

                    <div id="pp-capability-menu-wrapper" class="postbox">
                        <div class="pp-capability-menus">

                            <div class="pp-capability-menus-wrap">
                                <div id="pp-capability-menus-general"
                                        class="pp-capability-menus-content editable-role"
                                        style="display: block;">
                                    <?php
                                    $sn = 0;
                                    include(dirname(__FILE__) . '/editor-features-gutenberg.php');

                                    if ($classic_editor) {
                                        include(dirname(__FILE__) . '/editor-features-classic.php');
                                    }
                                    ?>

                                </div>
                            </div>

                        </div>
                    </div>

                    <input type="submit" name="editor-features-submit"
                            value="<?php _e('Save Changes', 'capsman-enhanced') ?>"
                            class="button-primary ppc-editor-features-submit"/> &nbsp;


                </td>
            </tr>
        </table>

    </form>

    <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
        cme_publishpressFooter();
    }
    ?>
</div>

<style>
    span.menu-item-link {
        webkit-user-select: none; /* Safari */        
        -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* IE10+/Edge */
        user-select: none; /* Standard */
    }

    input.check-all-menu-item {margin-top: 5px !important;}
</style>

<script type="text/javascript">
    /* <![CDATA[ */
    jQuery(document).ready(function ($) {

        // -------------------------------------------------------------
        //   Set form action attribute to include role
        // -------------------------------------------------------------
        $('#ppc-editor-features-form').attr('action', '<?php echo admin_url('admin.php?page=pp-capabilities-editor-features&role=' . $default_role . ''); ?>');

        // -------------------------------------------------------------
        //   Instant restricted item class
        // -------------------------------------------------------------
        $(document).on('change', '.pp-capability-menus-wrapper .ppc-menu-row .check-item', function () {
            var current_tab;

            <?php if ($classic_editor) { ?>
                if ($('.nav-tab-wrapper .classic-tab').hasClass('nav-tab-active')) {
                    current_tab = 'classic';
                } else {
                    current_tab = 'gutenberg';
                }
            <?php } else { ?>
                current_tab = 'gutenberg';
            <?php } ?>

            //add class if feature is restricted for any post type
            var anyRestricted = $(this).closest('tr').find('input:checked').length > 0;
            $(this).closest('tr').find('.menu-item-link').toggleClass('restricted', anyRestricted);

            var isChecked = $(this).is(':checked');

            //toggle all checkbox
            if ($(this).hasClass('check-all-menu-item')) {
                var suffix = ('gutenberg' == current_tab) ? '' : current_tab + '_';
                $("input[type='checkbox'][name='capsman_feature_restrict_" + suffix + $(this).data('pp_type') + "[]']").prop('checked', isChecked);

                $('.' + current_tab + '.menu-item-link').each(function(i,e) {
                    $(this).toggleClass('restricted', $(this).closest('tr').find('input:checked').length > 0);
                });
            } else {
                $('.' + current_tab + '.check-all-menu-link').removeClass('restricted').prop('checked', false);
            }
        });

        $(document).on("click", "span.menu-item-link", function (e) {
            if($(e.target).parent().hasClass('ppc-custom-features-delete')){
                return;
            }
            var chks = $(this).closest('tr').find('input');
            $(chks).prop('checked', !$(this).hasClass('restricted'));
            $(this).toggleClass('restricted', $(chks).filter(':checked').length);
        });

        // -------------------------------------------------------------
        //   Load selected roles menu
        // -------------------------------------------------------------
        $(document).on('change', '.pp-capability-menus-wrapper .ppc-editor-features-role', function () {

            //disable select
            $('.pp-capability-menus-wrapper .ppc-editor-features-role').attr('disabled', true);

            //hide button
            $('.pp-capability-menus-wrapper .ppc-editor-features-submit').hide();

            //show loading
            $('#pp-capability-menu-wrapper').hide();
            $('div.publishpress-caps-manage img.loading').show();

            //go to url
            window.location = '<?php echo admin_url('admin.php?page=pp-capabilities-editor-features&role='); ?>' + $(this).val() + '';

        });


        // -------------------------------------------------------------
        //   Editor features tab
        // -------------------------------------------------------------
        $('.editor-features-tab').click(function (e) {
            e.preventDefault();
            $('.editor-features-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.pp-capability-menus-select').hide();
            $($(this).attr('data-tab')).show();
        });

    });
    /* ]]> */
</script>
<?php
