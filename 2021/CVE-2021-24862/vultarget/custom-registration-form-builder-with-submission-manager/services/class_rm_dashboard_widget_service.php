<?php

/**
 *
 *
 * @author CMSHelplive
 */
class RM_Dashboard_Widget_Service extends RM_Services
{
    public function get_count_summary()
    {
        $Q = 'COUNT(#UID#) AS count';
        $WQ_today = "`submitted_on` BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)";
        $WQ_week  = "`submitted_on` BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)";
        $WQ_month = "`submitted_on` BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())";
        
        $c1 = RM_DBManager::get_generic('SUBMISSIONS', $Q, $WQ_today);
        $c2 = RM_DBManager::get_generic('SUBMISSIONS', $Q, $WQ_week);
        $c3 = RM_DBManager::get_generic('SUBMISSIONS', $Q, $WQ_month);
        
        $c1 = !is_array($c1) ? 0 : $c1[0]->count;
        $c2 = !is_array($c2) ? 0 : $c2[0]->count;
        $c3 = !is_array($c3) ? 0 : $c3[0]->count;
        
        return (object)array('today'=> $c1,'this_week'=> $c2,'this_month'=> $c3);
    }
    
}