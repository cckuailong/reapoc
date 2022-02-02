<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
	//The help for both pro and lite are shared.  Pro is where the master lives.  Use the flag below to
    //indicate if this help lives in lite or pro
	//$pro_version = true;

$open_section = filter_input(INPUT_GET, 'open_section', FILTER_SANITIZE_STRING, array('options' => array('default' => '')));

?>
<div class="hdr-main">
    HELP
</div>
<!-- =========================================
HELP FORM -->
<div id="main-help">
<div class="help-online"><br/>
	<i class="far fa-file-alt fa-sm"></i> For complete help visit
	<a href="https://snapcreek.com/support/docs/" target="_blank">Duplicator Migration and Backup Online Help</a> <br/>
	<small>Features available only in Duplicator Pro are flagged with a <sup>pro</sup> tag.</small>
</div>

<?php
$sectionId = 'section-security';
$expandClass =  $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Installer Security</h2>
    <div class="content" >
        <a name="help-s1-init"></a>
        <div id="dup-help-installer" class="help-page">
            The installer security screen will allow for basic password protection on the installer. The password is set at package creation time.  The password
            input on this screen must be entered before proceeding with an install.   This setting is optional and can be turned on/off via the package creation screens.
            <br/><br/>

            If you do not recall the password then login to the site where the package was created and click the details of the package to view the original password.
            To validate the password just typed you can toggle the view by clicking on the lock icon.	For detail on how to override this setting visit the online FAQ for
            <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-030-q" target="_blankopen_section">more details</a>.

            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td class="col-opt">Locked</td>
                    <td>
                        "Locked" means a password is protecting each step of the installer.  This option is recommended on all installers
                        that are accessible via a public URL but not required.
                    </td>
                </tr>
                <tr>
                    <td class="col-opt">Unlocked</td>
                    <td>
                        "Unlocked" means that if your installer is on a public server that anyone can access it.  This is a less secure way to run your installer. If you are running the
                        installer very quickly then removing all the installer files, then the chances of exposing it is going to be low depending	on your sites access history.
                        <br/><br/>

                        While it is not required to	have a password set it is recommended.  If your URL has little to no traffic or has never been the target of an attack
                        then running the installer without a password is going to be relatively safe if ran quickly.  However, a password is always a good idea.  Also, it is
                        absolutely required and recommended to remove <u>all</u> installer files after installation is completed by logging into the WordPress admin and
                        following the Duplicator prompts.
                    </td>
                </tr>
            </table>
            <br/>
            Note: Even though the installer has a password protection feature, it should only be used for the short term while the installer is being used. All installer files should and
            must be removed after the install is completed.  Files should not to be left on the server for any long duration of time to prevent any security related issues.
        </div>
    </div>
</section>

