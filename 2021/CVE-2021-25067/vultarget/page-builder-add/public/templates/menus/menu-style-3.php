<?php if ( ! defined( 'ABSPATH' ) ) exit;?>
<style type="text/css">
<?php $thisMenuWidgetId = "#widget-$j-$Columni-".$row["rowID"]; ?>

<?php echo $thisMenuWidgetId; ?> #lpp_logo{
	max-width: 320px;
    max-height: 80px;
}
<?php echo $thisMenuWidgetId; ?> #lpp_logo img{
	max-width: 320px;
    max-height: 80px;
}
<?php echo $thisMenuWidgetId; ?> #pb-nav-res-button > span{
	float: left;
	font-size: 60px;
	color:<?php echo $menuColor; ?>;
	cursor: pointer;
}
<?php echo $thisMenuWidgetId; ?> #pb-nav-res-button > img{
	float: left;
	width:95%;
	cursor: pointer;
}
<?php echo $thisMenuWidgetId; ?> #pb-nav-res-button{
	display: none;
	width:7%;
}
<?php echo $thisMenuWidgetId; ?>.custom-logo{
	max-width:380px;
	max-height: 95px;
}
<?php echo "#lpb_menu_widget-$j"; ?> ul{
	list-style:none;
	margin:0;
	padding:0
}

<?php echo "#lpb_menu_widget-$j"; ?> ul a{
	display:block;
	color:<?php echo $menuColor; ?> !important;
	text-decoration:none;
	padding:0 15px;
	font-family:<?php echo $pbMenuFontFamily;  ?>,sans-serif;
	font-size: <?php echo $pbMenuFontSize;  ?>px ;
}

<?php echo "#lpb_menu_widget-$j"; ?> ul li{
	position:relative;
	float:left;
	margin:0;
}

<?php echo "#lpb_menu_widget-$j"; ?> ul li.current-menu-item{
	color: <?php echo $pbMenuFontHoverColor;  ?>;
	background-color: <?php echo $pbMenuFontHoverBgColor;  ?>;

}

<?php echo "#lpb_menu_widget-$j"; ?> ul a:hover{
	color: <?php echo $pbMenuFontHoverColor;  ?> !important;
	background-color: <?php echo $pbMenuFontHoverBgColor;  ?>;
	border-radius: 3px;
	transition: all 0.5s;
	-webkit-transition: all 0.5s;
}

<?php echo "#lpb_menu_widget-$j"; ?> ul ul{
	display:none;
	position:absolute;
	top:100%;
	left:0;
	background:#fff;
	padding:0;
	z-index: 999;
}

<?php echo "#lpb_menu_widget-$j"; ?> ul ul li{
	float:none;
	width:200px;
}

<?php echo "#lpb_menu_widget-$j"; ?> ul ul a{
}

<?php echo "#lpb_menu_widget-$j"; ?> ul ul ul{
	top:0;
	left:100%
}

<?php echo "#lpb_menu_widget-$j"; ?> ul li:hover > ul{
	display:block
}

@media screen and (max-width: 1080px) {
	#<?php echo "#lpb_menu_widget-$j"; ?> ul a{
		font-size: 14px;
		padding:0 15px;
}

@media screen and (max-width: 780px) {

	#lpp_logo{display: none;}
	<?php echo "#lpb_menu_widget-$j"; ?>{ display: none; }
	#pb-nav-res-button {display: block; margin-left: 3%;}

	<?php echo "#lpb_menu_widget-$j"; ?>{
		text-align: left;
		float: left;
		margin-left: 10%;
		width:60%;
	}
	<?php echo "#lpb_menu_widget-$j"; ?> ul li{
		position:relative;
		float: none;
		margin:0;
		padding:5px;
	}

	<?php echo "#lpb_menu_widget-$j"; ?> ul li{
	}

	.<?php echo "widget-$j"; ?>{
		display: block !important;
	}

}
</style>
<?php
ob_start();
$widgetFALoadScripts = true;
?>
<div id="pb-nav-res-button">
 	<i class="fas fa-bars navBtnImg" style="font-size:40px;"></i>
 	<i class="fas fa-bars navBtnImgActive"  style="display: none; font-size:40px;" ></i>
</div>

<?php
wp_nav_menu( array( 'menu' => $menuName, 'container_id' => "lpb_menu_widget-$j" ) );
$this_widget_menu = ob_get_contents();
ob_end_clean();

ob_start();
?>


	(function($){
		$('.navBtnImg').click(function(){
			$('<?php echo "#lpb_menu_widget-$j"; ?>').show();
			$('.navBtnImg').hide();
			$('.navBtnImgActive').show();
		});
		$('.navBtnImgActive').click(function(){
			$('<?php echo "#lpb_menu_widget-$j"; ?>').hide();
			$('.navBtnImgActive').hide();
			$('.navBtnImg').show();
		});
		var currWindowsize = $(window).width();
		$(window).resize(function() {
	  		var currWindowsize = $(window).width();
	  		if (currWindowsize > 780) {
			$('#pb-nav-res-button').hide();
			$('.navBtnImgActive').hide();
			$('<?php echo "#lpb_menu_widget-$j"; ?>').show();
		}else{
			$('#pb-nav-res-button').show();
			$('.navBtnImg').show();
			$('<?php echo "#lpb_menu_widget-$j"; ?>').hide();
		}
		});
	})(jQuery);

<?php
$this_widget_menu_script = ob_get_contents();
ob_end_clean();

array_push($POPBallWidgetsScriptsArray, $this_widget_menu_script);

?>