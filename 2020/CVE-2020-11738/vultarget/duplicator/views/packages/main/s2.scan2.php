<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!-- ================================================================
SERVER  -->
<div class="details-title">
	<i class="fa fa-hdd-o"></i> <?php esc_html_e("Server", 'duplicator');	?>
	<div class="dup-more-details" title="<?php esc_attr_e('Show Diagnostics', 'duplicator');?>">
		<a href="?page=duplicator-tools&tab=diagnostics" target="_blank"><i class="fa fa-microchip"></i></a>
	</div>
</div>

<!-- ============
PHP SETTINGS -->
<div class="scan-item">

    <?php
    
    //TODO Login Need to go here

    $core_dir_included   = array();
    $core_files_included = array();
    //by default fault
    $core_dir_notice     = false;
    $core_file_notice    = false;

    if (!$Package->Archive->ExportOnlyDB && isset($_POST['filter-on']) && isset($_POST['filter-dirs'])) {

        //findout matched core directories
        $filter_dirs = explode(";", trim(sanitize_text_field(($_POST['filter-dirs']))));

        // clean possible blank spaces before and after the paths
        for ($i = 0; $i < count($filter_dirs); $i++) {
            $filter_dirs[$i] = trim($filter_dirs[$i]);
            $filter_dirs[$i] = (substr($filter_dirs[$i], -1) == "/") ? substr($filter_dirs[$i],0, strlen($filter_dirs[$i])-1):$filter_dirs[$i] ;

        }
        $core_dir_included = array_intersect($filter_dirs,
            DUP_Util::getWPCoreDirs());
        if (count($core_dir_included)) $core_dir_notice   = true;


        //find out core files
        $filter_files = explode(";", trim($_POST['filter-files']));

        // clean possible blank spaces before and after the paths
        for ($i = 0; $i < count($filter_files); $i++) {
            $filter_files[$i] = trim($filter_files[$i]);
        }
        $core_files_included = array_intersect($filter_files,
            DUP_Util::getWPCoreFiles());
        if (count($core_files_included)) $core_file_notice    = true;
    }
    ?>
	<div class='title' onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Setup', 'duplicator');?></div>
		<div id="data-srv-php-all"></div>
	</div>
	<div class="info">
	<?php
		//WEB SERVER
		$web_servers = implode(', ', $GLOBALS['DUPLICATOR_SERVER_LIST']);
		echo '<span id="data-srv-php-websrv"></span>&nbsp;<b>' . esc_html__('Web Server', 'duplicator') . ":</b>&nbsp; '".esc_attr($_SERVER['SERVER_SOFTWARE'])."' <br/>";
		_e("Supported web servers: ", 'duplicator');
		echo "<i>".esc_html($web_servers)."</i>";

		//PHP VERSION
		echo '<hr size="1" /><span id="data-srv-php-version"></span>&nbsp;<b>' . esc_html__('PHP Version', 'duplicator') . "</b> <br/>";
		_e('The minimum PHP version supported by Duplicator is 5.2.9. It is highly recommended to use PHP 5.3+ for improved stability.  For international language support please use PHP 7.0+.', 'duplicator');
		
		//OPEN_BASEDIR
		$test = ini_get("open_basedir");
		$test = ($test) ? 'ON' : 'OFF';
		echo '<hr size="1" /><span id="data-srv-php-openbase"></span>&nbsp;<b>' . esc_html__('PHP Open Base Dir', 'duplicator') . ":</b>&nbsp; '{$test}' <br/>";
		_e('Issues might occur when [open_basedir] is enabled. Work with your server admin to disable this value in the php.ini file if you’re having issues building a package.', 'duplicator');
		echo "&nbsp;<i><a href='http://php.net/manual/en/ini.core.php#ini.open-basedir' target='_blank'>[" . esc_html__('details', 'duplicator') . "]</a></i><br/>";

		//MAX_EXECUTION_TIME
		$test = (@set_time_limit(0)) ? 0 : ini_get("max_execution_time");
		echo '<hr size="1" /><span id="data-srv-php-maxtime"></span>&nbsp;<b>' . esc_html__('PHP Max Execution Time', 'duplicator') . ":</b>&nbsp; '{$test}' <br/>";
		_e('Timeouts may occur for larger packages when [max_execution_time] time in the php.ini is too low.  A value of 0 (recommended) indicates that PHP has no time limits. '
			. 'An attempt is made to override this value if the server allows it.', 'duplicator');
		echo '<br/><br/>';
		_e('Note: Timeouts can also be set at the web server layer, so if the PHP max timeout passes and you still see a build timeout messages, then your web server could be killing '
			. 'the process.   If you are on a budget host and limited on processing time, consider using the database or file filters to shrink the size of your overall package.   '
			. 'However use caution as excluding the wrong resources can cause your install to not work properly.', 'duplicator');
		echo "&nbsp;<i><a href='http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time' target='_blank'>[" . esc_html__('details', 'duplicator')  . "]</a></i>";

		if ($zip_check != null) {
			echo '<br/><br/>';
			echo '<span style="font-weight:bold">';
			_e('Get faster builds with Duplicator Pro with access to shell_exec zip.', 'duplicator');
			echo '</span>';
			echo "&nbsp;<i><a href='https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_max_execution_time_warn&utm_campaign=duplicator_pro' target='_blank'>[" . esc_html__('details', 'duplicator') . "]</a></i>";
		}
	?>
	</div>
