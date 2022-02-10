<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/26/18
 * Time: 12:33 AM
 */


?>
<!DOCTYPE html>
<html style="background: transparent">
<head>
    <title><?php echo $user_allowed ? $media->post_title : '404 - Not found!'; ?></title>
    <script>
        var wpdm_url = <?= json_encode(WPDM()->wpdm_urls) ?>;
    </script>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.form.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:300,400,700" rel="stylesheet">
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
        .w3eden #wpdm-download .btn.btn-download{
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
            max-height: 103px;
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
        .attachment-thumbnail.size-thumbnail {
            height: 48px;
            width: auto;
        }
        .form-control:focus{
            box-shadow: none !important;
        }
        #__pwd{
            background: #ffffff url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIzMnB4IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMycHgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6c2tldGNoPSJodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2gvbnMiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj48dGl0bGUvPjxkZXNjLz48ZGVmcy8+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIiBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSI+PGcgZmlsbD0iIzE1N0VGQiIgaWQ9Imljb24tMTE0LWxvY2siPjxwYXRoIGQ9Ik0xNiwyMS45MTQ2NDcyIEwxNiwyNC41MDg5OTQ4IEMxNiwyNC43ODAxNjk1IDE2LjIzMTkzMzYsMjUgMTYuNSwyNSBDMTYuNzc2MTQyNCwyNSAxNywyNC43NzIxMTk1IDE3LDI0LjUwODk5NDggTDE3LDIxLjkxNDY0NzIgQzE3LjU4MjU5NjIsMjEuNzA4NzI5IDE4LDIxLjE1MzEwOTUgMTgsMjAuNSBDMTgsMTkuNjcxNTcyOCAxNy4zMjg0MjcyLDE5IDE2LjUsMTkgQzE1LjY3MTU3MjgsMTkgMTUsMTkuNjcxNTcyOCAxNSwyMC41IEMxNSwyMS4xNTMxMDk1IDE1LjQxNzQwMzgsMjEuNzA4NzI5IDE2LDIxLjkxNDY0NzIgTDE2LDIxLjkxNDY0NzIgWiBNOSwxNC4wMDAwMTI1IEw5LDEwLjQ5OTIzNSBDOSw2LjM1NjcwNDg1IDEyLjM1Nzg2NDQsMyAxNi41LDMgQzIwLjYzMzcwNzIsMyAyNCw2LjM1NzUyMTg4IDI0LDEwLjQ5OTIzNSBMMjQsMTQuMDAwMDEyNSBDMjUuNjU5MTQ3MSwxNC4wMDQ3NDg4IDI3LDE1LjM1MDMxNzQgMjcsMTcuMDA5NDc3NiBMMjcsMjYuOTkwNTIyNCBDMjcsMjguNjYzMzY4OSAyNS42NTI5MTk3LDMwIDIzLjk5MTIxMiwzMCBMOS4wMDg3ODc5OSwzMCBDNy4zNDU1OTAxOSwzMCA2LDI4LjY1MjYxMSA2LDI2Ljk5MDUyMjQgTDYsMTcuMDA5NDc3NiBDNiwxNS4zMzk1ODEgNy4zNDIzMzM0OSwxNC4wMDQ3MTUyIDksMTQuMDAwMDEyNSBMOSwxNC4wMDAwMTI1IEw5LDE0LjAwMDAxMjUgWiBNMTIsMTQgTDEyLDEwLjUwMDg1MzcgQzEyLDguMDA5MjQ3OCAxNC4wMTQ3MTg2LDYgMTYuNSw2IEMxOC45ODAyMjQzLDYgMjEsOC4wMTUxMDA4MiAyMSwxMC41MDA4NTM3IEwyMSwxNCBMMTIsMTQgTDEyLDE0IEwxMiwxNCBaIiBpZD0ibG9jayIvPjwvZz48L2c+PC9zdmc+") 8px center no-repeat !important;
            background-size: 18px !important;
            padding-left: 36px;
        }
    </style>

