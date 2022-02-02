<?php

if (!defined("ABSPATH")) {
    exit();
}

class WpdiscuzCache implements WpDiscuzConstants {

    public $gravatars;
    public $wpUploadsDir;
    public $doGravatarsCache;
    private $avBaseDir;
    private $currentTime;
    private $timeout;
    private $options;
    private $helper;
    private $dbManager;

    public function __construct($options, $helper, $dbManager) {
        $this->options = $options;
        $this->helper = $helper;
        $this->dbManager = $dbManager;
        $this->gravatars = [];
        $this->wpUploadsDir = wp_upload_dir();
        $this->avBaseDir = $this->wpUploadsDir["basedir"] . self::GRAVATARS_CACHE_DIR;
        $this->currentTime = current_time("timestamp");
        $this->timeout = $this->options->general["gravatarCacheTimeout"] * DAY_IN_SECONDS;
        $this->doGravatarsCache = $this->options->general["isGravatarCacheEnabled"] && $this->options->isFileFunctionsExists;
        if ($this->doGravatarsCache) {
            wp_mkdir_p($this->avBaseDir);
            add_filter("pre_get_avatar", [&$this, "preGetGravatar"], 10, 3);
            add_filter("get_avatar", [&$this, "getAvatar"], 10, 6);
            if ($this->options->general["gravatarCacheMethod"] == "runtime") {
                add_filter("get_avatar_url", [&$this, "gravatarsRunTime"], 10, 3);
            } else {
                add_filter("get_avatar_url", [&$this, "gravatarsCronJob"], 10, 3);
            }
        }
        add_action("admin_post_purgeExpiredGravatarsCaches", [&$this, "purgeExpiredGravatarsCaches"]);
        add_action("admin_post_purgeGravatarsCaches", [&$this, "purgeGravatarsCaches"]);
        add_action(self::GRAVATARS_CACHE_ADD_ACTION, [&$this, "cacheGravatars"]);
        add_action(self::GRAVATARS_CACHE_DELETE_ACTION, [&$this, "deleteGravatars"]);
    }

    public function preGetGravatar($avatar, $idOrEmail, $args) {
        return $this->getAvatarHtml($avatar, $idOrEmail, $args);
    }

    public function getAvatar($avatar, $idOrEmail, $size, $default, $alt, $args) {
        if (strpos($avatar, self::GRAVATARS_CACHE_DIR) === false) {
            $avatar = $this->getAvatarHtml($avatar, $idOrEmail, $args);
        }
        return $avatar;
    }

    private function getAvatarHtml($avatar, $idOrEmail, $args) {
        if ($idOrEmail && $args &&
                isset($args["wpdiscuz_gravatar_field"]) &&
                $args["wpdiscuz_gravatar_field"] != ""
        ) {
            $cacheFileUrl = is_ssl() ? str_replace("http://", "https://", $this->wpUploadsDir["baseurl"]) . self::GRAVATARS_CACHE_DIR : $this->wpUploadsDir["baseurl"] . self::GRAVATARS_CACHE_DIR;
            $md5FileName = $this->getMD5FieldName($args["wpdiscuz_gravatar_field"]);
            if ($md5FileName) {
                $fileNameHash = $md5FileName . ".gif";
                $fileDirHash = $this->avBaseDir . $fileNameHash;
                if (file_exists($fileDirHash)) {
                    $fileUrlHash = $cacheFileUrl . $fileNameHash;
                    $url = $fileUrlHash;
                    $url2x = $fileUrlHash;
                    $class = ["avatar", "avatar-" . (int) $args["size"], "photo"];
                    if ($args["force_default"]) {
                        $class[] = "avatar-default";
                    }

                    if ($args["class"]) {
                        if (is_array($args["class"])) {
                            $class = array_merge($class, $args["class"]);
                        } else {
                            $class[] = $args["class"];
                        }
                    }

                    $avatar = sprintf(
                            "<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>", esc_attr($args["alt"]), esc_url_raw($url), esc_attr("$url2x 2x"), esc_attr(join(" ", $class)), esc_attr((int) $args["height"]), esc_attr((int) $args["width"]), $args["extra_attr"]
                    );
                }
            }
        }
        return $avatar;
    }