</div>

<!-- ============
WP SETTINGS -->
<div class="scan-item scan-item-last">


	<div class="title" onclick="Duplicator.Pack.toggleScanItem(this);">
		<div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('WordPress', 'duplicator');?></div>
		<div id="data-srv-wp-all"></div>
	</div>
	<div class="info">
		<?php
		//VERSION CHECK
		echo '<span id="data-srv-wp-version"></span>&nbsp;<b>' . esc_html__('WordPress Version', 'duplicator') . ":</b>&nbsp; '{$wp_version}' <br/>";
		printf(__('It is recommended to have a version of WordPress that is greater than %1$s.  Older version of WordPress can lead to migration issues and are a security risk. '
			. 'If possible please update your WordPress site to the latest version.', 'duplicator'), DUPLICATOR_SCAN_MIN_WP);

		//CORE FILES
		echo '<hr size="1" /><span id="data-srv-wp-core"></span>&nbsp;<b>' . esc_html__('Core Files', 'duplicator') . "</b> <br/>";
	
               
                $filter_text="";
                if($core_dir_notice) {
                    echo '<small id="data-srv-wp-core-missing-dirs">';
                       esc_html_e("The core WordPress paths below will <u>not</u> be included in the archive. These paths are required for WordPress to function!");
                       echo "<br/>";
                       foreach($core_dir_included as $core_dir) {
                           echo '&nbsp; &nbsp; <b><i class="fa fa-exclamation-circle scan-warn"></i>&nbsp;'. $core_dir . '</b><br/>';
					   }
                   echo '</small><br/>';
                   $filter_text="directories";
				}

                if($core_file_notice) {
                    echo '<small id="data-srv-wp-core-missing-dirs">';
                       esc_html_e("The core WordPress file below will <u>not</u> be included in the archive. This file is required for WordPress to function!");
                       echo "<br/>";
                       foreach($core_files_included as $core_file) {
                            echo '&nbsp; &nbsp; <b><i class="fa fa-exclamation-circle scan-warn"></i>&nbsp;'. $core_file . '</b><br/>';
					   }
                  echo '</small><br/>';
                  $filter_text .= (strlen($filter_text) > 0) ? " and file" : "files";

				}

                if(strlen($filter_text) > 0) {
					echo '<small>';
                    esc_html_e("Note: Please change the {$filter_text} filters if you wish to include the WordPress core files otherwise the data will have to be manually copied"
					. " to the new location for the site to function properly.", 'duplicator');
					echo '</small>';
				}


                if(!$core_dir_notice && !$core_file_notice):
                    esc_html_e("If the scanner is unable to locate the wp-config.php file in the root directory, then you will need to manually copy it to its new location. "
							. "This check will also look for core WordPress paths that should be included in the archive for WordPress to work correctly.", 'duplicator');
                endif;



		//CACHE DIR
		/*
		$cache_path = DUP_Util::safePath(WP_CONTENT_DIR) . '/cache';
		$cache_size = DUP_Util::byteSize(DUP_Util::getDirectorySize($cache_path));
		echo '<hr size="1" /><span id="data-srv-wp-cache"></span>&nbsp;<b>' . esc_html__('Cache Path', 'duplicator') . ":</b>&nbsp; '".esc_html($cache_path)."' (".esc_html($cache_size).") <br/>";
		_e("Cached data will lead to issues at install time and increases your archive size. Empty your cache directory before building the package by using  "
			. "your cache plugins clear cache feature.  Use caution if manually removing files the cache folder. The cache "
			. "size minimum threshold that triggers this warning is currently set at ", 'duplicator');
		echo esc_html(DUP_Util::byteSize(DUPLICATOR_SCAN_CACHESIZE)) . '.';
		*/

		//MU SITE
		if (is_multisite()) {
			echo '<hr size="1" /><span><div class="scan-warn"><i class="fa fa-exclamation-triangle fa-sm"></i></div></span>&nbsp;<b>' . esc_html__('Multisite: Unsupported', 'duplicator') . "</b> <br/>";
			esc_html_e('Duplicator does not support WordPress multisite migrations.  We recommend using Duplicator Pro which currently supports full multisite migrations and subsite to '
				. 'standalone site migrations.', 'duplicator');
			echo '<br/><br/>';

			esc_html_e('While it is not recommended you can still continue with the build of this package.  Please note that at install time additional manual custom configurations will '
				. 'need to be made to finalize this multisite migration.', 'duplicator');
			echo "&nbsp;<i><a href='https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_is_mu_warn&utm_campaign=duplicator_pro' target='_blank'>[" . esc_html__('upgrade to pro', 'duplicator') . "]</a></i>";
		} else {
			echo '<hr size="1" /><span><div class="scan-good"><i class="fa fa-check"></i></div></span>&nbsp;<b>' . esc_html__('Multisite: N/A', 'duplicator') . "</b> <br/>";
			esc_html_e('This is not a multisite install so duplication will proceed without issue.  Duplicator does not officially support multisite. However, Duplicator Pro supports '
				. 'duplication of a full multisite network and also has the ability to install a multisite subsite as a standalone site.', 'duplicator');
			echo "&nbsp;<i><a href='https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_is_mu_warn&utm_campaign=duplicator_pro' target='_blank'>[" . esc_html__('upgrade to pro', 'duplicator') . "]</a></i>";
		}
		?>
	</div>
