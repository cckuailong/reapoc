<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdufaqBackwardsCompatibility' ) ) {
/**
 * Class to handle transforming the plugin settings from the 
 * previous style (individual options) to the new one (options array)
 *
 * @since 2.0.0
 */
class ewdufaqBackwardsCompatibility {

	public function __construct() {
		
		if ( empty( get_option( 'ewd-ufaq-settings' ) ) and get_option( 'EWD_UFAQ_Full_Version' ) ) { $this->run_backwards_compat(); }
		elseif ( ! get_option( 'ewd-ufaq-permission-level' ) ) { update_option( 'ewd-ufaq-permission-level', 1 ); }
	}

	public function run_backwards_compat() {

		$settings = array(
			'custom-css' 								=> get_option( 'EWD_UFAQ_Custom_CSS' ),
			'disable-faq-toggle'						=> get_option( 'EWD_UFAQ_Toggle' ) == 'Yes' ? false : true,
			'faq-category-toggle'						=> get_option( 'EWD_UFAQ_Category_Toggle' ) == 'Yes' ? true : false,
			'faq-category-accordion'					=> get_option( 'EWD_UFAQ_Category_Accordion' ) == 'Yes' ? true : false,
			'expand-collapse-all'						=> get_option( 'EWD_UFAQ_Expand_Collapse_All' ) == 'Yes' ? true : false,
			'faq-accordion'								=> get_option( 'EWD_UFAQ_FAQ_Accordion' ) == 'Yes' ? true : false,
			'hide-categories'							=> get_option( 'EWD_UFAQ_Hide_Categories' ) == 'Yes' ? true : false,
			'hide-tags'									=> get_option( 'EWD_UFAQ_Hide_Tags' ) == 'Yes' ? true : false,
			'scroll-to-top'								=> get_option( 'EWD_UFAQ_Scroll_To_Top' ) == 'Yes' ? true : false,
			'display-all-answers'						=> get_option( 'EWD_UFAQ_Display_All_Answers' ) == 'Yes' ? true : false,
			'display-author'							=> get_option( 'EWD_UFAQ_Display_Author' ) == 'Yes' ? true : false,
			'display-date'								=> get_option( 'EWD_UFAQ_Display_Date' ) == 'Yes' ? true : false,
			'display-back-to-top'						=> get_option( 'EWD_UFAQ_Display_Back_To_Top' ) == 'Yes' ? true : false,
			'include-permalink'							=> get_option( 'EWD_UFAQ_Include_Permalink' ) == 'Yes' ? 'both' : ( get_option( 'EWD_UFAQ_Include_Permalink' ) == 'No' ? 'none' : strtolower( get_option( 'EWD_UFAQ_Include_Permalink' ) ) ),
			'permalink-type'							=> get_option( 'EWD_UFAQ_Permalink_Type' ) == 'SamePage' ? 'same_page' : 'individual_page',
			'comments-on'								=> get_option( 'EWD_UFAQ_Comments_On' ) == 'Yes' ? true : false,
			'disable-microdata'							=> get_option( 'EWD_UFAQ_Disable_Microdata' ) == 'Yes' ? true : false,
			'access-role'								=> get_option( 'EWD_UFAQ_Access_Role' ),
			'display-style'								=> strtolower( get_option( 'EWD_UFAQ_Display_Style' ) ),
			'number-of-columns'							=> strtolower( get_option( 'EWD_UFAQ_FAQ_Number_Of_Columns' ) ),
			'responsive-columns'						=> get_option( 'EWD_UFAQ_Responsive_Columns' ) == 'Yes' ? true : false,
			'color-block-shape'							=> strtolower( get_option( 'EWD_UFAQ_Color_Block_Shape' ) ),
			'faqs-per-page'								=> get_option( 'EWD_UFAQ_FAQs_Per_Page' ),
			'page-type'									=> strtolower( get_option( 'EWD_UFAQ_Page_Type' ) ),
			'faq-ratings'								=> get_option( 'EWD_UFAQ_FAQ_Ratings' ) == 'Yes' ? true : false,
			'thumbs-up-image'							=> get_option( 'EWD_UFAQ_Thumbs_Up_Image' ),
			'thumbs-down-image'							=> get_option( 'EWD_UFAQ_Thumbs_Down_Image' ),
			'woocommerce-integration'					=> get_option( 'EWD_UFAQ_WooCommerce_FAQs' ) == 'Yes' ? true : false,
			'woocommerce-use-product'					=> get_option( 'EWD_UFAQ_Use_Product' ) == 'Yes' ? true : false,
			'wpforms-integration'						=> get_option( 'EWD_UFAQ_WPForms_Integration' ) == 'Yes' ? true : false,
			'wpforms-post-count'						=> get_option( 'EWD_UFAQ_WPForms_Post_Count' ),
			'wpforms-faq-location'						=> strtolower( get_option( 'EWD_UFAQ_WPForms_FAQ_Location' ) ),
			'reveal-effect'								=> strtolower( get_option( 'EWD_UFAQ_Reveal_Effect' ) ),
			'pretty-permalinks'							=> get_option( 'EWD_UFAQ_Pretty_Permalinks' ) == 'Yes' ? true : false,
			'allow-proposed-answer'						=> get_option( 'EWD_UFAQ_Allow_Proposed_Answer' ) == 'Yes' ? true : false,
			'submit-custom-fields'						=> get_option( 'EWD_UFAQ_Submit_Custom_Fields' ) == 'Yes' ? true : false,
			'submit-question-captcha'					=> get_option( 'EWD_UFAQ_Submit_Question_Captcha' ) == 'Yes' ? true : false,
			'submitted-default-category'				=> get_option( 'EWD_UFAQ_Submitted_Default_Category' ),
			'admin-question-notification'				=> get_option( 'EWD_UFAQ_Admin_Question_Notification' ) == 'Yes' ? true : false,
			'admin-notification-email'					=> get_option( 'EWD_UFAQ_Admin_Notification_Email' ),
			'submit-faq-email'							=> get_option( 'EWD_UFAQ_Submit_FAQ_Email' ),
			'auto-complete-titles'						=> get_option( 'EWD_UFAQ_Auto_Complete_Titles' ) == 'Yes' ? true : false,
			'highlight-search-term'						=> get_option( 'EWD_UFAQ_Highlight_Search_Term' ) == 'Yes' ? true : false,
			'slug-base'									=> get_option( 'EWD_UFAQ_Slug_Base' ),
			'social-media'								=> array_diff( explode( ',', strtolower( get_option( 'EWD_UFAQ_Social_Media' ) ) ), array( 'blank' ) ),
			'faq-elements-order'						=> $this->convert_elements_order(),
			'slug-base'									=> get_option( 'EWD_UFAQ_Slug_Base' ),
			'group-by-category'							=> get_option( 'EWD_UFAQ_Group_By_Category' ) == 'Yes' ? true : false,
			'group-by-category-count'					=> get_option( 'EWD_UFAQ_Group_By_Category_Count' ) == 'Yes' ? true : false,
			'category-order-by'							=> strtolower( get_option( 'EWD_UFAQ_Group_By_Order_By' ) ),
			'category-order'							=> strtolower( get_option( 'EWD_UFAQ_Group_By_Order' ) ),
			'faq-order-by'								=> strtolower( get_option( 'EWD_UFAQ_Order_By' ) ),
			'faq-order'									=> strtolower( get_option( 'EWD_UFAQ_Order' ) ),
			'faq-fields'								=> $this->convert_field_array(),
			'hide-blank-fields'							=> get_option( 'EWD_UFAQ_Hide_Blank_Fields' ) == 'Yes' ? true : false,
			'label-posted'								=> get_option( 'EWD_UFAQ_Posted_Label' ),
			'label-by'									=> get_option( 'EWD_UFAQ_By_Label' ),
			'label-on'									=> get_option( 'EWD_UFAQ_On_Label' ),
			'label-categories'							=> get_option( 'EWD_UFAQ_Category_Label' ),
			'label-tags'								=> get_option( 'EWD_UFAQ_Tag_Label' ),
			'label-enter-question'						=> get_option( 'EWD_UFAQ_Enter_Question_Label' ),
			'label-search'								=> get_option( 'EWD_UFAQ_Search_Label' ),
			'label-permalink'							=> get_option( 'EWD_UFAQ_Permalink_Label' ),
			'label-back-to-top'							=> get_option( 'EWD_UFAQ_Back_To_Top_Label' ),
			'label-woocommerce-tab'						=> get_option( 'EWD_UFAQ_WooCommerce_Tab_Label' ),
			'label-share-faq'							=> get_option( 'EWD_UFAQ_Share_FAQ_Label' ),
			'label-ratings'								=> get_option( 'EWD_UFAQ_Find_FAQ_Helpful_Label' ),
			'label-search-placeholder'					=> get_option( 'EWD_UFAQ_Search_Placeholder_Label' ),
			'label-thank-you-submit'					=> get_option( 'EWD_UFAQ_Thank_You_Submit_Label' ),
			'label-submit-question'						=> get_option( 'EWD_UFAQ_Submit_Question_Label' ),
			'label-please-fill-form-below'				=> get_option( 'EWD_UFAQ_Please_Fill_Form_Below_Label' ),
			'label-send-question'						=> get_option( 'EWD_UFAQ_Send_Question_Label' ),
			'label-question-title'						=> get_option( 'EWD_UFAQ_Question_Title_Label' ),
			'label-question-title-explanation'			=> get_option( 'EWD_UFAQ_What_Question_Being_Answered_Label' ),
			'label-proposed-answer'						=> get_option( 'EWD_UFAQ_Proposed_Answer_Label' ),
			'label-question-author'						=> get_option( 'EWD_UFAQ_Review_Author_Label' ),
			'label-question-author-explanation'			=> get_option( 'EWD_UFAQ_What_Name_With_Review_Label' ),
			'label-captcha-image-number'				=> get_option( 'EWD_UFAQ_Captcha_Image_Number_Label' ),
			'label-retrieving-results'					=> get_option( 'EWD_UFAQ_Retrieving_Results' ),
			'label-no-results-found'					=> get_option( 'EWD_UFAQ_No_Results_Found_Text' ),
			'styling-toggle-background-color'			=> get_option( 'EWD_UFAQ_Styling_Default_Bg_Color' ),
			'styling-toggle-font-color'					=> get_option( 'EWD_UFAQ_Styling_Default_Font_Color' ),
			'styling-toggle-border-size'				=> get_option( 'EWD_UFAQ_Styling_Default_Border_Size' ),
			'styling-toggle-border-color'				=> get_option( 'EWD_UFAQ_Styling_Default_Border_Color' ),
			'styling-toggle-border-radius'				=> get_option( 'EWD_UFAQ_Styling_Default_Border_Radius' ),
			'styling-toggle-symbol-size'				=> get_option( 'EWD_UFAQ_Styling_Toggle_Symbol_Size' ),
			'styling-block-background-color'			=> get_option( 'EWD_UFAQ_Styling_Block_Bg_Color' ),
			'styling-block-font-color'					=> get_option( 'EWD_UFAQ_Styling_Block_Font_Color' ),
			'styling-list-font'							=> get_option( 'EWD_UFAQ_Styling_List_Font' ),
			'styling-list-font-size'					=> get_option( 'EWD_UFAQ_Styling_List_Font_Size' ),
			'styling-list-font-color'					=> get_option( 'EWD_UFAQ_Styling_List_Font_Color' ),
			'styling-list-margin'						=> get_option( 'EWD_UFAQ_Styling_List_Margin' ),
			'styling-list-padding'						=> get_option( 'EWD_UFAQ_Styling_List_Padding' ),
			'styling-question-font'						=> get_option( 'EWD_UFAQ_Styling_Question_Font' ),
			'styling-question-font-size'				=> get_option( 'EWD_UFAQ_Styling_Question_Font_Size' ),
			'styling-question-font-color'				=> get_option( 'EWD_UFAQ_Styling_Question_Font_Color' ),
			'styling-question-margin'					=> get_option( 'EWD_UFAQ_Styling_Question_Margin' ),
			'styling-question-padding'					=> get_option( 'EWD_UFAQ_Styling_Question_Padding' ),
			'styling-question-icon-top-margin'			=> get_option( 'EWD_UFAQ_Styling_Question_Icon_Top_Margin' ),
			'styling-answer-font'						=> get_option( 'EWD_UFAQ_Styling_Answer_Font' ),
			'styling-answer-font-size'					=> get_option( 'EWD_UFAQ_Styling_Answer_Font_Size' ),
			'styling-answer-font-color'					=> get_option( 'EWD_UFAQ_Styling_Answer_Font_Color' ),
			'styling-answer-margin'						=> get_option( 'EWD_UFAQ_Styling_Answer_Margin' ),
			'styling-answer-padding'					=> get_option( 'EWD_UFAQ_Styling_Answer_Padding' ),
			'styling-postdate-font'						=> get_option( 'EWD_UFAQ_Styling_Postdate_Font' ),
			'styling-postdate-font-size'				=> get_option( 'EWD_UFAQ_Styling_Postdate_Font_Size' ),
			'styling-postdate-font-color'				=> get_option( 'EWD_UFAQ_Styling_Postdate_Font_Color' ),
			'styling-postdate-margin'					=> get_option( 'EWD_UFAQ_Styling_Postdate_Margin' ),
			'styling-postdate-padding'					=> get_option( 'EWD_UFAQ_Styling_Postdate_Padding' ),
			'styling-category-heading-font'				=> get_option( 'EWD_UFAQ_Styling_Category_Heading_Font' ),
			'styling-category-heading-font-size'		=> get_option( 'EWD_UFAQ_Styling_Category_Heading_Font_Size' ),
			'styling-category-heading-font-color'		=> get_option( 'EWD_UFAQ_Styling_Category_Heading_Font_Color' ),
			'styling-category-font'						=> get_option( 'EWD_UFAQ_Styling_Category_Font' ),
			'styling-category-font-size'				=> get_option( 'EWD_UFAQ_Styling_Category_Font_Size' ),
			'styling-category-font-color'				=> get_option( 'EWD_UFAQ_Styling_Category_Font_Color' ),
			'styling-category-margin'					=> get_option( 'EWD_UFAQ_Styling_Category_Margin' ),
			'styling-category-padding'					=> get_option( 'EWD_UFAQ_Styling_Category_Padding' ),
			'styling-category-heading-type'				=> get_option( 'EWD_UFAQ_Styling_Category_Heading_Type' ),
			'styling-faq-heading-type'					=> get_option( 'EWD_UFAQ_Styling_FAQ_Heading_Type' ),
			'styling-toggle-symbol'						=> get_option( 'EWD_UFAQ_Toggle_Symbol' ),
		);

		add_option( 'ewd-ufaq-review-ask-time', get_option( 'EWD_UFAQ_Ask_Review_Date' ) );
		add_option( 'ewd-ufaq-installation-time', get_option( 'EWD_UFAQ_Install_Time' ) );
		
		update_option( 'ewd-ufaq-permission-level', get_option( 'EWD_UFAQ_Full_Version' ) == 'Yes' ? 2 : 1 );
		
		update_option( 'ewd-ufaq-settings', $settings );
	}

