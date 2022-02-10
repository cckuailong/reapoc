// External Dependencies
import React, { Component } from 'react';
import $ from 'jquery';

class MECShortcodes extends Component {
  
  static slug = 'mecdivi_MECShortcodes';
  render() {
	$.ajax({
		url: window.ETBuilderBackend.ajaxUrl,
		type: 'post',
		data: {
			action: 'MECDIVI_load_mec_shortcode',
			nonce: 'et_admin_load_nonce',
			shortcode_id: this.props.shortcode_id,
		},
		success: function (response) {
			$('.mec-shortcode').html(response);
		}
	});
    return (
      <div class="mec-shortcode"></div>
    );
  }
}

export default MECShortcodes;
