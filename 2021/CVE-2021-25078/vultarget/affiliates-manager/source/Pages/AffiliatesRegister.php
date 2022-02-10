<?php
/**
 * @author John Hargrove
 * 
 * Date: May 30, 2010
 * Time: 5:43:09 PM
 */

require_once WPAM_BASE_DIRECTORY . "/source/Validation/CallbackValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/NumberValidator.php";

class WPAM_Pages_AffiliatesRegister extends WPAM_Pages_PublicPage
{
	private $response;
	
	public function isAvailable($wpUser)
	{
		return true;
	}

	public function processRequest($request)
	{
                if(is_array($request)){
                    $request = wpam_sanitize_array($request);
                }
		$db = new WPAM_Data_DataAccess();
		$affiliateFields = $db->getAffiliateFieldRepository()->loadMultipleBy(
			array('enabled' => true),
			array('order' => 'asc')
		);

		if ( isset( $request['wpam_reg_submit'] ) && $request['wpam_reg_submit'] == '1' ) {
                        if(!isset($request['_wpnonce']) || !wp_verify_nonce($request['_wpnonce'], 'wpam_reg_submit')){
                            wp_die('Error! Nonce Security Check Failed! Go back to the registration page and submit again.');
                        }
                        $form_validated = false;
			$affiliateHelper = new WPAM_Util_AffiliateFormHelper();
			$vr = $affiliateHelper->validateForm( new WPAM_Validation_Validator(), $request, $affiliateFields );
                        if($vr->getIsValid()){
                            $form_validated = true;
                        }
                        $output = apply_filters( 'wpam_validate_registration_form_submission', '', $request);
                        if(!empty($output)){
                            $form_validated = false;
                        }
			if ($form_validated) {
				$model = $affiliateHelper->getNewAffiliate();
				
				$affiliateHelper->setModelFromForm( $model, $affiliateFields, $request );
                                
                                //Fire the action hook
                                do_action('wpam_front_end_registration_form_submitted', $model, $request);
                                
                                //Check if automatic affiliate approval option is enabled
                                if(get_option(WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption) == 1){
                                    $userHandler = new WPAM_Util_UserHandler();
                                    $userHandler->AutoapproveAffiliate($model);
                                    return new WPAM_Pages_TemplateResponse('aff_app_submitted_auto_approved');
                                }     
                                
                                //Do the non auto approval process
				$db = new WPAM_Data_DataAccess();
				$id = $db->getAffiliateRepository()->insert( $model );

				if ( $id == 0 ) {
					if ( WPAM_DEBUG )
						echo '<pre>', var_export($model, true), '</pre>';
					wp_die( __('Error submitting your details to the database. This is a bug, and your application was not submitted.', 'affiliates-manager' ) );
				}
				

				
				$mailer = new WPAM_Util_EmailHandler();
                                if(get_option(WPAM_PluginConfig::$SendAdminRegNotification) == 1){
                                    //Notify admin that affiliate has registered
                                    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
                                    $message  = sprintf( __( 'New affiliate registration on your site %s:', 'affiliates-manager' ), $blogname) . "\r\n\r\n";
                                    $message .= sprintf( __( 'Name: %s %s', 'affiliates-manager' ), $request['_firstName'], $request['_lastName']) . "\r\n";
                                    $message .= sprintf( __( 'Email: %s', 'affiliates-manager' ), $request['_email']) . "\r\n";
                                    $message .= sprintf( __( 'Company: %s', 'affiliates-manager' ), $request['_companyName']) . "\r\n";
                                    $message .= sprintf( __( 'Website: %s', 'affiliates-manager' ), $request['_websiteUrl']) . "\r\n\r\n";
                                    $message .= sprintf( __( 'View Application: %s', 'affiliates-manager' ),  admin_url('admin.php?page=wpam-affiliates&viewDetail='.$id)) . "\r\n";
                                    $admin_email = get_option(WPAM_PluginConfig::$AdminRegNotificationEmail);
                                    if(!isset($admin_email) || empty($admin_email)){
                                        $admin_email = get_option('admin_email');
                                    }
                                    $mailer->mailAffiliate( $admin_email, __( 'New Affiliate Registration', 'affiliates-manager' ), $message );
                                }
				//Notify affiliate of their application
				$affsubj  = sprintf(__('Affiliate application for %s', 'affiliates-manager' ), $blogname);
				$affmessage = WPAM_MessageHelper::GetMessage('affiliate_application_submitted_email');
				$mailer->mailAffiliate( $request['_email'], $affsubj, $affmessage );

				return new WPAM_Pages_TemplateResponse('affiliate_application_submitted');
			} 
                        else {
				return $this->getForm( $affiliateFields, $request, $vr );
			}
		}
		//else
		return $this->getForm($affiliateFields);
	}

	protected function getForm($affiliateFields, $request = null, WPAM_Validation_ValidatorResult $validationResult = null)
	{
		add_action('wp_footer', array( $this, 'onFooter' ) );
		//
                $tnc_page_url = get_option( WPAM_PluginConfig::$AffTncPageURL );
                $tnc_page_id = url_to_postid($tnc_page_url);
                $tnc_page = get_post($tnc_page_id);
                $tnc_content = $tnc_page->post_content;
		$tncBuilder = new WPAM_TermsCompiler($tnc_content);

		$response = new WPAM_Pages_TemplateResponse('affiliate_register_form');

		if ($request !== null) {
			$response->viewData['request'] = $request;
		}
		if ($validationResult !== null) {
			$response->viewData['validationResult'] = $validationResult;
		}
		$response->viewData['affiliateFields'] = $affiliateFields;
		$response->viewData['tnc'] = $tncBuilder->build();
                /*
		$postHelper = new WPAM_PostHelper();
                
		$response->viewData['postBackUrl'] = $this->getLink(
			array(
				//'page_id' => $postHelper->getPostId(WPAM_Plugin::PAGE_NAME_REGISTER),
				'action' => 'submit' )
		);
                */
		//save for form validation in the footer
		$this->response = $response;
		
		return $response;

	}

	public static function getPageId() {
		return get_option( WPAM_PluginConfig::$RegPageId );
	}

	public function onFooter() {
		wp_print_scripts( 'wpam_tnc' );

		$response = new WPAM_Pages_TemplateResponse('widget_form_errors', $this->response->viewData);
		echo $response->render();
	}
}
