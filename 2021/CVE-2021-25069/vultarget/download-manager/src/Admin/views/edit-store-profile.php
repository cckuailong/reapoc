<?php
/**
 * User: shahnuralam
 * Date: 6/24/18
 * Time: 10:47 PM
 */
if (!defined('ABSPATH')) die();

/*

?>

<style>

    .panel.with-nav-tabs .nav-tabs{
        padding-top: 10px;
        padding-left: 10px;
        background: #fafafa;
    }
    .panel.with-nav-tabs .nav-justified{
        margin-bottom: -1px;
    }

    .with-nav-tabs.panel-default .nav-tabs > li > a,
    .with-nav-tabs.panel-default .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-default .nav-tabs > li > a:focus {
        color: #777;
    }
    .with-nav-tabs.panel-default .nav-tabs > .open > a,
    .with-nav-tabs.panel-default .nav-tabs > .open > a:hover,
    .with-nav-tabs.panel-default .nav-tabs > .open > a:focus,
    .with-nav-tabs.panel-default .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-default .nav-tabs > li > a:focus {
        color: #777;
        background-color: #ddd;
        border-color: transparent;
    }
    .with-nav-tabs.panel-default .nav-tabs > li.active > a,
    .with-nav-tabs.panel-default .nav-tabs > li.active > a:hover,
    .with-nav-tabs.panel-default .nav-tabs > li.active > a:focus {
        color: #555;
        background-color: #fff;
        border-color: #ddd;
        border-bottom-color: transparent;
    }
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu {
        background-color: #f5f5f5;
        border-color: #ddd;
    }
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a {
        color: #777;
    }
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
        background-color: #ddd;
    }
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a,
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
    .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
        color: #fff;
        background-color: #555;
    }
</style>
<div class="panel with-nav-tabs panel-default">
    <div class="panel-heading">
        WordPress Download Manager
    </div>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1default" data-toggle="tab">Default 1</a></li>
        <li><a href="#tab2default" data-toggle="tab">Default 2</a></li>
        <li><a href="#tab3default" data-toggle="tab">Default 3</a></li>

    </ul>
    <div class="panel-body">
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab1default">Default 1</div>
            <div class="tab-pane fade" id="tab2default">Default 2</div>
            <div class="tab-pane fade" id="tab3default">Default 3</div>
            <div class="tab-pane fade" id="tab4default">Default 4</div>
            <div class="tab-pane fade" id="tab5default">Default 5</div>
        </div>
    </div>
</div>
<?php //*/ ?>
<div class="panel panel-default dashboard-panel">
    <div class="panel-heading">
        <?php _e( "Public Profile Info" , "download-manager" ); ?>
    </div>
    <div class="panel-body">

        <div class="form-group">
            <label><?php _e( "Title" , "download-manager" ); ?></label>
            <input type="text" value="<?php if (isset($store['title'])) echo $store['title']; ?>" placeholder="" id="" name="__wpdm_public_profile[title]" class="form-control">
        </div>
        <div class="form-group">
            <label><?php _e( "Short Intro" , "download-manager" ); ?></label>
            <input type="text" value="<?php if (isset($store['intro'])) echo $store['intro']; ?>" placeholder="" id="" name="__wpdm_public_profile[intro]" class="form-control">
        </div>
        <div class="form-group">
            <label for="store-logo"><?php _e( "Logo URL" , "download-manager" ); ?></label>
            <div class="input-group">
                <input type="text" name="__wpdm_public_profile[logo]" id="store-logo" class="form-control" value="<?php echo isset($store['logo']) ? $store['logo'] : ''; ?>"/>
                <span class="input-group-btn">
                        <button class="btn btn-secondary wpdm-media-upload" type="button" rel="#store-logo"><i class="far fa-image"></i></button>
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label for="store-banner"><?php _e( "Banner URL" , "download-manager" ); ?></label>
            <div class="input-group">
                <input type="text" name="__wpdm_public_profile[banner]" id="store-banner" class="form-control" value="<?php echo isset($store['banner']) ? $store['banner'] : ''; ?>"/>
                <span class="input-group-btn">
                        <button class="btn btn-secondary wpdm-media-upload" type="button" rel="#store-banner"><i class="far fa-image"></i></button>
                    </span>
            </div>
        </div>
        <div class="form-group">
            <label for="store-banner-bg"><?php _e( "Profile Header Background Color" , "download-manager" ); ?></label>
            <input type="color" name="__wpdm_public_profile[bgcolor]" id="store-banner-bg" class="form-control color-picker" value="<?php echo isset($store['bgcolor']) ? $store['bgcolor'] : '#eeeeee'; ?>"/>
        </div>
        <div class="form-group">
            <label for="store-banner"><?php _e( "Profile Header Text Color" , "download-manager" ); ?></label>

            <input type="color" name="__wpdm_public_profile[txtcolor]" id="store-banner" class="form-control color-picker" value="<?php echo isset($store['txtcolor']) ? $store['txtcolor'] : '#333333'; ?>"/>

        </div>
        <div class="form-group">
            <label><?php _e( "Description" , "download-manager" ); ?></label>
            <textarea type="text" data-placeholder="<?php _e( "Description" , "download-manager" ); ?>" id="" name="__wpdm_public_profile[description]" class="form-control"><?php if (isset($store['description'])) echo $store['description']; ?></textarea>
        </div>
    </div>

</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php _e( "Payment Settings" , "download-manager" ); ?>
    </div>
    <div class="panel-body">
        <label><?php _e( "PayPal Email" , "download-manager" ); ?></label>
        <input type="email" value="<?php if (isset($store['paypal'])) echo $store['paypal']; ?>" placeholder="" id="" name="__wpdm_public_profile[paypal]" class="form-control">
    </div>
</div>

<script>
    jQuery(document).ready(function($){
        $('.color-picker').wpColorPicker();
    });
</script>
