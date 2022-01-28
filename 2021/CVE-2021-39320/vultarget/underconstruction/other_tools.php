 <?php
add_thickbox();
wp_enqueue_script('under-construction-admin',plugin_dir_url( __FILE__ ). 'scripts/underconstruction-scripts.js',array('jquery'));
wp_enqueue_style('under-construction-admin-style',plugin_dir_url( __FILE__ ).'styles/underconstruction-style-common.css', array(), '3.1.1');

?>
 <div class="wrap">
	<h2>Other Tools</h2>
    <p>
        <div class="under-construction-plugin-box">
                <div class="under-construction-plugin-box-logo-container">
                        <img src="<?php echo plugin_dir_url(__FILE__) . 'images/products/appsumo-logo.png'; ?>">
                </div>
                <a href="https://appsumo.com/tools/wordpress/?utm_source=sumo&utm_medium=wp-widget&utm_campaign=underconstruction" target="_blank">AppSumo</a> Promotes great products to help you in your career and life.
        </div>

        <div class="under-construction-plugin-box">
                <div class="under-construction-plugin-box-logo-container">
                        <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/products/sendfox-logo.svg'; ?>">
                </div>
                <a href="http://sendfox.com" target="_blank">SendFox</a> Email marketing for content creators.
        </div>

        <div class="under-construction-plugin-box">
                <div class="under-construction-plugin-box-logo-container">
                        <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/products/sumo-logo.png'; ?>">
                </div>
                <a href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500'); ?>" class="thickbox">Sumo</a> Tools to grow your Email List, Social Sharing and Analytics.
        </div>

        <div class="under-construction-plugin-box">
                <div class="under-construction-plugin-box-logo-container">
                        <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/products/kingsumo-logo.svg'; ?>">
                </div>
                <a href="https://kingsumo.com/" target="_blank">KingSumo</a> Grow your email list through Viral Giveaways for WordPress
        </div>                
    </p>

    <div class="under-construction-plugin-box-form">
           <?php include plugin_dir_path( __FILE__ ).'appsumo-capture-form.php'; ?>
    </div>
</div>
