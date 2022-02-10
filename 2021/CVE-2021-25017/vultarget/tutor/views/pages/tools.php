<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e('Tools', 'tutor'); ?></h1>
	<hr class="wp-header-end">

	<nav class="nav-tab-wrapper tutor-nav-tab-wrapper">
		<?php
		if (tutils()->count($pages)){
			foreach ($pages as $key => $page){
				$title = is_array($page)? $page['title'] : $page;
				$active_class = $key == $current_page ? 'nav-tab-item-active' : '';
				$url = add_query_arg(array('sub_page' => $key ));
				echo "<a href='{$url}' class='nav-tab-item {$active_class} '>{$title}</a>";
			}
		}
		?>
	</nav>

	<div id="tutor-tools-page-wrap" class="tutor-tools-page-wrap">

		<?php
		do_action("tutor_tools_page_{$current_page}_before");

		if ( ! empty($pages[$current_page]['view_path']) && file_exists($pages[$current_page]['view_path'])){
			include $pages[$current_page]['view_path'];
		}elseif (file_exists(tutor()->path."views/pages/tools/{$current_page}.php")){
			include tutor()->path."views/pages/tools/{$current_page}.php";
		}else{
			do_action("tutor_tools_page_{$current_page}");
		}

		do_action("tutor_tools_page_{$current_page}_after");
		?>
	</div>

</div>