<?php
class pagesViewCfs extends viewCfs {
    public function displayDeactivatePage() {
        $this->assign('GET', reqCfs::get('get'));
        $this->assign('POST', reqCfs::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqCfs::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqCfs::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

