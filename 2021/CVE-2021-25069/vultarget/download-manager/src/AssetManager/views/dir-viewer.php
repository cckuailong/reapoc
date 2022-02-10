<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 2019-08-30 12:11
 */

if(!defined("ABSPATH")) die();
global $flatList;
$flatList = [];
function __print_items($items, $level = 0){
    global $flatList;
    foreach ($items as $index => $node) {
        $flatList[$index] = $node;
        if($node['type'] === 'dir') {
            $file = esc_attr(basename($node['path']));
            echo "<li class='tree-item level-{$level}' data-index='{$index}'>{$file}";
            if (count($node['items']) > 0) {
                echo "<ul>";
                __print_items($node['items'], $level+1);
                echo "</ul>";
            }
            echo "</li>";
        }
    }
}

?>

<style>
    .__wpdm_asset_tree ul{
        margin-left: 0px;
        padding-left: 0;
    }
    .__wpdm_asset_tree li{
        list-style: none;
        background: url("<?php echo WPDM_BASE_URL; ?>assets/images/folder.svg") left 4px no-repeat;
        background-size: 12px;
        padding-left: 18px;
        margin-left: 0;
        font-size: 12px;
        line-height:20px;
        cursor: pointer;
    }
    .container{
        display: flex;
    }
    .container .sidebar{
        width: 25%;
    }
    .container .content{
        width: 75%;
    }
</style>

<div class="container">
    <div class="sidebar">
        <ul class="__wpdm_asset_tree">
            <?php __print_items($dirTree);  ?>
        </ul>
    </div>
    <div class="content" id="dircontent">
        <div v-for="file in files">
            <div  class="file-item">{{ file.name }}</div>
        </div>
    </div>
</div>
<script src="<?php echo WPDM_BASE_URL ?>assets/js/vue.min.js"></script>
<script>
    var dirviewer = new Vue({
        el: '#dircontent',
        data: {
            files: []
        }
    });

    var assets = <?php echo json_encode($flatList); ?>;
    jQuery(function ($) {
        $('.tree-item').on('click', function () {
            var asset = assets[$(this).data('index')];
            console.log(asset.items);
            dirviewer.files = asset.items;
        });
    });
</script>