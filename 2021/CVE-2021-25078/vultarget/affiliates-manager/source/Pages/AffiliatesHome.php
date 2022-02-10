<?php
/**
 * @author John Hargrove
 * 
 * Date: May 24, 2010
 * Time: 9:21:19 PM
 */

class WPAM_Pages_AffiliatesHome extends WPAM_Pages_PublicPage
{
	private $response;
	
	public function __construct( $name, $title, WPAM_Pages_PublicPage $parentPage = NULL ) {
		parent::__construct($name, $title, $parentPage);
	}
	
	public function processRequest($request)
	{
                if(is_array($request)){
                    $request = wpam_sanitize_array($request);
                }
		$db = new WPAM_Data_DataAccess();

		if (is_user_logged_in())
		{
			$currentUser = wp_get_current_user();

			if ($db->getAffiliateRepository()->isUserAffiliate($currentUser->ID))
			{
				$affiliate = $db->getAffiliateRepository()->loadByUserId($currentUser->ID);
				if ($affiliate->isApproved() || $affiliate->isActive())
				{
                                        $home_page_id = get_option(WPAM_PluginConfig::$HomePageId);
                                        $home_page_url = get_permalink($home_page_id);
                                        $logout_url = wp_logout_url($home_page_url);
					$response = $this->doAffiliateControlPanel($affiliate, $request);
					$response->viewData['navigation'] = array(
						array( __( 'Overview', 'affiliates-manager' ), $this->getLink(array('sub' => 'overview'))),
						array( __( 'Sales', 'affiliates-manager' ), $this->getLink(array('sub' => 'sales'))),
						array( __( 'Payment History', 'affiliates-manager' ), $this->getLink(array('sub' => 'payments'))),
						array( __( 'Creatives', 'affiliates-manager' ), $this->getLink(array('sub' => 'creatives'))),
						array( __( 'Edit Profile', 'affiliates-manager' ), $this->getLink(array('sub' => 'profile'))),
                                                array( __( 'Log out', 'affiliates-manager' ), $logout_url),
					);

					if (get_option (WPAM_PluginConfig::$AffEnableImpressions))
						$response->viewData['navigation'][] = array( __( 'Impressions', 'affiliates-manager' ), $this->getLink(array('sub' => 'impressions')));
				}
				else if ($affiliate->isDeclined())
				{
					$response = $this->doDeclinedRequest($request);
				}
				else
				{
					$response = $this->doPendingApp();
				}
			}
			else
			{
				$response = $this->doAffiliateNotRegistered($request);
			}
		}
		else
		{
			// show log-in-to-register forms
			$response = $this->doAffiliateNotLoggedIn($request);
		}
		return $response;
	}

	public function doAffiliateNotLoggedIn($request)
	{
		$response = new WPAM_Pages_TemplateResponse('affiliate_not_logged_in');
		$response->viewData['loginUrl'] = wp_login_url($_SERVER['REQUEST_URI']);
		//$response->viewData['registerUrl'] = get_option('siteurl') . "/wp-register.php";
		$response->viewData['registerUrl'] = $this->getLink(array('page_id' => WPAM_Pages_AffiliatesRegister::getPageId()));
		return $response;
	}

	public function doAffiliateNotRegistered($request)
	{
		$response = new WPAM_Pages_TemplateResponse('affiliate_not_registered');
		$response->viewData['registerUrl'] = $this->getLink(array('page_id' => WPAM_Pages_AffiliatesRegister::getPageId()));
		return $response;
	}

	private function doPendingApp()
	{
		$response = new WPAM_Pages_TemplateResponse('affiliate_application_pending');
		return $response;
	}

	private function doHomeRequest($request)
	{
		$response = new WPAM_Pages_TemplateResponse('affiliate_home');
		return $response;
	}

	private function doDeclinedRequest($request)
	{
		$response = new WPAM_Pages_TemplateResponse('affiliate_application_declined');
		return $response;
	}

