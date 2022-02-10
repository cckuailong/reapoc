<div class="pp-version-notice-upgrade-menu-item-page">
    <p>
        <span class="dashicons dashicons-smiley spin bounce"></span>
        <div class="message"><?php echo esc_html($message); ?></div>
    </p>

    <script type="application/javascript">
        window.setTimeout(
            function () {
                window.location.replace("<?php echo esc_url($link); ?>");
            },
            600
        );
    </script>
</div>