<?php
if(!defined("ABSPATH")) die("Shit happens!");

if(current_user_can('access_server_browser')) { ?>
    <div class="w3eden">
        <button type="button" class="btn btn-warning btn-block" id="attachsf"
                style="margin-top: 10px"><?php echo __("Select from server", "download-manager"); ?></button>
        <script>
            jQuery(function ($) {
                $('body').on('click', '#attachsf', function (e) {
                    e.preventDefault();
                    hide_asset_picker_frame();
                    $(window.parent.document.body).append("<iframe id='wpdm-asset-picker' style='left:0;top:0;width: 100%;height: 100%;z-index: 999999999;position: fixed;background: rgba(255,255,255,0.4) url('<?php echo home_url('/wp-content/plugins/download-manager/assets/images/loader.svg') ?>') center center no-repeat;background-size: 100px 100px;border: 0;' src='<?php echo admin_url('/index.php?assetpicker=1'); ?>'></iframe>");

                });
            });

            function hide_asset_picker_frame() {
                jQuery('#wpdm-asset-picker').remove();
            }

            function attach_server_files(files) {
                jQuery.each(files, function (index, file) {
                    var d = new Date();
                    var ID = d.getTime();
                    var html = jQuery('#wpdm-file-entry').html();
                    var ext = file.name.split('.');
                    ext = ext[ext.length - 1];
                    if (ext.indexOf('://')) ext = 'url';
                    else if (ext.length == 1 || ext == filename || ext.length > 4 || ext == '') ext = '_blank';
                    var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/" + ext.toLowerCase() + ".png";
                    var title = file.name;
                    title = title.split('/');
                    title = title[title.length - 1];

                    var _file = {};
                    _file.filetitle = file.name;
                    _file.filepath = file.path;
                    _file.fileindex = ID;
                    _file.preview = icon;
                    wpdm_attach_file(_file);

                });
            }
        </script>
    </div>
    <?php
}