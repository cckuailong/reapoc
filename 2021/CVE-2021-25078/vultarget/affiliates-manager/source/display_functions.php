<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 4, 2010
 * Time: 1:37:23 AM
 */

function wpam_get_status_desc( $status ) {
	switch ( $status ) {
		case 'applied': return __( 'Affiliate has applied, but is waiting on your decision.', 'affiliates-manager' );
		case 'approved': return __( 'Affiliate has been approved, but has not accepted terms.', 'affiliates-manager' );
		case 'active': return __( 'Affiliate is active, and will receive credit for leads.', 'affiliates-manager' );
		case 'confirmed': return __( 'Affiliate has accepted terms and provided payment details.<br /> If the details look good, you should activate this affiliate so they can begin sending traffic.', 'affiliates-manager' );
		case 'inactive': return __( 'Affiliate has been disabled.', 'affiliates-manager' );
		case 'declined': return __( 'Affiliate was declined.', 'affiliates-manager' );
		case 'blocked': return __( 'Affiliate was blocked and cannot re-apply.', 'affiliates-manager' );
	}
}

function wpam_format_bounty( $bountyType, $bountyAmount ) {
	if ( $bountyType === 'fixed' ) {
		return sprintf( __( '%s per sale', 'affiliates-manager' ), $bountyAmount );
	} else if ( $bountyType === 'percent' ) {
                return sprintf( __( '%s%% of pre-tax sales', 'affiliates-manager' ), $bountyAmount );
	}
}

function wpam_html_state_code_options( $fieldValue ) {
	echo '<option value="">'.__('Select an option', 'affiliates-manager').'</option>';
	foreach ( WPAM_Validation_StateCodes::$stateCodes as $code => $name ) {
		echo '<option value="'.$code.'"';
		if ( $fieldValue == $code ) {
			echo ' selected="selected"';
		}
		echo '>' . $name . '</option>';
	}
}

function wpam_html_country_code_options( $fieldValue ) {
	echo '<option value="">'.__('Select an option', 'affiliates-manager').'</option>';

	foreach ( WPAM_Validation_CountryCodes::get_countries() as $code => $name ) {
		echo '<option value="'.$code.'"';
		if ( $fieldValue == $code )
			echo ' selected="selected"';
		echo '>'.$name.'</option>';
	}
}

if ( ! function_exists( 'wpam_money_format' ) ):
// from http://php.net/manual/en/function.money-format.php#89060
function wpam_money_format($format, $number)
{    
    $currency = get_option(WPAM_PluginConfig::$AffCurrencySymbol);
    if(empty($currency)){
       $currency = '$';
    }
    $formatted_amount = number_format($number, 2, '.', ',');
    $formatted_output = $currency . $formatted_amount;
    return $formatted_output;

    //--------- TODO - refactor the following later -------------
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
    $locale = localeconv();
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
    foreach ($matches as $fmatch) {
        $value = floatval($number);
        $flags = array(
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                           $match[1] : ' ',
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                           $match[0] : '+',
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
        );
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
        $conversion = $fmatch[5];

        $positive = true;
        if ($value < 0) {
            $positive = false;
            $value  *= -1;
        }
        $letter = $positive ? 'p' : 'n';

        $prefix = $suffix = $cprefix = $csuffix = $signal = '';

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
        switch (true) {
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                $prefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                $suffix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                $cprefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                $csuffix = $signal;
                break;
            case $flags['usesignal'] == '(':
            case $locale["{$letter}_sign_posn"] == 0:
                $prefix = '(';
                $suffix = ')';
                break;
        }
        $currency = get_option(WPAM_PluginConfig::$AffCurrencySymbol);
        if(empty($currency)){
            $currency = '$';
        }
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

        $value = number_format($value, $right, $locale['mon_decimal_point'],
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $value = @explode($locale['mon_decimal_point'], $value);

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
        if ($left > 0 && $left > $n) {
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
        }
        $value = implode($locale['mon_decimal_point'], $value);
        if ($locale["{$letter}_cs_precedes"]) {
            $value = $prefix . $currency . $space . $value . $suffix;
        } else {
            $value = $prefix . $value . $space . $currency . $suffix;
        }
        if ($width > 0) {
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                     STR_PAD_RIGHT : STR_PAD_LEFT);
        }

        $format = str_replace($fmatch[0], $value, $format);
    }
    return $format;
}
endif;

function wpam_format_money( $money, $add_span = true ) {
	if ( $add_span ) {
		if ( $money > 0 )
			return '<span class="positiveMoney">' . wpam_money_format( '%n', $money ) . "</span>";
		else if ( $money < 0 )
			return '<span class="negativeMoney">' . wpam_money_format( '%n', $money ) . "</span>";
		else
			return '<span>' . wpam_money_format( '%n', $money ) . "</span>";
	} else {
		return wpam_money_format( '%n', $money );
	}		
}

function wpam_format_status( $status ) {
	switch ( $status ) {
		case 'applied':
			return __( 'Applied', 'affiliates-manager' );	
		case 'declined':
			return __( 'Declined', 'affiliates-manager' );
		case 'approved':
			return __( 'Approved', 'affiliates-manager' );
		case 'active':
			return __( 'Active', 'affiliates-manager' );			
		case 'inactive':
			return __( 'Inactive', 'affiliates-manager' );			
		case 'confirmed':
			return __( 'Confirmed', 'affiliates-manager' );			
		case 'blocked':
			return __( 'Blocked', 'affiliates-manager' );			
	}
}

?>