	protected function doConfirmed($request)
	{
		$response = new WPAM_Pages_TemplateResponse('affiliate_cp_payment_details_confirmed');
		$response->viewData['creativesLink'] = $this->getLink(array('sub' => 'creatives'));
		return $response;
	}

	protected function doInactive($request)
	{
		return new WPAM_Pages_TemplateResponse('affiliate_inactive');
	}

	public function doAffiliateControlPanel($model, $request)
	{
                if(is_array($request)){
                    $request = wpam_sanitize_array($request);
                }
		$user = wp_get_current_user();
		$db = new WPAM_Data_DataAccess();

		$affiliate = $db->getAffiliateRepository()->loadByUserId($user->ID);

		if ($affiliate === null)
			wp_die('affiliates only.');

		if ($affiliate->isApproved())
		{
			if (isset($request['action']) && $request['action'] == 'confirm')
			{
				return $this->doConfirm($request, $affiliate);
			}
			return $this->doApproved($request);
		}
		else if ($affiliate->isActive())
		{
			return $this->doHome($request, $affiliate);
		}
		else if ($affiliate->isInactive())
		{
			return $this->doInactive($request);
		}
		else if ($affiliate->isConfirmed())
		{
			return $this->doConfirmed($request);
		}
	}

	protected function doHome( $request, $affiliate )
	{
                if(is_array($request)){
                    $request = wpam_sanitize_array($request);
                }
		$sub = isset( $request['sub'] ) ? $request['sub'] : '';
		switch ( $sub )	{
			case 'overview':  return $this->doOverviewHome( $request, $affiliate );
			case 'sales':     return $this->doSales( $request, $affiliate );
			case 'payments':  return $this->doPayments( $request, $affiliate );
			case 'creatives': return $this->doCreatives( $request );
			case 'profile':   return $this->doContactInfo( $request, $affiliate );
			case 'impressions':
				if (get_option (WPAM_PluginConfig::$AffEnableImpressions))
					return $this->doImpressions( $request, $affiliate );
				else return $this->doOverviewHome( $request, $affiliate );
			default:          return $this->doOverviewHome( $request, $affiliate );
		}

	}

	protected function doCreatives($request)
	{
		$db = new WPAM_Data_DataAccess();


		if ( isset( $request['action'] ) && $request['action'] == 'detail')
		{
			$response = new WPAM_Pages_TemplateResponse('affiliate_creative_detail');
			$affiliate = $db->getAffiliateRepository()->loadByUserId(wp_get_current_user()->ID);
			$creative = $db->getCreativesRepository()->load((int)$request['creativeId']);

			if ($creative === NULL)
				wp_die( __( 'Invalid creative.', 'affiliates-manager' ) );
			if ($affiliate === NULL)
				wp_die( __( 'Invalid affiliate', 'affiliates-manager' ) );
			if (!$creative->isActive())
				wp_die( __( 'Inactive creative.', 'affiliates-manager' ) );

			$response->viewData['affiliate'] = $affiliate;
			$response->viewData['creative'] = $creative;

			$linkBuilder = new WPAM_Tracking_TrackingLinkBuilder($affiliate, $creative);
			$response->viewData['htmlPreview'] = $linkBuilder->getHtmlSnippet();
			$response->viewData['htmlSnippet'] = $linkBuilder->getImpressionHtmlSnippet();

			return $response;
		}

		$response = new WPAM_Pages_TemplateResponse('affiliate_creative_list');
		$response->viewData['creatives'] = $db->getCreativesRepository()->loadAllActiveNoDeletes();
		return $response;

	}



