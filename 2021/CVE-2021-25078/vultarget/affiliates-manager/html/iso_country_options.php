	<option value="--"></option>

	<?php
	foreach (WPAM_Validation_CountryCodes::get_countries() as $code => $name)
	{
		echo '<option value="'.$code.'"';
		if ($this->viewData['request']['country'] == $code)
			echo ' selected="selected"';
		echo '>'.$name.'</option>';
	}
	?>
