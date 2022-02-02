<?php
if (!defined("ABSPATH")) {
    exit();
}

/* ===== CLOSED COMMNETS ===== */
$closedRegenerateDataCount = intval($this->dbManager->getClosedRegenerateCount());
$disabledClosed = $closedRegenerateDataCount ? "" : "disabled='disabled'";
/* ===== CLOSED COMMNETS ===== */

/* ===== VOTE DATA ===== */
$voteDataRegenerateCount = intval($this->dbManager->getVoteDataRegenerateCount());
$disabledVoteData = $voteDataRegenerateCount ? "" : "disabled='disabled'";
/* ===== VOTE DATA ===== */

/* ===== VOTE META ===== */
$voteRegenerateDataCount = intval($this->dbManager->getVoteRegenerateCount());
$disabledVoteMeta = $voteRegenerateDataCount ? "" : "disabled='disabled'";
/* ===== VOTE META ===== */
/* ===== SYNCRONIZE ===== */
$showSyncMessage = intval(get_option(self::OPTION_SLUG_SHOW_SYNC_COMMENTERS_MESSAGE));
/* ===== SYNCRONIZE ===== */
?>
<div class="wpdtool-accordion-item">

    <div class="fas wpdtool-accordion-title" data-wpdtool-selector="wpdtool-<?php echo $tool["selector"]; ?>">
        <p><?php esc_html_e("Regenerate Comments' Data", "wpdiscuz"); ?></p>        
    </div>

    <div class="wpdtool-accordion-content">

        <div class="wpdtool wpdtool-regenerate-closed-comments">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can regenerate closed comments.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-closed-regenerate"); ?>
                <div class="wpdtool-block">
                    <button <?php echo $disabledClosed; ?> type="submit" class="button button-secondary regenerate-closed-comments" title="<?php esc_attr_e("Start Regenerate", "wpdiscuz"); ?>">
                        <?php esc_html_e("Regenerate Closed Comments", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <input <?php echo $disabledClosed; ?> type="number" name="closed-regenerate-limit" value="500" min="1" class="closed-regenerate-limit"/>
                    <span class="closed-regenerate-import-progress">&nbsp;</span>
                    <input type="hidden" name="closed-regenerate-start-id" value="0" class="closed-regenerate-start-id"/>
                    <input type="hidden" name="closed-regenerate-count" value="<?php echo esc_attr($closedRegenerateDataCount); ?>" class="closed-regenerate-count"/>
                    <input type="hidden" name="closed-regenerate-step" value="0" class="closed-regenerate-step"/>
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-regenerate-vote-data">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can regenerate comment vote data.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-regenerate-vote-data"); ?>
                <div class="wpdtool-block">
                    <button <?php echo $disabledVoteData; ?> type="submit" class="button button-secondary regenerate-vote-data" title="<?php esc_attr_e("Start Regenerate", "wpdiscuz"); ?>">
                        <?php esc_html_e("Regenerate Vote Data", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <input <?php echo $disabledVoteData; ?> type="number" name="regenerate-vote-data-limit" value="500" min="1" class="regenerate-vote-data-limit"/>
                    <span class="regenerate-vote-data-import-progress">&nbsp;</span>
                    <input type="hidden" name="regenerate-vote-data-start-id" value="0" class="regenerate-vote-data-start-id"/>
                    <input type="hidden" name="regenerate-vote-data-count" value="<?php echo esc_attr($voteDataRegenerateCount); ?>" class="regenerate-vote-data-count"/>
                    <input type="hidden" name="regenerate-vote-data-step" value="0" class="regenerate-vote-data-step"/>
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-regenerate-vote-meta">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can regenerate comment vote metas.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-vote-regenerate"); ?>
                <div class="wpdtool-block">
                    <button <?php echo $disabledVoteMeta; ?> type="submit" class="button button-secondary regenerate-vote-metas" title="<?php esc_attr_e("Start Regenerate", "wpdiscuz"); ?>">
                        <?php esc_html_e("Regenerate Vote Metas", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <input <?php echo $disabledVoteMeta; ?> type="number" name="vote-regenerate-limit" value="500" min="1" class="vote-regenerate-limit"/>
                    <span class="vote-regenerate-import-progress">&nbsp;</span>
                    <input type="hidden" name="vote-regenerate-start-id" value="0" class="vote-regenerate-start-id"/>
                    <input type="hidden" name="vote-regenerate-count" value="<?php echo esc_attr($voteRegenerateDataCount); ?>" class="vote-regenerate-count"/>
                    <input type="hidden" name="vote-regenerate-step" value="0" class="vote-regenerate-step"/>
                </div>
            </form>
        </div>

        <div class="wpdtool wpdtool-synchronize-author-data">
            <p class="wpdtool-desc"><?php esc_html_e("Using this tool you can synchronize comment author data.", "wpdiscuz"); ?></p>
            <form action="" method="post" class="wc-tools-settings-form wc-form">
                <?php wp_nonce_field("wc_tools_form", "wpd-sync-commenters"); ?>
                <div class="wpdtool-block">
                    <button <?php echo $showSyncMessage ? "" : "disabled='disabled'"; ?> type="submit" class="button button-secondary sync-commenter-data" title="<?php esc_attr_e("Start Sync", "wpdiscuz"); ?>">
                        <?php esc_html_e("Synchronize Commenters Data", "wpdiscuz"); ?>&nbsp;
                        <i class="fas wc-hidden"></i>
                    </button>
                    <span class="sync-commenter-import-progress"></span>
                </div>
            </form>
        </div>

    </div>
</div>