<?php
if (!defined('ABSPATH')) die();
global $btnclass;
if(!is_array($params)) $params = array();
$p = serialize($params);
$tid = md5($p);
if(!isset($params['items_per_page'])) $params['items_per_page'] = 20;
$cols = isset($params['cols'])?$params['cols']:'page_link,file_count,download_count|categories|update_date|download_link';
$colheads = isset($params['colheads'])?$params['colheads']:'Title|Categories|Update Date|Download';
$cols = explode("|", $cols);
$colheads = explode("|", $colheads);
foreach ($cols as $index => &$col){
   $col = explode(",", $col);
   $colheads[$index] = !isset($colheads[$index])?$col[0]:$colheads[$index];
}

$column_positions = array();

//$coltemplate['title'] = $coltemplate['post_title'] = "%the_title%";
$coltemplate['page_link'] = "<a class=\"package-title\" href=\"%s\">%s</a>";

if(isset($params['jstable']) && $params['jstable']==1):
    $_cols =  explode("|", wpdm_valueof($params, 'cols', ['default' => '']));
    $datatable_col = ( isset($params['order_by']) && $params['order_by'] == 'title' ) ? '0' : array_search(wpdm_valueof($params, 'order_by'), $_cols);
    if(!$datatable_col || $datatable_col < 0) $datatable_col = 0;
    $datatable_order = ( isset($params['order']) && $params['order'] == 'DESC' ) ? 'desc' : 'asc';

    ?>

    <script>
        jQuery(function($){

            var __dt = $('#wpdmmydls-<?php echo $tid; ?>').dataTable({
                "dom": '<"wpdmdt-toolbar"lfrB>t<"wpdmdt-toolbarb"ip>',
                responsive: true,
                "order": [[ <?php echo $datatable_col; ?>, "<?php echo $datatable_order; ?>" ]],
                "language": {
                    "lengthMenu": "<?php _e("Display _MENU_ downloads per page",'download-manager')?>",
                    "zeroRecords": "<?php _e("Nothing _START_ to - sorry",'download-manager')?>",
                    "info": "<?php _e("Showing _START_ to _END_ of _TOTAL_ downloads",'download-manager')?>",
                    "infoEmpty": "<?php _e("No downloads available",'download-manager')?>",
                    "infoFiltered": "<?php _e("(filtered from _MAX_ total downloads)",'download-manager');?>",
                    "emptyTable":     "<?php _e("No data available in table",'download-manager');?>",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "loadingRecords": "<?php _e("Loading...",'download-manager'); ?>",
                    "processing":     "<?php _e("Processing...",'download-manager'); ?>",
                    "search":         "<?php _e("Search:",'download-manager'); ?>",
                    "paginate": {
                        "first":      "<?php _e("First",'download-manager'); ?>",
                        "last":       "<?php _e("Last",'download-manager'); ?>",
                        "next":       "<?php _e("Next",'download-manager'); ?>",
                        "previous":   "<?php _e("Previous",'download-manager'); ?>"
                    },
                    "aria": {
                        "sortAscending":  " : <?php _e("activate to sort column ascending",'download-manager'); ?>",
                        "sortDescending": ": <?php _e("activate to sort column descending",'download-manager'); ?>"
                    }
                },
                "iDisplayLength": <?php echo $params['items_per_page'] ?>,
                "aLengthMenu": [[<?php echo $params['items_per_page']; ?>, 10, 25, 50, -1], [<?php echo $params['items_per_page']; ?>, 10, 25, 50, "<?php _e("All",'download-manager'); ?>"]]
            });

            <?php if(count($_GET) > 0){ ?>
            $("div.wpdmdt-toolbar .dataTables_filter").append('<a href="<?php the_permalink(); ?>" class="btn btn-secondary ml-3" style="margin-top: -5px;border: 0;"><?php echo  esc_attr__( 'Reset Filter', WPDM_TEXT_DOMAIN ); ?></a>');
            <?php } ?>
        });
    </script>
