<?php
/**
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var array $VARS
 */
$as   = analytics( $VARS['id'], $VARS['product_name'], $VARS['version'], $VARS['module_type'], $VARS['slug'] );
$slug = $as->get_slug();

$show_deactivation_feedback_form = $VARS['show_deactivation_feedback_form'];
$confirmation_message            = $VARS['uninstall_confirmation_message'];

$is_anonymous                     = ( true );
$anonymous_feedback_checkbox_html = '';

$reasons_list_items_html = '';

$uninstall_reason_nonce = wp_create_nonce( 'uninstall_reason' );

if ( $show_deactivation_feedback_form ) {
	$reasons = $VARS['reasons'];

	foreach ( $reasons as $reason ) {
		$list_item_classes = 'reason' . ( ! empty( $reason['input_type'] ) ? ' has-input' : '' );

		if ( isset( $reason['internal_message'] ) && ! empty( $reason['internal_message'] ) ) {
			$list_item_classes      .= ' has-internal-message';
			$reason_internal_message = $reason['internal_message'];
		} else {
			$reason_internal_message = '';
		}

		$reason_input_type        = ( ! empty( $reason['input_type'] ) ? $reason['input_type'] : '' );
		$reason_input_placeholder = ( ! empty( $reason['input_placeholder'] ) ? $reason['input_placeholder'] : '' );

		$reason_list_item_html = <<< HTML
                <li class="{$list_item_classes}"
                    data-input-type="{$reason_input_type}"
                    data-input-placeholder="{$reason_input_placeholder}">
                    <label>
                        <span>
                            <input type="radio" name="selected-reason" value="{$reason['id']}"/>
                        </span>
                        <span class="deactivation_reason">{$reason['text']}</span>
                    </label>
                    <div class="internal-message">{$reason_internal_message}</div>
                </li>
HTML;

		$reasons_list_items_html .= $reason_list_item_html;
		$reasons_list_items_html .= '<input type="hidden" name="slug" value="' . $as->get_slug() . '">';
	}

	if ( $is_anonymous ) {
		$anonymous_feedback_checkbox_html = sprintf(
			'<label class="anonymous-feedback-label"><input type="checkbox" class="anonymous-feedback-checkbox"> %s</label>',
			__( 'Send contact details for help', 'analytics' )
		);
	}
}

// Aliases.
$deactivate_text = __( 'Deactivate', 'analytics' );
$theme_text      = __( 'Theme', 'analytics' );
$activate_x_text = __( 'Activate %s', 'analytics' );

