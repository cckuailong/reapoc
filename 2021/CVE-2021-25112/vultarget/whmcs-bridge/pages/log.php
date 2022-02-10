<?php
if ($cc_whmcs_bridge_version && get_option('cc_whmcs_bridge_debug')) {
	echo '<h2>Debug Log</h2><br/><div class="dbugoutput">';
	$r=get_option('cc_whmcs_bridge_log');
	if ($r) {
		$v=$r;
		foreach ($v as $m) {
            $stamp = explode(' ', trim($m[0]));

            echo '<h4 style="width:95%;background:#f7f7f7"><strong>'.date('d M y H:i:s', $stamp[1]).'</strong> ('.$stamp[0].'ms): <em>'.$m[1].'</em></h4>';
            echo '<div style="width:95%; word-wrap:break-word;">'.$m[2].'</div>';
		}
	}
	echo "</div>";
} else {
	echo 'If you have problems with the plugin, activate the debug mode to generate a debug log for our support team.';
}
