<script type="application/javascript">
    jQuery(function ($) {
        var linksMap = JSON.parse('<?php echo json_encode($convertedUrlsMap); ?>'),
            urlData = null;

        // Remove the domain and admin folder from the URL to match the menu's url.
        for (var i = 0; i < linksMap.length; i++) {
            urlData = linksMap[i];

            $('a.pp-version-notice-upgrade-menu-item.' + urlData.pluginName).attr('target', '_blank').attr('href', urlData.redirectTo);
        }
    });
</script>