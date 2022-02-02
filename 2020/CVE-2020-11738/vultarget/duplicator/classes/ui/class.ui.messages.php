<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Used to generate a thick box inline dialog such as an alert or confirm pop-up
 *
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 */

class DUP_UI_Messages
{
    const UNIQUE_ID_PREFIX = 'dup_ui_msg_';
    const NOTICE           = 'updated';
    const WARNING          = 'update-nag';
    const ERROR            = 'error';

    private static $unique_id = 0;
    private $id;
    public $type              = self::NOTICE;
    public $content           = '';
    public $wrap_cont_tag     = 'p';
    public $hide_on_init      = true;
    public $is_dismissible    = false;
    /**
     *
     * @var int delay in milliseconds
     */
    public $auto_hide_delay   = 0;
    public $callback_on_show  = null;
    public $callback_on_hide  = null;

    public function __construct($content = '', $type = self::NOTICE)
    {
        self::$unique_id ++;
        $this->id = self::UNIQUE_ID_PREFIX.self::$unique_id;

        $this->content = (string) $content;
        $this->type    = $type;
    }

    protected function get_notice_classes($classes = array())
    {
        if (is_string($classes)) {
            $classes = explode(' ', $classes);
        } else if (is_array($classes)) {

        } else {
            $classes = array();
        }

        if ($this->is_dismissible) {
            $classes[] = 'is-dismissible';
        }

        $result = array_merge(array('notice', $this->type), $classes);
        return trim(implode(' ', $result));
    }

    public function initMessage()
    {
        $classes = array();
        if ($this->hide_on_init) {
            $classes[] = 'no_display';
        }

        $this->wrap_tag = empty($this->wrap_tag) ? 'p' : $this->wrap_tag;
        echo '<div id="'.$this->id.'" class="'.$this->get_notice_classes($classes).'">'.
        '<'.$this->wrap_cont_tag.' class="msg-content">'.
        $this->content.
        '</'.$this->wrap_cont_tag.'>'.
        '</div>';
    }

    public function updateMessage($jsVarName, $echo = true)
    {
        $result = 'jQuery("#'.$this->id.' > .msg-content").html('.$jsVarName.');';
        
        if ($echo) {
            echo $result;
        } else {
            return $result;
        }
    }

    public function showMessage($echo = true)
    {
        $callStr = !empty($this->callback_on_show) ? $this->callback_on_show.';' : '';
        $result  = 'jQuery("#'.$this->id.'").fadeIn( "slow", function() { $(this).removeClass("no_display");'.$callStr.' });';
        if ($this->auto_hide_delay > 0) {
            $result .= 'setTimeout(function () { '.$this->hideMessage(false).' }, '.$this->auto_hide_delay.');';
        }

        if ($echo) {
            echo $result;
        } else {
            return $result;
        }
    }

    public function hideMessage($echo = true)
    {
        $callStr = !empty($this->callback_on_hide) ? $this->callback_on_hide.';' : '';
        $result  = 'jQuery("#'.$this->id.'").fadeOut( "slow", function() { $(this).addClass("no_display");'.$callStr.' });';

        if ($echo) {
            echo $result;
        } else {
            return $result;
        }
    }
}