<?php
class admin_navViewCfs extends viewCfs {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', dispatcherCfs::applyFilters('mainBreadcrumbs', $this->getModule()->getBreadcrumbsList()));
		return parent::getContent('adminNavBreadcrumbs');
	}
}