</head>
<body class="w3eden">
<div id="wpdm-download" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="wpdm-download-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div id="__error" class="alert alert-danger position-absolute" style="display: none;z-index: 999999;width: 100%;top: 0;border: 0;background: #ffe4ed;border-bottom: 2px solid #df1d5d;color: #df1d5d;font-size: 10pt"><?php _e('Wrong Password. Try Again!'); ?></div>
        <div class="modal-content">
            <?php if(!$user_allowed){ ?>
            <div class="modal-body">
                <div class="text-danger lead text-center p-4" style="font-weight: 300;font-size: 13pt;line-height: 30px">
                    <div class="d-inline-block">
                        <svg id="Layer_1" style="width: 28px;height: 28px" version="1.1" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                            .st0{fill:var(--color-danger);}
                        </style><g><g id="Icon-Exclamation" transform="translate(228.000000, 278.000000)"><path class="st0" d="M-196-222.1c-13.2,0-23.9-10.7-23.9-23.9c0-13.2,10.7-23.9,23.9-23.9s23.9,10.7,23.9,23.9     C-172.1-232.8-182.8-222.1-196-222.1L-196-222.1z M-196-267.3c-11.7,0-21.3,9.6-21.3,21.3s9.6,21.3,21.3,21.3s21.3-9.6,21.3-21.3     S-184.3-267.3-196-267.3L-196-267.3z" id="Fill-49"/><polygon class="st0" id="Fill-50" points="-197.4,-236.1 -194.6,-236.1 -194.6,-233.3 -197.4,-233.3    "/><polyline class="st0" id="Fill-51" points="-195.2,-238.9 -196.8,-238.9 -197.4,-250.2 -197.4,-258.7 -194.6,-258.7      -194.6,-250.2 -195.2,-238.9    "/></g></g></svg>
                    </div>
                    <?php echo __( "File not found!", "download-manager" ); ?>
                </div>
            </div>
            <?php } else { ?>
            <div class="modal-body">
                <div class="media">
                    <?php if($picon !== ''){ ?>
                    <div class="mr-3">
                        <?php echo $picon; ?>
                    </div>
                    <?php } ?>
                    <div class="media-body">
                        <h1><?php echo $media->post_title; ?></h1>
                        <p>
                            Updated on <?php echo date(get_option('date_format'), strtotime($media->post_modified)); ?> <?php echo date(get_option('time_format'), strtotime($media->post_modified)); ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if($password){ ?>
                    <form method="post" id="__vpass" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <?php wp_nonce_field(NONCE_KEY, '__xnonce'); ?>
                        <input type="hidden" name="__meida" value="<?php echo $__hash; ?>">
                        <input type="hidden" name="action" value="wpdm_media_pass">
                        <div  class="input-group">
                            <input name="__pswd" required="required" id="__pwd" autocomplete="off" placeholder="<?php echo __( "Media Password", "download-manager" ); ?>" class="form-control" type="password">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><?php echo __( "Download", "download-manager" ); ?></button>
                            </div>
                        </div>
                    </form>
                    <script>
                        jQuery(function ($) {
                            $('#__vpass').on('submit', function (e) {
                                $('#__vpass').addClass('blockui');
                                e.preventDefault();
                                $(this).ajaxSubmit({
                                    success: function (res) {
                                        if(res.success){
                                            location.href = "<?php echo home_url('/?__mediakey='); ?>"+res.__mediakey;
                                        } else {
                                            $('#__error').html(res.error).fadeIn();
                                            $('#__vpass').removeClass('blockui');
                                        }

                                    }
                                })
                            });
                        });
                    </script>
                <?php } ?>
                <?php if(!$password){ ?>
                <button href="<?php echo $download_url; ?>" class="btn btn-primary btn-block btn-download">
                    Download  [ <?php echo $media->filesize; ?> ]
                </button>
                <?php } ?>
            </div>

            <?php } ?>

        </div>

    </div>
</div>

<script>
    jQuery(function ($) {
        $('#wpdm-download').modal({backdrop: 'static'});
    });
</script>
</body>
</html>