<!-- ============================================
STEP 1
============================================== -->
<?php
$sectionId = 'section-step-1';
$expandClass =  $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">1</span> of 4: Deployment</h2>
    <div class="content" >
        <a class="help-target" name="help-s1"></a>
        <div id="dup-help-scanner" class="help-page">
            There are currently several modes that the installer can be in.  The mode will be shown at the top of each screen. Below is an overview of the various modes.

            <table class="help-opt">
            <tr>
                <th class="col-opt">Option</th>
                <th>Details</th>
            </tr>
            <tr>
                <td class="col-opt">Standard Install</td>
                <td>
                    This mode indicates that the installer and archive have been placed into an empty directory and the site is ready for a fresh/new redeployment.
                    This is the most common mode and the mode that has been around the longest.
                </td>
            </tr>
            <tr>
                <td class="col-opt">Standard Install <br/> Database Only</td>
                <td>
                    This mode indicates that the installer and archive were manually moved or transferred to a location and that only the Database will be installed
                    at this location.
                </td>
            </tr>
            <tr>
                <td class="col-opt">Overwrite Install <sup>pro</sup></td>
                <td>
                    This mode indicates that the installer was started in a location that contains an existing site.  With this mode <b>the existing site will be overwritten</b> with
                    the contents of the archive.zip/daf and the database.sql file.  This is an advanced option and users should be pre-paired to know that state of their database
                    and archive site files ahead of time.
                </td>
            </tr>
            <tr>
                <td class="col-opt">Overwrite Install <br/> Database Only <sup>pro</sup></td>
                <td>
                    This mode indicates that the installer was started in a location that contains an existing site.  With this mode <b>the existing site will be overwritten</b> with
                    the contents of the database.sql file.  This is an advanced option and users should be pre-paired to know that state of their database and site files ahead of time.
                </td>
            </tr>
            </table>
            <br/><br/>


            Step 1 of the installer is separated into four sections for pro and three for lite.  Below is an overview of each area:
            <br/><br/>

            <h3>Archive</h3>
            This is the archive file the installer must use in order to extract the web site files and database.   The 'Name' is a unique key that
            ties both the archive and installer together.   The installer needs the archive file name to match the 'Name' value exactly character for character in order
            for	this section to get a pass status.
            <br/><br/>
            If the archive name	is ever changed then it should be renamed back to the 'Name' value in order for the installer to properly identify it as part of a
            complete package.  Additional information such as the archive size and the package notes are mentioned in this section.
            <br/><br/>

            <h3>Validation</h3>
            This section shows the installers system requirements and notices.  All requirements must pass in order to proceed to Step 2.  Each requirement will show
            a <b class="dupx-pass">Pass</b>/<b class="dupx-fail">Fail</b> status.  Notices on the other hand are <u>not</u> required in order to continue with the install.
            <br/><br/>

            Notices are simply checks that will help you identify any possible issues that might occur.  If this section shows a
            <b class="dupx-pass">Good</b>/<b class="dupx-fail">Warn</b> for various checks. 	Click on the title link and	read the overview for how to solve the test.
            <br/><br/>

            <h3>Multisite <sup>pro</sup></h3>
            The multisite option allows users with a Pro Business or Gold license to perform additional multi-site tasks.  All licenses can backup & migrate standalone sites
            and full multisite networks. Multisite Plus+ (business and above) adds the  ability to install a subsite as a standalone site.
            <br/><br/>

            <h3>Options</h3>
            The options for step 1 can help better prepare your site should your server need additional settings beyond most general configuration.
            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td colspan="2" class="section">General Options</td>
                </tr>
                <tr>
                    <td class="col-opt">Extraction</td>
                    <td>
                        <b>Manual Archive Extraction</b><br/>
                        Set the Extraction value to "Manual Archive Extraction" when the archive file has already been manually extracted on the server.  This can be done through your hosts
                        control panel such as cPanel or by your host directly. This setting can be helpful if you have a large archive files or are having issues with the installer extracting
                        the file due to timeout issues.
                        <br/><br/>

                        <b>PHP ZipArchive</b><br/>
                        This extraction method will use the PHP <a href="http://php.net/manual/en/book.zip.php" target="_blank">ZipArchive</a> code to extract the archive zip file.
                        <br/><br/>

                        <b>Shell-Exec Unzip</b><br/>
                        This extraction method will use the PHP <a href="http://php.net/manual/en/function.shell-exec.php" target="_blank">shell_exec</a> to call the system unzip
                        command on the server.  This is the default mode that is used if its avail on the server.
                        <br/><br/>

                    </td>
                </tr>
                <tr>
                    <td class="col-opt">Permissions</td>
                    <td>
                        <b>All Files:</b> Check the 'All Files' check-box and enter in the desired <a href="http://php.net/manual/en/function.chmod.php" target="_blank">chmod command</a>
                        to recursively set the octal value on all the files being extracted. Typically this value is 644 on most servers and hosts.
                        <br/><br/>

                        <b>All Directories:</b> Check the 'All Directories' check-box and enter in the desired <a href="http://php.net/manual/en/function.chmod.php" target="_blank">chmod command</a>
                        to recursively set octal value on all the directories being extracted.  Typically this value is 755 on most servers and hosts.
                        <br/><br/>
                        <i>Note: These settings only apply to Linux operating systems</i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="section">Advanced Options</td>
                </tr>
                <tr>
                    <td class="col-opt">Safe Mode</td>
                    <td>
                        Safe mode is designed to configure the site with specific options at install time to help over come issues that may happen during the install were the site
                        is having issues.  These options should only be used if you run into issues after you have tried to run an install.
                        <br/><br/>
                        <b>Basic:</b> This safe mode option will disable all the plugins at install time.  When this option is set you will need to re-enable all plugins after the
                        install has full ran.
                        <br/><br/>

                        <b>Advanced:</b> This option applies all settings used in basic and will also de-activate and reactivate your theme when logging in for the first time.  This
                        options should be used only if the Basic option did not work.
                    </td>
                </tr>
                <tr>
                    <td class="col-opt">Config Files </td>
                    <td>
                        When dealing with configuration files (.htaccess, web.config and .user.ini) the installer can apply different modes:
                        <br/><br/>

                        <b>Create New:</b> This is the default recommended option which will create either a new .htaccess or web.config file.  The new file is streamlined to help
                        guarantee no conflicts are created during install.   The config files generated with this mode will be simple and basic.  The WordFence .user.ini file if
                        present will be removed.
                        <br/><br/>

                        <b>Restore Original:</b> This option simply renames the .htaccess__[HASH] or web.config.orig	files to .htaccess or web.config. The *.orig files come from the original
                        web server where the package was built.	Please note this option will cause issues with the install process if the configuration files are not properly setup to
                        handle the new server environment.  This is an	advanced option and should only be used if you know how to properly configure your web servers configuration.
                        <br/><br/>

                        <b>Ignore All:</b> This option simply does nothing.  No files are backed up, nothing is renamed or created.  This advanced option assumes you already have your
                        config files setup and know how they should behave in the new environment.  When the package is build it will always create a .htaccess__[HASH] or web.config.orig.
                        Since these files are already in the archive file they will show up when the archive is extracted.
                        <br/><br/>

                        <b>Additional Notes:</b>
                        Inside the archive.zip will be a copy of the original .htaccess (Apache) or the web.config (IIS) files that were setup with your packaged site.  They are both
                        renamed to .htaccess__[HASH] and web.config.orig.  When using either 'Create New' or 'Restore Original' any existing config files  will	be backed up with a .bak extension.
                        <i>None of these changes are made until Step 3 is completed, to avoid any issues the .htaccess might cause during the install</i>
                        <br/><br/>
                    </td>
                </tr>

                <tr>
                    <td class="col-opt">File Times</td>
                    <td>When the archive is extracted should it show the current date-time or keep the original time it had when it was built.  This setting will be applied to
                    all files and directories.</td>
                </tr>
                <tr>
                    <td class="col-opt">Logging</td>
                    <td>
                        The level of detail that will be sent to the log file (installer-log.txt).  The recommend setting for most installs should be 'Light'.
                        Note if you use Debug the amount of data written can be very large.  Debug is only recommended for support.
                    </td>
                </tr>

            </table>
            <br/><br/>

            <h3>Notices</h3>
            To proceed with the install users must check the checkbox labeled " I have read and accept all terms &amp; notices".   This means you accept the term of using the software
            and are aware of any notices.
        </div>
    </div>
