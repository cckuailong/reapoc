<?php
/**
 * @author John Hargrove
 * 
 * Date: 1/1/11
 * Time: 3:28 PM
 */

require_once WPAM_BASE_DIRECTORY . "/source/PayPal/Service.php";
require_once WPAM_BASE_DIRECTORY . "/source/PayPal/MassPayResultFile.php";

class WPAM_Pages_Admin_PaypalPaymentsPage extends WPAM_Pages_Admin_AdminPage
{
	public function processRequest($request)
	{
                /*
                if(is_array($request)){
                    $request = wpam_sanitize_array($request);
                }
                */
		$options = new WPAM_Options();
		if ($options->getPaypalMassPayEnabled() !== 0)
		{

			$step = isset( $request['step'] ) ? $request['step'] : NULL;
			switch ( $step ) {
				case 'select_affiliates': return $this->processSelectAffiliatesRequest($request);
				case 'review_affiliates': return $this->processReviewAffiliatesRequest($request);
				case 'submit_to_paypal': return $this->processSubmitToPaypalRequest($request);
				case 'view_payments': return $this->processViewPaymentsRequest($request);
				case 'view_payment_detail': return $this->processViewPaymentDetailRequest($request);
				case 'reconcile_manual': return $this->processReconcileManualRequest($request);
				case 'reconcile_with_file': return $this->processReconcileWithFileRequest($request);
				case 'notice':
				default:
					return new WPAM_Pages_TemplateResponse('admin/paypalpayments/home');
			}
		}
		else
		{
			return new WPAM_Pages_TemplateResponse('admin/paypalpayments/not_configured');
		}
	}

	private function processReconcileWithFileRequest($request)
	{
		$db = new WPAM_Data_DataAccess();
		$data = $this->loadPaypalLogDetails((int)$request['id']);

		if ( isset( $request['substep'] ) && $request['substep'] ==='confirm')
		{
                        $nonce = $request['_wpnonce'];
                        if(!wp_verify_nonce($nonce, 'wpam_payments_rwfc_nonce')){
                            wp_die(__('Error! Nonce Security Check Failed! Go back to the PayPal Mass Pay menu to reconcile using a result file.', 'affiliates-manager'));
                        }
			try
			{
				$resultFile = new WPAM_PayPal_MassPayResultFile($_FILES['resultsFile']['tmp_name']);
				
				if (bccomp($data['pplog']->amount, $resultFile->getPaymentAmount(), 2) != 0)
				{
					throw new Exception( __( 'This results file does not appear to match this payment.', 'affiliates-manager' ) );
				}

				$unmatchedTransactions = $data['transactions'];
				$data['transactions_modified'] = array();

				foreach ($resultFile->getTransactions() as $transaction)
				{
					$matchedTransaction = NULL;
					foreach ($unmatchedTransactions as $index => $unmatchedTransaction)
					{
						$affiliate = $data['affiliates'][$unmatchedTransaction->affiliateId];
						if ($affiliate->paypalEmail == $transaction['Recipient'])
						{
							$transactionAmount = bcmul($unmatchedTransaction->amount, '-1', 2);
							if (bccomp($transaction['Amount'], $transactionAmount, 2) == 0)
							{
								$matchedTransaction = $unmatchedTransaction;
								unset($unmatchedTransactions[$index]);
								if ($transaction['Status'] == 'Completed')
								{
									$matchedTransaction->newStatus = 'confirmed';
								}
								else
								{
									$matchedTransaction->newStatus = 'failed';
								}
								$data['transactions_modified'][] = $matchedTransaction;

							}
							else
							{
								throw new Exception(
									sprintf( 
										__( "Matched a transaction in your results file, but the value was incorrect. Expected %s but found '%s'", 'affiliates-manager' ),
										$transaction['Amount'],
										$transactionAmount ) );
							}
						}
					}

					if ($matchedTransaction === NULL)
						throw new Exception( __( 'Could not correlate a transaction from your results file with the transactions on record.', 'affiliates-manager' ) );
				}

				// any unmatched transactions?
				if (count($unmatchedTransactions) > 0)
				{
					throw new Exception( __( 'Could not correlate all transactions', 'affiliates-manager' ) );
				}

				return new WPAM_Pages_TemplateResponse('admin/paypalpayments/reconcile_file_review', $data);
			}
			catch (Exception $e)
			{
				$data['errorMsg'] = $e->getMessage();
				return new WPAM_Pages_TemplateResponse('admin/paypalpayments/reconcile_with_file', $data);
			}
		}
		else if ( isset( $request['substep'] ) && $request['substep'] == 'confirm_ok' )
		{
                        $nonce = $request['_wpnonce'];
                        if(!wp_verify_nonce($nonce, 'wpam_payments_rwfco_nonce')){
                            wp_die(__('Error! Nonce Security Check Failed! Go back to the PayPal Mass Pay menu and confirm to reconcile using a result file.', 'affiliates-manager'));
                        }
			$pplog = $db->getPaypalLogRepository()->load($request['id']);

			if ($pplog !== NULL && $pplog->status == 'pending')
			{
				foreach ($request['transactions'] as $transaction)
				{
					$tr = $db->getTransactionRepository()->load($transaction['transactionId']);
					$tr->status = $transaction['newStatus'];
					$db->getTransactionRepository()->update($tr);
				}

				$pplog->status = 'reconciled';
				$db->getPaypalLogRepository()->update($pplog);
			}

			return $this->processViewPaymentDetailRequest($request);
		}
		else
		{
			return new WPAM_Pages_TemplateResponse('admin/paypalpayments/reconcile_with_file', $data);
		}
	}

