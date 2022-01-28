<?php
class dateCfs {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(CFS_DATE_FORMAT_HIS, $time);
	}
}