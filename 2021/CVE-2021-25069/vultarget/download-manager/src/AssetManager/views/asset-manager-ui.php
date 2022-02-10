<?php

use WPDM\AssetManager\AssetManager;

if(!defined('ABSPATH')) die('Error!');
global $current_user;
$root = AssetManager::root();
//$items = file_exists($root)?glob($root.'*', GLOB_ONLYDIR):array();
if(is_admin()){
    ?>

    <div>

    <?php
}
?>
    <style>
        @import url("https://fonts.googleapis.com/css?family=Overpass+Mono&subset=latin");
        #wpcontent{
            padding-left: 0 !important;
        }
        #mainfmarea{
            width: calc(100% - 160px);
            position: fixed;
            z-index: 99;
        }
        #wpdm-dashboard-content{
            overflow: visible;
        }
        #rename, #upfile, #newfol{
            z-index: 999999999999;
            padding-top: 150px;
            overflow: hidden;
        }
        #upfile{
            width: 350px;
        }
        #upfile .drag-drop .drag-drop-inside{
            margin: 45px auto 0;
            width: 300px;
        }
        #upfile .panel-heading{
            box-shadow: none !important;
            background: #f5f5f5;
        }
        #filelist .panel-heading{
            padding-right: 30px;
        }
        .w3eden #upfile  .progress-bar-info {
            background-color: var(--color-purple);
        }
        #breadcrumb,
        .wpdm-file-locator,
        .wpdm-dir-locator a.explore-dir{
            font-family: 'Overpass Mono', sans-serif !important;
        }
        .well-file{
            font-family: Montserrat,sans-serif;
            font-size: 12px;
            line-height: 40px;
        }
        .progress:after{
            position: absolute;
            color: rgba(0,0,0,0.3);
            width: 100%;
            text-align: center;
            left: 0;
            font-family: Montserrat,sans-serif;
            font-size: 12px;
            text-transform: uppercase;
        }
        .panel-file{
            font-size: 12px;
        }
        .panel-file .media-body {
            line-height: normal;
            display: inline-block;
        }
        .item_label{
            margin-top: 5px;
            font-size: 9pt;
            line-height: 1;
            display: block;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #5e6a6d;
        }

        .w3eden .well .btn-sm{
            padding: 8px 16px;
        }
        .panel-file .panel-footer{
            text-align: center;
        }
        .modal *{
            font-size: 10pt;
        }
        .w3eden .modal-header{
            font-size: 10pt;
            line-height: normal;
            font-family: var(--wpdm-font);
            background: #f5f5f5;
            border-radius: 10px 10px 0 0 !important;
        }
        .w3eden .modal-footer{
            border: 0 !important;
            border-radius: 0 0 10px 10px !important;
            padding-top: 0;
        }
        .w3eden .modal-content{
            border-radius: 10px !important;
            box-shadow: 0 0 25px rgba(0,0, 0, 0.2);
        }
        #drag-drop-area{
            border: 0.2rem dashed #28c83599 !important;
            border-radius: 5px;
            font-family: "Overpass Mono", monospace;
            color: rgba(0,0,0,0.2);
            font-size: 14pt;
        }
        #drag-drop-area .btn{
            font-family: "Overpass Mono", monospace;
            border-radius: 500px;
        }

        #breadcrumb{
            margin: 10px 0 10px;
            font-size: 9pt;
            color: #888888;
        }

        .w3eden .panel.wpdm-file-manager-panel{
            border: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin-bottom: 0 !important;
        }
        .w3eden .panel.wpdm-file-manager-panel .panel-heading{
            /*background: linear-gradient(to bottom, rgb(253, 255, 255) 0%,rgb(240, 243, 243) 100%);*/
            background: rgba(240, 243, 243, 0.3);
            border-top: 1px solid #e7eaea !important;
            border-bottom: 1px solid #e7eaea !important;
            /*box-shadow: 2px 0 5px rgba(18, 150, 201, 0.09);*/

        }
        .panel-heading,
        #breadcrumb a{
            color: #aaaaaa;
            text-transform: unset !important;
            font-weight: 400;
        }
        #breadcrumb .fa{
            color: #8896aa;
            display: inline-block;
            margin-top: -2px !important;
            vertical-align: middle;
            margin-right: 5px;
            margin-left: 5px;
        }
        .panel-file .panel-body{
            height: 130px;
            text-align: center;
        }
        #scandir .file-row,
        #scandir .dir-row{
            padding: 10px 10px 10px 0;
            border-bottom: 1px solid #eeeeee;
            -webkit-transition: all ease-in-out 300ms;
            -moz-transition: all ease-in-out 300ms;
            -ms-transition: all ease-in-out 300ms;
            -o-transition: all ease-in-out 300ms;
            transition: all ease-in-out 300ms;
        }
        #scandir .file-row:hover, #scandir .dir-row:hover {
            background: rgba(0,0,0,0.02);
            -webkit-transition: all ease-in-out 300ms;
            -moz-transition: all ease-in-out 300ms;
            -ms-transition: all ease-in-out 300ms;
            -o-transition: all ease-in-out 300ms;
            transition: all ease-in-out 300ms;

        }
        img.icon{
            width: 38px;
            margin-right: 5px;
            padding: 0 !important;
        }
        #scandir img{
            box-shadow: none;
            margin-bottom: 0;
        }
        .action-btns-ctrl{
            line-height: 40px;
            width: 32px;
            text-align: center;
            outline: none !important;
            margin-right: 5px;
        }
        .action-btns {
            opacity: 0 !important;
            position: absolute !important;
            z-index: -99;
            right: 50px;
            white-space: nowrap;
            -webkit-transition: all ease-in-out 400ms;
            -moz-transition: all ease-in-out 400ms;
            -ms-transition: all ease-in-out 400ms;
            -o-transition: all ease-in-out 400ms;
            transition: all ease-in-out 400ms;
            line-height: 40px;
        }
        .action-btns.action-btns-show{
            opacity: 1 !important;
            z-index: 999;
        }

        .action-btns .btn.btn-xs {
            width: 32px;
            height: 28px;
            line-height: 26px;
            padding: 0;
            font-size: 8pt;
            text-align: center;
            border-radius: 5px;
        }

        img.fm-folder{
            box-shadow: none;
            width: 16px;
            display: inline-block;
            vertical-align: middle;
        }
        .wpdmfm-folder-tree{
            padding: 0;
            margin: 0;
        }

        .wpdmfm-folder-tree li{
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 12px;
            color: #757f8d;
            white-space: nowrap;
            line-height: 20px;
        }
        .wpdmfm-folder-tree li > .handle{
            background: url("<?php echo WPDM_BASE_URL.'assets/images/folder.svg' ?>") left center no-repeat;
            background-size: 16px;
            display: inline-block;
            width: 20px;
            height: 20px;
            float: left;
            cursor: pointer;
        }
        .wpdmfm-folder-tree ul{
            margin-left: 18px !important;
            padding-left: 0;
        }
        .wpdmfm-folder-tree li a:visited,
        .wpdmfm-folder-tree li a:hover,
        .wpdmfm-folder-tree li a{
            color: #657989;
            text-decoration:  none;
            font-family: var(--wpdm-font);
            cursor: pointer;
        }
        .wpdmfm-folder-tree li a:hover{
            color: #6075c8;
        }
        .wpdmfm-folder-tree li.expanded > .handle{
            background: url("<?php echo WPDM_BASE_URL.'assets/images/folder-o.svg' ?>") left 2px no-repeat;
            background-size: 16px;
        }
        .wpdmfm-folder-tree li.busy > .handle{
            background: url("<?php echo WPDM_BASE_URL.'assets/images/loader.svg' ?>") left no-repeat;
            background-size: 16px;
        }
        .wpdm-dir-locator,
        .wpdm-file-locator{
            padding: 15px !important;
        }
        .wpdm-dir-locator{
            background: rgba(240, 243, 243, 0.2);
            border-right: 1px solid #e7eaea;
        }

        [data-simplebar]{
            height: 500px;
            overflow: auto;
            min-width: 100%;
        }
        .wpdm-dir-locator .simplebar-content{
            overflow-x: auto !important;
        }
        .wpdm-file-locator [data-simplebar]{
            overflow-x: hidden !important;
        }

        #wpbody-content{
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        #mainfmc{
            overflow: hidden;
        }
        .w3eden #mainfmc .btn{
            text-transform: capitalize !important;
            font-family: var(--wpdm-font) !important;
            font-weight: 400 !important;
        }

        .w3eden .btn.btn-simple:not(:hover){
            color: #7886a2;
            border-color: #c9cfdb;
        }

        .w3eden  .btn.btn-simple:hover { background-color: rgba(201, 207, 219, 0.2) !important; }
        .w3eden  .btn.btn-simple:hover:not(.btn-danger):not(.btn-info):not(.btn-primary):not(.btn-success){ color: #3c5382; }
        .w3eden  .btn.btn-simple.btn-success:hover { background-color: rgba(var(--color-success-rgb), 0.1) !important; }
        .w3eden  .btn.btn-simple.btn-primary:hover { background-color: rgba(var(--color-primary-rgb), 0.1) !important; }
        .w3eden  .btn.btn-simple.btn-danger:hover { background-color: rgba(var(--color-danger-rgb), 0.1) !important; }
        .w3eden  .btn.btn-simple.btn-info:hover { background-color: rgba(var(--color-info-rgb), 0.1) !important; }
        .w3eden  .btn.btn-simple.btn-warning:hover { background-color: rgba(var(--color-warning-rgb), 0.1) !important; color: var(--color-warning) !important; }

        .w3eden  .btn.btn-simple:active {
            box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.13);
        }

        #wpdmeditor{
            position: absolute;
            left: 0;
            top: 0;
            border: 0;
            width: 100%;
            height: 100%;
            z-index: 9;
            box-shadow: none;
            border-radius: 0;
        }
        #wpdmeditor .panel-heading{
            border-top: 0 !important;
        }
        #wpdmeditor .panel-heading,
        #wpdmeditor .panel-footer{
            border-radius: 0;
        }
        #wpdmeditor #filecontent_alt,
        #wpdmeditor #filecontent{
            height: calc(100% - 40px);
            width: 100%;
            padding: 30px;
            overflow: auto;
            font-family: "Overpass Mono", monospace;
            border: 0;
            background: transparent;
        }
        .CodeMirror.cm-s-default.CodeMirror-wrap{
            height: calc(100% - 94px);
        }
        #wpdmeditor #filecontent_alt{
            text-align: center;
        }
        #wpdmeditor #filecontent_alt img{
            max-width: 100%;
        }
        #filewin{
            -webkit-transition: all ease-in-out 400ms;
            -moz-transition: all ease-in-out 400ms;
            -ms-transition: all ease-in-out 400ms;
            -o-transition: all ease-in-out 400ms;
            transition: all ease-in-out 400ms;
        }

        .w3eden #__file_settings_tabs.nav.nav-tabs > li > a{
            box-shadow: none !important;
            border: 1px solid #e8e8e8;
            padding: 8px 16px;
            font-size: 10px;
            font-weight: 400 !important;
        }
        .w3eden #__file_settings_tabs.nav.nav-tabs > li.active > a{
            border-bottom: 1px solid #ffffff;
        }
        .w3eden #__file_settings_tabs.nav.nav-tabs > li:not(.active) > a{
            background: #fafafa;
        }

        .w3eden #__asset_settings .form-control.form-control-lg{
            border: 0;
            background: #ffffff; text-align: center; font-family: "Overpass Mono", monospace;font-size: 11pt !important;box-shadow: none !important;
        }
        .w3eden #__asset_settings .panel-default{
            border: 1px solid #e6e6e6;
        }
        .w3eden #__asset_settings .panel-default .panel-heading {
            border-bottom: 1px solid #e6e6e6;
            background: #fafafa;
        }
        .w3eden #__asset_settings .panel-default .panel-footer {
            border-top: 1px solid #e6e6e6;
            background: #fafafa;
        }

        .allow-roles label,
        .w3eden #__asset_settings .tab-content *,
        .w3eden #__asset_settings .tab-content{
            font-size: 11px;
        }
        .allow-roles label{
            font-weight: 400;
            line-height: 16px;
        }
        .allow-roles label input{
            margin: 0 5px !important;
        }


        .w3eden .modal-header .close.pull-right {
            height: 16px;
            line-height: 16px;
        }

        #__asset_links .asset-link{
            margin: 5px 0;
            border: 1px solid #e8e8e8;
            border-radius: 3px !important;
        }
        #__asset_links .asset-link .form-control{
            background: #ffffff;
            border: 0 !important;
            box-shadow: none !important;
        }
        #__asset_links .asset-link .input-group-addon{
            border: 0 !important;
            background: #ffffff;
            padding-right: 0;
            color: var(--color-info);
        }
        #__asset_links .asset-link .btn{
            border: 0 !important;
            background: #ffffff;
            color: var(--color-success);
            border-left: 1px solid #e8e8e8 !important;
            z-index: 2;
        }
        #__asset_links .asset-link .btn .fa-trash.color-danger{
            color: var(--color-danger) !important;
        }

        .w3eden #__asset_links .asset-link.input-group-lg > .form-control, .w3eden #__asset_links .asset-link.input-group-lg > .input-group-addon, .w3eden #__asset_links .asset-link.input-group-lg > .input-group-btn > .btn {
            height: 36px;
            padding: 8px 12px;
        }

        .wp-video{ margin: 0 auto !important; width: 100% !important; }
        .wp-video-shortcode,
        .wp-audio-shortcode {
            margin: 15px 15px 10px 15px;
            width: calc(100% - 30px) !important;
        }
        .wp-video-shortcode{
            height: auto !important;
        }

        #filelist .panel.upcompleted .panel-heading::before {
            content: "\f560";
            position: absolute;
            color: #41c441;
            right: 10px;
            font-family: "Font Awesome 5 Free";
            transition: all ease-in-out 400ms;

        }
        #filelist .panel.upfailed .panel-heading::before {
            content: "\f071";
            position: absolute;
            color: var(--color-red);
            right: 10px;
            font-family: "Font Awesome 5 Free";
            transition: all ease-in-out 400ms;

        }
        button.btn-unzip{
            display: none !important;
        }
        button.btn-unzip.application_zip{
            display: inline-block !important;
        }
        .modal-backdrop.in{
            display: none;
        }
        .w3eden .modal.fade.in{
            background: rgba(0,0,0,0.3);
        }
    </style>

