<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:18
 */
if(!defined("ABSPATH")) die();

    ?>

    <div id="__imtemplates">
        <div style="padding: 100px; text-align: center" class="lead">
            <i class="fa fa-spin fa-sun"></i> <?php echo __( "Connecting Template Server", "download-manager" ) ?>
        </div>
    </div>

    <style>
        img{
            max-width: 100%;
        }
    </style>
    <script>
        jQuery(function ($) {
            $('#__imtemplates').load(ajaxurl, {action: 'connect_template_server'});
            $('body').on('click', '.btn-import-template', function () {
                var $this = $(this);
                $this.attr('disabled', 'disabled').html('<i class="fa fa-spin fa-sun"></i>');
                $.post(ajaxurl, {action: 'wpdm_import_template', template: $(this).data('template'), template_type: $(this).data('type')}, function (response) {
                    WPDM.notify("<b><i  class='fa fa-check-double'></i> Done</b><br/>Template imported successfully!");
                    $this.removeAttr('disabled').html('Import');
                });
            });

        });
    </script>
    <style>
        .w3eden .panel .panel-footer.import-footer{
            position: relative;
            padding: 0 0 0 15px;
            line-height: 36px;
            overflow: hidden;
        }
        .w3eden .btn.btn-import-template{
            top: 0;right: 0;border-radius: 0;height: 100%;position: absolute;padding: 0 20px;
        }
    </style>
