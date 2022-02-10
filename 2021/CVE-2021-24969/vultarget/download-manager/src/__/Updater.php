<?php


namespace WPDM\__;


class Updater
{
    function __construct()
    {
        add_action('admin_footer', [$this, 'requestUpdateCheck']);
        add_action("wp_ajax_wpdm_check_update", [$this, 'checkUpdate']);
    }

    function getLatestVersions()
    {
        $latest = get_option('wpdm_latest', false);
        $latest_check = get_option('wpdm_latest_check');
        $time = time() - intval($latest_check);
        if(!$latest || $time > 86400) {
            $latest_v_url = 'https://wpdmcdn.s3-accelerate.amazonaws.com/versions.json';
            $latest = wpdm_remote_get($latest_v_url);
            update_option('wpdm_latest', $latest, false);
            update_option('wpdm_latest_check', time(), false);
        }
        $latest = json_decode($latest);
        $latest = (array)$latest;
        return $latest;
    }

    function checkUpdate()
    {

        if (!current_user_can(WPDM_ADMIN_CAP) || get_option('wpdm_update_notice') === 'disabled') die();

        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $latest = $this->getLatestVersions();
        $plugins = get_plugins();

        $page = isset($_REQUEST['page']) ? esc_attr($_REQUEST['page']) : '';
        $plugin_info_url = isset($_REQUEST['plugin_url']) ? $_REQUEST['plugin_url'] : 'https://www.wpdownloadmanager.com/purchases/';
        if (is_array($latest)) {
            foreach ($latest as $plugin_dir => $latestv) {
                if ($plugin_dir !== 'download-manager') {
                    if (!($page == 'plugins' || get_post_type() == 'wpdmpro')) die('');
                    $plugin_data = wpdm_plugin_data($plugin_dir);
                    if(!is_array($plugin_data) || !isset($plugin_data['Name'])) continue;
                    $plugin_name = $plugin_data['Name'];
                    $plugin_info_url = isset($plugin_data['PluginURI']) ? $plugin_data['PluginURI'] : '';
                    $active = is_plugin_active($plugin_data['plugin_index_file']) ? 'active' : '';
                    $current_version = isset($plugin_data['Version']) ? $plugin_data['Version'] : '0';
                    if (version_compare($current_version, $latestv, '<') == true) {
                        $trid = sanitize_title($plugin_name);
                        $plugin_update_url = admin_url('/edit.php?post_type=wpdmpro&page=settings&tab=plugin-update&plugin=' . $plugin_dir); //'https://www.wpdownloadmanager.com/purchases/?'; //
                        if ($trid != '') {
                            wpdm_plugin_update_email($plugin_name, $latestv, $plugin_update_url);
                            if ($page == 'plugins') {
                                echo <<<NOTICE
     <script type="text/javascript">
      jQuery(function(){
        jQuery('tr:data[data-slug={$trid}]').addClass('update').after('<tr class="plugin-update-tr {$active} update"><td colspan=3 class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>There is a new version of <strong>{$plugin_name}</strong> available. <a href="{$plugin_update_url}&v={$latestv}" style="margin-left:10px" target=_blank>Update now ( v{$latestv} )</a></p></div></td></tr>');
      });
      </script>
NOTICE;
                            } else {
                                echo <<<NOTICE
     <script type="text/javascript">
      jQuery(function(){
        jQuery('.wrap > h2').after('<div class="updated error" style="margin:10px 0px;padding:10px;border-left:2px solid #dd3d36;background: #ffffff"><div style="float:left;"><b style="color:#dd3d36;">Important!</b><br/>There is a new version of <u>{$plugin_name}</u> available.</div> <a style="border-radius:0; float:right;;color:#ffffff; background: #D54E21;padding:10px 15px;text-decoration: none;font-weight: bold;font-size: 9pt;letter-spacing:1px" href="{$plugin_update_url}&v={$latestv}"  target=_blank><i class="fa fa-sync"></i> update v{$latestv}</a><div style="clear:both"></div></div>');
         });
         </script>
NOTICE;
                            }
                        }
                    }
                }

            }
        }
        if (__::is_ajax())
            die('');
    }

    /**
     * Add js code in admin footer to sent update check request
     */
    function requestUpdateCheck()
    {

        global $pagenow;
        if (!current_user_can(WPDM_ADMIN_CAP)) return;

        if(!in_array($pagenow, array('plugins.php'))) return;
        $tmpvar = explode("?", basename($_SERVER['REQUEST_URI']));
        $page = array_shift($tmpvar);
        $page = explode(".", $page);
        $page = array_shift($page);


        $page = $page == 'plugins' ? $page : get_post_type();

        ?>
        <script type="text/javascript">
            jQuery(function () {
                console.log('Checking WPDM Version!');
                jQuery.post(ajaxurl, {
                    action: 'wpdm_check_update',
                    page: '<?php echo $page; ?>'
                }, function (res) {
                    jQuery('#wpfooter').after(res);
                });


            });
        </script>

        <?php
    }
}
