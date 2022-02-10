<?php
class Mappress_Obj {
	function __construct($atts=null) {
		$this->update($atts);
	}

	function update($atts=null) {
		if (!$atts)
			return;

		$obj_atts = get_object_vars($this);
		$atts = (array) $atts;

		// Only the object's attributes are updated
		foreach ($obj_atts as $key => $value ) {
			$newvalue = (isset($atts[$key])) ? $atts[$key] : null;

			// Allow attributes to be all lowercase to handle shortcodes
			if ($newvalue === null) {
				$lkey = strtolower($key);
				$newvalue = (isset($atts[$lkey])) ? $atts[$lkey] : null;
			}

			if ($newvalue === null)
				continue;

			// Convert any string versions of true/false
			if ($newvalue === "true")
				$newvalue = true;
			if ($newvalue === "false")
				$newvalue = false;

			$this->$key = $newvalue;
		}
	}
}
?>