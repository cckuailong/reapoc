<?php
/**
 * Date: 9/28/16
 * Time: 9:26 PM
 */
if(!defined('ABSPATH')) die('!');
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "Privacy Settings" , "download-manager" ); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_noip" />
            <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_noip'),1); ?> value="1" name="__wpdm_noip"><?php _e( "Do not store visitor's IP" , "download-manager" ); ?></label><br/>
            <em><?php _e( "Check this option if you do not want to store visitors IPs" , "download-manager" ); ?></em>
        </div>

        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_delstats_on_udel" />
            <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_delstats_on_udel'),1); ?> value="1" name="__wpdm_delstats_on_udel"><?php _e( "Delete download history when users close accounts" , "download-manager" ); ?></label><br/>
            <em><?php _e( "If any user is deleted or close his/her own account, delete their download history too" , "download-manager" ); ?></em>
        </div>

        <?php
        do_action("wpdm_privacy_settings_option");
        ?>

    </div>
</div>

<?php
do_action("wpdm_privacy_settings_panel");
?>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e( "Temporary Storage Management" , "download-manager" ); ?></div>
    <div class="panel-body">
        <label style="margin-right: 10px"><input type="radio" name="__wpdm_tmp_storage" <?php checked('file', get_option('__wpdm_tmp_storage', 'file')) ?> value="file"> <?php _e( "Save in File", "download-manager" ) ?></label>
        <label style="margin-right: 10px"><input type="radio" name="__wpdm_tmp_storage" <?php checked('db', get_option('__wpdm_tmp_storage', 'file')) ?> value="db"> <?php _e( "Store in Database", "download-manager" ) ?></label>
        <!-- label style="margin-right: 10px"><input type="radio" name="__wpdm_tmp_storage" <?php checked('cookie', get_option('__wpdm_tmp_storage', 'file')) ?> value="cookie"> <?php _e( "Use Cookie", "download-manager" ) ?></label -->
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e( "Cache & Stats" , "download-manager" ); ?></div>
    <div class="panel-body">
            <button type="button" id="clearCache" style="width: 200px" class="btn btn-success"><?php _e( "Clear All Cache" , "download-manager" ); ?></button>
            <button type="button" id="clearStats" style="width: 200px" class="btn btn-danger"><?php _e( "Delete All Stats Data" , "download-manager" ); ?></button>
    </div>
    <div class="panel-footer bg-white">
        <label><input type="checkbox" name="__wpdm_auto_clean_cache" <?php checked(1, get_option('__wpdm_auto_clean_cache', 0)); ?> value="1" /> <?php echo __( "Auto clean cache dir & temporary storage everyday", "download-manager" ) ?></label>
    </div>
</div>
<script>
    jQuery(function($) {
        $('#clearCache').on('click', function () {
            $(this).html('<i class="fa fa-sync fa-spin"></i>');
            $.get(ajaxurl + '?action=clear_cache', function (res) {
                $('#clearCache').html('<i class="fa fa-check-circle"></i>')
            });
            return false;
        });
        $('#clearStats').on('click', function () {
            if (!confirm('Are you sure?')) return false;
            $(this).html('<i class="fa fa-sync fa-spin"></i>');
            $.get(ajaxurl + '?action=clear_stats', function (res) {
                $('#clearStats').html('<i class="fa fa-check-circle"></i>')
            });
            return false;
        });

    });
</script>
