<?php
if(!defined('ABSPATH')) die('Error!');
?>
    <!DOCTYPE html>
    <html style="background: transparent">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title><?php _e('Asset Manager Picker', 'download-manager') ?></title>

            <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap3/css/bootstrap.css" />
            <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/admin-styles.css" />
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css" />
            <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
            <script src="<?php echo includes_url(); ?>/js/jquery/jquery.form.min.js"></script>
            <script src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap3/js/bootstrap.min.js"></script>
            <script src="<?php echo WPDM_BASE_URL; ?>assets/js/front.js"></script>


        <?php
        \WPDM\__\Apply::googleFont();
        ?>
        <style>
            @import url("https://fonts.googleapis.com/css?family=Overpass+Mono&subset=latin");
            html, body{
                overflow: visible;
                height: 100%;
                width: 100%;
                padding: 0;
                margin: 0;
                font-weight: 300;
                font-size: 10pt;
                font-family: var(--wpdm-font);
            }
            h4.modal-title{
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #555555;
                font-size: 11pt;
                display: inline-block;
                font-family: var(--wpdm-font);
            }

            .w3eden label{
                font-weight: 400;
            }
            img{
                max-width: 100%;
            }
            .modal-backdrop{
                background: rgba(0,0,0,0.5);
            }


            .modal.fade{
                opacity:1;
            }
            .modal.fade .modal-dialog {
                -webkit-transform: translate(0);
                -moz-transform: translate(0);
                transform: translate(0);
            }

            .modal {
                text-align: center;
                padding: 0!important;
            }

            .wpdm-social-lock.btn {
                display: block;
                width: 100%;
            }

            @media (min-width: 768px) {
                .modal:before {
                    content: '';
                    display: inline-block;
                    height: 100%;
                    vertical-align: middle;
                    margin-right: -4px;
                }

                .modal-dialog {
                    display: inline-block;
                    text-align: left;
                    vertical-align: middle;
                }

                .wpdm-social-lock.btn {
                    display: inline-block;
                    width: 47%;
                }
            }

            @-moz-keyframes spin {
                from { -moz-transform: rotate(0deg); }
                to { -moz-transform: rotate(360deg); }
            }
            @-webkit-keyframes spin {
                from { -webkit-transform: rotate(0deg); }
                to { -webkit-transform: rotate(360deg); }
            }
            @keyframes spin {
                from {transform:rotate(0deg);}
                to {transform:rotate(360deg);}
            }
            .spin{
                -webkit-animation-name: spin;
                -webkit-animation-duration: 2000ms;
                -webkit-animation-iteration-count: infinite;
                -webkit-animation-timing-function: linear;
                -moz-animation-name: spin;
                -moz-animation-duration: 2000ms;
                -moz-animation-iteration-count: infinite;
                -moz-animation-timing-function: linear;
                -ms-animation-name: spin;
                -ms-animation-duration: 2000ms;
                -ms-animation-iteration-count: infinite;
                -ms-animation-timing-function: linear;

                animation-name: spin;
                animation-duration: 2000ms;
                animation-iteration-count: infinite;
                animation-timing-function: linear;
                display: inline-block;
            }


            .w3eden .card-default {
                border-radius: 3px;
                margin-top: 10px !important;
            }
            .w3eden .card-default:last-child{
                margin-bottom: 0 !important;
            }
            .w3eden .card-default .card-header{
                letter-spacing: 0.5px;
                font-weight: 600;
                background-color: #f6f8f9;
            }

            .w3eden .card-default .card-footer{
                background-color: #fafafa;
            }

            .btn{
                outline: none !important;
            }
            .w3eden .card{
                margin-bottom: 0;
            }
            .w3eden .modal-header{
                border: 0;
            }
            .w3eden .modal-content{
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
                border: 0;
                border-radius: 3px;
                background: transparent;
                overflow: hidden;
                max-width: 100%;
            }
            .w3eden .modal-body{
                max-height:  calc(100vh - 210px);
                overflow-y: auto;
                padding: 0 !important;
            }


            .w3eden .input-group-lg .input-group-btn .btn{
                border-top-right-radius: 4px !important;
                border-bottom-right-radius: 4px !important;
            }
            .w3eden .wpforms-field-medium{
                max-width: 100% !important;
                width: 100% !important;
            }

            .w3eden .input-group.input-group-lg .input-group-btn .btn {
                font-size: 11pt !important;
            }


            .close{
                position: absolute;
                z-index: 999999;
                top: 5px;
                right: 5px;
                opacity: 0 !important;
            }
            .modal-content:hover .close{
                opacity: 0.8 !important;
            }
            .close:hover .fa-times-circle{
                color: #ff3c54 !important;
            }
            .close .fa-times-circle,
            .close:hover .fa-times-circle,
            .modal-content:hover .close,
            .close{
                -webkit-transition: ease-in-out 400ms;
                -moz-transition: ease-in-out 400ms;
                -ms-transition: ease-in-out 400ms;
                -o-transition: ease-in-out 400ms;
                transition: ease-in-out 400ms;
            }

            #filelist .panel-heading{
                padding-right: 30px;
            }
            .w3eden #upfile  .progress-bar-info {
                background-color: var(--color-purple);
            }
            #breadcrumb,
            .list-group-item,
            .wpdm-file-locator,
            .wpdm-dir-locator a.explore-dir{
                font-family: 'Overpass Mono', sans-serif !important;
            }
            .well-file{
                font-family: Montserrat,sans-serif;
                font-size: 12px;
                line-height: 28px;
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
                font-family: 'Overpass Mono';
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

            .w3eden .modal-content{
                border-radius: 3px !important;
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
                border: 1px solid #e7eaea !important;
                margin: 0 !important;
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
                height: calc(100% - 94px);
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
                border-right: 1px solid #eee;
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

            .w3eden #__asset_settings .form-control.input-lg{
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

            #newcomment{
                min-height: 50px;
                font-size: 12px !important;
            }

            #__asset_comments .asset-comment{
                border: 1px solid #e8e8e8;
                margin: 5px 0;
                border-radius: 3px;
                padding: 15px;
            }
            #__asset_comments .asset-comment .avatar{
                width: 32px;
                height: auto;
                border-radius: 500px;
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

        </style>




        <?php do_action("wpdm_modal_iframe_head"); ?>
    </head>
    <body class="w3eden" style="background: transparent">

    <div class="modal fade" id="wpdm-asset-picker" tabindex="-1" role="dialog" aria-labelledby="wpdm-optinmagicLabel">
        <div class="modal-dialog" role="document" style="width: 80%;max-width: calc(100% - 20px);">

            <div class="modal-content">
                <div class="modal-body">
                    <?php
                    global $current_user;
                    $root = \WPDM\AssetManager\AssetManager::root();
                    //$items = file_exists($root)?glob($root.'*', GLOB_ONLYDIR):array();
                    if(is_admin()){
                    ?>



                        <?php
                        }
                        ?>


                        <div class="w3eden" id="mainfmarea">
                            <?php do_action("wpdm_frontend_filemanager_top", ""); ?>
                            <div id="loadingfm" class="blockui" style="position: fixed;width: 100%;height: 100%;z-index: 99"></div>
                            <div id="mainfmc" class="panel panel-default wpdm-file-manager-panel" style="display: none;">
                                <div class="panel-body">
                                    <div class="media well-sm well-file" style="margin: 0;padding: 0">
                                        <div class="pull-right">
                                            <button class="btn btn-primary btn-simple btn-sm ttip" title="Reload" id="reload"><i class="fa fa-sync"></i></button>
                                            <button class="btn btn-danger btn-simple btn-sm" data-target="#wpdm-asset-picker" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                                        </div>
                                        <h3 style="display: inline-block;font-size: 12pt;letter-spacing: 0.5px;font-weight: 400;font-family: var(--wpdm-font)">
                                            <i class="fas fa-photo-video text-primary"></i> <?php echo __( "Server File Picker", "download-manager" ) ?> <sup style="color: var(--color-info) !important;font-size: 10px;font-family: 'Overpass Mono', sans-serif !important;">BETA</sup>
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
                                    <div id="ldn" style="float:right;font-size: 9pt;margin-top: 10px;display: none" class="text-danger"><i class="fa fa-sun fa-spin"></i> Loading...</div>
                                    <div id="breadcrumb" style="margin: 0"></div>
                                </div>
                                <div class="panel-body-c" id="wpdmfm_explorer">

                                    <div class="row" style="margin: 0">
                                        <?php do_action("wpdm_frontend_filemanager_after_breadcrumb", ""); ?>
                                        <div class="col-md-3 wpdm-dir-locator">
                                            <div data-simplebar ss-container>
                                                <ul class="wpdmfm-folder-tree" id="wpdmfm-folder-tree">
                                                    <li data-path="" id="<?php echo md5('home'); ?>" class="expand-dir"><i class="fa fa-hdd color-purple"></i> <a class="explore-dir" href="#" data-path=""> Home</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6 wpdm-file-locator" id="filewin">
                                            <div id="wpdmeditor" class="panel panel-default">
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
                                        <div class="col-md-3" id="cogwin" style="margin-right: 0;padding-right: 0;padding-left: 0;">
                                            <div class="panel panel-default" style="border: 0;border-radius: 0">
                                                <div class="panel-heading" style="border-radius: 0;border-top: 0 !important;margin-bottom: 1px !important;background: #fafafa;border-bottom: 1px solid #eeeeee !important;">
                                                    Selected Files
                                                </div>

                                                <div class="list-group" id="__file_list">

                                                        <div class="list-group-item" v-for="(file, index) in files" :id="'file_' + index">
                                                            <div class="media">
                                                                <div class="pull-right"><a href="#" class="btn-remove-file" :data-target="index"><i class="fa fa-trash text-danger"></i></a></div>
                                                                <div class="media-body">
                                                                    {{file.name}}
                                                                </div>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="panel-footer" style="border-bottom: 1px solid #dddddd !important;border-radius: 0">
                                                    <button type="button" id="attach-files" class="btn btn-primary btn-block"><?php echo __( "Attach selected files", "download-manager" )?></button>
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

                                        <div class="col-md-12">
                                            <div class="media-folder media" data-id="{{dirid}}" data-path="{{path}}" style="cursor: pointer">
                                                <img class="icon pull-left" src="<?php echo plugins_url('download-manager/assets/file-type-icons/folder.png'); ?>" />
                                                <div class="dir-info"><div class="item_label" title="{{item}}">{{item_label}}</div><small class="color-purple">{{note}}</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="fileTPL" style="display: none">

                                <div class="file-row">
                                    <div class="row panel-file file-tpl">
                                        <div class="col-md-12  text-left btn-open-file c-pointer  btn-add-queue" data-filetype="{{contenttype}}" data-path="{{path}}"  data-label="{{item_label}}" data-target="{{path_on}}">

                                            <div class="file-info media">
                                                <img class="icon pull-left" src="{{icon}}" />
                                                <div class="dir-info"><div class="item_label" title="{{item}}">{{item_label}}</div><small class="color-purple">{{note}}</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <script src="<?php echo WPDM_BASE_URL ?>assets/js/vue.min.js"></script>
                        <script src="<?php echo WPDM_BASE_URL ?>assets/js/simple-scrollbar.min.js"></script>
                        <script>
                            var current_path = '', editor = '', opened = '', wpdmfm_selected_files = [], wpdmfm_active_asset_settings;
                            var ajaxurl = "<?php echo admin_url('/admin-ajax.php'); ?>";

                            var fileList = new Vue({
                                el: '#__file_list',
                                data: {
                                    files: []
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
                                    $('#reload').attr('disabled', 'disabled').find('.fa').addClass('fa-spin');
                                    WPDM.blockUI('#filewin');
                                    $.get(ajaxurl, {__wpdm_scandir:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_scandir', path: path}, function (data) {
                                        if(data.success === true) {
                                            $('#scandir').html('');
                                            var items = data.items;
                                            $.each(items, function (index, entry) {
                                                if (entry.type == 'file') {
                                                    var tpl = $('#fileTPL').html();
                                                    tpl = tpl.replace("{{icon}}", entry.icon);
                                                    tpl = tpl.replace("{{contenttype}}", entry.contenttype);
                                                    tpl = tpl.replace(/\{\{item_label\}\}/ig, entry.item_label);
                                                    tpl = tpl.replace("{{note}}", entry.note);
                                                    tpl = tpl.replace("{{file_size}}", entry.file_size);
                                                    tpl = tpl.replace(/\{\{path\}\}/ig, entry.path);
                                                    tpl = tpl.replace(/\{\{path_on\}\}/ig, entry.wp_rel_path);
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
                                            WPDM.pushNotify("Error!", data.message, 'https://cdn0.iconfinder.com/data/icons/small-n-flat/24/678080-shield-error-256.png', 'https://cdn0.iconfinder.com/data/icons/small-n-flat/24/678080-shield-error-256.png');
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

                                function expand_dir(id) {
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
                                        });
                                        $this.removeClass('busy').addClass('expanded');
                                        if(slide == 1)
                                            $('#'+chid).slideDown();
                                        else
                                            $('#'+chid).show();
                                    });
                                }



                                $('#reload').on('click', function () {
                                    refresh_scandir(current_path);
                                });

                                $('body').on('click', '#attach-files', function(){
                                    window.parent.attach_server_files(fileList.files);
                                    jQuery('#wpdm-asset-picker').modal('hide');
                                });

                                $('body').on('click', '#close-editor', function (e) {
                                    e.preventDefault();
                                    hide_editor();
                                });

                                /*$('body').on('click', '#close-settings', function (e) {
                                    e.preventDefault();
                                    hide_settings();
                                });*/



                                $('body').on('click', '.media-folder', function (e) {
                                    e.preventDefault();
                                    current_path = $(this).data('path');
                                    refresh_scandir(current_path);
                                    expand_dir($(this).data('id'));
                                });


                                $('body').on('click', '.btn-add-queue', function (e) {
                                    e.preventDefault();
                                    if($(this).data('queued') === undefined) {
                                        var file_path = $(this).data('target');
                                        var file_name = $(this).data('label');
                                        fileList.files.push({path: file_path, name: file_name});
                                    }
                                    $(this).data('queued', 1);
                                    console.log(fileList.files);
                                });

                                $('body').on('click', '.btn-remove-file', function (e) {
                                    e.preventDefault();
                                    fileList.files.splice($(this).data('target'), 1);
                                    //$(this).data('queued', 1);
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


                                refresh_scandir('');
                                expand_dir('<?php echo md5('home'); ?>');

                                $('.ttip').tooltip();


                            });

                        </script>



                </div>

            </div>

        </div>
        <?php

        ?>
    </div>

    <script>

        jQuery(function ($) {

            $('a').each(function () {
                $(this).attr('target', '_blank');
            });

            $('body').on('click','a', function () {
                $(this).attr('target', '_blank');
            });

            $('#wpdm-asset-picker').on('hidden.bs.modal', function (e) {
                var parentWindow = document.createElement("a");
                parentWindow.href = document.referrer.toString();
                if(parentWindow.hostname === window.location.hostname)
                    window.parent.hide_asset_picker_frame();
                else
                    window.parent.postMessage({'task': 'hideiframe'}, "*");
            });

            $(window).on('resize', function () {
                $('#wpdm-asset-picker .modal-content').css('height', (window.innerHeight - 200) + 'px');
                jQuery('#wpdm-asset-picker [data-simplebar]').css('height', (window.innerHeight - 345) + 'px');
            });


        });

        function showModal() {
            jQuery('#wpdm-asset-picker').modal('show');
            jQuery('#wpdm-asset-picker .modal-content').css('height', (window.innerHeight - 200) + 'px');
            jQuery('#wpdm-asset-picker [data-simplebar]').css('height', (window.innerHeight - 345) + 'px');

        }
        showModal();
    </script>
    <div style="display: none">

        <?php do_action("wpdm_modal_iframe_footer"); ?>
    </div>
    </body>
    </html>


