<?php
namespace NotificationX\Admin\Reports;

use NotificationX\Core\Helper as NotificationX_Helper;

class EmailTemplate {

    public function header(){
        $output = <<<NXTEMHEADER
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NotificationX Email Template</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
        <style type="text/css">
            .nx-email-body, .nx-wrapper-body {
                font-size: 14px;
                font-family: 'Roboto', sans-serif;
            }
            .nx-box-analytics-parent {
                padding: 0px 25px;
            }
            @media screen and ( min-width: 786px ) {
                table.nx-email-body > tbody > tr > td {
                    padding-top: 0px;
                    padding-bottom: 20px;
                }
                table.nx-email-body > tbody > tr > td.nx-email-header {
                    padding-top: 30px !important;
                }
                table.nx-email-body > tbody > tr:last-of-type > td {
                    padding-bottom: 30px !important;
                }
            }
            @media screen and (max-width: 620px) {
                table.nx-email-wrapper > tbody > tr > td, table.nx-email-body > tbody > tr > td,
                table.nx-email-footer > tbody > tr > td {
                    padding: 15px !important;
                }
                table.nx-email-wrapper > tbody > tr > td.nx-email-footer-parent {
                    padding-top: 0px !important;
                }

                table.nx-email-body > tbody > tr > td.nx-box-analytics-parent {
                    padding: 5px !important;
                }

                .nx-email-body, .nx-email-footer {
                    width: 100% !important;
                }
                .nx-email-logo {
                    width: 100px;
                }
                .nx-mobile-font {
                    font-size: 12px !important;
                    line-height: 1.5;
                }
                .nx-box-analytics .nx-mobile-font {
                    font-size: 10px !important;
                }
                .nx-box-analytics > tbody > tr > td > table > tbody > tr:nth-of-type(2) td.nx-mobile-font {
                    font-size: 15px !important;
                }
                .nx-mobile-icon {
                    width: 15px !important
                }
                td#nx-no-padding {
                    padding: 0px !important;
                }
            }
        </style>
    </head>
    <body class="nx-wrapper-body" style="background-color: #f3f7fa; margin: 0; padding: 0">
        <table class="nx-email-wrapper" cellpadding="50" cellspacing="0" border="0" width="100%" align="center" bgcolor="#f3f7fa">
            <tbody>
                <tr>
                    <td>
                        <table class="nx-email-body" cellpadding="35" cellspacing="0" border="0" width="600" align="center" bgcolor="#FFF">
                            <tbody>
NXTEMHEADER;
        return $output;
    }

    public function footer(){
        $facebook = esc_url( NOTIFICATIONX_PUBLIC_URL  . 'image/reports/facebook.png' );
        $twitter  = esc_url( NOTIFICATIONX_PUBLIC_URL  . 'image/reports/twitter.png' );
        $youtube  = esc_url( NOTIFICATIONX_PUBLIC_URL  . 'image/reports/youtube.png' );
        $web      = esc_url( NOTIFICATIONX_PUBLIC_URL  . 'image/reports/web.png' );

        $output = <<<NXTEMFOOTER
        </tbody>
        </table> <!-- /.nx-email-body -->
    </td>
</tr>
<tr>
    <td style="padding: 0px 0px 50px" class="nx-email-footer-parent">
        <table class="nx-email-footer" cellpadding="0" cellspacing="0" border="0" width="600" align="center">
            <tbody>
                <tr>
                    <td align="center" style="padding: 0px 60px">
                        <a style="background-image: url('$facebook'); background-repeat: no-repeat; display: inline-block; width: 19px; height: 19px;" href="https://www.facebook.com/groups/NotificationX.Community" title="Join Us in Facebook" target="_blank"></a>
                        <a style="background-image: url('$twitter'); background-repeat: no-repeat; display: inline-block; width: 19px; height: 19px;" href="https://twitter.com/NotificationX_" target="_blank" title="Follow Us"></a>
                        <a style="background-image: url('$youtube'); background-repeat: no-repeat; display: inline-block; width: 19px; height: 19px;" href="https://www.youtube.com/wpdevelopernet" target="_blank" title="Subscribe to Get New Tutorial"></a>
                        <a style="background-image: url('$web'); background-repeat: no-repeat; display: inline-block; width: 19px; height: 19px;" href="https://notificationx.com" target="_blank" title="Follow us On Web"></a>
                        <p style="margin: 0; color: #737373; line-height: 1.5; margin-top: 10px;">If you have any suggestion regarding the NotificationX Analytics Report, do not hesitate to reply to this mail.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
</tbody>
</table>
</body>
</html>
NXTEMFOOTER;
        return $output;
    }

    public function body_header( $args = array(), $frequency ){
        $args = current( $args );
        $logo = esc_url( NOTIFICATIONX_PUBLIC_URL . 'image/reports/logo.png' );
        $from_date = isset( $args['from_date'] ) ? date( 'M j, Y', strtotime( $args['from_date'] ) ) : '';
        $to_date = isset( $args['to_date'] ) ? date( 'M j, Y', strtotime( $args['to_date'] ) ) : '';

        if( empty( $from_date ) || empty( $to_date ) ) {
            return '';
        }

        if( $frequency !== 'nx_daily' ) {
            $to_date = "- " . $to_date;
        } else {
            $to_date = '';
        }

        $output = <<<NXBODYHEADER
<tr>
    <td class="nx-email-header">
        <table width="100%" cellpadding="0" cellspacing="0" align="center">
            <tbody>
                <tr>
                    <td align="left">
                        <a href=""><img class="nx-email-logo" style="display: block; max-width: 100%;" src="$logo" alt=""></a>
                    </td>
                    <td class="nx-mobile-font" align="right" style="font:normal 14px 'Roboto',sans-serif">
                        <font color="#848484">Your Analytics <span class="il">Report</span></font><br><font color="#444444">$from_date $to_date</font>
                    </td>
                </tr>
            </tbody>
        </table>
    </td> <!-- Header LOGO, Date Range -->
</tr>
NXBODYHEADER;
        return $output;
    }

    public function body( $args = array(), $frequency ){
        if( empty( $args ) ) {
            return '';
        }
        $body_header = $this->body_header( $args, $frequency );

        $analytics_box = '';

        if( is_array( $args ) ) {
            foreach( $args as $analytics_key => $analytics_value ) {
                $analytics_box .= $this->analytics_box( $analytics_value, $frequency );
            }
        }

        $pro_msg = $this->pro_message();
        $overall_promo_text = $this->promo( $args, $frequency );

        $output = <<<NXTEMBODY
$body_header
$overall_promo_text
$analytics_box
$pro_msg
NXTEMBODY;
        return $output;
    }

    public function template_body( $args, $frequency ){
        $output = $this->header() . $this->body( $args, $frequency ) . $this->footer();
        return $output;
    }

    public function promo( $args = array(), $frequency ){
        if( empty( $args ) ) {
            return false;
        }

        $views = array_reduce( $args, function( $carry, $item ) {
            $carry += $item['views'];
            return $carry;
        } );

        // $last_wk_views = array_reduce( $args, function( $carry, $item ) {
        //     $carry += $item['last_views'];
        //     return $carry;
        // } );

        $clicks = array_reduce( $args, function( $carry, $item ) {
            $carry += $item['clicks'];
            return $carry;
        } );

        // $last_wk_clicks = array_reduce( $args, function( $carry, $item ) {
        //     $carry += $item['last_clicks'];
        //     return $carry;
        // } );

        $ctr = array_reduce( $args, function( $carry, $item ) {
            $carry += $item['ctr'];
            return $carry;
        } );

        $views = number_format( $views );
        $clicks = number_format( $clicks );
        $ctr = number_format( $ctr );

        $text_lead = 'In the last 7 Days';

        if( $frequency === 'nx_daily' ) {
            $text_lead = 'Yesterday';
        }

        if( $frequency === 'nx_monthly' ) {
            $text_lead = 'In the last month';
        }
        $text_lead = esc_html( $text_lead );

        $output = <<<NXPROMO
<tr>
    <td class="nx-mobile-font" style="line-height: 1.5;">
        <p style="margin: 0px; color:#555555">$text_lead NotificationX helped you have site visits of <b>$views</b>, total Click of <b>$clicks</b>, and total CTR of <b>$ctr</b></p>
    </td>
</tr> <!-- Overall Text -->
NXPROMO;
        return $output;
    }

    public static function analytics_box( $args = array(), $frequency = 'nx_weekly' ){
        if( empty( $args ) ) {
            return false;
        }

        $type              = $args['type'];
        $title             = esc_html( $args['title'] );
        $views             = number_format( $args['views'] );
        $percentage_views  = esc_html( $args['percentage_views'] );
        $clicks            = number_format( $args['clicks'] );
        $percentage_clicks = esc_html( $args['percentage_clicks'] );
        $ctr               = number_format( $args['ctr'] );
        $percentage_ctr    = esc_html( $args['percentage_ctr'] );

        $up_arrow = $v_arrow = $c_arrow = $ctr_arrow = esc_url( NOTIFICATIONX_PUBLIC_URL . 'image/reports/nx-template-up.png' );
        $down_arrow = esc_url( NOTIFICATIONX_PUBLIC_URL  . 'image/reports/nx-template-down.png' );
        $v_color = $c_color = $ctr_color = '#34cf8a';
        if( $percentage_views < 0 ) {
            $v_color = '#ff616c';
            $v_arrow = $down_arrow;
        }
        if( $percentage_clicks < 0 ) {
            $c_color = '#ff616c';
            $c_arrow = $down_arrow;
        }
        if( $percentage_ctr < 0 ) {
            $ctr_color = '#ff616c';
            $ctr_arrow = $down_arrow;
        }

        $percentage_views = NotificationX_Helper::nice_number( $args['percentage_views'] );
        $percentage_clicks = NotificationX_Helper::nice_number( $args['percentage_clicks'] );
        $percentage_ctr = NotificationX_Helper::nice_number( $args['percentage_ctr'] );

        if( is_array( $type ) ) {
            $type_name = esc_html( $type['source'] );
        } else {
            $type_name = esc_html( $type );
        }

        switch( $frequency ) {
            case 'nx_weekly' :
                $days_ago = '7 days ago';
                break;
            case 'nx_daily' :
                $days_ago = '1 days ago';
                break;
            case 'nx_monthly' :
                $initial_timestamp = strtotime('first day of last month', current_time('timestamp'));
                $days_in_last_month = cal_days_in_month(CAL_GREGORIAN, date( 'm', $initial_timestamp ), date( 'Y', $initial_timestamp ));
                $days_ago = $days_in_last_month . ' days ago';
                break;
        }

        $days_ago  = esc_html( $days_ago );
        $v_color   = esc_attr( $v_color );
        $c_color   = esc_attr( $c_color );
        $ctr_color = esc_attr( $ctr_color );

        $output = <<<NXBOXTEM
<tr>
    <td  class="nx-box-analytics-parent">
        <table class="nx-box-analytics" cellspacing="10" cellpadding="0" border="0" align="center" width="100%">
            <tbody>
                <tr>
                    <td align="left" class="nx-mobile-font" colspan="3" style="font-size: 13px;">
                    <strong>$type_name</strong> > $title
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #e5ecf2" width="33.333%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                            <tbody>
                                <tr>
                                    <td align="center" class="nx-mobile-font" style="background-color: #e5ecf2; text-transform: uppercase; padding: 10px 0px; font-size: 14px;">
                                        Total Views
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nx-mobile-font" align="center" style="padding: 10px 5px; font-size: 26px;">
                                        $views
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nx-mobile-font" style="padding:3px 10px 10px;font:700 10px" align="center">
                                        <font color="$v_color"><img class="nx-mobile-icon" src="$v_arrow" alt="" style="padding-right:5px; width:19px; vertical-align: text-bottom;">$percentage_views%</font>
                                        <br><font color="#909090">$days_ago</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td> <!-- BOX END -->
                    <td style="border:1px solid #e5ecf2;" width="33.333%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                            <tbody>
                                <tr>
                                    <td align="center" class="nx-mobile-font" style="background-color: #e5ecf2; text-transform: uppercase; padding: 10px 0px; font-size: 14px;">
                                        Total Clicks
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nx-mobile-font" align="center" style="padding: 10px 5px; font-size: 26px;">
                                        $clicks
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nx-mobile-font" style="padding:3px 10px 10px;font:700 10px" align="center">
                                        <font color="$c_color"><img class="nx-mobile-icon" src="$c_arrow" alt="" style="padding-right:5px; width:19px; vertical-align: text-bottom;">$percentage_clicks%</font>
                                        <br><font color="#909090">$days_ago</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td> <!-- BOX END -->
                    <td style="border:1px solid #e5ecf2" width="33.333%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                            <tbody>
                                <tr>
                                    <td align="center" class="nx-mobile-font" style="background-color: #e5ecf2; text-transform: uppercase; padding: 10px 0px; font-size: 14px;">
                                        Total CTR
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nx-mobile-font" align="center" style="padding: 10px 5px; font-size: 26px;">
                                        $ctr
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nx-mobile-font" style="padding:3px 10px 10px;font:700 10px" align="center">
                                        <font color="$ctr_color"><img class="nx-mobile-icon" src="$ctr_arrow" alt="" style="padding-right:5px; width:19px; vertical-align: text-bottom;">$percentage_ctr%</font>
                                        <br><font color="#909090">$days_ago</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td> <!-- BOX END -->
                </tr>
            </tbody>
        </table>
    </td>
</tr> <!-- Analytics BOX ROW -->
NXBOXTEM;
        return $output;
    }

    public static function pro_message(){
        $is_pro              = defined( 'NOTIFICATIONX_PRO_VERSION' );
        $graph               = esc_url( NOTIFICATIONX_PUBLIC_URL . 'image/reports/graph.png' );
        $admin_analytics_url = admin_url( 'admin.php?page=nx-analytics' );
        if( $is_pro ) {
            $output = <<<NXPROMSG
<tr>
    <td class="nx-mobile-font nx-pro-message" align="center" style="font-size: 15px; line-height: 1.7; color: #737373;">
        <a href="$admin_analytics_url" target="_blank"><img style="display: block; max-width: 100%; padding: 15px 0 0" src="$graph" alt="Visit Dashboard"></a>
        <a style="margin-top: 20px; background-color: #6125d5; color: #FFF; display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px;" href="$admin_analytics_url">Visit Dashboard</a>
    </td>
</tr>
NXPROMSG;
            return $output;
        }

        $output = <<<NXPROMSG
<tr>
    <td class="nx-mobile-font nx-pro-message" align="center" style="font-size: 15px; line-height: 1.7; color: #737373;">
        <p style="text-align: left; margin-top: 0px;">Resolve doubts from the minds of your users using NotificaitonX- the best Social Proof and FOMO Plugin for WordPress.</p>
        <p style="text-align: left; margin-top: 0px;">Get everything NotificationX has to offer by upgrading to a PRO plan. </p>
        <a target="_blank" href="https://notificationx.com/in/advanced-reports"><img style="display: block; max-width: 100%; padding: 15px 0 0" src="$graph" alt=""></a>
        <a style="margin-top: 20px; background-color: #6125d5; color: #FFF; display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px;" target="_blank" href="https://notificationx.com/in/advanced-reports">Get More Data</a>
    </td>
</tr>
NXPROMSG;
        return $output;
    }
}