	protected function doOverviewHome($request, $affiliate)
	{
		$db = new WPAM_Data_DataAccess();
		$accountSummary = $db->getTransactionRepository()->getAccountSummary($affiliate->affiliateId);

                $args = array();
                $args['aff_id'] = $affiliate->affiliateId;
                //show total clicks for today
                $args['start_date'] = date("Y-m-d H:i:s", strtotime('today'));
                $args['end_date'] = date("Y-m-d H:i:s", strtotime('tomorrow'));
                $today_clicks = WPAM_Click_Tracking::get_total_clicks($args);
                //show total number of transactions for today
                $today_transaction_count = WPAM_Commission_Tracking::get_transaction_count($args);
                //show total commission for today
                $today_total_commission = WPAM_Commission_Tracking::get_total_commission_amount($args);
                //show total clicks for this month
                $args['start_date'] = date("Y-m-d H:i:s", strtotime(date("Y-m-01")));
                $args['end_date'] = date("Y-m-d H:i:s", strtotime(date("Y-m-01", strtotime("+1 month"))));
                $monthly_clicks = WPAM_Click_Tracking::get_total_clicks($args);
                //show total number of transactions for this month
                $monthly_transaction_count = WPAM_Commission_Tracking::get_transaction_count($args);
                //show total commission for this month
                $monthly_total_commission = WPAM_Commission_Tracking::get_total_commission_amount($args);
                
		$response = new WPAM_Pages_TemplateResponse('affiliate_cp_home');
		$response->viewData['accountStanding'] = $accountSummary->standing;
		$response->viewData['commissionRateString'] = $this->getCommissionRateString($affiliate);
		$response->viewData['monthVisitors'] = $monthly_clicks;//$eventSummary->visits;
		$response->viewData['monthClosedTransactions'] = $monthly_transaction_count;//$eventSummary->purchases;
		$response->viewData['monthRevenue'] = $monthly_total_commission;//$monthAccountSummary->credits;
		$response->viewData['todayVisitors'] = $today_clicks;//$todayEventSummary->visits;
		$response->viewData['todayClosedTransactions'] = $today_transaction_count;//$todayEventSummary->purchases;
		$response->viewData['todayRevenue'] = $today_total_commission;//$todayAccountSummary->credits;

		if (get_option (WPAM_PluginConfig::$AffEnableImpressions)) {
			$response->viewData['monthImpressions'] = $db->getImpressionRepository()->getImpressionsForRange(
				strtotime(date("Y-m-01")),
				strtotime(date("Y-m-01", strtotime("+1 month"))),
				$affiliate->affiliateId
			);
			$response->viewData['todayImpressions'] = $db->getImpressionRepository()->getImpressionsForRange(
				strtotime('today'),
				strtotime('tomorrow'),
				$affiliate->affiliateId
			);
		}

		return $response;

	}
	
	protected function doSales( $request, $affiliate ) {
		$response = new WPAM_Pages_TemplateResponse( 'affiliate_cp_transactions' );

		$where = array(
			'affiliateId' => $affiliate->affiliateId,
			'type' => 'credit' //load credits
		);

		$affiliateHelper = new WPAM_Util_AffiliateFormHelper();		
		$affiliateHelper->addTransactionDateRange( $where, $request, $response );
		
		$db = new WPAM_Data_DataAccess();
		$response->viewData['transactions'] = $db->getTransactionRepository()->loadMultipleBy(
			$where,
			array('dateCreated' => 'desc')
			);
		$response->viewData['showBalance'] = false;

		return $response;
	}

	protected function doPayments( $request, $affiliate ) {
		$response = new WPAM_Pages_TemplateResponse( 'affiliate_cp_transactions' );

		$where = array( 'affiliateId' => $affiliate->affiliateId,
						'type' => array( '!=', 'credit' ), //load payouts & adjustments
		);
		
		$affiliateHelper = new WPAM_Util_AffiliateFormHelper();		
		$affiliateHelper->addTransactionDateRange( $where, $request, $response );
		
		$db = new WPAM_Data_DataAccess();
		$response->viewData['transactions'] = $db->getTransactionRepository()->loadMultipleBy(
			$where,
			array('dateCreated' => 'desc')
			);

		return $response;
	}

