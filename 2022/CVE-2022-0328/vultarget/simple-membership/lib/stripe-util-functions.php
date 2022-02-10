<?php

/* Misc Utility Functions for the Stripe Gateway */

class StripeUtilFunctions {

    public static function get_stripe_plan_info($api_key, $plan_id) {
        SwpmMiscUtils::load_stripe_lib();
        
        $stripe_err = '';

        try {
            \Stripe\Stripe::setApiKey($api_key);

            $plan = \Stripe\Plan::retrieve($plan_id);
        } catch (\Stripe\Error\Authentication $e) {
            // Invalid secret key
            $stripe_err = $e->getMessage();
        } catch (Exception $e) {
            //that's probably invalid plan ID or some other error
            $stripe_err = $e->getMessage();
        }
        if (empty($stripe_err)) {
            //we proceed with getting plan details only if no errors occurred
            $plan_data['name'] = isset($plan->nickname) ? $plan->nickname : '';
            $plan_data['amount'] = $plan->amount;
            $plan_data['currency'] = $plan->currency;
            $plan_data['interval'] = $plan->interval;
            $plan_data['interval_count'] = $plan->interval_count;
            $plan_data['trial_period_days'] = $plan->trial_period_days;
            return array('success' => true, 'plan_data' => $plan_data);
        } else {
            return array('success' => false, 'error_msg' => $stripe_err);
        }
    }

}
