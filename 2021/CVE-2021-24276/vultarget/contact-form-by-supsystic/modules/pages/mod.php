<?php
class pagesCfs extends moduleCfs {
    /**
     * Check if current page is Login page
     */
    public function isLogin() {
		return (basename($_SERVER['SCRIPT_NAME']) == 'wp-login.php' 
				|| strpos($_SERVER['REQUEST_URI'], '/login/') === 0);	// Some plugins create login page by this address
    }
}