</div>

<script>
(function($){

	//Ints the various server data responses from the scan results
	Duplicator.Pack.intServerData= function(data)
	{
		$('#data-srv-php-websrv').html(Duplicator.Pack.setScanStatus(data.SRV.PHP.websrv));
		$('#data-srv-php-openbase').html(Duplicator.Pack.setScanStatus(data.SRV.PHP.openbase));
		$('#data-srv-php-maxtime').html(Duplicator.Pack.setScanStatus(data.SRV.PHP.maxtime));
		$('#data-srv-php-version').html(Duplicator.Pack.setScanStatus(data.SRV.PHP.version));
		$('#data-srv-php-openssl').html(Duplicator.Pack.setScanStatus(data.SRV.PHP.openssl));
		$('#data-srv-php-all').html(Duplicator.Pack.setScanStatus(data.SRV.PHP.ALL));

		$('#data-srv-wp-version').html(Duplicator.Pack.setScanStatus(data.SRV.WP.version));
		$('#data-srv-wp-core').html(Duplicator.Pack.setScanStatus(data.SRV.WP.core));
		// $('#data-srv-wp-cache').html(Duplicator.Pack.setScanStatus(data.SRV.WP.cache));
		$('#data-srv-wp-all').html(Duplicator.Pack.setScanStatus(data.SRV.WP.ALL));
	}
	
})(jQuery);
</script>