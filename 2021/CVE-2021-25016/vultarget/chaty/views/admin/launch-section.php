<?php if (!defined('ABSPATH')) { exit; } ?>
<section class="section" id="launch-section">
    <h1 class="section-title">
        <?php esc_attr_e('Launch it!', CHT_OPT);?>
    </h1>

    <div class="form-horizontal">
        <input type="hidden" name="cht_active" value="0"  >
        <div class="form-horizontal__item flex-center">
            <label class="form-horizontal__item-label"><?php esc_attr_e('Active', CHT_OPT);?>:</label>
            <div>
                <label class="switch">
                    <span class="switch__label"><?php esc_attr_e('Off', CHT_OPT);?></span>
                    <?php
                    $cht_active = get_option('cht_active');
                    if($cht_active === false) {
	                    $cht_active = 1;
                    }
                    ?>
                    <input data-gramm_editor="false" type="checkbox" name="cht_active" class="cht_active" value="1" <?php checked($cht_active, 1) ?>>
                    <span class="switch__styled"></span>
                    <span class="switch__label"><?php esc_attr_e('On', CHT_OPT);?></span>
                </label>
            </div>
        </div>
    </div>
    <input type="hidden" name="nonce" value="<?php esc_attr_e(wp_create_nonce("chaty_plugin_nonce")) ?>">
    <div class="text-center">
        <button class="btn-save">
            <?php esc_attr_e('Save Changes', CHT_OPT); ?>
        </button>
    </div>
</section>
<?php
$created_on = get_option('cht_created_on');
if($created_on === false) {
	$created_on = "";
}
if(get_option('cht_active') === false) {
	$created_on = date("Y-m-d");
	?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("#toplevel_page_chaty-app li").removeClass("current");
            jQuery("#toplevel_page_chaty-app li:eq(2)").addClass("current")
        });
    </script>
    <?php
}
?>
<input type="hidden" name="cht_created_on" value="<?php echo esc_attr($created_on) ?>" />
