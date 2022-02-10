<?php
/**
 * The template for displaying ECWD Category pages
 *
 * @package WordPress
 * @subpackage Event Calendar WD
 */
global $ecwd_options;
$option = get_option('ecwd_event_category_' . $wp_query->queried_object->term_id);
$img_src = (isset($option['ecwd_taxonomy_image'])) ? $option['ecwd_taxonomy_image'] : "";
$term_description = $wp_query->queried_object->description;
$display_description = (!isset($ecwd_options['category_archive_description']) || (isset($ecwd_options['category_archive_description']) && $ecwd_options['category_archive_description'] === '1'));
$display_image = (!isset($ecwd_options['category_archive_image']) || (isset($ecwd_options['category_archive_image']) && $ecwd_options['category_archive_image'] === '1'));
$cat_title = $wp_query->queried_object->name;

$events_template_part_slug = (!empty($ecwd_options['category_archive_template_part_slug'])) ? $ecwd_options['category_archive_template_part_slug'] : "content";
$events_template_part_name = (!empty($ecwd_options['category_archive_template_part_name'])) ? $ecwd_options['category_archive_template_part_name'] : get_post_format();


get_header();
if ( class_exists( 'WooCommerce' ) ) {
    do_action('woocommerce_before_main_content');//TODO REMOVE?
}
?>

<section id="primary" class="content-area">    
    <div id="content" class="site-content" role="main">     
        <header class="page-header">
            <h1 class="page-title"><?php echo $cat_title; ?></h1>			
        </header>
        <div class="entry-header">                       
            <?php if ($display_image && $img_src != "") { ?>
                <div id="ecwd_category_archive_img">
                    <img src="<?php echo $img_src; ?>" />
                </div>
            <?php } ?>
            <?php if ($display_description) { ?>
                <div id="ecwd_category_archive_description">
                    <h2><?php echo $term_description; ?></h2>
                </div>
            <?php } ?> 
        </div>
    <?php get_template_part($events_template_part_slug, $events_template_part_name); ?>


</section><!-- #primary -->

<?php 
  get_sidebar();
    if ( class_exists( 'WooCommerce' ) ) {
        do_action('woocommerce_after_main_content');//TODO REMOVE ?
    }

  get_footer(); ?>