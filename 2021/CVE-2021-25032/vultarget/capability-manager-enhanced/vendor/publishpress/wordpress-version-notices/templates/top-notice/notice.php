<?php
$linkStart = '</div><div class="pp-version-notice-bold-purple-button"><a href="' . $linkURL . '" target="_blank">';
$linkEnd   = '</a></div>';
$message   = sprintf('<div class="pp-version-notice-bold-purple-message">' . $message, $linkStart, $linkEnd);
?>
<div class="pp-version-notice-bold-purple"><?php echo $message; ?></div>