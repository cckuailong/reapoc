<?php
class dispatcherUms {
    static protected $_pref = 'ums_';

    static public function addAction($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        if(strpos($tag, 'ums_') === false) 
            $tag = self::$_pref. $tag;
        return add_action( $tag, $function_to_add, $priority, $accepted_args );
    }
    static public function doAction($tag) {
        if(strpos($tag, 'ums_') === false)
            $tag = self::$_pref. $tag;
        $numArgs = func_num_args();
        if($numArgs > 2) {
			$args = array($tag);
            for($i = 1; $i < func_num_args(); $i++) {
                $args[] = func_get_arg($i);
            }
            return call_user_func_array('do_action', $args);
        } elseif($numArgs == 2) {
            $args = func_get_arg(1);
        } else
            $args = NULL;
        return do_action($tag, $args);
    }
    static public function addFilter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        if(strpos($tag, 'ums_') === false)
            $tag = self::$_pref. $tag;
        return add_filter( $tag, $function_to_add, $priority, $accepted_args );
    }
    static public function applyFilters($tag, $value) {
        if(strpos($tag, 'ums_') === false)
            $tag = self::$_pref. $tag;
		$numArgs = func_num_args();
        if($numArgs > 2) {
            $args = array($tag);
            for($i = 1; $i < func_num_args(); $i++) {
                $args[] = func_get_arg($i);
            }
            return call_user_func_array('apply_filters', $args);
        } else {
            return apply_filters( $tag, $value );
        }
    }
}
