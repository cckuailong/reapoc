<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit; 

require_once DUPLICATOR_PLUGIN_PATH . '/classes/ui/class.ui.screen.base.php';

/*
Because the default way is overwriting the option names in the hidden input wp_screen_options[option]
I added all inputs via one option name and saved them with the update_user_meta function.
Also, the set-screen-option is not being triggered inside the class, that's why it's here. -TG
*/
add_filter('set-screen-option', 'dup_packages_set_option', 10, 3);
function dup_packages_set_option($status, $option, $value) {
    if('package_screen_options' == $option){
        $user_id = get_current_user_id();
    }
    return false;
}

class DUP_Package_Screen extends DUP_UI_Screen
{

	public function __construct($page)
    {
       add_action('load-'.$page, array($this, 'Init'));
    }

	public function Init()
	{
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'list';
		$active_tab = isset($_GET['action']) && $_GET['action'] == 'detail' ? 'detail' : $active_tab;
		$this->screen = get_current_screen();

		switch (strtoupper($active_tab)) {
			case 'LIST':	$content = $this->get_list_help();		break;
			case 'NEW1':	$content = $this->get_step1_help();		break;
			case 'NEW2':	$content = $this->get_step2_help(); 	break;
			case 'NEW3':	$content = $this->get_step3_help(); 	break;
			case 'DETAIL':	$content = $this->get_details_help(); 	break;
			default:
				$content = $this->get_list_help();
				break;
		}

		$guide = '#guide-packs';
		$faq   = '#faq-package';
		$content .= "<b>References:</b><br/>"
					. "<a href='".esc_url("https://snapcreek.com/duplicator/docs/guide/".$guide)."' target='_sc-guide'>User Guide</a> | "
					. "<a href='".esc_url("https://snapcreek.com/duplicator/docs/faqs-tech/".$faq)."' target='_sc-guide'>FAQs</a> | "
					. "<a href='https://snapcreek.com/duplicator/docs/quick-start/' target='_sc-guide'>Quick Start</a>";

		$this->screen->add_help_tab( array(
				'id'        => 'dup_help_package_overview',
				'title'     => esc_html__('Overview','duplicator'),
				'content'   => "<p>{$content}</p>"
			)
		);

		$this->getSupportTab($guide, $faq);
		$this->getHelpSidbar();
	}

	public function get_list_help()
	{
		return  __("<b><i class='fa fa-archive'></i> Packages » All</b><br/> The 'Packages' section is the main interface for managing all the packages that have been created.  "
				. "A Package consists of two core files, the 'archive.zip' and the 'installer.php' file.  The archive file is a zip file containing all your WordPress files and a "
				. "copy of your WordPress database.  The installer file is a php file that when browsed to via a web browser presents a wizard that redeploys/installs the website "
				. "by extracting the archive file and installing the database.   To create a package, click the 'Create New' button and follow the prompts. <br/><br/>"

                . "<b><i class='fa fa-download'></i> Downloads</b><br/>"
			    . "To download the package files click on the Installer and Archive buttons after creating a package.  The archive file will have a copy of the installer inside of it named "
				. "installer-backup.php in case the original installer file is lost.  To see the details of a package click on the <i class='fa fa-archive'></i> details button.<br/><br/>"

				. "<b><i class='far fa-file-archive'></i> Archive Types</b><br/>"
				. "An archive file can be saved as either a .zip file or .daf file.  A zip file is a common archive format used to compress and group files.  The daf file short for "
				. "'Duplicator Archive Format' is a custom format used specifically  for working with larger packages and scale-ability issues on many shared hosting platforms.  Both "
				. "formats work very similar.  The main difference is that the daf file can only be extracted using the installer.php file or the "
				. "<a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-052-q' target='_blank'>DAF extraction tool</a>.  The zip file can be used by the installer.php "
				. "or other zip tools like winrar/7zip/winzip or other client-side tools. <br/><br/>"
			,'duplicator');
	}


	public function get_step1_help()
	{
		return __("<b>Packages New » 1 Setup</b> <br/>"
				. "The setup step allows for optional filtered directory paths, files, file extensions and database tables.  To filter specific system files, click the 'Enable File Filters' "
				. "checkbox and add the full path of the file or directory, followed by a semicolon.  For a file extension add the name (i.e. 'zip') followed by a semicolon. <br/><br/>"

				. "To exclude a database table, check the box labeled 'Enable Table Filters' and check the table name to exclude. To include only a copy of your database in the "
				. "archive file check the box labeled 'Archive Only the Database'.  The installer.php file can optionally be pre-filled with data at install time but is not "
				. "required.  <br/><br/>",'duplicator');
	}


	public function get_step2_help()
	{
		return __("<b>Packages » 2 Scan</b> <br/>"
				. "The plugin will scan your system files and database to let you know if there are any concerns or issues that may be present.  All items in green mean the checks "
				. "looked good.  All items in red indicate a warning.  Warnings will not prevent the build from running, however if you do run into issues with the build then investigating "
				. "the warnings should be considered.  Click on each section for more details about each scan check. <br/><br/>",'duplicator');
	}

	public function get_step3_help()
	{
		return __("<b>Packages » 3 Build</b> <br/>"
				. "The final step in the build process where the installer script and archive of the website can be downloaded.   To start the install process follow these steps: "
				. "<ol>"
				. "<li>Download the installer.php and archive.zip files to your local computer.</li>"
				. "<li>For localhost installs be sure you have PHP, Apache & MySQL installed on your local computer with software such as XAMPP, Instant WordPress or MAMP for MAC. "
				. "Place the package.zip and installer.php into any empty directory under your webroot then browse to the installer.php via your web browser to launch the install wizard.</li>"
				. "<li>For remote installs use FTP or cPanel to upload both the archive.zip and installer.php to your hosting provider. Place the files in a new empty directory under "
				. "your host's webroot accessible from a valid URL such as http://your-domain/your-wp-directory/installer.php to launch the install wizard. On some hosts the root directory "
				. "will be a something like public_html -or- www.  If your're not sure contact your hosting provider. </li>"
				. "</ol>"
				. "For complete instructions see:<br/>
					<a href='https://snapcreek.com/duplicator/docs/quick-start/?utm_source=duplicator_free&amp;utm_medium=wordpress_plugin&amp;utm_content=package_built_install_help&amp;utm_campaign=duplicator_free#quick-040-q' target='_blank'>
					How do I install this Package?</a><br/><br/>",'duplicator');
	}

	public function get_details_help()
	{
		return __("<b>Packages » Details</b> <br/>"
				. "The details view will give you a full break-down of the package including any errors that may have occured during the install. <br/><br/>",'duplicator');
	}

}