    public function gravatarsRunTime($url, $idOrEmail, $args) {
        if ($url && $idOrEmail && $args &&
                isset($args["wpdiscuz_gravatar_field"]) &&
                isset($args["size"]) &&
                isset($args["wpdiscuz_gravatar_size"]) &&
                $args["wpdiscuz_gravatar_field"] != "" &&
                $args["size"] == $args["wpdiscuz_gravatar_size"] &&
                $fileData = @file_get_contents($url)
        ) {
            $md5FileName = $this->getMD5FieldName($args["wpdiscuz_gravatar_field"]);
            if ($md5FileName) {
                $fileNameHash = $md5FileName . ".gif";
                $cacheFile = $this->avBaseDir . $fileNameHash;
                if (@file_put_contents($cacheFile, $fileData)) {
                    $cacheFileUrl = is_ssl() ? str_replace("http://", "https://", $this->wpUploadsDir["baseurl"]) . self::GRAVATARS_CACHE_DIR : $this->wpUploadsDir["baseurl"] . self::GRAVATARS_CACHE_DIR;
                    $fileUrlHash = $cacheFileUrl . $fileNameHash;
                    $url = $fileUrlHash;
                    $this->gravatars[$md5FileName] = [
                        "user_id" => $args["wpdiscuz_gravatar_user_id"],
                        "user_email" => $args["wpdiscuz_gravatar_user_email"],
                        "url" => $url,
                        "hash" => $md5FileName,
                        "cached" => 1
                    ];
                }
            }
        }
        return $url;
    }

    public function gravatarsCronJob($url, $idOrEmail, $args) {
        if ($url && $idOrEmail && $args &&
                isset($args["wpdiscuz_gravatar_field"]) &&
                isset($args["size"]) &&
                isset($args["wpdiscuz_gravatar_size"]) &&
                $args["wpdiscuz_gravatar_field"] != "" &&
                $args["size"] == $args["wpdiscuz_gravatar_size"]
        ) {
            $md5FileName = $this->getMD5FieldName($args["wpdiscuz_gravatar_field"]);
            if ($md5FileName) {
                $this->gravatars[$md5FileName] = [
                    "user_id" => $args["wpdiscuz_gravatar_user_id"],
                    "user_email" => $args["wpdiscuz_gravatar_user_email"],
                    "url" => $url,
                    "hash" => $md5FileName,
                    "cached" => 0
                ];
            }
        }
        return $url;
    }

    public function cacheGravatars() {
        $gravatars = $this->dbManager->getGravatars();
        if ($gravatars) {
            $cachedIds = [];
            foreach ($gravatars as $k => $gravatar) {
                $id = $gravatar["id"];
                $url = $gravatar["url"];
                $hash = $gravatar["hash"];
                if ($fileData = @file_get_contents($url)) {
                    $cacheFile = $this->avBaseDir . $hash . ".gif";
                    if (@file_put_contents($cacheFile, $fileData)) {
                        $cachedIds[] = $id;
                    }
                }
            }
            $this->dbManager->updateGravatarsStatus($cachedIds);
        }
    }

    public function purgeExpiredGravatarsCaches() {
        if (current_user_can("manage_options") && isset($_GET["_wpnonce"]) && wp_verify_nonce($_GET["_wpnonce"], "purgeExpiredGravatarsCaches")) {
            $timeFrame = $this->options->general["gravatarCacheTimeout"] * DAY_IN_SECONDS;
            $expiredGravatars = $this->dbManager->getExpiredGravatars($timeFrame);
            if ($expiredGravatars) {
                $files = function_exists("scandir") ? scandir($this->avBaseDir) : false;
                if ($files) {
                    foreach ($files as $k => $file) {
                        if (in_array($file, $expiredGravatars)) {
                            @unlink($this->avBaseDir . $file);
                        }
                    }
                }
                $this->dbManager->deleteExpiredGravatars($timeFrame);
            }
        }
        wp_redirect(admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_tab=" . self::TAB_GENERAL));
    }

    public function purgeGravatarsCaches() {
        if (current_user_can("manage_options") && isset($_GET["_wpnonce"]) && wp_verify_nonce($_GET["_wpnonce"], "purgeGravatarsCaches")) {
            $this->deleteGravatars();
        }
        wp_redirect(admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_tab=" . self::TAB_GENERAL));
    }

    public function deleteGravatars() {
        $files = function_exists("scandir") ? scandir($this->avBaseDir) : false;
        if ($files && is_array($files)) {
            foreach ($files as $k => $file) {
                if ($file != "." && $file != ".." && $file != ".htaccess") {
                    @unlink($this->avBaseDir . $file);
                }
            }
        }
        $this->dbManager->deleteGravatars();
    }

    private function getMD5FieldName($md5Field) {
        $fieldName = "";
        if (is_object($md5Field)) {
            $fieldName = isset($md5Field->comment_author_email) ? md5($md5Field->comment_author_email) : "";
        } else {
            $fieldName = md5($md5Field);
        }
        return $fieldName;
    }

}
