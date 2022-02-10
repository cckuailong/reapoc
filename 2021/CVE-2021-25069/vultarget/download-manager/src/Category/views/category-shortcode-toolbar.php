<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 6/6/20 09:48
 */

//$tbgrid - Toolbar grid layout setting variable, 12 grid layout distribute amonng search bar, order by dropdown, order dropdown, and filter button
//$sr - Search bar width
//$ob - Orderby dropdown width
//$od - Order dropdown width
//$bt - Button area width
if(!defined("ABSPATH")) die();
list($sr, $ob, $od, $bt) = [6,2,2,2];
if(isset($tbgrid)){
    list($sr, $ob, $od, $bt) = explode(",", $tbgrid);
}
//Show  toolbar on post type archive
if(is_post_type_archive()) $toolbar = 1;
if ($toolbar) {
?>
<form method="get" class="<?php if(isset($async) && (int)$async === 1) echo '__wpdm_submit_async'; ?>" data-container="#content_<?php echo $scid; ?>" id="sc_form_<?php echo $scid; ?>" style="margin-bottom: 15px">
    <?php
        if ($toolbar !== 'skinny' && $title !== '') {


            ?>
            <div class="panel panel-default card category-panel wpdm-shortcode-toolbar">
                <div class="panel-body card-body">
                    <div class="media">
                        <?php echo isset($icon) ? $icon : ''; ?>
                        <div class="media-body">
                            <h3 style="margin: 0"><?php echo $title; ?></h3>
                            <?php echo $desc; ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer card-footer">
                    <div class="row">
                        <div class="col-lg-<?php echo $sr; ?> col-md-12">
                            <input type="text" name="skw" value="<?php echo stripslashes_deep(wpdm_query_var('skw', 'txt')) ?>" placeholder="<?php echo __( "Search Keyword...", "download-manager" ); ?>" class="form-control" />
                        </div>
                        <div class="col-lg-<?php echo $ob; ?> col-md-4">
                            <select name="orderby" class="wpdm-custom-select custom-select">
                                <option value="date" disabled="disabled"><?php echo __( "Order By:", "download-manager" ) ?></option>
                                <option value="date" <?php selected('date', wpdm_query_var('orderby')) ?>><?php echo __( "Publish Date", "download-manager" ) ?></option>
                                <option value="title" <?php selected('title', wpdm_query_var('orderby')) ?>><?php echo __( "Title", "download-manager" ) ?></option>
                                <option value="update_date" <?php selected('update_date', wpdm_query_var('orderby')) ?>><?php echo __( "Update Date", "download-manager" ) ?></option>
                                <option value="downloads" <?php selected('downloads', wpdm_query_var('orderby')) ?>><?php echo __( "Downloads", "download-manager" ) ?></option>
                                <option value="views" <?php selected('views', wpdm_query_var('orderby')) ?>><?php echo __( "Views", "download-manager" ) ?></option>
                            </select>
                        </div>
                        <div class="col-lg-<?php echo $od; ?> col-md-4">
                            <select name="order" class="wpdm-custom-select custom-select">
                                <option value="desc" disabled="disabled"><?php echo __( "Order:", "download-manager" ) ?></option>
                                <option value="desc" <?php selected('desc', wpdm_query_var('order')) ?>><?php echo __( "Descending", "download-manager" ) ?></option>
                                <option value="asc" <?php selected('asc', wpdm_query_var('order')) ?>><?php echo __( "Ascending", "download-manager" ) ?></option>
                            </select>
                        </div>
                        <div class="col-lg-<?php echo $bt; ?> col-md-4">
                            <button type="submit" class="btn btn-secondary btn-block"><?php echo __( "Apply Filter", "download-manager" ) ?></button>
                        </div>
                    </div>

                </div>
            </div>
            <?php
        } else {
            ?>

            <div class="card panel panel-default wpdm-shortcode-toolbar">
                <div class="card-body panel-body">
                    <div class="row">
                        <div class="col-lg-<?php echo $sr; ?> col-md-12">
                            <input type="search" name="skw" value="<?php echo stripslashes_deep(wpdm_query_var('skw', 'txt')) ?>" placeholder="<?php echo __( "Search Keyword...", "download-manager" ); ?>" class="form-control" />
                        </div>
                        <div class="col-lg-<?php echo $ob; ?> col-md-4">
                            <select name="orderby" class="wpdm-custom-select custom-select">
                                <option value="date" disabled="disabled"><?php echo __( "Order By:", "download-manager" ) ?></option>
                                <option value="date" <?php selected('date', wpdm_query_var('orderby')) ?>><?php echo __( "Publish Date", "download-manager" ) ?></option>
                                <option value="title" <?php selected('title', wpdm_query_var('orderby')) ?>><?php echo __( "Title", "download-manager" ) ?></option>
                                <option value="update_date" <?php selected('update_date', wpdm_query_var('orderby')) ?>><?php echo __( "Update Date", "download-manager" ) ?></option>
                                <option value="downloads" <?php selected('downloads', wpdm_query_var('orderby')) ?>><?php echo __( "Downloads", "download-manager" ) ?></option>
                                <option value="views" <?php selected('views', wpdm_query_var('orderby')) ?>><?php echo __( "Views", "download-manager" ) ?></option>
                            </select>
                        </div>
                        <div class="col-lg-<?php echo $od; ?> col-md-4">
                            <select name="order" class="wpdm-custom-select custom-select">
                                <option value="desc" disabled="disabled"><?php echo __( "Order:", "download-manager" ) ?></option>
                                <option value="desc" <?php selected('desc', wpdm_query_var('order')) ?>><?php echo __( "Descending", "download-manager" ) ?></option>
                                <option value="asc" <?php selected('asc', wpdm_query_var('order')) ?>><?php echo __( "Ascending", "download-manager" ) ?></option>
                            </select>
                        </div>
                        <div class="col-lg-<?php echo $bt; ?> col-md-4">
                            <button type="submit" class="btn btn-secondary btn-block"><?php echo __( "Apply Filter", "download-manager" ) ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }
    ?>
</form>
<div class="spacer mb-3 d-block clearfix"></div>
<?php }
