<div class="wrap">
    <div class="supsystic-plugin">
        <?php /*?><header class="supsystic-plugin">
            <h1><?php echo CFS_WP_PLUGIN_NAME?></h1>
        </header><?php */?>
		<?php echo $this->breadcrumbs?>
        <section class="supsystic-content">
            <nav class="supsystic-navigation supsystic-sticky <?php dispatcherCfs::doAction('adminMainNavClassAdd')?>">
                <ul>
					<?php foreach($this->tabs as $tabKey => $tab) { ?>
						<?php if(isset($tab['hidden']) && $tab['hidden']) continue;?>
						<li class="supsystic-tab-<?php echo $tabKey;?> <?php echo (($this->activeTab == $tabKey || in_array($tabKey, $this->activeParentTabs)) ? 'active' : '')?>">
							<a href="<?php echo $tab['url']?>" title="<?php echo $tab['label']?>">
								<?php if(isset($tab['fa_icon'])) { ?>
									<i class="fa <?php echo $tab['fa_icon']?>"></i>	
								<?php } elseif(isset($tab['wp_icon'])) { ?>
									<i class="dashicons-before <?php echo $tab['wp_icon']?>"></i>	
								<?php } elseif(isset($tab['icon'])) { ?>
									<i class="<?php echo $tab['icon']?>"></i>	
								<?php }?>
								<span class="sup-tab-label"><?php echo $tab['label']?></span>
							</a>
						</li>
					<?php }?>
                </ul>
            </nav>
            <div class="supsystic-container supsystic-<?php echo $this->activeTab?>">
				<?php dispatcherCfs::doAction('discountMsg');?>
				<?php echo $this->content?>
                <div class="clear"></div>
            </div>
        </section>
    </div>
</div>
<!--Option available in PRO version Wnd-->
<div id="cfsOptInProWnd" style="display: none;" title="<?php _e('Improve Free version', CFS_LANG_CODE)?>">
	<p id="cfsOptWndTemplateTxt" style="display: none;">
		<?php printf(__('Please be advised that this template with all other options and PRO templates is available only in <a target="_blank" href="%s">PRO version</a>. You can <a target="_blank" href="%s" class="button">Get PRO</a> today and get this and other PRO templates and features for your Forms!', CFS_LANG_CODE), $this->mainLink, $this->mainLink)?>
	</p>
	<p id="cfsOptWndOptionTxt">
		<?php printf(__('Please be advised that this option is available only in <a target="_blank" href="%s">PRO version</a>. You can <a target="_blank" href="%s" class="button">Get PRO</a> today and get this and other PRO option for your Forms!', CFS_LANG_CODE), $this->mainLink, $this->mainLink)?>
	</p>
</div>
