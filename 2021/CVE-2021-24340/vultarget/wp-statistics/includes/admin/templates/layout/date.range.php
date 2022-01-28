<form action="<?php echo admin_url('admin.php'); ?>" method="get" class="wps-inline" id="jquery-datepicker">
    <?php
    if (isset($select_box)) {
        ?>
        <br />
        <?php echo $select_box['title']; ?>:&nbsp;
        <select name="<?php echo $select_box['name']; ?>" id="<?php echo $select_box['name']; ?>">
            <?php
            foreach ($select_box['list'] as $value => $name) {
                $selected = ((isset($select_box['active']) and $select_box['active'] == $value) ? ' selected' : '');
                ?>
                <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                <?php
            }
            ?>
        </select><input type="submit" value="<?php _e('Select', 'wp-statistics'); ?>" class="button-primary btn-danger wps-btn-inline"><br />
        <?php
    }
    ?>

    <ul class="subsubsub wp-statistics-sub-fullwidth">
        <?php
        foreach ($DateRang['list'] as $number_days => $value) {
            ?>
            <li class="all">
                <a <?php if ($value['active'] === true) { ?> class="current" <?php } ?> href="<?php echo $value['link']; ?>"><?php echo $value['title']; ?></a>
            </li> |
        <?php } ?>

        <!-- Show JQuery DatePicker -->
        <?php _e('Time Frame', 'wp-statistics'); ?>:

        <!-- Set Page name To Form -->
        <input name="page" type="hidden" value="<?php echo $pageName; ?>">

        <!-- Set Custom Input -->
        <?php
        if (isset($custom_get)) {
            foreach ($custom_get as $key => $val) {
                ?>
                <input name="<?php echo $key; ?>" type="hidden" value="<?php echo $val; ?>">
                <?php
            }
        }
        ?>

        <!-- set Page Pagination To Form -->
        <?php if (isset($pagination) and $pagination > 1) { ?>
            <input name="<?php echo \WP_STATISTICS\Admin_Template::$paginate_link_name; ?>" type="hidden" value="<?php echo $pagination; ?>">
        <?php } ?>

        <!-- Set Jquery DatePicker -->
        <input type="text" size="18" name="date-from" data-wps-date-picker="from" value="<?php echo $DateRang['from']; ?>" placeholder="YYYY-MM-DD" autocomplete="off">
        <?php _e('to', 'wp-statistics'); ?>
        <input type="text" size="18" name="date-to" data-wps-date-picker="to" value="<?php echo $DateRang['to']; ?>" placeholder="YYYY-MM-DD" autocomplete="off">
        <input type="submit" value="<?php _e('Go', 'wp-statistics'); ?>" class="button-primary">
        <input type="hidden" name="<?php echo \WP_STATISTICS\Admin_Template::$request_from_date; ?>" id="date-from" value="<?php echo $DateRang['from']; ?>">
        <input type="hidden" name="<?php echo \WP_STATISTICS\Admin_Template::$request_to_date; ?>" id="date-to" value="<?php echo $DateRang['to']; ?>">
</form>
<?php
if (isset($filter) and isset($filter['code'])) {
    echo $filter['code'];
    ?>
    <div class="wp-clearfix"></div>
    <?php
}
?>
</ul>
<script>
    jQuery('#jquery-datepicker').submit(function () {
        jQuery("input[data-wps-date-picker]").prop('disabled', true);
    });
</script>
