<?php
class dateUms {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(UMS_DATE_FORMAT_HIS, $time);
	}
}