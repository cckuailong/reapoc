<?php
if (!defined('WPINC')) {
    die('Closed');
}

$field_template= strtolower($data->selected_field);
include RM_ADMIN_DIR."views/widgets/$field_template.php";
?>