</section>

<!-- ============================================
STEP 2
============================================== -->
<?php
$sectionId = 'section-step-2';
$expandClass =  $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">2</span> of 4: Install Database</h2>
    <div class="content" >
        <a class="help-target" name="help-s2"></a>
        <div id="dup-help-step1" class="help-page">

            <h3>Basic/cPanel:</h3>
            There are currently two options you can use to perform the database setup.  The "Basic" option requires knowledge about the existing server and on most hosts
            will require that the database be setup ahead of time.  The cPanel option is for hosts that support <a href="http://cpanel.com/" target="_blank">cPanel Software</a>.
            This option will automatically show you the existing databases and users on your cPanel server and allow you to create new databases directly
            from the installer.
            <br/><br/>

            <h3>cPanel Login <sup>pro</sup></h3>
            <i>The cPanel connectivity option is only available for Duplicator Pro.</i>
            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td class="col-opt">Host</td>
                    <td>This should be the primary domain account URL that is associated with your host.  Most hosts will require you to register a primary domain name.
                    This should be the URL that you place in the host field.  For example if your primary domain name is "mysite.com" then you would enter in
                    "https://mysite.com:2083".  The port 2038 is the common	port number that cPanel works on.  If you do not know your primary domain name please contact your
                    hosting provider or server administrator.</td>
                </tr>
                <tr>
                    <td class="col-opt">Username</td>
                    <td>The cPanel username used to login to your cPanel account.  <i>This is <b>not</b> the same thing as your WordPress administrator account</i>.
                    If your unsure of this name please contact your hosting provider or server administrator.</td>
                </tr>
                <tr>
                    <td class="col-opt">Password</td>
                    <td>The password of the cPanel user</td>
                </tr>
                <tr>
                    <td class="col-opt">Troubleshoot</td>
                    <td>
                        <b>Common cPanel Connection Issues:</b><br/>
                        - Your host does not use <a href="http://cpanel.com/" target="_blank">cPanel Software</a> <br/>
                        - Your host has disabled cPanel API access <br/>
                        - Your host has configured cPanel to work differently (please contact your host) <br/>
                        - View a list of valid cPanel <a href='https://snapcreek.com/wordpress-hosting/' target='_blank'>Supported Hosts</a>
                    </td>
                </tr>
            </table>
            <br/><br/>

            <!-- DATABASE SETUP-->
            <h3>Setup</h3>
            The database setup options allow you to connect to an existing database or in the case of cPanel connect or create a new database.
            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td class="col-opt">Action</td>
                    <td>
                        <b>Create New Database:</b> Will attempt to create a new database if it does not exist.  When using the 'Basic' option this option will not work on many
                        hosting	providers as the ability to create new databases is normally locked down.  If the database does not exist then you will need to login to your
                        control panel and create the database.  If your host supports 'cPanel' then you can use this option to create a new database after logging in via your
                        cPanel account.
                        <br/><br/>

                        <b>Connect and Remove All Data:</b> This options will DELETE all tables in the database you are connecting to.  Please make sure you have
                        backups of all your data before using an portion of the installer, as this option WILL remove all data.
                        <br/><br/>

                        <b>Connect and Backup Any Existing Data:</b><sup>pro</sup> This options will RENAME all tables in the database you are connecting to with a prefix of
                        "<?php echo $GLOBALS['DB_RENAME_PREFIX'] ?>".
                        <br/><br/>

                        <b>Manual SQL Execution:</b><sup>pro</sup> This options requires that you manually run your own SQL import to an existing database before running the installer.
                        When this action is selected the dup-database__[hash].sql file found inside the dup-installer folder of the archive.zip file will NOT be ran.   The database your connecting to should already
                        be a valid WordPress installed database.  This option is viable when you need to run advanced search and replace options on the database.
                        <br/><br/>

                    </td>
                </tr>
                <tr>
                    <td class="col-opt">Host</td>
                    <td>The name of the host server that the database resides on.  Many times this will be 'localhost', however each hosting provider will have it's own naming
                    convention please check with your server administrator or host to valid for sure the name needed.  To add a port number just append it to the host i.e.
                    'localhost:3306'.</td>
                </tr>
                <tr>
                    <td class="col-opt">Database</td>
                    <td>The name of the database to which this installation will connect and install the new tables and data into.  Some hosts will require a prefix while others
                    do not.  Be sure to know exactly how your host requires the database name to be entered.</td>
                </tr>
                <tr>
                    <td class="col-opt">User</td>
                    <td>The name of a MySQL database server user. This is special account that has privileges to access a database and can read from or write to that database.
                    <i>This is <b>not</b> the same thing as your WordPress administrator account</i>.</td>
                </tr>
                <tr>
                    <td class="col-opt">Password</td>
                    <td>The password of the MySQL database server user.</td>
                </tr>

            </table>
            <br/><br/>

            <!-- OPTIONS-->
            <h3>Options</h3>
            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td class="col-opt">Prefix<sup>pro*</sup></td>
                    <td>By default, databases are prefixed with the cPanel account's username (for example, myusername_databasename).  However you can ignore this option if
                    your host does not use the default cPanel username prefix schema.  Check the 'Ignore cPanel Prefix' and the username prefixes will be ignored.
                    This will still require you to enter in the cPanels required setup prefix if they require one.  The checkbox will be set to read-only if your host has
                    disabled prefix settings.  Please see your host full requirements when using the cPanel options.</td>
                </tr>
                <tr>
                    <td class="col-opt">Legacy</td>
                    <td>When creating a database table, the Mysql version being used may not support the collation type of the Mysql version where the table was created.
                    In this scenario, the installer will fallback to a legacy collation type to try and create the table. This value should only be checked if you receive an error when
                    testing the database.
                    <br/><br/>
                    For example, if the database was created on MySQL 5.7 and the tables collation type was 'utf8mb4_unicode_520_ci', however your trying to run the installer
                    on an older MySQL 5.5 engine that does not support that type then an error will be thrown.  If this option is checked  then the legacy setting will try to
                    use 'utf8mb4_unicode_520', then 'utf8mb4', then 'utf8' and so on until it runs out of options.
                    <br/><br/>
                    For more information about this feature see the online FAQ question titled
                    <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q" target="_blank">"What is compatibility mode & 'unknown collation' errors"</a>
                    </td>
                </tr>
                <tr>
                    <td class="col-opt">Spacing</td>
                    <td>The process will remove utf8 characters represented as 'xC2' 'xA0' and replace with a uniform space.  Use this option if you find strange question
                    marks in you posts</td>
                </tr>
                <tr>
                    <td class="col-opt">Mode</td>
                    <td>The MySQL mode option will allow you to set the mode for this session.  It is very useful when running into conversion issues.  For a full overview please
                    see the	<a href="https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html" target="_blank">MySQL mode documentation</a> specific to your version.</td>
                </tr>
                <tr>
                    <td class="col-opt">Charset</td>
                    <td>When the database is populated from the SQL script it will use this value as part of its connection.  Only change this value if you know what your
                    databases character set should be.</td>
                </tr>
                <tr>
                    <td class="col-opt">Collation</td>
                    <td>When the database is populated from the SQL script it will use this value as part of its connection.  Only change this value if you know what your
                    databases collation set should be.</td>
                </tr>
            </table>
            <sup>*cPanel Only Option</sup>
            <br/><br/>

            <h3>Validation</h3>
            Testing the database connection is important and can help isolate possible issues that may arise with database version and compatibility issues.

            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td class="col-opt">Test<br/>Database</td>
                    <td>
                        The 'Test Database' button will help validate if the connection parameters are correct for this server and help with details about any issues
                        that may arise.
                    </td>
                </tr>
                <tr>
                    <td class="col-opt">Troubleshoot</td>
                    <td>
                        <b>Common Database Connection Issues:</b><br/>
                        - Double check case sensitive values 'User', 'Password' &amp; the 'Database Name' <br/>
                        - Validate the database and database user exist on this server <br/>
                        - Check if the database user has the correct permission levels to this database <br/>
                        - The host 'localhost' may not work on all hosting providers <br/>
                        - Contact your hosting provider for the exact required parameters <br/>
                        - Visit the online resources 'Common FAQ page' <br/>

                    </td>
                </tr>
            </table>
        </div>
    </div>
