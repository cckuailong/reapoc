<style type="text/css">
#pb-nav-res-button > span{
	float: left;
	font-size: 60px;
	color:<?php echo $menuColor; ?>;
	cursor: pointer;
}
#pb-nav-res-button > img{
	float: left;
	width:100%;
	cursor: pointer;
}
#pb-nav-res-button{
	display: none;
	width:7%;
}
#lpp_logo{
	max-width: 240px !important;
    max-height: 80px !important;
    color:<?php echo $menuColor; ?> !important;
    display: inline-block;
}

#lpp_logo img{
	max-width: 200px !important;
    max-height: 80px;
}

#lpb_menu_widget{
	text-align: center;
	display: inline-block;
	margin-left: 10%;
}

#lpb_menu_widget ul{
	list-style:none;
	margin:0;
	padding:0
}
.custom-logo{
	max-width:380px;
	max-height: 95px;
}
#lpb_menu_widget ul a{
	display:block;
	color:<?php echo $menuColor; ?> !important;
	text-decoration:none;
	font-size: <?php echo $menuFontSize; ?>px !important;
	padding:5px 25px;
	font-family:<?php echo $menufont;  ?>,sans-serif;
}

#lpb_menu_widget ul li{
	position:relative;
	float:left;
	margin:0;
	padding:5px;
}

#lpb_menu_widget ul li.current-menu-item{
	background:#ddd;
}

#lpb_menu_widget ul a:hover{
	color: <?php echo $menufontHoverColor; ?> !important;
	background: <?php echo $menuFontHoverBgColor; ?> !important;
}

#lpb_menu_widget ul ul{
	display:none;
	position:absolute;
	top:100%;
	left:0;
	background:#fff;
	padding:0;
}

#lpb_menu_widget ul ul li{
	float:none;
	width:200px;
}

#lpb_menu_widget ul ul a{
}

#lpb_menu_widget ul ul ul{
	top:0;
	left:100%
}

#lpb_menu_widget ul li:hover > ul{
	display:block;
}

@media screen and (max-width: 1080px) {
	#lpb_menu_widget ul a{
		font-size: 14px;
		padding:0 15px;
}

@media screen and (max-width: 780px) {

	#lpp_logo{display: none;}
	#lpb_menu_widget{ display: none; }
	#pb-nav-res-button {display: block; margin-left: 3%;}

	#lpb_menu_widget{
		text-align: left;
	}
	#lpb_menu_widget ul li{
		position:relative;
		float: none;
		margin:0;
		padding:5px;
	}

	#lpb_menu_widget ul li{
		border-bottom: 1px solid rgba(0, 0, 0, 0.07);
	}


}
</style>
<?php
ob_start();
?>
<div id="lpp_logo"><?php 
if (!empty($pageLogoUrl)) {
  echo("<img src='$pageLogoUrl' alt='Site Logo'>");
} else{
	if(!has_custom_logo()) {
		?>
	    <h1 style="font-size:2.5em !important;"><?php bloginfo('name'); ?></h1>
	    <?php
	} else{ the_custom_logo();  }
}
 ?></div>
 <div id="pb-nav-res-button">
 				<span class="dashicons dashicons-menu navBtnImg"></span>
 				<span class="dashicons dashicons-menu navBtnImgActive" style="display: none;"></span>
 </div>

 <script type="text/javascript">
(function($){
	$('.navBtnImg').click(function(){
		$('#lpb_menu_widget').show();
		$('.navBtnImg').hide();
		$('.navBtnImgActive').show();
	});
	$('.navBtnImgActive').click(function(){
		$('#lpb_menu_widget').hide();
		$('.navBtnImgActive').hide();
		$('.navBtnImg').show();
	});
	var currWindowsize = $(window).width();
	$(window).resize(function() {
  		var currWindowsize = $(window).width();
  		if (currWindowsize > 780) {
		$('#pb-nav-res-button').hide();
		$('.navBtnImgActive').hide();
		$('#lpb_menu_widget').show();
	}else{
		$('#pb-nav-res-button').show();
		$('.navBtnImg').show();
		$('#lpb_menu_widget').hide();
	}
	});
})(jQuery);
</script>
 <?php
wp_nav_menu( array( 'menu' => $menuName, 'container_id' => 'lpb_menu_widget', 'menu_class' => 'w3-navbar' ) );
$this_widget_menu = ob_get_contents();
ob_end_clean();

?>