	private function processReconcileManualRequest($request)
	{
		$db = new WPAM_Data_DataAccess();

		if ($request['substep'] == 'confirm')
		{
                        $nonce = $request['_wpnonce'];
                        if(!wp_verify_nonce($nonce, 'wpam_payments_reconcile_manual_nonce')){
                            wp_die(__('Error! Nonce Security Check Failed! Go back to the PayPal Mass Pay menu to manually reconcile payments.', 'affiliates-manager'));
                        }
			$pplog = $db->getPaypalLogRepository()->load((int)$request['id']);
			if ($pplog === NULL)
				throw new Exception( __( 'Invalid PayPal LogID', 'affiliates-manager' ) );

			foreach ($request['transactionStatus'] as $transactionId => $status)
			{
				$tr = $db->getTransactionRepository()->load((int)$transactionId);
				if ($tr === NULL)
					throw new Exception( __( 'Transaction not found', 'affiliates-manager' ) );
				if ($status === 'success')
				{
					$tr->status = 'confirmed';
				}
				else if ($status === 'failed')
				{
					$tr->status = 'failed';
				}
				else
				{
					throw new Exception( __( 'Invalid transaction status', 'affiliates-manager' ) );
				}
				$db->getTransactionRepository()->update($tr);
			}
			$pplog->status = 'reconciled';
			$db->getPaypalLogRepository()->update($pplog);
			return $this->processViewPaymentDetailRequest($request);
		}
		return new WPAM_Pages_TemplateResponse('admin/paypalpayments/reconcile_manual', $this->loadPaypalLogDetails((int)$request['id']));
	}

	private function loadPaypalLogDetails($id)
	{
		$db = new WPAM_Data_DataAccess();
		$pplog = $db->getPaypalLogRepository()->load($id);
		$transactions = $db->getTransactionRepository()->loadMultipleBy(array('referenceId' => $pplog->correlationId));
		$affiliates=array();
		foreach ($transactions as $tr)
		{
			$affiliate = $db->getAffiliateRepository()->load($tr->affiliateId);
			$affiliates[$affiliate->affiliateId] = $affiliate;
		}
		$viewData = array(
			'pplog' => $pplog,
			'transactions' => $transactions,
			'affiliates' => $affiliates
		);
		return $viewData;
	}

