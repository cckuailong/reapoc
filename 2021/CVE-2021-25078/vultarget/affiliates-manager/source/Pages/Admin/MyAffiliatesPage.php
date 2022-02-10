<?php

/**
 * @author John Hargrove
 * 
 * Date: Jun 6, 2010
 * Time: 6:08:48 PM
 */
class WPAM_Pages_Admin_MyAffiliatesPage extends WPAM_Pages_Admin_AdminPage {

    private $response;

    public function processRequest($request) {
        if(is_array($request)){
            $request = wpam_sanitize_array($request);
        }
        $db = new WPAM_Data_DataAccess();

        if (isset($request['viewDetail']) && is_numeric($request['viewDetail'])) {
            $affiliateFields = $db->getAffiliateFieldRepository()->loadMultipleBy(
                    array('enabled' => true), array('order' => 'asc')
            );

            $id = (int) $request['viewDetail'];
            $model = $db->getAffiliateRepository()->load($id);
            if ($model == null) {
                wp_die("Invalid affiliate ID.");
            }

            if (isset($request['action']) && $request['action'] == 'saveInfo') {
                if(!isset($request['_wpnonce']) || !wp_verify_nonce($request['_wpnonce'], 'wpam_add_affiliate')){
                    wp_die('Error! Nonce Security Check Failed! Go back to the page and submit again.');
                }
                $validator = new WPAM_Validation_Validator();

                //validate bounty type & amount if they're in the appropriate status
                if (!$model->isPending() && !$model->isBlocked() && !$model->isDeclined()) {
                    $validator->addValidator('ddBountyType', new WPAM_Validation_SetValidator(array('fixed', 'percent')));

                    if ($request['ddBountyType'] === 'fixed') {
                        $validator->addValidator('txtBountyAmount', new WPAM_Validation_MoneyValidator());
                    } else if ($request['ddBountyType'] === 'percent') {
                        $validator->addValidator('txtBountyAmount', new WPAM_Validation_NumberValidator());
                    }

                    $validator->addValidator('ddPaymentMethod', new WPAM_Validation_SetValidator(array('check', 'paypal', 'manual')));

                    if ($request['ddPaymentMethod'] === 'paypal') {
                        $validator->addValidator('txtPaypalEmail', new WPAM_Validation_EmailValidator());
                    }
                }

                $affiliateHelper = new WPAM_Util_AffiliateFormHelper();
                $vr = $affiliateHelper->validateForm($validator, $request, $affiliateFields, TRUE);
                if ($vr->getIsValid()) {
                    $affiliateHelper->setModelFromForm($model, $affiliateFields, $request);
                    $affiliateHelper->setPaymentFromForm($model, $request);
                    $db->getAffiliateRepository()->update($model);
                } else {
                    return $this->getDetailForm($affiliateFields, $model, $request, $vr);
                }
            }
            return $this->getDetailForm($affiliateFields, $model, $request);
        } else {
            //Show all the affiliates list.
            //Lets include the affiliates_list.php file
            $response = new WPAM_Pages_TemplateResponse('admin/affiliates_list');
        }
        return $response;
    }

