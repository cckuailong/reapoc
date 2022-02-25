<div class="ps_global_sidebar_menu">
  <ul class="ps_global_menu">
    <?php
    $c = 0;
    foreach (prsv_get('ps_meta')->get_all_options_fields() as $pagecomposer => $page) {
      ?>
      <li>
        <a class="ps_settings_tablinks <?php echo $c == 0 ? 'active' : '' ?>" onclick="openTab(event, '<?php echo $pagecomposer ?>')" href="#"><i class="pswp_set_icon-<?php echo $page['icon'] ?>"></i> <?php echo $page['title_page'] ?></a>
      </li>
      <?php
      $c++;
    }
    ?>
    <li>
      <a class="ps_settings_tablinks" onclick="openTab(event, 'debug_status')" href="#">Debug</a>
    </li>
  </ul>
</div>