<?php endif; ?>
<style>
    .wpdmdt-toolbar {
        padding: 10px 10px 0 10px;
    }
    .wpdmdt-toolbarb {
        padding: 5px 10px 10px;
    }
    .wpdmdt-toolbar > div {
        display: inline-block;
    }
    table,td,th{
        border: 0;
    }
    #wpdm-all-packages .card{
        overflow: hidden;
    }
    .dataTables_wrapper .table{
        margin: 0;
    }
    #wpdmmydls-<?php echo $tid; ?>{
        border-bottom: 1px solid #dddddd;
        border-top: 1px solid #dddddd;
        font-size: 10pt;
        min-width: 100%;
    }
    #wpdmmydls-<?php echo $tid; ?> .wpdm-download-link img{
        box-shadow: none !important;
        max-width: 100%;
    }
    .w3eden .pagination{
        margin: 0 !important;
    }
    #wpdmmydls-<?php echo $tid; ?> td:not(:first-child){
        vertical-align: middle !important;
    }
    #wpdmmydls-<?php echo $tid; ?> td.__dt_col_download_link .btn{
        display: block;
        width: 100%;
    }
    #wpdmmydls-<?php echo $tid; ?> td.__dt_col_download_link,
    #wpdmmydls-<?php echo $tid; ?> th#download_link{
        max-width: 155px !important;
        width: 155px;

    }
    #wpdmmydls-<?php echo $tid; ?> th{
        background-color: rgba(0,0,0,0.04);
        border-bottom: 1px solid rgba(0,0,0,0.025);
    }
    #wpdmmydls-<?php echo $tid; ?>_length label,
    #wpdmmydls-<?php echo $tid; ?>_filter label{
        font-weight: 400;
    }
    #wpdmmydls-<?php echo $tid; ?>_filter input[type=search]{
        display: inline-block;
        width: 200px;
    }
    #wpdmmydls-<?php echo $tid; ?>_length select{
        display: inline-block;
        width: 60px;
    }

    #wpdmmydls-<?php echo $tid; ?> .package-title{
        color:#36597C;
        font-size: 11pt;
        font-weight: 700;
    }
    #wpdmmydls-<?php echo $tid; ?> .small-txt{
        margin-right: 7px;
    }
    #wpdmmydls-<?php echo $tid; ?> td{
        min-width: 150px;
    }

    #wpdmmydls-<?php echo $tid; ?> td.__dt_col_categories{
        max-width: 300px;
    }

    #wpdmmydls-<?php echo $tid; ?> .small-txt,
    #wpdmmydls-<?php echo $tid; ?> small{
        font-size: 9pt;
    }
    .w3eden .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: rgba(0,0,0,0.015);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:active,
    .dataTables_wrapper .dataTables_paginate .paginate_button:focus,
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }


    @media (max-width: 799px) {
        #wpdmmydls-<?php echo $tid; ?> tr {
            display: block;
            border: 3px solid rgba(0,0,0,0.3) !important;
            margin-bottom: 10px !important;
            position: relative;
        }
        #wpdmmydls-<?php echo $tid; ?> thead{
            display: none;
        }
        #wpdmmydls-<?php echo $tid; ?>,
        #wpdmmydls-<?php echo $tid; ?> td:first-child {
            border: 0 !important;
        }
        #wpdmmydls-<?php echo $tid; ?> td {
            display: block;
        }
        #wpdmmydls-<?php echo $tid; ?> td.__dt_col_download_link {
            display: block;
            max-width: 100% !important;
            width: auto !important;

        }
    }


