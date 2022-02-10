<?php
/**
 * Class DownloadStats
 */

namespace WPDM\__;

global $userDownloadCount;
$userDownloadCount = [];

class DownloadStats
{

    private $dbTable;


    function __construct()
    {
        global $wpdb;
        $this->dbTable = "{$wpdb->prefix}ahm_download_stats";
    }

    /**
     * @param $pid
     * @param $filename
     * @param $oid
     */
    function add($pid, $filename, $oid = null){
        global $wpdb, $current_user;

        //Handle downloads from email lock
        if(wpdm_query_var('subscriber' )){
            $subscriber = Crypt::decrypt(wpdm_query_var('subscriber' ));
            $wpdb->update("{$wpdb->prefix}ahm_emails", ['request_status' => 1], ['id' => $subscriber]);
        }

        $uid = get_current_user_id();
        $ip = (get_option('__wpdm_noip') == 0) ? wpdm_get_client_ip() : "";
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $hash = "uniq_".md5($pid.$uid.date("Y-m-d-h-i").wpdm_get_client_ip());
        if((int)Session::get($hash) === 1 || wpdm_query_var('nostat', ['validate' => 'int']) === 1) return;
        Session::set($hash, 1);
        $version = get_post_meta($pid, '__wpdm_version', true);
        $wpdb->insert("{$this->dbTable}", array('pid' => (int)$pid, 'uid' => (int)$uid, 'oid' => $oid, 'year' => date("Y"), 'month' => date("m"), 'day' => date("d"), 'timestamp' => time(), 'ip' => "$ip", 'filename' => $filename, 'agent' => $agent, 'version' => $version));
        update_post_meta($pid, '__wpdm_download_count', (int)get_post_meta($pid, '__wpdm_download_count', true) + 1);

        $this->updateUserDownloadCount($pid);

        if (is_user_logged_in()) {
            $index = $current_user->ID;
        } else {
            $index = str_replace(".", "_", $ip);
            if ($index == '') $index = uniqid();
        }

        if ($ip == '') $ip = $index;
        Session::set('downloaded_' . $pid, $ip);
    }

    /**
     * Get user download count for the given package
     * @param $packageID
     * @param null $userID
     * @return int|null
     */
    function userDownloadCount($packageID, $userID = null)
    {
        global $wpdb, $userDownloadCount;
        $packageID = (int)$packageID;

        if(!$userID) {
            if(is_user_logged_in())
                $userID = get_current_user_id();
            else {
                $userID = wpdm_get_client_ip();
            }
        }
        $userID = esc_sql($userID);

        $piduid = $packageID."_".$userID;

        if(isset($userDownloadCount[$piduid])) return $userDownloadCount[$piduid];
        $sql = "select download_count from {$wpdb->prefix}ahm_user_download_counts  WHERE user = '{$userID}' and package_id = '{$packageID}'";
        $download_count = (int)$wpdb->get_var($sql);
        $userDownloadCount[$piduid] = $download_count;
        return $download_count;
    }

    /**
     * Reset user download count for the given package
     * @param $packageID
     * @param null $userID
     * @return bool|int
     */
    function resetUserDownloadCount($packageID, $userID = null)
    {
        global $wpdb;
        $packageID = (int)$packageID;
        $where['package_id'] = $packageID;
        if($userID !== 'all') {
            if (!$userID) {
                if (is_user_logged_in())
                    $userID = get_current_user_id();
                else {
                    $userID = wpdm_get_client_ip();
                }
            }
            $userID = esc_sql($userID);
            $where['user'] = $userID;
        }
        return $wpdb->update("{$wpdb->prefix}ahm_user_download_counts", ['donwload_count' => 0], $where);
    }

    /**
     * Update user download count for the given package
     * @param $packageID
     * @param null $userID
     */
    function updateUserDownloadCount($packageID, $userID = null)
    {
        global $wpdb;
        $packageID = (int)$packageID;

        if(!$userID) {
            if(is_user_logged_in())
                $userID = get_current_user_id();
            else {
                $userID = wpdm_get_client_ip();
            }
        }
        $userID = esc_sql($userID);
        $download_count = (int)$this->userDownloadCount($packageID, $userID);
        $download_count++;
        $found = $wpdb->get_var("select count(ID) from {$wpdb->prefix}ahm_user_download_counts where user = '$userID' and package_id = '$packageID'");
        if(!$found)
            $wpdb->insert("{$wpdb->prefix}ahm_user_download_counts", ['download_count' => $download_count, 'user' => $userID, 'package_id' => $packageID]);
        else
            $wpdb->update("{$wpdb->prefix}ahm_user_download_counts", ['download_count' => $download_count], ['user' => $userID, 'package_id' => $packageID]);
    }



    /**
     * @deprecated Use <strong>add</strong> method, WPDM()->downloadHistory->add($pid, $filename, $oid = null);
     * @param $pid
     * @param $uid
     * @param $oid
     * @param string $filename
     */
    function newStat($pid, $uid, $oid, $filename = "")
    {
        global $wpdb, $current_user;
        return true;

        //Deprecated
    }


}
