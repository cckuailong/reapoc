<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("Media Upload Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr scope="row">
                <th><label for="wmuPhraseConfirmDelete"><?php esc_html_e("Alert message before deleting attached file", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuPhraseConfirmDelete"]); ?>" name="wmuPhraseConfirmDelete" id="wmuPhraseConfirmDelete"/></td>
            </tr>            
            <tr scope="row">
                <th><label for="wmuPhraseNotAllowedFile"><?php esc_html_e("Message if one or more file types are not allowed", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuPhraseNotAllowedFile"]); ?>" name="wmuPhraseNotAllowedFile" id="wmuPhraseNotAllowedFile"/></td>
            </tr>
            <tr scope="row">
                <th><label for="wmuPhraseMaxFileCount"><?php esc_html_e("Message if attached more files than allowed", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuPhraseMaxFileCount"]); ?>" name="wmuPhraseMaxFileCount" id="wmuPhraseMaxFileCount"/></td>
            </tr>
            <tr scope="row">
                <th><label for="wmuPhraseMaxFileSize"><?php esc_html_e("Message if upload file size is bigger than allowed", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuPhraseMaxFileSize"]); ?>" name="wmuPhraseMaxFileSize" id="wmuPhraseMaxFileSize"/></td>
            </tr>
            <tr scope="row">
                <th><label for="wmuPhrasePostMaxSize"><?php esc_html_e("Message if post size is bigger than allowed", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuPhrasePostMaxSize"]); ?>" name="wmuPhrasePostMaxSize" id="wmuPhrasePostMaxSize"/></td>
            </tr>
            <tr scope="row">
                <th><label for="wmuAttachImage"><?php esc_html_e("Attach an image to this comment", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuAttachImage"]); ?>" name="wmuAttachImage" id="wmuAttachImage"/></td>
            </tr>
            <tr scope="row">
                <th><label for="wmuChangeImage"><?php esc_html_e("Change the attached image", "wpdiscuz"); ?></label></th>
                <td><input type="text" value="<?php echo esc_attr($this->phrases["wmuChangeImage"]); ?>" name="wmuChangeImage" id="wmuChangeImage"/></td>
            </tr>
            <?php do_action("wpdiscuz_mu_phrases"); ?>
        </tbody>
    </table>
</div>