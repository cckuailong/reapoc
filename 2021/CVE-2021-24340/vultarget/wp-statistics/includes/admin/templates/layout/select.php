<?php
if (isset($list) and is_array($list) and count($list) > 0) {
    ?>
    <form action="" method="get" id="wp-statistics-select-pages">
        <span class="select-title"><?php _e('Select Page', 'wp-statistics'); ?>:</span>
        <input name="page" type="hidden" value="<?php echo $pageName; ?>">
        <?php
        if (isset($custom_get)) {
            foreach ($custom_get as $key => $val) {
                if ($key == "ID") {
                    continue;
                }
                ?>
                <input name="<?php echo $key; ?>" type="hidden" value="<?php echo $val; ?>">
                <?php
            }
        }
        ?>
        <select name="ID" data-type-show="select2">
            <?php
            foreach ($list as $id => $name) {
                ?>
                <option value="<?php echo $id; ?>" <?php selected($_GET['ID'], $id); ?>><?php echo $name; ?></option>
                <?php
            }
            ?>
        </select><span class="submit-form"></span>
    </form><br/>
<?php } ?>