	protected function doContactInfo( $request, $affiliate ) {
		add_action('wp_footer', array( $this, 'onFooter' ) );

		$db = new WPAM_Data_DataAccess();	   

		$affiliateFields = $db->getAffiliateFieldRepository()->loadMultipleBy(
			array('enabled' => true),
			array('order' => 'asc')
		);

		$response = new WPAM_Pages_TemplateResponse('affiliate_cp_contactinfo');
		$response->viewData['affiliateFields'] = $affiliateFields;

		$affiliateHelper = new WPAM_Util_AffiliateFormHelper();		
		$response->viewData['paymentMethods'] = $affiliateHelper->getPaymentMethods();
		$response->viewData['paymentMethod'] = isset( $request['ddPaymentMethod'] ) ? $request['ddPaymentMethod'] : $affiliate->paymentMethod;
		$response->viewData['paypalEmail'] = isset( $request['txtPaypalEmail'] ) ? $request['txtPaypalEmail'] : $affiliate->paypalEmail;

		$user = wp_get_current_user();
		
		if (isset($request['action']) && $request['action'] == 'saveInfo')
		{
                        if(!isset($request['_wpnonce']) || !wp_verify_nonce($request['_wpnonce'], 'wpam_add_affiliate')){
                            wp_die('Error! Nonce Security Check Failed! Go back to the page and submit again.');
                        }
			$validator = new WPAM_Validation_Validator();
			$validator->addValidator('ddPaymentMethod', new WPAM_Validation_SetValidator(array('check','paypal','manual')));
				
			if ($request['ddPaymentMethod'] === 'paypal') {
				$validator->addValidator('txtPaypalEmail', new WPAM_Validation_EmailValidator());
			}
			
			$vr = $affiliateHelper->validateForm($validator, $request, $affiliateFields, TRUE);
                        
                        //update the password if set
                        if(isset($_POST['_aff_New_Password']) && !empty($_POST['_aff_New_Password'])){
                            $aff_id = $affiliate->affiliateId;
                            $user_id = $affiliate->userId;
                            $new_password = sanitize_text_field($_POST['_aff_New_Password']);
                            $repeat_new_password = (isset($_POST['_aff_Repeat_New_Password']) && !empty($_POST['_aff_Repeat_New_Password'])) ? sanitize_text_field($_POST['_aff_Repeat_New_Password']) : '';
                            $update_pass = true;
                            // Check for "\" in password.
                            if ( false !== strpos( wp_unslash( $new_password ), "\\" ) ) {
                                $update_pass = false;                          
                                $vr->addError( new WPAM_Validation_ValidatorError( '_aff_New_Password', __(': Password may not contain slashes.', 'affiliates-manager') ) );
                            }                           
                            // Checking the password has been typed twice the same.
                            if ( ( ! empty( $new_password ) ) && $new_password != $repeat_new_password ) { 
                                $update_pass = false;
                                $vr->addError( new WPAM_Validation_ValidatorError( '_aff_New_Password', __(': Please enter the same password in both password fields.', 'affiliates-manager') ) );
                            }
                            if($update_pass){
                                wp_set_password( $new_password, $user_id );
                            }
                            //echo "affiliate ID: ".$aff_id.", user ID: ".$user_id.", password: ".$new_password;
                        }
                        
			if ($vr->getIsValid()) {
				//#79 hackery to do the "normal" WP email approval process
				require_once ABSPATH . 'wp-admin/includes/ms.php';
				$_POST['email'] = sanitize_email($request['_email']);
				$_POST['user_id'] = $user->ID;
				unset( $request['_email'] );
				global $errors;
				//*try* to save email
				send_confirmation_on_profile_email();
				if ( ! empty( $errors->errors ) ) {
					$vr->addError( new WPAM_Validation_ValidatorError( '_email', $_POST['email'] . " " . $errors->get_error_message( 'user_email' ) ) );
					$response->viewData['validationResult'] = $vr;
					$response->viewData['affiliate'] = $affiliate;
					//save for form validation in the footer
					$this->response = $response;
					return $response;
				}
				
				$affiliateHelper->setModelFromForm( $affiliate, $affiliateFields, $request );
				$affiliateHelper->setPaymentFromForm( $affiliate, $request );				
				$db->getAffiliateRepository()->update( $affiliate );
			} else {
				$response->viewData['validationResult'] = $vr;
			}
		}
		
		$new_email = get_option( $user->ID . '_new_email' );
		if ( $new_email && $new_email != $user->user_email ) {
			$response->viewData['newEmail'] = $new_email;
			$response->viewData['userId'] = $user->ID;
		}

		$response->viewData['affiliate'] = $affiliate;
		
		//save for form validation in the footer
		$this->response = $response;

		return $response;
	}

