<?php
/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and user.ini
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Crypt
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Step 3 functions
 * Singlethon
 */
final class DUPX_S3_Funcs
{
    const MODE_NORMAL = 1;
    // const MODE_CHUNK  = 2; reserved for PRO version
    const MODE_SKIP   = 3; // not implemented yet

    /**
     *
     * @var DUPX_S3_Funcs
     */
    protected static $instance = null;

    /**
     *
     * @var array
     */
    public $post = null;

    /**
     *
     * @var array
     */
    public $cTableParams = null;

    /**
     *
     * @var array
     */
    public $report = array();

    /**
     *
     * @var int
     */
    private $timeStart = null;

    /**
     *
     * @var database connection
     */
    private $dbh = null;

    /**
     *
     * @var bool
     */
    private $fullReport = false;

    private function __construct()
    {
        $this->timeStart = DUPX_U::getMicrotime();
    }

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * inizialize 3sFunc data
     */
    public function initData()
    {
        DUPX_Log::info('INIT S3 DATA', 2);
        // else init data from $_POST
        $this->setPostData();
        $this->setReplaceList();
        $this->initReport();
        $this->copyOriginalConfigFiles();
    }

    private function initReport()
    {
        $this->report = self::getInitReport();
    }

    public static function getInitReport()
    {
        return array(
            'pass' => 0,
            'chunk' => 0,
            'chunkPos' => array(),
            'progress_perc' => 0,
            'scan_tables' => 0,
            'scan_rows' => 0,
            'scan_cells' => 0,
            'updt_tables' => 0,
            'updt_rows' => 0,
            'updt_cells' => 0,
            'errsql' => array(),
            'errser' => array(),
            'errkey' => array(),
            'errsql_sum' => 0,
            'errser_sum' => 0,
            'errkey_sum' => 0,
            'profile_start' => '',
            'profile_end' => '',
            'time' => '',
            'err_all' => 0,
            'warn_all' => 0,
            'warnlist' => array()
        );
    }

    public function getJsonReport()
    {
        $this->report['warn_all'] = empty($this->report['warnlist']) ? 0 : count($this->report['warnlist']);

        if ($this->fullReport) {
            return array(
                'step1' => json_decode(urldecode($this->post['json'])),
                'step3' => $this->report
            );
        } else {
            return array(
                'step3' => $this->report
            );
        }
    }

    private static function logSectionHeader($title, $func, $line)
    {
        $log = "\n".'===================================='."\n".
            $title;
        if ($GLOBALS["LOGGING"] > 1) {
            $log .= ' [FUNC: '.$func.' L:'.$line.']';
        }
        $log .= "\n".
            '====================================';
        DUPX_Log::info($log);
    }

