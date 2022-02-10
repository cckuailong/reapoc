<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/26/18
 * Time: 12:33 AM
 * Updated: 2020-06-19
 */


?><!DOCTYPE html>
<html style="background: transparent">
<head>
    <title><?php echo $package->post_title; ?></title>
    <script>
        var wpdm_url = <?= json_encode(WPDM()->wpdm_urls) ?>;
    </script>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet">
    <script src="<?php echo WPDM_BASE_URL ?>assets/js/simple-scrollbar.min.js"></script>

    <?php
    WPDM()->apply::uiColors();
    ?>
    <style>
        body{
            font-family: "Merriweather Sans", sans-serif;
            font-weight: 400;
            font-size: 10px;
            color: #425676;
            background: #233459;
        }
        .w3eden #wpdm-download h1{
            font-size: 11pt;
            font-weight: 600;
            line-height: 1.5;
        }
        h1,h2,h3{
            font-weight: 800;
            letter-spacing: 0.4px;
        }
        .w3eden #wpdm-download h3{
            font-size: 9pt;
        }
        .w3eden p{
            font-size: 10px;
            margin: 0;
            letter-spacing: 0.3px;
            line-height: 1.5;
        }
        #wpdm-download .modal-dialog{
            width: 360px;
            max-width: 96%;
        }
        #wpdm-download .modal-content{
            border-radius: 4px;
            border: 0;
            box-shadow: 0 0 15px rgba(0,0,0,0.12);
        }
        #wpdm-download .modal-footer{
            border-top: 1px solid #eeeeee;
            background: #fafafa;
            padding: 15px;
        }
        .w3eden #wpdm-download .btn{
            padding: 12px;
            font-weight: 600 !important;
            font-size: 9pt;
            letter-spacing: 1.5px;
        }
        .modal-backdrop{
            background: rgba(70, 99, 156, 0.87);
        }
        .modal-backdrop.show{
            opacity: 1;
        }
        p svg{
            width: 12px;
            display: inline-block;
            margin-right: 3px;
            margin-top: -3px;
        }
        .w3eden .list-group {
            border-color: rgba(67, 93, 148, 0.1) !important;
            max-height: 120px;
            overflow: auto;
            border-radius: 0 !important;
        }
        .w3eden .list-group div.file-item{
            padding: 10px;
            color: var(--color-muted);
            font-size: 10px;
            border-color: rgba(67, 93, 148, 0.1) !important;
            line-height: 1.5;
            border-radius: 0 !important;
        }
        .w3eden .list-group div.file-item h3{
            font-size:10pt;
            margin: 0;
            font-weight: 600;
            color: #4b6286;
        }
        .w3eden .list-group div.file-item svg{
            width: 18px;
            margin-top: 5px;
        }

        .ss-wrapper {
            overflow : hidden;
            height   : 100%;
            position : relative;
            z-index  : 1;
            float: left;
            width: 100%;
        }

        .ss-content {
            height          : 100%;
            width           : 100%;
            padding         : 0 32px 0 0;
            position        : relative;
            right           : -18px;
            overflow        : auto;
            -moz-box-sizing : border-box;
            box-sizing      : border-box;
        }

        .ss-scroll {
            position            : relative;
            background          : rgba(0, 0, 0, .1);
            width               : 9px;
            border-radius       : 4px;
            top                 : 0;
            z-index             : 2;
            cursor              : pointer;
            opacity: 0;
            transition: opacity 0.25s linear;
        }

        .ss-container:hover .ss-scroll {
            opacity: 1;
        }

        .ss-grabbed {
            user-select: none;
            -o-user-select: none;
            -moz-user-select: none;
            -khtml-user-select: none;
            -webkit-user-select: none;
        }
    </style>

