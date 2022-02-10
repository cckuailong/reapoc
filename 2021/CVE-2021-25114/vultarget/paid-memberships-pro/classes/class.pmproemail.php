<?php
	class PMProEmail
	{
		
		function __construct()
		{
			$this->email = $this->from = $this->fromname = $this->subject = $this->template = $this->data = $this->body = NULL;
		}					
		
		function sendEmail($email = NULL, $from = NULL, $fromname = NULL, $subject = NULL, $template = NULL, $data = NULL)
		{			
			//if values were passed
			if($email)
				$this->email = $email;
			if($from)
				$this->from = $from;
			if($fromname)
				$this->fromname = $fromname;
			if($subject)
				$this->subject = $subject;
			if($template)
				$this->template = $template;
			if($data)
				$this->data = $data;

			// If email is disabled don't send it.
			// Note option may have 'false' stored as a string.
			$template_disabled = pmpro_getOption( 'email_' . $this->template . '_disabled' );
			if ( ! empty( $template_disabled ) && $template_disabled !== 'false' ) {
				return false;
			}

			//default values
			global $current_user, $pmpro_email_templates_defaults;
			if(!$this->email)
				$this->email = $current_user->user_email;
				
			if(!$this->from)
				$this->from = pmpro_getOption("from_email");
			
			if(!$this->fromname)
				$this->fromname = pmpro_getOption("from_name");
	
			if(!$this->template)
				$this->template = "default";
			
			//Okay let's get the subject stuff.
			$template_subject = pmpro_getOption( 'email_' . $this->template . '_subject' );
			if ( ! empty( $template_subject ) ) {
				$this->subject = $template_subject;
			} elseif ( empty( $this->subject ) ) {
				$this->subject = ! empty( $pmpro_email_templates_defaults[$this->template]['subject'] ) ? sanitize_text_field( $pmpro_email_templates_defaults[$this->template]['subject'] ) : sprintf(__("An Email From %s", 'paid-memberships-pro' ), get_option("blogname"));
			}

			//decode the subject line in case there are apostrophes/etc in it
			$this->subject = html_entity_decode($this->subject, ENT_QUOTES, 'UTF-8');
						
			$this->headers = array("Content-Type: text/html");
			
			$this->attachments = array();
			
			//load the template			
			$locale = apply_filters("plugin_locale", get_locale(), "paid-memberships-pro");

			if( empty( $this->data['body'] ) && ! empty( pmpro_getOption( 'email_' . $this->template . '_body' ) ) )
				$this->body = pmpro_getOption( 'email_' . $this->template . '_body' );
			elseif(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/email/" . $locale . "/" . $this->template . ".html"))
				$this->body = file_get_contents(get_stylesheet_directory() . "/paid-memberships-pro/email/" . $locale . "/" . $this->template . ".html");	//localized email folder in child theme
			elseif(file_exists(get_stylesheet_directory() . "/paid-memberships-pro/email/" . $this->template . ".html"))
				$this->body = file_get_contents(get_stylesheet_directory() . "/paid-memberships-pro/email/" . $this->template . ".html");	//email folder in child theme
			elseif(file_exists(get_stylesheet_directory() . "/membership-email-" . $this->template . ".html"))
				$this->body = file_get_contents(get_stylesheet_directory() . "/membership-email-" . $this->template . ".html");			//membership-email- file in child theme
			elseif(file_exists(get_template_directory() . "/paid-memberships-pro/email/" . $locale . "/" . $this->template . ".html"))	
				$this->body = file_get_contents(get_template_directory() . "/paid-memberships-pro/email/" . $locale . "/" . $this->template . ".html");	//localized email folder in parent theme
			elseif(file_exists(get_template_directory() . "/paid-memberships-pro/email/" . $this->template . ".html"))
				$this->body = file_get_contents(get_template_directory() . "/paid-memberships-pro/email/" . $this->template . ".html");	//email folder in parent theme
			elseif(file_exists(get_template_directory() . "/membership-email-" . $this->template . ".html"))
				$this->body = file_get_contents(get_template_directory() . "/membership-email-" . $this->template . ".html");			//membership-email- file in parent theme			
			elseif(file_exists(WP_LANG_DIR . '/pmpro/email/' . $locale . "/" . $this->template . ".html"))
				$this->body = file_get_contents(WP_LANG_DIR . '/pmpro/email/' . $locale . "/" . $this->template . ".html");				//localized email folder in WP language folder
			elseif(file_exists(WP_LANG_DIR . '/pmpro/email/' . $this->template . ".html"))
				$this->body = file_get_contents(WP_LANG_DIR . '/pmpro/email/' . $this->template . ".html");								//email folder in WP language folder
			elseif(file_exists(PMPRO_DIR . "/languages/email/" . $locale . "/" . $this->template . ".html"))
				$this->body = file_get_contents(PMPRO_DIR . "/languages/email/" . $locale . "/" . $this->template . ".html");					//email folder in PMPro language folder
			elseif( empty( $this->data['body'] ) && ! empty( $pmpro_email_templates_defaults[$this->template]['body'] ) )
				$this->body = $pmpro_email_templates_defaults[$this->template]['body'];									//default template in plugin
			elseif(!empty($this->data) && !empty($this->data['body']))
				$this->body = $this->data['body'];																						//data passed in


			// Get template header.
			if( pmpro_getOption( 'email_header_disabled' ) != 'true' ) {
				$email_header = pmpro_email_templates_get_template_body('header');
			} else {
				$email_header = '';
			}

			// Get template footer
			if( pmpro_getOption( 'email_footer_disabled' ) != 'true' ) {
				$email_footer = pmpro_email_templates_get_template_body('footer');
			} else {
				$email_footer = '';
			}

			// Add header and footer to email body.
			$this->body = $email_header . $this->body . $email_footer;

			//if data is a string, assume we mean to replace !!body!! with it
			if(is_string($this->data))
				$this->data = array("body"=>$data);											
				
			//filter for data
			$this->data = apply_filters("pmpro_email_data", $this->data, $this);	//filter
			
			//swap data into body and subject line
			if(is_array($this->data))
			{
				foreach($this->data as $key => $value)
				{
					if ( 'body' != $key ) {
						$this->body = str_replace("!!" . $key . "!!", $value, $this->body);
						$this->subject = str_replace("!!" . $key . "!!", $value, $this->subject);
					}
				}
			}
			
			//filters
			$temail = apply_filters("pmpro_email_filter", $this);		//allows filtering entire email at once

			if ( empty( $temail ) ) {
				return false;
			}

			$this->email = apply_filters("pmpro_email_recipient", $temail->email, $this);
			$this->from = apply_filters("pmpro_email_sender", $temail->from, $this);
			$this->fromname = apply_filters("pmpro_email_sender_name", $temail->fromname, $this);
			$this->add_from_to_headers();
			$this->subject = apply_filters("pmpro_email_subject", $temail->subject, $this);
			$this->template = apply_filters("pmpro_email_template", $temail->template, $this);
			$this->body = apply_filters("pmpro_email_body", $temail->body, $this);
			$this->headers = apply_filters("pmpro_email_headers", $temail->headers, $this);
			$this->attachments = apply_filters("pmpro_email_attachments", $temail->attachments, $this);
			
			if(wp_mail($this->email,$this->subject,$this->body,$this->headers,$this->attachments))
			{
				return true;
			}
			else
			{
				return false;
			}		
		}
		
		/**
		 * Add the From Name and Email to the headers.
		 * @since 2.1
		 */
		function add_from_to_headers() {
			// Make sure we have a headers array
			if ( empty( $this->headers ) ) {
				$this->headers = array();
			} elseif ( ! is_array( $this->headers ) ) {
				$this->headers = array( $this->headers );
			}
			
			// Remove any previous from header
			foreach( $this->headers as $key => $header ) {
				if( strtolower( substr( $header, 0, 5 ) ) == 'from:' ) {
					unset( $this->headers[$key] );
				}
			}
			
			// Add From Email and Name or Just Email
			if( !empty( $this->from ) && !empty( $this->fromname ) ) {
				$this->headers[] = 'From:' . $this->fromname . ' <' . $this->from . '>'; 
			} elseif( !empty( $this->from ) ) {
				$this->headers[] = 'From:' . $this->from;
			}
		}
		
		function sendCancelEmail($user = NULL, $old_level_id = NULL)
		{
			global $wpdb, $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
			
			$this->email = $user->user_email;
			$this->subject = sprintf(__('Your membership at %s has been CANCELLED', 'paid-memberships-pro'), get_option("blogname"));

			$this->data = array("user_email" => $user->user_email, "display_name" => $user->display_name, "user_login" => $user->user_login, "sitename" => get_option("blogname"), "siteemail" => pmpro_getOption("from_email"));

			if(!empty($old_level_id)) {
				if(!is_array($old_level_id))
					$old_level_id = array($old_level_id);
				$this->data['membership_id'] = $old_level_id[0];	//pass just the first as the level id
				$this->data['membership_level_name'] = pmpro_implodeToEnglish($wpdb->get_col("SELECT name FROM $wpdb->pmpro_membership_levels WHERE id IN('" . implode("','", $old_level_id) . "')"));
			} else {
				$this->data['membership_id'] = '';
				$this->data['membership_level_name'] = __('All Levels', 'paid-memberships-pro' );
			}

			$this->template = apply_filters("pmpro_email_template", "cancel", $this);
			return $this->sendEmail();
		}
		
		function sendCancelAdminEmail($user = NULL, $old_level_id = NULL)
		{
			global $wpdb, $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
			
			$this->email = get_bloginfo("admin_email");
			$this->subject = sprintf(__("Membership for %s at %s has been CANCELLED", 'paid-memberships-pro'), $user->user_login, get_option("blogname"));			

			$this->data = array("user_login" => $user->user_login, "user_email" => $user->user_email, "display_name" => $user->display_name, "sitename" => get_option("blogname"), "siteemail" => pmpro_getOption("from_email"), "login_link" => pmpro_login_url(), "login_url" => pmpro_login_url());
			
			if(!empty($old_level_id)) {
				if(!is_array($old_level_id))
					$old_level_id = array($old_level_id);
				$this->data['membership_id'] = $old_level_id[0];	//pass just the first as the level id
				$this->data['membership_level_name'] = pmpro_implodeToEnglish($wpdb->get_col("SELECT name FROM $wpdb->pmpro_membership_levels WHERE id IN('" . implode("','", $old_level_id) . "')"));

				//start and end date
				$startdate = $wpdb->get_var("SELECT UNIX_TIMESTAMP(CONVERT_TZ(startdate, '+00:00', @@global.time_zone)) as startdate FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND membership_id = '" . $old_level_id[0] . "' AND status IN('inactive', 'cancelled', 'admin_cancelled') ORDER BY id DESC");
				if(!empty($startdate))
					$this->data['startdate'] = date_i18n(get_option('date_format'), $startdate);
				else
					$this->data['startdate'] = "";
				$enddate = $wpdb->get_var("SELECT UNIX_TIMESTAMP(CONVERT_TZ(enddate, '+00:00', @@global.time_zone)) as enddate FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND membership_id = '" . $old_level_id[0] . "' AND status IN('inactive', 'cancelled', 'admin_cancelled') ORDER BY id DESC");
				if(!empty($enddate))
					$this->data['enddate'] = date_i18n(get_option('date_format'), $enddate);
				else
					$this->data['enddate'] = "";
			} else {
				$this->data['membership_id'] = '';
				$this->data['membership_level_name'] = __('All Levels', 'paid-memberships-pro' );
				$this->data['startdate'] = '';
				$this->data['enddate'] = '';
			}

			$this->template = apply_filters("pmpro_email_template", "cancel_admin", $this);

			return $this->sendEmail();
		}
		
		function sendCheckoutEmail($user = NULL, $invoice = NULL)
		{
			global $wpdb, $current_user, $discount_code;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
				
			$confirmation_in_email = get_pmpro_membership_level_meta( $user->membership_level->id, 'confirmation_in_email', true );
			if ( ! empty( $confirmation_in_email ) ) {
				$confirmation_message = $user->membership_level->confirmation;
			} else {
				$confirmation_message = '';
			}
			
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Your membership confirmation for %s", 'paid-memberships-pro' ), get_option("blogname"));	
			
			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"display_name" => $user->display_name,
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $user->membership_level->id,
								"membership_level_name" => $user->membership_level->name,
								"membership_level_confirmation_message" => $confirmation_message,
								"membership_cost" => pmpro_getLevelCost($user->membership_level),								
								"login_link" => pmpro_login_url(),
								"login_url" => pmpro_login_url(),
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,								
							);						
			
			// Figure out which template to use.
			if ( empty( $this->template ) ) {
				if( ! empty( $invoice ) && ! pmpro_isLevelFree( $user->membership_level ) ) {
					if( $invoice->gateway == "paypalexpress") {
						$this->template = "checkout_express";
					} elseif( $invoice->gateway == "check" ) {
						$this->template = "checkout_check";						
					} elseif( pmpro_isLevelTrial( $user->membership_level ) ) {
						$this->template = "checkout_trial";
					} else {
						$this->template = "checkout_paid";
					}										
				} elseif( pmpro_isLevelFree( $user->membership_level ) ) {
					$this->template = "checkout_free";					
				} else {
					$this->template = "checkout_freetrial";					
				}
			}
			
			$this->template = apply_filters( "pmpro_email_template", $this->template, $this );
			
			// Gather data depending on template being used.
			if( in_array( $this->template, array( 'checkout_express', 'checkout_check', 'checkout_trial', 'checkout_paid' ) ) ) {									
				if( $this->template === 'checkout_check' ) {					
					$this->data["instructions"] = wpautop(pmpro_getOption("instructions"));
				}
				
				$this->data["invoice_id"] = $invoice->code;
				$this->data["invoice_total"] = pmpro_formatPrice($invoice->total);
				$this->data["invoice_date"] = date_i18n( get_option( 'date_format' ), $invoice->getTimestamp() );
				$this->data["billing_name"] = $invoice->billing->name;
				$this->data["billing_street"] = $invoice->billing->street;
				$this->data["billing_city"] = $invoice->billing->city;
				$this->data["billing_state"] = $invoice->billing->state;
				$this->data["billing_zip"] = $invoice->billing->zip;
				$this->data["billing_country"] = $invoice->billing->country;
				$this->data["billing_phone"] = $invoice->billing->phone;
				$this->data["cardtype"] = $invoice->cardtype;
				$this->data["accountnumber"] = hideCardNumber($invoice->accountnumber);
				$this->data["expirationmonth"] = $invoice->expirationmonth;
				$this->data["expirationyear"] = $invoice->expirationyear;
				$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																	 $invoice->billing->street,
																	 "", //address 2
																	 $invoice->billing->city,
																	 $invoice->billing->state,
																	 $invoice->billing->zip,
																	 $invoice->billing->country,
																	 $invoice->billing->phone);
				
				if( $invoice->getDiscountCode() ) {
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $invoice->discount_code->code . "</p>\n";
				} else {
					$this->data["discount_code"] = "";
				}
			} elseif( $this->template === 'checkout_free' ) {				
				if( ! empty( $discount_code ) ) {
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $discount_code . "</p>\n";		
				} else {
					$this->data["discount_code"] = "";
				}
			} elseif ( $this->template === 'checkout_freetrial' ) {				
				if( ! empty( $discount_code ) ) {
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $discount_code . "</p>\n";		
				} else {
					$this->data["discount_code"] = "";
				}
			}
			
			$enddate = $wpdb->get_var("SELECT UNIX_TIMESTAMP(CONVERT_TZ(enddate, '+00:00', @@global.time_zone)) FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND status = 'active' LIMIT 1");
			if( $enddate ) {
				$this->data["membership_expiration"] = "<p>" . sprintf(__("This membership will expire on %s.", 'paid-memberships-pro' ), date_i18n(get_option('date_format'), $enddate)) . "</p>\n";
			} else {
				$this->data["membership_expiration"] = "";
			}			
			
			return $this->sendEmail();
		}
		
		function sendCheckoutAdminEmail($user = NULL, $invoice = NULL)
		{
			global $wpdb, $current_user, $discount_code;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
			
			$this->email = get_bloginfo("admin_email");
			$this->subject = sprintf(__("Member checkout for %s at %s", 'paid-memberships-pro' ), $user->membership_level->name, get_option("blogname"));	
			
			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $user->membership_level->id,
								"membership_level_name" => $user->membership_level->name,
								"membership_cost" => pmpro_getLevelCost($user->membership_level),								
								"login_link" => pmpro_login_url(),
								"login_url" => pmpro_login_url(),
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,								
							);						
			
			// Figure out which template to use.
			if ( empty( $this->template ) ) {
				if( ! empty( $invoice ) && ! pmpro_isLevelFree( $user->membership_level ) ) {
					if( $invoice->gateway == "paypalexpress") {
						$this->template = "checkout_express_admin";
					} elseif( $invoice->gateway == "check" ) {
						$this->template = "checkout_check_admin";						
					} elseif( pmpro_isLevelTrial( $user->membership_level ) ) {
						$this->template = "checkout_trial_admin";
					} else {
						$this->template = "checkout_paid_admin";
					}										
				} elseif( pmpro_isLevelFree( $user->membership_level ) ) {
					$this->template = "checkout_free_admin";					
				} else {
					$this->template = "checkout_freetrial_admin";					
				}
			}
			
			$this->template = apply_filters( "pmpro_email_template", $this->template, $this );
			
			// Gather data depending on template being used.
			if( in_array( $this->template, array( 'checkout_express_admin', 'checkout_check_admin', 'checkout_trial_admin', 'checkout_paid_admin' ) ) ) {
				$this->data["invoice_id"] = $invoice->code;
				$this->data["invoice_total"] = pmpro_formatPrice($invoice->total);
				$this->data["invoice_date"] = date_i18n(get_option('date_format'), $invoice->getTimestamp());
				$this->data["billing_name"] = $invoice->billing->name;
				$this->data["billing_street"] = $invoice->billing->street;
				$this->data["billing_city"] = $invoice->billing->city;
				$this->data["billing_state"] = $invoice->billing->state;
				$this->data["billing_zip"] = $invoice->billing->zip;
				$this->data["billing_country"] = $invoice->billing->country;
				$this->data["billing_phone"] = $invoice->billing->phone;
				$this->data["cardtype"] = $invoice->cardtype;
				$this->data["accountnumber"] = hideCardNumber($invoice->accountnumber);
				$this->data["expirationmonth"] = $invoice->expirationmonth;
				$this->data["expirationyear"] = $invoice->expirationyear;
				$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																	 $invoice->billing->street,
																	 "", //address 2
																	 $invoice->billing->city,
																	 $invoice->billing->state,
																	 $invoice->billing->zip,
																	 $invoice->billing->country,
																	 $invoice->billing->phone);
				
				if( $invoice->getDiscountCode() ) {
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $invoice->discount_code->code . "</p>\n";
				} else {
					$this->data["discount_code"] = "";
				}
			} elseif( $this->template === 'checkout_free_admin' ) {				
				if( ! empty( $discount_code ) ) {
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $discount_code . "</p>\n";		
				} else {
					$this->data["discount_code"] = "";
				}
			} elseif( $this->template === 'checkout_freetrial_admin' ) {				
				$this->data["discount_code"] = "";
			}
			
			$enddate = $wpdb->get_var("SELECT UNIX_TIMESTAMP(CONVERT_TZ(enddate, '+00:00', @@global.time_zone)) FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND status = 'active' LIMIT 1");
			if( $enddate ) {
				$this->data["membership_expiration"] = "<p>" . sprintf(__("This membership will expire on %s.", 'paid-memberships-pro' ), date_i18n(get_option('date_format'), $enddate)) . "</p>\n";
			} else {
				$this->data["membership_expiration"] = "";
			}
			
			return $this->sendEmail();
		}
		
		function sendBillingEmail($user = NULL, $invoice = NULL)
		{
			global $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$invoice)
				return false;
			
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Your billing information has been updated at %s", "paid-memberships-pro"), get_option("blogname"));

			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $user->membership_level->id,
								"membership_level_name" => $user->membership_level->name,
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,																	
								"billing_name" => $invoice->billing->name,
								"billing_street" => $invoice->billing->street,
								"billing_city" => $invoice->billing->city,
								"billing_state" => $invoice->billing->state,
								"billing_zip" => $invoice->billing->zip,
								"billing_country" => $invoice->billing->country,
								"billing_phone" => $invoice->billing->phone,
								"cardtype" => $invoice->cardtype,
								"accountnumber" => hideCardNumber($invoice->accountnumber),
								"expirationmonth" => $invoice->expirationmonth,
								"expirationyear" => $invoice->expirationyear,
								"login_link" => pmpro_login_url(),
								"login_url" => pmpro_login_url(),
							);
			$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																 $invoice->billing->street,
																 "", //address 2
																 $invoice->billing->city,
																 $invoice->billing->state,
																 $invoice->billing->zip,
																 $invoice->billing->country,
																 $invoice->billing->phone);

			$this->template = apply_filters( "pmpro_email_template", "billing", $this );

			return $this->sendEmail();
		}
		
		function sendBillingAdminEmail($user = NULL, $invoice = NULL)
		{
			global $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$invoice)
				return false;
			
			$this->email = get_bloginfo("admin_email");
			$this->subject = sprintf(__("Billing information has been updated for %s at %s", "paid-memberships-pro"), $user->user_login, get_option("blogname"));
			
			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $user->membership_level->id,
								"membership_level_name" => $user->membership_level->name,
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,																	
								"billing_name" => $invoice->billing->name,
								"billing_street" => $invoice->billing->street,
								"billing_city" => $invoice->billing->city,
								"billing_state" => $invoice->billing->state,
								"billing_zip" => $invoice->billing->zip,
								"billing_country" => $invoice->billing->country,
								"billing_phone" => $invoice->billing->phone,
								"cardtype" => $invoice->cardtype,
								"accountnumber" => hideCardNumber($invoice->accountnumber),
								"expirationmonth" => $invoice->expirationmonth,
								"expirationyear" => $invoice->expirationyear,
								"login_link" => pmpro_login_url(),
								"login_url" => pmpro_login_url(),
							);
			$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																 $invoice->billing->street,
																 "", //address 2
																 $invoice->billing->city,
																 $invoice->billing->state,
																 $invoice->billing->zip,
																 $invoice->billing->country,
																 $invoice->billing->phone);

			$this->template = apply_filters( "pmpro_email_template", "billing_admin", $this );

			return $this->sendEmail();
		}
		
		function sendBillingFailureEmail($user = NULL, $invoice = NULL)
		{
			global $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$invoice)
				return false;

			$membership_level = pmpro_getSpecificMembershipLevelForUser( $user->ID, $invoice->membership_id );
			
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Membership payment failed at %s", "paid-memberships-pro"), get_option("blogname"));
			
			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $membership_level->id,
								"membership_level_name" => $membership_level->name,
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,									
								"billing_name" => $invoice->billing->name,
								"billing_street" => $invoice->billing->street,
								"billing_city" => $invoice->billing->city,
								"billing_state" => $invoice->billing->state,
								"billing_zip" => $invoice->billing->zip,
								"billing_country" => $invoice->billing->country,
								"billing_phone" => $invoice->billing->phone,
								"cardtype" => $invoice->cardtype,
								"accountnumber" => hideCardNumber($invoice->accountnumber),
								"expirationmonth" => $invoice->expirationmonth,
								"expirationyear" => $invoice->expirationyear,
								"login_link" => pmpro_login_url(pmpro_url("billing")),
								"login_url" => pmpro_login_url(pmpro_url("billing")),
							);
			$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																 $invoice->billing->street,
																 "", //address 2
																 $invoice->billing->city,
																 $invoice->billing->state,
																 $invoice->billing->zip,
																 $invoice->billing->country,
																 $invoice->billing->phone);

			$this->template = apply_filters("pmpro_email_template", "billing_failure", $this);

			return $this->sendEmail();
		}				
		
		function sendBillingFailureAdminEmail($email, $invoice = NULL)
		{		
			if(!$invoice)			
				return false;
				
			$user = get_userdata($invoice->user_id);
			$membership_level = pmpro_getSpecificMembershipLevelForUser( $user->ID, $invoice->membership_id );
			
			$this->email = $email;
			$this->subject = sprintf(__("Membership payment failed For %s at %s", "paid-memberships-pro"), $user->display_name, get_option("blogname"));
			
			$this->data = array(
								"subject" => $this->subject, 
								"name" => "Admin", 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $membership_level->id,
								"membership_level_name" => $membership_level->name,
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,									
								"billing_name" => $invoice->billing->name,
								"billing_street" => $invoice->billing->street,
								"billing_city" => $invoice->billing->city,
								"billing_state" => $invoice->billing->state,
								"billing_zip" => $invoice->billing->zip,
								"billing_country" => $invoice->billing->country,
								"billing_phone" => $invoice->billing->phone,
								"cardtype" => $invoice->cardtype,
								"accountnumber" => hideCardNumber($invoice->accountnumber),
								"expirationmonth" => $invoice->expirationmonth,
								"expirationyear" => $invoice->expirationyear,
								"login_link" => pmpro_login_url( get_edit_user_link( $user->ID ) ),
								"login_url" => pmpro_login_url( get_edit_user_link( $user->ID ) ),
							);
			$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																 $invoice->billing->street,
																 "", //address 2
																 $invoice->billing->city,
																 $invoice->billing->state,
																 $invoice->billing->zip,
																 $invoice->billing->country,
																 $invoice->billing->phone);
			$this->template = apply_filters("pmpro_email_template", "billing_failure_admin", $this);

			return $this->sendEmail();
		}
		
		function sendCreditCardExpiringEmail($user = NULL, $invoice = NULL)
		{
			global $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$invoice)
				return false;
			
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Credit card on file expiring soon at %s", "paid-memberships-pro"), get_option("blogname"));
			
			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $user->membership_level->id,
								"membership_level_name" => $user->membership_level->name,
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,									
								"billing_name" => $invoice->billing->name,
								"billing_street" => $invoice->billing->street,
								"billing_city" => $invoice->billing->city,
								"billing_state" => $invoice->billing->state,
								"billing_zip" => $invoice->billing->zip,
								"billing_country" => $invoice->billing->country,
								"billing_phone" => $invoice->billing->phone,
								"cardtype" => $invoice->cardtype,
								"accountnumber" => hideCardNumber($invoice->accountnumber),
								"expirationmonth" => $invoice->expirationmonth,
								"expirationyear" => $invoice->expirationyear,
								"login_link" => pmpro_login_url(pmpro_url("billing")),
								"login_url" => pmpro_login_url(pmpro_url("billing"))
							);
			$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																 $invoice->billing->street,
																 "", //address 2
																 $invoice->billing->city,
																 $invoice->billing->state,
																 $invoice->billing->zip,
																 $invoice->billing->country,
																 $invoice->billing->phone);

			$this->template = apply_filters("pmpro_email_template", "credit_card_expiring", $this);

			return $this->sendEmail();
		}
		
		function sendInvoiceEmail($user = NULL, $invoice = NULL)
		{
			global $wpdb, $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$invoice)
				return false;
			
			$user->membership_level = pmpro_getMembershipLevelForUser($user->ID);
			
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Invoice for %s membership", "paid-memberships-pro"), get_option("blogname"));

			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"membership_id" => $user->membership_level->id,
								"membership_level_name" => $user->membership_level->name,
								"display_name" => $user->display_name,
								"user_email" => $user->user_email,	
								"invoice_id" => $invoice->code,
								"invoice_total" => pmpro_formatPrice($invoice->total),
								"invoice_date" => date_i18n(get_option('date_format'), $invoice->getTimestamp()),
								"billing_name" => $invoice->billing->name,
								"billing_street" => $invoice->billing->street,
								"billing_city" => $invoice->billing->city,
								"billing_state" => $invoice->billing->state,
								"billing_zip" => $invoice->billing->zip,
								"billing_country" => $invoice->billing->country,
								"billing_phone" => $invoice->billing->phone,
								"cardtype" => $invoice->cardtype,
								"accountnumber" => hideCardNumber($invoice->accountnumber),
								"expirationmonth" => $invoice->expirationmonth,
								"expirationyear" => $invoice->expirationyear,
								"login_link" => pmpro_login_url(),
								"login_url" => pmpro_login_url(),
								"invoice_link" => pmpro_login_url(pmpro_url("invoice", "?invoice=" . $invoice->code)),
								"invoice_url" => pmpro_login_url(pmpro_url("invoice", "?invoice=" . $invoice->code))
							);
			$this->data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
																 $invoice->billing->street,
																 "", //address 2
																 $invoice->billing->city,
																 $invoice->billing->state,
																 $invoice->billing->zip,
																 $invoice->billing->country,
																 $invoice->billing->phone);
		
			if($invoice->getDiscountCode()) {
				if(!empty($invoice->discount_code->code))
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $invoice->discount_code->code . "</p>\n";
				else
					$this->data["discount_code"] = "<p>" . __("Discount Code", 'paid-memberships-pro' ) . ": " . $invoice->discount_code . "</p>\n";
			} else {
				$this->data["discount_code"] = "";
			}
		
			$enddate = $wpdb->get_var("SELECT UNIX_TIMESTAMP(CONVERT_TZ(enddate, '+00:00', @@global.time_zone)) FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $user->ID . "' AND status = 'active' LIMIT 1");
			if($enddate)
				$this->data["membership_expiration"] = "<p>" . sprintf(__("This membership will expire on %s.", 'paid-memberships-pro' ), date_i18n(get_option('date_format'), $enddate)) . "</p>\n";
			else
				$this->data["membership_expiration"] = "";


			$this->template = apply_filters("pmpro_email_template", "invoice", $this);

			return $this->sendEmail();
		}
		
		function sendTrialEndingEmail($user = NULL)
		{
			global $current_user, $wpdb;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
			
			//make sure we have the current membership level data
			/*$user->membership_level = $wpdb->get_row("SELECT l.id AS ID, l.name AS name, UNIX_TIMESTAMP(CONVERT_TZ(mu.startdate, '+00:00', @@global.time_zone)) as startdate, mu.billing_amount, mu.cycle_number, mu.cycle_period, mu.trial_amount, mu.trial_limit
														FROM {$wpdb->pmpro_membership_levels} AS l
														JOIN {$wpdb->pmpro_memberships_users} AS mu ON (l.id = mu.membership_id)
														WHERE mu.user_id = " . $user->ID . "
														LIMIT 1");*/
			$user->membership_level = pmpro_getMembershipLevelForUser($user->ID);
						
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Your trial at %s is ending soon", "paid-memberships-pro"), get_option("blogname"));

			$this->data = array(
				"subject" => $this->subject, 
				"name" => $user->display_name, 
				"user_login" => $user->user_login,
				"sitename" => get_option("blogname"), 				
				"membership_id" => $user->membership_level->id,
				"membership_level_name" => $user->membership_level->name, 
				"siteemail" => pmpro_getOption("from_email"), 
				"login_link" => pmpro_login_url(),
				"login_url" => pmpro_login_url(),
				"display_name" => $user->display_name, 
				"user_email" => $user->user_email, 
				"billing_amount" => pmpro_formatPrice($user->membership_level->billing_amount), 
				"cycle_number" => $user->membership_level->cycle_number, 
				"cycle_period" => $user->membership_level->cycle_period, 
				"trial_amount" => pmpro_formatPrice($user->membership_level->trial_amount), 
				"trial_limit" => $user->membership_level->trial_limit,
				"trial_end" => date_i18n(get_option('date_format'), strtotime(date_i18n("m/d/Y", $user->membership_level->startdate) . " + " . $user->membership_level->trial_limit . " " . $user->membership_level->cycle_period), current_time("timestamp"))
			);

			$this->template = apply_filters("pmpro_email_template", "trial_ending", $this);

			return $this->sendEmail();
		}
		
		function sendMembershipExpiredEmail($user = NULL)
		{
			global $current_user, $wpdb;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;						
						
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Your membership at %s has ended", "paid-memberships-pro"), get_option("blogname"));			

			$this->data = array("subject" => $this->subject, "name" => $user->display_name, "user_login" => $user->user_login, "sitename" => get_option("blogname"), "siteemail" => pmpro_getOption("from_email"), "login_link" => pmpro_login_url(), "login_url" => pmpro_login_url(), "display_name" => $user->display_name, "user_email" => $user->user_email, "levels_link" => pmpro_url("levels"), "levels_url" => pmpro_url("levels"));

			$this->template = apply_filters("pmpro_email_template", "membership_expired", $this);

			return $this->sendEmail();
		}
		
		function sendMembershipExpiringEmail($user = NULL)
		{
			global $current_user, $wpdb;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
			
			//make sure we have the current membership level data
			/*$user->membership_level = $wpdb->get_row("SELECT l.id AS ID, l.name AS name, UNIX_TIMESTAMP(CONVERT_TZ(mu.enddate, '+00:00', @@global.time_zone)) as enddate
														FROM {$wpdb->pmpro_membership_levels} AS l
														JOIN {$wpdb->pmpro_memberships_users} AS mu ON (l.id = mu.membership_id)
														WHERE mu.user_id = " . $user->ID . "
														LIMIT 1");*/
			$user->membership_level = pmpro_getMembershipLevelForUser($user->ID);
						
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Your membership at %s will end soon", "paid-memberships-pro"), get_option("blogname"));

			$this->data = array("subject" => $this->subject, "name" => $user->display_name, "user_login" => $user->user_login, "sitename" => get_option("blogname"), "membership_id" => $user->membership_level->id, "membership_level_name" => $user->membership_level->name, "siteemail" => pmpro_getOption("from_email"), "login_link" => pmpro_login_url(), "login_url" => pmpro_login_url(), "enddate" => date_i18n(get_option('date_format'), $user->membership_level->enddate), "display_name" => $user->display_name, "user_email" => $user->user_email);

			$this->template = apply_filters("pmpro_email_template", "membership_expiring", $this);

			return $this->sendEmail();
		}
		
		function sendAdminChangeEmail($user = NULL)
		{
			global $current_user, $wpdb;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;
			
			//make sure we have the current membership level data
			$user->membership_level = pmpro_getMembershipLevelForUser($user->ID, true);

			if(!empty($user->membership_level) && !empty($user->membership_level->name)) {
				$membership_level_name = $user->membership_level->name;
				$membership_level_id = $user->membership_level->id;
			} else {
				$membership_level_name = __('None', 'paid-memberships-pro');
				$membership_level_id = '';
			}
						
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Your membership at %s has been changed", "paid-memberships-pro"), get_option("blogname"));

			$this->data = array("subject" => $this->subject, "name" => $user->display_name, "display_name" => $user->display_name, "user_login" => $user->user_login, "user_email" => $user->user_email, "sitename" => get_option("blogname"), "membership_id" => $membership_level_id, "membership_level_name" => $membership_level_name, "siteemail" => pmpro_getOption("from_email"), "login_link" => pmpro_login_url(), "login_url" => pmpro_login_url());

			if(!empty($user->membership_level) && !empty($user->membership_level->ID)) {
				$this->data["membership_change"] = sprintf(__("The new level is %s.", 'paid-memberships-pro' ), $user->membership_level->name);
			} else {
				$this->data["membership_change"] = __("Your membership has been cancelled.", "paid-memberships-pro");
			}

			if(!empty($user->membership_level->enddate))
			{
					$this->data["membership_change"] .= " " . sprintf(__("This membership will expire on %s.", 'paid-memberships-pro' ), date_i18n(get_option('date_format'), $user->membership_level->enddate));
			}
			elseif(!empty($this->expiration_changed))
			{
				$this->data["membership_change"] .= " " . __("This membership does not expire.", 'paid-memberships-pro' );
			}

			$this->template = apply_filters("pmpro_email_template", "admin_change", $this);

			return $this->sendEmail();
		}
		
		function sendAdminChangeAdminEmail($user = NULL)
		{
			global $current_user, $wpdb;
			if(!$user)
				$user = $current_user;
			
			if(!$user)
				return false;

			//make sure we have the current membership level data
			$user->membership_level = pmpro_getMembershipLevelForUser($user->ID, true);
						
			if(!empty($user->membership_level) && !empty($user->membership_level->name)) {
				$membership_level_name = $user->membership_level->name;
				$membership_level_id = $user->membership_level->id;
			} else {
				$membership_level_name = __('None', 'paid-memberships-pro');
				$membership_level_id = '';
			}

			$this->email = get_bloginfo("admin_email");
			$this->subject = sprintf(__("Membership for %s at %s has been changed", "paid-memberships-pro"), $user->user_login, get_option("blogname"));

			$this->data = array("subject" => $this->subject, "name" => $user->display_name, "display_name" => $user->display_name, "user_login" => $user->user_login, "user_email" => $user->user_email, "sitename" => get_option("blogname"), "membership_id" => $membership_level_id, "membership_level_name" => $membership_level_name, "siteemail" => get_bloginfo("admin_email"), "login_link" => pmpro_login_url(), "login_url" => pmpro_login_url());

			if(!empty($user->membership_level) && !empty($user->membership_level->ID)) {
				$this->data["membership_change"] = sprintf(__("The new level is %s.", 'paid-memberships-pro' ), $user->membership_level->name);
			} else {
				$this->data["membership_change"] = __("Membership has been cancelled.", 'paid-memberships-pro' );	
			}
			
			if(!empty($user->membership_level) && !empty($user->membership_level->enddate))
			{
					$this->data["membership_change"] .= " " . sprintf(__("This membership will expire on %s.", 'paid-memberships-pro' ), date_i18n(get_option('date_format'), $user->membership_level->enddate));
			}
			elseif(!empty($this->expiration_changed))
			{
				$this->data["membership_change"] .= " " . __("This membership does not expire.", 'paid-memberships-pro' );
			}

			$this->template = apply_filters("pmpro_email_template", "admin_change_admin", $this);

			return $this->sendEmail();
		}

		/**
		 * Send billable invoice email.
		 *
		 * @since 1.8.6
		 *
		 * @param WP_User $user
		 * @param MemberOrder $order
		 *
		 * @return bool Whether the email was sent successfully.
		 */
		function sendBillableInvoiceEmail($user = NULL, $order = NULL)
		{
			global $current_user;

			if(!$user)
				$user = $current_user;

			if(!$user || !$order)
				return false;

			$level = pmpro_getLevel($order->membership_id);

			$this->email = $user->user_email;
			$this->subject = __('Invoice for order #: ', 'paid-memberships-pro') . $order->code;

			// Load invoice template
			if ( file_exists( get_stylesheet_directory() . '/paid-memberships-pro/pages/orders-email.php' ) ) {
				$template = get_stylesheet_directory() . '/paid-memberships-pro/pages/orders-email.php';
			} elseif ( file_exists( get_template_directory() . '/paid-memberships-pro/pages/orders-email.php' ) ) {
				$template = get_template_directory() . '/paid-memberships-pro/pages/orders-email.php';
			} else {
				$template = PMPRO_DIR . '/adminpages/templates/orders-email.php';
			}

			ob_start();
			require_once( $template );

			$invoice = ob_get_contents();
			ob_end_clean();

			$this->data = array(
				'order_code' => $order->code,
				'login_link' => pmpro_login_url(),
				'login_url' => pmpro_login_url(),
				'invoice_link' => pmpro_login_url(pmpro_url("invoice", "?invoice=" . $order->code)),
				'invoice_url' => pmpro_login_url(pmpro_url("invoice", "?invoice=" . $order->code)),
				"invoice_id" => $order->id,
				'invoice' => $invoice
			);

			$this->template = apply_filters("pmpro_email_template", "billable_invoice", $this);

			return $this->sendEmail();
		}

		function sendPaymentActionRequiredEmail($user = NULL, $order = NULL, $invoice_url = NULL)
		{
			global $wpdb, $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$order)
				return false;

			// if an invoice URL wasn't passed in, grab it from the order
			if(empty($invoice_url) && isset($order->invoice_url))
				$invoice_url = $order->invoice_url;

			// still no invoice URL? bail
			if(empty($invoice_url))
				return false;
				
			$this->email = $user->user_email;
			$this->subject = sprintf(__("Payment action required for your %s membership", 'paid-memberships-pro' ), get_option("blogname"));	
			
			$this->template = "payment_action";

			$this->template = apply_filters("pmpro_email_template", $this->template, $this);

			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"display_name" => $user->display_name,
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"invoice_link" => $invoice_url,
								"invoice_url" => $invoice_url,
							);
						
			return $this->sendEmail();
		}

		function sendPaymentActionRequiredAdminEmail($user = NULL, $order = NULL, $invoice_url = NULL)
		{
			global $wpdb, $current_user;
			if(!$user)
				$user = $current_user;
			
			if(!$user || !$order)
				return false;

			// if an invoice URL wasn't passed in, grab it from the order
			if(empty($invoice_url) && isset($order->invoice_url))
				$invoice_url = $order->invoice_url;

			// still no invoice URL? bail
			if(empty($invoice_url))
				return false;
				
			$this->email = get_bloginfo("admin_email");
			$this->subject = sprintf(__("Payment action required: membership for %s at %s", 'paid-memberships-pro' ), $user->user_login, get_option("blogname"));	
			
			$this->template = "payment_action_admin";

			$this->template = apply_filters("pmpro_email_template", $this->template, $this);

			$this->data = array(
								"subject" => $this->subject, 
								"name" => $user->display_name, 
								"display_name" => $user->display_name,
								"user_login" => $user->user_login,
								"sitename" => get_option("blogname"),
								"siteemail" => pmpro_getOption("from_email"),
								"user_email" => $user->user_email,
								"invoice_link" => $invoice_url,
								"invoice_url" => $invoice_url,
							);
						
			return $this->sendEmail();
		}
	}
