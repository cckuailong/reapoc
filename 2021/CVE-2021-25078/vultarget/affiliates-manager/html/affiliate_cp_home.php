<div class="aff-wrap">
    <?php
    include WPAM_BASE_DIRECTORY . "/html/affiliate_cp_nav.php";
    ?>

    <div class="wrap">

        <table class="pure-table">
            <thead>
                <tr>
                    <th colspan="2"><?php _e('Account Summary', 'affiliates-manager') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php _e('Balance', 'affiliates-manager') ?></td>
                    <td><?php echo wpam_format_money($this->viewData['accountStanding']) ?></td>
                </tr>
                <tr>
                    <td><?php _e('Commission Rate', 'affiliates-manager') ?></td>
                    <td><?php echo $this->viewData['commissionRateString'] ?></td>
                </tr>
            </tbody>
        </table>

        <table class="pure-table">
            <thead>
                <tr>
                    <th colspan="2"><?php _e('Today', 'affiliates-manager') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="summaryPanel">
                            <?php if (get_option(WPAM_PluginConfig::$AffEnableImpressions)) { ?>
                                <div class="summaryPanelLine">
                                    <div class="summaryPanelLineValue"><?php echo $this->viewData['todayImpressions'] ?></div>
                                    <div class="summaryPanelLineLabel"><?php _e('Impressions', 'affiliates-manager') ?></div>
                                </div>
                            <?php } ?>
                            <div class="summaryPanelLine">
                                <div class="summaryPanelLineValue"><?php echo $this->viewData['todayVisitors'] ?></div>
                                <div class="summaryPanelLineLabel"><?php _e('Visitors', 'affiliates-manager') ?></div>
                            </div>
                            <div class="summaryPanelLine">
                                <div class="summaryPanelLineValue"><?php echo $this->viewData['todayClosedTransactions'] ?></div>
                                <div class="summaryPanelLineLabel"><?php _e('Closed Transactions', 'affiliates-manager') ?></div>
                            </div>
                            <div class="summaryPanelLine">
                                <div class="summaryPanelLineValue"><?php echo $this->viewData['todayRevenue'] ?></div>
                                <div class="summaryPanelLineLabel"><?php _e('Revenue', 'affiliates-manager') ?></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <table class="pure-table">
            <thead>
                <tr>
                    <th colspan="2"><?php _e('This Month', 'affiliates-manager') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="summaryPanel">
                            <?php if (get_option(WPAM_PluginConfig::$AffEnableImpressions)) { ?>
                                <div class="summaryPanelLine">
                                    <div class="summaryPanelLineValue"><?php echo $this->viewData['monthImpressions'] ?></div>
                                    <div class="summaryPanelLineLabel"><?php _e('Impressions', 'affiliates-manager') ?></div>
                                </div>
                            <?php } ?>
                            <div class="summaryPanelLine">
                                <div class="summaryPanelLineValue"><?php echo $this->viewData['monthVisitors'] ?></div>
                                <div class="summaryPanelLineLabel"><?php _e('Visitors', 'affiliates-manager') ?></div>
                            </div>
                            <div class="summaryPanelLine">
                                <div class="summaryPanelLineValue"><?php echo $this->viewData['monthClosedTransactions'] ?></div>
                                <div class="summaryPanelLineLabel"><?php _e('Closed Transactions', 'affiliates-manager') ?></div>
                            </div>
                            <div class="summaryPanelLine">
                                <div class="summaryPanelLineValue"><?php echo ($this->viewData['monthRevenue']) ?></div>
                                <div class="summaryPanelLineLabel"><?php _e('Revenue', 'affiliates-manager') ?></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>