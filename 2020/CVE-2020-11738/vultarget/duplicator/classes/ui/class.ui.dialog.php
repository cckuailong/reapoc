<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * Used to generate a thick-box inline dialog such as an alert or confirm pop-up
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 *
 */

// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

class DUP_UI_Dialog
{
    /**
     * The title that shows up in the dialog
     */
    public $title;

    /**
     * The message displayed in the body of the dialog
     */
    public $message;

    /**
     * The width of the dialog the default is used if not set
     * Alert = 475px (default) |  Confirm = 500px (default)
     */
    public $width;

    /**
     * The height of the dialog the default is used if not set
     * Alert = 125px (default) |  Confirm = 150px (default)
     */
    public $height;

    /**
     * When the progress meter is running show this text
     * Available only on confirm dialogs
     */
    public $progressText;

    /**
     * When true a progress meter will run until page is reloaded
     * Available only on confirm dialogs
     */
    public $progressOn = true;

    /**
     * The javascript call back method to call when the 'Yes' button is clicked
     * Available only on confirm dialogs
     */
    public $jscallback;

    /**
     *
     * @var string
     */
    public $okText;

    /**
     *
     * @var string
     */
    public $cancelText;

    /**
     * If true close dialog on confirm
     * @var bool
     */
    public $closeOnConfirm = false;


    /**
     * The id given to the full dialog
     */
    private $id;

    /**
     * A unique id that is added to all id elements
     */
    private $uniqid;

    /**
     *  Init this object when created
     */
    public function __construct()
    {
        add_thickbox();
        $this->progressText = __('Processing please wait...', 'duplicator');
        $this->uniqid		= substr(uniqid('',true),0,14) . rand();
        $this->id           = 'dup-dlg-'.$this->uniqid;
        $this->okText       = __('OK', 'duplicator');
        $this->cancelText   = __('Cancel', 'duplicator');
    }

    /**
     * Gets the unique id that is assigned to each instance of a dialog
     *
     * @return int      The unique ID of this dialog
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Gets the unique id that is assigned to each instance of a dialogs message text
     *
     * @return int      The unique ID of the message
     */
    public function getMessageID()
    {
        return "{$this->id}_message";
    }

    /**
     * Initialize the alert base html code used to display when needed
     *
     * @return string	The html content used for the alert dialog
     */
    public function initAlert()
    {
        $onClickClose = '';
        if (!is_null($this->jscallback)) {
            $onClickClose .= $this->jscallback.';';
        }
        $onClickClose .= 'tb_remove();';

        $html = '
		<div id="'.esc_attr($this->id).'" style="display:none">
			<div class="dup-dlg-alert-txt">
				'.$this->message.'
				<br/><br/>
			</div>
			<div class="dup-dlg-alert-btns">
				<input type="button" class="button button-large" value="'.esc_attr($this->okText).'" onclick="'.$onClickClose.'" />
			</div>
		</div>';

        echo $html;
    }

    /**
     * Shows the alert base JS code used to display when needed
     *
     * @return string	The javascript content used for the alert dialog
     */
    public function showAlert()
    {
        $this->width  = is_numeric($this->width) ? $this->width : 500;
        $this->height = is_numeric($this->height) ? $this->height : 175;

        $html = "tb_show('".esc_js($this->title)."', '#TB_inline?width=".esc_js($this->width)."&height=".esc_js($this->height)."&inlineId=".esc_js($this->id)."');\n" .
				 "var styleData = jQuery('#TB_window').attr('style') + 'height: ".esc_js($this->height)."px !important';\n" .
			 	 "jQuery('#TB_window').attr('style', styleData);";

		echo $html;
    }

    /**
     * Shows the confirm base JS code used to display when needed
     *
     * @return string	The javascript content used for the confirm dialog
     */
    public function initConfirm()
    {
        $progress_data  = '';
        $progress_func2 = '';

        $onClickConfirm = '';
        if (!is_null($this->jscallback)) {
            $onClickConfirm .= $this->jscallback.';';
        }

        //Enable the progress spinner
        if ($this->progressOn) {
            $progress_func1 = "__DUP_UI_Dialog_".$this->uniqid;
            $progress_func2 = ";{$progress_func1}(this)";
            $progress_data  = "<div class='dup-dlg-confirm-progress'><i class='fas fa-circle-notch fa-spin fa-lg fa-fw'></i> ".esc_js($this->progressText)."</div>
				<script>
					function {$progress_func1}(obj)
					{
						jQuery(obj).parent().parent().find('.dup-dlg-confirm-progress').show();
						jQuery(obj).closest('.dup-dlg-confirm-btns').find('input').attr('disabled', 'true');
					}
				</script>";
            $onClickConfirm .= $progress_func2.';';
        }

        if ($this->closeOnConfirm) {
             $onClickConfirm .= 'tb_remove();';
        }

        $html =
            '<div id="'.esc_attr($this->id).'" style="display:none">
				<div class="dup-dlg-confirm-txt">
					<span id="'.esc_attr($this->id).'_message">'.esc_html($this->message).'</span>
					<br/><br/>
					'.$progress_data.'
				</div>
				<div class="dup-dlg-confirm-btns">
					<input type="button" class="button button-large" value="'.esc_attr($this->okText).'" onclick="'.$onClickConfirm.'" />
					<input type="button" class="button button-large" value="'.esc_attr($this->cancelText).'" onclick="tb_remove()" />
				</div>
			</div>';

        echo $html;
    }

    /**
     * Shows the confirm base JS code used to display when needed
     *
     * @return string	The javascript content used for the confirm dialog
     */
    public function showConfirm()
    {
        $this->width  = is_numeric($this->width) ? $this->width : 500;
        $this->height = is_numeric($this->height) ? $this->height : 225;
        $html = "tb_show('".esc_js($this->title)."', '#TB_inline?width=".esc_js($this->width)."&height=".esc_js($this->height)."&inlineId=".esc_js($this->id)."');\n" .
				 "var styleData = jQuery('#TB_window').attr('style') + 'height: ".esc_js($this->height)."px !important';\n" .
			 	 "jQuery('#TB_window').attr('style', styleData);";

		echo $html;
    }
}