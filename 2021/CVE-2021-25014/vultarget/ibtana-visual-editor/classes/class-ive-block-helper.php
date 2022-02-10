<?php
/**
 * UAGB Block Helper.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'IVE_Block_Helper' ) ) {

	/**
	 * Class IVE_Block_Helper.
	 */
	class IVE_Block_Helper {

		/**
		 * Get block CSS
		 *
		 * @since 1.19.0
		 * @param array  $attr The block attributes.
		 * @param string $id The selector ID.
		 * @return array The Widget List.
		 */
		public static function get_button_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/ibtana-visual-editorbtn']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$unit = 'px';
			$index = 0;
			$typography = isset($attr['typography']) ? ($attr['typography']) : '';
			$fonttypography = str_replace(' ','+',$typography);
			$fontfamilyname = ($typography !== '') ? $fonttypography : 'Open+Sans';
			$iconGrad = $attr['iconGrad'];
			$background = isset($attr['btns'][$index]['background']) ? ($attr['btns'][$index]['background']) : 'transparent';
			$backgroundHov = isset($attr['btns'][$index]['backgroundHover']) ? ($attr['btns'][$index]['backgroundHover']) : 'transparent';
			$mobpaddingBT =  isset($attr['btns'][$index]['mobpaddingBT']) ? $attr['btns'][$index]['mobpaddingBT'] : 10;
			$mobpaddingLR =  isset($attr['btns'][$index]['mobpaddingLR']) ? $attr['btns'][$index]['mobpaddingLR'] : 10;
			$tabpaddingBT =  isset($attr['btns'][$index]['tabpaddingBT']) ? $attr['btns'][$index]['tabpaddingBT'] : 10;
			$tabpaddingLR =  isset($attr['btns'][$index]['tabpaddingLR']) ? $attr['btns'][$index]['tabpaddingLR'] : 10;
			$deskpaddingBT =  isset($attr['btns'][$index]['deskpaddingBT']) ? $attr['btns'][$index]['deskpaddingBT'] : 10;
			$deskpaddingLR =  isset($attr['btns'][$index]['deskpaddingLR']) ? $attr['btns'][$index]['deskpaddingLR'] : 10;
			$vBgImgPosition = isset($attr['vBgImgPosition']) ? $attr['vBgImgPosition'] : 'center center';
			$bgfirstcolorr = isset($attr['bgfirstcolorr']) ? $attr['bgfirstcolorr'] : '';
			$hovGradFirstColor = isset($attr['hovGradFirstColor']) ? $attr['hovGradFirstColor'] : '';
			$bgGradLoc = isset($attr['bgGradLoc']) ? $attr['bgGradLoc'] : 0;
			$bgSecondColr = isset($attr['bgSecondColr']) ? $attr['bgSecondColr'] : '';
			$hovGradSecondColor = isset($attr['hovGradSecondColor']) ? $attr['hovGradSecondColor'] : '';
			$bgGradLocSecond = isset($attr['bgGradLocSecond']) ? $attr['bgGradLocSecond'] : 100;
			$bgGradAngle = isset($attr['bgGradAngle']) ? $attr['bgGradAngle'] : 180;

			if('radial' === $attr['bgGradType']){
				$backgroundImage = 'radial-gradient(at '.$vBgImgPosition.','.$bgfirstcolorr.' '.$bgGradLoc.'%, '.$bgSecondColr.' '.$bgGradLocSecond.'%)';
			}else{
				$backgroundImage = 'linear-gradient('.$bgGradAngle.'deg, '.$bgfirstcolorr.' '.$bgGradLoc.'%, '.$bgSecondColr.'  '.$bgGradLocSecond.'%)';
			}

			if('radial' === $attr['bgGradType']){
				$backgroundImageHov = 'radial-gradient(at '.$vBgImgPosition.','.$hovGradFirstColor.' '.$bgGradLoc.'%, '.$hovGradSecondColor.' '.$bgGradLocSecond.'%)';
			}else{
				$backgroundImageHov = 'linear-gradient('.$bgGradAngle.'deg, '.$hovGradFirstColor.' '.$bgGradLoc.'%, '.$hovGradSecondColor.'  '.$bgGradLocSecond.'%)';
			}

			$hovericon = 'inline !important';
			if(isset($attr['iconDisableHover']) && $attr['iconDisableHover'] == 1){
			  $hovericon = 'none !important';
			}

			$selectors = array(
				' .anchrstyle' => array(
					'opacity' 				=> isset($attr['bgOpacity']) ? $attr['bgOpacity'] : 1,
					'text-decoration' 		=> 'none',
					'border-radius' 		=> isset($attr['btns'][$index]['borderRadius']) ? ($attr['btns'][$index]['borderRadius']).$unit : '0' . $unit,
					'border-width' 			=> isset($attr['btns'][$index]['borderWidth']) ? ($attr['btns'][$index]['borderWidth']).$unit : '0' . $unit,
					'border-color' 			=> isset($attr['btns'][$index]['border']) ? ($attr['btns'][$index]['border']) : '',
					'border-style' 			=> 'solid',
					'color' 				=> isset($attr['btns'][$index]['color']) ? $attr['btns'][$index]['color'] : '#555555',
					'letter-spacing' 		=> isset($attr['letterSpacing']) ? ($attr['letterSpacing']).$unit : '0' . $unit,
					'font-family' 			=> $typography,
					'font-style' 			=> isset($attr['fontStyle']) ? ($attr['fontStyle']) : 'normal',
					'font-weight' 			=> isset($attr['fontWeight']) ? ($attr['fontWeight']) : 'normal',
					'background-image' 		=> $iconGrad ? $backgroundImage : 'unset',
					'background-color' 		=> !$iconGrad ? $background : 'unset',
					'font-size'				=> isset($attr['btns'][$index]['desksize']) ? $attr['btns'][$index]['desksize'] . $unit : '18'.$unit,
					'padding'				=> $deskpaddingBT.$unit .' '. $deskpaddingLR . $unit,
					'box-shadow'			=> isset($attr['boxshadowcolor']) ? $attr['boxshadowpos'].' '. $attr['boxshadowx'] .$unit.' '. $attr['boxshadowY'].$unit.' '. $attr['boxshadowblur'].$unit.' '. $attr['boxshadowspread'].$unit.' '. $attr['boxshadowcolor'] : '' ,
					'display'				=> 'inline-block'
				),
				' .anchrstyle:hover' => array(
					'background-color' 		=> !$iconGrad ? $backgroundHov : 'unset',
					'color' 				=> isset($attr['btns'][$index]['colorHover']) ? $attr['btns'][$index]['colorHover'] : '#555555',
					'border-color' 			=> isset($attr['btns'][$index]['borderHover']) ? ($attr['btns'][$index]['borderHover']) : 'transparent',
					'background-image' 		=> $iconGrad ? $backgroundImageHov : 'unset',
					'box-shadow'			=> isset($attr['hoverboxshadowcolor']) ? $attr['hoverboxshadowpos'].' '. $attr['hoverboxshadowx'] .$unit.' '. $attr['hoverboxshadowY'].$unit.' '. $attr['hoverboxshadowblur'].$unit.' '. $attr['hoverboxshadowspread'].$unit.' '. $attr['hoverboxshadowcolor'] : '' ,
				),
				' .anchrstyle .ive-left-icon-parent' => array(
					'display' 				=> 'inline'
				),
				' .anchrstyle .ive-right-icon-parent' => array(
					'display' 				=> 'inline'
				),
				'.btn-inner-wrap' => array(
					'display'				=> $attr['deskvisible'] ? 'block' : 'none',
					'margin-top'			=> isset($attr['btns'][$index]['deskMarginTop']) ? $attr['btns'][$index]['deskMarginTop'] . $unit : '20'.$unit,
					'margin-bottom'			=> isset($attr['btns'][$index]['deskMarginBottom']) ? $attr['btns'][$index]['deskMarginBottom'] . $unit : '20'.$unit,
				),
				' .anchrstyle .ive-button-icon-padding'.$index.' i' => array(
					'font-size'				=> isset($attr['iconsize'][0]) ? $attr['iconsize'][0]. $unit : '12'. $unit
				),
				' .anchrstyle .ive-button-icon-padding'.$index => array(
					'padding-left'			=> isset($attr['btns'][$index]['iconpaddingleft']) ? $attr['btns'][$index]['iconpaddingleft'].$unit: '5'.$unit,
					'padding-right'			=> isset($attr['btns'][$index]['iconpaddingright']) ? $attr['btns'][$index]['iconpaddingright'].$unit : '5'.$unit,
					'color'					=> isset($attr['iconColor']) ? $attr['iconColor'] : '',
					'background-color'		=> isset($attr['iconBGColor']) ? $attr['iconBGColor'] : '',
				),
				' .anchrstyle .ive-button-icon-padding'.$index.':hover' => array(
					'color'				=> isset($attr['iconhoverColor']) ? $attr['iconhoverColor'] : '',
					'background-color'	=> isset($attr['iconhoverBGColor']) ? $attr['iconhoverBGColor'] : '',
				),
				' .anchrstyle:hover .ive-button-icon-padding'.$index => array(
				  'display'	=> $hovericon,
				)
			);

			$t_selectors = array(
				' .anchrstyle' => array(
					'font-size'				=> isset($attr['btns'][$index]['tabsize']) ? $attr['btns'][$index]['tabsize'] . $unit : '16'.$unit,
					'padding'				=> $tabpaddingBT.$unit .' '. $tabpaddingLR . $unit,
				),
				'.btn-inner-wrap' => array(
					'display'				=> $attr['tabvisible'] ? 'block' : 'none',
					'margin-top'			=> isset($attr['btns'][$index]['tabMarginTop']) ? $attr['btns'][$index]['tabMarginTop'] . $unit : '20'.$unit,
					'margin-bottom'			=> isset($attr['btns'][$index]['tabMarginBottom']) ? $attr['btns'][$index]['tabMarginBottom'] . $unit : '20'.$unit,
				),
				' .anchrstyle .ive-button-icon-padding'.$index.' i' => array(
					'font-size'				=> isset($attr['iconsize'][1]) ? $attr['iconsize'][1]. $unit : '12'. $unit
				)
            );

            $m_selectors = array(
				' .anchrstyle' => array(
					'font-size'				=> isset($attr['btns'][$index]['mobsize']) ? $attr['btns'][$index]['mobsize'] . $unit : '14'.$unit,
					'padding'				=> $mobpaddingBT.$unit .' '. $mobpaddingLR . $unit,
				),
				'.btn-inner-wrap' => array(
					'display'				=> $attr['mobvisible'] ? 'block' : 'none',
					'margin-top'			=> isset($attr['btns'][$index]['mobMarginTop']) ? $attr['btns'][$index]['mobMarginTop'] . $unit : '20'.$unit,
					'margin-bottom'			=> isset($attr['btns'][$index]['mobMarginBottom']) ? $attr['btns'][$index]['mobMarginBottom'] . $unit : '20'.$unit,
				),
				' .anchrstyle .ive-button-icon-padding'.$index.' i' => array(
					'font-size'				=> isset($attr['iconsize'][2]) ? $attr['iconsize'][2]. $unit : '12'. $unit
				)
            );

			// animation css
			$animationtype = isset($attr['animationtype']) ? $attr['animationtype'] : '';
			$animationdelay = isset($attr['animationdelay']) ? $attr['animationdelay'] : '';
			$animationspeed = isset($attr['animationspeed']) ? $attr['animationspeed'] : '';
			$animationiteration = isset($attr['animationiteration']) ? $attr['animationiteration'] : '';

			if($animationtype !='none' ){
				$anchrstyle = ' .anchrstyle:hover' ;
				$selectors[$anchrstyle]['animation-name']				= $animationtype;
				$selectors[$anchrstyle]['animation-delay'] = $animationdelay.'s';
				$selectors[$anchrstyle]['animation-duration'] = $animationspeed.'s';
				$selectors[$anchrstyle]['animation-iteration-count'] = $animationiteration ;
			}
			//animation css end

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
            );

			return IVE_Helper::generate_all_css( $combined_selectors, ' .ive-btn-main-parent' . $attr['uniqueID'] );

		}

		public static function get_page_title_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/page-title']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$selectors = array(
				'.vw-page-title' => array(
					'display'				=> isset($attr['page_title']) && $attr['page_title'] ? 'none' : 'block'
				),
				'.vw-page-pagination' => array(
					'display'				=> isset($attr['pagination_title']) && $attr['pagination_title'] ? 'none' : 'block'
				)
			);

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
            );

			return IVE_Helper::generate_all_css( $combined_selectors, '' );
		}

		public static function get_google_map_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/google-map']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$unit = 'px';

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$selectors = array(
				' .ive-google-map__wrap' => array(
					'background-color'		=> isset($attr['bgColor']) ? $attr['bgColor'] : '',
					'margin-top'			=> isset($attr['margin_top']) ? $attr['margin_top'].$unit : '35'.$unit,
					'margin-bottom'			=> isset($attr['margin_bottom']) ? $attr['margin_bottom'].$unit : '35'.$unit,
				),
				' .ive-google-map__iframe' => array(
					'height'				=> isset($attr['height']) ? $attr['height'].$unit : '300'.$unit,
					'opacity'				=> isset($attr['bgOpacity']) ? $attr['bgOpacity'] : 1
				)
			);

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
            );

			return IVE_Helper::generate_all_css( $combined_selectors, '.ive_google_map' . $attr['uniqueID'] );
		}

		public static function get_image_gallery_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/gallery']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();
			$selectors = array(
				' .ibtana-blocks-gallery-item' => array(
					'cursor'				=> 'pointer',
				),
				' .gallery-overlay' => array(
					'background'			=> isset($attr['overlayacolor']) ? $attr['overlayacolor'] : '#F5353561',
					'opacity'				=> isset($attr['imgopacity']) ? $attr['imgopacity'] : 1
				),
				' .ibtana-blocks-gallery-item i' => array(
					'justify-content'				=> isset($attr['iconPosition']) ? $attr['iconPosition'] : '',
					'color'									=> isset($attr['iconColor']) ? $attr['iconColor'] : '',
					'font-size'							=> isset($attr['iconfontSize']) ? $attr['iconfontSize'].'px' : '12px',
					'top' =>  'calc(50% - 10px)',
					'display' =>  'flex',
					'position' =>  'relative',
				),
			);

			$paddingtopdesk = isset($attr['paddingtop'][0]) ? $attr['paddingtop'][0].'px' : '0';
			$paddingleftdesk = isset($attr['paddingleft'][0]) ? $attr['paddingleft'][0].'px' : '0';
			$paddingrightdesk = isset($attr['paddingright'][0]) ? $attr['paddingright'][0].'px' : '0';
			$paddingbottomdesk = isset($attr['paddingbottm'][0]) ? $attr['paddingbottm'][0].'px' : '0';

			$paddingtoptab = isset($attr['paddingtop'][1]) ? $attr['paddingtop'][1].'px' : '0';
			$paddinglefttab = isset($attr['paddingleft'][1]) ? $attr['paddingleft'][1].'px' : '0';
			$paddingrighttab = isset($attr['paddingright'][1]) ? $attr['paddingright'][1].'px' : '0';
			$paddingbottomtab = isset($attr['paddingbottm'][1]) ? $attr['paddingbottm'][1].'px' : '0';

			$paddingtopmob = isset($attr['paddingtop'][2]) ? $attr['paddingtop'][2].'px' : '0';
			$paddingleftmob = isset($attr['paddingleft'][2]) ? $attr['paddingleft'][2].'px' : '0';
			$paddingrightmob = isset($attr['paddingright'][2]) ? $attr['paddingright'][2].'px' : '0';
			$paddingbottommob = isset($attr['paddingbottm'][2]) ? $attr['paddingbottm'][2].'px' : '0';

			$d_selectors = array(
				' .ibtana-blocks-gallery-item' => array(
					'padding'				=> $paddingtopdesk.' '.$paddingrightdesk.' '.$paddingbottomdesk.' '.$paddingleftdesk
				),
			);
			$t_selectors = array(
				' .ibtana-blocks-gallery-item' => array(
					'padding'				=> $paddingtoptab.' '.$paddingrighttab.' '.$paddingbottomtab.' '.$paddinglefttab
				),
			);
			$m_selectors = array(
				' .ibtana-blocks-gallery-item' => array(
					'padding'				=> $paddingtopmob.' '.$paddingrightmob.' '.$paddingbottommob.' '.$paddingleftmob
				),
			);


			$animationtype = isset($attr['animationtype']) ? $attr['animationtype'] : '';
			$animationspeed = isset($attr['animationspeed']) ? $attr['animationspeed'] : '';
			if($animationtype !='none' ){
				$aniclass= ' .ibtana-blocks-gallery-item:hover' ;
				$selectors[$aniclass]['animation-name']				= $animationtype;
				$selectors[$aniclass]['transition'] = 'transform '.$animationspeed.'s';

				if($animationtype =='zoomIn' ){
					$selectors[$aniclass]['transform'] = 'scale(0.8)';
				}else if($animationtype =='zoomOut' ){
					$selectors[$aniclass]['transform'] = 'scale(1.1)';
				}
			}

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);

			return IVE_Helper::generate_all_css( $combined_selectors, '.ive-gallery-wrap-id-' . $attr['uniqueID'] );
		}

		public static function get_icon_css( $attr, $id ) {
			$defaults = IVE_Helper::$block_list['ive/icon']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$unit = 'px';
			$margintbdesk  = isset($attr['margintb'][2]) ? $attr['margintb'][2].$unit : '5'.$unit;
			$marginlrdesk = isset($attr['marginlr'][2]) ? $attr['marginlr'][2].$unit : '5'.$unit;
			$margintbtab  = isset($attr['margintb'][1]) ? $attr['margintb'][1].$unit : '5'.$unit;
			$marginlrtab  = isset($attr['marginlr'][1]) ? $attr['marginlr'][1].$unit : '5'.$unit;
			$margintbmob  = isset($attr['margintb'][0]) ? $attr['margintb'][0].$unit : '5'.$unit;
			$marginlrmob  = isset($attr['marginlr'][0]) ? $attr['marginlr'][0].$unit : '5'.$unit;

			$iconsticky = $attr['iconsticky'];
			$alignType = isset($attr['alignType']) ? $attr['alignType'] : 'horizontal';
			$stickytop = $alignType == 'horizontal' ? 'auto' : '50%' ;
			$stickytransform = $alignType == 'horizontal' ? 'none' : 'translateY(-50%)' ;
			$stickybottom = $alignType == 'horizontal' ? 0 : 'auto' ;
			$stickyposition = isset($attr['stickyposition']) ? $attr['stickyposition'] : 'left';
			$stickyleft = $stickyposition == 'left' ? 0 : 'auto' ;
			$stickyright = $stickyposition == 'right' ? 0 : 'auto' ;

			$align = 'grid';
			if($alignType == 'horizontal'){
				$align = 'flex';
			}

			$iconCount = $attr['iconCount'];

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			for ($i=0; $i < $iconCount; $i++) {
				$icon = $attr['icons'][$i];
				//classes
				$paddingClass = ' .ive_icon_parent_icon_padding'.$i;
				$sizeClass = ' .ive_icon_parent_icon_size'.$i;
				$hoverClass = ' .ive-svg-item-'.$i.':hover .ive_icon_parent_icon_padding'.$i;
				$iconGradClass = ' .ive-svg-item-'.$i.' .ive_icon_parent_icon_padding'.$i;
				$iconGradHoverClass = ' .ive-svg-item-'.$i.':hover .ive_icon_parent_icon_padding'.$i;
				$defaultval = '0'.$unit;

				if( isset( $icon['iconGrad'] ) ){
					$gradRadPos = isset($icon['gradRadPos']) ? $icon['gradRadPos'] : '';
					$gradFirstColor = isset($icon['gradFirstColor']) ? $icon['gradFirstColor'] : '';
					$gradFirstLoc = isset($icon['gradFirstLoc']) ? $icon['gradFirstLoc']. '%' : '';
					$gradSecondColor = isset($icon['gradSecondColor']) ? $icon['gradSecondColor'] : '';
					$gradSecondLoc = isset($icon['gradSecondLoc']) ? $icon['gradSecondLoc'] .'%' : '';
					$gradAngle = isset($icon['gradAngle']) ? $icon['gradAngle'] .'deg' : '';

					$hovGradFirstColor = isset($icon['hovGradFirstColor']) ? $icon['hovGradFirstColor'] : '';
					$hovGradSecondColor = isset($icon['hovGradSecondColor']) ? $icon['hovGradSecondColor'] : '';

					if($icon['gradType'] === 'radial'){
						$gradient = ' radial-gradient(at '.$gradRadPos.', '. $gradFirstColor.' '. $gradFirstLoc .' '. $gradSecondColor .' '. $gradSecondLoc .' ) !important' ;
						$gradientHover = ' radial-gradient(at '.$gradRadPos.', '. $hovGradFirstColor.' '. $gradFirstLoc .' '. $gradSecondColor .' '. $gradSecondLoc .' ) !important' ;
					}else{
						$gradient = ' linear-gradient('.$gradAngle.', '. $gradFirstColor.' '. $gradFirstLoc .', '. $gradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
						$gradientHover = ' linear-gradient('.$gradAngle.', '. $hovGradFirstColor.' '. $gradFirstLoc .', '. $hovGradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
					}

				}else{
					$gradient = ' unset !important';
					$gradientHover = ' unset !important';
				}


				$selectors[$iconGradClass]['background-image'] = $gradient;
				$selectors[$iconGradHoverClass]['background-image'] = $gradientHover;

				//desktop icon css
				$stylecon = ($icon['style'] == 'default');
				$background = isset($icon['background']) ? $icon['background'] : '#ffffff';
				$backgroundColor = $stylecon ? 'unset' : $background;
				$border = isset($icon['border']) ? $icon['border'] : '#444444';
				$borderColor = $stylecon ? 'unset' : $border;
				$bordWidth = isset($icon['borderWidth']) ? $icon['borderWidth'] : 2;
				$borderWidth = $stylecon ? $defaultval : $bordWidth.$unit;
				$bordRadius = isset($icon['borderRadius']) ? $icon['borderRadius'] : 0;
				$borderRadius = $stylecon ? $defaultval : $bordRadius.$unit;

				$deskpadding = isset($icon['deskpadding']) ? $icon['deskpadding'].$unit : '20'.$unit;
				$deskpadding2 = isset($icon['deskpadding2']) ? $icon['deskpadding2'].$unit : '20'.$unit;
				$paddingdesk = !$stylecon ? $deskpadding.' '.$deskpadding2 : 'unset' ;

				$tabpadding = isset($icon['tabpadding']) ? $icon['tabpadding'].$unit : '16'.$unit;
				$tabpadding2 = isset($icon['tabpadding2']) ? $icon['tabpadding2'].$unit : '16'.$unit;
				$paddingtab = !$stylecon ? $tabpadding.' '.$tabpadding2 : 'unset' ;

				$mobpadding = isset($icon['mobpadding']) ? $icon['mobpadding'].$unit : '12'.$unit;
				$mobpadding2 = isset($icon['mobpadding2']) ? $icon['mobpadding2'].$unit : '12'.$unit;
				$paddingmob = !$stylecon ? $mobpadding.' '.$mobpadding2 : 'unset' ;

				$selectors[$paddingClass]['border-style'] = isset($icon['borderStyle']) ? $icon['borderStyle'] : 'none';
				$selectors[$paddingClass]['color'] = isset($icon['color']) ? $icon['color'] : '#444444';
				$selectors[$paddingClass]['background-color'] = $backgroundColor;
				$selectors[$paddingClass]['border-color'] = $borderColor;
				$selectors[$paddingClass]['border-width'] = $borderWidth;
				$selectors[$paddingClass]['border-radius'] = $borderRadius;
				$selectors[$paddingClass]['line-height'] = 0;

				//hover css
				$selectors[$hoverClass]['background'] = ( !$stylecon && isset($icon['hoverBackground'])) ? $icon['hoverBackground'] : 'undefined';
				$selectors[$hoverClass]['border-color'] = ( !$stylecon && isset($icon['hoverBorder'])) ? $icon['hoverBorder'] : 'undefined';
				$selectors[$hoverClass]['color'] = isset($icon['hoverColor']) ? $icon['hoverColor'] : '#eeeeee';

				$selectors[$sizeClass]['font-size'] = isset($icon['desksize']) ? $icon['desksize'].$unit : '50'.$unit;
				$selectors[$paddingClass]['padding'] = $paddingdesk;
				$selectors[$paddingClass]['width'] = (isset($icon['deskwidth']) && $icon['deskwidth'] != 0 ) ? $icon['deskwidth'].$unit : 'auto';
				$selectors[$paddingClass]['height'] = (isset($icon['deskheight']) && $icon['deskheight'] != 0 ) ? $icon['deskheight'].$unit : 'auto';

				//tablet icon css
				$t_selectors[$sizeClass]['font-size'] = isset($icon['tabsize']) ? $icon['tabsize'].$unit : '35'.$unit;
				$t_selectors[$paddingClass]['padding'] = $paddingtab;
				$t_selectors[$paddingClass]['width'] = (isset($icon['tabwidth']) && $icon['tabwidth'] != 0 ) ? $icon['tabwidth'].$unit : 'auto';
				$t_selectors[$paddingClass]['height'] = (isset($icon['tabheight']) && $icon['tabheight'] != 0 ) ? $icon['tabheight'].$unit : 'auto';

				//mobile icon css
				$m_selectors[$sizeClass]['font-size'] = isset($icon['mobsize']) ? $icon['mobsize'].$unit : '20'.$unit;
				$m_selectors[$paddingClass]['padding'] = $paddingmob;
				$m_selectors[$paddingClass]['width'] = (isset($icon['mobwidth']) && $icon['mobwidth'] != 0 ) ? $icon['mobwidth'].$unit : 'auto';
				$m_selectors[$paddingClass]['height'] = (isset($icon['mobheight']) && $icon['mobheight'] != 0 ) ? $icon['mobheight'].$unit : 'auto';
			}

			$selectors['.ive-svg-icons-block']['display'] = $align;
			$selectors[' .ive-svg-icon-margin']['margin'] = $margintbdesk.' '.$marginlrdesk;
			$t_selectors[' .ive-svg-icon-margin']['margin']  = $margintbtab.' '.$marginlrtab;
			$m_selectors[' .ive-svg-icon-margin']['margin']  = $margintbmob.' '.$marginlrmob;

			if ($iconsticky) {
				$selectors['.ive-svg-icons-block']['position'] = 'fixed';
				$selectors['.ive-svg-icons-block']['z-index'] = 99;
				$selectors['.ive-svg-icons-block']['top'] = $stickytop;
				$selectors['.ive-svg-icons-block']['transform'] = $stickytransform;
				$selectors['.ive-svg-icons-block']['left'] = $stickyleft;
				$selectors['.ive-svg-icons-block']['right'] = $stickyright;
				$selectors['.ive-svg-icons-block']['bottom'] = $stickybottom;
			}

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);

			return IVE_Helper::generate_all_css( $combined_selectors, '.ive-svg-icons' . $attr['uniqueID'] );
		}

		public static function get_separator_css( $attr, $id ) {
			$defaults = IVE_Helper::$block_list['ive/separator']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$unit = 'px';

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$selectors = array(
				' .ive-separator' => array(
					'height'				=> isset($attr['spacerHeight']) ? $attr['spacerHeight'].$unit : '6'.$unit
				),
				' .ive-separator-hr' => array(
					'border-color'			=> isset($attr['dividerColor']) ? $attr['dividerColor'] : '#eeeeee',
					'width'					=> isset($attr['dividerWidth']) ? $attr['dividerWidth'].'%' : '80%',
					'border-top-width'		=> isset($attr['dividerHeight']) ? $attr['dividerHeight'].$unit : '1'.$unit,
					'border-style'			=> isset($attr['dividerStyle']) ? $attr['dividerStyle'] : 'solid',
					'margin'				=> '0 auto',
					'opacity'				=> isset($attr['dividerOpacity']) ? $attr['dividerOpacity']/100 : 1
				)
			);

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);

			return IVE_Helper::generate_all_css( $combined_selectors, '.ive-separator-' . $attr['uniqueID'] );
		}

		public static function get_progress_bar_css( $attr, $id ) {
			$defaults = IVE_Helper::$block_list['ive/progress-bar']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$unit 							= 'px';
			$size 							= isset($attr['circularSize']) ? $attr['circularSize'] : '150';
			$barType 						= isset($attr['barType']) ? $attr['barType'] : 'linear';
			$percentage 				= isset($attr['percentage']) ? $attr['percentage'] : 25;
			$counter 						= isset($attr['counter']) ? $attr['counter'] : false;
			$barThickness 			= isset($attr['barThickness']) ? $attr['barThickness'] : 1;
			$circleRadius 			= 50 - ($barThickness + 3) / 2;
			$circlePathLength 	= $circleRadius * pi() * 2;
			$strokeArcLength 		= ($circlePathLength * $percentage) / 100;
			$strokeArcLengthVal	= number_format((float)$strokeArcLength, 3, '.', '');
			$strokeDasharray 		= number_format((float)$circlePathLength, 3, '.', '');
			if( $counter ) {
				$circular_pg = '301.430px, 301.593px';
			}else{
				$circular_pg = $strokeArcLengthVal.$unit.', '.$strokeDasharray.$unit;
			}

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$selectors = array(
				'.ibtana_progress-bar' => array(
					'margin-top'			=> isset($attr['margin_top']) ? $attr['margin_top'].$unit : '10'.$unit,
					'margin-bottom'			=> isset($attr['margin_bottom']) ? $attr['margin_bottom'].$unit : '10'.$unit
				),
				' .circular-progressbar-right' => array(
					'margin-left'			=> 'auto'
				),
				' .circular-progressbar-center' => array(
					'margin'				=> 'auto'
				),
				' .ibtana_progress_title' => array(
					'font-size'				=> isset($attr['deskfontSize']) ? $attr['deskfontSize'].$unit : '24'.$unit,
					'color'					=> isset($attr['titleColor']) ? $attr['titleColor'].' !important' : '#111111',
					'background'			=> isset($attr['titlebgColor']) ? $attr['titlebgColor'] : '',
					'font-family'			=> isset($attr['typography']) ? $attr['typography'] : '',
					'letter-spacing'		=> isset($attr['letterSpacing']) ? $attr['letterSpacing'].$unit : 0,
					'font-weight'			=> isset($attr['fontWeight']) ? $attr['fontWeight'] : 'normal',
					'font-style'			=> isset($attr['fontStyle']) ? $attr['fontStyle'] : 'normal',
					'white-space'			=> 'pre-wrap'
				),
				' .ibtana_progress-bar-container.row' => array(
					'border-color'			=> isset($attr['progress_border']) ? $attr['progress_border'] : '#fff',
					'border-style'			=> 'solid',
					'border-width'			=> isset($attr['progress_borderWidth']) ? $attr['progress_borderWidth'].$unit : '2'.$unit,
					'border-radius'			=> isset($attr['progress_borderRadius']) ? $attr['progress_borderRadius'].$unit : 0,
					'padding'				=> isset($attr['progress_padding']) ? $attr['progress_padding'].$unit : '20'.$unit
				),
				' .ibtana_progress-bar-container.row .ibtana_progress-bar-line-path' => array(
					'stroke-dashoffset'		=> $counter ? '100'.$unit : (100 - $percentage).$unit
				),
				' .ibtana_progress-bar-container.circular' => array(
					'height'				=> isset($size) ? $size.$unit : '150'.$unit,
					'width'					=> isset($size) ? $size.$unit : '150'.$unit,
					'position'				=> 'relative'
				),
				' .ibtana_progress-bar-label' => array(
					'font-size'				=> isset($attr['deskfontSize_cont']) ? $attr['deskfontSize_cont'].$unit : '24'.$unit,
					'visibility'			=> 'visible',
					'text-align'			=> 'right',
					'min-width'				=> '24px',
					'color'					=> isset($attr['contentColor']) ? $attr['contentColor'] : '#111111',
					'font-family'			=> isset($attr['typography_cont']) ? $attr['typography_cont'] : '',
					'letter-spacing'		=> isset($attr['letterSpacing_cont']) ? $attr['letterSpacing_cont'].$unit : 0,
					'font-weight'			=> isset($attr['fontWeight_cont']) ? $attr['fontWeight_cont'] : 'normal',
					'font-style'			=> isset($attr['fontStyle_cont']) ? $attr['fontStyle_cont'] : 'normal',
				),
				' .ibtana_progress-bar-label:hover' => array(
					'color'					=> isset($attr['contentHoverColor']) ? $attr['contentHoverColor'] : '#111111',
				),
				' .ibtana_progress-bar-container.circular .ibtana_progress-bar-circle' => array(
					'position' 				=> 'absolute'
				),
				' .ibtana_progress-bar-container.circular .ibtana_progress-bar-circle-trail' => array(
					'stroke-dasharray'		=> $strokeDasharray.$unit.', '.$strokeDasharray.$unit
				),
				' .ibtana_progress-bar-container.circular .ibtana_progress-bar-circle-path' => array(
					'stroke-dasharray'		=> $circular_pg,
					'stroke-dashoffset'		=> ($counter ? '310' : '0').$unit
				)
			);

			if( $barType === 'circular' ) {
				$selectors[' .ibtana_progress-bar-label']['font-size']			=	isset($attr['deskfontSize_cont']) ? $attr['deskfontSize_cont'].$unit : '24'.$unit;
				$selectors[' .ibtana_progress-bar-label']['position']				=	'absolute';
				$selectors[' .ibtana_progress-bar-label']['visibility']			=	 'visible';
				$selectors[' .ibtana_progress-bar-label']['top']						=	'50%';
				$selectors[' .ibtana_progress-bar-label']['transform']			=	'translateY(-50%)';
				$selectors[' .ibtana_progress-bar-label']['margin']					= 'auto';
				$selectors[' .ibtana_progress-bar-label']['text-align']			= 'center';
				$selectors[' .ibtana_progress-bar-label']['left']						= 0;
				$selectors[' .ibtana_progress-bar-label']['right']					= 0;
				$selectors[' .ibtana_progress-bar-label']['color']					= isset($attr['contentColor']) ? $attr['contentColor'] : '#111111';
				$selectors[' .ibtana_progress-bar-label']['font-family']		=	isset($attr['typography_cont']) ? $attr['typography_cont'] : '';
				$selectors[' .ibtana_progress-bar-label']['letter-spacing']	= isset($attr['letterSpacing_cont']) ? $attr['letterSpacing_cont'].$unit : 0;
				$selectors[' .ibtana_progress-bar-label']['font-weight']		= isset($attr['fontWeight_cont']) ? $attr['fontWeight_cont'] : 'normal';
				$selectors[' .ibtana_progress-bar-label']['font-style']			= isset($attr['fontStyle_cont']) ? $attr['fontStyle_cont'] : 'normal';
			}

			if ( isset( $attr['percentBgGradient'] ) && $attr['percentBgGradient'] === true ) {

				$percentBgGradLocOne		=	isset( $attr['percentBgGradLocOne'] )	?	$attr['percentBgGradLocOne'] : 0;
				$percentBgGradLocSecond	=	isset( $attr['percentBgGradLocSecond'] ) ? $attr['percentBgGradLocSecond'] : 100;

					if ( isset( $attr['percentBgGradType'] ) && ( $attr['percentBgGradType'] === 'radial' ) ) {
						$percentVbgImgPosition	=	isset( $attr['percentVbgImgPosition'] ) ? $attr['percentVbgImgPosition'] : 'center center';
						if ( $attr['percentBgFirstColor'] && $attr['percentBgSecondColor'] ) {
							$selectors[' .ibtana_progress-bar-label']['background-image']				=	'radial-gradient(at ' . $percentVbgImgPosition . ', ' . $attr['percentBgFirstColor'] . ' ' . $percentBgGradLocOne . '%, ' . $attr['percentBgSecondColor'] . ' ' . $percentBgGradLocSecond . '%)';
						}
						if ( $attr['percentBgHovGradFirstColor'] && $attr['percentBgHovGradSecondColor'] ) {
							$selectors[' .ibtana_progress-bar-label:hover']['background-image']	=	'radial-gradient(at ' . $percentVbgImgPosition . ', ' . $attr['percentBgHovGradFirstColor'] . ' ' . $percentBgGradLocOne . '%, ' . $attr['percentBgHovGradSecondColor'] . ' ' . $percentBgGradLocSecond . '%)';
						}
					} else {
						$percentBgGradAngle	=	isset( $attr['percentBgGradAngle'] ) ? $attr['percentBgGradAngle'] : 180;
						if ( $attr['percentBgFirstColor'] && $attr['percentBgSecondColor'] ) {
							$selectors[' .ibtana_progress-bar-label']['background-image']				=	'linear-gradient(' . $percentBgGradAngle . 'deg, ' . $attr['percentBgFirstColor'] . ' ' . $percentBgGradLocOne . '%, ' . $attr['percentBgSecondColor'] . ' ' . $percentBgGradLocSecond . '%)';
						}
						if ( $attr['percentBgHovGradFirstColor'] && $attr['percentBgHovGradSecondColor'] ) {
							$selectors[' .ibtana_progress-bar-label:hover']['background-image']	=	'linear-gradient(' . $percentBgGradAngle . 'deg, ' . $attr['percentBgHovGradFirstColor'] . ' ' . $percentBgGradLocOne . '%, ' . $attr['percentBgHovGradSecondColor'] . ' ' . $percentBgGradLocSecond . '%)';
						}
					}

			} else {
				if ( isset( $attr['percentBgColor'] ) ) {
					$selectors[' .ibtana_progress-bar-label']['background-color']	=	$attr['percentBgColor'];
				}
				if ( isset( $attr['percentBgHoverColor'] ) ) {
					$selectors[' .ibtana_progress-bar-label:hover']['background-color']	=	$attr['percentBgHoverColor'];
				}
			}

			$t_selectors = array(
				' .ibtana_progress_title' => array(
					'font-size'				=> isset($attr['tabfontSize']) ? $attr['tabfontSize'].$unit : '20'.$unit
				),
				' .ibtana_progress-bar-label' => array(
					'font-size'				=> isset($attr['tabfontSize_cont']) ? $attr['tabfontSize_cont'].$unit : '20'.$unit,
				)
			);

			$m_selectors = array(
				' .ibtana_progress_title' => array(
					'font-size'				=> isset($attr['mobfontSize']) ? $attr['mobfontSize'].$unit : '16'.$unit
				),
				' .ibtana_progress-bar-label' => array(
					'font-size'				=> isset($attr['mobfontSize_cont']) ? $attr['mobfontSize_cont'].$unit : '16'.$unit,
				)
			);

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);

			return IVE_Helper::generate_all_css( $combined_selectors, '.ibtana_progress_bar' . $attr['uniqueID'] );
		}

		public static function get_advanced_text_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/ibtana-visual-editorheading']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$dropCap	=	isset( $attr['dropCap'] ) ? $attr['dropCap'] : false;

			$unit = 'px';
			//attributes
			$gradientDisable		= isset($attr['gradientDisable']) ? $attr['gradientDisable'] : false;
			$textOptions			= isset($attr['textOptions']) ? $attr['textOptions'] : 'text';
			$bgGradType				= isset($attr['bgGradType']) ? $attr['bgGradType'] : 'linear';
			$vBgImgPosition			= isset($attr['vBgImgPosition']) ? $attr['vBgImgPosition'] : 'center center';
			$bgfirstcolorr			= isset($attr['bgfirstcolorr']) ? $attr['bgfirstcolorr'] : '';
			$bgGradAngle			= isset($attr['bgGradAngle']) ? $attr['bgGradAngle'] : 180;
			$bgGradLoc				= isset($attr['bgGradLoc']) ? $attr['bgGradLoc'] : 0;
			$bgSecondColr			= isset($attr['bgSecondColr']) ? $attr['bgSecondColr'] : '#00B5E2';
			$bgGradLocSecond		= isset($attr['bgGradLocSecond']) ? $attr['bgGradLocSecond'] : 100;
			$headhoverbgfirstcolor 	= isset($attr['headhoverbgfirstcolor']) ? $attr['headhoverbgfirstcolor'] : '';
			$headhoverbgSecondColr 	= isset($attr['headhoverbgSecondColr']) ? $attr['headhoverbgSecondColr'] : '';
			$animationtype			= isset($attr['animationtype']) ? $attr['animationtype'] : 'none';
			$paddingtype			= isset($attr['paddingtype']) ? $attr['paddingtype'] : 'px';
			$marginType				= isset($attr['marginType']) ? $attr['marginType'] : 'px';
			$optionSide				= isset($attr['optionSide']) ? $attr['optionSide'] : 'row';
			$deskalign				= isset($attr['deskalign']) ? $attr['deskalign'] : 'center';
			$tabalign				= isset($attr['tabalign']) ? $attr['tabalign'] : 'center';
			$mobalign				= isset($attr['mobalign']) ? $attr['mobalign'] : 'center';

			$backgdfirstcolor				= isset($attr['backgdfirstcolor']) ? $attr['backgdfirstcolor'] : '';
			$backgdGradLoc				= isset($attr['backgdGradLoc']) ? $attr['backgdGradLoc'] : '';
			$backgdSecondColr				= isset($attr['backgdSecondColr']) ? $attr['backgdSecondColr'] : '';
			$backgdGradLocSecond				= isset($attr['backgdGradLocSecond']) ? $attr['backgdGradLocSecond'] : '';
			$backgdGradType				= isset($attr['backgdGradType']) ? $attr['backgdGradType'] : '';
			$backgdGradAngle				= isset($attr['backgdGradAngle']) ? $attr['backgdGradAngle'] : '';
			$backgdImgPosition				= isset($attr['backgdImgPosition']) ? $attr['backgdImgPosition'] : '';
			$backgdheadhoverfirstcolor				= isset($attr['backgdheadhoverfirstcolor']) ? $attr['backgdheadhoverfirstcolor'] : '';
			$backgdheadhoverSecondColr				= isset($attr['backgdheadhoverSecondColr']) ? $attr['backgdheadhoverSecondColr'] : '';
			$backgdOpacity				= isset($attr['backgdOpacity']) ? $attr['backgdOpacity'] : '';
			$headhoverbackgdOpacity				= isset($attr['headhoverbackgdOpacity']) ? $attr['headhoverbackgdOpacity'] : '';
			$bggradientDisable				= isset($attr['bggradientDisable']) ? $attr['bggradientDisable'] : '';

			$backgroundImage; $bgcolorgrad;
			if($gradientDisable){
				if ($bgGradType === 'radial') {
					$backgroundImage = 'radial-gradient(at ' .$vBgImgPosition. ',' .$bgfirstcolorr. ' ' .$bgGradLoc. '%,' .$bgSecondColr. ' ' .$bgGradLocSecond. '%);';
				}else{
					$backgroundImage = 'linear-gradient(' .$bgGradAngle. 'deg,' .$bgfirstcolorr. ' ' .$bgGradLoc. '%,' .$bgSecondColr. ' ' .$bgGradLocSecond. '%);';
				}
				$bgcolorgrad = isset($attr['headbggradColor']) ? $attr['headbggradColor'] : '';
			}else{
				$backgroundImage = 'unset';
      			$bgcolorgrad = isset($attr['backgroundcolor']) ? $attr['backgroundcolor'] : '';
			}

			$backgroundImageHov; $bgcolorgradHov;
			if($gradientDisable){
				if ($bgGradType === 'radial') {
					$backgroundImageHov = 'radial-gradient(at ' .$vBgImgPosition. ',' .$headhoverbgfirstcolor. ' ' .$bgGradLoc. '%,' .$headhoverbgSecondColr. ' ' .$bgGradLocSecond. '%);';
				}else{
					$backgroundImageHov = 'linear-gradient(' .$bgGradAngle. 'deg,' .$headhoverbgfirstcolor. ' ' .$bgGradLoc. '%,' .$headhoverbgSecondColr. ' ' .$bgGradLocSecond. '%);';
				}
				$bgcolorgradHov = isset($attr['headhoverbggradcolor']) ? $attr['headhoverbggradcolor'] : '';
			}else{
				$backgroundImageHov = 'unset';
      			$bgcolorgradHov = isset($attr['hoverbackgroundcolor']) ? $attr['hoverbackgroundcolor'] : '';
			}
			$backgroundImage_div = '';
			$backgroundImage_div_hover = '';

			if($bggradientDisable){
				if ($backgdGradType === 'radial') {
					$backgroundImage_div = 'radial-gradient(at ' .$backgdImgPosition. ',' .$backgdfirstcolor. ' ' .$backgdGradLoc. '%,' .$backgdSecondColr. ' ' .$backgdGradLocSecond. '%);';
					$backgroundImage_div_hover = 'radial-gradient(at ' .$backgdImgPosition. ',' .$backgdheadhoverfirstcolor. ' ' .$backgdGradLoc. '%,' .$backgdheadhoverSecondColr. ' ' .$backgdGradLocSecond. '%);';
				}else{
					$backgroundImage_div = 'linear-gradient(' .$backgdGradAngle. 'deg,' .$backgdfirstcolor. ' ' .$backgdGradLoc. '%,' .$backgdSecondColr. ' ' .$backgdGradLocSecond. '%);';
					$backgroundImage_div_hover = 'linear-gradient(' .$backgdGradAngle. 'deg,' .$backgdheadhoverfirstcolor. ' ' .$backgdGradLoc. '%,' .$backgdheadhoverSecondColr. ' ' .$backgdGradLocSecond. '%);';
				}
			}

			//box shadow
			$boxshadowpos    = isset($attr['boxshadowpos']) ? $attr['boxshadowpos'] : '';
			$boxshadowx      = isset($attr['boxshadowx']) ? $attr['boxshadowx'] : 0;
			$boxshadowy      = isset($attr['boxshadowy']) ? $attr['boxshadowy'] : 0;
			$boxshadowblur   = isset($attr['boxshadowblur']) ? $attr['boxshadowblur'] : 5;
			$boxshadowspread = isset($attr['boxshadowspread']) ? $attr['boxshadowspread'] : 1;
			$boxshadowcolor  = isset($attr['boxshadowcolor']) ? $attr['boxshadowcolor'] : 'transparent';

			$boxshadowposhover    = isset($attr['boxshadowposhover']) ? $attr['boxshadowposhover'] : '';
			$boxshadowxhover      = isset($attr['boxshadowxhover']) ? $attr['boxshadowxhover'] : 0;
			$boxshadowyhover      = isset($attr['boxshadowyhover']) ? $attr['boxshadowyhover'] : 0;
			$boxshadowblurhover   = isset($attr['boxshadowblurhover']) ? $attr['boxshadowblurhover'] : 5;
			$boxshadowspreadhover = isset($attr['boxshadowspreadhover']) ? $attr['boxshadowspreadhover'] : 1;
			$boxshadowcolorhover  = isset($attr['boxshadowcolorhover']) ? $attr['boxshadowcolorhover'] : 'transparent';


			//border
			$bordertype   = isset($attr['bordertype']) ? $attr['bordertype'] : 'none';
			$bordertop    = isset($attr['bordertop']) ? $attr['bordertop'].$unit : '0'.$unit;
			$borderright  = isset($attr['borderright']) ? $attr['borderright'].$unit : '0'.$unit;
			$borderbottom = isset($attr['borderbottom']) ? $attr['borderbottom'].$unit : '0'.$unit;
			$borderleft   = isset($attr['borderleft']) ? $attr['borderleft'].$unit : '0'.$unit;

			$borderadiustype   = isset($attr['borderadiustype']) ? $attr['borderadiustype'] : 'px';
			$borderadiustop    = isset($attr['borderadiustop']) ? $attr['borderadiustop'].$borderadiustype : '0'.$borderadiustype;
			$borderadiusright  = isset($attr['borderadiusright']) ? $attr['borderadiusright'].$borderadiustype : '0'.$borderadiustype;
			$borderadiusbottom = isset($attr['borderadiusbottom']) ? $attr['borderadiusbottom'].$borderadiustype : '0'.$borderadiustype;
			$borderadiusleft   = isset($attr['borderadiusleft']) ? $attr['borderadiusleft'].$borderadiustype : '0'.$borderadiustype;

			$bordertypehover   = isset($attr['bordertypehover']) ? $attr['bordertypehover'] : 'none';
			$bordertophover    = isset($attr['bordertophover']) ? $attr['bordertophover'].$unit : '0'.$unit;
			$borderrighthover  = isset($attr['borderrighthover']) ? $attr['borderrighthover'].$unit : '0'.$unit;
			$borderbottomhover = isset($attr['borderbottomhover']) ? $attr['borderbottomhover'].$unit : '0'.$unit;
			$borderlefthover   = isset($attr['borderlefthover']) ? $attr['borderlefthover'].$unit : '0'.$unit;

			$borderadiustypehover   = isset($attr['borderadiustypehover']) ? $attr['borderadiustypehover'] : 'px';
			$borderadiustophover    = isset($attr['borderadiustophover']) ? $attr['borderadiustophover'].$borderadiustypehover : '0'.$borderadiustypehover;
			$borderadiusrighthover  = isset($attr['borderadiusrighthover']) ? $attr['borderadiusrighthover'].$borderadiustypehover : '0'.$borderadiustypehover;
			$borderadiusbottomhover = isset($attr['borderadiusbottomhover']) ? $attr['borderadiusbottomhover'].$borderadiustypehover : '0'.$borderadiustypehover;
			$borderadiuslefthover   = isset($attr['borderadiuslefthover']) ? $attr['borderadiuslefthover'].$borderadiustypehover : '0'.$borderadiustypehover;

			//visibility
			$deskvisible = isset($attr['deskvisible']) ? $attr['deskvisible'] : true;
			$tabvisible  = isset($attr['tabvisible']) ? $attr['tabvisible'] : true;
			$mobvisible  = isset($attr['mobvisible']) ? $attr['mobvisible'] : true;

			//padding
			$paddingtopdesk 	= isset($attr['paddingtopdesk']) ? $attr['paddingtopdesk'].$paddingtype : 0;
			$paddingrightdesk 	= isset($attr['paddingrightdesk']) ? $attr['paddingrightdesk'].$paddingtype : 0;
			$paddingbottomdesk 	= isset($attr['paddingbottomdesk']) ? $attr['paddingbottomdesk'].$paddingtype : 0;
			$paddingleftdesk 	= isset($attr['paddingleftdesk']) ? $attr['paddingleftdesk'].$paddingtype : 0;

			$paddingtoptablet	  = isset($attr['paddingtoptablet']) ? $attr['paddingtoptablet'].$paddingtype : 0;
			$paddingrighttablet   = isset($attr['paddingrighttablet']) ? $attr['paddingrighttablet'].$paddingtype : 0;
			$paddingbottomtablet  = isset($attr['paddingbottomtablet']) ? $attr['paddingbottomtablet'].$paddingtype : 0;
			$paddinglefttablet 	  = isset($attr['paddinglefttablet']) ? $attr['paddinglefttablet'].$paddingtype : 0;

			$paddingtopmob 	   = isset($attr['paddingtopmob']) ? $attr['paddingtopmob'].$paddingtype : 0;
			$paddingrightmob   = isset($attr['paddingrightmob']) ? $attr['paddingrightmob'].$paddingtype : 0;
			$paddingbottommob  = isset($attr['paddingbottommob']) ? $attr['paddingbottommob'].$paddingtype : 0;
			$paddingleftmob    = isset($attr['paddingleftmob']) ? $attr['paddingleftmob'].$paddingtype : 0;

			$iconfontSizedesk = isset($attr['iconfontSize'][0]) ? $attr['iconfontSize'][0].$unit : '12'.$unit;
			$iconfontSizetab = isset($attr['iconfontSize'][1]) ? $attr['iconfontSize'][1].$unit : '12'.$unit;
			$iconfontSizemob = isset($attr['iconfontSize'][2]) ? $attr['iconfontSize'][2].$unit : '12'.$unit;

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$selectors = array(
				' .ive-advanced-text-inner-wrap' => array(
					'background'			=> $backgroundImage,
					'opacity'				=> isset($attr['bgOpacity']) ? $attr['bgOpacity']/100 : 1,
					'font-weight'			=> isset($attr['fontWeight']) ? $attr['fontWeight'] : 400,
					'font-style'			=> isset($attr['fontStyle']) ? $attr['fontStyle'] : 'normal',
					'font-family'			=> isset($attr['typography']) ? $attr['typography'] : '',
					'color'					=> isset($attr['color']) ? $attr['color'] . ' !important' : '',
					'letter-spacing'		=> isset($attr['letterSpacing']) ? $attr['letterSpacing'].$unit : '1'.$unit,
					'text-transform'		=> isset($attr['textTransform']) ? $attr['textTransform'] : '',
				),
				' .ive-advanced-text-inner-wrap:hover' => array(
					'color'					=> isset($attr['headinghovercolor']) ? $attr['headinghovercolor'].' !important' : '',
					'opacity'				=> isset($attr['headhoverbgOpacity']) ? $attr['headhoverbgOpacity']/100 : 1,
					'background'			=> $backgroundImageHov
				),
				'.ive-advanced-text-wrap' => array(
					'background-color'		=> $bgcolorgrad,
					'box-shadow'			=> $boxshadowpos.' '.$boxshadowx.$unit.' '.$boxshadowy.$unit.' '.$boxshadowblur.$unit.' '.$boxshadowspread.$unit.' '.$boxshadowcolor,
					'margin-top'			=> isset($attr['topMargin']) ? $attr['topMargin'].$marginType : '1'.$marginType,
					'margin-bottom'			=> isset($attr['bottomMargin']) ? $attr['bottomMargin'].$marginType : '1'.$marginType,
					'background'			=> $backgroundImage_div,
				),
				'.ive-advanced-text-wrap:hover' => array(
					'background-color'		=> $bgcolorgradHov,
					'box-shadow'			=> $boxshadowposhover.' '.$boxshadowxhover.$unit.' '.$boxshadowyhover.$unit.' '.$boxshadowblurhover.$unit.' '.$boxshadowspreadhover.$unit.' '.$boxshadowcolorhover,
					'background'			=> $backgroundImage_div_hover,
				)
			);

			$d_selectors = array(
				'.ive-advanced-text-wrap' => array(
					'display'				=> $deskvisible ? (($textOptions !== 'text') ? 'flex' : 'block' ) : 'none',
				),
				' .ive-advanced-text-inner-wrap' => array(
					'font-size'				=> isset($attr['deskfontSize']) ? $attr['deskfontSize'].$unit : '24'.$unit,
					'padding'				=> $paddingtopdesk.' '.$paddingrightdesk.' '.$paddingbottomdesk.' '.$paddingleftdesk
				),
			);

			$t_selectors = array(
				'.ive-advanced-text-wrap' => array(
					'display'				=> $tabvisible ? (($textOptions !== 'text') ? 'flex' : 'block' ) : 'none',
				),
				' .ive-advanced-text-inner-wrap' => array(
					'font-size'				=> isset($attr['tabfontSize']) ? $attr['tabfontSize'].$unit : '20'.$unit,
					'padding'				=> $paddingtoptablet.' '.$paddingrighttablet.' '.$paddingbottomtablet.' '.$paddinglefttablet
				)
			);

			$m_selectors = array(
				'.ive-advanced-text-wrap' => array(
					'display'				=> $mobvisible ? (($textOptions !== 'text') ? 'flex' : 'block') : 'none',
				),
				' .ive-advanced-text-inner-wrap' => array(
					'font-size'				=> isset($attr['mobfontSize']) ? $attr['mobfontSize'].$unit : '16'.$unit,
					'padding'				=> $paddingtopmob.' '.$paddingrightmob.' '.$paddingbottommob.' '.$paddingleftmob
				)
			);

			if ($textOptions == 'icon') {
				if ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $deskalign == 'right' ) {
					$selectors[' .ive-text-option-parent']['margin-left'] = 'auto';
				}elseif ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $deskalign == 'left' ) {
					$selectors[' .ive-text-option-parent']['margin-left'] = 0;
				}elseif ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $deskalign == 'center' ) {
					$selectors[' .ive-text-option-parent']['margin'] = 'auto';
				}

				if ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $tabalign == 'right' ) {
					$t_selectors[' .ive-text-option-parent']['margin-left'] = 'auto';
				}elseif ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $tabalign == 'left' ) {
					$t_selectors[' .ive-text-option-parent']['margin-left'] = 0;
				}elseif ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $tabalign == 'center' ) {
					$t_selectors[' .ive-text-option-parent']['margin'] = 'auto';
				}

				if ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $mobalign == 'right' ) {
					$m_selectors[' .ive-text-option-parent']['margin-left'] = 'auto';
				}elseif ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $mobalign == 'left' ) {
					$m_selectors[' .ive-text-option-parent']['margin-left'] = 0;
				}elseif ( ($optionSide == 'column-reverse' || $optionSide == 'column') && $mobalign == 'center' ) {
					$m_selectors[' .ive-text-option-parent']['margin'] = 'auto';
				}

				$selectors[' .ive-paragraph-icon']['font-size'] = $iconfontSizedesk;
				$t_selectors[' .ive-paragraph-icon']['font-size'] = $iconfontSizetab;
				$m_selectors[' .ive-paragraph-icon']['font-size'] = $iconfontSizemob;
				$selectors[' .ive-paragraph-icon']['color'] = isset($attr['iconColor']) ? $attr['iconColor'] : '';

				$selectors[' .ive-paragraph-icon:hover']['color'] = isset($attr['iconHoverColor']) ? $attr['iconHoverColor'] : '';

			}
			if ($textOptions !== 'text') {
				$selectors['.ive-advanced-text-wrap']['display'] = 'flex';
				$selectors['.ive-advanced-text-wrap']['flex-direction'] = $optionSide;
			}

			if ( ($optionSide == 'row-reverse' || $optionSide == 'row') && $textOptions !== 'text') {
				$selectors['.ive-advanced-text-wrap']['justify-content'] = $deskalign;
				$t_selectors['.ive-advanced-text-wrap']['justify-content'] = $tabalign;
				$m_selectors['.ive-advanced-text-wrap']['justify-content'] = $mobalign;
			}

			if ($optionSide === 'row' && $textOptions !== 'text' ) {
				$selectors[' .ive-text-option-parent']['padding-right'] = isset($attr['optionPadding']) ? $attr['optionPadding'].$unit : '20'.$unit;
			}
			if ($optionSide === 'row-reverse' && $textOptions !== 'text' ) {
				$selectors[' .ive-text-option-parent']['padding-left'] = isset($attr['optionPadding2']) ? $attr['optionPadding2'].$unit : '20'.$unit;
			}

			// if ($optionSide == 'row' && $textOptions !== 'text' ) {
			// 	// $selectors[' .ive-advanced-text-inner-wrap']['padding-right'] = isset($attr['optionPadding2']) ? $attr['optionPadding2'].$unit : '20'.$unit;
			// }

			if($gradientDisable){
				$selectors[' .ive-advanced-text-inner-wrap']['-webkit-text-fill-color'] = 'transparent';
				$selectors[' .ive-advanced-text-inner-wrap']['-webkit-background-clip'] = 'text';

				$selectors['.ive-advanced-text-inner-wrap:hover']['-webkit-text-fill-color'] = 'transparent';
				$selectors['.ive-advanced-text-inner-wrap:hover']['-webkit-background-clip'] = 'text';
				$selectors['.ive-advanced-text-inner-wrap:hover']['background-clip'] = 'text';
			}

			if ($animationtype != 'none' ) {
				$selectors['.ive-advanced-text-wrap']['animation-iteration-count'] = isset($attr['animationiteration']) ? $attr['animationiteration'] : 1;
				$selectors['.ive-advanced-text-wrap']['visibility'] = 'visible';
				$selectors['.ive-advanced-text-wrap']['animation-name'] = $animationtype;
				$selectors['.ive-advanced-text-wrap']['animation-delay'] = isset($attr['animationdelay']) ? $attr['animationdelay'].'s' : '';
				$selectors['.ive-advanced-text-wrap']['animation-duration'] = isset($attr['animationspeed']) ? $attr['animationspeed'].'s' : '';
			}

			if ($bordertype != 'none') {
				$border = $bordertop.' '.$borderright.' '.$borderbottom.' '.$borderleft;
				$borderRadius = $borderadiustop.' '.$borderadiusright.' '.$borderadiusbottom.' '.$borderadiusleft;

				$selectors['.ive-advanced-text-wrap']['border-color'] = isset($attr['bordercolor']) ? $attr['bordercolor'] : '';
				$selectors['.ive-advanced-text-wrap']['border-width'] = $border;
				$selectors['.ive-advanced-text-wrap']['border-style'] = $bordertype;
				$selectors['.ive-advanced-text-wrap']['border-radius'] = $borderRadius;
			}

			if ($bordertypehover != 'none') {
				$borderHov = $bordertophover.' '.$borderrighthover.' '.$borderbottomhover.' '.$borderlefthover;
				$borderRadiusHov = $borderadiustophover.' '.$borderadiusrighthover.' '.$borderadiusbottomhover.' '.$borderadiuslefthover;

				$selectors['.ive-advanced-text-wrap:hover']['border-color'] = isset($attr['bordercolorhover']) ? $attr['bordercolorhover'] : '';
				$selectors['.ive-advanced-text-wrap:hover']['border-width'] = $borderHov;
				$selectors['.ive-advanced-text-wrap:hover']['border-style'] = $bordertypehover;
				$selectors['.ive-advanced-text-wrap:hover']['border-radius'] = $borderRadiusHov;
			}

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);
			return IVE_Helper::generate_all_css( $combined_selectors, '.ive-div-advance-text' . $attr['uniqueID'] );
		}

		public static function get_multiblock_slider_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/carousel']['attributes'];

			$attr = array_merge( $defaults, $attr );

			//attributes
			$unit = 'px';
			$innerPadding 		= isset($attr['innerPadding']) ? $attr['innerPadding'] : [];
			$contentBorder 		= isset($attr['contentBorder']) ? $attr['contentBorder'] : [];
			$owlNavMaxWidth 	= isset($attr['owlNavMaxWidth']) ? $attr['owlNavMaxWidth'] : [];
			$owlNavTop 			= isset($attr['owlNavTop']) ? $attr['owlNavTop'] : [];
			$owlNavLeft 		= isset($attr['owlNavLeft']) ? $attr['owlNavLeft'] : [];
			$owlNavRight 		= isset($attr['owlNavRight']) ? $attr['owlNavRight'] : [];
			$navType 			= isset($attr['navType']) ? $attr['navType'] : [];
			$arrowBtnWidth 		= isset($attr['arrowBtnWidth']) ? $attr['arrowBtnWidth'] : [];
			$arrowBtnHeight 	= isset($attr['arrowBtnHeight']) ? $attr['arrowBtnHeight'] : [];
			$navArrowBdWidth 	= isset($attr['navArrowBdWidth']) ? $attr['navArrowBdWidth'] : [];
			$arrowBtnPadding 	= isset($attr['arrowBtnPadding']) ? $attr['arrowBtnPadding'] : [];
			$navArrowSize 		= isset($attr['navArrowSize']) ? $attr['navArrowSize'] : [];
			$isbggradient 		= isset($attr['isbggradient']) ? $attr['isbggradient'] : false;
			$bgGradType 		= isset($attr['bgGradType']) ? $attr['bgGradType'] : '';

			$gradRadPos		= isset($attr['vBgImgPosition']) ? $attr['vBgImgPosition'] : '';
			$gradFirstColor		= isset($attr['bgfirstcolorr']) ? $attr['bgfirstcolorr'] : '';
			$gradFirstLoc		= isset($attr['bgGradLoc1']) ? $attr['bgGradLoc1'] .'%' : '';
			$gradSecondColor		= isset($attr['bgSecondColr']) ? $attr['bgSecondColr'] : '';
			$gradSecondLoc		= isset($attr['bgGradLocSecond']) ? $attr['bgGradLocSecond']. '%' : '';
			$gradAngle		= isset($attr['bgGradAngle']) ? $attr['bgGradAngle'].'deg' : '';

			$hovGradFirstColor		= isset($attr['hovGradFirstColor']) ? $attr['hovGradFirstColor'] : '';
			$hovGradSecondColor		= isset($attr['hovGradSecondColor']) ? $attr['hovGradSecondColor'] : '';
			$actvGradFirstColor		= isset($attr['actvGradFirstColor']) ? $attr['actvGradFirstColor'] : '';
			$actvGradSecondColor	= isset($attr['actvGradSecondColor']) ? $attr['actvGradSecondColor'] : '';

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$background = ''; $backgroundhover = ''; $backgrounddot = ''; $backgrounddotact = '';
			if($isbggradient){
					if($bgGradType === 'radial'){
						$background = ' radial-gradient(at '.$gradRadPos.', '. $gradFirstColor.' '. $gradFirstLoc .' , '. $gradSecondColor .' '. $gradSecondLoc .' ) !important' ;
						$backgroundhover = ' radial-gradient(at '.$gradRadPos.', '. $hovGradFirstColor.' '. $gradFirstLoc .' , '. $hovGradSecondColor .' '. $gradSecondLoc .' ) !important' ;
						$backgrounddotact = ' radial-gradient(at '.$gradRadPos.', '. $actvGradFirstColor.' '. $gradFirstLoc .' , '. $actvGradSecondColor .' '. $gradSecondLoc .' ) !important' ;
					}else{
						$background = ' linear-gradient('.$gradAngle.', '. $gradFirstColor.' '. $gradFirstLoc .', '. $gradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
						$backgroundhover = ' linear-gradient('.$gradAngle.', '. $hovGradFirstColor.' '. $gradFirstLoc .', '. $hovGradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
						$backgrounddotact = ' linear-gradient('.$gradAngle.', '. $actvGradFirstColor.' '. $gradFirstLoc .', '. $actvGradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
					}
					$backgrounddot	= $background ;
			}else{
				$background = isset($attr['navArrowBgColor']) ? $attr['navArrowBgColor'].' !important' : '#ffffff';
				$backgroundhover = isset($attr['navArrowBgHovColor']) ? $attr['navArrowBgHovColor'].' !important' : '#ffffff';
				$backgrounddot	= isset($attr['dotColor']) ? $attr['dotColor'].' !important' : '#222222';
				$backgrounddotact = isset($attr['dotActiveColor']) ? $attr['dotActiveColor'].' !important' : '#000000';
			}

			$selectors = array(
				' .ive-carousel-content-wrap' => array(
					'padding'				=> ( isset($innerPadding) ) ? ($innerPadding[2].$unit.' '.$innerPadding[1].$unit.' '.$innerPadding[3].$unit.' '.$innerPadding[0].$unit).' !important' : 0,
					'border-width'			=> ( isset($contentBorder) ) ? ($contentBorder[2].$unit.' '.$contentBorder[1].$unit.' '.$contentBorder[3].$unit.' '.$contentBorder[0].$unit).' !important' : 0,
					'border-style'			=> isset($attr['contentBorderStyle']) ? $attr['contentBorderStyle'].' !important' : 'solid',
					'border-radius'			=> isset($attr['contentBorderRadius']) ? $attr['contentBorderRadius'].$unit.' !important' : 0
				),
				' .owl-dots .owl-dot.active span' => array(
					'background'			=> $backgrounddotact,
				),
				' .owl-dots .owl-dot span' => array(
					'background'			=> $backgrounddot,
					'border-radius'		=> isset($attr['dotBorderRadius']) ? $attr['dotBorderRadius'].$unit.' !important' : 0,
					'border-style'		=> 'solid !important',
					'border-color'					=> isset($attr['navArrowBdColor']) ? $attr['navArrowBdColor'].' !important' : '#000000'
				),
				' .owl-dots' => array(
					'width'					=> 'auto !important',
					'position'				=> 'relative !important',
					'float'					=> isset($attr['dotsalign']) ? $attr['dotsalign'].' !important' : 'center',
					'padding-top'			=> ( isset($attr['dotPaddingTop']) && $attr['dotPaddingTop'] !== 0) ? $attr['dotPaddingTop'].$unit.' !important' : ''
				),
				' .owl-nav button' => array(
					'border-radius'			=> isset($attr['navArrowBdRadius']) ? $attr['navArrowBdRadius'].$unit.' !important' : 0,
					'border-style'			=> 'solid !important',
					'color'					=> isset($attr['navArrowColor']) ? $attr['navArrowColor'].' !important' : '#000000',
					'background'			=> $background,
					'border-color'			=> isset($attr['navArrowBdColor']) ? $attr['navArrowBdColor'].' !important' : '#000000',
				),
				' .owl-nav button i' => array(
					'color'						=> isset($attr['navArrowColor']) ? $attr['navArrowColor'].' !important' : '#000000',
				),
				' .owl-nav button:hover' => array(
					'color'						=> isset($attr['navArrowHovColor']) ? $attr['navArrowHovColor'].' !important' : '#ffffff',
					'background'			=> $backgroundhover,
					'border-color'		=> isset($attr['navArrowBdHovColor']) ? $attr['navArrowBdHovColor'].' !important' : '#ffffff',
				),
				' .owl-nav button:hover i' => array(
					'color'						=> isset($attr['navArrowHovColor']) ? $attr['navArrowHovColor'].' !important' : '#ffffff',
				),
				'.ive-carousel-wrap' => array(
					'max-width'				=> isset($attr['maxWidth']) ? $attr['maxWidth'].$unit : 'none',
				)
			);

			$m_selectors = array(
				' .owl-nav' => array(
					'max-width'				=> ( !empty($owlNavMaxWidth) ) ? $owlNavMaxWidth[2].'% !important' : '100% !important',
					'top'					=> ( !empty($owlNavTop) ) ? $owlNavTop[2].'% !important' : '35% !important',
				),
				' .owl-nav button' => array(
					'width'					=> ( !empty($arrowBtnWidth) ) ? $arrowBtnWidth[2].$unit.' !important' : '40'.$unit.' !important',
					'height'				=> ( !empty($arrowBtnHeight) ) ? $arrowBtnHeight[2].$unit.' !important' : '40'.$unit.' !important',
					'border-width'			=> ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[2].$unit.' !important' : 0,
					'position'				=> 'relative !important'
				),
				' .owl-nav button.owl-prev' => array(
					'padding'				=> (isset($arrowBtnPadding) ? ($arrowBtnPadding[2][0].$unit.' '.$arrowBtnPadding[2][1].$unit.' '.$arrowBtnPadding[2][2].$unit.' '.$arrowBtnPadding[2][3].$unit).' !important' : ''),
					'left'					=> ( isset($owlNavLeft) ) ? $owlNavLeft[2].'% !important' : 0,
				),
				' .owl-nav button.owl-next' => array(
					'padding'				=> (isset($arrowBtnPadding) ? ($arrowBtnPadding[2][0].$unit.' '.$arrowBtnPadding[2][1].$unit.' '.$arrowBtnPadding[2][2].$unit.' '.$arrowBtnPadding[2][3].$unit).' !important' : ''),
					'right'					=> ( isset($owlNavRight) ) ? $owlNavRight[2].'% !important' : 0,
				),
				' .owl-nav button i' => array(
					'font-size'				=> (isset($navArrowSize) ? $navArrowSize[2].$unit : '20'.$unit ).' !important'
				),
				' .owl-dots .owl-dot span' => array(
				  'border-width'			=> ( isset($navArrowBdWidth) ) ? $navArrowBdWidth[2].$unit.' !important' : 0,
				),
			);

			$t_selectors = array(
				' .owl-nav' => array(
					'max-width'				=> ( isset($owlNavMaxWidth) ) ? $owlNavMaxWidth[1].'% !important' : '100% !important',
					'top'					=> ( isset($owlNavTop) ) ? $owlNavTop[1].'% !important' : '35% !important',
				),
				' .owl-nav button' => array(
					'width'					=> ( isset($arrowBtnWidth) ) ? $arrowBtnWidth[1].$unit.' !important' : '40'.$unit.' !important',
					'height'				=> ( isset($arrowBtnHeight) ) ? $arrowBtnHeight[1].$unit.' !important' : '40'.$unit.' !important',
					'border-width'			=> ( isset($navArrowBdWidth) ) ? $navArrowBdWidth[1].$unit.' !important' : 0,
					'position'				=> 'relative !important'
				),
				' .owl-nav button.owl-prev' => array(
					'padding'				=> (isset($arrowBtnPadding) ? ($arrowBtnPadding[1][0].$unit.' '.$arrowBtnPadding[1][1].$unit.' '.$arrowBtnPadding[1][2].$unit.' '.$arrowBtnPadding[1][3].$unit).' !important' : ''),
					'left'					=> ( isset($owlNavLeft) ) ? $owlNavLeft[1].'% !important' : 0,
				),
				' .owl-nav button.owl-next' => array(
					'padding'				=> (isset($arrowBtnPadding) ? ($arrowBtnPadding[1][0].$unit.' '.$arrowBtnPadding[1][1].$unit.' '.$arrowBtnPadding[1][2].$unit.' '.$arrowBtnPadding[1][3].$unit).' !important' : ''),
					'right'					=> ( isset($owlNavRight) ) ? $owlNavRight[1].'% !important' : 0,
				),
				' .owl-nav button i' => array(
					'font-size'				=> (isset($navArrowSize) ? $navArrowSize[1].$unit : '20'.$unit ).' !important'
				),
				' .owl-dots .owl-dot span' => array(
				  'border-width'			=> ( isset($navArrowBdWidth) ) ? $navArrowBdWidth[1].$unit.' !important' : 0,
				),
			);

			$d_selectors = array(
				' .owl-nav' => array(
					'max-width'				=> ( isset($owlNavMaxWidth) ) ? $owlNavMaxWidth[0].'% !important' : '100% !important',
					'top'					=> ( isset($owlNavTop) ) ? $owlNavTop[0].'% !important' : '35% !important',
				),
				' .owl-nav button' => array(
					'width'					=> ( isset($arrowBtnWidth) ) ? $arrowBtnWidth[0].$unit.' !important' : '40'.$unit.' !important',
					'height'				=> ( isset($arrowBtnHeight) ) ? $arrowBtnHeight[0].$unit.' !important' : '40'.$unit.' !important',
					'border-width'			=> ( isset($navArrowBdWidth) ) ? $navArrowBdWidth[0].$unit.' !important' : 0,
					'position'				=> 'relative !important'
				),
				' .owl-nav button.owl-prev' => array(
					'padding'				=> (isset($arrowBtnPadding) ? ($arrowBtnPadding[0][0].$unit.' '.$arrowBtnPadding[0][1].$unit.' '.$arrowBtnPadding[0][2].$unit.' '.$arrowBtnPadding[0][3].$unit).' !important' : ''),
					'left'					=> ( isset($owlNavLeft) ) ? $owlNavLeft[0].'% !important' : 0,
				),
				' .owl-nav button.owl-next' => array(
					'padding'				=> (isset($arrowBtnPadding) ? ($arrowBtnPadding[0][0].$unit.' '.$arrowBtnPadding[0][1].$unit.' '.$arrowBtnPadding[0][2].$unit.' '.$arrowBtnPadding[0][3].$unit).' !important' : ''),
					'right'					=> ( isset($owlNavRight) ) ? $owlNavRight[0].'% !important' : 0,
				),
				' .owl-nav button i' => array(
					'font-size'				=> (isset($navArrowSize) ? $navArrowSize[0].$unit : '20'.$unit ).' !important'
				),
				' .owl-dots .owl-dot span' => array(
				  'border-width'			=> ( isset($navArrowBdWidth) ) ? $navArrowBdWidth[0].$unit.' !important' : 0,
				),
			);

			if ( !empty($navType) ) {
				$d_selectors[' .owl-nav']['display'] 	= ( $navType[0] == 'arrows' || $navType[0] == 'both' ) ? '' : 'none !important';
				$d_selectors[' .owl-dots']['display'] 	= ( $navType[0] == 'dots' || $navType[0] == 'both' ) ? '' : 'none !important';

				$t_selectors[' .owl-nav']['display'] 	= ( $navType[1] == 'arrows' || $navType[1] == 'both' ) ? '' : 'none !important';
				$t_selectors[' .owl-dots']['display'] 	= ( $navType[1] == 'dots' || $navType[1] == 'both' ) ? '' : 'none !important';

				$m_selectors[' .owl-nav']['display'] 	= ( $navType[2] == 'arrows' || $navType[2] == 'both' ) ? '' : 'none !important';
				$m_selectors[' .owl-dots']['display'] 	= ( $navType[2] == 'dots' || $navType[2] == 'both' ) ? '' : 'none !important';
			}

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);

			return IVE_Helper::generate_all_css( $combined_selectors, '.ive-carousel-id' . $attr['uniqueID'] );
		}

		public static function get_multiblock_slider_image_css( $attr, $id ) {

			$defaults = IVE_Helper::$block_list['ive/carouselimage']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$left 		= isset($attr['left']) ? $attr['left'] : [];
			$right 		= isset($attr['right']) ? $attr['right'] : [];

			$isbggradient 	= isset($attr['isbggradient']) ? $attr['isbggradient'] : false;
			$bgGradType 		= isset($attr['bgGradType']) ? $attr['bgGradType'] : 'linear';
			$vBgImgPosition 		= isset($attr['vBgImgPosition']) ? $attr['vBgImgPosition'] : '';
			$bgfirstcolorr 		= isset($attr['bgfirstcolorr']) ? $attr['bgfirstcolorr'] : '';
			$bgGradLoc 		= isset($attr['bgGradLoc1']) ? $attr['bgGradLoc1']. '%' : '';
			$bgSecondColr 		= isset($attr['bgSecondColr']) ? $attr['bgSecondColr'] : '';
			$bgGradLocSecond 		= isset($attr['bgGradLocSecond']) ? $attr['bgGradLocSecond']. '%' : '';
			$bgGradAngle 		= isset($attr['bgGradAngle']) ? $attr['bgGradAngle'].'deg' : '';

			$background = '';
			if($isbggradient){
					if($bgGradType === 'radial'){
						$background = ' radial-gradient(at '.$gradRadPos.', '. $bgfirstcolorr.' '. $bgGradLoc .' , '. $bgSecondColr .' '. $bgGradLocSecond .' ) !important' ;
					}else{
						$background = ' linear-gradient('.$bgGradAngle.', '. $bgfirstcolorr.' '. $bgGradLoc .', '. $bgSecondColr . ' '. $bgGradLocSecond .' ) !important' ;
					}
			}else{
				$background = isset($attr['bgColor']) ? $attr['bgColor'] : '';
			}

			$t_selectors = array();
			$m_selectors = array();
			$d_selectors = array();
			$selectors   = array();

			$selectors = array(
				' .carousel-image' => array(
					'position'				=> 'relative'
				),
				' .carosol-overlay' => array(
					'position'				=> 'absolute',
					'left'					=> 0,
					'top'					=> 0,
					'width'					=> '100%',
					'height'				=> '100%',
					'background'		=> $background,
					'opacity'				=> isset($attr['bgOpacity']) ? $attr['bgOpacity']/100 : '',
				),
				' .carousel-content' => array(
					'transform'				=> 'translateY(-50%)',
					'top'					=> '50%'
				)
			);

			$d_selectors = array(
				' .carousel-content' => array(
					'left'					=> (!empty($left) ? $left[2].'% !important' : 0 ),
					'right'					=> (!empty($right) ? $right[2].'% !important' : 0 )
				)
			);

			$t_selectors = array(
				' .carousel-content' => array(
					'left'					=> (!empty($left) ? $left[1].'% !important' : 0 ),
					'right'					=> (!empty($right) ? $right[1].'% !important' : 0 )
				)
			);

			$m_selectors = array(
				' .carousel-content' => array(
					'left'					=> (!empty($left) ? $left[0].'% !important' : 0 ),
					'right'					=> (!empty($right) ? $right[0].'% !important' : 0 )
				)
			);

			$combined_selectors = array(
				'desktop' 		=> $selectors,
				'desktop_media'	=> $d_selectors,
				'tablet'  		=> $t_selectors,
				'mobile'  		=> $m_selectors,
			);

			return IVE_Helper::generate_all_css( $combined_selectors, '.carousel-outer-dynamic' . $attr['uniqueID'] );
		}

		public static function get_tabs_css( $attr, $id ) {
		  $defaults = IVE_Helper::$block_list['ive/tabs']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();
		  $tabCount = $attr['tabCount'];

		  $mainDivmaxWidth		= isset($attr['maxWidth']) ? $attr['maxWidth'] .'px ' : '';
		  $titleColorHover		= isset($attr['titleColorHover']) ? $attr['titleColorHover'] : '';
		  $titleBorderHover		= isset($attr['titleBorderHover']) ? $attr['titleBorderHover'] : '';
		  $titleBgHover				= isset($attr['titleBgHover']) ? $attr['titleBgHover'] : '';

		  $tabsidmclass 																			=	'.ive-tabs-wrap';
			$selectors[$tabsidmclass]['max-width']							= $mainDivmaxWidth;
			$d_selectors[' .ive-tabs-xl-left']['text-align']		= 'left !important';
			$d_selectors[' .ive-tabs-xl-center']['text-align']	= 'center !important';
		  $d_selectors[' .ive-tabs-xl-right']['text-align']		= 'right !important';

		  $tabhoverClass 															= ' .ive-title-item:hover .ive-tab-title';
		  $selectors[$tabhoverClass]['color']					= $titleColorHover.' !important';
		  $selectors[$tabhoverClass]['border-color']	= $titleBorderHover;

		  $backgroundColor			= isset($attr['titleBg']) ? $attr['titleBg'] : '';
		  $titleColor						= isset($attr['titleColor']) ? $attr['titleColor'].' !important' : '';
		  $size									= isset($attr['size']) ? $attr['size'] : '';
		  $sizeType							= isset($attr['sizeType']) ? $attr['sizeType'] : '';
		  $lineHeight						= isset($attr['lineHeight']) ? $attr['lineHeight'] : '';
		  $lineType							= isset($attr['lineType']) ? $attr['lineType'] : '';
		  $fontWeight						= isset($attr['fontWeight']) ? $attr['fontWeight'] : '';
		  $fontStyle						= isset($attr['fontStyle']) ? $attr['fontStyle'] : '';
		  $letterSpacing				= isset($attr['letterSpacing']) ? $attr['letterSpacing'].'px !important' : '';
		  $fontfamily						= isset($attr['typography']) ? $attr['typography'] : '';
		  $borderWidth					= isset($attr['titleBorderWidth']) ? $attr['titleBorderWidth'].'px !important' : '';
		  $borderRadius					= isset($attr['titleBorderRadius']) ? $attr['titleBorderRadius'].'px !important' : '';

			$titlePadding					= isset( $attr['titlePadding'] ) ? $attr['titlePadding'] : [
				[ 10, 10, 10, 10 ],
				[ 10, 10, 10, 10 ],
				[ 10, 10, 10, 10 ]
			];
			$selectors[' .ive-tab-title']['padding']		=	$titlePadding[0][0] . 'px ' . $titlePadding[0][1] . 'px ' . $titlePadding[0][2] . 'px ' . $titlePadding[0][3] . 'px !important';
			$t_selectors[' .ive-tab-title']['padding']	=	$titlePadding[1][0] . 'px ' . $titlePadding[1][1] . 'px ' . $titlePadding[1][2] . 'px ' . $titlePadding[1][3] . 'px !important';
			$m_selectors[' .ive-tab-title']['padding']	=	$titlePadding[2][0] . 'px ' . $titlePadding[2][1] . 'px ' . $titlePadding[2][2] . 'px ' . $titlePadding[2][3] . 'px !important';

			$titlePaddingTop			= isset($attr['titlePaddingTop']) ? $attr['titlePaddingTop'] . 'px ' : ' 0px ';
			$titlePaddingRight		= isset($attr['titlePaddingRight']) ? $attr['titlePaddingRight'].'px ' : ' 0px ';
			$titlePaddingBottom		= isset($attr['titlePaddingBottom']) ? $attr['titlePaddingBottom'].'px ' : ' 0px ';
			$titlePaddingLeft			= isset($attr['titlePaddingLeft']) ? $attr['titlePaddingLeft'].'px ' : ' 0px ';

			$titleMarginTop				= isset($attr['titleMarginTop']) ? $attr['titleMarginTop'].'px ' : '';
			$titleMarginBottom		= isset($attr['titleMarginBottom']) ? $attr['titleMarginBottom'].'px ' : '0px ';
			$titleMarginLeft			= isset($attr['titleMarginLeft']) ? $attr['titleMarginLeft'].'px ' : '0px ';
		  $titleMarginRight			= isset($attr['titleMarginRight']) ? $attr['titleMarginRight'].'px ' : '0px ';
		  $borderColor					= isset($attr['titleBorder']) ? $attr['titleBorder'].' !important' : '';
		  $widthType						= isset($attr['widthType']) ? $attr['widthType'] : 'normal';
		  $layout								= isset($attr['layout']) ? $attr['layout'] : 'tabs';
	    $tabSize							= isset($attr['tabSize']) ? $attr['tabSize'] : '';
	    $tabLineHeight				= isset($attr['tabLineHeight']) ? $attr['tabLineHeight'] : '';
	    $mobileSize						= isset($attr['mobileSize']) ? $attr['mobileSize'] : '';
	    $mobileLineHeight			= isset($attr['mobileLineHeight']) ? $attr['mobileLineHeight'] : '';

		  $titleMargin					= isset($attr['titleMargin']) ? $attr['titleMargin'].'px ' : '';
		  $titleColorActive			= isset($attr['titleColorActive']) ? $attr['titleColorActive'] .' !important' : '';
		  $titleBorderActive		= isset($attr['titleBorderActive']) ? $attr['titleBorderActive'] .' !important' : '';
		  $titleBgActive				= isset($attr['titleBgActive']) ? $attr['titleBgActive'] .' !important' : '';

			//slider
			$navType 					= isset($attr['navType']) ? $attr['navType'] : [];
			$owlNavMaxWidth 	= isset($attr['owlNavMaxWidth']) ? $attr['owlNavMaxWidth'] : [];
			$owlNavTop 				= isset($attr['owlNavTop']) ? $attr['owlNavTop'] : [];
			$owlNavLeft 			= isset($attr['owlNavLeft']) ? $attr['owlNavLeft'] : [];
			$owlNavRight 			= isset($attr['owlNavRight']) ? $attr['owlNavRight'] : [];
			$arrowBtnWidth 		= isset($attr['arrowBtnWidth']) ? $attr['arrowBtnWidth'] : [];
			$arrowBtnHeight 	= isset($attr['arrowBtnHeight']) ? $attr['arrowBtnHeight'] : [];
			$navArrowBdWidth 	= isset($attr['navArrowBdWidth']) ? $attr['navArrowBdWidth'] : [];
			$arrowBtnPadding 	= isset($attr['arrowBtnPadding']) ? $attr['arrowBtnPadding'] : [];
			$navArrowSize 		= isset($attr['navArrowSize']) ? $attr['navArrowSize'] : [];

			$tabshowBGimg				= isset($attr['backgroundType']) ? $attr['backgroundType'] : 'color';

			$tabActiveClass 				= ' .ive-tab-title-active .ive-tab-title';
			$tabHeadingClass 				= ' .ive-tab-alltitle-heading';
			$tabHeadingHoverClass		= ' .ive-tab-alltitle-heading:hover ';

			$selectors[$tabHeadingHoverClass]['color']				= $titleColorHover.' !important';
			$selectors[$tabHeadingHoverClass]['border-color']	= $titleBorderHover;

	    $selectors[$tabActiveClass]['color']				= $titleColorActive;
	    $selectors[$tabActiveClass]['border-color']	= $titleBorderActive;
			if( $tabshowBGimg == 'color' ) {
				$selectors[$tabHeadingHoverClass]['background-color']= $titleBgHover;
		    $selectors[$tabActiveClass]['background-color'] = $titleBgActive;
		    $selectors[$tabHeadingClass]['background-color'] = $backgroundColor;
			  $selectors[$tabhoverClass]['background-color']= $titleBgHover;
		  }
	    $selectors[$tabHeadingClass]['color'] = $titleColor ;
	    $selectors[$tabHeadingClass]['font-size'] = $size.$sizeType;
	    $selectors[$tabHeadingClass]['line-height'] = $lineHeight.$lineType;
	    $selectors[$tabHeadingClass]['font-weight'] = $fontWeight ;
	    $selectors[$tabHeadingClass]['font-style'] = $fontStyle;
	    $selectors[$tabHeadingClass]['letter-spacing'] = $letterSpacing;
	    $selectors[$tabHeadingClass]['font-family'] = $fontfamily	;
	    $selectors[$tabHeadingClass]['border-width'] = $borderWidth ;
	    $selectors[$tabHeadingClass]['border-radius'] = $borderRadius ;

			$selectors[$tabHeadingClass]['border-color'] = $borderColor	;

			$marginRighttab		= isset($attr['gutter'][ 1 ]) ? $attr['gutter'][ 1 ].'px' : '';
			$t_selectors[$tabHeadingClass]['margin-right'] = $marginRighttab	;

			$t_selectors[$tabHeadingClass]['font-size'] = $tabSize.$sizeType;
			$t_selectors[$tabHeadingClass]['line-height'] = $tabLineHeight.$lineType;

			//Mobilecss
			$marginRighttab		= isset($attr['gutter'][ 2 ]) ? $attr['gutter'][ 2 ].'px' : '';
			$m_selectors[$tabHeadingClass]['margin-right'] = $marginRighttab	;

			$m_selectors[$tabHeadingClass]['font-size'] = $mobileSize.$sizeType;
			$m_selectors[$tabHeadingClass]['line-height'] = $mobileLineHeight.$lineType;

		  if( 'vtabs' !== $layout && 'percent' === $widthType ) {
				$selectors[' > .ive-tabs-title-list li .ive-tab-title']['margin-right']	=	isset( $attr['gutter'][0] ) ? $attr['gutter'][0] . 'px' : '10px';

				if ( isset( $attr['tabWidth'] ) && ! empty( $attr['tabWidth'] ) && is_array( $attr['tabWidth'] ) && ! empty( $attr['tabWidth'][1] ) && '' !== $attr['tabWidth'][1] ) {
					$t_selectors[' > .ive-tabs-title-list.ive-tabs-list-columns > li']['flex']	=	'0 1 ' . round( 100 / $attr['tabWidth'][1], 2 ) . '%';
				}

				if ( isset( $attr['gutter'] ) && ! empty( $attr['gutter'] ) && is_array( $attr['gutter'] ) && isset( $attr['gutter'][1] ) && is_numeric( $attr['gutter'][1] ) ) {
					$t_selectors[' > .ive-tabs-title-list li .ive-tab-title']['margin-right']	=	$attr['gutter'][1] . 'px';
				}

				if ( isset( $attr['tabWidth'] ) && ! empty( $attr['tabWidth'] ) && is_array( $attr['tabWidth'] ) && ! empty( $attr['tabWidth'][2] ) && '' !== $attr['tabWidth'][2] ) {
					$m_selectors[' > .ive-tabs-title-list.ive-tabs-list-columns > li']['flex']	=	'0 1 ' . round( 100 / $attr['tabWidth'][2], 2 ) . '%';
				}

				if ( isset( $attr['gutter'] ) && ! empty( $attr['gutter'] ) && is_array( $attr['gutter'] ) && isset( $attr['gutter'][2] ) && is_numeric( $attr['gutter'][2] ) ) {
					$m_selectors[' > .ive-tabs-title-list li .ive-tab-title']['margin-right']	=	$attr['gutter'][2] . 'px';
				}

				$setmargin = $titleMarginTop .' 0px '. $titleMarginBottom .' !important' ;
		  } else {
		    $marginRight= '';
		    $marginTab = $titleMargin;
				$setmargin = $titleMarginTop . $titleMarginRight . $titleMarginBottom . $titleMarginLeft .' !important' ;
				$selectors[$tabHeadingClass]['margin-right'] = $marginRight	;
		  }


		  /*if($titleMargin){
		    $tabmargin = $titleMargin	. $marginTab . $titleMargin . $marginTab .' !important' ;
		  }else{
		    $tabmargin= ' !important';
		  }*/

			$tabtabTitleClass 	= ' .ive-tab-alltitle-item' ;
	    $selectors[$tabtabTitleClass]['margin'] = $setmargin;

		  $tabcontentminHeight		= isset($attr['minHeight']) ? $attr['minHeight'] .' px ' : '';
		  $tabcontentinnerPadding		= isset($attr['innerPadding']) ? $attr['innerPadding'] .'px ' : '';
		  $tabcontentcontentBorder		= isset($attr['contentBorder']) ? $attr['contentBorder'] .'px ' : '';
		  $tabcontentcontentBgColor		= isset($attr['contentBgColor']) ? $attr['contentBgColor'] : '';
		  $contentBorderColor		= isset($attr['contentBorderColor']) ? $attr['contentBorderColor'] : '';

		  $selectors[' .ive-tabs-content-wrap']['min-height'] 						= $tabcontentminHeight;
		  $selectors[' .ive-tabs-content-wrap']['padding'] 								= $tabcontentinnerPadding;
		  $selectors[' .ive-tabs-content-wrap']['border-width'] 					= $tabcontentcontentBorder;
		  $selectors[' .ive-tabs-content-wrap']['background-color'] 			= $tabcontentcontentBgColor;
		  $selectors[' .ive-tabs-content-wrap']['border-color'] 					= $contentBorderColor;

			$tabSubtitleClass = ' .ive-title-sub-text';
			$subtitleweight		= isset($attr['subtitleFont'][0]['weight']) ? $attr['subtitleFont'][0]['weight'] : '';
			$subtitlestyle		= isset($attr['subtitleFont'][0]['style']) ? $attr['subtitleFont'][0]['style'] : '';
			$subtitleletterSpacing		= isset($attr['subtitleFont'][0]['letterSpacing']) ? $attr['subtitleFont'][0]['letterSpacing'] .'px' : '';
			$subtitlefamily		= isset($attr['subtitleFont'][0]['family']) ? $attr['subtitleFont'][0]['family'] : '';
			$subtitlepadding		= isset($attr['subtitleFont'][0]['padding']) ? $attr['subtitleFont'][0]['padding'][0].'px '.$attr['subtitleFont'][0]['padding'][1].'px '.$attr['subtitleFont'][0]['padding'][2].'px '.$attr['subtitleFont'][0]['padding'][3].'px '  : '';
			$subtitlemargin		= isset($attr['subtitleFont'][0]['margin']) ? $attr['subtitleFont'][0]['margin'][0].'px '.$attr['subtitleFont'][0]['margin'][1].'px '.$attr['subtitleFont'][0]['margin'][2].'px '.$attr['subtitleFont'][0]['margin'][3].'px '  : '';

			$subtitlefontSizeDesk		= isset($attr['subtitleFont'][0]['size'][0]) ? $attr['subtitleFont'][0]['size'][0] .$attr['subtitleFont'][0]['sizeType'] : '';
	    $subtitlelineHeightDesk		= isset($attr['subtitleFont'][0]['lineHeight'][0]) ? $attr['subtitleFont'][0]['lineHeight'][0] .$attr['subtitleFont'][0]['lineType'] : '';

			$subtitlefontSizetab		= isset($attr['subtitleFont'][0]['size'][1]) ? $attr['subtitleFont'][0]['size'][1] .$attr['subtitleFont'][0]['sizeType'] .' !important' : '';
	    $subtitlelineHeightab		= isset($attr['subtitleFont'][0]['lineHeight'][1]) ? $attr['subtitleFont'][0]['lineHeight'][1] .$attr['subtitleFont'][0]['lineType'] : '';

			$subtitlefontSizemob		= isset($attr['subtitleFont'][0]['size'][2]) ? $attr['subtitleFont'][0]['size'][2] .$attr['subtitleFont'][0]['sizeType'] : '';
			$subtitlelineHeightmob		= isset($attr['subtitleFont'][0]['lineHeight'][2]) ? $attr['subtitleFont'][0]['lineHeight'][2] .$attr['subtitleFont'][0]['lineType'] : '';

			$d_selectors[$tabSubtitleClass]['font-size'] = $subtitlefontSizeDesk	;
			$d_selectors[$tabSubtitleClass]['line-height'] = $subtitlelineHeightDesk ;
			$t_selectors[$tabSubtitleClass]['font-size'] = $subtitlefontSizetab	;
			$t_selectors[$tabSubtitleClass]['line-height'] = $subtitlelineHeightab	;
			$m_selectors[$tabSubtitleClass]['font-size'] = $subtitlefontSizemob	;
			$m_selectors[$tabSubtitleClass]['line-height'] = $subtitlelineHeightmob	;

			$selectors[$tabSubtitleClass]['font-weight'] 					= $subtitleweight;
			$selectors[$tabSubtitleClass]['font-style'] 					= $subtitlestyle;
			$selectors[$tabSubtitleClass]['letter-spacing'] 			= $subtitleletterSpacing;
			$selectors[$tabSubtitleClass]['font-family'] 					= $subtitlefamily;
			$selectors[$tabSubtitleClass]['padding'] 							= $subtitlepadding;
			$selectors[$tabSubtitleClass]['margin'] 							= $subtitlemargin;

		  for ($i=0; $i < $tabCount; $i++) {
		    //classes
		    $tabHeadingImgClass = ' .ive-tab-img-'.$i;
		    $titleImgHeight		= isset($attr['titles'][$i]['imageheight']) ? $attr['titles'][$i]['imageheight'] .'px ' : '';
				$imagewidth				= isset($attr['titles'][$i]['imagewidth']) ? $attr['titles'][$i]['imagewidth'] .'px ' : '';
		    $selectors[$tabHeadingImgClass]['height'] = $titleImgHeight;
		    $selectors[$tabHeadingImgClass]['width'] = $imagewidth;
		  }

			$tabshowBGimg				= isset($attr['backgroundType']) ? $attr['backgroundType'] : '';
		  if($tabshowBGimg == 'image'){
		  	for ($i=0; $i < $tabCount; $i++) {
					$ic = $i+1;
					$tabClass = ' .tabBGImg.ive-title-item-'.$ic;
			 		$tabBGImg				= isset($attr['titles'][$i]['normalBGimgURL']) ? $attr['titles'][$i]['normalBGimgURL'] : '';
					$selectors[$tabClass]['background'] = "url('$tabBGImg')";
					$selectors[$tabClass]['background-size'] = "cover";

			 		$tabhoverBGImg				= isset($attr['titles'][$i]['hoverBGImgimgURL']) ? $attr['titles'][$i]['hoverBGImgimgURL'] : '';
					$selectors[$tabClass.':hover']['background'] = "url('$tabhoverBGImg')";
					$selectors[$tabClass.':hover']['background-size'] = "cover";

			 		$activeBGimgURL				= isset($attr['titles'][$i]['activeBGimgURL']) ? $attr['titles'][$i]['activeBGimgURL'] : '';
					$selectors[$tabClass.'.ive-tab-title-active']['background'] = "url('$activeBGimgURL')";
					$selectors[$tabClass.'.ive-tab-title-active']['background-size'] = "cover";

					$selectors[$tabHeadingClass]['background-color'] = 'transparent !important';
				}
		  }

			if($tabshowBGimg == 'gradient'){
				$vBgImgPosition = isset($attr['vBgImgPosition']) ? $attr['vBgImgPosition'] : 'center center';
				$bgfirstcolorr = isset($attr['bgfirstcolorr']) ? $attr['bgfirstcolorr'] : '';
				$headhoverbgfirstcolor = isset($attr['headhoverbgfirstcolor']) ? $attr['headhoverbgfirstcolor'] : '';
				$bgGradLoc = isset($attr['bgGradLoc']) ? $attr['bgGradLoc'] : 0;
				$bgSecondColr = isset($attr['bgSecondColr']) ? $attr['bgSecondColr'] : '';
				$headhoverbgSecondColr = isset($attr['headhoverbgSecondColr']) ? $attr['headhoverbgSecondColr'] : '';
				$bgGradLocSecond = isset($attr['bgGradLocSecond']) ? $attr['bgGradLocSecond'] : 100;
				$bgGradAngle = isset($attr['bgGradAngle']) ? $attr['bgGradAngle'] : 180;
				$backgdOpacity				= isset($attr['backgdOpacity']) ? $attr['backgdOpacity'] : '';
				$actvGradFirstColor = isset($attr['actvGradFirstColor']) ? $attr['actvGradFirstColor'] : '';
				$actvGradSecondColor = isset($attr['actvGradSecondColor']) ? $attr['actvGradSecondColor'] : '';

				$bgGradType = isset($attr['bgGradType']) ? $attr['bgGradType'] : '';

				if('radial' === $bgGradType){
					$backgroundImage = 'radial-gradient(at '.$vBgImgPosition.','.$bgfirstcolorr.' '.$bgGradLoc.'%, '.$bgSecondColr.' '.$bgGradLocSecond.'%)';
				}else{
					$backgroundImage = 'linear-gradient('.$bgGradAngle.'deg, '.$bgfirstcolorr.' '.$bgGradLoc.'%, '.$bgSecondColr.'  '.$bgGradLocSecond.'%)';
				}
				if('radial' === $bgGradType){
					$backgroundImageHov = 'radial-gradient(at '.$vBgImgPosition.','.$headhoverbgfirstcolor.' '.$bgGradLoc.'%, '.$headhoverbgSecondColr.' '.$bgGradLocSecond.'%)';
					$titleBgActive = 'radial-gradient(at '.$vBgImgPosition.','.$actvGradFirstColor.' '.$bgGradLoc.'%, '.$actvGradSecondColor.' '.$bgGradLocSecond.'%)';
				}else{
					$backgroundImageHov = 'linear-gradient('.$bgGradAngle.'deg, '.$headhoverbgfirstcolor.' '.$bgGradLoc.'%, '.$headhoverbgSecondColr.'  '.$bgGradLocSecond.'%)';
					$titleBgActive = 'linear-gradient('.$bgGradAngle.'deg, '.$actvGradFirstColor.' '.$bgGradLoc.'%, '.$actvGradSecondColor.'  '.$bgGradLocSecond.'%)';
				}
				$selectors[$tabHeadingClass]['background'] = $backgroundImage ;
				$selectors[$tabHeadingClass.':hover']['background'] = $backgroundImageHov ;
				$selectors[$tabActiveClass]['background'] = $titleBgActive .'!important';

					// $selectors[$tabHeadingHoverClass]['background-color']= $titleBgHover;
			    // $selectors[$tabActiveClass]['background-color'] = $titleBgActive;
			    // $selectors[$tabHeadingClass]['background-color'] = $backgroundColor;
			}
			// print_r($attr['titles']);

			//SLIDER CSS
			$slider_class = ' .ive-tabs_carousel-id' . $attr['uniqueID'] ;

			if ( !empty($navType) ) {
				$d_selectors[$slider_class.' .owl-nav']['display'] 	= ( $navType[0] == 'arrows' || $navType[0] == 'both' ) ? '' : 'none !important';
				$d_selectors[$slider_class.' .owl-dots']['display'] 	= ( $navType[0] == 'dots' || $navType[0] == 'both' ) ? '' : 'none !important';

				$t_selectors[$slider_class.' .owl-nav']['display'] 	= ( $navType[1] == 'arrows' || $navType[1] == 'both' ) ? '' : 'none !important';
				$t_selectors[$slider_class.' .owl-dots']['display'] 	= ( $navType[1] == 'dots' || $navType[1] == 'both' ) ? '' : 'none !important';

				$m_selectors[$slider_class.' .owl-nav']['display'] 	= ( $navType[2] == 'arrows' || $navType[2] == 'both' ) ? '' : 'none !important';
				$m_selectors[$slider_class.' .owl-dots']['display'] 	= ( $navType[2] == 'dots' || $navType[2] == 'both' ) ? '' : 'none !important';
			}else{
				$selectors[$slider_class.' .owl-nav']['display'] 	= 'none !important';
				$selectors[$slider_class.' .owl-dots']['display'] = 'none !important';
			}

			$unit = 'px';
			$backgrounddotact = isset($attr['dotActiveColor']) ? $attr['dotActiveColor'].' !important' : '#000000';
			$background = isset($attr['navArrowBgColor']) ? $attr['navArrowBgColor'].' !important' : '#ffffff';
			$backgroundhover = isset($attr['navArrowBgHovColor']) ? $attr['navArrowBgHovColor'].' !important' : '#ffffff';
			$backgrounddot	= isset($attr['dotColor']) ? $attr['dotColor'].' !important' : '#222222';
			$backgrounddotact = isset($attr['dotActiveColor']) ? $attr['dotActiveColor'].' !important' : '#000000';

			$selectors[$slider_class.' .owl-dots .owl-dot.active span']['background'] = $backgrounddotact;
			$selectors[$slider_class.' .owl-dots .owl-dot span']['background'] = $backgrounddot;
			$selectors[$slider_class.' .owl-dots .owl-dot span']['border-radius'] = isset($attr['dotBorderRadius']) ? $attr['dotBorderRadius'].$unit.' !important' : 0;
			$selectors[$slider_class.' .owl-dots .owl-dot span']['border-style'] = 'solid !important';
			$selectors[$slider_class.' .owl-dots .owl-dot span']['border-color'] = isset($attr['navArrowBdColor']) ? $attr['navArrowBdColor'].' !important' : '#000000';
			$selectors[$slider_class.' .owl-dots']['width'] = 'auto !important';
			$selectors[$slider_class.' .owl-dots']['position'] = 'relative !important';
			$selectors[$slider_class.' .owl-dots']['float'] = isset($attr['dotsalign']) ? $attr['dotsalign'].' !important' : 'center';
			$selectors[$slider_class.' .owl-dots']['padding-top'] = ( isset($attr['dotPaddingTop']) && $attr['dotPaddingTop'] !== 0) ? $attr['dotPaddingTop'].$unit.' !important' : '';
			$selectors[$slider_class.' .owl-nav button']['border-radius'] = isset($attr['navArrowBdRadius']) ? $attr['navArrowBdRadius'].$unit.' !important' : 0;
			$selectors[$slider_class.' .owl-nav button']['border-style'] = 'solid !important' ;
			$selectors[$slider_class.' .owl-nav button']['color'] = isset($attr['navArrowColor']) ? $attr['navArrowColor'].' !important' : '#000000';
			$selectors[$slider_class.' .owl-nav button']['background'] = $background ;
			$selectors[$slider_class.' .owl-nav button']['border-color'] = isset($attr['navArrowBdColor']) ? $attr['navArrowBdColor'].' !important' : '#000000';
			$selectors[$slider_class.' .owl-nav button i']['color'] = isset($attr['navArrowColor']) ? $attr['navArrowColor'].' !important' : '#000000';
			$selectors[$slider_class.' .owl-nav button:hover']['color'] = isset($attr['navArrowHovColor']) ? $attr['navArrowHovColor'].' !important' : '#ffffff' ;
			$selectors[$slider_class.' .owl-nav button:hover']['background'] = $backgroundhover;
			$selectors[$slider_class.' .owl-nav button:hover']['border-color'] = isset($attr['navArrowBdHovColor']) ? $attr['navArrowBdHovColor'].' !important' : '#ffffff';
			$selectors[$slider_class.' .owl-nav button:hover i']['color'] = isset($attr['navArrowHovColor']) ? $attr['navArrowHovColor'].' !important' : '#ffffff';
			$selectors[$slider_class.' .owl-nav button:hover']['border-color'] = isset($attr['navArrowBdHovColor']) ? $attr['navArrowBdHovColor'].' !important' : '#ffffff';

			$m_selectors[$slider_class.' .owl-nav']['max-width'] = ( !empty($owlNavMaxWidth) ) ? $owlNavMaxWidth[2].'% !important' : '100% !important';
			$m_selectors[$slider_class.' .owl-nav']['top'] = ( !empty($owlNavTop) ) ? $owlNavTop[2].'% !important' : '35% !important';

			$m_selectors[$slider_class.' .owl-nav button']['width'] = ( !empty($arrowBtnWidth) ) ? $arrowBtnWidth[2].$unit.' !important' : '40'.$unit.' !important';
			$m_selectors[$slider_class.' .owl-nav button']['height'] = ( !empty($arrowBtnHeight) ) ? $arrowBtnHeight[2].$unit.' !important' : '40'.$unit.' !important';
			$m_selectors[$slider_class.' .owl-nav button']['border-width'] = ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[2].$unit.' !important' : 0;
			$m_selectors[$slider_class.' .owl-nav button']['position'] = 'relative !important';

			$m_selectors[$slider_class.' .owl-nav button.owl-prev']['padding'] = (!empty($arrowBtnPadding) ? ($arrowBtnPadding[2][0].$unit.' '.$arrowBtnPadding[2][1].$unit.' '.$arrowBtnPadding[2][2].$unit.' '.$arrowBtnPadding[2][3].$unit).' !important' : '');
			$m_selectors[$slider_class.' .owl-nav button.owl-prev']['left'] = ( !empty($owlNavLeft) ) ? $owlNavLeft[2].'% !important' : 0;
			$m_selectors[$slider_class.' .owl-nav button.owl-next']['padding'] = (!empty($arrowBtnPadding) ? ($arrowBtnPadding[2][0].$unit.' '.$arrowBtnPadding[2][1].$unit.' '.$arrowBtnPadding[2][2].$unit.' '.$arrowBtnPadding[2][3].$unit).' !important' : '');
			$m_selectors[$slider_class.' .owl-nav button.owl-next']['right'] = ( !empty($owlNavRight) ) ? $owlNavRight[2].'% !important' : 0;

			$m_selectors[$slider_class.' .owl-nav button i']['font-size'] = (!empty($navArrowSize) ? $navArrowSize[2].$unit : '20'.$unit ).' !important';

			$m_selectors[$slider_class.' .owl-dots .owl-dot span']['border-width'] = ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[2].$unit.' !important' : 0;


			$t_selectors[$slider_class.' .owl-nav']['max-width'] = ( !empty($owlNavMaxWidth) ) ? $owlNavMaxWidth[1].'% !important' : '100% !important';
			$t_selectors[$slider_class.' .owl-nav']['top'] = ( !empty($owlNavTop) ) ? $owlNavTop[1].'% !important' : '35% !important';
			$t_selectors[$slider_class.' .owl-nav button']['width'] = ( !empty($arrowBtnWidth) ) ? $arrowBtnWidth[1].$unit.' !important' : '40'.$unit.' !important';
			$t_selectors[$slider_class.' .owl-nav button']['height'] = ( !empty($arrowBtnHeight) ) ? $arrowBtnHeight[1].$unit.' !important' : '40'.$unit.' !important';
			$t_selectors[$slider_class.' .owl-nav button']['border-width'] = ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[1].$unit.' !important' : 0;
			$t_selectors[$slider_class.' .owl-nav button']['position'] = 'relative !important';

			$t_selectors[$slider_class.' .owl-nav button.owl-prev']['padding'] = (!empty($arrowBtnPadding) ? ($arrowBtnPadding[1][0].$unit.' '.$arrowBtnPadding[1][1].$unit.' '.$arrowBtnPadding[1][2].$unit.' '.$arrowBtnPadding[1][3].$unit).' !important' : '');
			$t_selectors[$slider_class.' .owl-nav button.owl-prev']['left'] = ( !empty($owlNavLeft) ) ? $owlNavLeft[1].'% !important' : 0;
			$t_selectors[$slider_class.' .owl-nav button.owl-next']['padding'] = (!empty($arrowBtnPadding) ? ($arrowBtnPadding[1][0].$unit.' '.$arrowBtnPadding[1][1].$unit.' '.$arrowBtnPadding[1][2].$unit.' '.$arrowBtnPadding[1][3].$unit).' !important' : '');
			$t_selectors[$slider_class.' .owl-nav button.owl-next']['right'] = ( !empty($owlNavRight) ) ? $owlNavRight[1].'% !important' : 0;

			$t_selectors[$slider_class.' .owl-nav button i']['font-size'] = (!empty($navArrowSize) ? $navArrowSize[1].$unit : '20'.$unit ).' !important';
			$t_selectors[$slider_class.' .owl-dots .owl-dot span']['border-width'] = ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[1].$unit.' !important' : 0;

			$d_selectors[$slider_class.' .owl-nav']['max-width'] = ( !empty($owlNavMaxWidth) ) ? $owlNavMaxWidth[0].'% !important' : '100% !important' ;
			$d_selectors[$slider_class.' .owl-nav']['top'] = ( !empty($owlNavTop) ) ? $owlNavTop[0].'% !important' : '35% !important' ;

			$d_selectors[$slider_class.' .owl-nav button']['width'] = ( !empty($arrowBtnWidth) ) ? $arrowBtnWidth[0].$unit.' !important' : '40'.$unit.' !important' ;
			$d_selectors[$slider_class.' .owl-nav button']['height'] = ( !empty($arrowBtnHeight) ) ? $arrowBtnHeight[0].$unit.' !important' : '40'.$unit.' !important' ;
			$d_selectors[$slider_class.' .owl-nav button']['border-width'] = ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[0].$unit.' !important' : 0 ;
			$d_selectors[$slider_class.' .owl-nav button']['position'] = 'relative !important' ;

			$d_selectors[$slider_class.' .owl-nav button.owl-prev']['padding'] = (!empty($arrowBtnPadding) ? ($arrowBtnPadding[0][0].$unit.' '.$arrowBtnPadding[0][1].$unit.' '.$arrowBtnPadding[0][2].$unit.' '.$arrowBtnPadding[0][3].$unit).' !important' : '') ;
			$d_selectors[$slider_class.' .owl-nav button.owl-prev']['left'] = ( !empty($owlNavLeft) ) ? $owlNavLeft[0].'% !important' : 0 ;

			$d_selectors[$slider_class.' .owl-nav button.owl-next']['padding'] = (!empty($arrowBtnPadding) ? ($arrowBtnPadding[0][0].$unit.' '.$arrowBtnPadding[0][1].$unit.' '.$arrowBtnPadding[0][2].$unit.' '.$arrowBtnPadding[0][3].$unit).' !important' : '') ;
			$d_selectors[$slider_class.' .owl-nav button.owl-next']['right'] = ( !empty($owlNavRight) ) ? $owlNavRight[0].'% !important' : 0 ;

			$d_selectors[$slider_class.' .owl-nav button i']['font-size'] = (!empty($navArrowSize) ? $navArrowSize[0].$unit : '20'.$unit ).' !important' ;
			$d_selectors[$slider_class.' .owl-dots .owl-dot span']['border-width'] = ( !empty($navArrowBdWidth) ) ? $navArrowBdWidth[0].$unit.' !important' : 0 ;

			/*$selectors = array(
				$slider_class.'.ive-carousel-wrap' => array(
					'max-width'				=> isset($attr['maxWidth']) ? $attr['maxWidth'].$unit : 'none',
				)
			);*/

		  $combined_selectors = array(
		    'desktop' 			=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  			=> $t_selectors,
		    'mobile'  			=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.ive-tabs-id' . $attr['uniqueID'] );

		}

		public static function get_accordion_css( $attr, $id ) {
		  $defaults = IVE_Helper::$block_list['ive/accordion']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$d_selectors[' .ive-accordions-xl-start']['justify-content']	= 'left !important';
			$d_selectors[' .ive-accordions-xl-center']['justify-content']= 'center !important';
		  $d_selectors[' .ive-accordions-xl-end']['justify-content']	= 'right !important';

			$maxWidth		= isset($attr['maxWidth']) ? $attr['maxWidth'] : 'none';
			$color		= isset($attr['titleStyles'][0]['color']) ? $attr['titleStyles'][0]['color'] : '';
			$border		= isset($attr['titleStyles'][0]['border']) ? $attr['titleStyles'][0]['border'] : '';
			$background		= isset($attr['titleStyles'][0]['background']) ? $attr['titleStyles'][0]['background'] : '';
			$gradType		= isset($attr['gradType']) ? $attr['gradType'] : '';
			$gradRadPos		= isset($attr['gradRadPos']) ? $attr['gradRadPos'] : '';
			$gradFirstColor		= isset($attr['gradFirstColor']) ? $attr['gradFirstColor'] : '';
			$gradFirstLoc		= isset($attr['gradFirstLoc']) ? $attr['gradFirstLoc'] .'%' : '';
			$gradSecondColor		= isset($attr['gradSecondColor']) ? $attr['gradSecondColor'] : '';
			$gradSecondLoc		= isset($attr['gradSecondLoc']) ? $attr['gradSecondLoc']. '%' : '';
			$gradAngle		= isset($attr['gradAngle']) ? $attr['gradAngle'].'deg' : '';
			$padding		= isset($attr['titleStyles'][0]['padding']) ? $attr['titleStyles'][0]['padding'].'px !important' : '';
			$marginTop		= isset($attr['titleStyles'][0]['marginTop']) ? $attr['titleStyles'][0]['marginTop'].'px' : '';
			$borderWidth		= isset($attr['titleStyles'][0]['borderWidth']) ? $attr['titleStyles'][0]['borderWidth'] .'px' : '';
			$borderRadius		= isset($attr['titleStyles'][0]['borderRadius']) ? $attr['titleStyles'][0]['borderRadius'].'px' : '';
			$sizeType		= isset($attr['titleStyles'][0]['sizeType']) ? $attr['titleStyles'][0]['sizeType'] : '';
			$sizedesk		= isset($attr['titleStyles'][0]['size'][0]) ? $attr['titleStyles'][0]['size'][0] : '';
			$sizetab		= isset($attr['titleStyles'][0]['size'][1]) ? $attr['titleStyles'][0]['size'][1] : '';
		  	$sizemob		= isset($attr['titleStyles'][0]['size'][2]) ? $attr['titleStyles'][0]['size'][2] : '';
			$lineType		= isset($attr['titleStyles'][0]['lineType']) ? $attr['titleStyles'][0]['lineType'] : '';
			$lineHeightdesk		= isset($attr['titleStyles'][0]['lineHeight'][0]) ? $attr['titleStyles'][0]['lineHeight'][0] : '';
			$lineHeighttab		= isset($attr['titleStyles'][0]['lineHeight'][1]) ? $attr['titleStyles'][0]['lineHeight'][1] : '';
		  	$lineHeightmob		= isset($attr['titleStyles'][0]['lineHeight'][2]) ? $attr['titleStyles'][0]['lineHeight'][2] : '';
			$letterSpacing		= isset($attr['titleStyles'][0]['letterSpacing']) ? $attr['titleStyles'][0]['letterSpacing'] .'px' : '';
			$textTransform		= isset($attr['titleStyles'][0]['textTransform']) ? $attr['titleStyles'][0]['textTransform'] : '';
			$family		= isset($attr['titleStyles'][0]['family']) ? $attr['titleStyles'][0]['family']  : '';
			$style		= isset($attr['titleStyles'][0]['style']) ? $attr['titleStyles'][0]['style'] : '';
			$weight		= isset($attr['titleStyles'][0]['weight']) ? $attr['titleStyles'][0]['weight'] : '';

			$contentPadding		= isset($attr['contentPadding']) ? $attr['contentPadding'] .'px' : '';
			$contentBgColor		= isset($attr['contentBgColor']) ? $attr['contentBgColor'] : '';
			$contentBorderColor		= isset($attr['contentBorderColor']) ? $attr['contentBorderColor'] : '';
	 		$contentBorder		= isset($attr['contentBorder']) ? $attr['contentBorder'] .'px' : '';
			$contentBorderRadius		= isset($attr['contentBorderRadius']) ? $attr['contentBorderRadius'] .'px' : '0 px';
			$minHeight		= isset($attr['minHeight']) ? $attr['minHeight'] .'px' : '0';

			$colorHover		= isset($attr['titleStyles'][0]['colorHover']) ? $attr['titleStyles'][0]['colorHover'] .' !important' : '';
			$borderHover		= isset($attr['titleStyles'][0]['borderHover']) ? $attr['titleStyles'][0]['borderHover'] .' !important' : '';
			$backgroundHover		= isset($attr['titleStyles'][0]['backgroundHover']) ? $attr['titleStyles'][0]['backgroundHover'] .' ' : '';

			$hovGradFirstColor		= isset($attr['hovGradFirstColor']) ? $attr['hovGradFirstColor'] : '';
			$hovGradSecondColor		= isset($attr['hovGradSecondColor']) ? $attr['hovGradSecondColor'] : '';



			$colorActive		= isset($attr['titleStyles'][0]['colorActive']) ? $attr['titleStyles'][0]['colorActive'] .' !important' : '';
			$borderActive		= isset($attr['titleStyles'][0]['borderActive']) ? $attr['titleStyles'][0]['borderActive'] .' !important' : '';
			$backgroundActive		= isset($attr['titleStyles'][0]['backgroundActive']) ? $attr['titleStyles'][0]['backgroundActive'] : '';
			$actvGradFirstColor		= isset($attr['actvGradFirstColor']) ? $attr['actvGradFirstColor'] : '';
			$actvGradSecondColor		= isset($attr['actvGradSecondColor']) ? $attr['actvGradSecondColor'] : '';
			$openPane		= isset($attr['openPane']) ? $attr['openPane'] : '';

			$openPane1 = $openPane + 1 ;
			$accordianwrap = '.ive-accordion-wrap';
			$accordianHeaderClass = ' .ive-blocks-accordion-header';
			$accordianHeadertitleClass = ' .ive-blocks-accordion-header .ive-blocks-accordion-title';
			$accordianActiveHeaderClass = ' .ive-accordion-pane .ive-blocks-accordion-header.ive-accordion-panel-active';
			$accordianHeaderHoverClass = ' .ive-blocks-accordion-header:hover';

			$selectors	=	array(
				' .ive-blocks-accordion-header .ive-blocks-accordion-icon-trigger:before'						=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconColor'] . ' !important' : '#555555'
				),
				' .ive-blocks-accordion-header .ive-blocks-accordion-icon-trigger:after'						=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconColor'] . ' !important' : '#555555'
				),

				' .ive-blocks-accordion-header:hover .ive-blocks-accordion-icon-trigger:before'			=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconHoverColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconHoverColor'] . ' !important' : '#444444'
				),
				' .ive-blocks-accordion-header:hover .ive-blocks-accordion-icon-trigger:after'				=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconHoverColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconHoverColor'] . ' !important' : '#444444'
				),

				' .ive-accordion-panel-active .ive-blocks-accordion-icon-trigger:before'	=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconActiveColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconActiveColor'] . ' !important' : '#ffffff'
				),
				' .ive-accordion-panel-active .ive-blocks-accordion-icon-trigger:after'	=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconActiveColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconActiveColor'] . ' !important' : '#ffffff'
				),


				' .ive-blocks-accordion-header .ive-blocks-accordion-icon-trigger'	=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconBgColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconBgColor'] . ' !important' : '#f2f2f2'
				),

				' .ive-blocks-accordion-header:hover .ive-blocks-accordion-icon-trigger'	=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconBgHoverColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconBgHoverColor'] . ' !important' : '#eeeeee'
				),
				' .ive-accordion-panel-active .ive-blocks-accordion-icon-trigger'	=>	array(
					'background-color'	=>	isset( $attr['titleStyles'][0]['titleTriggerIconActiveBgColor'] ) ? $attr['titleStyles'][0]['titleTriggerIconActiveBgColor'] . ' !important' : '#444444'
				)
			);

			if($maxWidth=='') {
				$selectors[$accordianwrap]['max-width']= 'none';
			} else {
				$selectors[$accordianwrap]['max-width']= $maxWidth .'px';
			}
			$selectors[$accordianHeaderClass]['color']					= $color . ' !important';
			$selectors[$accordianHeaderClass]['border-color']		= $border;
			$selectors[$accordianHeaderClass]['padding']				= $padding;
			$selectors[$accordianHeaderClass]['margin-top']			= $marginTop;
			$selectors[$accordianHeaderClass]['border-width']		= $borderWidth;
			$selectors[$accordianHeaderClass]['border-radius']	= $borderRadius;
			$selectors[$accordianHeaderClass]['letter-spacing']	= $letterSpacing;
			$selectors[$accordianHeaderClass]['text-transform']	= $textTransform;
			$selectors[$accordianHeaderClass]['font-family']		= $family;
			$selectors[$accordianHeaderClass]['font-style']			= $style;
			$selectors[$accordianHeaderClass]['font-weight']		= $weight;

			$d_selectors[$accordianHeaderClass]['font-size']= $sizedesk.$sizeType .' !important';
			$d_selectors[$accordianHeaderClass]['line-height']= $lineHeightdesk.$lineType;

			$t_selectors[$accordianHeaderClass]['font-size']= $sizetab.$sizeType .' !important';
			$t_selectors[$accordianHeaderClass]['line-height']= $lineHeighttab.$lineType;

			$m_selectors[$accordianHeaderClass]['font-size']= $sizemob.$sizeType .' !important';
			$m_selectors[$accordianHeaderClass]['line-height']= $lineHeightmob.$lineType;



			$accordians10Class = '.ive-start-active-pane-'.$openPane1.' .ive-accordion-pane-'.$openPane1.' .ive-blocks-accordion-header.ive-accordion-panel-active';
			$selectors[$accordians10Class]['color']= $colorActive;
			$selectors[$accordians10Class]['border-color']= $borderActive;


			if( !$attr['iconGrad'] ) {
				$selectors[$accordianHeaderClass]['background-color'] 			= $background . ' !important';
				$selectors[$accordianHeaderHoverClass]['background-color']	= $backgroundHover . ' !important';
				$selectors[$accordianActiveHeaderClass]['background-color']	= $backgroundActive;
				// $selectors[$accordians10Class]['background-color']					= $backgroundActive;
			}
			if( $attr['iconGrad'] ){
				if($gradType === 'radial'){
					$gradient = ' radial-gradient(at '.$gradRadPos.', '. $gradFirstColor.' '. $gradFirstLoc .' , '. $gradSecondColor .' '. $gradSecondLoc .' ) !important' ;
					$gradientHover = ' radial-gradient(at '.$gradRadPos.', '. $hovGradFirstColor.' '. $gradFirstLoc .' , '. $hovGradSecondColor .' '. $gradSecondLoc .' ) !important' ;
					$gradientActive = ' radial-gradient(at '.$gradRadPos.', '. $actvGradFirstColor.' '. $gradFirstLoc .' , '. $actvGradSecondColor .' '. $gradSecondLoc .' ) !important' ;
				}else{
					$gradient = ' linear-gradient('.$gradAngle.', '. $gradFirstColor.' '. $gradFirstLoc .', '. $gradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
					$gradientHover = ' linear-gradient('.$gradAngle.', '. $hovGradFirstColor.' '. $gradFirstLoc .', '. $hovGradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
					$gradientActive = ' linear-gradient('.$gradAngle.', '. $actvGradFirstColor.' '. $gradFirstLoc .', '. $actvGradSecondColor . ' '. $gradSecondLoc .' ) !important' ;
				}
				$selectors[$accordianHeaderClass]['background-image']= $gradient;
				$selectors[$accordianHeaderClass.':hover']['background-image']= $gradientHover;
				$selectors[$accordianHeaderHoverClass]['background-image']= $gradientHover;
				$selectors[$accordians10Class]['background-image']= $gradientActive;
				$selectors[$accordianActiveHeaderClass]['background-image']= $gradientActive;
			}

			$d_selectors[$accordianHeadertitleClass]['line-height']= $lineHeightdesk.$lineType;
			$t_selectors[$accordianHeadertitleClass]['line-height']= $lineHeighttab.$lineType;
			$m_selectors[$accordianHeadertitleClass]['line-height']= $lineHeightmob.$lineType;

		  $accordianContentClass = ' .ive-accordion-panel-inner';
			$selectors[$accordianContentClass]['padding']= $contentPadding;
			$selectors[$accordianContentClass]['background-color']= $contentBgColor;
			$selectors[$accordianContentClass]['border-color']= $contentBorderColor;
			$selectors[$accordianContentClass]['border-width']= $contentBorder;
			$selectors[$accordianContentClass]['border-radius']= $contentBorderRadius;
			$selectors[$accordianContentClass]['min-height']= $minHeight;

			$selectors[$accordianHeaderClass.':hover']['color']= $colorHover;
			$selectors[$accordianHeaderClass.':hover']['border-color']= $borderHover;

			$selectors[$accordianActiveHeaderClass]['color']= $colorActive;
			$selectors[$accordianActiveHeaderClass]['border-color']= $borderActive;



		  $combined_selectors = array(
		    'desktop' 			=>	$selectors,
		    'desktop_media'	=>	$d_selectors,
		    'tablet'  			=>	$t_selectors,
		    'mobile'  			=>	$m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.ive-accordion-' . $attr['uniqueID'] );

		}

		public static function get_accordion_pane_css($attr, $id){
		  $defaults = IVE_Helper::$block_list['ive/pane']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$iconfontSizedesk = isset($attr['iconfontSize'][0]) ? $attr['iconfontSize'][0] : '12' ;
			$iconfontSizetab = isset($attr['iconfontSize'][1]) ? $attr['iconfontSize'][1] : '12' ;
			$iconfontSizemob = isset($attr['iconfontSize'][2]) ? $attr['iconfontSize'][2] : '12' ;

			$iveiconSVGClass = '.ive-btn-svg-icon svg';

			$d_selectors[$iveiconSVGClass]['width']= $iconfontSizedesk.'px' ;
			$d_selectors[$iveiconSVGClass]['height']= $iconfontSizedesk.'px' ;
			$t_selectors[$iveiconSVGClass]['width']= $iconfontSizetab.'px' ;
			$t_selectors[$iveiconSVGClass]['height']= $iconfontSizetab.'px' ;
			$m_selectors[$iveiconSVGClass]['width']= $iconfontSizemob.'px' ;
			$m_selectors[$iveiconSVGClass]['height']= $iconfontSizemob.'px' ;

		  $combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.pane-svg-' . $attr['uniqueID'] );

		}
		public static function get_form_checkbox_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-checkbox']['attributes'];
		  $attr = array_merge( $defaults, $attr );


		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );

		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_checkbox' . $attr['uniqueID'] );
		}

		public static function get_form_date_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-date']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
			
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_date' . $attr['uniqueID'] );
		}

		public static function get_form_email_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-email']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_Email' . $attr['uniqueID'] );
		}

		public static function get_form_name_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-name']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_name' . $attr['uniqueID'] );
		}

		public static function get_form_number_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-number']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_number' . $attr['uniqueID'] );
		}

		public static function get_form_phone_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-phone']['attributes'];
		  $attr = array_merge( $defaults, $attr );

			  $t_selectors = array();
			  $m_selectors = array();
			  $d_selectors = array();
			  $selectors   = array();

				$unit = 'px';

				$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																				.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																				.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																				.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

				$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																			 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																			 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																			 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

				$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																			 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																			 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																			 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

				$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
				$frameNormalboxshadx = $attr['frameNormalboxshadx'];
				$frameNormalboxshady = $attr['frameNormalboxshady'];
				$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
				$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
				$spacingMargin = $attr['spacingMargin'];
				$spacingPadding = $attr['spacingPadding'];

				//hover
				$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																							.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																							.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																							.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

				$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																						 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																						 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																						 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

				$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																						 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																						 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																						 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

				$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
				$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
				$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
				$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
				$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

				$d_selectors = array(
					'' => array(
						'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
						'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
						'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
						'border-radius' => $deskBorderRadius,
						'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
						'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
						'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
						'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
					),
					':hover' => array(
						'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
						'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
						'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
						'border-radius' => $deskHovBorderRadius,
						'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
					)
				);

				$t_selectors = array(
					'' => array(
						'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
						'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
						'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
						'border-radius' => $tabBorderRadius,
						'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
						'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
						'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
						'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
					),
					':hover' => array(
						'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
						'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
						'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
						'border-radius' => $tabHovBorderRadius,
						'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
					)
				);

				$m_selectors = array(
					'' => array(
						'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
						'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
						'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
						'border-radius' => $mobBorderRadius,
						'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
						'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
						'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
						'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
					),
					':hover' => array(
						'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
						'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
						'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
						'border-radius' => $mobHovBorderRadius,
						'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
					)
				);
				$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

				$combined_selectors = array(
			    'desktop' 		=> $selectors,
			    'desktop_media'	=> $d_selectors,
			    'tablet'  		=> $t_selectors,
			    'mobile'  		=> $m_selectors,
			  );
			  return IVE_Helper::generate_all_css( $combined_selectors, '.form_phone' . $attr['uniqueID'] );
		}

		public static function get_form_radio_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-radio']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_radio' . $attr['uniqueID'] );
		}

		public static function get_form_select_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-select']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_select' . $attr['uniqueID'] );
		}

		public static function get_form_text_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-text']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_text' . $attr['uniqueID'] );
		}

		public static function get_form_textarea_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-textarea']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_textarea' . $attr['uniqueID'] );
		}

		public static function get_form_url_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/form-field-url']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';

			$deskBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][0][0].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][1].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][2].$unit.' '
																																			.$attr['frameNormalBorderRadius'][0][3].$unit : 0;

			$tabBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][1][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][1][3].$unit : 0;

			$mobBorderRadius = (!empty($attr['frameNormalBorderRadius'])) ? $attr['frameNormalBorderRadius'][2][0].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][1].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][2].$unit.' '
																																		 .$attr['frameNormalBorderRadius'][2][3].$unit : 0;

			$frameNormalboxshadcolor = $attr['frameNormalboxshadcolor'];
			$frameNormalboxshadx = $attr['frameNormalboxshadx'];
			$frameNormalboxshady = $attr['frameNormalboxshady'];
			$frameNormalboxshadblur = $attr['frameNormalboxshadblur'];
			$frameNormalboxshadspread = $attr['frameNormalboxshadspread'];
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];

			//hover
			$deskHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][0][0].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][1].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][2].$unit.' '
																																						.$attr['frameNormalHovBorderRadius'][0][3].$unit : 0;

			$tabHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][1][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][1][3].$unit : 0;

			$mobHovBorderRadius = (!empty($attr['frameNormalHovBorderRadius'])) ? $attr['frameNormalHovBorderRadius'][2][0].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][1].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][2].$unit.' '
																																					 .$attr['frameNormalHovBorderRadius'][2][3].$unit : 0;

			$frameNormalHovboxshadcolor = $attr['frameNormalHovboxshadcolor'];
			$frameNormalHovboxshadx = $attr['frameNormalHovboxshadx'];
			$frameNormalHovboxshady = $attr['frameNormalHovboxshady'];
			$frameNormalHovboxshadblur = $attr['frameNormalHovboxshadblur'];
			$frameNormalHovboxshadspread = $attr['frameNormalHovboxshadspread'];

			$d_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][0] : 'transparent'),
					'border-radius' => $deskBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[0].' '.$frameNormalboxshadx[0].$unit.' '.$frameNormalboxshady[0].$unit.' '.$frameNormalboxshadblur[0].$unit.' '.$frameNormalboxshadspread[0].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][0] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][0].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][0] : 'transparent'),
					'border-radius' => $deskHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[0].' '.$frameNormalHovboxshadx[0].$unit.' '.$frameNormalHovboxshady[0].$unit.' '.$frameNormalHovboxshadblur[0].$unit.' '.$frameNormalHovboxshadspread[0].$unit,
				)
			);

			$t_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][1] : 'transparent'),
					'border-radius' => $tabBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[1].' '.$frameNormalboxshadx[1].$unit.' '.$frameNormalboxshady[1].$unit.' '.$frameNormalboxshadblur[1].$unit.' '.$frameNormalboxshadspread[1].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][1] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][1].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][1] : 'transparent'),
					'border-radius' => $tabHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[1].' '.$frameNormalHovboxshadx[1].$unit.' '.$frameNormalHovboxshady[1].$unit.' '.$frameNormalHovboxshadblur[1].$unit.' '.$frameNormalHovboxshadspread[1].$unit,
				)
			);

			$m_selectors = array(
				'' => array(
					'border-style' => (!empty($attr['frameNormalBorderStyle']) ? $attr['frameNormalBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalBorderWidth']) ? $attr['frameNormalBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalBorderColor']) ? $attr['frameNormalBorderColor'][2] : 'transparent'),
					'border-radius' => $mobBorderRadius,
					'box-shadow' => $frameNormalboxshadcolor[2].' '.$frameNormalboxshadx[2].$unit.' '.$frameNormalboxshady[2].$unit.' '.$frameNormalboxshadblur[2].$unit.' '.$frameNormalboxshadspread[2].$unit,
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'block' : 'none',
				),
				':hover' => array(
					'border-style' => (!empty($attr['frameNormalHovBorderStyle']) ? $attr['frameNormalHovBorderStyle'][2] : 'none'),
					'border-width' => (!empty($attr['frameNormalHovBorderWidth']) ? $attr['frameNormalHovBorderWidth'][2].'px' : '0'),
					'border-color' => (!empty($attr['frameNormalHovBorderColor']) ? $attr['frameNormalHovBorderColor'][2] : 'transparent'),
					'border-radius' => $mobHovBorderRadius,
					'box-shadow' => $frameNormalHovboxshadcolor[2].' '.$frameNormalHovboxshadx[2].$unit.' '.$frameNormalHovboxshady[2].$unit.' '.$frameNormalHovboxshadblur[2].$unit.' '.$frameNormalHovboxshadspread[2].$unit,
				)
			);
			$selectors[' .ive-form-hidden-label']['display'] = (isset($attr['hideLabel']) && $attr['hideLabel']) ? 'none' : 'block' ;

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.form_url' . $attr['uniqueID'] );
		}

		public static function get_form_button_css($attr, $id) {
			$defaults = IVE_Helper::$block_list['ive/button-single']['attributes'];
		  $attr = array_merge( $defaults, $attr );

		  $t_selectors = array();
		  $m_selectors = array();
		  $d_selectors = array();
		  $selectors   = array();

			$unit = 'px';
			$spacingMargin = $attr['spacingMargin'];
			$spacingPadding = $attr['spacingPadding'];
			$frameNormalboxshadcolor = (!empty($attr['focusOutlineColor']) ? $attr['focusOutlineColor'] : 'transparent') ;
			$focusOutlineWeight = (!empty($attr['focusOutlineWeight']) ? $attr['focusOutlineWeight'].$unit : '0');

			$selectors = array(
				'' => array(
					'border-style' => 'solid',
					'border-width' => (!empty($attr['borderWeight']) ? $attr['borderWeight'].'px' : '0'),
					'background-color' => (!empty($attr['color']) ? $attr['color'] : 'transparent'),
					'color' => (!empty($attr['textColor']) ? $attr['textColor'] : '#000'),
					'border-color' => (!empty($attr['borderColor']) ? $attr['borderColor'] : 'transparent'),
					'border-radius' => (!empty($attr['borderRadius']) ? $attr['borderRadius'].$unit : '0'),
				),
				':hover' => array(
					'background-color' => (!empty($attr['hoverColor']) ? $attr['hoverColor'] : 'transparent'),
					'color' => (!empty($attr['hoverTextColor']) ? $attr['hoverTextColor'] : '#000'),
					'border-color' => (!empty($attr['hoverBorderColor']) ? $attr['hoverBorderColor'] : 'transparent'),
				),
				':focus' => array(
					'box-shadow' => $frameNormalboxshadcolor.' 0px 0px 1px '.$focusOutlineWeight,
				)
			);

			$d_selectors = array(
				'' => array(
					'margin' => (!empty($spacingMargin) ? $spacingMargin[0][0].$unit.' '.$spacingMargin[0][1].$unit.' '.$spacingMargin[0][2].$unit.' '.$spacingMargin[0][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[0][0].$unit.' '.$spacingPadding[0][1].$unit.' '.$spacingPadding[0][2].$unit.' '.$spacingPadding[0][3].$unit : 0),
					'display'				=> ($attr['displayFields'][0] && $attr['displayFields'][0]=='true') ? 'inline-flex' : 'none',
				),
			);

			$t_selectors = array(
				'' => array(
					'margin' => (!empty($spacingMargin) ? $spacingMargin[1][0].$unit.' '.$spacingMargin[1][1].$unit.' '.$spacingMargin[1][2].$unit.' '.$spacingMargin[1][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[1][0].$unit.' '.$spacingPadding[1][1].$unit.' '.$spacingPadding[1][2].$unit.' '.$spacingPadding[1][3].$unit : 0),
					'display'				=> ($attr['displayFields'][1] && $attr['displayFields'][1]=='true') ? 'inline-flex' : 'none',
				),
			);

			$m_selectors = array(
				'' => array(
					'margin' => (!empty($spacingMargin) ? $spacingMargin[2][0].$unit.' '.$spacingMargin[2][1].$unit.' '.$spacingMargin[2][2].$unit.' '.$spacingMargin[2][3].$unit : 0),
					'padding' => (!empty($spacingPadding) ? $spacingPadding[2][0].$unit.' '.$spacingPadding[2][1].$unit.' '.$spacingPadding[2][2].$unit.' '.$spacingPadding[2][3].$unit : 0),
					'display'				=> ($attr['displayFields'][2] && $attr['displayFields'][2]=='true') ? 'inline-flex' : 'none',
				),
			);

			$combined_selectors = array(
		    'desktop' 		=> $selectors,
		    'desktop_media'	=> $d_selectors,
		    'tablet'  		=> $t_selectors,
		    'mobile'  		=> $m_selectors,
		  );
		  return IVE_Helper::generate_all_css( $combined_selectors, '.ive-button-' . $attr['uniqueID'] );
		}

	}
}