</section>

<!-- ============================================
STEP 3
============================================== -->
<?php
$sectionId = 'section-step-3';
$expandClass =  $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">3</span> of 4: Update Data</h2>
    <div class="content" >
        <a class="help-target" name="help-s3"></a>
        <div id="dup-help-step2" class="help-page">

            <!-- SETTINGS-->
            <h3>New Settings</h3>
            These are the new values (URL, Path and Title) you can update for the new location at which your site will be installed at.
            <br/><br/>

            <h3>Replace <sup>pro</sup></h3>
            This section will allow you to add as many custom search and replace items that you would like.  For example you can search for other URLs to replace.  Please use high
            caution when using this feature as it can have unintended consequences as it will search the entire database.   It is recommended to only use highly unique items such as
            full URL or file paths with this option.
            <br/><br/>

            <!-- ADVANCED OPTS -->
            <h3>Options</h3>
            <table class="help-opt">
                <tr>
                    <th class="col-opt">Option</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td colspan="2" class="section">New Admin Account</td>
                </tr>
                <tr>
                    <td class="col-opt">Username</td>
                    <td>A new WordPress username to create.  This will create a new WordPress administrator account.  Please note that usernames are not changeable from the within the UI.</td>
                </tr>
                <tr>
                    <td class="col-opt">Password</td>
                    <td>The new password for the new user.  Must be at least 6 characters long.</td>
                </tr>
                <tr>
                    <td colspan="2" class="section">Scan Options</td>
                </tr>
                <tr>
                    <td class="col-opt">Cleanup <sup>pro</sup></td>
                    <td>The checkbox labeled Remove schedules &amp; storage endpoints will empty the Duplicator schedule and storage settings.  This is recommended to keep enabled so that you do not have unwanted schedules and storage options enabled.</td>
                </tr>
                <tr>
                    <td class="col-opt">Old URL</td>
                    <td>The old URL of the original values that the package was created with.  These values should not be changed, unless you know the underlying reasons</td>
                </tr>
                <tr>
                    <td class="col-opt">Old Path</td>
                    <td>The old path of the original values that the package was created with.  These values should not be changed, unless you know the underlying reasons</td>
                </tr>
                <tr>
                    <td class="col-opt">Site URL</td>
                    <td> For details see WordPress <a href="http://codex.wordpress.org/Changing_The_Site_URL" target="_blank">Site URL</a> &amp; <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory" target="_blank">Alternate Directory</a>.  If you're not sure about this value then leave it the same as the new setup URL.</td>
                </tr>
                <tr>
                    <td class="col-opt">Scan Tables</td>
                    <td>Select the tables to be updated. This process will update all of the 'Old Settings' with the new 'Setup' settings. Hold down the 'ctrl key' to select/deselect multiple.</td>
                </tr>
                <tr>
                    <td class="col-opt">Activate Plugins</td>
                    <td>These plug-ins are the plug-ins that were activated when the package was created and represent the plug-ins that will be activated after the install.</td>
                </tr>
                <tr>
                    <td class="col-opt">Update email domains</td>
                    <td>The domain portion of all email addresses will be updated if this option is enabled.</td>
                </tr>
                <tr>
                    <td class="col-opt">Full Search</td>
                    <td>Full search forces a scan of every single cell in the database. If it is not checked then only text based columns are searched which makes the update process much faster.
                    Use this option if you have issues with data not updating correctly.</td>
                </tr>
                <tr>
                    <td class="col-opt">Post GUID</td>
                    <td>If your moving a site keep this value checked. For more details see the <a href="http://codex.wordpress.org/Changing_The_Site_URL#Important_GUID_Note" target="_blank">notes on GUIDS</a>.	Changing values in the posts table GUID column can change RSS readers to evaluate that the posts are new and may show them in feeds again.</td>
                </tr>
                <tr>
                    <td class="col-opt">Cross search <sup>pro</sup></td>
                    <td>
                        This option enables the searching and replacing of subsite domains and paths that link to each other.  <br>
                        Check this option if hyperlinks of at least one subsite point to another subsite.<br>
                        Uncheck this option there if there are at least <?php echo MAX_SITES_TO_DEFAULT_ENABLE_CORSS_SEARCH ?> subsites and no subsites hyperlinking to each other (Checking this option in this scenario would unnecessarily load your server).<br><br>
                        Check this option If you unsure if you need this option.<br></td>
                </tr>
                <tr>
                    <td class="col-opt"> Max size check for serialize objects</td>
                    <td>
                        Large serialized objects can cause a fatal error when Duplicator attempts to transform them. <br>
                        If a fatal error is generated, lower this limit. <br>
                        If a warning of this type appears in the final report <br>
                        <pre style="white-space: pre-line;">
                            DATA-REPLACE ERROR: Serialization
                            ENGINE: serialize data too big to convert; data len: XXX Max size: YYY
                            DATA: .....
                        </pre>
                        And you think that the serialized object is necessary you can increase the limit or <b>set it to 0 to have no limit</b>.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="section">WP-Config File</td>
                </tr>
                <tr>
                    <td class="col-opt">Config SSL</td>
                    <td>Turn off SSL support for WordPress. This sets FORCE_SSL_ADMIN in your wp-config file to false if true, otherwise it will create the setting if not set.  The "Enforce on Login"
                        will turn off SSL support for WordPress Logins.</td>
                </tr>
                <tr>
                    <td class="col-opt">Config Cache</td>
                    <td>Turn off Cache support for WordPress. This sets WP_CACHE in your wp-config file to false if true, otherwise it will create the setting if not set.  The "Keep Home Path"
                    sets WPCACHEHOME in your wp-config file to nothing if true, otherwise nothing is changed.</td>
                </tr>
            </table>
        </div>
    </div>