<div class="w3eden" id="mainfmarea">
    <?php do_action("wpdm_frontend_filemanager_top", ""); ?>
    <div id="loadingfm" class="blockui" style="position: fixed;width: 100%;height: 100%;z-index: 99"></div>
    <div id="mainfmc" class="panel panel-default wpdm-file-manager-panel" style="display: none;">
        <div class="panel-body">
            <div class="media well-sm well-file" style="margin: 0;padding: 0">
                <div class="pull-right">
                    <button class="btn btn-primary btn-simple btn-sm ttip" title="Reload" id="reload"><i class="fa fa-sync"></i></button>
                    <div class="btn-group">
                        <button class="btn btn-simple btn-sm" data-toggle="modal" data-target="#newfol"><i class="fa fa-folder-open"></i> <?php echo __( "New Folder", "download-manager" ) ?></button>
                        <button class="btn btn-simple btn-sm" data-toggle="modal" data-target="#newfile"><i class="far fa-file"></i> <?php echo  esc_attr__( 'New File', WPDM_TEXT_DOMAIN ); ?></button>
                        <button class="btn btn-simple btn-sm" id="btn-upload-file" ><i class="fa fa-cloud-upload-alt"></i> <?php echo __( "Upload File", "download-manager" ) ?></button>
                    </div>
                    <button class="btn btn-info btn-simple btn-sm ttip" id="btn-paste" disabled="disabled" title="Paste"><i class="fa fa-clipboard"></i></button>
                    <button class="btn btn-simple btn-sm ttip" title="Full Screen"  onclick="openFullscreen('mainfmarea');"><i class="fa fa-expand-arrows-alt"></i></button>
                </div>
                <h3 style="display: inline-block;font-size: 14pt;letter-spacing: 0.5px;font-weight: 600;font-family: var(--wpdm-font)">
                   <img src="<?php echo WPDM_BASE_URL ?>assets/images/asset-manager.svg" style="height: 20px;margin-right: 5px" /> <?php echo __( "Digital Asset Manager", "download-manager" ) ?> <sup style="color: var(--color-info) !important;font-size: 10px;font-family: 'Overpass Mono', sans-serif !important;">BETA</sup>
                </h3>
                <?php /* if(!current_user_can('manage_options')){ ?>
                <div class="media-body">
                    <div class="progress text-center" style="margin: 2px 0 0 5px;height: 27px;line-height: 27px;border-radius;border-radius: 2px;font-family: 'Overpass Mono', sans-serif !important;">
                        Used: <span id="disklimit"><i class="fa fa-sun fa-spin"></i></span> | Limit: <?php echo wpdm_user_space_limit(); ?> MB
                        <div title="15% Used" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;line-height: 31px;font-size: 13px;overflow: visible">
                        </div>
                    </div>
                </div>
                <?php } */ ?>
            </div>
        </div>
        <div class="panel-heading" style="border-radius: 0;border-top: 1px solid #dddddd">
            <div id="ldn" style="float:right;font-size: 9pt;margin-top: 10px;display: none" class="text-danger"><i class="fa fa-sun fa-spin"></i> <?php echo  esc_attr__( 'Loading', WPDM_TEXT_DOMAIN ); ?>...</div>
            <div id="breadcrumb" style="margin: 0"></div>
        </div>
        <div class="panel-body-c" id="wpdmfm_explorer">

            <div class="row" style="margin: 0">
                <?php do_action("wpdm_frontend_filemanager_after_breadcrumb", ""); ?>
                <div class="col-md-3 wpdm-dir-locator">
                    <div data-simplebar ss-container>
                        <ul class="wpdmfm-folder-tree" id="wpdmfm-folder-tree">
                            <li data-path="" id="<?php echo md5('home'); ?>" class="expand-dir"><i class="fa fa-hdd color-purple"></i> <a class="explore-dir" href="#" data-path=""> <?php echo  esc_attr__( 'Home', WPDM_TEXT_DOMAIN ); ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-9 wpdm-file-locator" id="filewin">
                    <div id="wpdmeditor" class="panel panel-default blockui" style="display: none">
                        <div class="panel-heading"><div class="pull-right"><a id="close-editor" href="#"><i class="fa fa-times-circle text-muted"></i></a></div><span id="wpdmefn"></span></div>
                        <textarea id="filecontent"></textarea>
                        <div id="filecontent_alt" style="display: none"></div>
                        <div class="panel-footer text-right">
                            <button type="button" id="savefile" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo __( "Save Changes", "download-manager" ) ?></button>
                        </div>
                    </div>
                    <div data-simplebar ss-container>
                        <div id="scandir">
                        </div>
                    </div>
                </div>
                <div class="col-md-3" id="cogwin" style="display: none;">
                    <div class="panel panel-default blockui" style="left:0;width: 100%;height: 900px;border: 0;border-left: 1px solid #eee;border-radius: 0;position: absolute;">
                        <div class="panel-heading" style="border-radius: 0;border-top: 0 !important;">
                            <a href="#" class="pull-right" id="close-settings"><i class="fas fa-times-circle text-muted"></i></a>
                            <?php echo  esc_attr__( 'Asset Settings', WPDM_TEXT_DOMAIN ); ?>
                        </div>
                        <div class="panel-body" id="__asset_settings">
                            <div class="thumbnail text-center" v-html="asset.preview"></div>
                            <div class="form-group">
                                <div class="media" style="border:1px solid #e8e8e8;border-radius: 3px;padding: 10px;">
                                    <div class="pull-right">
                                        <button class="btn btn-xs btn-secondary btn-rename" type="button" title="<?php echo  esc_attr__( 'Rename', WPDM_TEXT_DOMAIN ); ?>" data-toggle="modal" data-target="#rename"><i class="fas fa-i-cursor"></i> <?php echo  esc_attr__( 'Rename', WPDM_TEXT_DOMAIN ); ?></button>
                                    </div>
                                    <div class="media-body">
                                        &nbsp;{{ asset.name }}
                                    </div>
                                </div>

                                <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="rename">
                                    <div class="modal-dialog modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                                                <strong class="modal-title" id="myModalLabel"><?php echo  esc_attr__( 'Rename', WPDM_TEXT_DOMAIN ); ?></strong>
                                            </div>
                                            <div id="upload" class="modal-body">
                                                <input type="text" v-bind:value="asset.name" placeholder="<?php echo  esc_attr__( 'New Name', WPDM_TEXT_DOMAIN ); ?>" id="newname" class="form-control form-control-lg" style="margin: 0px;border: 1px dashed #d4d4d4;">
                                            </div>
                                            <div class="modal-footer text-right">
                                                <button type="button" id="renamenow" class="btn btn-info"  :data-assetid="asset.ID" :data-oldname="asset.name" :data-path="asset.path"><?php echo  esc_attr__( 'Rename', WPDM_TEXT_DOMAIN ); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div>

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" id="__file_settings_tabs" role="tablist" style="margin-left: -15px;padding-left: 15px;margin-right: -15px;border-bottom: 1px solid #e8e8e8;">
                                    <li role="presentation" class="active"><a href="#share" aria-controls="share" role="tab" data-toggle="tab"><?php echo  esc_attr__( 'Share', WPDM_TEXT_DOMAIN ); ?></a></li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <!-- div role="tabpanel" class="tab-pane" id="activity">...</div -->
                                    <div role="tabpanel" class="tab-pane active" id="share">
                                        <div class="panel panel-default" style="margin-top: 15px;margin-bottom: 0;" v-if="asset.type === 'file'">
                                            <div class="input-group" style="border: 0 !important;">
                                                <input type="text" readonly="readonly" id="sharecode" class="form-control form-control-lg" v-bind:value="asset.sharecode" />
                                                <div onclick="jQuery('#sharecode').select();document.execCommand('copy');" class="input-group-addon ttip" title="<?php echo __( "Copy Shortcode", "download-manager" ) ?>" style="border: 0 !important;background: #ffffff;cursor: pointer;"><i class="fa fa-copy color-purple"></i></div>
                                            </div>
                                            <div class="panel-footer text-center">
                                                <?php echo  esc_attr__( 'Use the shortcode to embed this asset on any page or post', WPDM_TEXT_DOMAIN ); ?>
                                            </div>
                                        </div>




                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php do_action("wpdm_frontend_filemanager_bottom", ""); ?>
            </div>

        </div>
    </div>



    <div id="dirTPL" style="display: none">
        <div class="dir-row">
            <div class="row panel-file panel-folder">

                <div class="col-md-8">
                    <div class="media-folder media" data-id="{{dirid}}" data-path="{{path}}" style="cursor: pointer">
                        <img class="icon pull-left" src="<?php echo plugins_url('download-manager/assets/file-type-icons/folder.png'); ?>" />
                        <div class="dir-info"><div class="item_label" title="{{item}}">{{item_label}}</div><small class="color-purple">{{note}}</small></div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="action-btns" id="action-btns-{{id}}">
                        <button class="btn btn-xs btn-simple btn-primary btn-settings" type="button" title="Settings" data-oldname="{{item}}" data-path="{{path}}"><i class="far fa-sun"></i></button>
                        <button class="btn btn-xs btn-simple btn-success btn-zip" type="button" title="Zip" data-dirname="{{item}}" data-dirpath="{{path}}"><i class="fa fa-archive"></i></button>
                        <button class="btn btn-xs btn-info btn-simple" data-item="{{item}}" title="Cut" type="button"><i class="fa fa-cut"></i></button>
                        <button class="btn btn-xs btn-primary btn-simple btn-copy" data-item="{{item}}" title="Copy" type="button"><i class="fa fa-copy"></i></button>
                        <button class="btn btn-xs btn-danger btn-simple btn-delete" type="button" title="Delete" data-path="{{path}}"><i class="fas fa-trash"></i></button>
                    </div>
                    <a href="#" class="text-muted pull-right action-btns-ctrl" data-target="#action-btns-{{id}}"><i class="fas fa-ellipsis-v"></i></a>
                </div>

            </div>
        </div>
    </div>

    <div id="fileTPL" style="display: none">

            <div class="file-row">
                <div class="row panel-file file-tpl">
                    <div class="col-md-8  text-left btn-open-file c-pointer" data-filetype="{{contenttype}}" data-path="{{path}}">

                        <div class="file-info media">
                            <img class="icon pull-left" src="{{icon}}" />
                            <div class="dir-info"><div class="item_label" title="{{item}}">{{item_label}}</div><small class="color-purple">{{note}}</small></div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="action-btns" id="action-btns-{{id}}">
                            <button class="btn btn-xs btn-simple btn-primary btn-settings" type="button" title="Settings" data-oldname="{{item}}" data-path="{{path}}"><i class="far fa-sun"></i></button>
                            <button class="btn btn-xs btn-simple btn-warning btn-unzip {{ext}}"  type="button" title="UnZip" data-dirname="{{item}}" data-dirpath="{{path}}"><i class="fas fa-box-open"></i></button>
                            <button class="btn btn-xs btn-simple btn-cut" data-item="{{item}}" title="Cut" type="button"><i class="fa fa-cut"></i></button>
                            <button class="btn btn-xs btn-simple btn-copy" data-item="{{item}}" title="Copy" type="button"><i class="fa fa-copy"></i></button>
                            <a class="btn btn-xs btn-simple btn-download" type="button" title="Download" href="<?php echo home_url('/?wpdmfmdl={{path}}') ?>"><i class="fas fa-arrow-down"></i></a>
                            <button class="btn btn-xs btn-simple btn-danger btn-delete" title="Delete" type="button" data-path="{{path}}"><i class="fas fa-trash"></i></button>
                        </div>
                        <a href="#" class="text-muted pull-right action-btns-ctrl" data-target="#action-btns-{{id}}"><i class="fas fa-ellipsis-v"></i></a>
                    </div>
                </div>
            </div>

    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="__upfile">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">



            </div>
        </div>
    </div>

    <div id="upfile" style="position: fixed;z-index: 999999;bottom: 0px;right: 40px;display: none">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right c-pointer" onclick="jQuery('#upfile').slideUp();"><i class="far fa-window-close color-red"></i></div>
            <?php echo __( "Upload File", "download-manager" ) ?>
        </div>
        <div class="panel-body">
            <div id="upload" class="modal-body">
                <div id="plupload-upload-ui" class="hide-if-no-js">
                    <div id="drag-drop-area">
                        <div class="drag-drop-inside">
                            <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                            <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                            <p class="drag-drop-buttons"><button id="plupload-browse-button" type="button" class="btn btn-success"> &mdash; <?php esc_attr_e('Select Files'); ?> &mdash; </button></p>
                        </div>
                    </div>
                </div>

                <?php
                $slimit = get_option('__wpdm_max_upload_size',0);
                if($slimit>0)
                    $slimit = wp_convert_hr_to_bytes($slimit.'M');
                else
                    $slimit = wp_max_upload_size();

                $plupload_init = array(
                    'runtimes'            => 'html5,silverlight,flash,html4',
                    'browse_button'       => 'plupload-browse-button',
                    'container'           => 'plupload-upload-ui',
                    'drop_element'        => 'drag-drop-area',
                    'file_data_name'      => (current_user_can(WPDM_ADMIN_CAP)?'package_file':'attach_file'),
                    'multiple_queues'     => true,
                    'max_file_size'       => $slimit.'b',
                    'url'                 => admin_url('admin-ajax.php'),
                    'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                    'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                    'filters'             => array(array('title' => __('Allowed Files'), 'extensions' =>  implode(",", WPDM()->fileSystem->getAllowedFileTypes()))),
                    'multipart'           => true,
                    'urlstream_upload'    => true,

                    // additional post data to send to our ajax hook
                    'multipart_params'    => array(
                        '_ajax_nonce' => wp_create_nonce(NONCE_KEY),
                        '__noconflict' => 1,
                        '__wpdmfm_upload' => wp_create_nonce(NONCE_KEY),
                        'action'      => (current_user_can(WPDM_ADMIN_CAP)?'wpdm_admin_upload_file':'wpdm_frontend_file_upload'),            // the ajax action name
                    ),
                );

                if(get_option('__wpdm_chunk_upload',0) == 1){
                    $plupload_init['chunk_size'] = get_option('__wpdm_chunk_size', 1024).'kb';
                    $plupload_init['max_retries'] = 3;
                } else
                    $plupload_init['max_file_size'] = wp_max_upload_size().'b';

                // we should probably not apply this filter, plugins may expect wp's media uploader...
                $plupload_init = apply_filters('plupload_init', $plupload_init); ?>



            </div>
        </div>

    </div>

        <div id="filelist"></div>
        <div  style="clear: both"></div>
    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="newfol">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                    <strong class="modal-title" id="myModalLabel"><?php echo  esc_attr__( 'New Folder', WPDM_TEXT_DOMAIN ); ?></strong>
                </div>
                <div id="upload" class="modal-body">
                    <input type="text" placeholder="Folder Name" id="folname" class="form-control form-control-lg" style="margin: 0">
                </div>
                <div class="modal-footer text-right">
                    <button type="button" id="createfol" class="btn btn-info"><?php echo  esc_attr__( 'Create Folder', WPDM_TEXT_DOMAIN ); ?></button>
                    <div style="float:left;display: none;" id="fcd" class="text-success"><i class="fa fa-check-circle"></i> <?php echo  esc_attr__( 'Folder Created', WPDM_TEXT_DOMAIN ); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="newfile">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                    <strong class="modal-title" id="myModalLabel"><?php echo  esc_attr__( 'New File', WPDM_TEXT_DOMAIN ); ?></strong>
                </div>
                <div id="upload" class="modal-body">
                    <input type="text" placeholder="File Name" id="filename" class="form-control form-control-lg" style="margin: 0">
                </div>
                <div class="modal-footer text-right">
                    <button type="button" id="createfile" class="btn btn-info"><?php echo  esc_attr__( 'Create File', WPDM_TEXT_DOMAIN ); ?></button>
                    <div style="float:left;display: none;" id="fcd" class="text-success"><i class="fa fa-check-circle"></i> <?php echo  esc_attr__( 'File Created', WPDM_TEXT_DOMAIN ); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="__link_settings">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                    <strong class="modal-title"><?php echo  esc_attr__( 'Update link', WPDM_TEXT_DOMAIN ); ?></strong>
                </div>
                <div class="modal-body">
                    <form id="update_sharelink_form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <?php wp_nonce_field(WPDMAM_NONCE_KEY, '__wpdm_updatelink'); ?>
                        <input type="hidden" name="action" value="wpdm_updatelink" />
                        <input type="hidden" name="ID" v-bind:value="link.ID" />
                        <div class="panel panel-default">
                            <div class="panel-heading" style="box-shadow: none !important;border-bottom: 1px solid #e3e3e3 !important;border-top: 0 !important;"><?php echo __( "Authorized User Groups:", "download-manager" ) ?></div>
                            <div class="panel-body allow-roles">

                                <div class="row">
                                    <div class="col-md-3">
                                        <label><input v-bind:checked="roleSelected('guest')" type="checkbox" name="access[roles][]" value="guest" /> <?php echo __( "All Visitors" , "download-manager" ); ?></label>
                                    </div>

                                    <?php
                                    global $wp_roles;
                                    $roles = array_reverse($wp_roles->role_names);
                                    foreach( $roles as $role => $name ) {
                                        ?>
                                        <div class="col-md-3"><label><input v-bind:checked="roleSelected('<?php echo $role; ?>')" type="checkbox" name="access[roles][]" value="<?php echo $role; ?>" /> <?php echo $name; ?></label></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <?php
                            if(is_plugin_active('wpdm-custom-access-level/wpdm-custom-access-level.php')){ ?>


                                <div class="panel-heading" style="box-shadow: none !important;border-radius: 0;border-top: 1px solid #e3e3e3;"><?php echo __('Authorized Users:','download-manager'); ?></div>
                                <div class="panel-body" id="_uaco">


                                    <span style="margin-right: 3px" class="btn btn-simple btn-sm" id="uaco-admin" v-for="user in link.access.users">
                                        <input type="hidden" name="access[users][]" value="admin">
                                        <a class="uaco-del" onclick="jQuery(this.rel).remove()" v-bind:rel="'#uaco-' + user"><i class="far fa-times-circle"></i></a>&nbsp;{{ user }}
                                    </span>


                                </div>
                                <div class="panel-footer"><input id="_maname" placeholder="Start typing to search members..." style="width: 100%" type="text" class="form-control"></div>


                            <?php } ?>

                        </div>

                        <div class="text-right">
                            <button type="submit" id="create_sharelink" class="btn btn-info"><?php echo  esc_attr__( 'Update Link', WPDM_TEXT_DOMAIN ); ?></button>
                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>




</div>

    <script src="<?php echo WPDM_BASE_URL ?>assets/js/vue.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL ?>assets/js/simple-scrollbar.min.js"></script>
<script>
    var current_path = '', editor = '', opened = '', wpdmfm_active_asset = '', wpdmfm_active_asset_settings;
    var _path = localStorage.getItem('__wpdm_am_cp');
    if(_path) current_path = _path;
    var assetSettings = new Vue({
        el: '#__asset_settings',
        data: {
            asset: []
        }
    });

    var linkSettings = new Vue({
        el: '#__link_settings',
        data: {
            link: {
                access: {
                    roles: ['guest'],
                    users:['admin']
                }
            }
        },
        methods: {
            roleSelected: function (role) {
                for(var i=0; i < this.link.access.roles.length; i++){
                    if( this.link.access.roles[i] == role){
                        return true
                    }
                }
                return false
            }
        }
    });

    /* View in fullscreen */
    function openFullscreen(elementid) {
        var elem = document.getElementById(elementid);
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) { /* Firefox */
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE/Edge */
            elem.msRequestFullscreen();
        }
    }


    jQuery(function ($) {

        //$('#wpdmfm_explorer').css('height', (window.innerHeight - 190)+'px');

        $('#mainfmc').fadeIn();
        $('#loadingfm').hide();



        function refresh_scandir(path) {
            hide_editor();
            hide_settings();
            $('#reload').attr('disabled', 'disabled').find('.fa').addClass('fa-spin');
            WPDM.blockUI('#filewin');
            localStorage.setItem('__wpdm_am_cp', path);
            $.get(ajaxurl, {__wpdm_scandir:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_scandir', path: path}, function (data) {
                if(data.success === true) {
                    $('#scandir').html('');
                    var items = data.items;
                    $.each(items, function (index, entry) {
                        if (entry.type == 'file') {
                            ext = entry.contenttype.replace("/", "_");
                            var tpl = $('#fileTPL').html();
                            tpl = tpl.replace("{{icon}}", entry.icon);
                            tpl = tpl.replace("{{ext}}", ext);
                            tpl = tpl.replace("{{contenttype}}", entry.contenttype);
                            tpl = tpl.replace("{{item_label}}", entry.item_label);
                            tpl = tpl.replace("{{note}}", entry.note);
                            tpl = tpl.replace("{{file_size}}", entry.file_size);
                            tpl = tpl.replace(/\{\{path\}\}/ig, entry.path);
                            tpl = tpl.replace(/\{\{item\}\}/ig, entry.item);
                            tpl = tpl.replace(/\{\{id\}\}/ig, index);
                        } else {
                            var tpl = $('#dirTPL').html();
                            tpl = tpl.replace("{{icon}}", entry.icon);
                            tpl = tpl.replace("{{item_label}}", entry.item_label);
                            tpl = tpl.replace("{{note}}", entry.note);
                            tpl = tpl.replace("{{file_size}}", entry.file_size);
                            tpl = tpl.replace(/\{\{path\}\}/ig, entry.path);
                            tpl = tpl.replace(/\{\{item\}\}/ig, entry.item);
                            tpl = tpl.replace(/\{\{id\}\}/ig, index);
                            tpl = tpl.replace(/\{\{dirid\}\}/ig, entry.id);
                        }
                        $('#scandir').append(tpl);
                    });
                    WPDM.unblockUI('#filewin');
                    $('#reload').removeAttr('disabled', 'disabled').html("<i class='fa fa-sync'></i>");
                    $('#breadcrumb').html(data.breadcrumb);

                } else {
                    WPDM.pushNotify("<?php echo esc_attr__( 'Error', 'download-manager' ); ?>!", data.message, 'https://cdn0.iconfinder.com/data/icons/small-n-flat/24/678080-shield-error-256.png', 'https://cdn0.iconfinder.com/data/icons/small-n-flat/24/678080-shield-error-256.png');
                }
            });
        }

        function hide_editor() {
            $('#wpdmeditor').fadeOut();
            if(editor == '') return;
            editor.codemirror.toTextArea();
            $('#filecontent').val('');
            $('#wpdmeditor').addClass('blockui');
        }

        function hide_settings() {
            $('#cogwin').hide();
            $('#filewin').removeClass('col-md-6').addClass('col-md-9');
            $('#cogwin > .panel').addClass('blockui');
        }

        function auto_expand()
        {
            var expanded = localStorage.getItem('__wpdmam_xdirs');
            if(!expanded) expanded = "";
            expanded = expanded.split(",");

        }

        function expand_dir(id) {

            /*var expanded = localStorage.getItem('__wpdmam_xdirs');
            if(!expanded) expanded = "";
            expanded = expanded.split(",");
            expanded.push(id);
            localStorage.setItem('__wpdmam_xdirs', expanded);*/

            localStorage.setItem('__expanded_'+id, true);

            var $this = $('#'+id);
            $this.addClass('busy');
            var chid = "expanded_" + id;
            var slide = 1;
            var _ajaxurl = ajaxurl == undefined ? wpdm_url.ajax : ajaxurl;
            $.get(_ajaxurl, {__wpdm_scandir:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_scandir', dirs: 1, path: $this.data('path')}, function (dirs){
                if($("#"+chid).length == 1) {
                    $("#" + chid).remove();
                    slide = 0;
                }

                $this.append("<ul id='"+chid+"' style='display: none'></ul>");
                $.each(dirs, function (id, dir) {
                    $('#'+chid).append("<li class='expand-dir' id='"+dir.id+"' data-path='"+dir.path+"'><span class='handle'></span><a href='#' class='explore-dir' data-path='"+dir.path+"'>"+dir.item_label+"</a></li>");
                    var xpanded = localStorage.getItem('__expanded_'+dir.id);
                    if(xpanded) {
                        expand_dir(dir.id);
                    }
                });
                $this.removeClass('busy').addClass('expanded');
                if(slide == 1)
                    $('#'+chid).slideDown();
                else
                    $('#'+chid).show();
            });
        }

        // create the uploader and pass the config from above
        var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

        // checks if browser supports drag and drop upload, makes some css adjustments if necessary
        uploader.bind('Init', function(up){
            var uploaddiv = jQuery('#plupload-upload-ui');

            if(up.features.dragdrop){
                uploaddiv.addClass('drag-drop');
                jQuery('#drag-drop-area')
                    .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                    .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

            }else{
                uploaddiv.removeClass('drag-drop');
                jQuery('#drag-drop-area').unbind('.wp-uploader');
            }
        });

        uploader.init();

        // a file was added in the queue
        uploader.bind('FilesAdded', function(up, files){
            //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

            uploader.settings.multipart_params.current_path = current_path;

            plupload.each(files, function(file){
                jQuery('#filelist').append(
                    '<div class="panel panel-default file" id="' + file.id + '"><div class="panel-heading txtellipsis"><b>' +

                    file.name + '</b></div><div class="panel-body">' +
                    '<div class="progress" style="margin: 0;"><div class="progress-bar progress-bar-info progress-bar-striped fileprogress" role="progressbar"><span class="sr-only">(<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ')</span></div></div></div></div>');
            });

            up.refresh();
            up.start();
        });

        uploader.bind('UploadProgress', function(up, file) {
            jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
            jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
        });


        // a file was uploaded
        uploader.bind('FileUploaded', function(up, file, response) {
            var d = new Date();
            var ID = d.getTime();
            if(response.status == 200) {
                response = JSON.parse(response.response);
                if (response.success) {
                    jQuery('#' + file.id).addClass('upcompleted');
                    refresh_scandir(current_path);
                    jQuery('#' + file.id + ".upcompleted").on('click', function () {
                        jQuery(this).slideUp();
                    });
                } else {
                    jQuery('#' + file.id).addClass('upfailed');
                    jQuery('#' + file.id + ".upfailed").on('click', function () {
                        jQuery(this).slideUp();
                    });
                }
            } else {
                jQuery('#' + file.id).addClass('upfailed');
                jQuery('#' + file.id + ".upfailed").on('click', function () {
                    jQuery(this).slideUp();
                });
            }
        });

        $('#reload').on('click', function () {
            refresh_scandir(current_path);
        });

        $('#createfol').on('click', function () {
            var folname = $('#folname').val();
            if(folname !=''){
                $('#createfol').html('<i class="fa fa-refresh fa-spin"></i> &nbsp; Creating...');
                $.get(ajaxurl, {__wpdm_mkdir:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_mkdir', path: current_path, name: folname}, function (data) {
                    $('#folname').val('');
                    $('#createfol').html('Create Folder');
                    $('#fcd').fadeIn();
                    refresh_scandir(current_path);
                });
            }
        });

        $('#createfile').on('click', function () {
            var filename = $('#filename').val();
            if(filename !=''){
                $('#createfile').html('<i class="fa fa-refresh fa-spin"></i> &nbsp; Creating...');
                $.get(ajaxurl, {__wpdm_newfile:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_newfile', path: current_path, name: filename}, function (data) {
                    $('#filename').val('');
                    $('#createfile').html('Create File');
                    $('#fcd').fadeIn();
                    refresh_scandir(current_path);
                });
            }
        });

        /* Delete */
        $('body').on('click', '.btn-delete', function (e) {
            e.preventDefault();
            if(!confirm('Are you sure?')) return false;
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            var filepath = $(this).data('path');
            $.get(ajaxurl, {__wpdm_unlink:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_unlink', path: current_path, delete: filepath}, function (data) {
                refresh_scandir(current_path);
            });
        });


        $('body').on('click', '.btn-open-file', function (e) {
            e.preventDefault();
            $('#wpdmeditor').fadeIn();
            var filepath = $(this).data('path');
            var filename = $(this).find('.item_label').attr('title');
            $('#wpdmefn').text(filename);
            $.get(ajaxurl, {__wpdm_openfile:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_openfile', path: current_path, file: filepath}, function (data) {
                if(data.content != '') {
                    $('#filecontent').show();
                    $('#filecontent_alt').hide();
                    $('#filecontent').val(data.content);
                    opened = data.id;
                    $('#savefile').data('path', filepath);
                    $('#wpdmeditor').removeClass('blockui');
                    $('#wpdmeditor .panel-footer').fadeIn();
                    editor = wp.codeEditor.initialize($('#filecontent'), wpdmcm_settings);
                    var fileext = filename.split('.').pop();
                    fileext = fileext == 'html' ? 'htmlmixed' : fileext;
                    fileext = fileext == 'js' ? 'javascript' : fileext;
                    editor.codemirror.setOption('mode', fileext);
                } else {
                    $('#filecontent').hide();
                    $('#filecontent_alt').html(data.embed).show();
                    $('#wpdmeditor .panel-footer').fadeOut();
                    $('#wpdmeditor').removeClass('blockui');
                }
            });
        });

        $('body').on('click', '#close-editor', function (e) {
            e.preventDefault();
            hide_editor();
        });

        $('body').on('click', '#close-settings', function (e) {
            e.preventDefault();
            hide_settings();
        });

        $('body').on('click', '#savefile', function (e) {
            e.preventDefault();
            $('#wpdmeditor').addClass('blockui');
            var filepath = $(this).data('path');
            var content = editor.codemirror.getValue();
            $.post(ajaxurl, {__wpdm_savefile:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_savefile', content: content, file: filepath, opened: opened }, function (data) {
                WPDM.pushNotify("Save File", data.message);
                $('#wpdmeditor').removeClass('blockui');
            });
        });


        $('body').on('click', '.btn-edit-link', function () {
            $('#update_sharelink_form').addClass('blockui');
            var linkid = $(this).data('linkid');
            $.post(ajaxurl, {__wpdm_getlinkdet:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_getlinkdet', linkid: linkid }, function (data) {
                linkSettings.link = data;
                $('#update_sharelink_form').removeClass('blockui');
            });
        });


        $('body').on('click', '.btn-delete-link', function (e) {
            e.preventDefault();
            var linkid = $(this).data('linkid');
            WPDM.confirm("Delete Link", "Are you sure?",[
                {
                    'label': 'No',
                    'class': 'btn btn-secondary',
                    'callback': function () {
                        $(this).modal('hide');
                    }
                },
                {
                    'label': 'Yes, Remove',
                    'class': 'btn btn-danger',
                    'callback': function () {
                        $(this).find('.modal-body').html('<i class="fa fa-sun fa-spin"></i> Deleting...');
                        var confirm = $(this);
                        $.post(ajaxurl, {__wpdm_deletelink:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_deletelink', linkid: linkid }, function (data) {
                            confirm.modal('hide');
                            $('#asset-link-'+linkid).slideUp();
                        });
                    }
                }
            ]);

        });


        $('#sharelink_form').on('submit', function (e) {
            e.preventDefault();
            $('#sharelink_form').addClass('blockui');
            $(this).ajaxSubmit({
                success: function(data){
                    assetSettings.asset.links = data;
                    $('#sharelink_form').removeClass('blockui');
                    $('#sharelink').modal('hide');

                }
            });
        });

        $('#update_sharelink_form').on('submit', function (e) {
            e.preventDefault();
            $('#update_sharelink_form').addClass('blockui');
            $(this).ajaxSubmit({
                success: function(data){
                    console.log(data);
                    $('#update_sharelink_form').removeClass('blockui');
                    $('#__link_settings').modal('hide');

                }
            });
        });


        $('body').on('click', '.media-folder', function (e) {
            e.preventDefault();
            current_path = $(this).data('path');
            refresh_scandir(current_path);
            expand_dir($(this).data('id'));
        });

        $('body').on('click', '.btn-copy', function (e) {
            e.preventDefault();
            localStorage.setItem("__wpdm_fm_copy", current_path+"|||"+$(this).data('item'));
            localStorage.setItem("__wpdm_fm_move", 0);
            $('.btn-copy').html('<i class="fa fa-copy"></i>');
            $(this).html('<i class="fa fa-check-circle"></i>');
            $('#btn-paste').removeAttr('disabled').attr("data-item", localStorage.getItem("__wpdm_fm_copy"));
        });

        $('body').on('click', '.btn-cut', function (e) {
            e.preventDefault();
            localStorage.setItem("__wpdm_fm_copy", current_path+"|||"+$(this).data('item'));
            localStorage.setItem("__wpdm_fm_move", 1);
            $('.btn-copy').html('<i class="fa fa-copy"></i>');
            $(this).html('<i class="fa fa-check-circle"></i>');
            $('#btn-paste').removeAttr('disabled').attr("data-item", localStorage.getItem("__wpdm_fm_copy"));
        });

        /* Rename */
        $('body').on('click', '#renamenow', function (e) {
            e.preventDefault();
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            var filepath = $(this).data('path');
            $.post(ajaxurl, {__wpdm_rename:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_rename', path: current_path, newname: $('#newname').val(), assetid: assetSettings.asset.ID}, function (data) {
                refresh_scandir(current_path);
                $(this).data('oldname', $('#newname').val());
                $('#renamenow').html('Rename');
                $('#rename').modal('hide');
            });
        });

        $('body').on('click', '#btn-upload-file', function () {
            $('#upfile').slideToggle();
        });

        $('body').on('click', '.btn-settings', function (e) {
            e.preventDefault();
            var file_path = $(this).data('path');
            wpdmfm_active_asset = file_path;
            $('#filewin').removeClass('col-md-9').addClass('col-md-6');
            $('#cogwin').fadeIn();
            $('#cogwin > .panel').addClass('blockui');
            $.get(ajaxurl, {__wpdm_filesettings:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_filesettings', file: file_path }, function (data) {
                data.sharecode = "[wpdm_asset id='"+data.ID+"']";
                assetSettings.asset = data;
                $('#cogwin > .panel').removeClass('blockui');
            });

        });

        $('body').on('click', '.btn-zip', function (e) {
            e.preventDefault();
            var zip_dir_path = $(this).data('dirpath');
            //WPDM.blockUI("#wpdmfm_explorer");
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            $.get(ajaxurl, {__wpdm_createzip:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_createzip', dir_path: zip_dir_path }, function (response) {
                if(!response.success){
                    WPDM.notify(response.message, 'error');
                } else {
                    refresh_scandir(current_path);
                }
            });
        });

        $('body').on('click', '.btn-unzip.application_zip', function (e) {
            e.preventDefault();
            var zip_dir_path = $(this).data('dirpath');
            //WPDM.blockUI("#wpdmfm_explorer");
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            $.get(ajaxurl, {__wpdm_unzipit:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_unzipit', dir_path: zip_dir_path }, function (response) {
                if(!response.success){
                    WPDM.notify(response.message, 'error');
                } else {
                    refresh_scandir(current_path);
                }
            });
        });

        $('body').on('click', '.action-btns-ctrl', function (e) {
            e.preventDefault();
            $($(this).data('target')).toggleClass("action-btns-show");
        });
        $('body').on('click', '#btn-paste', function (e) {
            e.preventDefault();
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            var params = {__wpdm_copypaste:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_copypaste', source: localStorage.getItem("__wpdm_fm_copy"), dest: current_path};
            if(localStorage.getItem("__wpdm_fm_move") == 1)
                params = {__wpdm_cutpaste:'<?php echo wp_create_nonce(WPDMAM_NONCE_KEY); ?>', action: 'wpdm_cutpaste', source: localStorage.getItem("__wpdm_fm_copy"), dest: current_path};
            $.get(ajaxurl, params, function (data) {
                if(!data.success){
                    WPDM.notify(data.message, 'error');
                } else {
                    refresh_scandir(current_path);
                }
                $('#btn-paste').html('<i class="fa fa-clipboard"></i>');
                if(localStorage.getItem("__wpdm_fm_move") == 1){
                    localStorage.setItem("__wpdm_fm_move", 0);
                    localStorage.setItem("__wpdm_fm_copy", '');
                    $('#btn-paste').attr('disabled','disabled');
                }
            });
        });

        $('body').on('click', '.asset-link .form-control', function () {
            $(this).select();
            document.execCommand('copy');
        });

        $('body').on('click', '.expand-dir > .handle, .explore-dir', function (e) {
            e.preventDefault();

            var $this = $(this).parent('.expand-dir');
            var chid = "expanded_"+$(this).parent('.expand-dir').attr('id');

            if ($(this).hasClass('explore-dir')){
                current_path = $this.data('path');
                refresh_scandir($this.data('path'));
            }

            if($this.hasClass('expanded') && !$(this).hasClass('explore-dir')){
                $('#'+chid).slideUp(function () {
                    $(this).remove();
                    $this.removeClass('expanded');
                    localStorage.removeItem('__expanded_'+$this.attr('id'));
                });
                return false;
            }

            $this.addClass('busy');

            expand_dir($(this).parent('.expand-dir').attr('id'));
        });

        var uacc = '';

        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        $("#maname")
            .bind("keydown", function (event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).data("ui-autocomplete").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(ajaxurl + '?action=wpdm_cal_suggest_members', {
                        action: 'wpdm_cal_suggest_members',
                        term: extractLast($("#maname").val())
                    }, response);
                },
                search: function () {

                    var term = extractLast($("#maname").val());
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function () {

                    return false;
                },
                select: function (event, ui) {
                    $('#uaco').prepend('<span style="margin-right: 3px" class="btn btn-simple btn-sm" id="uaco-' + ui.item.value.replace(/[^a-zA-Z]/ig, '-') + '"><input type="hidden" name="access[users][]" value="' + ui.item.value + '" /> <a class="uaco-del" onclick="jQuery(this.rel).remove()" rel="#uaco-' + ui.item.value.replace(/[^a-zA-Z]/ig, '-') + '"><i class="far fa-times-circle"></i></a>&nbsp;' + ui.item.value + '</span>');
                    this.value = "";
                    return false;
                }
            });

        $("#_maname")
            .bind("keydown", function (event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).data("ui-autocomplete").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(ajaxurl + '?action=wpdm_cal_suggest_members', {
                        action: 'wpdm_cal_suggest_members',
                        term: extractLast($("#_maname").val())
                    }, response);
                },
                search: function () {

                    var term = extractLast($("#_maname").val());
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function () {

                    return false;
                },
                select: function (event, ui) {
                    $('#_uaco').prepend('<span style="margin-right: 3px" class="btn btn-simple btn-sm" id="uaco-' + ui.item.value.replace(/[^a-zA-Z]/ig, '-') + '"><input type="hidden" name="access[users][]" value="' + ui.item.value + '" /> <a class="uaco-del" onclick="jQuery(this.rel).remove()" rel="#uaco-' + ui.item.value.replace(/[^a-zA-Z]/ig, '-') + '"><i class="far fa-times-circle"></i></a>&nbsp;' + ui.item.value + '</span>');
                    this.value = "";
                    return false;
                }
            });


        if(localStorage.getItem("__wpdm_fm_copy") != undefined && localStorage.getItem("__wpdm_fm_copy") != ''){
            $('#btn-paste').removeAttr('disabled').attr("data-item", localStorage.getItem("__wpdm_fm_copy"));
        }

        refresh_scandir(current_path);
        expand_dir('<?php echo md5('home'); ?>');

        $('.ttip').tooltip();


    });

</script>


<?php if(is_admin()){ ?>
    <script>
        jQuery(function ($) {
            $('[data-simplebar], #cogwin > .panel').css('height', (window.innerHeight - 160)+'px');
            $(window).on('resize', function() {
                $('[data-simplebar], #cogwin > .panel').css('height', (window.innerHeight - 160) + 'px');
                if($('#mainfmarea').is(":fullscreen")){
                    $('[data-simplebar]').css('height', (window.innerHeight - 135) + 'px');
                }
            });
        });
    </script>
    </div>
<?php }