</style>
<div class="w3eden">
    <div id="wpdm-all-packages">
        <table id="wpdmmydls-<?php echo $tid; ?>" class="table table-striped wpdm-all-packages-table">
            <thead>
            <tr>
                <?php foreach ($colheads as $ix => $colhead){
                    $_colhead = explode("::", $colhead);
                    $width = (isset($_colhead[1]))?"width: {$_colhead[1]} !important;max-width: {$_colhead[1]} !important;":"";
                    ?>
                <th style="<?php echo $width; ?>"  id="<?php echo $cols[$ix][0]; ?>" class="<?php if($ix > 0) echo 'hidden-sm hidden-xs'; ?>"><?php _e($_colhead[0],'download-manager'); ?></th>
                <?php } ?>

            </tr>
            </thead>
            <tbody>
            <?php


            $cfurl = get_permalink();
            $query_params = array("post_type"=>"wpdmpro","posts_per_page"=>$items,"offset"=>$offset);
            if(isset($tax_query)) $query_params['tax_query'] = $tax_query;
            $query_params['orderby'] = (isset($params['order_by']))?$params['order_by']:'date';

            $order_field = isset($params['order_by']) ? $params['order_by'] : 'date';
            $order = isset($params['order']) ? $params['order'] : 'DESC';

            $order_fields = array('__wpdm_download_count','__wpdm_view_count','__wpdm_package_size_b');
            if(!in_array( "__wpdm_".$order_field, $order_fields)) {
                $query_params['orderby'] = $order_field;
                $query_params['order'] = $order;
            } else {
                $query_params['orderby'] = 'meta_value_num';
                $query_params['meta_key'] = "__wpdm_".$order_field;
                $query_params['order'] = $order;
            }

            if(is_array(wpdm_query_var('tax'))){
                foreach (wpdm_query_var('tax') as $tax => $term) {
                    $query_params['tax_query'][] = [
                            'taxonomy' => $tax,
                        'field' => 'slug',
                        'terms' => [$term]
                    ];
                    $query_params['tax_query']['relation'] = 'AND';
                }
            }

            $taxonomies = get_object_taxonomies( 'wpdmpro' );

            $q = new WP_Query($query_params);
            $total_files = $q->found_posts;
            while ($q->have_posts()): $q->the_post();

                $ext = "unknown";
                $data = WPDM()->package->prepare(get_the_ID())->packageData;
                //wpdmdd($data);
                global $post;
                $data += (array)$post;
                $data['id'] = $data['ID'];
                $data['files'] = WPDM()->package->getFiles(get_the_ID());
                //$author = get_user_by('id', $post->post_author);
                $data['author'] = $data['author_name'];

                if(isset($data['files'])&&count($data['files'])){
                    if(count($data['files']) == 1) {
                        $tmpavar = $data['files'];
                        $ffile = $tmpvar = array_shift($tmpavar);
                        $tmpvar = explode(".", $tmpvar);
                        $ext = count($tmpvar) > 1 ? end($tmpvar) : $ext;
                    } else
                        $ext = 'zip';
                } else $data['files'] = array();

                foreach ($taxonomies as $taxonomy) {
                    $terms = wp_get_post_terms(get_the_ID(), $taxonomy);
                    $_terms = array();
                    foreach ($terms as $term) {
                        $lurl = add_query_arg(['tax' => [$taxonomy => $term->slug]], $cfurl);
                        $_terms[] = "<a class='sbyc' href='{$lurl}'>{$term->name}</a>";
                    }
                    $_terms = @implode(", ", $_terms);
                    $data[$taxonomy] = $_terms;
                }

                if($ext=='') $ext = 'unknown';

                $ext = \WPDM\__\FileSystem::fileTypeIcon($ext);

                if(isset($data['icon']) && $data['icon'] !== '') $ext = $data['icon'];

                if(isset($params['thumb']) && (int)$params['thumb'] == 1) $ext = wpdm_thumb($post, array(96,104), 'url');

                $data['download_url'] = '';
                $data['download_link'] = WPDM()->package->downloadLink($data['ID'], 0, array('template_type' => 'link'));
                $data = apply_filters("wpdm_after_prepare_package_data", $data);
                $download_link = htmlspecialchars_decode($data['download_link']);
                if (function_exists('wpdmpp_effective_price') && wpdmpp_effective_price($data['ID']) > 0)
                    $download_link = wpdmpp_waytocart($data, 'btn-primary');

                if(WPDM()->package->userCanAccess($data['ID']) || !get_option("_wpdm_hide_all", 0)){
                    ?>

                    <tr class="__dt_row">
                        <?php
                        $tcols = $cols;
                        array_shift($tcols);
                        foreach ($cols as $colx => $cold){
                            $dor = array('publish_date' => strtotime(get_the_date('Y-m-d')), 'create_date' => strtotime(get_the_date('Y-m-d')), 'update_date' => strtotime(get_the_modified_date('Y-m-d', get_the_ID())));
                            ?>
                        <td <?php if(in_array($cold[0], array('publish_date', 'update_date','create_date'))) { ?> data-order="<?php echo $dor[$cold[0]]; ?>" <?php } ?> class="__dt_col_<?php echo $colx; ?> __dt_col __dt_col_<?php echo $cold[0]; ?>" <?php if($colx == 0) { ?>style="background-image: url('<?php echo $ext ; ?>');background-size: 36px;background-position: 5px 8px;background-repeat:  no-repeat;padding-left: 52px;line-height: normal;"<?php } ?>>
                            <?php

                            foreach ($cold as $cx => $c){
                                $cxc = ($cx > 0)?'small-txt':'';
                                switch ($c) {
                                    case 'title':
                                        echo "<strong>".get_the_title()."</strong><br/>";
                                        break;
                                    case 'page_link':
                                        echo "<a class=\"package-title\" href='".get_the_permalink(get_the_ID())."'>".get_the_title()."</a><br/>";
                                        break;
                                    case 'excerpt':
                                    case (preg_match('/excerpt_.+/', $c) ? true : false) :
                                        $xcol = explode("_", $c);
                                        $len = isset($xcol[1])?$xcol[1]:false;
                                        $cont = strip_tags($data['post_content']);

                                        if(!$len)
                                            echo "<div class='__dt_excerpt {$cxc}'>".get_the_excerpt()."</div>";
                                        else {
                                            $excerpt = strlen($cont) > $len?substr($cont, 0, strpos($cont, ' ', $len)):$cont;
                                            echo "<div class='__dt_excerpt {$cxc}'>" . $excerpt . "</div>";
                                        }
                                        break;
                                    case 'download_count':
                                        if($cx > 0)
                                            echo "<span class='__dt_download_count {$cxc}'><i class=\"far fa-arrow-alt-circle-down\"></i> ". (isset($data['download_count'])?$data['download_count']:0)." ".(isset($data['download_count']) && $data['download_count'] > 1 ?  __('downloads','download-manager') : __('download','download-manager'))."</span>";
                                        else
                                            echo "<span class=\"hidden-md hidden-lg td-mobile\">{$colheads[$colx]}: </span><span class='__dt_download_count {$cxc}'>{$data['download_count']}</span>";
                                        break;
                                    case 'view_count':
                                        if($cx > 0)
                                            echo "<span class='__dt_view_count {$cxc}'><i class=\"fa fa-eye\"></i> ". (isset($data['view_count'])?$data['view_count']:0)." ".(isset($data['view_count']) && $data['view_count'] > 1 ?  __('views','download-manager') : __('view','download-manager'))."</span>";
                                        else
                                            echo "<span class=\"hidden-md hidden-lg td-mobile\">{$colheads[$colx]}: </span><span class='__dt_view_count'>{$data['view_count']}</span>";
                                        break;
                                    case 'categories':
                                        echo "<span class='__dt_categories {$cxc}'>".$data['wpdmcategory']."</span>";
                                        break;
                                    case 'tags':
                                        echo "<span class='__dt_categories {$cxc}'>".$data['wpdmtag']."</span>";
                                        break;
                                    case 'update_date':
                                        echo "<span class='__dt_update_date {$cxc}'>".get_the_modified_date('', get_the_ID())."</span>";
                                        break;
                                    case 'publish_date':
                                        echo "<span class='__dt_publish_date {$cxc}'>".get_the_date()."</span>";
                                        break;
                                    case 'download_link':
                                        echo $download_link ? $download_link : '<button type="button" disabled="disabled" class="btn btn-danger btn-block">'.__( "Download", "download-manager" ).'</button>';
                                        break;
                                    case 'audio_player':
                                        $data['files'] = WPDM()->package->getFiles($data['ID']);
                                        echo WPDM()->package->audioPlayer($data, true, 'success');
                                        break;
                                    default:
                                        if(isset($data[$c])) {
                                            if ($cx > 0)
                                                echo "<span class='__dt_{$c} {$cxc}'>" . $data[$c] . "</span>";
                                            else
                                                echo $data[$c];
                                        }
                                        break;


                            }}
                            if($colx == 0) echo '<div class="hidden-md hidden-lg td-mobile"></div>';
                                        ?>


                        </td>
                        <?php }  ?>

                    </tr>
                <?php } endwhile; ?>
            <?php if((!isset($params['jstable']) || $params['jstable']==0) && $total_files==0): ?>
                <tr>
                    <td colspan="4" class="text-center">

                        <?php echo isset($params['no_data_msg']) && $params['no_data_msg']!=''?$params['no_data_msg']:__('No Packages Found','download-manager'); ?>

                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php
        global $wp_rewrite,$wp_query;

        isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

        echo  wpdm_paginate_links($total_files, $items, $cp);
        wp_reset_query();
        ?>

    </div>
</div>