</section>

<!-- ============================================
STEP 4
============================================== -->
<?php
$sectionId = 'section-step-4';
$expandClass =  $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">4</span> of 4: Test Site</h2>
    <div class="content" >
        <a class="help-target" name="help-s4"></a>
        <div id="dup-help-step3" class="help-page">
            <h3>Final Steps</h3>

            <b>Review Install Report</b><br/>
            The install report is designed to give you a synopsis of the possible errors and warnings that may exist after the installation is completed.
            <br/><br/>

            <b>Test Site</b><br/>
            After the install is complete run through your entire site and test all pages and posts.
            <br/><br/>

            <b>Final Security Cleanup</b><br/>
            When completed with the installation please delete all installation files.  <b>Leaving these files on your server can be a security risk!</b>   You can remove
            all these files by logging into your WordPress admin and following the remove notification links or by deleting these file manually.   Be sure these files/directories are removed.  Optionally
            it is also recommended to remove the archive.zip/daf file.
            <ul>
                <li>dup-installer</li>
                <li>installer.php</li>
                <li>installer-backup.php</li>
                <li>dup-installer-bootlog__[HASH].txt</li>
                <li>archive.zip/daf</li>
            </ul>
        </div>
    </div>
</section>

<?php
$sectionId = 'section-troubleshoot';
$expandClass =  $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Troubleshooting Tips</h2>
    <div class="content" >
        <a class="help-target" name="help-s5"></a>
        <div id="troubleshoot" class="help-page">

            <div style="padding: 0px 10px 10px 10px;">
                <b>Common Quick Fix Issues:</b>
                <ul>
                    <li>Use a <a href='https://snapcreek.com/wordpress-hosting/' target='_blank'>Duplicator approved hosting provider</a></li>
                    <li>Validate directory and file permissions (see below)</li>
                    <li>Validate web server configuration file (see below)</li>
                    <li>Clear your browsers cache</li>
                    <li>Deactivate and reactivate all plugins</li>
                    <li>Resave a plugins settings if it reports errors</li>
                    <li>Make sure your root directory is empty</li>
                </ul>

                <b>Permissions:</b><br/>
                Not all operating systems are alike.  Therefore, when you move a package (zip file) from one location to another the file and directory permissions may not always stick.  If this is the case then check your WordPress directories and make sure it's permissions are set to 755. For files make sure the permissions are set to 644 (this does not apply to windows servers).   Also pay attention to the owner/group attributes.  For a full overview of the correct file changes see the <a href='http://codex.wordpress.org/Hardening_WordPress#File_permissions' target='_blank'>WordPress permissions codex</a>
                <br/><br/>

                <b>Web server configuration files:</b><br/>
                For Apache web server the root .htaccess file was copied to .htaccess__[HASH]. A new stripped down .htaccess file was created to help simplify access issues.  For IIS web server the web.config file was copied to web.config.orig, however no new web.config file was created.  If you have not altered this file manually then resaving your permalinks and resaving your plugins should resolve most all changes that were made to the root web configuration file.   If your still experiencing issues then open the .orig file and do a compare to see what changes need to be made. <br/><br/><b>Plugin Notes:</b><br/> It's impossible to know how all 3rd party plugins function.  The Duplicator attempts to fix the new install URL for settings stored in the WordPress options table.   Please validate that all plugins retained there settings after installing.   If you experience issues try to bulk deactivate all plugins then bulk reactivate them on your new duplicated site. If you run into issues were a plugin does not retain its data then try to resave the plugins settings.
                <br/><br/>

                 <b>Cache Systems:</b><br/>
                 Any type of cache system such as Super Cache, W3 Cache, etc. should be emptied before you create a package.  Another alternative is to include the cache directory in the directory exclusion path list found in the options dialog. Including a directory such as \pathtowordpress\wp-content\w3tc\ (the w3 Total Cache directory) will exclude this directory from being packaged. In is highly recommended to always perform a cache empty when you first fire up your new site even if you excluded your cache directory.
                 <br/><br/>

                 <b>Trying Again:</b><br/>
                 If you need to retry and reinstall this package you can easily run the process again by deleting all files except the installer and package file and then browse to the installer again.
                 <br/><br/>

                 <b>Additional Notes:</b><br/>
                 If you have made changes to your PHP files directly this might have an impact on your duplicated site.  Be sure all changes made will correspond to the sites new location.
                 Only the package (zip file) and the installer (php file) should be in the directory where you are installing the site.  Please read through our knowledge base before submitting any issues.
                 If you have a large log file that needs evaluated please email the file, or attach it to a help ticket.
            </div>
        </div>
    </div>
</section>

<div style="text-align:center; margin-top: 28px;">For additional help please visit <a href="https://snapcreek.com/support/docs/" target="_blank">Duplicator Migration and Backup Online Help</a></div>


</div>

<script>
$(document).ready(function ()
{
    //Disable href for toggle types
    $("section.expandable .expand-header").click(function() {
        var section = $(this).parent();
        if(section.hasClass('open')) {
            section.removeClass('open').addClass('close');
        } else {
             section.removeClass('close').addClass('open');
        }
    });

    <?php if (!empty($open_section)) { ?>
        $("html, body").animate({ scrollTop: $('#<?php echo $open_section; ?>').offset().top }, 1000);
    <?php } ?>

});
</script>
<!-- END OF VIEW HELP -->
