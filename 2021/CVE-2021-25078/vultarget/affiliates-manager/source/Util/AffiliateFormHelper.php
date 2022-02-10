<?php

class WPAM_Util_AffiliateFormHelper {

	public function setModelFromForm( WPAM_Data_Models_AffiliateModel &$model, $affiliateFields, $request ) {

		foreach ($affiliateFields as $affiliateField)
		{
			$request_value = isset($request['_'.$affiliateField->databaseField]) ? $request['_'.$affiliateField->databaseField] : '';
                        
			if ( $affiliateField->fieldType == 'phoneNumber' ){
				$value = $request_value;
                        }
			else if ( $affiliateField->fieldType == 'ssn' && is_array( $request_value ) ){
				$value = implode($request_value);
                        }
			else{
				$value = $request_value;
                        }
			if ($affiliateField->type == 'base')
			{
				if ( $affiliateField->fieldType == 'email' && empty( $value ) )
					continue; //#79 skip email update if not set
				$model->{$affiliateField->databaseField} = $value;
			}
			else
			{
				$model->userData[$affiliateField->databaseField] = $value;
			}
		}
	}

	public function setPaymentFromForm( WPAM_Data_Models_AffiliateModel &$model, $request ) {
		//assume all of these have been validated already
		
		if( isset( $request['ddPaymentMethod'] ) ) {
			$model->paymentMethod = $request['ddPaymentMethod'];
		}

		if( isset( $request['txtPaypalEmail'] ) ) {
			$model->paypalEmail = $request['txtPaypalEmail'];
		}

		if( isset( $request['ddBountyType'] ) ) {
			$model->bountyType = $request['ddBountyType'];
		}
		
		if( isset( $request['txtBountyAmount'] ) ) {
			$model->bountyAmount = $request['txtBountyAmount'];
		}		
	}

	public function isEmailBlocked($value)
	{
		$db = new WPAM_Data_DataAccess();
		$affRepo = $db->getAffiliateRepository();
		return !$affRepo->existsBy(array('email' => $value, 'status' => 'blocked'));
	}

	public function isEmailInUse($value)
	{
		$db = new WPAM_Data_DataAccess();
		$affRepo = $db->getAffiliateRepository();

		return !$affRepo->existsBy(
			array(
				'email' => $value,
				'status' => array('!=', 'declined')
			)
		);
	}

	public function getNewAffiliate() {
		$model = new WPAM_Data_Models_AffiliateModel();

		$model->userData = array();

		$model->status = 'applied';
		$model->dateCreated = time();

		$idGenerator = new WPAM_Tracking_UniqueIdGenerator();
		$model->uniqueRefKey = $idGenerator->generateId();

		return $model;
	}

	public function validateForm($validator, $request, $affiliateFields, $existingUser = false)
	{
		foreach ($affiliateFields as $affiliateField)
		{
			$fieldValue = isset($request[$affiliateField->databaseField]) ? $request[$affiliateField->databaseField] : NULL;
			$fieldName = '_'.$affiliateField->databaseField;

			if ($affiliateField->databaseField == 'email') {
				$validator->addValidator($fieldName, new WPAM_Validation_CallbackValidator( __( 'is a blocked e-mail ', 'affiliates-manager' ), array( $this, "isEmailBlocked" ) ) );
				if ( ! $existingUser )
					$validator->addValidator($fieldName, new WPAM_Validation_CallbackValidator( __( 'is in use', 'affiliates-manager' ), array( $this, "isEmailInUse" ) ) );
			}

			if ( $affiliateField->required || ! empty( $fieldValue ) ) {
				switch ($affiliateField->fieldType) {
					case 'string':
						$validator->addValidator($fieldName, new WPAM_Validation_StringValidator(1, $affiliateField->length));
						break;
                                        case 'textarea':
						$validator->addValidator($fieldName, new WPAM_Validation_StringValidator(1));
						break;    
					case 'email':
						$validator->addValidator($fieldName, new WPAM_Validation_EmailValidator());
						break;
					case 'number':
						$validator->addValidator($fieldName, new WPAM_Validation_NumberValidator());
						break;
					case 'zipCode':
						$validator->addValidator($fieldName, new WPAM_Validation_ZipCodeValidator());
						break;
					case 'phoneNumber':
						$validator->addValidator($fieldName, new WPAM_Validation_MultiPartPhoneNumberValidator());
						break;
					case 'stateCode':
						$validator->addValidator($fieldName, new WPAM_Validation_StateCodeValidator());
						break;
					case 'countryCode':
						$validator->addValidator($fieldName, new WPAM_Validation_CountryCodeValidator());
						break;
					case 'ssn':
						$validator->addValidator($fieldName, new WPAM_Validation_MultiPartSocialSecurityNumberValidator());
						break;
				}
			}
		}

		return $validator->validate($request);
	}

	//#45 shared with MyAffiliatesPage
	public function addTransactionDateRange( array &$where, array $request, WPAM_Pages_TemplateResponse &$response ) {
		$response->viewData['from'] = '';
		$response->viewData['to'] = '';

		if ( ! empty( $request['from'] ) ) {
			$where['~dateCreated'] = array( '>=', date('Y-m-d', strtotime( $request['from'] ) ) );
			$response->viewData['from'] = $request['from'];
		}
					
		if ( ! empty( $request['to'] ) ) {
			$where['~~dateCreated'] = array( '<=', date('Y-m-d 23:59:59', strtotime( $request['to'] ) ) );
			$response->viewData['to'] = $request['to'];
		}
	}

	public function getPaymentMethods() {
		$payments = array();
		
		if (get_option(WPAM_PluginConfig::$PayoutMethodCheckIsEnabledOption) == 1)
			$payments['check'] = __( 'Paper Check', 'affiliates-manager' );

		if (get_option(WPAM_PluginConfig::$PayoutMethodPaypalIsEnabledOption) == 1)
			$payments['paypal'] = __( 'PayPal Transfer', 'affiliates-manager' );

                if (get_option(WPAM_PluginConfig::$PayoutMethodManualIsEnabledOption) == 1)
			$payments['manual'] = __( 'Manual Transfer', 'affiliates-manager' );

		return $payments;
	}

}