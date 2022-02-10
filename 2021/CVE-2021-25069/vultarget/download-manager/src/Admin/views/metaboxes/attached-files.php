<?php

$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

$fileinfo = get_post_meta($post->ID, '__wpdm_fileinfo', true);


?>

<style>
    #wpdm-attached-files .inside{
        padding: 0;
        margin: 0;
    }
    .wpdm-split-container{
        display: flex;
        min-height: 60px;
    }

    .wpdm-split-container .wpdm-sidebar{
        border-right: 1px solid #ccd0d4;
        background: #fafafa;
        width: 40%;
    }

    .wpdm-split-container .wpdm-content{
        width: 60%;
    }

    .wpdm-split-container .wpdm-content .tab-pane{
        padding: 30px;
    }

    .wpdm-split-container .wpdm-sidebar .wpdm-list-item:not(:last-child){
        border-bottom: 1px solid #ccd0d4;
    }
    .wpdm-split-container .wpdm-sidebar .wpdm-list-item{
        padding: 20px;
        cursor: pointer;
    }
    .wpdm-split-container .wpdm-sidebar .wpdm-list-item.active{
        background: #eef4f7 !important;
        color: #0e6c9b;
    }
    .wpdm-split-container .wpdm-sidebar .wpdm-list-item.ui-sortable-placeholder{
        background: #ccd0d4 !important;
        height: 40px;
        visibility: visible !important;
    }
    .wpdm-split-container .wpdm-sidebar .wpdm-list-item.ui-sortable-helper{
        border: 1px solid #ccd0d4;
        background: #e8eef3;
    }
    .wpdm-split-container .wpdm-sidebar .wpdm-list-item:hover{
        background: rgba(255,255,255,0.3);
    }

    #wpdm-attach-files{
        max-height: 500px;
        overflow: auto;
     }
    .d-block{
        display: block;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 28px;
        height: 16px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 500px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 10px;
        width: 10px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 500px;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(12px);
        -ms-transform: translateX(12px);
        transform: translateX(12px);
    }

    .wpdm-split-container .wpdm-sidebar .wpdm-list-item.to-delete{
        background: #f7eef1 !important;
        color: #c33455;
        border-bottom: 1px solid #e25c7c;
    }

</style>

<div class="w3eden">
    <div class="wpdm-split-container">
        <div class="wpdm-sidebar">
            <input type="text" style="border: 0;border-radius: 0;border-bottom: 1px solid #ccd0d4;" class="form-control input-lg" id="searchfile" placeholder="<?php echo __( 'Search in Attached Files...', 'download-manager' ); ?>" />
            <div id="wpdm-attach-files">
                <?php foreach ($files as $file_id => $file) { ?>
                <div class="wpdm-list-item" style="position: relative" data-target="#file_info_<?php echo  $file_id ?>">
                    <button type="button" class="btn btn-sm btn-danger show-on-hover" style="position: absolute;right: 20px" rel="del"><i class="fas fa-trash"></i></button>
                    <div>
                        <input class="faz" type="hidden" value="<?php echo  $file ?>" name="file[files][<?php echo  $file_id ?>]" />
                        <strong class="d-block"><?php echo  wpdm_valueof($fileinfo, "{$file_id}/title"); ?></strong>
                        <?php echo  $file ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="wpdm-content">
        <div id="wpdm-file-info">
            <?php

            foreach ($files as $id => $value) {
                $file_index = $id;
                $svalue = $value;
                if (strlen($value) > 50) {
                    $svalue = substr($value, 0, 23) . "..." . substr($value, strlen($value) - 27);
                }
                $imgext = array('png', 'jpg', 'jpeg', 'gif');
                $ext = explode(".", $value);
                $ext = end($ext);
                $ext = strtolower($ext);

                $filepath = WPDM()->fileSystem->absPath($value, $post->ID);

                $thumb = $url = "";

                if ($filepath) {
                    if (in_array($ext, $imgext)) {
                        if (WPDM\__\__::is_url($filepath)) {
                            $url = $filepath;
                            $filepath = str_replace(home_url(), ABSPATH, $filepath);
                        } else {

                            if ($filepath !== $url)
                                $thumb = wpdm_dynamic_thumb($filepath, array(48, 48), true);
                        }
                    }

                    if ($ext == '')
                        $ext = '_blank';
                } else {
                    $ext = '_blank';
                }

                include __DIR__.'/file-info.php';

            }
            ?>
        </div>
        </div>
    </div>
</div>
<script>

    jQuery(function ($){
        $('body').on('click', '.wpdm-list-item', function (){
            $('#wpdm-attached-files .tab-pane').addClass('hide');
            $($(this).data('target')).removeClass('hide').removeAttr('style');
            $('.wpdm-list-item').removeClass('active');
            $(this).addClass('active');
        });

        $('body').on('click','button[rel=del], button[rel=undo]', function () {

            if ($(this).attr('rel') == 'del') {
                $(this).parent('.wpdm-list-item').addClass('to-delete');
                var fld = $(this).parents('div.wpdm-list-item').find('input.faz');
                fld.data('oldname', fld.attr('name')).attr('name', 'del[]');
                $(this).attr('rel', 'undo').html('<i class="fa fa-undo"></i>');

            } else {
                $(this).parent('.wpdm-list-item').removeClass('to-delete');
                var fld = $(this).parents('div.wpdm-list-item').find('input.faz');
                fld.attr('name', fld.data('oldname'));
                $(this).attr('rel', 'del').html('<i class="fas fa-trash"></i>');


            }

            return false;
        });

        $('#searchfile').on('keyup', function (){
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("searchfile");
            filter = input.value.toUpperCase();
            ul = document.getElementById("wpdm-attach-files");
            li = ul.getElementsByClassName("wpdm-list-item");
            for (i = 0; i < li.length; i++) {
                /*a = li[i].getElementsByTagName("a")[0];*/
                txtValue = li[i].textContent || li[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        });

        $('#wpdm-attach-files .wpdm-list-item:first-child').trigger('click');
        $('#wpdm-attach-files').sortable();

    });
</script>

<script type="text/wpdm-template" id="wpdm-file-item-template" style="display:none;">
    <div class="wpdm-list-item" data-target="#file_info_{{fileindex}}">
        <input class="faz" type="hidden" value="{{filepath}}" name="file[files][{{fileindex}}]" />
        <strong class="d-block">{{filetitle}}</strong>
        {{filepath}}
    </div>
</script>
<script type="text/wpdm-template" id="wpdm-file-info-template" style="display:none;">
    <?php
    include __DIR__.'/file-info.tpl.php';
    ?>
</script>
