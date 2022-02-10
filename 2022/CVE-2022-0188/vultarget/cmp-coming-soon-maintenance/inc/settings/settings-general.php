<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="table-wrapper general skip-preview-validation">
    <h3><?php _e('General Settings', 'cmp-coming-soon-maintenance');?></h3>
    <table class="general">
        <tbody>
        <tr>
            <th><?php _e('CMP Status', 'cmp-coming-soon-maintenance');?></th>
            <td>
                <fieldset>
                    <div class="toggle-wrapper">
                        <input type="checkbox"  name="cmp_status" id="cmp-status" class="toggle-checkbox" <?php checked( '1', $this->cmp_active() ); ?>>
                        <label for="cmp-status" class="toggle"><span class="toggle_handler"></span></label> 
                    </div>
                </fieldset>

                <fieldset class="cmp-status-pages"  style="display: <?php echo ( $this->cmp_active() == '1' ) ? 'block' : 'none';?>">
                    <label for="cmp-status-pages-0"<?php echo ($page_filter == '0') ? ' class="active"' : '';?>>
                        <input type="radio" name="cmp-status-pages" id="cmp-status-pages-0" value="0" <?php checked( '0', $page_filter ); ?>>
                        <?php _e('Whole Website', 'cmp-coming-soon-maintenance');?>
                    </label>

                    <label for="cmp-status-pages-1"<?php echo ($page_filter == '1') ? ' class="active"' : '';?>>
                        <input type="radio" name="cmp-status-pages" id="cmp-status-pages-1" value="1" <?php checked( '1', $page_filter ); ?>>
                        <?php _e('Homepage only', 'cmp-coming-soon-maintenance');?>
                    </label>

                    <a href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced"><div class="label<?php echo ($page_filter == '2') ? ' active' : '';?>">
                        <input type="radio" name="cmp-status-pages" id="cmp-status-pages-2" value="2" <?php checked( '2', $page_filter ); ?>>
                        <?php _e('Whitelist/Blacklist', 'cmp-coming-soon-maintenance');?>
                    </div></a>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th><?php _e('CMP Mode', 'cmp-coming-soon-maintenance');?></th>
            <td>
                <fieldset class="cmp-status switch<?php echo $this->cmp_mode() == 2 ? ' active' : '';?>">
                        <input type="radio" name="cmp-activate" value="2" <?php checked( '2', $this->cmp_mode() ); ?> <?php disabled( '0', $this->cmp_active() ); ?>>&nbsp;<?php _e('Coming Soon & Landing Page', 'cmp-coming-soon-maintenance');?><br>
                    <span class="info"><?php _e('Returns standard 200 HTTP OK response code to indexing robots. Set this option if you want to use our plugin as "Coming Soon" page.','cmp-coming-soon-maintenance')?></span>
                </fieldset>

                <fieldset class="cmp-status switch<?php echo $this->cmp_mode() == 1 ? ' active' : '';?>">
                        <input type="radio" name="cmp-activate" value="1" <?php checked( '1', $this->cmp_mode() ); ?> <?php disabled( '0', $this->cmp_active() ); ?>>&nbsp;<?php _e('Maintenance Mode', 'cmp-coming-soon-maintenance');?><br>
                    <span class="info"><?php _e('Returns 503 HTTP Service unavailable code to indexing robots. Set this option if your site is down due to maintanance and you want to display Maintanance page.','cmp-coming-soon-maintenance')?></span>
                </fieldset>

                <fieldset class="cmp-status switch<?php echo $this->cmp_mode() == 3 ? ' active' : '';?>">
                    <input type="radio" name="cmp-activate" value="3" <?php checked( '3', $this->cmp_mode() ); ?> <?php disabled( '0', $this->cmp_active() ); ?>>&nbsp;<?php _e('Redirect Mode', 'cmp-coming-soon-maintenance');?><br>
                    <span class="info redirect"><?php _e('Choose Redirect Mode if you want to redirect your website to another URL.','cmp-coming-soon-maintenance')?></span>
                    <div class="redirect-inputs" <?php echo  $this->cmp_mode() == 3  ? 'style="display: block"' : 'style="display: none"';?>>
                        <input type="text" id="niteoCS_URL_redirect" name="niteoCS_URL_redirect" value="<?php echo esc_url( $niteoCS_URL_redirect ); ?>" class="regular-text code"><br> 
                        <label for="niteoCS_redirect_time"><?php _e('Delay Time in Seconds', 'cmp-coming-soon-maintenance');?></label>
                        <input type="text" id="niteoCS_redirect_time" name="niteoCS_redirect_time" value="<?php echo esc_attr( $niteoCS_redirect_time ); ?>" class="regular-text code"><br> 
                    </div>						 
                </fieldset>
            </td>
        </tr>

        <?php echo $this->render_settings->submit(); ?>

        </tbody>
    </table>

</div>