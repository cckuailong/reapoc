<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * The base class for all filter types Directories/Files/Extentions
 *
 * @package Duplicator
 * @subpackage classes/package
 *
 */

// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

class DUP_Archive_Filter_Scope_Base
{
    //All internal storage items that duplicator decides to filter
    public $Core     = array();
    //Global filter items added from settings
    public $Global = array();
    //Items when creating a package or template that a user decides to filter
    public $Instance = array();
}

/**
 * The filter types that belong to directories
 *
 * @package Duplicator
 * @subpackage classes/package
 *
 */
class DUP_Archive_Filter_Scope_Directory extends DUP_Archive_Filter_Scope_Base
{
    //Items that are not readable
    public $Warning    = array();
    //Items that are not readable
    public $Unreadable = array();
}

/**
 * The filter types that belong to files
 *
 * @package Duplicator
 * @subpackage classes/package
 *
 */
class DUP_Archive_Filter_Scope_File extends DUP_Archive_Filter_Scope_Directory
{
    //Items that are too large
    public $Size = array();

}

/**
 * The filter information object which store all information about the filtered
 * data that is gathered to the execution of a scan process
 *
 * @package Duplicator
 * @subpackage classes/package
 *
 */
class DUP_Archive_Filter_Info
{
    //Contains all folder filter info
    public $Dirs       = array();
    //Contains all file filter info
    public $Files      = array();
    //Contains all extensions filter info
    public $Exts       = array();
    public $UDirCount  = 0;
    public $UFileCount = 0;
    public $UExtCount  = 0;
	public $TreeSize;
	public $TreeWarning;

    /**
     *  Init this object
     */
    public function __construct()
    {
        $this->reset();
    }

        /**
     * reset and clean all object
     */
    public function reset()
    {
        $this->Dirs  = new DUP_Archive_Filter_Scope_Directory();
        $this->Files = new DUP_Archive_Filter_Scope_File();
        $this->Exts  = new DUP_Archive_Filter_Scope_Base();
		$this->TreeSize = array();
		$this->TreeWarning = array();
    }
}

