<?php
/**
 * @author John Hargrove
 * 
 * Date: Jul 6, 2010
 * Time: 11:43:49 PM
 */

require_once "../../../wp-load.php";
require_once "boot-strap.php";

$tncBuilder = new WPAM_TermsCompiler(get_option(WPAM_PluginConfig::$TNCOptionOption));

?>

<html>
<head>
	<title>Terms & Conditions</title>
	<style type="text/css">
		body { font-family: ariel, tahoma, sans-serif; font-size: 0.8em;  }
	</style>
</head>
<body>
<div style="white-space: pre-wrap;">
<?php echo  $tncBuilder->build()?>	
</div>
</body>
</html>
