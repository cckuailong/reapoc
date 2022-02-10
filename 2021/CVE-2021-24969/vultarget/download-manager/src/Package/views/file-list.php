
<ul class="list-group package-file-list">
<?php

foreach($files as $ind => $file){
    $relative_path = str_replace(array($package_dir, ABSPATH), '', $file);
    $individual_file_actions = '';
?>
<li class="list-group-item" title="<?php echo $file; ?>">
    <div class="pull-right"><nobr>
        <?php if($password_lock) { ?>
            
                <input type="password" placeholder="File Password" class="form-control input-xs"> <button type="btn" class="btn btn-primary btn-xs"><i class="fa fa-download"></i></button>
            
        <?php } else { ?>
            <button class="btn btn-primary btn-xs"><i class="fa fa-download"></i></button>
        <?php } ?>
        <?php echo apply_filters("individual_file_action", $individual_file_actions, $package_id, $file, $ind); ?>
            </nobr>
    </div>
    <?php echo isset($fileinfo[$file]['title']) && $fileinfo[$file]['title'] != '' ? $fileinfo[$file]['title'] : $relative_path; ?>
</li>
<?php } ?>
</ul>

<style>
    .package-file-list{
        padding-left: 0;
    }
    .package-file-list,
    .package-file-list .list-group-item{
        margin-left: 0;
    }
    .package-file-list .list-group-item{
        font-size: 9pt;
        line-height: 30px;
    }
    .package-file-list .list-group-item .form-control.input-xs{
        height: 25px;
        width: 100px;
        border-radius: 2px !important;
        display: inline;
    }
</style>