	protected function doImpressions( $request, $affiliate ) {
		$response = new WPAM_Pages_TemplateResponse( 'affiliate_cp_impressions' );

		$db = new WPAM_Data_DataAccess();

		$where = array('sourceAffiliateId' => $affiliate->affiliateId);

		$response->viewData['impressions'] = $db->getImpressionRepository()->loadMultipleByLimit(
			$where,
			array('dateCreated' => 'desc'),
			100
			);

		$creativeNames = array ();

		foreach ( $response->viewData['impressions'] as $impression ) {
			if (!array_key_exists ($impression->sourceCreativeId, $creativeNames))
				$creativeNames[$impression->sourceCreativeId] = $db->getCreativesRepository()->load($impression->sourceCreativeId)->name;
		}

		$response->viewData['creativeNames'] = $creativeNames;

		$where = array('sourceAffiliateId' => $affiliate->affiliateId);
		$response->viewData['impressionCount'] = $db->getImpressionRepository()->count ( $where );

		return $response;
	}

	protected function getCommissionRateString( WPAM_Data_Models_AffiliateModel $affiliate ) {
		if ($affiliate->bountyType === 'fixed') {
			return sprintf( __('%s per sale.', 'affiliates-manager' ), wpam_format_money( $affiliate->bountyAmount, false ) );
		} else {
			return sprintf( __( '%s%% of each completed sale, pre-tax', 'affiliates-manager' ), $affiliate->bountyAmount );
		}
	}

	protected function doApproved($request)
	{
		$confirmUrl = $this->getLink(array(
			'action' => 'confirm',
			'step' => 'show_terms'
		));
		return new WPAM_Pages_TemplateResponse('affiliate_cp_approved', array('confirmUrl' => $confirmUrl));
	}

	protected function doConfirm($request, $affiliate)
	{
		if ($request['step'] === 'show_terms')
		{
			$response = new WPAM_Pages_TemplateResponse('affiliate_cp_agree_terms');
			$response->viewData['affiliate'] = $affiliate;
                        //
                        $tnc_page_url = get_option( WPAM_PluginConfig::$AffTncPageURL );
                        $tnc_page_id = url_to_postid($tnc_page_url);
                        $tnc_page = get_post($tnc_page_id);
                        $tnc_content = $tnc_page->post_content;
			$termsCompiler = new WPAM_TermsCompiler($tnc_content);
			$response->viewData['tnc'] = $termsCompiler->build();
			$response->viewData['nextStepUrl'] = $this->getLink(
				array(
					'step' => 'accept_terms',
					'action' => 'confirm'
				)
			);
			return $response;
		}
		else if ($request['step'] === 'accept_terms')
		{
			return $this->getPaymentMethodFormResponse($affiliate);
		}
		else if ($request['step'] === 'submit_payment_details')
		{
                        if(!isset($request['_wpnonce']) || !wp_verify_nonce($request['_wpnonce'], 'affiliate_cp_submit_payment_details')){
                            wp_die('Error! Nonce Security Check Failed! Please submit your payment details again.');
                        }
			$vr = $this->validateForm($request);
			if ($vr->getIsValid())
			{
				$this->confirmAffiliate($affiliate, $request);

				//Transition affiliate directly to activated without admin review
                                $db = new WPAM_Data_DataAccess();
				$affiliate->activate();
				$db->getAffiliateRepository()->update($affiliate);

				$user = new WP_User($affiliate->userId);
				$user->add_cap(WPAM_PluginConfig::$AffiliateActiveCap);

				return new WPAM_Pages_TemplateResponse('affiliate_cp_payment_details_confirmed', array(
					'creativesLink' => $this->getLink(array(
						'sub' => 'creatives'
					))
				));
			}
			else
			{
				return $this->getPaymentMethodFormResponse($affiliate, $request, $vr);
			}

		}
	}