	public function convert_elements_order() {

		$elements_order = array();

		$old_elements = get_option( 'EWD_UFAQ_FAQ_Elements' );

		foreach ( $old_elements as $old_element ) {

			$elements_order[ strtolower( $old_element ) ] = $this->get_element_label( $old_element );
		}

		return json_encode( $elements_order );
	}

	public function convert_field_array() {

		$old_faq_fields = get_option( 'EWD_UFAQ_FAQ_Fields' );
		$new_faq_fields = array();

		foreach ( $old_faq_fields as $old_faq_field ) {

			$new_field = array(
				'id'			=> $old_faq_field['FieldID'],
				'name'			=> $old_faq_field['FieldName'],
				'type'			=> $old_faq_field['FieldType'],
				'options'		=> ! empty( $old_faq_field['FieldValues'] ) ? $old_faq_field['FieldValues'] : '',
			);

			$new_faq_fields[] = $new_field;
		}

		return json_encode( $new_faq_fields );
	}

	public function get_element_label( $element ) {

		if ( $element == 'Author_Date' ) { return 'Author/Date'; }
		elseif ( $element == 'Custom_Fields' ) { return 'Custom Fields'; }
		elseif ( $element == 'Social_Media' ) { return 'Social Media'; }
		elseif ( $element == 'Back_To_Top' ) { return 'Back to Top'; }

		return $element;
	}
}

}