	private function processViewPaymentDetailRequest($request)
	{
		return new WPAM_Pages_TemplateResponse('admin/paypalpayments/view_payment_detail', $this->loadPaypalLogDetails((int)$request['id']));
	}

	private function processViewPaymentsRequest($request)
	{
		$db = new WPAM_Data_DataAccess();
		$viewData=array(
			'logs' => $db->getPaypalLogRepository()->loadMultipleBy(array(), array('paypalLogId' => 'desc'))
		);

		return new WPAM_Pages_TemplateResponse('admin/paypalpayments/pending_payments', $viewData);
	}

	private function processSubmitToPaypalRequest($request)
	{
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'wpam_payments_submit_to_paypal_nonce')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the PayPal Mass Pay menu to continue with these affiliate payments.', 'affiliates-manager'));
                }
		$options = new WPAM_Options();
		$db = new WPAM_Data_DataAccess();
		$aff_db = $db->getAffiliateRepository();
		$tr_db = $db->getTransactionRepository();

		$paypalService = new WPAM_PayPal_Service(
			$options->getPaypalAPIEndPointURL(),
			$options->getPaypalAPIUser(),
			$options->getPaypalAPIPassword(),
			$options->getPaypalAPISignature()
		);
		$massPayRequest = new WPAM_PayPal_MassPayRequest( WPAM_PayPal_MassPayRequest::RECEIVERTYPE_EMAIL_ADDRESS, __( 'Affiliate Payment', 'affiliates-manager' ) );

		$transactions = array();

		$amount = '0.00';
		$fee = '0.00';
		$totalAmount = '0.00';

		foreach ($request['affiliates'] as $affiliateArray)
		{
			$affiliateModel = $aff_db->load($affiliateArray['id']);
			if ($affiliateModel === NULL)
				throw new Exception( __( 'Affiliate not found', 'affiliates-manager' ) );

			if ($affiliateModel->paymentMethod !== 'paypal')
				throw new Exception( __( 'Payment method for affiliate is not PayPal', 'affiliates-manager' ) );

			$paymentTransaction = new WPAM_Data_Models_TransactionModel();
			$paymentTransaction->affiliateId = $affiliateModel->affiliateId;
			$paymentTransaction->amount = bcmul($affiliateArray['amount'],-1,2);
			$paymentTransaction->dateCreated = time();
			$paymentTransaction->dateModified = time();
			$paymentTransaction->description = __( 'Payout via PayPal Mass Pay', 'affiliates-manager' );
			$paymentTransaction->status = WPAM_Data_Models_TransactionModel::STATUS_PENDING;
			$paymentTransaction->type = WPAM_Data_Models_TransactionModel::TYPE_PAYOUT;

			$transactionId = $tr_db->insert( $paymentTransaction );
			$transactions[] = array('transactionId' => $transactionId);

			$massPayRequest->addRecipient( $affiliateModel->paypalEmail, $affiliateArray['amount'], $transactionId );

			$amount = bcadd($amount, $affiliateArray['amount'],2);
			if ($amount*0.02>1.00)
				$fee += 1.00;
			else
				$fee = bcadd($fee, bcmul($affiliateArray['amount'],'0.02',2),2);

		}
		$totalAmount = $amount + $fee;

		$ppResponse = $paypalService->doMassPay($massPayRequest);

		$ppLogModel = new WPAM_Data_Models_PaypalLogModel();
		$ppLogModel->ack = $ppResponse->getAck();
		$ppLogModel->build = $ppResponse->getBuild();
		$ppLogModel->correlationId = $ppResponse->getCorrelationId();
		$ppLogModel->dateOccurred = time();
		$ppLogModel->errors = $ppResponse->getErrors();
		$ppLogModel->responseTimestamp = $ppResponse->getTimestamp();
		$ppLogModel->version = $ppResponse->getVersion();
		$ppLogModel->rawResponse = $ppResponse->getRawResponse();
		$ppLogModel->status = $ppResponse->IsFailure() ? 'failed' : 'pending';
		$ppLogModel->amount = $amount;
		$ppLogModel->totalAmount = $totalAmount;
		$ppLogModel->fee = $fee;

		$ppLogId = $db->getPaypalLogRepository()->insert($ppLogModel);

		if ($ppResponse->IsFailure())
		{
			foreach ($transactions as $transaction)
			{
				$tr_db->delete(array('transactionId' => $transaction['transactionId']));
			}
			return new WPAM_Pages_TemplateResponse('admin/paypalpayments/masspay_failed', array('response' => $ppResponse));
		}

		foreach ($transactions as $transaction)
		{
			$dbtr = $tr_db->load($transaction['transactionId']);
			$dbtr->referenceId = $ppResponse->getCorrelationId();
			$tr_db->update($dbtr);
		}

		$response = new WPAM_Pages_TemplateResponse('admin/paypalpayments/masspay_submitted', array('response' => $ppResponse, 'ppLogId' => $ppLogId));

		return $response;
	}

	private function processReviewAffiliatesRequest($request)
	{
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'wpam_payments_review_affiliates_nonce')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the PayPal Mass Pay menu and selects affiliates to pay.', 'affiliates-manager'));
                }
		$db = new WPAM_Data_DataAccess();
		$aff_db = $db->getAffiliateRepository();

		$viewData = array();
		$viewData['affiliates'] = array();
		$viewData['paymentTotal'] = 0;
		$viewData['feeTotal'] = 0;
		$viewData['total'] = 0;

		foreach (array_keys($request['chkAffiliate']) as $id)
		{
			$id = (int)$id;
			$paymentAmount = $request['txtAffiliatePaymentAmount'][$id];
			$feeAmount = min($paymentAmount * 0.02, 1.00);
			$affiliate = $aff_db->loadAffiliateSummary(array('affiliateId' => $id));
			$affiliate = $affiliate[0];
			$affiliate->newBalance = $affiliate->balance - $paymentAmount;
			$affiliate->paymentAmount = $paymentAmount;
			$viewData['affiliates'][] = $affiliate;
			$viewData['paymentTotal'] += $paymentAmount;
			$viewData['feeTotal'] += $feeAmount;
			$viewData['total'] += $paymentAmount + $feeAmount;
		}

		return new WPAM_Pages_TemplateResponse('admin/paypalpayments/review_affiliates', $viewData);
	}

	private function processSelectAffiliatesRequest( $request ) {
            
                if(!empty($request['from']) || !empty($request['to'])){  /*date range selected */
                    if(!isset($request['_wpnonce']) || !wp_verify_nonce($request['_wpnonce'], 'wpam_payments_select_aff_date_range_nonce')){
                        wp_die(__('Error! Nonce Security Check Failed! Go back to the PayPal Mass Pay menu and select affiliates again.', 'affiliates-manager'));
                    }
                }
		$db = new WPAM_Data_DataAccess();
		$aff_db = $db->getAffiliateRepository();
		
		$response = new WPAM_Pages_TemplateResponse('admin/paypalpayments/select_affiliates');
		$where = array( 'paymentMethod' => 'paypal' );

		$affiliateHelper = new WPAM_Util_AffiliateFormHelper();		
		$affiliateHelper->addTransactionDateRange( $where, $request, $response );
		
		$response->viewData['affiliates'] = $aff_db->loadAffiliateSummary(
			$where,
			'0.01',//any one with a penny or more shows up
			array( 'balance' => 'desc' )
		);
		
		$response->viewData['notShownCount'] = $aff_db->count( array(
			'paymentMethod' => array( '!=', 'paypal' ),
			'status' => array( 'IN', array( 'active','inactive' ) )
		) );

		$response->viewData['minPayout'] = get_option(WPAM_PluginConfig::$MinPayoutAmountOption);
		
		return $response;
	}
	
}