	protected function confirmAffiliate($affiliate, $request)
	{
		$affiliate->confirm();
		if ($request['ddPaymentMethod'] === 'paypal')
		{
			$affiliate->setPaypalPaymentMethod($request['txtPaypalEmail']);
		}
		else if ($request['ddPaymentMethod'] === 'check')
		{
			$affiliate->setCheckPaymentMethod($request['txtCheckTo']);
		}
                else if ($request['ddPaymentMethod'] === 'manual')
		{
			$affiliate->setManualPaymentMethod();
		}
                
		$db = new WPAM_Data_DataAccess();
		$db->getAffiliateRepository()->update($affiliate);
	}

	protected function getPaymentMethodFormResponse($affiliate, $request = array(), $validationResult = NULL)
	{
		add_action('wp_footer', array( $this, 'onFooter' ) );

		$response = new WPAM_Pages_TemplateResponse('affiliate_cp_payment_details_simple');
		$response->viewData['request'] = $request;
		$response->viewData['affiliate'] = $affiliate;
		
		$affiliateHelper = new WPAM_Util_AffiliateFormHelper();
		$response->viewData['paymentMethods'] = $affiliateHelper->getPaymentMethods();

		$response->viewData['validationResult'] = $validationResult;
		$response->viewData['nextStepUrl'] = $this->getLink(array(
			'step' => 'submit_payment_details',
			'action' => 'confirm'
		));

		//save for form validation in the footer
		$this->response = $response;

		return $response;
	}

	protected function validateForm($request)
	{
		$validator = new WPAM_Validation_Validator();

		$validator->addValidator('ddPaymentMethod', new WPAM_Validation_SetValidator(array('paypal','check','manual')));

		if ($request['ddPaymentMethod'] === 'check')
		{
			$validator->addValidator('txtCheckTo', new WPAM_Validation_StringValidator(1));
		}
		else if ($request['ddPaymentMethod'] === 'paypal')
		{
			$validator->addValidator('txtPaypalEmail', new WPAM_Validation_EmailValidator());
		}

		return $validator->validate($request);
	}


	public function isAvailable($wpUser)
	{
		// root is visible to all classes of users
		return true;
	}
	
	public static function getPageId() {
		return get_option( WPAM_PluginConfig::$HomePageId );
	}
	
	public function onFooter() {
		wp_localize_script( 'wpam_contact_info', 'currencyL10n', array(
								'fixedLabel' => sprintf( __( 'Bounty Rate (%s per Sale)', 'affiliates-manager' ), WPAM_MoneyHelper::getDollarSign() ),
								'percentLabel' => __( 'Bounty Rate (% of Sale)', 'affiliates-manager' ),
								'okLabel' => __( 'OK', 'affiliates-manager' ),
		) );
		wp_print_scripts( 'wpam_contact_info' );
		wp_print_scripts( 'wpam_payment_method' );
		
		$response = new WPAM_Pages_TemplateResponse('widget_form_errors', $this->response->viewData);
		echo $response->render();
	}

}
