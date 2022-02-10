<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
if (isset($_GET['tab'])){
  $currentTab = $_GET['tab'];
} else {
  $currentTab = 'api';
}
?>

<?php 
    if (!empty($GLOBALS['uaf_action_return'])):
        $actionReturn = $GLOBALS['uaf_action_return'];  
?>
        <div class="updated <?php echo $actionReturn['status']; ?>" id="message"><p><?php echo $actionReturn['body']; ?></p></div>
<?php    
    endif; 
?>

<div class="wrap dcwrap">

    <h1>Use Any Font</h1>

    <nav class="nav-tab-wrapper">
        <?php foreach ($uaf_tabs as $tabKey => $tabData) { ?>
            <a href="?page=use-any-font&tab=<?php echo $tabKey; ?>" class="nav-tab <?php echo $currentTab == $tabKey?'nav-tab-active':''; ?>"><?php echo $tabData['name']; ?></a>
        <?php } ?>
    </nav>

    <div class="tab-content">
        <table width="100%" class="noborder">
            <tr>
                <td valign="top" class="leftcontent">
                    <?php include($uaf_tabs[$currentTab]['path']); ?>
                </td>
                <td width="15">&nbsp;</td>
                <td width="250" valign="top"><?php include('uaf_sidebar.php'); ?></td>
            </tr>
        </table>
    </div>          
            
</div>