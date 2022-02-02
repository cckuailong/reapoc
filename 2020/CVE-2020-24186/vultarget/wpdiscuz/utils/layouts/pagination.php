<?php
if (!defined("ABSPATH")) {
    exit();
}

if ($pageCount && $pageCount > 1) {    
    if (isset($page)) {
        $start = $page - $lrItemsCount > 0 ? $page - $lrItemsCount : 0;
        $start = (($page + $lrItemsCount) >= $pageCount) ? ($pageCount - (2 * $lrItemsCount + 1)) : $start;
        $start = $start > 0 ? $start : 0;
        $end = $page + $lrItemsCount < $pageCount ? $page + $lrItemsCount : $pageCount;
        $end = $end < 2 * $lrItemsCount ? 2 * $lrItemsCount : $end;
        $end = $end < $pageCount - 1 ? $end : $pageCount - 1;
    } else {
        $start = 0;
        $end = $pageCount < (2 * $lrItemsCount) ? $pageCount - 1 : 2 * $lrItemsCount;
    }
    ?>
    <div class='wpd-pagination'>
        <?php
        if ($page - $lrItemsCount > 0) {
            ?>
            <a href='#0' class='wpd-page-link wpd-not-clicked' data-wpd-page='0'><?php esc_html_e("&laquo;", "wpdiscuz"); ?></a>
            <?php
        }
        if ($page > 0) {
            ?>
            <a href='#<?php echo esc_url_raw($page - 1); ?>' class='wpd-page-link wpd-not-clicked' data-wpd-page='<?php echo esc_attr($page - 1); ?>'><?php esc_html_e("&lsaquo;", "wpdiscuz"); ?></a>
            <?php
        }
        for ($i = $start; $i <= $end; $i++) {
            $pageText = intval($i + 1);
            if ($i < $pageCount) {
                if ($i == $page) {
                    ?>
                    <span style="background: <?php echo esc_attr($this->options->thread_styles["primaryColor"]); ?>;" class='wpd-page-link wpd-current-page' data-wpd-page='<?php echo esc_attr($i); ?>'><?php echo esc_html($pageText); ?></span>
                <?php } else { ?>
                    <a href='#<?php echo esc_url_raw($i); ?>' class='wpd-page-link wpd-not-clicked' data-wpd-page='<?php echo esc_attr($i); ?>'><?php echo esc_html($pageText); ?></a>
                    <?php
                }
            }
        }
        if ($page < $pageCount - 1) {
            ?>
            <a href='#<?php echo esc_url_raw($page + 1); ?>' class='wpd-page-link wpd-not-clicked' data-wpd-page='<?php echo esc_attr($page + 1); ?>'><?php esc_html_e("&rsaquo;", "wpdiscuz"); ?></a>
            <?php
        }
        if ($page + $lrItemsCount < $pageCount - 1) {
            ?>
            <a href='#<?php echo esc_url_raw(intval($pageCount) - 1); ?>' class='wpd-page-link wpd-not-clicked' data-wpd-page='<?php echo esc_attr(intval($pageCount) - 1); ?>'><?php esc_html_e("&raquo;", "wpdiscuz"); ?></a>
            <?php
        }
        ?>                    
        <input type='hidden' class='wpd-action' value='<?php echo esc_attr($action); ?>'/>
        <div class="clear"></div>
    </div>
    <?php
}