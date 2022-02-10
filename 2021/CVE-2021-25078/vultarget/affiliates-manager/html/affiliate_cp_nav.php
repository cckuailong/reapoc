<?php
$current_tab = 'sub=overview';
if(isset($_GET['sub'])){
    $current_tab = 'sub='.sanitize_text_field($_GET['sub']);
}
?>
<div id="aff-controls" class="pure-menu pure-menu-open pure-menu-horizontal wpam-nav-menu">
<ul class="pure-menu-list">
<?php foreach($this->viewData['navigation'] as $link) { list($linkText, $linkHref) = $link; ?>
        <?php
        $active_class = '';
        if (strpos($linkHref, $current_tab) !== false) {
            $active_class = ' pure-menu-selected';
        }
        ?>
        <li class="pure-menu-item<?php echo $active_class;?>"><a class="pure-menu-link" href="<?php echo $linkHref?>"><?php echo $linkText?></a></li>
<?php } ?>
</ul>
</div>