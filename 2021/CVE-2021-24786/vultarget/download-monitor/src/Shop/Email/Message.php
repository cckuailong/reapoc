<?php

namespace Never5\DownloadMonitor\Shop\Email;

class Message {

	/** @var string */
	private $to;

	/** @var string */
	private $subject;

	/** @var string */
	private $template;

	/** @var \Never5\DownloadMonitor\Shop\Order\Order */
	private $order;

	/**
	 * Message constructor.
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $template
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 */
	public function __construct( $to, $subject, $template, $order ) {
		$this->to       = $to;
		$this->subject  = $subject;
		$this->template = $template;
		$this->order    = $order;
	}

	/**
	 * Send this message
	 */
	public function send() {

		// variable parser
		$var_parser = new VarParser();

		// start buffer
		ob_start();

		// load template
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/email/' . sanitize_file_name( $this->template ) );

		// put template in var
		$html_body = ob_get_clean();

		// replace all email variables
		$html_body = $var_parser->parse( $this->order, $html_body );

		// add HTML filter
		add_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );

		// add action so we can alter phpmailer to add our altbody
		add_action( 'phpmailer_init', array( $this, '_set_alt_body' ) );

		// make email subject filterable
		$email_subject = apply_filters( 'dlm_shop_email_' . sanitize_file_name( $this->template ), $this->subject );

		// send email
		wp_mail( $this->to, $email_subject, $html_body );

		// reset phpmailer altbody
		$GLOBALS['phpmailer'] = null;

		remove_action( 'phpmailer_init', array( $this, '_set_alt_body' ) );

		// remove HTML filter
		remove_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
	}

	/**
	 * Set HTML body
	 *
	 * @param SomeWPGlobalPassedByReferencePHPMailerObject $phpmailer
	 */
	public function _set_alt_body( $phpmailer ) {
		$var_parser = new VarParser();
		ob_start();
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/email/' . sanitize_file_name( $this->template ) . '-plain' );
		$plain_body         = ob_get_clean();
		$plain_body         = $var_parser->parse( $this->order, $plain_body );
		$phpmailer->AltBody = $plain_body;
	}

	/**
	 * HTML emails filter
	 *
	 * @return string
	 */
	public function _set_html_content_type() {
		return 'text/html';
	}
}