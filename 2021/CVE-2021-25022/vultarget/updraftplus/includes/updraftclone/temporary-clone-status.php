<?php

if (!defined('ABSPATH')) die('No direct access allowed');

class UpdraftPlus_Temporary_Clone_Status {

	/**
	 * WP is Installed
	 */
	const INSTALLED = 1;

	/**
	 * Data is uploading
	 */
	const UPLOADING = 2;

	/**
	 * Data is uploaded and is restoring
	 */
	const RESTORING = 3;

	/**
	 * The current status number
	 *
	 * @var int
	 */
	public $current_status;

	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_action('init', array($this, 'init'));
	}

	/**
	 * This function is called via the WordPress init action, it will check if the page is not the admin backend and output the clone status
	 *
	 * @return void
	 */
	public function init() {
		if (is_admin() || (defined('WP_CLI') && WP_CLI) || 'GET' != $_SERVER['REQUEST_METHOD']) return;

		$this->output_status_page();
	}

	/**
	 * Outputs the clone status
	 *
	 * @param bool $die - Defines if should die at the end
	 * @return void
	 */
	public function output_status_page($die = true) {
		$this->current_status = $this->get_status();
		$this->page_start();
		echo '<div class="updraftclone_content_container">';

		echo '<img class="updraftclone_logo" alt="UpdraftClone Logo" src="'.trailingslashit(UPDRAFTPLUS_URL).'images/updraftclone_logo_white.png">';
		echo $this->get_content();
		?>
		<div class="status-box">
			<section class="progress">
				<div class="progress-item <?php echo $this->get_progress_item_class(self::INSTALLED); ?>">
					<div class="progress-item__bar"></div>
					<span class="icon"><?php echo $this->get_progress_item_icon(self::INSTALLED); ?></span>
					<?php _e('WordPress installed', 'updraftplus'); ?>
				</div>
				<div class="progress-item <?php echo $this->get_progress_item_class(self::UPLOADING); ?>">
					<div class="progress-item__bar"></div>
					<span class="icon"><?php echo $this->get_progress_item_icon(self::UPLOADING); ?></span>
					<?php
					if (self::UPLOADING >= $this->current_status) {
						_e('Receiving site data', 'updraftplus');
					} else {
						_e('Site data received', 'updraftplus');
					}
					?>
				</div>
				<div class="progress-item <?php echo $this->get_progress_item_class(self::RESTORING); ?>">
					<div class="progress-item__bar"></div>
					<span class="icon"><?php echo $this->get_progress_item_icon(self::RESTORING); ?></span>
					<?php
					if (self::RESTORING >= $this->current_status) {
						_e('Deploying site data', 'updraftplus');
					} else {
						_e('Site data has been deployed', 'updraftplus');
					}
					?>
				</div>
				<div class="progress-item">
					<div class="progress-item__bar"></div>
					<span class="icon"><?php echo $this->get_progress_item_icon(); ?></span>
					<?php
						_e('Clone ready', 'updraftplus');
					?>
				</div>
			</section>
			<?php echo '<p class="status-description">' . $this->get_status_description() . '</p>'; ?>

		</div>

		<?php
		echo '</div>';
		$this->page_end();
		if ($die) die();
	}

	/**
	 * This function will output the start of the updraftclone status page
	 *
	 * @return void
	 */
	public function page_start() {
		echo '<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" class="wp-toolbar" lang="en-US">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="refresh" content="60">
		<meta name="robots" content="noindex, nofollow">
		<title>UpdraftClone</title>
		<style>

		@-webkit-keyframes rotateIcon {

			from {
				-webkit-transform: rotate(0);
						transform: rotate(0);
			}
		
			to {
				-webkit-transform: rotate(360deg);
						transform: rotate(360deg);
			}
		
		}

		@keyframes rotateIcon {

			from {
				-webkit-transform: rotate(0);
						transform: rotate(0);
			}
		
			to {
				-webkit-transform: rotate(360deg);
						transform: rotate(360deg);
			}
		
		}

		body {
			background-color: #EDEDED;
			margin: 0;
			padding: 20px;
		}
		p {
			padding: 0;
			margin: 15px 0;
			line-height: 1.2;
		}
		body:before {
			content: \' \';
			position: absolute;
			background: #db6939;
			display: block;
			height: 477px;
			top: 0;
			left: 0;
			width: 100%;
			z-index: 1;
		}
		a {
			color: #ffceb9;
		}
		.updraftclone_content_container {
			position: relative;
			z-index: 2;
			margin:auto; 
			margin-top:40px; 
			width:80%; 
			max-width: 520px; 
			text-align:center; 
			color: #ffffff; 
			font-family: Source Sans Pro, Helvetica, Arial, Lucida, sans-serif; 
			font-weight: 300; 
			font-size: 16px;
		}
		.updraftclone_logo {
			width: 50%;
		}

		.status-box {
			max-width: 520px;
			margin: 0 auto;
			background: #FFF;
			box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
			color: #43322B;
		}
		section.progress {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			position: relative;
		}
		
		.progress-item {
			-webkit-box-flex: 1;
				-ms-flex: 1;
					flex: 1;
			position: relative;
			padding: 10px;
			padding-bottom: 20px;
		}

		.progress-item.active {
			font-weight: bold;
		}

		.progress-item__bar {
			height: 10px;
			position: absolute;
			top: 0;
			width: 100%;
			left: 0;
		}

		.done .progress-item__bar {
			background: #43322B;
		}
		
		.active .progress-item__bar {
			background: #43322B;
			overflow: hidden;
		}

		.active .progress-item__bar:before {
			content: \' \';
			display: block;
			width: 10px;
			height: 10px;
			position: absolute;
			background: #43322B;
			right: 0;
			top: 0;
			border-top: 10px solid #FFF;
			border-right: 10px solid #FFF;
			-webkit-transform: translateY(-5px) translateX(10px) rotate(45deg);
					transform: translateY(-5px) translateX(10px) rotate(45deg);
		}

		section.progress:before {
			content: \' \';
			position: absolute;
			display: block;
			height: 10px;
			top: 0;
			left: 0;
			width: 100%;
			box-shadow: 0 4px 14px rgba(0, 0, 0, 0.21);
		}
		span.icon {
			display: block;
			padding-top: 15px;
			padding-bottom: 10px;
		}
		span.icon svg {
			display: inline-block;
			max-width: 28px;
			fill: #C4C4C4;
			color: currentColor;
		}

		.done span.icon svg {
			fill: green;
		}

		.active span.icon svg {
			fill: #43322B;
			-webkit-animation-name: rotateIcon;
					animation-name: rotateIcon;
			-webkit-animation-duration: 1.8s;
					animation-duration: 1.8s;
			-webkit-animation-iteration-count: infinite;
					animation-iteration-count: infinite;
			-webkit-animation-timing-function: linear;
					animation-timing-function: linear;
		}

		.progress-item:not(.done):not(.active) {
			color: #C4C4C4;
		}
		.done {
			color: green;
		}

		p.status-description {
			margin:  0;
			padding: 20px;
			border-top: 1px solid #EBEBEB;
		}

		@media (max-width: 520px) {
			.progress-item:not(.active) {
				display: none;
			}
		}
		</style>
		</head>
		<body>';
	}

	/**
	 * This function will output the end of the updraftclone status page
	 *
	 * @return void
	 */
	public function page_end() {
		?>
			<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="display: none;">
				<symbol viewBox="0 0 20 20" id="yes-alt"><title>yes-alt</title><g><path d="M10 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm-.615 12.66h-1.34l-3.24-4.54 1.34-1.25 2.57 2.4 5.14-5.93 1.34.94-5.81 8.38z"/></g></symbol>
				<symbol viewBox="0 0 20 20" id="yes"><title>yes</title><g><path d="M14.83 4.89l1.34.94-5.81 8.38H9.02L5.78 9.67l1.34-1.25 2.57 2.4z"/></g></symbol>
				<symbol viewBox="0 0 20 20" id="update"><title>update</title><g><path d="M10.2 3.28c3.53 0 6.43 2.61 6.92 6h2.08l-3.5 4-3.5-4h2.32c-.45-1.97-2.21-3.45-4.32-3.45-1.45 0-2.73.71-3.54 1.78L4.95 5.66C6.23 4.2 8.11 3.28 10.2 3.28zm-.4 13.44c-3.52 0-6.43-2.61-6.92-6H.8l3.5-4c1.17 1.33 2.33 2.67 3.5 4H5.48c.45 1.97 2.21 3.45 4.32 3.45 1.45 0 2.73-.71 3.54-1.78l1.71 1.95c-1.28 1.46-3.15 2.38-5.25 2.38z"/></g></symbol>
				<symbol viewBox="0 0 20 20" id="clock"><title>clock</title><g><path d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm0 14c3.31 0 6-2.69 6-6s-2.69-6-6-6-6 2.69-6 6 2.69 6 6 6zm-.71-5.29c.07.05.14.1.23.15l-.02.02L14 13l-3.03-3.19L10 5l-.97 4.81h.01c0 .02-.01.05-.02.09S9 9.97 9 10c0 .28.1.52.29.71z"/></g></symbol>
			</svg>		
			</body>
		</html>
		<?php
	}

	/**
	 * This function will get and return the clone status title ready to be displayed on the page
	 *
	 * @return string - the clone status title
	 */
	public function get_status_title() {
		
		$code = "";

		switch ($this->current_status) {
			case self::INSTALLED:
				$code = __("WordPress installed", "updraftplus");
				break;
			case self::UPLOADING:
				$code = __("Receiving site data", "updraftplus");
				break;
			case self::RESTORING:
				$code = __("Deploying site data", "updraftplus");
				break;
			default:
				$code = "";
				break;
		}

		return $code;
	}

	/**
	 * This function will get and return the clone status description ready to be displayed on the page
	 *
	 * @return string - the clone status description
	 */
	public function get_status_description() {
		$description = "";

		switch ($this->current_status) {
			case self::INSTALLED:
				$description = __('WordPress installed; now awaiting the site data to be sent.', 'updraftplus');
				break;
			case self::UPLOADING:
				$backup_details = $this->get_backup_details();
				$description = sprintf(__('The sending of the site data has begun. So far %s data archives totalling %s have been received', 'updraftplus'), '<strong>'.$backup_details['sets'].'</strong>', '<strong>'.round($backup_details['uploaded'], 2).' MB</strong>');
				break;
			case self::RESTORING:
				UpdraftPlus_Backup_History::rebuild();
				$backup_details = $this->get_backup_details();
				$description = __('The site data has all been received, and its import has begun.', 'updraftplus').' '.sprintf(__('%s archives remain', 'updraftplus'), '<strong>'.$backup_details['sets'].'</strong>');
				break;
			default:
				$description = "(?)";
				break;
		}

		return $description;
	}

	/**
	 * This function will return information about the backup such as the amount of sets and the size of the backup set
	 *
	 * @return array - an array with backup information
	 */
	public function get_backup_details() {
		global $updraftplus;
		
		$backup_history = UpdraftPlus_Backup_History::get_history();
		$backupable_entities = $updraftplus->get_backupable_file_entities();
		$sets = 0;
		$uploaded = 0;
		
		foreach ($backupable_entities as $key => $info) {
			foreach ($backup_history as $backup) {
				if (isset($backup[$key]) && isset($backup[$key.'-size'])) {
					$sets += count($backup[$key]);
					$uploaded += $backup[$key.'-size'];
				}
			}
		}
		
		$uploaded = round($uploaded / 1048576, 1);

		return array('uploaded' => $uploaded, 'sets' => $sets);
	}

	/**
	 * This function will get and return the clone content ready to be displayed on the page
	 *
	 * @return string - the clone content
	 */
	public function get_content() {
		$content = '<p>'.__('Your UpdraftClone is still setting up.', 'updraftplus').' '.sprintf(__('You can check the progress here or in %s', 'updraftplus'), '<a href="https://updraftplus.com/my-account/clones/" target="_blank">'.__('your UpdraftPlus.com account', 'updraftplus')).'</a></p>';
		$content .= '<p><a href="https://updraftplus.com/faq-category/updraftclone/" target="_blank">'.__('To read FAQs/documentation about UpdraftClone, go here.', 'updraftplus').'</a></p>';
		return $content;
	}

	/**
	 * This function will work out what stage the clone is in and return the correct status code
	 *
	 * @return int - the clone status code
	 */
	public function get_status() {
		global $updraftplus;

		$backup_history = UpdraftPlus_Backup_History::get_history();

		if (empty($backup_history)) return self::INSTALLED;

		$updraft_dir = trailingslashit($updraftplus->backups_dir_location());

		if (file_exists($updraft_dir.'ready_for_restore')) return self::RESTORING;

		return self::UPLOADING;
	}

	/**
	 * Get the progress item class
	 *
	 * @param int $number The status number
	 * @return string
	 */
	private function get_progress_item_class($number) {
		return ($number === $this->current_status) ? 'active' : (($number < $this->current_status) ? 'done' : '');
	}

	/**
	 * Get the progress item icon
	 *
	 * @param int $number The status number
	 * @return string
	 */
	private function get_progress_item_icon($number = 1000) {
		$icon = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">';
		$icon .= '<use href="#'.(($number === $this->current_status) ? 'update' : (($number < $this->current_status) ? 'yes' : 'clock')).'" />';
		$icon .= '</svg>';
		return $icon;
	}
}

if (defined('UPDRAFTPLUS_THIS_IS_CLONE') && 1 == UPDRAFTPLUS_THIS_IS_CLONE) {
	new UpdraftPlus_Temporary_Clone_Status();
}