</head>
<body class="w3eden">
<div id="wpdm-download" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="wpdm-download-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="media">
                    <?php if($picon !== ''){ ?>
                    <div class="mr-3">
                        <img style="width: 50px" src="<?php echo $picon; ?>" />
                    </div>
                    <?php } ?>
                    <div class="media-body">
                        <h1><?php echo $package->post_title; ?> <?php if(count($files) == 1) { ?>( <?php echo strtoupper(WPDM()->package->fileTypes($package->ID, false)[0]); ?> )<?php } ?></h1>
                        <p>
                            <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="alarm-exclamation" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-alarm-exclamation fa-w-16 fa-fw fa-2x"><path fill="var(--color-danger)" d="M256 352a24 24 0 1 0 24 24 24 24 0 0 0-24-24zm-8.5-24h17a8.14 8.14 0 0 0 8-7.5l7-136a8 8 0 0 0-8-8.5h-31a8 8 0 0 0-8 8.5l7 136a8 8 0 0 0 8 7.5zM32 112a80.09 80.09 0 0 1 80-80 79.23 79.23 0 0 1 50 18 253.22 253.22 0 0 1 34.44-10.8C175.89 15.42 145.86 0 112 0A112.14 112.14 0 0 0 0 112c0 25.86 9.17 49.41 24 68.39a255.93 255.93 0 0 1 17.4-31.64A78.94 78.94 0 0 1 32 112zM400 0c-33.86 0-63.89 15.42-84.44 39.25A253.22 253.22 0 0 1 350 50.05a79.23 79.23 0 0 1 50-18 80.09 80.09 0 0 1 80 80 78.94 78.94 0 0 1-9.36 36.75A255.93 255.93 0 0 1 488 180.39c14.79-19 24-42.53 24-68.39A112.14 112.14 0 0 0 400 0zM256 64C132.29 64 32 164.29 32 288a222.89 222.89 0 0 0 54.84 146.54L34.34 487a8 8 0 0 0 0 11.32l11.31 11.31a8 8 0 0 0 11.32 0l52.49-52.5a223.21 223.21 0 0 0 293.08 0L455 509.66a8 8 0 0 0 11.32 0l11.31-11.31a8 8 0 0 0 0-11.32l-52.5-52.49A222.89 222.89 0 0 0 480 288c0-123.71-100.29-224-224-224zm0 416c-105.87 0-192-86.13-192-192S150.13 96 256 96s192 86.13 192 192-86.13 192-192 192z" class=""></path></svg> <?php echo isset($validity['expire']) && $validity['expire'] ? "Expires in {$validity['expire']}": "Expired {$validity['expired']} ago"; ?> &nbsp; <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="arrow-circle-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-arrow-circle-down fa-w-16 fa-fw fa-2x"><path fill="var(--color-info)" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm216 248c0 118.7-96.1 216-216 216-118.7 0-216-96.1-216-216 0-118.7 96.1-216 216-216 118.7 0 216 96.1 216 216zm-92.5-4.5l-6.9-6.9c-4.7-4.7-12.5-4.7-17.1.2L273 330.3V140c0-6.6-5.4-12-12-12h-10c-6.6 0-12 5.4-12 12v190.3l-82.5-85.6c-4.7-4.8-12.4-4.9-17.1-.2l-6.9 6.9c-4.7 4.7-4.7 12.3 0 17l115 115.1c4.7 4.7 12.3 4.7 17 0l115-115.1c4.7-4.6 4.7-12.2 0-16.9z" class=""></path></svg> <b><?php echo isset($validity['use']) ? $validity['use'] : 0; ?></b> downloads remains<br/>
                        </p>
                    </div>
                </div>
                <?php if(count($files) > 1) {
                    ?>
                    <div class="list-group mt-3" data-simplebar ss-container>
                        <?php
                        foreach ($files as $fid => $file) {
                            ?>
                            <div class="file-item list-group-item">

                                <?php if($keyvalid) { ?>
                                <div class="float-right">
                                    <a href="<?php echo $download_url; ?>&ind=<?php echo $fid; ?><?php if(isset($_GET['subscriber'])) echo '&subscriber='.wpdm_query_var('subscriber'); ?>">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="arrow-alt-circle-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-arrow-alt-circle-down fa-w-16 fa-fw fa-2x"><g class="fa-group"><path fill="rgba(var(--color-success-rgb), 0.15)" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm124.41 268.32L264.52 390.48a12.08 12.08 0 0 1-17 0L131.59 276.32c-7.67-7.49-2.22-20.48 8.57-20.48h71.51V140a12.08 12.08 0 0 1 12.1-12h64.56a12.08 12.08 0 0 1 12.1 12v115.84h71.41c10.79 0 16.24 12.89 8.57 20.48z" class="fa-secondary"></path><path   fill="var(--color-success)" d="M223.77 128h64.56a12.08 12.08 0 0 1 12.1 12v115.84h71.41c10.79 0 16.24 12.89 8.57 20.48L264.52 390.48a12.08 12.08 0 0 1-17 0L131.59 276.32c-7.67-7.49-2.22-20.48 8.57-20.48h71.51V140a12.08 12.08 0 0 1 12.1-12z" class="fa-primary"></path></g></svg>
                                    </a>
                                </div>
                                <?php } ?>
                                <h3><?php echo basename($file); ?></h3>
                                <?php echo wpdm_file_size($file); ?>

                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="modal-footer">
                <?php if($keyvalid) { ?>
                <a href="<?php echo $download_url; if(isset($_GET['subscriber'])) echo '&subscriber='.wpdm_query_var('subscriber'); ?>" class="btn btn-primary btn-block">
                    Download  [ <?php echo wpdm_package_size($package->ID); ?> ]
                </a>
                <?php } else { ?>
                    <button disabled="disabled" class="btn btn-danger btn-block">
                        &mdash; Download link is expired &mdash;
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="text-center" style="width: 100%;z-index: 999999;position: absolute;bottom: 20px;color: rgba(255,255,255, 0.4);font-size: 10px">
        &mdash; &nbsp;<a href="<?php echo home_url('/'); ?>" style="color: rgba(255,255,255, 0.4);">Go to Home</a> &mdash;
        <a href="<?php echo get_permalink($package->ID); ?>" style="color: rgba(255,255,255, 0.4);">View Package</a>&nbsp; &mdash;
    </div>
</div>
<script>
    jQuery(function ($) {
        $('#wpdm-download').modal({backdrop: 'static'});
    });
</script>
</body>
</html>
