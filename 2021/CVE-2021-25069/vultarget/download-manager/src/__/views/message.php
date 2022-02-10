<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/26/18
 * Time: 12:33 AM
 */


?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type"
          content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
    <style>
        body{
            font-family: var(--wpdm-font);
            background: #ffffff url("<?php echo get_the_post_thumbnail_url(); ?>") no-repeat;
            background-size: cover;
        }
        .outer {
            display: table;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }

        .middle {
            display: table-cell;
            vertical-align: middle;
        }



        .w3eden .panel .panel-heading{
            font-size: 10px;
        }
        .w3eden p{
            margin: 15px 0 !important;
        }

    </style>
</head>
<body>
<div class="outer">
    <div class="middle">
        <div class="inner">
	        <?php
	        echo do_shortcode($msg);
	        ?>
        </div>
    </div>
</div>

</body>


</html>
