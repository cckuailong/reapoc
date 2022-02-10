<?php
/**
 * @author Justin Foell
 */

class WPAM_Pages_Admin_NewAffiliatePage extends WPAM_Pages_Admin_AdminPage {
	
	private $response;
	
	public function processRequest( $request ) {
		$db = new WPAM_Data_DataAccess();
  		
		$affiliateFields = $db->getAffiliateFieldRepository()->loadMultipleBy(
			array( 'enabled' => true ),
			array( 'order' => 'asc' )
		);
		
		if ( isset( $request['action'] ) && $request['action'] == 'saveInfo' ) {
                        if(!isset($request['_wpnonce']) || !wp_verify_nonce($request['_wpnonce'], 'wpam_add_affiliate')){
                            wp_die('Error! Nonce Security Check Failed! Go back to the page and submit again.');
                        }
                        if(is_array($request)){
                            $request = wpam_sanitize_array($request);
                        }
                    
			$validator = new WPAM_Validation_Validator();

			$validator->addValidator('ddBountyType', new WPAM_Validation_SetValidator(array('fixed','percent')));

			if ($request['ddBountyType'] === 'fixed') {
				$validator->addValidator('txtBountyAmount', new WPAM_Validation_MoneyValidator());
			} else if ($request['ddBountyType'] === 'percent') {
				$validator->addValidator('txtBountyAmount', new WPAM_Validation_NumberValidator());
			}

			$affiliateHelper = new WPAM_Util_AffiliateFormHelper();
			$vr = $affiliateHelper->validateForm( $validator, $request, $affiliateFields );
			if ( $vr->getIsValid() ) {
				$model = $affiliateHelper->getNewAffiliate();
				
				$affiliateHelper->setModelFromForm( $model, $affiliateFields, $request );
				$affiliateHelper->setPaymentFromForm( $model, $request );

				//send email etc.
				$userHandler = new WPAM_Util_UserHandler();
                                if(get_option(WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption) == 1){
                                    $userHandler->AutoapproveAffiliate($model, $request['ddBountyType'], $request['txtBountyAmount']);
                                }
                                else{
                                    $userHandler->approveAffiliate( $model, $request['ddBountyType'], $request['txtBountyAmount'], false );
                                }
				return new WPAM_Pages_TemplateResponse( 'admin/affiliate_new_success' );
			}
			else
			{
				return $this->getForm( $affiliateFields, $request, $vr );
			}
		}
		return $this->getForm( $affiliateFields, $request );
	}

	protected function getForm( $affiliateFields, $request = NULL, WPAM_Validation_ValidatorResult $validationResult = NULL ) {
		//add widget_form_error js to affiliate_new page
		add_action('admin_footer', array( $this, 'onFooter' ) );

		$response = new WPAM_Pages_TemplateResponse( 'admin/affiliate_new' );
		$response->viewData['affiliateFields'] = $affiliateFields;
		
		if ( $request !== NULL ) {
                        if(is_array($request)){
                            $request = wpam_sanitize_array($request);
                        }
			$response->viewData['request'] = $request;
			$response->viewData['bountyType'] = isset( $request['ddBountyType'] ) ? $request['ddBountyType'] : NULL;
			$response->viewData['bountyAmount'] = isset( $request['txtBountyAmount'] ) ? $request['txtBountyAmount'] : NULL ;
		}
		
		if ( $validationResult !== NULL ) {
			$response->viewData['validationResult'] = $validationResult;
		}
		$response->viewData['affiliateFields'] = $affiliateFields;
		$response->viewData['saveLabel'] = __( 'Add Affiliate', 'affiliates-manager' );

		//save for form validation in the footer
		$this->response = $response;

		return $response;
	}
	
	public function onFooter() {		
		wp_enqueue_script( 'wpam_contact_info' );
		wp_localize_script( 'wpam_contact_info', 'currencyL10n', array(
								'fixedLabel' => sprintf( __( 'Bounty Rate (%s per Sale)', 'affiliates-manager' ), WPAM_MoneyHelper::getDollarSign() ),
								'percentLabel' => __( 'Bounty Rate (% of Sale)', 'affiliates-manager' ),
								'okLabel' => __( 'OK', 'affiliates-manager' ),
		) );
		
		$response = new WPAM_Pages_TemplateResponse('widget_form_errors', $this->response->viewData);
		echo $response->render();
	}

}
