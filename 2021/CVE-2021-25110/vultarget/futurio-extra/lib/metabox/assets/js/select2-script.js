/**
|| --------------------------------------------------------------------------------------------
|| Select2 Scripts
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

var DilazMetaboxS2Scripts = new function() {

	"use strict";
	
	/**
	 * Global Variables
	 */
	var $t = this;
	var $ = jQuery.noConflict();
	
	/**
	 * select2 field
	 */
	$t.select2Field = function() {
		$('.select2single, .select2multiple').each(function() {
			
			var $this = $(this),
				$dataWidth = ($this.data('width').length > 1) ? $this.data('width') : '50px';
			 // data-width="230px"
			$this.select2({
				placeholder : '',
				width : $dataWidth,
				allowClear : true,
			});
		});
	}
	
	/**
	 * select2 ajax query
	 */
	$t.select2Query = function() {
		$('.dilaz-mb-query-select').each(function() {
			
			var $this = $(this);
			
			$this.select2({
				placeholder : $this.data('placeholder'),
				multiple : $this.data('multiple'),
				width : $this.data('width'),
				allowClear : true,
				minimumInputLength : $this.data('min-input'), // minimum number of characters
				maximumInputLength : $this.data('max-input'), // maximum number of characters
				delay : 250, // milliseconds before triggering the request
				debug : false,
				maximumSelectionLength: $this.data('max-options'), // maximum number of options selected
				ajax : {
					type : 'POST',
					url : ajaxurl,
					dataType : 'json',
					data : function (params) {
						return {
							q : params.term,
							action : 'dilaz_mb_query_select',
							selected : $this.val(),
							query_type : $this.data('query-type'),
							query_args : $this.data('query-args'),
						};
					},
					processResults : function(data) {
						
						var items   = [],
							newItem = null;

						for (var thisId in data) {
							
							newItem = {
								'id' : data[thisId]['id'],
								'text' : data[thisId]['name']
							};

							items.push(newItem);
						}

						return { results : items };
					} 
				}
			});
		});
	}
	
	/**
	 * select2 search user
	 */
	$t.select2SearchUser = function() {
		$('.dilaz-mb-select-users').each(function() {
			
			var $this = $(this);
			
			$this.select2({
				placeholder : $this.data('placeholder'),
				multiple : $this.data('multiple'),
				width : $this.data('width'),
				allowClear : true,
				minimumInputLength : $this.data('min-input'), // minimum number of characters
				maximumInputLength : $this.data('max-input'), // maximum number of characters
				delay : 250, // milliseconds before triggering the request
				debug : true,
				maximumSelectionLength: $this.data('max-options'), // maximum number of options selected
				ajax : {
					type : 'POST',
					url : ajaxurl,
					dataType : 'json',
					data : function (params) {
						return {
							q : params.term,
							action : 'dilaz_mb_search_user',
							selected : $this.val(),
							post_args : $this.data('post-args'),
						};
					},
					processResults : function(data) {
						
						var items = [],
						newItem = null;

						for (var thisId in data) {
							
							newItem = {
								'id' : data[thisId]['id'],
								'text' : data[thisId]['name']
							};

							items.push(newItem);
						}

						return { results : items };
					} 
				}
			});
		});
	}
	
	/**
	 * Init
	 *
	 */
	$t.init = function() {
		
		$t.select2Field();
		$t.select2Query();
		$t.select2SearchUser();

	};
}

jQuery(document).ready(function($) {
	
	DilazMetaboxS2Scripts.init();
	
});