    protected function getDetailForm($affiliateFields, $model, $request = null, $validationResult = null) {
        //add widget_form_error js to affiliate_detail page
        add_action('admin_footer', array($this, 'onFooter'));

        $db = new WPAM_Data_DataAccess();
        $response = new WPAM_Pages_TemplateResponse('admin/affiliate_detail');
        $response->viewData['affiliateFields'] = $affiliateFields;
        $response->viewData['affiliate'] = $model;

        $where = array('affiliateId' => $model->affiliateId);

        $affiliateHelper = new WPAM_Util_AffiliateFormHelper();
        $affiliateHelper->addTransactionDateRange($where, $request, $response);
        
        $response->viewData['transactions'] = $db->getTransactionRepository()->loadMultipleBy(
                $where, array('dateCreated' => 'desc')
        );

        $response->viewData['showBalance'] = true;
        $response->viewData['paymentMethods'] = $affiliateHelper->getPaymentMethods();
        $response->viewData['paymentMethod'] = isset($request['ddPaymentMethod']) ? $request['ddPaymentMethod'] : $model->paymentMethod;
        $response->viewData['paypalEmail'] = isset($request['txtPaypalEmail']) ? $request['txtPaypalEmail'] : $model->paypalEmail;
        $response->viewData['bountyType'] = isset($request['ddBountyType']) ? $request['ddBountyType'] : $model->bountyType;
        $response->viewData['bountyAmount'] = isset($request['txtBountyAmount']) ? $request['txtBountyAmount'] : $model->bountyAmount;

        $this->addBalance($response->viewData['transactions'], $db->getTransactionRepository()->getBalance(
                        $model->affiliateId, empty($request['from']) ? NULL : $request['from']
                ), 'desc');

        $accountStanding = $db->getTransactionRepository()->getAccountSummary($model->affiliateId);

        $response->viewData['accountStanding'] = $accountStanding->standing;
        $response->viewData['accountCredits'] = $accountStanding->credits;
        $response->viewData['accountDebits'] = $accountStanding->debits;
        $response->viewData['accountAdjustments'] = $accountStanding->adjustments;

        $response->viewData['user'] = new WP_User($model->userId);

        if ($request !== null) {
            $response->viewData['request'] = $request;
        }
        if ($validationResult !== null) {
            //die(print_r($validationResult, true));
            $response->viewData['validationResult'] = $validationResult;
        }
        $response->viewData['affiliateFields'] = $affiliateFields;
        $response->viewData['creatives'] = $db->getCreativesRepository()->loadAllActiveNoDeletes();

        if (get_option(WPAM_PluginConfig::$AffEnableImpressions)) {
            $where = array('sourceAffiliateId' => $model->affiliateId);

            $response->viewData['impressions'] = $db->getImpressionRepository()->loadMultipleByLimit(
                    $where, array('dateCreated' => 'desc'), 100
            );

            $creativeNames = array();

            foreach ($response->viewData['impressions'] as $impression) {
                if (!array_key_exists($impression->sourceCreativeId, $creativeNames))
                    $creativeNames[$impression->sourceCreativeId] = $db->getCreativesRepository()->load($impression->sourceCreativeId)->name;
            }

            $response->viewData['creativeNames'] = $creativeNames;

            $where = array('sourceAffiliateId' => $model->affiliateId);
            $response->viewData['impressionCount'] = $db->getImpressionRepository()->count($where);
        }

        //$summary = $db->getEventRepository()->getSummary ( $model->affiliateId );
        $args = array();
        $args['aff_id'] = $model->affiliateId;
        $total_clicks = WPAM_Click_Tracking::get_all_time_total_clicks($args);
        $total_transaction_count = WPAM_Commission_Tracking::get_all_time_transaction_count($args);
        $response->viewData['visitCount'] = $total_clicks; //$summary->visits;
        $response->viewData['purchaseCount'] = $total_transaction_count; //$summary->purchases;
        //save for form validation in the footer
        $this->response = $response;

        return $response;
    }

    protected function addBalance(&$transactions, $balance, $order = 'desc') {
        $ordered_transactions = $transactions;
        if ($order == 'desc')
            $ordered_transactions = array_reverse($transactions, true);

        foreach ($ordered_transactions as $index => $info) {
            $balance += $info->amount;
            $transactions[$index]->balance = $balance;
        }
    }

    public function onFooter() {
        wp_enqueue_script('wpam_contact_info');
        wp_localize_script('wpam_contact_info', 'currencyL10n', array(
            'fixedLabel' => sprintf(__('Bounty Rate (%s per Sale)', 'affiliates-manager'), WPAM_MoneyHelper::getDollarSign()),
            'percentLabel' => __('Bounty Rate (% of Sale)', 'affiliates-manager'),
            'okLabel' => __('OK', 'affiliates-manager'),
        ));
        wp_enqueue_script('wpam_money_format');

        $response = new WPAM_Pages_TemplateResponse('widget_form_errors', $this->response->viewData);
        echo $response->render();
    }

}
