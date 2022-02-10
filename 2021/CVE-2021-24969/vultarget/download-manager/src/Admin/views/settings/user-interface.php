<?php
/**
 * Author: shahnuralam
 * Date: 2019-01-01
 * Time: 14:39
 */
if (!defined('ABSPATH')) die();

$ui_button = get_option('__wpdm_ui_download_button');
$ui_button_sc = get_option('__wpdm_ui_download_button_sc');

?>
<!-- div class="panel panel-default">
    <div class="panel-heading"><?php _e("Frontend UI Library",'download-manager'); ?></div>
    <div class="panel-body">
        <label style="margin-right: 20px"><input  <?php checked('', get_option('__wpdm_bsversion','')); ?>  type="radio" value="" name="__wpdm_bsversion"> <?php _e( "Bootstrap 4" , "download-manager" ); ?></label> <label><input <?php checked('3', get_option('__wpdm_bsversion','')); ?> type="radio" value="3" name="__wpdm_bsversion"> <?php _e( "Bootstrap 3" , "download-manager" ); ?></label>
    </div>
</div -->
<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Disable Style & Script",'download-manager'); ?></div>
    <div class="panel-body">

        <?php
            $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array()));
            if(!is_array($wpdmss)) $wpdmss = array();
        ?>
        <input type="hidden" name="__wpdm_disable_scripts[]" value="" >
        <ul>
            <li><label><input <?php if(in_array('wpdm-bootstrap-js', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-bootstrap-js" name="__wpdm_disable_scripts[]"> <?php _e( "Disable Bootstrap JS" , "download-manager" ); ?></label></li>
            <li><label><input <?php if(in_array('wpdm-bootstrap-css', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-bootstrap-css" name="__wpdm_disable_scripts[]"> <?php _e( "Disable Bootstrap CSS" , "download-manager" ); ?></label></li>
            <li><label><input <?php if(in_array('wpdm-font-awesome', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-font-awesome" name="__wpdm_disable_scripts[]"> <?php _e( "Disable Font Awesome" , "download-manager" ); ?></label></li>
            <li><label><input <?php if(in_array('wpdm-front', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-front" name="__wpdm_disable_scripts[]"> <?php _e( "Disable Front CSS" , "download-manager" ); ?></label></li>
            <!-- li><label><input <?php if(in_array('google-font', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="google-font" name="__wpdm_disable_scripts[]"> <?php _e("Google Font","download-manager"); ?></label></li -->
        </ul>
        <em><?php _e( "Because, sometimes your theme may have those scripts/styles enqueued already" , "download-manager" ); ?></em>
        <hr/>
        <div><label><?php _e("Google Font:","download-manager"); ?></label></div>
        <select name="__wpdm_google_font" id="__wpdm_google_font" data-selected="<?php echo get_option('__wpdm_google_font', 'Rubik'); ?>"></select>

    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Colors",'download-manager'); ?></div>


        <?php $uicolors = maybe_unserialize(get_option('__wpdm_ui_colors', array())); ?>

        <table class="table">
            <thead>
            <tr>
                <th>Color Name</th>
                <th width="50px">Default</th>
                <th width="50px">Hover</th>
                <th width="50px">Active</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo __("Primary:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-primary" class="color-control" name="__wpdm_ui_colors[primary]" value="<?php echo isset($uicolors['primary'])?$uicolors['primary']:'#4a8eff'; ?>" /></td>
                <td><input type="text" data-css-var="--color-primary-hover" class="color-control" name="__wpdm_ui_colors[primary_hover]" value="<?php echo isset($uicolors['primary_hover'])?$uicolors['primary_hover']:'#4a8eff'; ?>" /></td>
                <td><input type="text" data-css-var="--color-primary-acitve" class="color-control" name="__wpdm_ui_colors[primary_active]" value="<?php echo isset($uicolors['primary_active'])?$uicolors['primary_active']:'#4a8eff'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Secondary:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-secondary" class="color-control" name="__wpdm_ui_colors[secondary]" value="<?php echo isset($uicolors['secondary'])?$uicolors['secondary']:'#6c757d'; ?>" /></td>
                <td><input type="text" data-css-var="--color-secondary-hover" class="color-control" name="__wpdm_ui_colors[secondary_hover]" value="<?php echo isset($uicolors['secondary_hover'])?$uicolors['secondary_hover']:'#6c757d'; ?>" /></td>
                <td><input type="text" data-css-var="--color-secondary-active" class="color-control" name="__wpdm_ui_colors[secondary_active]" value="<?php echo isset($uicolors['secondary_active'])?$uicolors['secondary_active']:'#6c757d'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Info:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-info" class="color-control" name="__wpdm_ui_colors[info]" value="<?php echo isset($uicolors['info'])?$uicolors['info']:'#2CA8FF'; ?>" /></td>
                <td><input type="text" data-css-var="--color-info-hover" class="color-control" name="__wpdm_ui_colors[info_hover]" value="<?php echo isset($uicolors['info_hover'])?$uicolors['info_hover']:'#2CA8FF'; ?>" /></td>
                <td><input type="text" data-css-var="--color-info-active" class="color-control" name="__wpdm_ui_colors[info_active]" value="<?php echo isset($uicolors['info_active'])?$uicolors['info_active']:'#2CA8FF'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Success:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-success" class="color-control" name="__wpdm_ui_colors[success]" value="<?php echo isset($uicolors['success'])?$uicolors['success']:'#18ce0f'; ?>" /></td>
                <td><input type="text" data-css-var="--color-success-hover" class="color-control" name="__wpdm_ui_colors[success_hover]" value="<?php echo isset($uicolors['success_hover'])?$uicolors['success_hover']:'#18ce0f'; ?>" /></td>
                <td><input type="text" data-css-var="--color-success-acitve" class="color-control" name="__wpdm_ui_colors[success_active]" value="<?php echo isset($uicolors['success_active'])?$uicolors['success_active']:'#18ce0f'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Warning:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-warning" class="color-control" name="__wpdm_ui_colors[warning]" value="<?php echo isset($uicolors['warning'])?$uicolors['warning']:'#FFB236'; ?>" /></td>
                <td><input type="text" data-css-var="--color-warning-hover" class="color-control" name="__wpdm_ui_colors[warning_hover]" value="<?php echo isset($uicolors['warning_hover'])?$uicolors['warning_hover']:'#FFB236'; ?>" /></td>
                <td><input type="text" data-css-var="--color-warning-acitve" class="color-control" name="__wpdm_ui_colors[warning_active]" value="<?php echo isset($uicolors['warning_active'])?$uicolors['warning_active']:'#FFB236'; ?>" /></td>
            </tr>
            <tr>
                <td><?php echo __("Danger:", "download-manager") ?></td>
                <td><input type="text" data-css-var="--color-danger" class="color-control" name="__wpdm_ui_colors[danger]" value="<?php echo isset($uicolors['danger'])?$uicolors['danger']:'#ff5062'; ?>" /></td>
                <td><input type="text" data-css-var="--color-danger-hover" class="color-control" name="__wpdm_ui_colors[danger_hover]" value="<?php echo isset($uicolors['danger_hover'])?$uicolors['danger_hover']:'#ff5062'; ?>" /></td>
                <td><input type="text" data-css-var="--color-danger-active" class="color-control" name="__wpdm_ui_colors[danger_active]" value="<?php echo isset($uicolors['danger_active'])?$uicolors['danger_active']:'#ff5062'; ?>" /></td>
            </tr>
            </tbody>
        </table>


</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Download Button",'download-manager'); ?></div>

        <table class="table table-bordered">
            <tr>
                <th colspan="2"><?php _e("Details Page", "download-manager");  ?></th>
            </tr>
            <tr>
                <td valign="middle" title="Select Button Color">
                    <small>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-link', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-link" name="__wpdm_ui_download_button[color]"> None</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-primary', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-primary" name="__wpdm_ui_download_button[color]"> Primary</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-secondary', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-secondary" name="__wpdm_ui_download_button[color]"> Secondary</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-info', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-info" name="__wpdm_ui_download_button[color]"> Info</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-success', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-success" name="__wpdm_ui_download_button[color]"> Success</label>
                        <label><input class="__wpdm_ui_download_button_color" <?php checked('btn-danger', (isset($ui_button['color'])?$ui_button['color']:'')); ?> type="radio" value="btn-danger" name="__wpdm_ui_download_button[color]"> Danger</label>
                    </small>
                </td>
                <td rowspan="2" valign="middle" align="center" style="width: 200px">
                    <button id="__wpdm_ui_download_button" type="button" style="border-radius: <?php echo (isset($ui_button['borderradius'])?$ui_button['borderradius']:4);?>px" class="<?php echo wpdm_download_button_style(true); ?>"><?php _e("Download", "download-manager");  ?></button>
                </td>
            </tr>
            <tr>
                <td valign="middle">
                    <small>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('btn-xs', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="btn-xs" name="__wpdm_ui_download_button[size]"> Extra Small</label>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('btn-sm', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="btn-sm" name="__wpdm_ui_download_button[size]"> Small</label>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="" name="__wpdm_ui_download_button[size]"> Regular</label>
                        <label><input class="__wpdm_ui_download_button_size" <?php checked('btn-lg', (isset($ui_button['size'])?$ui_button['size']:'')); ?> type="radio" value="btn-lg" name="__wpdm_ui_download_button[size]"> Large</label>
                        <div style="width: 160px;float: right;" class="input-group input-group-sm"><div class="input-group-addon"><?php echo __( "Border Radius", "download-manager" ) ?>:</div><input min="0" max="999" id="__wpdm_ui_download_button_br"  name="__wpdm_ui_download_button[borderradius]" data-target="#__wpdm_ui_download_button" class="form-control" type="number" value="<?php echo (isset($ui_button['borderradius'])?$ui_button['borderradius']:4);?>" /></div>
                    </small>
                </td>
            </tr>
            <tr>
                <th colspan="2"><?php _e("Shortcode Page", "download-manager");  ?></th>
            </tr>
            <tr>
                <td valign="middle" title="Select Button Color">
                    <small>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-link', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-link" name="__wpdm_ui_download_button_sc[color]"> None</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-primary', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-primary" name="__wpdm_ui_download_button_sc[color]"> Primary</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-secondary', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-secondary" name="__wpdm_ui_download_button_sc[color]"> Secondary</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-info', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-info" name="__wpdm_ui_download_button_sc[color]"> Info</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-success', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-success" name="__wpdm_ui_download_button_sc[color]"> Success</label>
                        <label><input class="__wpdm_ui_download_button_sc_color" <?php checked('btn-danger', (isset($ui_button_sc['color'])?$ui_button_sc['color']:'')); ?> type="radio" value="btn-danger" name="__wpdm_ui_download_button_sc[color]"> Danger</label>
                    </small>
                </td>
                <td rowspan="2" valign="middle" align="center" style="width: 200px">
                    <button id="__wpdm_ui_download_button_sc" type="button" class="<?php echo wpdm_download_button_style(); ?>"  style="border-radius: <?php echo (isset($ui_button_sc['borderradius'])?$ui_button_sc['borderradius']:4);?>px"><?php _e("Download", "download-manager");  ?></button>
                </td>
            </tr>
            <tr>
                <td valign="middle">
                    <small>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('btn-xs', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="btn-xs" name="__wpdm_ui_download_button_sc[size]"> Extra Small</label>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('btn-sm', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="btn-sm" name="__wpdm_ui_download_button_sc[size]"> Small</label>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="" name="__wpdm_ui_download_button_sc[size]"> Regular</label>
                        <label><input class="__wpdm_ui_download_button_sc_size" <?php checked('btn-lg', (isset($ui_button_sc['size'])?$ui_button_sc['size']:'')); ?> type="radio" value="btn-lg" name="__wpdm_ui_download_button_sc[size]"> Large</label>
                        <div style="width: 160px;float: right;" class="input-group input-group-sm"><div class="input-group-addon"><?php echo __( "Border Radius", "download-manager" ) ?>:</div><input min="0" max="999" data-target="#__wpdm_ui_download_button_sc" id="__wpdm_ui_download_button_sc_br" class="form-control" type="number"  name="__wpdm_ui_download_button_sc[borderradius]" value="<?php echo (isset($ui_button_sc['borderradius'])?$ui_button_sc['borderradius']:4);?>" /></div>
                    </small>
                </td>
            </tr>

        </table>

</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Image and Previews",'download-manager'); ?></div>
    <div class="panel-body"><input type="hidden" name="__wpdm_crop_thumbs" value="0">
      <label><input type="checkbox" name="__wpdm_crop_thumbs" value="1" <?php checked(1, get_option('__wpdm_crop_thumbs', 0)); ?> /> <?php echo __( "Crop thumbnails", "download-manager" ) ?></label>
    </div>
</div>

<style>
    .color-control{
        padding: 0 !important;
        height: 24px;
        text-align: center;
    }
    .wp-picker-container .wp-color-result.button{
        width: 28px;
        float: right;
        padding: 2px !important;
        height: 28px;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 500px;
    }
    .wp-picker-holder{
        position: absolute;
        z-index: 999999;
        margin-top: 26px;
    }
    .color-group{
        float: right;
        margin-top: -2px !important;
        margin-right: -12px !important;
    }
    .wp-picker-container{
        float: left;
    }
    .wp-color-result{
        border-bottom: 0 !important;
    }
    .wp-picker-input-wrap label{
        margin: 0 !important;
    }
    .wp-picker-container input[type="text"].wp-color-picker{
        margin: 0 !important;
        box-shadow: none;
        border-radius: 0;
        height: 25px;
    }
    .wp-picker-input-wrap .button{
        border-bottom: 0;
        border-radius: 0;
    }
    .wp-color-result-text{
        display: none;
    }
    .wp-picker-input-wrap{
        position: absolute;
        margin-left: 28px;
        width: 150px;
    }
    #__wpdm_google_font{
        width: 400px !important;
    }
</style>
<script>
    jQuery(function($){

        $('.color-control').wpColorPicker({
            change: function (event, ui) {
                let root = document.documentElement;
                root.style.setProperty($(this).data('css-var'), ui.color.toString());
            }
        });

        $('.__wpdm_ui_download_button_color, .__wpdm_ui_download_button_size').on('change', function () {
            $('#__wpdm_ui_download_button').attr('class', 'btn '+ $('.__wpdm_ui_download_button_color:checked').val() + ' ' + $('.__wpdm_ui_download_button_size:checked').val());
        });

        $('.__wpdm_ui_download_button_sc_color, .__wpdm_ui_download_button_sc_size').on('change', function () {
            $('#__wpdm_ui_download_button_sc').attr('class', 'btn '+ $('.__wpdm_ui_download_button_sc_color:checked').val() + ' ' + $('.__wpdm_ui_download_button_sc_size:checked').val());
        });

        $('#__wpdm_ui_download_button_br, #__wpdm_ui_download_button_sc_br').on('change', function () {
            $($(this).data('target')).css('border-radius', $(this).val()+'px');
        });

        $.getJSON("https://www.googleapis.com/webfonts/v1/webfonts?key=<?php echo get_option('_wpdm_google_app_secret', 'AIzaSyCgvNB-55xoUiz1zKIJgFPQbqyn4lCCB_E'); ?>", function(fonts){
            $('#__wpdm_google_font')
                .append($("<option></option>")
                    .attr("value", '')
                    .text('-- NONE SELECTED --'));
            $('#__wpdm_google_font').each(function () {
                for (var i = 0; i < fonts.items.length; i++) {
                    if(fonts.items[i].family === $(this).data('selected'))
                        $(this)
                            .append($("<option></option>")
                                .attr("value", fonts.items[i].family)
                                .attr("selected", "selected")
                                .text(fonts.items[i].family));
                    else
                        $(this)
                            .append($("<option></option>")
                                .attr("value", fonts.items[i].family)
                                .text(fonts.items[i].family));
                }
                $(this).trigger("chosen:updated");
            });



        });

    });
</script>
