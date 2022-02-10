<!-- widget_form_errors -->
<?php

if (isset($this->viewData['validationResult']) && !$this->viewData['validationResult']->getIsValid())
{
?>
	<script type="text/javascript">

		jQuery(document).ready(function() {
<?php
	foreach ($this->viewData['validationResult']->getErrors() as $error)
	{
		?>
		var label = jQuery("label[for=<?php echo $error->getFieldName()?>]");

		label.addClass("wpam_form_error");
		jQuery("#wpam_form_error_panel > ul").append("<li>" + label.html() + " <?php echo $error->getMessage()?></li>");
		<?php
	}
?>
	});

	</script>

<?php
}
?>