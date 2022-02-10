<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 30/5/20 13:44
 */
if(!defined("ABSPATH")) die();

get_header();

$category = get_queried_object();
$cpage_global = maybe_unserialize(get_option('__wpdm_cpage'));
$cpage_global = !is_array($cpage_global) ? [ 'template' => 'link-template-default', 'cols' => 2, 'colsphone' => 1, 'colspad' => 1, 'heading' => 1 ] : $cpage_global;

$cpage = maybe_unserialize(get_term_meta(get_queried_object_id(), '__wpdm_pagestyle', true));
$cpage = !is_array($cpage) ? $cpage_global : $cpage;
if(get_queried_object_id() > 0)
    $cpage['categories'] = $category->slug;
$cpage['toolbar'] = (int)$cpage['heading'];
$cpage['async'] = 1;
$cpage['paging'] = 1;
$cols  = 12/$cpage['cols'];
$colspad  = 12/$cpage['colspad'];
$colsphone  = 12/$cpage['colsphone'];
if(wpdm_query_var('skw', 'txt') !== '') $cpage['s'] = wpdm_query_var('skw', 'txt');
?>
<div class="w3eden">
    <div class="container pt-3">

        <?php do_action("wpdm_before_category_page_content", $category); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="pb-5">
                    <?php
                    if($cpage['toolbar']  === 1)
                    echo WPDM()->package->shortCodes->packages( $cpage );
                    else {
                        //Global query data
                        echo "<div class='row'>";
                        while (have_posts()) {
                            the_post();
                            echo "<div class='col-lg-{$cols} col-md-{$colspad} col-sm-{$colsphone}'>" . WPDM()->package->fetchTemplate($cpage['template'], get_the_ID()) . "</div>";
                        }
                        echo  "</div>";

                        global $wp_rewrite;
                        $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

                        $pagination = array(
                            'base'               => add_query_arg( 'paged', '%#%' ),
                            'format'             => '',
                            'total'              => $wp_query->max_num_pages,
                            'current'            => $current,
                            'show_all'           => false,
                            'type'               => 'list',
                            'prev_next'          => true,
                            'prev_text'          => '<i class="far fa-arrow-alt-circle-left"></i> ' . __( 'Previous', 'attire' ),
                            'next_text'          => __( 'Next', 'attire' ) . ' <i class="far fa-arrow-alt-circle-right"></i>',
                            'screen_reader_text' => '',
                        );

                        if ( $wp_rewrite->using_permalinks() && ! is_search() ) {
                            $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
                        }

                        if ( ! empty( $wp_query->query_vars['s'] ) ) {
                            $pagination['add_args'] = array( 's' => get_query_var( 's' ) );
                        }
                        ?>
                        <div class="text-center p-3">
                            <div class="d-inline-block">
                            <?php
                            echo str_replace( '<ul class=\'page-numbers\'>',
                                '<ul class="pagination pagination-centered page-numbers">',
                                get_the_posts_pagination( $pagination ) );
                            ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php do_action("wpdm_after_category_page_content", $category); ?>

    </div>
</div>
<?php

get_footer();
