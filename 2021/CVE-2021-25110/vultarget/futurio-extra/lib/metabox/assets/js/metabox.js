/**
|| --------------------------------------------------------------------------------------------
|| Metabox JS
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Metabox
|| @since		Dilaz Metabox 1.0
|| @author		WebDilaz Team, http://webdilaz.com, http://themedilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
*/

var DilazMetaboxScript = new function() {

	"use strict";
	
	/**
	 * Global Variables
	 */
	var $t = this;
	var $ = jQuery.noConflict();
	var $doc = $(document);
	
	/**
	 * DoWhen start
	 */
	$t.doWhen = function() {
		$doc.doWhen();
	}
	
	/**
	 * Tabs Content min-Height
	 */
	$t.tabMinHeight = function() {
		$(window).load(function() {
			$('.dilaz-metabox').each(function() {
				
				var $this      = $(this),
					$navHeight = $this.find('.dilaz-mb-tabs-nav').height(),
					$content   = $this.find('.dilaz-mb-tabs-content');
					
				$content.css({'min-height':$navHeight+20});
			});
		});
	}
	
	/**
	 * Tabs
	 */
	$t.tabs = function() {
		
		var dilazMetabox = $('.dilaz-metabox');
		
		dilazMetabox.closest('.postbox').addClass('dilaz-mb-wrapper');
		
		if (dilazMetabox.hasClass('dilaz-mb-wp5')) {
			dilazMetabox.closest('.postbox').addClass('dilaz-mb-wp5-wrapper');
		}
		
		dilazMetabox.find('.dilaz-mb-tabs-nav-item:first-of-type, .dilaz-meta-tab:first-of-type').addClass('active');
		
		$('.dilaz-mb-tabs').on('click', '.dilaz-mb-tabs-nav-item', function() {
			
			var tabNav     = $(this),
				tabContent = tabNav.closest('.dilaz-mb-tabs').siblings().children().eq(tabNav.index());
				
			tabNav.addClass('active').siblings().removeClass('active');
			tabContent.addClass('active').siblings().removeClass('active');		
		});
	}
	
	/**
	 * Hidden field
	 */
	$t.hiddenField = function() {
		$('div[data-dilaz-hidden="yes"]').hide();
	}
	
	/**
	 * Checkbox field
	 */
	$t.checkboxField = function() {
		$('.dilaz-mb-tabs-content').on('click', '.dilaz-mb-checkbox', function() {
			$(this).toggleClass('focus');
		});
	}
	
	/**
	 * Radio field
	 */
	$t.radioField = function() {
		$('.dilaz-mb-tabs-content').on('click', '.dilaz-mb-radio', function() {
			
			var $this = $(this);
			
			$this.addClass('focus');
			$this.parent().siblings().find('.dilaz-mb-radio').removeClass('focus');
		});
	}
	
	/**
	 * Switch and buttonset fields
	 */
	$t.switchAndButtonset = function() {
		$('.dilaz-mb-tabs-content').on('click', '.dilaz-mb-switch, .dilaz-mb-button-set', function() {
			
			var $this = $(this);
			
			$this.parent().addClass('selected');
			$this.parent().siblings().removeClass('selected');
		});
	}
	
	/**
	 * UI slider field
	 */
	$t.uiSliderField = function() {
		$('.dilaz-mb-slider').each(function() {
			
			var $this = $(this),
				$min  = parseInt($this.data('min')),
				$max  = parseInt($this.data('max')),
				$step = parseInt($this.data('step')),
				$val  = parseInt($this.data('val'));
				
			$this.slider({
				animate : true,
				range   : 'min',
				value   : $val,
				min     : $min,
				max     : $max,
				step    : $step,
				slide   : function(event, ui) {
					$this.next($val).find('span').text(ui.value);
					$this.siblings('input').val(ui.value);
				},
				change  : function(event, ui) {
					$this.next($val).find('span').text(ui.value);
					$this.siblings('input').val( ui.value);
				}
			});
		});
	}
	
	/**
	 * UI range field
	 */
	$t.uiRangeField = function() {
		$('.dilaz-mb-range').each(function() {
			
			var $this      = $(this),
				$minVal    = parseInt($this.data('min-val')),
				$maxVal    = parseInt($this.data('max-val')),
				$min       = parseInt($this.data('min')),
				$max       = parseInt($this.data('max')),
				$step      = parseInt($this.data('step')),
				$range     = $this.find('.dilaz-mb-slider-range'),
				$optMin    = $this.find('#option-min'),
				$optMinVal = $optMin.val(),
				$optMax    = $this.find('#option-max'),
				$optMaxVal = $optMax.val();
				
			$range.slider({
				range  : true,
				min    : $min,
				max    : $max,
				step   : $step,
				values : [$minVal, $maxVal],
				slide  : function(event, ui) {
					$optMin.val(ui.values[0]);
					$optMin.next('.dilaz-mb-min-val').find('.val').text(ui.values[0]);
					$optMax.val(ui.values[1]);
					$optMax.next('.dilaz-mb-max-val').find('.val').text(ui.values[1]);
				}
			});
		});
	}
	
	/**
	 * File upload field
	 */
	$t.fileUploadField = function() {
		$('.dilaz-mb-file-upload-button').each(function() {
			
			var imageFrame;
			
			$(this).on('click', function(event) {
				
				event.preventDefault();
				
				var options, attachment;
				
				var $self              = $(event.target),
					$fileUpload        = $self.closest('.dilaz-mb-file-upload'),
					$fileWrapper       = $fileUpload.find('.dilaz-mb-file-wrapper'),
					$fileWrapperParent = $fileUpload.parent(),
					$fileId            = $fileWrapper.data('file-id') || '',
					$fileLibrary       = $self.data('file-library') || '',
					$fileFormat        = $self.data('file-format') || '',
					$fileMime          = $self.data('file-mime') || '',
					$fileSpecific      = $self.data('file-specific') || false,
					$fileMultiple      = $self.data('file-multiple') || false,
					$fileType          = $self.data('file-type') || '',
					$frameTitle        = $self.data('frame-title') || '',
					$frameButtonText   = $self.data('frame-button-text') || '',
					$mediaPreview      = $fileWrapperParent.find('.dilaz-mb-media-file');
				
				/* Restricts media uploaded to current postID only */
				var $uploadedTo = ($fileSpecific == true) ? wp.media.view.settings.post.id : '';
				
				/* open frame if it exists */
				if ( imageFrame ) {
					imageFrame.open();
					return;
				}
				
				/* frame settings */
				imageFrame = wp.media({
					title    : $frameTitle,
					multiple : $fileMultiple,
					library  : {	
						type       : $fileType,
						uploadedTo : $uploadedTo 
					},
					button : {
						text : $frameButtonText
					}
				});
				
				/* frame select handler */
				imageFrame.on( 'select', function() {
					
					var selection = imageFrame.state().get('selection');
					
					if (!selection)
						return;
					
					/* loop through the selected files */
					selection.each( function(attachment) {
						
						var type = attachment.attributes.type;
						
						if (type == 'image') {
							
							/* if uploaded image is smaller than default thumbnail(250 by 250)
							then get the full image url */
							if (attachment.attributes.sizes.thumbnail !== undefined) {
								var image_src = attachment.attributes.sizes.thumbnail.url;
							} else {
								var image_src = attachment.attributes.url;
							}
						}
						
						/* attachment data */
						var src     = attachment.attributes.url,
							id      = attachment.id,
							title   = attachment.attributes.title,
							caption = attachment.attributes.caption,
							type    = attachment.attributes.type;
							
						$fileWrapper.find('.dilaz_metabox_title_bg_image').val(title);
						$fileWrapper.find('.dilaz_metabox_caption_bg_image').val(caption);
						
						var $fileOutput = '';
						
						$fileOutput += '<div class="dilaz-mb-media-file '+ $fileType +'  '+ (id != '' ? '' : 'empty') +'" id="file-'+ $fileId +'">';
						$fileOutput += '<input type="hidden" name="'+ $fileId +'[]" id="file_'+ $fileId +'" class="dilaz-mb-file-id upload" value="'+ id +'">';
						$fileOutput += '<div class="filename '+ $fileType +'">'+ title +'</div>';
						$fileOutput += '<span class="sort ui-sortable-handle"></span>';
						$fileOutput += '<a href="#" class="dilaz-mb-remove-file" title="Remove"><i class="fa fa-close"></i></a>';
						
						switch ( type ) {
							case 'image':
								$fileOutput += '<img src="'+ image_src +'" class="dilaz-mb-file-preview file-image" alt="">';
								break;
								
							case 'audio':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/audio.png" class="dilaz-mb-file-preview file-audio" alt="">';
								break;
								
							case 'video':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/video.png" class="dilaz-mb-file-preview file-video" alt="">';
								break;
								
							case 'document':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/document.png" class="dilaz-mb-file-preview file-document" alt="">';
								break;
								
							case 'spreadsheet':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/spreadsheet.png" class="dilaz-mb-file-preview file-spreadsheet" alt="">';
								break;
								
							case 'interactive':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/interactive.png" class="dilaz-mb-file-preview file-interactive" alt="">';
								break;
								
							case 'text':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/text.png" class="dilaz-mb-file-preview file-text" alt="">';
								break;
								
							case 'archive':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/archive.png" class="dilaz-mb-file-preview file-archive" alt="">';
								break;
								
							case 'code':
								$fileOutput += '<img src="'+ dilaz_mb_lang.dilaz_mb_images +'media/code.png" class="dilaz-mb-file-preview file-code" alt="">';
								break;
								
						}
						
						$fileOutput += '</div>';
						
						if ($fileMultiple == true) {
							$fileWrapper.append($fileOutput);
						} else {
							$fileWrapper.html($fileOutput);
						}
					});
				});
				
				/* open frame */
				imageFrame.open();
			});
		});
	}
	
	/**
	 * Remove file
	 */
	$t.removeFile = function() {
		$doc.on('click', '.dilaz-mb-remove-file', function(e) {
			
			e.preventDefault();
			
			var $this = $(this);
			
			$this.siblings('input').attr('value', '');
			$this.parent('.dilaz-mb-media-file').slideUp(500);
			
			setTimeout(function() {
				$this.parent('.dilaz-mb-media-file').remove();
			}, 1000);
			
			return false;
		});
	}
	
	/**
	 * File sorting, drag-and-drop
	 */
	$t.fileSorting = function() {
		$('.dilaz-mb-file-wrapper').each(function() {
			
			var $this = $(this),
				$multiple = $this.data('file-multiple');
				
			if ($multiple) {
				$this.sortable({
					opacity : 0.6,
					revert : false,
					handle : '.sort',
					cursor : 'move',
					// axis: 'y',
					placeholder: 'ui-sortable-placeholder'
				});
				$('.dilaz-mb-file-wrapper').disableSelection();
			}
		});
	}
	
	/**
	 * Radio image field
	 */
	$t.radioImageField = function() {
		$('.dilaz-image-selector').click(function() {
			$(this).parent().parent().find('.dilaz-image-selector-img').removeClass('dilaz-image-selector-img-selected');
			$(this).siblings('.dilaz-image-selector-img').addClass('dilaz-image-selector-img-selected');
		});
		
		$('.dilaz-image-selector-img').show();
	}
	
	/**
	 * jQuery add-on for checking existence of multiple classes in an element
	 */
	$t.hasClasses = function() {
		for (var i = 0; i < arguments.length; i++) {
			var classes = arguments[i].split(" ");
			for (var j = 0; j < classes.length; j++) {
				if (this.hasClass(classes[j])) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * post status select
	 * Show/Hide fields for specific post types
	 */
	$t.postStatusSelect = function() {
		$('.dilaz-mb-field').each(function() {
			
			$.fn.hasClasses = function() {
				$t.hasClasses();
			}
			
			var $optField = $(this);
			
			if ($optField.hasClasses('standard aside image gallery link quote status video audio chat')) {
				$optField.css('display', 'none');
			} else {
				$optField.css('display', 'block');
			}
		});
		
		var $postFormatInput = $('#post-formats-select input');
		
		$postFormatInput.change(function() {
			
			var $postFormat        = $(this),
				$postFormatVal     = $postFormat.val(),
				$postFormatOpt     = ($postFormatVal == 0) ? 'standard' : $postFormatVal,
				$mbTabContentField = $('.dilaz-mb-field');
			
			$mbTabContentField.each(function() {
				
				var $optField = $(this);
				
				if ($optField.hasClasses('standard aside image gallery link quote status video audio chat')) {
					if ($optField.hasClass($postFormatOpt)) {
						$optField.css('display', 'block');
					} else {
						$optField.css('display', 'none');
					}
				}
			});
		});
		
		$postFormatInput.each(function() {
			
			var $postFormat    = $(this),
				$postFormatVal = $postFormat.val(),
				$postFormatOpt = ($postFormatVal == 0) ? 'standard' : $postFormatVal;
				
			if ($postFormat.is(':checked')) {
				
				var $mbTabContentField = $('.dilaz-mb-field');
				
				$mbTabContentField.each(function() {
					
					var $optField = $(this);
					
					if ($optField.hasClass($postFormatOpt)) {
						$optField.css('display', 'block');
					} 
				});
			}
		});
	}
	
	/**
	 * jQuery add-on for checking prefixed class in an element
	 */
	$t.hasClassPrefix = function(classPrefix) {
		for (var i = 0; i < this.length; i++) {
			if (('' + $(this[i]).attr('class')).indexOf(classPrefix) != -1)
				return true;
		}
		return false;
	}
	
	/**
	 * page template select
	 * Show/Hide fields for specific page templates
	 */
	$t.pageTemplateSelect = function() {
		
		$('.dilaz-mb-field').each(function() {
			
			$.fn.hasClassPrefix = function(classPrefix) {
				$t.hasClassPrefix(classPrefix);
			}
			
			var $optField = $(this);
			
			if ($optField.hasClassPrefix('page-')) {
				$optField.css('display', 'none');
			} else {
				$optField.css('display', 'block');
			}
		});
		
		var $pageTemplateSelect = $('select#page_template');
		
		$pageTemplateSelect.on('change', function() {
			
			var $pageTemplate      = $(this),
				$pageTemplateVal   = $pageTemplate.val(),
				$pageTemplateOpt   = $pageTemplateVal.slice(0,-4), // remove .php file extension
				$mbTabContentField = $('.dilaz-mb-field');
				
			$mbTabContentField.each(function() {
				
				var $optField = $(this);
				
				if ($optField.hasClassPrefix('page-')) {
					if ($optField.hasClass($pageTemplateOpt)) {
						$optField.css('display', 'block');
					} else {
						$optField.css('display', 'none');
					}
				}
			});
		});
		
		/* automatically show fields for selected page template */
		$pageTemplateSelect.trigger('change');
	}
	
	/**
	 * Repeatable field - sortable
	 */
	$t.repeatableField = function() {
		$('.dilaz-mb-repeatable').sortable({
			opacity: 0.6,
			revert: false,
			handle: '.sort-repeatable',
			cursor: 'move',
			axis: 'y',
			update: function() {
				var i = 0;
				$(this).children().each(function(i) {
					$(this).find('input').attr('name', function(index, name) {
						return name.replace(/\[([^\]])\]/g, function(fullMatch, n) {
							return '['+Number(i)+']';
						});
					});
					i++;
				});
			}
		});
	}
	
	/**
	 * add new repeatable items in the repeatable field
	 */
	$t.addRepeatableField = function() {
		$('.dilaz-mb-add-repeatable-item').on('click', function() {
			var $this     = $(this),
				sorter    = '<span class="sort-repeatable"><i class="dashicons dashicons-move"></i></span>',
				remover   = '<span class="repeatable-remove button"><i class="dashicons dashicons-no-alt"></i></span>',
				rList     = $this.prev('.dilaz-mb-repeatable'),
				sortable  = rList.data('s'),
				nS        = rList.data('ns'),
				removable = rList.data('r'),
				nR        = rList.data('nr'),
				rListItem = rList.find('>li'),
				rClone    = rList.find('>li:last').clone(),
				rItems    = rListItem.length;
				
			rClone.each(function() {
				var $this = $(this);
				
				/* hide so that we can slidedown */
				$this.hide();
				
				/* clear all fields */
				$this.find('input').val('').attr('name', function(index, name) {
					return name.replace(/\[([^\]])\]/g, function(fullMatch, n) {
						return '['+(Number(n) + 1)+']';
					});
				});
				
				/* if items not-sortable is equal to number of shown items */
				if (nS <= rItems) {
					if (!$this.find('.sort-repeatable').length && sortable == true) {
						$this.prepend(sorter);
					}
				}
				
				/* if items not-repeatable is equal to number of shown items */
				if (nR == rItems || nR < 1) {
					if (!$this.find('.repeatable-remove').length && removable == true) {
						$this.append(remover);
					}
				}
			});
			$(rList).append(rClone);
			rClone.slideDown(100);
		});
	}
	
	/**
	 * remove repeatable field
	 */
	$t.removeRepeatableField = function() {
		$doc.on('click', '.repeatable-remove', function(e) {
			e.preventDefault();
			
			var $this = $(this),
				$parent = $this.parent();
			
			/* one item should always remain */
			if ($parent.siblings().length > 0) {
				$parent.slideUp(100);
				setTimeout(function() {
					$parent.remove();
				}, 1000);
			}
			
			return false;
		});
	}
	
	/**
	 * Init
	 *
	 */
	$t.init = function() {
		
		$t.doWhen();
		$t.tabMinHeight();
		$t.tabs();
		$t.hiddenField();
		$t.checkboxField();
		$t.radioField();
		$t.switchAndButtonset();
		$t.uiSliderField();
		$t.uiRangeField();
		$t.fileUploadField();
		$t.removeFile();
		$t.fileSorting();
		$t.radioImageField();
		$t.postStatusSelect();
		$t.pageTemplateSelect();
		$t.repeatableField();
		$t.addRepeatableField();
		$t.removeRepeatableField();
		
	};
}

jQuery(document).ready(function($) {
	
	DilazMetaboxScript.init();
	
});