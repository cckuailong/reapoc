<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>
<div class="dcform">
    <form method="post" action="">
        <p>
            <label>Use Alternative Server</label>
            <span class="field">
                <select name="uaf_server_url_type">
                    <option value="alternative" <?php echo $GLOBALS['uaf_user_settings']['uaf_server_url_type'] == "alternative"?'selected=selected':''; ?>>Yes</option>
                    <option value="default" <?php echo $GLOBALS['uaf_user_settings']['uaf_server_url_type'] == "default"?'selected=selected':''; ?>>No</option>
                </select>
                ( When you are unable to upload the font using both Default Js and PHP Uploader or verify API key. )
            </span>
        </p>

        <p>
            <label>Use PHP Uploader</label>
            <span class="field">
                <select name="uaf_uploader_type">
                    <option value="php" <?php echo $GLOBALS['uaf_user_settings']['uaf_uploader_type'] == "php"?'selected=selected':''; ?>>Yes</option>
                    <option value="js" <?php echo $GLOBALS['uaf_user_settings']['uaf_uploader_type'] == "js"?'selected=selected':''; ?>>No</option>
                </select>
                ( Only if default javascript uploader doesn't work. <em>Need PHP Curl. </em> )
            </span>
        </p>

        <p>
            <label>Use Absolute Font Path</label>
            <span class="field">
                <select name="uaf_use_absolute_font_path">
                    <option value="1" <?php echo $GLOBALS['uaf_user_settings']['uaf_use_absolute_font_path'] == "1"?'selected=selected':''; ?>>Yes</option>
                    <option value="0" <?php echo $GLOBALS['uaf_user_settings']['uaf_use_absolute_font_path'] == "0"?'selected=selected':''; ?>>No</option>
                </select>
                ( Select No if you want to use relative font path. )
            </span>
        </p>

        <p>
            <label>Disable Font List in Wordpress Editor</label>
            <span class="field">
                <select name="uaf_disbale_editor_font_list">
                    <option value="1" <?php echo $GLOBALS['uaf_user_settings']['uaf_disbale_editor_font_list'] == "1"?'selected=selected':''; ?>>Yes</option>
                    <option value="0" <?php echo $GLOBALS['uaf_user_settings']['uaf_disbale_editor_font_list'] == "0"?'selected=selected':''; ?>>No</option>
                </select>
                ( When you have conflict with wordpress editor. )
            </span>
        </p>

        <p>
            <label>Enable Multi Language Support</label>
            <span class="field">
                <select name="uaf_enable_multi_lang_support">
                    <option value="1" <?php echo $GLOBALS['uaf_user_settings']['uaf_enable_multi_lang_support'] == "1"?'selected=selected':''; ?>>Yes</option>
                    <option value="0" <?php echo $GLOBALS['uaf_user_settings']['uaf_enable_multi_lang_support'] == "0"?'selected=selected':''; ?>>No</option>
                </select>
                ( When you are using multi language and need to set different font based on language. Currently Supported : WPML & Polylang )
            </span>
        </p>

        <p>
            <label>Font Display Property</label>
            <span class="field">
                <select name="uaf_font_display_property">
                    <option value="auto" <?php echo $GLOBALS['uaf_user_settings']['uaf_font_display_property'] == "auto"?'selected=selected':''; ?>>auto</option>
                    <option value="block" <?php echo $GLOBALS['uaf_user_settings']['uaf_font_display_property'] == "block"?'selected=selected':''; ?>>block</option>
                    <option value="swap" <?php echo $GLOBALS['uaf_user_settings']['uaf_font_display_property'] == "swap"?'selected=selected':''; ?>>swap</option>
                    <option value="fallback" <?php echo $GLOBALS['uaf_user_settings']['uaf_font_display_property'] == "fallback"?'selected=selected':''; ?>>fallback</option>
                </select>
            </span>
        </p>
        <p> 
            <label>&nbsp;</label>
            <span class="field">
                <?php wp_nonce_field( 'uaf_save_settings', 'uaf_nonce' ); ?>
                <input type="submit" name="save-uaf-options" class="button-primary" value="Save Settings" />
            </span>
        </p>
    </form>
</div>