as_enqueue_local_style( 'as_dialog_boxes', '/admin/dialog-boxes.css' );
?>
<script type="text/javascript">
	(function ($) {
		var reasonsHtml = <?php echo json_encode( $reasons_list_items_html ); ?>,
			modalHtml =
				'<div class="as-modal as-modal-deactivation-feedback<?php echo empty( $confirmation_message ) ? ' no-confirmation-message' : ''; ?>">'
				+ '	<div class="as-modal-dialog">'
				+ '		<div class="as-modal-header">'
				+ '		    <h4><?php echo __( 'Quick Feedback', 'analytics' ); ?></h4>'
				+ '		</div>'
				+ '		<div class="as-modal-body">'
				+ '			<div class="as-modal-panel" data-panel-id="confirm"><p><?php echo $confirmation_message; ?></p></div>'
				+ '			<div class="as-modal-panel active" data-panel-id="reasons"><h3><strong><?php echo esc_js( sprintf( __( 'If you have a moment, please let us know why you are %s', 'analytics' ), ( $as->is_plugin() ? __( 'deactivating', 'analytics' ) : __( 'switching', 'analytics' ) ) ) ); ?>:</strong></h3><ul id="reasons-list">' + reasonsHtml + '</ul></div>'
				+ '		</div>'
				+ '		<div class="as-modal-footer">'
				+ '         <?php echo $anonymous_feedback_checkbox_html; ?>'
				+ '			<a href="#" class="button button-secondary button-deactivate"></a>'
				+ '			<a href="#" class="button button-primary button-close"><?php echo __( 'Cancel', 'analytics' ); ?></a>'
				+ '		</div>'
				+ '	</div>'
				+ '</div>',
			$modal = $(modalHtml),
			$deactivateLink = $('#the-list .deactivate > [data-module-slug=<?php echo $as->get_slug(); ?>].as-module-slug').prev(),
			selectedReasonID = false,
			redirectLink = '',
			$anonymousFeedback    = $modal.find( '.anonymous-feedback-label' ),
			isAnonymous           = <?php echo ( $is_anonymous ? 'true' : 'false' ); ?>,
			otherReasonID         = <?php echo Analytics::REASON_OTHER; ?>,
			dontShareDataReasonID = <?php echo Analytics::REASON_DONT_LIKE_TO_SHARE_MY_INFORMATION; ?>,
			deleteThemeUpdateData = <?php echo $as->is_theme() ? 'true' : 'false'; ?>,
			showDeactivationFeedbackForm = <?php echo ( $show_deactivation_feedback_form ? 'true' : 'false' ); ?>;

		$modal.appendTo($('body'));

		registerEventHandlers();

		function registerEventHandlers() {
			$deactivateLink.click(function (evt) {
				evt.preventDefault();
				redirectLink = $(this).attr('href');
				showModal();
			});
			<?php
			if ( ! $as->is_plugin() ) {
				/**
				 * For "theme" module type, the modal is shown when the current user clicks on
				 */
				?>
			$('body').on('click', '.theme-browser .theme .theme-actions .button.activate', function (evt) {
				evt.preventDefault();

				redirectLink = $(this).attr('href');

				if ( $modal.hasClass( 'no-confirmation-message' ) && ! showDeactivationFeedbackForm ) {
					deactivateModule();
				} else {
					showModal();
				}
			});
				<?php
			}
			?>

			$modal.on('input propertychange', '.reason-input input', function () {
				if (!isOtherReasonSelected()) {
					return;
				}

				var reason = $(this).val().trim();

				/**
				 * If reason is not empty, remove the error-message class of the message container
				 * to change the message color back to default.
				 */
				if (reason.length > 0) {
					$('.message').removeClass('error-message');
					enableDeactivateButton();
				}
			});

			$modal.on('blur', '.reason-input input', function () {
				var $userReason = $(this);

				setTimeout(function () {
					if (!isOtherReasonSelected()) {
						return;
					}

					/**
					 * If reason is empty, add the error-message class to the message container
					 * to change the message color to red.
					 */
					if (0 === $userReason.val().trim().length) {
						$('.message').addClass('error-message');
						disableDeactivateButton();
					}
				}, 150);
			});

			$modal.on('click', '.as-modal-footer .button', function (evt) {
				evt.preventDefault();

				if ($(this).hasClass('disabled')) {
					return;
				}

				var _parent = $(this).parents('.as-modal:first');
				var _this = $(this);

				if (_this.hasClass('allow-deactivate')) {
					var $radio = $modal.find('input[type="radio"]:checked');

					if (0 === $radio.length) {
						window.location.href = redirectLink;
						return;
					}

					var slug = $modal.find('input[name="slug"]').val();

					var $selected_reason = $radio.parents('li:first'),
						$input = $selected_reason.find('textarea, input[type="text"]'),
						userReason = ( 0 !== $input.length ) ? $input.val().trim() : '';

					var $deactivation_reason = $selected_reason.find('.deactivation_reason').text();

					if (isOtherReasonSelected() && ( '' === userReason )) {
						return;
					}

					$.ajax({
						url       : ajaxurl,
						method    : 'POST',
						data      : {
							action : 'submit_uninstall_reason',
							deactivation_reason : $deactivation_reason,
							reason_info         : userReason,
							is_anonymous        : isAnonymousFeedback(),
							slug                : slug,
							security            : '<?php echo $uninstall_reason_nonce; ?>',
						},
						beforeSend: function () {
							_parent.find('.as-modal-footer .button').addClass('disabled');
							_parent.find('.as-modal-footer .button-secondary').text('Processing...');
						},
						complete  : function () {
							// Do not show the dialog box, deactivate the plugin.
							window.location.href = redirectLink;
						}
					});
				} else if (_this.hasClass('button-deactivate')) {
					// Change the Deactivate button's text and show the reasons panel.
					_parent.find('.button-deactivate').addClass('allow-deactivate');

					if ( showDeactivationFeedbackForm ) {
						showPanel('reasons');
					} else {
						deactivateModule();
					}
				}
			});

			$modal.on('click', 'input[type="radio"]', function () {
				var $selectedReasonOption = $( this );

				// If the selection has not changed, do not proceed.
				if (selectedReasonID === $selectedReasonOption.val())
					return;

				selectedReasonID = $selectedReasonOption.val();

				if ( isAnonymous ) {
					if ( isReasonSelected( dontShareDataReasonID ) ) {
						$anonymousFeedback.hide();
					} else {
						$anonymousFeedback.show();
					}
				}

				var _parent = $(this).parents('li:first');

				$modal.find('.reason-input').remove();
				$modal.find( '.internal-message' ).hide();
				$modal.find('.button-deactivate').html('<?php echo esc_js(
					sprintf(
						__( 'Submit & %s', 'analytics' ),
						$as->is_plugin() ?
						$deactivate_text :
						sprintf( $activate_x_text, $theme_text )
					)
				) ?>');

				enableDeactivateButton();

				if ( _parent.hasClass( 'has-internal-message' ) ) {
					_parent.find( '.internal-message' ).show();
				}

				if (_parent.hasClass('has-input')) {
					var inputType = _parent.data('input-type'),
						inputPlaceholder = _parent.data('input-placeholder'),
						reasonInputHtml = '<div class="reason-input"><span class="message"></span>' + ( ( 'textfield' === inputType ) ? '<input type="text" maxlength="128" />' : '<textarea rows="5" maxlength="128"></textarea>' ) + '</div>';

					_parent.append($(reasonInputHtml));
					_parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();

					if (isOtherReasonSelected()) {
						showMessage('<?php echo esc_js( __( 'Kindly tell us the reason so we can improve.', 'analytics' ) ); ?>');
						disableDeactivateButton();
					}
				}
			});

			// If the user has clicked outside the window, cancel it.
			$modal.on('click', function (evt) {
				var $target = $(evt.target);

				// If the user has clicked anywhere in the modal dialog, just return.
				if ($target.hasClass('as-modal-body') || $target.hasClass('as-modal-footer')) {
					return;
				}

				// If the user has not clicked the close button and the clicked element is inside the modal dialog, just return.
				if (
					! $target.hasClass( 'button-close' ) &&
					( $target.parents( '.as-modal-body' ).length > 0 || $target.parents( '.as-modal-footer' ).length > 0 )
				) {
					return;
				}

				closeModal();

				return false;
			});
		}

		function isAnonymousFeedback() {
			if ( ! isAnonymous ) {
				return false;
			}

			return ( isReasonSelected( dontShareDataReasonID ) || $anonymousFeedback.find( 'input' ).prop( 'checked' ) );
		}

		function isReasonSelected( reasonID ) {
			// Get the selected radio input element.
			var $selectedReasonOption = $modal.find('input[type="radio"]:checked');

			return ( reasonID == $selectedReasonOption.val() );
		}

		function isOtherReasonSelected() {
			return isReasonSelected( otherReasonID );
		}

		function showModal() {
			resetModal();

			// Display the dialog box.
			$modal.addClass('active');

			$('body').addClass('has-as-modal');
		}

		function closeModal() {
			$modal.removeClass('active');

			$('body').removeClass('has-as-modal');
		}

		function resetModal() {
			selectedReasonID = false;

			enableDeactivateButton();

			// Uncheck all radio buttons.
			$modal.find('input[type="radio"]').prop('checked', false);

			// Remove all input fields ( textfield, textarea ).
			$modal.find('.reason-input').remove();

			$modal.find('.message').hide();

			if ( isAnonymous ) {
				$anonymousFeedback.find( 'input' ).prop( 'checked', false );

				// Hide, since by default there is no selected reason.
				$anonymousFeedback.hide();
			}

			var $deactivateButton = $modal.find('.button-deactivate');

			/*
			 * If the modal dialog has no confirmation message, that is, it has only one panel, then ensure
			 * that clicking the deactivate button will actually deactivate the plugin.
			 */
			if ( $modal.hasClass( 'no-confirmation-message' ) ) {
				$deactivateButton.addClass( 'allow-deactivate' );

				showPanel( 'reasons' );
			} else {
				$deactivateButton.removeClass( 'allow-deactivate' );

				showPanel( 'confirm' );
			}
		}

		function showMessage(message) {
			$modal.find('.message').text(message).show();
		}

		function enableDeactivateButton() {
			$modal.find('.button-deactivate').removeClass('disabled');
		}

		function disableDeactivateButton() {
			$modal.find('.button-deactivate').addClass('disabled');
		}

		function showPanel(panelType) {
			$modal.find( '.as-modal-panel' ).removeClass( 'active' );
			$modal.find( '[data-panel-id="' + panelType + '"]' ).addClass( 'active' );

			updateButtonLabels();
		}

		function updateButtonLabels() {
			var $deactivateButton = $modal.find( '.button-deactivate' );

			// Reset the deactivate button's text.
			if ( 'confirm' === getCurrentPanel() ) {
				$deactivateButton.text(
				<?php
				echo json_encode(
					sprintf(
						__( 'Yes - %s', 'analytics' ),
						$as->is_plugin() ?
						$deactivate_text :
						sprintf( $activate_x_text, $theme_text )
					)
				)
				?>
				 );
			} else {
				$deactivateButton.html(
				<?php
				echo json_encode(
					sprintf(
						__( 'Skip & %s', 'analytics' ),
						$as->is_plugin() ?
						$deactivate_text :
						sprintf( $activate_x_text, $theme_text )
					)
				)
				?>
				 );
			}
		}

		function getCurrentPanel() {
			return $modal.find('.as-modal-panel.active').attr('data-panel-id');
		}

		/**
		 * @author CyberChimps
		 *
		 * @since 1.0.0
		 */
		function deactivateModule() {
			window.location.href = redirectLink;
		}
	})(jQuery);
</script>