    private function setPostData()
    {
        // POST PARAMS
        // SEARCH AND SEPLACE SETTINGS
        $this->post = array();

        $this->post['blogname']   = isset($_POST['blogname']) ? htmlspecialchars($_POST['blogname'], ENT_QUOTES) : 'No Blog Title Set';
        $this->post['postguid']   = filter_input(INPUT_POST, 'postguid', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));
        $this->post['fullsearch'] = filter_input(INPUT_POST, 'fullsearch', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));

        $this->post['path_old'] = DUPX_U::isset_sanitize($_POST, 'path_old', array('default' => null, 'trim' => true));
        $this->post['path_new'] = DUPX_U::isset_sanitize($_POST, 'path_new', array('default' => null, 'trim' => true));

        $this->post['siteurl'] = DUPX_U::isset_sanitize($_POST, 'siteurl', array('default' => null, 'trim' => true));
        if (!is_null($this->post['siteurl'])) {
            $this->post['siteurl'] = rtrim($this->post['siteurl'], '/');
        }

        $this->post['url_old'] = DUPX_U::isset_sanitize($_POST, 'url_old', array('default' => null, 'trim' => true));
        if (!is_null($this->post['url_old'])) {
            $this->post['siteurl'] = rtrim($this->post['url_old'], '/');
        }

        $this->post['url_new'] = DUPX_U::isset_sanitize($_POST, 'url_new', array('default' => null, 'trim' => true));
        if (!is_null($this->post['url_new'])) {
            $this->post['siteurl'] = rtrim($this->post['url_new'], '/');
        }

        $this->post['tables']             = isset($_POST['tables']) && is_array($_POST['tables']) ? array_map('DUPX_U::sanitize_text_field', $_POST['tables']) : array();
        $this->post['maxSerializeStrlen'] = filter_input(INPUT_POST, DUPX_CTRL::NAME_MAX_SERIALIZE_STRLEN_IN_M, FILTER_VALIDATE_INT,
                array("options" => array('default' => DUPX_Constants::DEFAULT_MAX_STRLEN_SERIALIZED_CHECK_IN_M, 'min_range' => 0))) * 1000000;
        $this->post['replaceMail']        = filter_input(INPUT_POST, 'search_replace_email_domain', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));

        // DATABASE CONNECTION
        $this->post['dbhost']    = trim(filter_input(INPUT_POST, 'dbhost', FILTER_DEFAULT, array('options' => array('default' => ''))));
        $this->post['dbuser']    = trim(filter_input(INPUT_POST, 'dbuser', FILTER_DEFAULT, array('options' => array('default' => ''))));
        $this->post['dbname']    = trim(filter_input(INPUT_POST, 'dbname', FILTER_DEFAULT, array('options' => array('default' => ''))));
        $this->post['dbpass']    = trim(filter_input(INPUT_POST, 'dbpass', FILTER_DEFAULT, array('options' => array('default' => ''))));
        $this->post['dbcharset'] = DUPX_U::isset_sanitize($_POST, 'dbcharset', array('default' => ''));
        $this->post['dbcollate'] = DUPX_U::isset_sanitize($_POST, 'dbcollate', array('default' => ''));

        // NEW ADMIN USER
        $this->post['wp_username']   = DUPX_U::isset_sanitize($_POST, 'wp_username', array('default' => '', 'trim' => true));
        $this->post['wp_password']   = DUPX_U::isset_sanitize($_POST, 'wp_password', array('default' => '', 'trim' => true));
        $this->post['wp_mail']       = DUPX_U::isset_sanitize($_POST, 'wp_mail', array('default' => '', 'trim' => true));
        $this->post['wp_nickname']   = DUPX_U::isset_sanitize($_POST, 'wp_nickname', array('default' => '', 'trim' => true));
        $this->post['wp_first_name'] = DUPX_U::isset_sanitize($_POST, 'wp_first_name', array('default' => '', 'trim' => true));
        $this->post['wp_last_name']  = DUPX_U::isset_sanitize($_POST, 'wp_last_name', array('default' => '', 'trim' => true));

        // WP CONFIG SETTINGS
        $this->post['ssl_admin']  = filter_input(INPUT_POST, 'ssl_admin', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));
        $this->post['cache_wp']   = filter_input(INPUT_POST, 'cache_wp', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));
        $this->post['cache_path'] = filter_input(INPUT_POST, 'cache_path', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));

        // OTHER
        $this->post['exe_safe_mode'] = filter_input(INPUT_POST, 'exe_safe_mode', FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));
        $this->post['config_mode']   = DUPX_U::isset_sanitize($_POST, 'config_mode', array('default' => 'NEW'));
        $this->post['plugins']       = filter_input(INPUT_POST, 'plugins', FILTER_SANITIZE_STRING,
            array(
            'options' => array(
                'default' => array()
            ),
            'flags' => FILTER_REQUIRE_ARRAY,
        ));

        $this->post['json'] = filter_input(INPUT_POST, 'json', FILTER_DEFAULT, array('options' => array('default' => '{}')));
    }

    /**
     * get vaule post if  thepost isn't inizialized inizialize it
     * 
     * @param string $key
     * @return mixed
     */
    public function getPost($key = null)
    {
        if (is_null($this->post)) {
            $this->initData();
        }

        if (is_null($key)) {
            return $this->post;
        } else if (isset($this->post[$key])) {
            return $this->post[$key];
        } else {
            return null;
        }
    }

    /**
     * add table in tables list to scan in search and replace engine if isn't already in array
     * 
     * @param string $table
     */
    public function addTable($table)
    {
        if (empty($table)) {
            return;
        }

        // make sure post data is inizialized
        $this->getPost();
        if (!in_array($table, $this->post['tables'])) {
            $this->post['tables'][] = $table;
        }
    }

    /**
     * open db connection if is closed
     */
    private function dbConnection()
    {
        if (is_null($this->dbh)) {
            // make sure post data is inizialized
            $this->getPost();

            //MYSQL CONNECTION
            $this->dbh   = DUPX_DB::connect($this->post['dbhost'], $this->post['dbuser'], $this->post['dbpass'], $this->post['dbname']);
            $dbConnError = (mysqli_connect_error()) ? 'Error: '.mysqli_connect_error() : 'Unable to Connect';

            if (!$this->dbh) {
                $msg = "Unable to connect with the following parameters: <br/> <b>HOST:</b> {$post_db_host}<br/> <b>DATABASE:</b> {$post_db_name}<br/>";
                $msg .= "<b>Connection Error:</b> {$dbConnError}";
                DUPX_Log::error($msg);
            }

            $db_max_time = mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_TIME']);
            @mysqli_query($this->dbh, "SET wait_timeout = ".mysqli_real_escape_string($this->dbh, $db_max_time));

            $post_db_charset = $this->post['dbcharset'];
            $post_db_collate = $this->post['dbcollate'];
            DUPX_DB::setCharset($this->dbh, $post_db_charset, $post_db_collate);
        }
    }

    public function getDbConnection()
    {
        // make sure dbConnection is inizialized
        $this->dbConnection();
        return $this->dbh;
    }

    /**
     * close db connection if is open
     */
    public function closeDbConnection()
    {
        if (!is_null($this->dbh)) {
            mysqli_close($this->dbh);
            $this->dbh = null;
        }
    }

    public function initLog()
    {
        // make sure dbConnection is inizialized
        $this->dbConnection();

        $charsetServer = @mysqli_character_set_name($this->dbh);
        $charsetClient = @mysqli_character_set_name($this->dbh);

        //LOGGING
        $date = @date('h:i:s');
        $log  = "\n\n".
            "********************************************************************************\n".
            "DUPLICATOR PRO INSTALL-LOG\n".
            "STEP-3 START @ ".$date."\n".
            "NOTICE: Do NOT post to public sites or forums\n".
            "********************************************************************************\n".
            "CHARSET SERVER:\t".DUPX_Log::varToString($charsetServer)."\n".
            "CHARSET CLIENT:\t".DUPX_Log::varToString($charsetClient)."\n".
            "********************************************************************************\n".
            "OPTIONS:\n";

        $skipOpts = array('tables', 'plugins', 'dbpass', 'json', 'search', 'replace', 'mu_search', 'mu_replace', 'wp_password');
        foreach ($this->post as $key => $val) {
            if (in_array($key, $skipOpts)) {
                continue;
            }
            $log .= str_pad($key, 22, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($val)."\n";
        }
        $log .= "********************************************************************************\n";

        DUPX_Log::info($log);

        $POST_LOG = $this->post;
        unset($POST_LOG['tables']);
        unset($POST_LOG['plugins']);
        unset($POST_LOG['dbpass']);
        ksort($POST_LOG);

        //Detailed logging
        $log = "--------------------------------------\n";
        $log .= "POST DATA\n";
        $log .= "--------------------------------------\n";
        $log .= print_r($POST_LOG, true);
        DUPX_Log::info($log, DUPX_Log::LV_DEBUG);

        $log = "--------------------------------------\n";
        $log .= "TABLES TO SCAN\n";
        $log .= "--------------------------------------\n";
        $log .= (isset($this->post['tables']) && count($this->post['tables']) > 0) ? DUPX_Log::varToString($this->post['tables']) : 'No tables selected to update';
        $log .= "--------------------------------------\n";
        $log .= "KEEP PLUGINS ACTIVE\n";
        $log .= "--------------------------------------\n";
        $log .= (isset($this->post['plugins']) && count($this->post['plugins']) > 0) ? DUPX_Log::varToString($this->post['plugins']) : 'No plugins selected for activation';
        DUPX_Log::info($log, 2);
        DUPX_Log::flush();
    }

    /**
     *
     * @staticvar type $configTransformer
     * 
     * @return WPConfigTransformer
     */
    public function getWpConfigTransformer()
    {
        static $configTransformer = null;

        if (is_null($configTransformer)) {
            //@todo: integrate all logic into DUPX_WPConfig::updateVars
            if (!is_writable(DUPX_Package::getWpconfigArkPath())) {
                if (DupLiteSnapLibIOU::chmod(DUPX_Package::getWpconfigArkPath(), 0644)) {
                    DUPX_Log::info("File Permission Update: dup-wp-config-arc__[HASH].txt set to 0644");
                } else {
                    $err_log = "\nWARNING: Unable to update file permissions and write to dup-wp-config-arc__[HASH].txt.  ";
                    $err_log .= "Check that the wp-config.php is in the archive.zip and check with your host or administrator to enable PHP to write to the wp-config.php file.  ";
                    $err_log .= "If performing a 'Manual Extraction' please be sure to select the 'Manual Archive Extraction' option on step 1 under options.";
                    DUPX_Log::error($err_log);
                }
            }
            $configTransformer = new WPConfigTransformer(DUPX_Package::getWpconfigArkPath());
        }

        return $configTransformer;
    }

    /**
     *
     * @return string
     */
    public function copyOriginalConfigFiles()
    {
        $wpOrigPath = DUPX_Package::getOrigWpConfigPath();
        $wpArkPath  = DUPX_Package::getWpconfigArkPath();

        if (file_exists($wpOrigPath)) {
            if (!@unlink($wpOrigPath)) {
                DUPX_Log::info('Can\'t delete copy of WP Config orig file');
            }
        }

        if (!file_exists($wpArkPath)) {
            DUPX_Log::info('WP Config ark file don\' exists');
        } else {
            if (!@copy($wpArkPath, $wpOrigPath)) {
                $errors = error_get_last();
                DUPX_Log::info("COPY ERROR: ".$errors['type']."\n".$errors['message']);
            } else {
                echo DUPX_Log::info("Original WP Config file copied", 2);
            }
        }

        $htOrigPath = DUPX_Package::getOrigHtaccessPath();
        $htArkPath  = DUPX_Package::getHtaccessArkPath();

        if (file_exists($htOrigPath)) {
            if (!@unlink($htOrigPath)) {
                DUPX_Log::info('Can\'t delete copy of htaccess orig file');
            }
        }

        if (!file_exists($htArkPath)) {
            DUPX_Log::info('htaccess ark file don\' exists');
        } else {
            if (!@copy($htArkPath, $htOrigPath)) {
                $errors = error_get_last();
                DUPX_Log::info("COPY ERROR: ".$errors['type']."\n".$errors['message']);
            } else {
                echo DUPX_Log::info("htaccess file copied", 2);
            }
        }
    }

    /**
     * set replace list
     *
     * Auto inizialize function
     */
    public function setReplaceList()
    {
        self::logSectionHeader('SET SEARCH AND REPLACE LIST', __FUNCTION__, __LINE__);
        $this->setGlobalSearchAndReplaceList();
    }

    /**
     *
     * @return int MODE_NORAML
     */
    public function getEngineMode()
    {
        return self::MODE_NORMAL;
    }

    private function setGlobalSearchAndReplaceList()
    {
        $s_r_manager = DUPX_S_R_MANAGER::getInstance();

        // make sure dbConnection is inizialized
        $this->dbConnection();

        // DIRS PATHS
        $post_path_old = $this->post['path_old'];
        $post_path_new = $this->post['path_new'];
        $s_r_manager->addItem($post_path_old, $post_path_new, DUPX_S_R_ITEM::TYPE_PATH, 10);

        // URLS
        // url from _POST
        $old_urls_list = array($this->post['url_old']);
        $post_url_new  = $this->post['url_new'];
        $at_new_domain = '@'.DUPX_U::getDomain($post_url_new);

        try {
            $confTransformer = $this->getWpConfigTransformer();

            // urls from wp-config
            if (!is_null($confTransformer)) {
                if ($confTransformer->exists('constant', 'WP_HOME')) {
                    $old_urls_list[] = $confTransformer->get_value('constant', 'WP_HOME');
                }

                if ($confTransformer->exists('constant', 'WP_SITEURL')) {
                    $old_urls_list[] = $confTransformer->get_value('constant', 'WP_SITEURL');
                }
            }

            // urls from db
            $dbUrls = mysqli_query($this->dbh, 'SELECT * FROM `'.mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix).'options` where option_name IN (\'siteurl\',\'home\')');
            if ($dbUrls instanceof mysqli_result) {
                while ($row = $dbUrls->fetch_object()) {
                    $old_urls_list[] = $row->option_value;
                }
            } else {
                DUPX_Log::info('DB ERROR: '.mysqli_error($this->dbh));
            }
        } catch (Exception $e) {
            DUPX_Log::info('CONTINUE EXCEPTION: '.$e->getMessage());
            DUPX_Log::info('TRACE:');
            DUPX_Log::info($e->getTraceAsString());
        }

        foreach (array_unique($old_urls_list) as $old_url) {
            $s_r_manager->addItem($old_url, $post_url_new, DUPX_S_R_ITEM::TYPE_URL_NORMALIZE_DOMAIN, 10);

            // Replace email address (xyz@oldomain.com to xyz@newdomain.com).
            if ($this->post['replaceMail']) {
                $at_old_domain = '@'.DUPX_U::getDomain($old_url);
                $s_r_manager->addItem($at_old_domain, $at_new_domain, DUPX_S_R_ITEM::TYPE_STRING, 20);
            }
        }
    }

    public function runSearchAndReplace()
    {
        self::logSectionHeader('RUN SEARCH AND REPLACE', __FUNCTION__, __LINE__);

        // make sure post data is inizialized
        $this->getPost();

        DUPX_UpdateEngine::load($this->post['tables']);
        DUPX_UpdateEngine::logStats();
        DUPX_UpdateEngine::logErrors();
    }

    public function removeMaincenanceMode()
    {
        self::logSectionHeader('REMOVE MAINTENANCE MODE', __FUNCTION__, __LINE__);
        // make sure post data is inizialized
        $this->getPost();


        if (isset($this->post['remove_redundant']) && $this->post['remove_redundant']) {
            if ($GLOBALS['DUPX_STATE']->mode == DUPX_InstallerMode::OverwriteInstall) {
                DUPX_U::maintenanceMode(false, $GLOBALS['DUPX_ROOT']);
            }
        }
    }

    public function removeLicenseKey()
    {
        self::logSectionHeader('REMOVE LICENSE KEY', __FUNCTION__, __LINE__);
        // make sure dbConnection is inizialized
        $this->dbConnection();

        if (isset($GLOBALS['DUPX_AC']->brand) && isset($GLOBALS['DUPX_AC']->brand->enabled) && $GLOBALS['DUPX_AC']->brand->enabled) {
            $license_check = mysqli_query($this->dbh,
                "SELECT COUNT(1) AS count FROM `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` WHERE `option_name` LIKE 'duplicator_pro_license_key' ");
            $license_row   = mysqli_fetch_row($license_check);
            $license_count = is_null($license_row) ? 0 : $license_row[0];
            if ($license_count > 0) {
                mysqli_query($this->dbh,
                    "UPDATE `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET `option_value` = '' WHERE `option_name` LIKE 'duplicator_pro_license_key'");
            }
        }
    }

    public function createNewAdminUser()
    {
        self::logSectionHeader('CREATE NEW ADMIN USER', __FUNCTION__, __LINE__);
        // make sure dbConnection is inizialized
        $this->dbConnection();

        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (strlen($this->post['wp_username']) >= 4 && strlen($this->post['wp_password']) >= 6) {
            $wp_username   = mysqli_real_escape_string($this->dbh, $this->post['wp_username']);
            $newuser_check = mysqli_query($this->dbh,
                "SELECT COUNT(*) AS count FROM `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."users` WHERE user_login = '{$wp_username}' ");
            $newuser_row   = mysqli_fetch_row($newuser_check);
            $newuser_count = is_null($newuser_row) ? 0 : $newuser_row[0];

            if ($newuser_count == 0) {

                $newuser_datetime = @date("Y-m-d H:i:s");
                $newuser_datetime = mysqli_real_escape_string($this->dbh, $newuser_datetime);
                $newuser_security = mysqli_real_escape_string($this->dbh, 'a:1:{s:13:"administrator";b:1;}');

                $post_wp_username = $this->post['wp_username'];
                $post_wp_password = $this->post['wp_password'];

                $post_wp_mail     = $this->post['wp_mail'];
                $post_wp_nickname = $this->post['wp_nickname'];
                if (empty($post_wp_nickname)) {
                    $post_wp_nickname = $post_wp_username;
                }
                $post_wp_first_name = $this->post['wp_first_name'];
                $post_wp_last_name  = $this->post['wp_last_name'];

                $wp_username   = mysqli_real_escape_string($this->dbh, $post_wp_username);
                $wp_password   = mysqli_real_escape_string($this->dbh, $post_wp_password);
                $wp_mail       = mysqli_real_escape_string($this->dbh, $post_wp_mail);
                $wp_nickname   = mysqli_real_escape_string($this->dbh, $post_wp_nickname);
                $wp_first_name = mysqli_real_escape_string($this->dbh, $post_wp_first_name);
                $wp_last_name  = mysqli_real_escape_string($this->dbh, $post_wp_last_name);

                $newuser1 = @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."users`
                        (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_registered`, `user_activation_key`, `user_status`, `display_name`)
                        VALUES ('{$wp_username}', MD5('{$wp_password}'), '{$wp_username}', '{$wp_mail}', '{$newuser_datetime}', '', '0', '{$wp_username}')");

                $newuser1_insert_id = intval(mysqli_insert_id($this->dbh));

                $newuser2 = @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta`
                        (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', '".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."capabilities', '{$newuser_security}')");

                $newuser3 = @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta`
                        (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', '".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."user_level', '10')");

                //Misc Meta-Data Settings:
                @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'rich_editing', 'true')");
                @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'admin_color',  'fresh')");
                @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'nickname', '{$wp_nickname}')");
                @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'first_name', '{$wp_first_name}')");
                @mysqli_query($this->dbh,
                        "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'last_name', '{$wp_last_name}')");

                DUPX_Log::info("\nNEW WP-ADMIN USER:");
                if ($newuser1 && $newuser2 && $newuser3) {
                    DUPX_Log::info("- New username '{$this->post['wp_username']}' was created successfully allong with MU usermeta.");
                } elseif ($newuser1) {
                    DUPX_Log::info("- New username '{$this->post['wp_username']}' was created successfully.");
                } else {
                    $newuser_warnmsg            = "- Failed to create the user '{$this->post['wp_username']}' \n ";
                    $this->report['warnlist'][] = $newuser_warnmsg;

                    $nManager->addFinalReportNotice(array(
                        'shortMsg' => 'New admin user create error',
                        'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsg' => $newuser_warnmsg,
                        'sections' => 'general'
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'new-user-create-error');

                    DUPX_Log::info($newuser_warnmsg);
                }
            } else {
                $newuser_warnmsg            = "\nNEW WP-ADMIN USER:\n - Username '{$this->post['wp_username']}' already exists in the database.  Unable to create new account.\n";
                $this->report['warnlist'][] = $newuser_warnmsg;

                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'New admin user create error',
                    'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg' => $newuser_warnmsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                    'sections' => 'general'
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'new-user-create-error');

                DUPX_Log::info($newuser_warnmsg);
            }
        }
    }

    public function configurationFileUpdate()
    {
        self::logSectionHeader('CONFIGURATION FILE UPDATES', __FUNCTION__, __LINE__);
        DUPX_Log::incIndent();
        // make sure post data is inizialized
        $this->getPost();
        $strReplaced = 0;

        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        try {
            if (file_exists(DUPX_Package::getWpconfigArkPath())) {
                $confTransformer = $this->getWpConfigTransformer();

                $mu_newDomain     = parse_url($this->getPost('url_new'));
                $mu_oldDomain     = parse_url($this->getPost('url_old'));
                $mu_newDomainHost = $mu_newDomain['host'];
                $mu_oldDomainHost = $mu_oldDomain['host'];
                $mu_newUrlPath    = parse_url($this->getPost('url_new'), PHP_URL_PATH);
                $mu_oldUrlPath    = parse_url($this->getPost('url_old'), PHP_URL_PATH);

                if (empty($mu_newUrlPath) || ($mu_newUrlPath == '/')) {
                    $mu_newUrlPath = '/';
                } else {
                    $mu_newUrlPath = rtrim($mu_newUrlPath, '/').'/';
                }

                if (empty($mu_oldUrlPath) || ($mu_oldUrlPath == '/')) {
                    $mu_oldUrlPath = '/';
                } else {
                    $mu_oldUrlPath = rtrim($mu_oldUrlPath, '/').'/';
                }

                if ($confTransformer->exists('constant', 'WP_HOME')) {
                    $confTransformer->update('constant', 'WP_HOME', $this->post['url_new'], array('normalize' => true, 'add' => false));
                    DUPX_Log::info('UPDATE WP_HOME '.DUPX_Log::varToString($this->post['url_new']));
                }
                if ($confTransformer->exists('constant', 'WP_SITEURL')) {
                    $confTransformer->update('constant', 'WP_SITEURL', $this->post['url_new'], array('normalize' => true, 'add' => false));
                    DUPX_Log::info('UPDATE WP_SITEURL '.DUPX_Log::varToString($this->post['url_new']));
                }
                if ($confTransformer->exists('constant', 'DOMAIN_CURRENT_SITE')) {
                    $confTransformer->update('constant', 'DOMAIN_CURRENT_SITE', $mu_newDomainHost, array('normalize' => true, 'add' => false));
                    DUPX_Log::info('UPDATE DOMAIN_CURRENT_SITE '.DUPX_Log::varToString($mu_newDomainHost));
                }
                if ($confTransformer->exists('constant', 'PATH_CURRENT_SITE')) {
                    $confTransformer->update('constant', 'PATH_CURRENT_SITE', $mu_newUrlPath, array('normalize' => true, 'add' => false));
                    DUPX_Log::info('UPDATE PATH_CURRENT_SITE '.DUPX_Log::varToString($mu_newUrlPath));
                }

                /**
                 * clean multisite settings for security reasons.
                 */
                if ($confTransformer->exists('constant', 'WP_ALLOW_MULTISITE')) {
                    $confTransformer->remove('constant', 'WP_ALLOW_MULTISITE');
                    DUPX_Log::info('REMOVED WP_ALLOW_MULTISITE');
                }
                if ($confTransformer->exists('constant', 'ALLOW_MULTISITE')) {
                    $confTransformer->update('constant', 'ALLOW_MULTISITE', 'false', array('add' => false, 'raw' => true, 'normalize' => true));
                    DUPX_Log::info('TRANSFORMER: ALLOW_MULTISITE constant value set to false in WP config file');
                }
                if ($confTransformer->exists('constant', 'MULTISITE')) {
                    $confTransformer->update('constant', 'MULTISITE', 'false', array('add' => false, 'raw' => true, 'normalize' => true));
                    DUPX_Log::info('TRANSFORMER: MULTISITE constant value set to false in WP config file');
                }
                if ($confTransformer->exists('constant', 'NOBLOGREDIRECT')) {
                    $confTransformer->update('constant', 'NOBLOGREDIRECT', 'false', array('add' => false, 'raw' => true, 'normalize' => true));
                    DUPX_Log::info('TRANSFORMER: NOBLOGREDIRECT constant value set to false in WP config file');
                }
                if ($confTransformer->exists('constant', 'SUBDOMAIN_INSTALL')) {
                    $confTransformer->remove('constant', 'SUBDOMAIN_INSTALL');
                    DUPX_Log::info('TRANSFORMER: SUBDOMAIN_INSTALL constant removed from WP config file');
                }
                if ($confTransformer->exists('constant', 'VHOST')) {
                    $confTransformer->remove('constant', 'VHOST');
                    DUPX_Log::info('TRANSFORMER: VHOST constant removed from WP config file');
                }
                if ($confTransformer->exists('constant', 'SUNRISE')) {
                    $confTransformer->remove('constant', 'SUNRISE');
                    DUPX_Log::info('TRANSFORMER: SUNRISE constant removed from WP config file');
                }


                $dbname = DUPX_U::getEscapedGenericString($this->post['dbname']);
                $dbuser = DUPX_U::getEscapedGenericString($this->post['dbuser']);
                $dbpass = DUPX_U::getEscapedGenericString($this->post['dbpass']);
                $dbhost = DUPX_U::getEscapedGenericString($this->post['dbhost']);

                $confTransformer->update('constant', 'DB_NAME', $dbname, array('raw' => true));
                DUPX_Log::info('UPDATE DB_NAME '.DUPX_Log::varToString($dbname));

                $confTransformer->update('constant', 'DB_USER', $dbuser, array('raw' => true));
                DUPX_Log::info('UPDATE DB_USER '.DUPX_Log::varToString($dbuser));

                $confTransformer->update('constant', 'DB_PASSWORD', $dbpass, array('raw' => true));
                DUPX_Log::info('UPDATE DB_PASSWORD '.DUPX_Log::varToString('** OBSCURED **'));

                $confTransformer->update('constant', 'DB_HOST', $dbhost, array('raw' => true));
                DUPX_Log::info('UPDATE DB_HOST '.DUPX_Log::varToString($dbhost));

                //SSL CHECKS
                if ($this->post['ssl_admin']) {
                    $confTransformer->update('constant', 'FORCE_SSL_ADMIN', 'true', array('raw' => true, 'normalize' => true));
                    DUPX_Log::info('UPDATE FORCE_SSL_ADMIN '.DUPX_Log::varToString(true));
                } else {
                    if ($confTransformer->exists('constant', 'FORCE_SSL_ADMIN')) {
                        $confTransformer->update('constant', 'FORCE_SSL_ADMIN', 'false', array('raw' => true, 'add' => false, 'normalize' => true));
                        DUPX_Log::info('UPDATE FORCE_SSL_ADMIN '.DUPX_Log::varToString(false));
                    }
                }

                // COOKIE_DOMAIN
                if ($confTransformer->exists('constant', 'COOKIE_DOMAIN')) {
                    $const_val     = $confTransformer->get_value('constant', 'COOKIE_DOMAIN');
                    $const_new_val = str_replace($mu_oldDomainHost, $mu_newDomainHost, $const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'COOKIE_DOMAIN', $const_new_val, array('normalize' => true));
                    }
                }

                if ($this->post['cache_wp']) {
                    $confTransformer->update('constant', 'WP_CACHE', 'true', array('raw' => true, 'normalize' => true));
                    DUPX_Log::info('UPDATE WP_CACHE '.DUPX_Log::varToString(true));
                } else {
                    if ($confTransformer->exists('constant', 'WP_CACHE')) {
                        $confTransformer->update('constant', 'WP_CACHE', 'false', array('raw' => true, 'add' => false, 'normalize' => true));
                        DUPX_Log::info('UPDATE WP_CACHE '.DUPX_Log::varToString(false));
                    }
                }

                // Cache: [ ] Keep Home Path
                if ($this->post['cache_path']) {
                    if ($confTransformer->exists('constant', 'WPCACHEHOME')) {
                        $wpcachehome_const_val     = $confTransformer->get_value('constant', 'WPCACHEHOME');
                        $wpcachehome_const_val     = DUPX_U::wp_normalize_path($wpcachehome_const_val);
                        $wpcachehome_new_const_val = str_replace($this->post['path_old'], $this->post['path_new'], $wpcachehome_const_val, $strReplaced);
                        if ($strReplaced > 0) {
                            $confTransformer->update('constant', 'WPCACHEHOME', $wpcachehome_new_const_val, array('normalize' => true));
                            DUPX_Log::info('UPDATE WPCACHEHOME '.DUPX_Log::varToString($wpcachehome_new_const_val));
                        }
                    }
                } else {
                    $confTransformer->remove('constant', 'WPCACHEHOME');
                    DUPX_Log::info('REMOVE WPCACHEHOME');
                }

                if ($GLOBALS['DUPX_AC']->is_outer_root_wp_content_dir) {
                    if (empty($GLOBALS['DUPX_AC']->wp_content_dir_base_name)) {
                        $ret = $confTransformer->remove('constant', 'WP_CONTENT_DIR');
                        DUPX_Log::info('REMOVE WP_CONTENT_DIR');
                        // sometimes WP_CONTENT_DIR const removal failed, so we need to update them
                        if (false === $ret) {
                            $wpContentDir = "dirname(__FILE__).'/wp-content'";
                            $confTransformer->update('constant', 'WP_CONTENT_DIR', $wpContentDir, array('raw' => true, 'normalize' => true));
                            DUPX_Log::info('UPDATE WP_CONTENT_DIR '.DUPX_Log::varToString($wpContentDir));
                        }
                    } else {
                        $wpContentDir = "dirname(__FILE__).'/".$GLOBALS['DUPX_AC']->wp_content_dir_base_name."'";
                        $confTransformer->update('constant', 'WP_CONTENT_DIR', $wpContentDir, array('raw' => true, 'normalize' => true));
                        DUPX_Log::info('UPDATE WP_CONTENT_DIR '.DUPX_Log::varToString($wpContentDir));
                    }
                } elseif ($confTransformer->exists('constant', 'WP_CONTENT_DIR')) {
                    $wp_content_dir_const_val = $confTransformer->get_value('constant', 'WP_CONTENT_DIR');
                    $wp_content_dir_const_val = DUPX_U::wp_normalize_path($wp_content_dir_const_val);
                    $new_path                 = str_replace($this->post['path_old'], $this->post['path_new'], $wp_content_dir_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WP_CONTENT_DIR', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WP_CONTENT_DIR '.DUPX_Log::varToString($new_path));
                    }
                }

                //WP_CONTENT_URL
                // '/' added to prevent word boundary with domains that have the same root path
                if ($GLOBALS['DUPX_AC']->is_outer_root_wp_content_dir) {
                    if (empty($GLOBALS['DUPX_AC']->wp_content_dir_base_name)) {
                        $ret = $confTransformer->remove('constant', 'WP_CONTENT_URL');
                        DUPX_Log::info('REMOVE WP_CONTENT_URL');
                        // sometimes WP_CONTENT_DIR const removal failed, so we need to update them
                        if (false === $ret) {
                            $new_url = $this->post['url_new'].'/wp-content';
                            $confTransformer->update('constant', 'WP_CONTENT_URL', $new_url, array('raw' => true, 'normalize' => true));
                            DUPX_Log::info('UPDATE WP_CONTENT_URL '.DUPX_Log::varToString($new_url));
                        }
                    } else {
                        $new_url = $this->post['url_new'].'/'.$GLOBALS['DUPX_AC']->wp_content_dir_base_name;
                        $confTransformer->update('constant', 'WP_CONTENT_URL', $new_url, array('normalize' => true));
                        DUPX_Log::info('UPDATE WP_CONTENT_URL '.DUPX_Log::varToString($new_url));
                    }
                } elseif ($confTransformer->exists('constant', 'WP_CONTENT_URL')) {
                    $wp_content_url_const_val = $confTransformer->get_value('constant', 'WP_CONTENT_URL');
                    $new_path                 = str_replace($this->post['url_old'].'/', $this->post['url_new'].'/', $wp_content_url_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WP_CONTENT_URL', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WP_CONTENT_URL '.DUPX_Log::varToString($new_path));
                    }
                }

                //WP_TEMP_DIR
                if ($confTransformer->exists('constant', 'WP_TEMP_DIR')) {
                    $wp_temp_dir_const_val = $confTransformer->get_value('constant', 'WP_TEMP_DIR');
                    $wp_temp_dir_const_val = DUPX_U::wp_normalize_path($wp_temp_dir_const_val);
                    $new_path              = str_replace($this->post['path_old'], $this->post['path_new'], $wp_temp_dir_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WP_TEMP_DIR', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WP_TEMP_DIR '.DUPX_Log::varToString($new_path));
                    }
                }

                // WP_PLUGIN_DIR
                if ($confTransformer->exists('constant', 'WP_PLUGIN_DIR')) {
                    $wp_plugin_dir_const_val = $confTransformer->get_value('constant', 'WP_PLUGIN_DIR');
                    $wp_plugin_dir_const_val = DUPX_U::wp_normalize_path($wp_plugin_dir_const_val);
                    $new_path                = str_replace($this->post['path_old'], $this->post['path_new'], $wp_plugin_dir_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WP_PLUGIN_DIR', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WP_PLUGIN_DIR '.DUPX_Log::varToString($new_path));
                    }
                }

                // WP_PLUGIN_URL
                if ($confTransformer->exists('constant', 'WP_PLUGIN_URL')) {
                    $wp_plugin_url_const_val = $confTransformer->get_value('constant', 'WP_PLUGIN_URL');
                    $new_path                = str_replace($this->post['url_old'].'/', $this->post['url_new'].'/', $wp_plugin_url_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WP_PLUGIN_URL', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WP_PLUGIN_URL '.DUPX_Log::varToString($new_path));
                    }
                }

                // WPMU_PLUGIN_DIR
                if ($confTransformer->exists('constant', 'WPMU_PLUGIN_DIR')) {
                    $wpmu_plugin_dir_const_val = $confTransformer->get_value('constant', 'WPMU_PLUGIN_DIR');
                    $wpmu_plugin_dir_const_val = DUPX_U::wp_normalize_path($wpmu_plugin_dir_const_val);
                    $new_path                  = str_replace($this->post['path_old'], $this->post['path_new'], $wpmu_plugin_dir_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WPMU_PLUGIN_DIR', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WPMU_PLUGIN_DIR '.DUPX_Log::varToString($new_path));
                    }
                }

                // WPMU_PLUGIN_URL
                if ($confTransformer->exists('constant', 'WPMU_PLUGIN_URL')) {
                    $wpmu_plugin_url_const_val = $confTransformer->get_value('constant', 'WPMU_PLUGIN_URL');
                    $new_path                  = str_replace($this->post['url_old'].'/', $this->post['url_new'].'/', $wpmu_plugin_url_const_val, $strReplaced);
                    if ($strReplaced > 0) {
                        $confTransformer->update('constant', 'WPMU_PLUGIN_URL', $new_path, array('normalize' => true));
                        DUPX_Log::info('UPDATE WPMU_PLUGIN_URL '.DUPX_Log::varToString($new_path));
                    }
                }
                DUPX_Log::info("\n*** UPDATED WP CONFIG FILE ***");
            } else {
                DUPX_Log::info("WP-CONFIG ARK FILE NOT FOUND");
                DUPX_Log::info("WP-CONFIG ARK FILE:\n - 'dup-wp-config-arc__[HASH].txt'");
                DUPX_Log::info("SKIP FILE UPDATES\n");

                $shortMsg = 'wp-config.php not found';
                $longMsg  = <<<LONGMSG
Error updating wp-config file.<br>
The installation is finished but check the wp-config.php file and manually update the incorrect values.
LONGMSG;
                /*    $nManager->addNextStepNotice(array(
                  'shortMsg' => $shortMsg,
                  'level' => DUPX_NOTICE_ITEM::CRITICAL,

                  ), DUPX_NOTICE_MANAGER::ADD_UNIQUE , 'wp-config-transformer-exception'); */
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => $shortMsg,
                    'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg' => $longMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections' => 'general'
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'wp-config-transformer-exception');
            }
        } catch (Exception $e) {
            $shortMsg = 'wp-config.php transformer:'.$e->getMessage();
            $longMsg  = <<<LONGMSG
Error updating wp-config file.<br>
The installation is finished but check the wp-config.php file and manually update the incorrect values.
LONGMSG;
            /*    $nManager->addNextStepNotice(array(
              'shortMsg' => $shortMsg,
              'level' => DUPX_NOTICE_ITEM::CRITICAL,

              ), DUPX_NOTICE_MANAGER::ADD_UNIQUE , 'wp-config-transformer-exception'); */
            $nManager->addFinalReportNotice(array(
                'shortMsg' => $shortMsg,
                'level' => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg' => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections' => 'general'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'wp-config-transformer-exception');

            DUPX_Log::info("WP-CONFIG TRANSFORMER EXCEPTION\n".$e->getTraceAsString());
        }
        DUPX_Log::resetIndent();
    }

    public function htaccessUpdate()
    {
        $this->getPost();
        self::logSectionHeader('HTACCESS UPDATE MODE: '.DUPX_LOG::varToString($this->post['config_mode']), __FUNCTION__, __LINE__);


        switch ($this->post['config_mode']) {
            case 'NEW':
                DUPX_ServerConfig::createNewConfigs();
                break;
            case 'RESTORE':
                DUPX_ServerConfig::renameOrigConfigs();
                DUPX_Log::info("\nWARNING: Retaining the original .htaccess or web.config files may cause");
                DUPX_Log::info("issues with the initial setup of your site.  If you run into issues with the install");
                DUPX_Log::info("process choose 'Create New' for the 'Config Files' options");
                break;
            case 'IGNORE':
                DUPX_Log::info("\nWARNING: Choosing the option to ignore the .htaccess, web.config and .user.ini files");
                DUPX_Log::info("can lead to install issues.  The 'Ignore All' option is designed for advanced users.");
                break;
        }
    }

    public function generalUpdateAndCleanup()
    {
        self::logSectionHeader('GENERAL UPDATES & CLEANUP', __FUNCTION__, __LINE__);
        // make sure dbConnection is inizialized
        $this->dbConnection();
        $this->deactivateIncompatiblePlugins();
        $blog_name   = mysqli_real_escape_string($this->dbh, $this->post['blogname']);
        
        /** FINAL UPDATES: Must happen after the global replace to prevent double pathing
          http://xyz.com/abc01 will become http://xyz.com/abc0101  with trailing data */
        mysqli_query($this->dbh,
            "UPDATE `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $blog_name)."' WHERE option_name = 'blogname' ");
        mysqli_query($this->dbh,
            "UPDATE `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $this->post['url_new'])."'  WHERE option_name = 'home' ");
        mysqli_query($this->dbh,
            "UPDATE `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $this->post['siteurl'])."'  WHERE option_name = 'siteurl' ");
        mysqli_query($this->dbh,
            "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` (option_value, option_name) VALUES('".mysqli_real_escape_string($this->dbh,
                $this->post['exe_safe_mode'])."','duplicator_exe_safe_mode')");
        //Reset the postguid data
        if ($this->post['postguid']) {
            mysqli_query($this->dbh,
                "UPDATE `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."posts` SET guid = REPLACE(guid, '".mysqli_real_escape_string($this->dbh, $this->post['url_new'])."', '".mysqli_real_escape_string($this->dbh,
                    $this->post['url_old'])."')");
            $update_guid = @mysqli_affected_rows($this->dbh) or 0;
            DUPX_Log::info("Reverted '{$update_guid}' post guid columns back to '{$this->post['url_old']}'");
        }
    }

    /**
     * Deactivate incompatible plugins
     *
     * @return void
     */
    private function deactivateIncompatiblePlugins() {
        self::logSectionHeader("DEACTIVATE PLUGINS CHECK", __FUNCTION__, __LINE__);
        // make sure post data is inizialized
        $this->getPost();
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        $plugin_list = array();
        $auto_deactivate_plugins = $this->getAutoDeactivatePlugins();
        $deactivated_plugins = array();
        $reactivate_plugins_after_installation = array();
        foreach ($this->post['plugins'] as $plugin_slug) {
            if (isset($auto_deactivate_plugins[$plugin_slug])) {
                DUPX_Log::info("deactivate ".$plugin_slug);
                $deactivated_plugins[] = $plugin_slug;
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => $auto_deactivate_plugins[$plugin_slug]['shortMsg'],
                    'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg' => $auto_deactivate_plugins[$plugin_slug]['longMsg'],
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections' => 'general'
                ));
                if ($auto_deactivate_plugins[$plugin_slug]['reactivate']) {
                    $reactivate_plugins_after_installation[$plugin_slug] = $auto_deactivate_plugins[$plugin_slug]['title'];
                }
            } else {
                $plugin_list[] = $plugin_slug;
            }
        }

        if (!empty($deactivated_plugins)) {
            DUPX_Log::info('Plugin(s) listed here are deactivated: '. implode(', ', $deactivated_plugins));
        }

        if (!empty($reactivate_plugins_after_installation)) {
            DUPX_Log::info('Plugin(s) reactivated after installation: '. implode(', ', $deactivated_plugins));
            $reactivate_plugins_after_installation_str = serialize($reactivate_plugins_after_installation);
            mysqli_query($this->dbh, "INSERT INTO `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` (option_value, option_name) VALUES('".mysqli_real_escape_string($this->dbh,
                $reactivate_plugins_after_installation_str)."','duplicator_reactivate_plugins_after_installation')");
        }
        
        // Start
        // Force Duplicator active so we the security cleanup will be available
        if (!in_array('duplicator/duplicator.php', $plugin_list)) {
            $plugin_list[] = 'duplicator/duplicator.php';
        }
        $serial_plugin_list = @serialize($plugin_list);
        // End
        
        mysqli_query($this->dbh,
            "UPDATE `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($this->dbh, $serial_plugin_list)."'  WHERE option_name = 'active_plugins' ");
    }

    /**
     * Get Automatic deactivation plugins lists
     * 
     * @return array key as plugin slug and val as plugin title
     */
    private function getAutoDeactivatePlugins() {
        $excludePlugins = array();
        
        if (!DUPX_U::is_ssl()) {
            DUPX_Log::info('Really Simple SSL [as Non-SSL installation] will be Deactivated, If It is activated', DUPX_Log::LV_HARD_DEBUG);
            $excludePlugins['really-simple-ssl/rlrsssl-really-simple-ssl.php'] = array(
                    'title' => "Really Simple SSL",
                    'shortMsg' => "Deactivated Plugin:  Really Simple SSL",
                    'longMsg' => "This plugin has been deactivated since this migration is going from SSL (HTTPS) to Non-SSL (HTTP).  This will allow you to login to your WordPress Admin.  "
								. " To reactivate the plugin please go to the admin plugin page.",
                    'reactivate' => false

                );
        }

        if ($GLOBALS['DUPX_AC']->url_old != $this->post['siteurl']) {
            DUPX_Log::info('Simple Google reCAPTCHA [as Package creation site URL and installation site URL are different] will be deactivated, If It is activated', DUPX_Log::LV_HARD_DEBUG);
            $excludePlugins['simple-google-recaptcha/simple-google-recaptcha.php'] = array(
                'title' => "Simple Google reCAPTCHA",
                'shortMsg' => "Deactivated Plugin:  Simple Google reCAPTCHA",
                'longMsg' => "It is deactivated because the Google Recaptcha required reCaptcha site key which is bound to the site's address. Your package site's address and installed site's address doesn't match. You can reactivate it from the installed site login panel after completion of the installation.<br>
                                <strong>Please do not forget to change the reCaptcha site key after activating it.</strong>",
                'reactivate' => false
            );
        }

        DUPX_Log::info('WPBakery Page Builder will be Deactivated, If It is activated', DUPX_Log::LV_HARD_DEBUG);
        $excludePlugins['js_composer/js_composer.php']  = array(
            'title' => 'WPBakery Page Builder',
            'shortMsg' => "Deactivated Plugin:  WPBakery Page Builder",
            'longMsg' => "This plugin is deactivated automatically, because it requires a reacivation to work properly.  "
						. "<b>Please reactivate from the WordPress admin panel after logging in.</b> This will re-enable your site's frontend.",
            'reactivate' => true
        );

        DUPX_Log::info('Deactivated plugins list here: '.DUPX_Log::varToString(array_keys($excludePlugins)));
        return $excludePlugins;
    }

    public function noticeTest()
    {
        self::logSectionHeader('NOTICES TEST', __FUNCTION__, __LINE__);
        // make sure dbConnection is inizialized
        $this->dbConnection();

        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        if (file_exists(DUPX_Package::getWpconfigArkPath())) {
            $wpconfig_ark_contents = file_get_contents(DUPX_Package::getWpconfigArkPath());
            $config_vars           = array('WPCACHEHOME', 'COOKIE_DOMAIN', 'WP_SITEURL', 'WP_HOME', 'WP_TEMP_DIR');
            $config_found          = DUPX_U::getListValues($config_vars, $wpconfig_ark_contents);

            //Files
            if (!empty($config_found)) {
                $msg                        = "WP-CONFIG NOTICE: The wp-config.php has following values set [".implode(", ", $config_found)."].  \n";
                $msg                        .= "Please validate these values are correct by opening the file and checking the values.\n";
                $msg                        .= "See the codex link for more details: https://codex.wordpress.org/Editing_wp-config.php";
                // old system
                $this->report['warnlist'][] = $msg;
                DUPX_Log::info($msg);

                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'wp-config notice',
                    'level' => DUPX_NOTICE_ITEM::NOTICE,
                    'longMsg' => $msg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                    'sections' => 'general'
                ));
            }

            //-- Finally, back up the old wp-config and rename the new one
            $wpconfig_path = "{$GLOBALS['DUPX_ROOT']}/wp-config.php";
            if (DUPX_Package::getWpconfigArkPath() !== $wpconfig_path) {
                if (copy(DUPX_Package::getWpconfigArkPath(), $wpconfig_path) === false) {
                    DUPX_LOG::info(
                        'COPY SOURCE: '.DUPX_LOG::varToString(DUPX_Package::getWpconfigArkPath())."\n".
                        "COPY DEST:".DUPX_LOG::varToString($wpconfig_path), DUPX_Log::LV_DEBUG);
                    DUPX_Log::error("ERROR: Unable to copy 'dup-wp-config-arc__[HASH].txt' to 'wp-config.php'.\n".
                        "Check server permissions for more details see FAQ: https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-055-q");
                }
            }
        } else {
            $msg                        = "WP-CONFIG NOTICE: <b>wp-config.php not found.</b><br><br>";
            $msg                        .= "No action on the wp-config was possible.<br>";
            $msg                        .= "Be sure to insert a properly modified wp-config for correct wordpress operation.";
            $this->report['warnlist'][] = $msg;

            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'wp-config not found',
                'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
                'longMsg' => $msg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections' => 'general'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'wp-config-not-found');

            DUPX_Log::info($msg);
        }

        //Database
        $result = @mysqli_query($this->dbh,
                "SELECT option_value FROM `".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` WHERE option_name IN ('upload_url_path','upload_path')");
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                if (strlen($row[0])) {
                    $msg = "MEDIA SETTINGS NOTICE: The table '".mysqli_real_escape_string($this->dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options' has at least one the following values ['upload_url_path','upload_path'] \n";
                    $msg .= "set please validate settings. These settings can be changed in the wp-admin by going to /wp-admin/options.php'";

                    $this->report['warnlist'][] = $msg;
                    DUPX_Log::info($msg);

                    $nManager->addFinalReportNotice(array(
                        'shortMsg' => 'Media settings notice',
                        'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg' => $msg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                        'sections' => 'general'
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'media-settings-notice');

                    break;
                }
            }
        }

        if (empty($this->report['warnlist'])) {
            DUPX_Log::info("No General Notices Found\n");
        }
    }

    public function cleanupTmpFiles()
    {
        self::logSectionHeader('CLEANUP TMP FILES', __FUNCTION__, __LINE__);
        // make sure post data is inizialized
        $this->getPost();

        //Cleanup any tmp files a developer may have forgotten about
        //Lets be proactive for the developer just in case
        $wpconfig_path_bak   = "{$GLOBALS['DUPX_ROOT']}/wp-config.bak";
        $wpconfig_path_old   = "{$GLOBALS['DUPX_ROOT']}/wp-config.old";
        $wpconfig_path_org   = "{$GLOBALS['DUPX_ROOT']}/wp-config.org";
        $wpconfig_path_orig  = "{$GLOBALS['DUPX_ROOT']}/wp-config.orig";
        $wpconfig_safe_check = array($wpconfig_path_bak, $wpconfig_path_old, $wpconfig_path_org, $wpconfig_path_orig);
        foreach ($wpconfig_safe_check as $file) {
            if (file_exists($file)) {
                $tmp_newfile = $file.uniqid('_');
                if (rename($file, $tmp_newfile) === false) {
                    DUPX_Log::info("WARNING: Unable to rename '{$file}' to '{$tmp_newfile}'");
                }
            }
        }
    }

    public function finalReportNotices()
    {
        self::logSectionHeader('FINAL REPORT NOTICES', __FUNCTION__, __LINE__);

        $this->wpConfigFinalReport();
        $this->htaccessFinalReport();
    }

    private function htaccessFinalReport()
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        $orig = file_get_contents(DUPX_Package::getOrigHtaccessPath());
        $new  = file_get_contents($GLOBALS['DUPX_ROOT'].'/.htaccess');

        $lightBoxContent = '<div class="row-cols-2">'.
            '<div class="col col-1"><b>Original .htaccess</b><pre>'.htmlspecialchars($orig).'</pre></div>'.
            '<div class="col col-2"><b>New .htaccess</b><pre>'.htmlspecialchars($new).'</pre></div>'.
            '</div>';
        $longMsg         = DUPX_U_Html::getLigthBox('.htaccess changes', 'HTACCESS COMPARE', $lightBoxContent, false);

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'htaccess changes',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => $longMsg,
            'sections' => 'changes',
            'open' => true,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'htaccess-changes');
    }

    private function wpConfigFinalReport()
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (($orig = file_get_contents(DUPX_Package::getOrigWpConfigPath())) === false) {
            $orig = 'Can read origin wp-config.php file';
        } else {
            $orig = $this->obscureWpConfig($orig);
        }

        if (($new = file_get_contents($GLOBALS['DUPX_ROOT'].'/wp-config.php')) === false) {
            $new = 'Can read wp-config.php file';
        } else {
            $new = $this->obscureWpConfig($new);
        }

        $lightBoxContent = '<div class="row-cols-2">'.
            '<div class="col col-1"><b>Original wp-config.php</b><pre>'.htmlspecialchars($orig).'</pre></div>'.
            '<div class="col col-2"><b>New wp-config.php</b><pre>'.htmlspecialchars($new).'</pre></div>'.
            '</div>';
        $longMsg         = DUPX_U_Html::getLigthBox('wp-config.php changes', 'WP-CONFIG.PHP COMPARE', $lightBoxContent, false);

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'wp-config.php changes',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => $longMsg,
            'sections' => 'changes',
            'open' => true,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'wp-config-changes');
    }

    private function obscureWpConfig($src)
    {
        $transformer = new WPConfigTransformerSrc($src);
        $obsKeys     = array(
            'DB_NAME',
            'DB_USER',
            'DB_HOST',
            'DB_PASSWORD',
            'AUTH_KEY',
            'SECURE_AUTH_KEY',
            'LOGGED_IN_KEY',
            'NONCE_KEY',
            'AUTH_SALT',
            'SECURE_AUTH_SALT',
            'LOGGED_IN_SALT',
            'NONCE_SALT');

        foreach ($obsKeys as $key) {
            if ($transformer->exists('constant', $key)) {
                $transformer->update('constant', $key, '**OBSCURED**');
            }
        }

        return $transformer->getSrc();
    }

    public function complete()
    {
        // make sure post data is inizialized
        $this->getPost();
        $this->closeDbConnection();

        $ajax3_sum = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->timeStart);
        DUPX_Log::info("\nSTEP-3 COMPLETE @ ".@date('h:i:s')." - RUNTIME: {$ajax3_sum} \n\n");

        $this->fullReport              = true;
        $this->report['pass']          = 1;
        $this->report['chunk']         = 0;
        $this->report['chunkPos']      = null;
        $this->report['progress_perc'] = 100;
        // error_reporting($ajax3_error_level);
    }

    public function error($message)
    {
        // make sure post data is inizialized
        $this->getPost();

        $this->closeDbConnection();

        $ajax3_sum = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->timeStart);
        DUPX_Log::info("\nSTEP-3 ERROR @ ".@date('h:i:s')." - RUNTIME: {$ajax3_sum} \n\n");

        $this->report['pass']          = -1;
        $this->report['chunk']         = 0;
        $this->report['chunkPos']      = null;
        $this->report['error_message'] = $message;
    }

    protected function __clone()
    {

    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}