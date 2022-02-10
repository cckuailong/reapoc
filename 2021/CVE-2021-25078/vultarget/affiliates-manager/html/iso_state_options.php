<option value="--"></option>

<?php
foreach (WPAM_Validation_StateCodes::$stateCodes as $code => $name)
{
	echo '<option value="'.$code.'"';
	if ($this->viewData['request']['addressState'] == $code)
	{
		echo ' selected="selected"';
	}
	echo '>' . $name . '</option>';
}
?>