<?php
/**
 * Notice manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Notice manager
 * singleton class
 */
final class DUPX_NOTICE_MANAGER
{
    const ADD_NORMAL               = 0; // add notice in list
    const ADD_UNIQUE               = 1; // add if unique id don't exists
    const ADD_UNIQUE_UPDATE        = 2; // add or update notice unique id
    const ADD_UNIQUE_APPEND        = 3; // append long msg
    const DEFAULT_UNIQUE_ID_PREFIX = '__auto_unique_id__';

    private static $uniqueCountId = 0;

    /**
     *
     * @var DUPX_NOTICE_ITEM[]
     */
    private $nextStepNotices = array();

    /**
     *
     * @var DUPX_NOTICE_ITEM[]
     */
    private $finalReporNotices = array();

    /**
     *
     * @var DUPX_NOTICE_MANAGER
     */
    private static $instance = null;

    /**
     *
     * @var string
     */
    private $persistanceFile = null;

    /**
     *
     * @return DUPX_S_R_MANAGER
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->persistanceFile = $GLOBALS["NOTICES_FILE_PATH"];
        $this->loadNotices();
    }

    /**
     * save notices from json file
     */
    public function saveNotices()
    {
        $notices = array(
            'globalData' => array(
                'uniqueCountId' => self::$uniqueCountId
            ),
            'nextStep' => array(),
            'finalReport' => array()
        );

        foreach ($this->nextStepNotices as $uniqueId => $notice) {
            $notices['nextStep'][$uniqueId] = $notice->toArray();
        }

        foreach ($this->finalReporNotices as $uniqueId => $notice) {
            $notices['finalReport'][$uniqueId] = $notice->toArray();
        }

        file_put_contents($this->persistanceFile, DupLiteSnapJsonU::wp_json_encode_pprint($notices));
    }

    /**
     * load notice from json file
     */
    private function loadNotices()
    {
        if (file_exists($this->persistanceFile)) {
            $json    = file_get_contents($this->persistanceFile);
            $notices = json_decode($json, true);

            $this->nextStepNotices   = array();
            $this->finalReporNotices = array();

            if (!empty($notices['nextStep'])) {
                foreach ($notices['nextStep'] as $uniqueId => $notice) {
                    $this->nextStepNotices[$uniqueId] = DUPX_NOTICE_ITEM::getItemFromArray($notice);
                }
            }

            if (!empty($notices['finalReport'])) {
                foreach ($notices['finalReport'] as $uniqueId => $notice) {
                    $this->finalReporNotices[$uniqueId] = DUPX_NOTICE_ITEM::getItemFromArray($notice);
                }
            }

            self::$uniqueCountId = $notices['globalData']['uniqueCountId'];
        } else {
            $this->resetNotices();
        }
    }

    /**
     * remove all notices and save reset file
     */
    public function resetNotices()
    {
        $this->nextStepNotices   = array();
        $this->finalReporNotices = array();
        self::$uniqueCountId     = 0;
        $this->saveNotices();
    }

    /**
     * return next step notice by id
     *
     * @param string $id
     * @return DUPX_NOTICE_ITEM
     */
    public function getNextStepNoticeById($id)
    {
        if (isset($this->nextStepNotices[$id])) {
            return $this->nextStepNotices[$id];
        } else {
            return null;
        }
    }

    /**
     * return last report notice by id
     *
     * @param string $id
     * @return DUPX_NOTICE_ITEM
     */
    public function getFinalReporNoticeById($id)
    {
        if (isset($this->finalReporNotices[$id])) {
            return $this->finalReporNotices[$id];
        } else {
            return null;
        }
    }

    /**
     *
     * @param array|DUPX_NOTICE_ITEM $item // if string add new notice obj with item message and level param
     *                                            // if array must be [
     *                                                                   'shortMsg' => text,
     *                                                                   'level' => level,
     *                                                                   'longMsg' => html text,
     *                                                                   'sections' => sections list,
     *                                                                   'faqLink' => [
     *                                                                                     'url' => external link
     *                                                                                     'label' => link text if empty get external url link
     *                                                                               ]
     *                                                                 ]
     * @param int $mode         // ADD_NORMAL | ADD_UNIQUE | ADD_UNIQUE_UPDATE | ADD_UNIQUE_APPEND
     * @param string $uniqueId  // used for ADD_UNIQUE or ADD_UNIQUE_UPDATE or ADD_UNIQUE_APPEND
     *
     * @return string   // notice insert id
     *
     * @throws Exception
     */
    public function addBothNextAndFinalReportNotice($item, $mode = self::ADD_NORMAL, $uniqueId = null)
    {
        $this->addNextStepNotice($item, $mode, $uniqueId);
        $this->addFinalReportNotice($item, $mode, $uniqueId);
    }

    /**
     *
     * @param array|DUPX_NOTICE_ITEM $item // if string add new notice obj with item message and level param
     *                                            // if array must be [
     *                                                                   'shortMsg' => text,
     *                                                                   'level' => level,
     *                                                                   'longMsg' => html text,
     *                                                                   'sections' => sections list,
     *                                                                   'faqLink' => [
     *                                                                                     'url' => external link
     *                                                                                     'label' => link text if empty get external url link
     *                                                                               ]
     *                                                                 ]
     * @param int $mode         // ADD_NORMAL | ADD_UNIQUE | ADD_UNIQUE_UPDATE | ADD_UNIQUE_APPEND
     * @param string $uniqueId  // used for ADD_UNIQUE or ADD_UNIQUE_UPDATE or ADD_UNIQUE_APPEND
     *
     * @return string   // notice insert id
     *
     * @throws Exception
     */
    public function addNextStepNotice($item, $mode = self::ADD_NORMAL, $uniqueId = null)
    {
        if (!is_array($item) && !($item instanceof DUPX_NOTICE_ITEM)) {
            throw new Exception('Invalid item param');
        }
        return self::addReportNoticeToList($this->nextStepNotices, $item, $mode, $uniqueId);
    }

    /**
     * addNextStepNotice wrapper to add simple message with error level
     *
     * @param string $message
     * @param int $level        // warning level
     * @param int $mode         // ADD_NORMAL | ADD_UNIQUE | ADD_UNIQUE_UPDATE | ADD_UNIQUE_APPEND
     * @param string $uniqueId  // used for ADD_UNIQUE or ADD_UNIQUE_UPDATE or ADD_UNIQUE_APPEND
     *
     * @return string   // notice insert id
     *
     * @throws Exception
     */
    public function addNextStepNoticeMessage($message, $level = DUPX_NOTICE_ITEM::INFO, $mode = self::ADD_NORMAL, $uniqueId = null)
    {
        return $this->addNextStepNotice(array(
                'shortMsg' => $message,
                'level' => $level,
                ), $mode, $uniqueId);
    }

    /**
     *
     * @param array|DUPX_NOTICE_ITEM $item // if string add new notice obj with item message and level param
     *                                            // if array must be [
     *                                                                   'shortMsg' => text,
     *                                                                   'level' => level,
     *                                                                   'longMsg' => html text,
     *                                                                   'sections' => sections list,
     *                                                                   'faqLink' => [
     *                                                                                     'url' => external link
     *                                                                                     'label' => link text if empty get external url link
     *                                                                               ]
     *                                                                 ]
     * @param int $mode         // ADD_NORMAL | ADD_UNIQUE | ADD_UNIQUE_UPDATE | ADD_UNIQUE_APPEND
     * @param string $uniqueId  // used for ADD_UNIQUE or ADD_UNIQUE_UPDATE or ADD_UNIQUE_APPEND
     *
     * @return string   // notice insert id
     *
     * @throws Exception
     */
    public function addFinalReportNotice($item, $mode = self::ADD_NORMAL, $uniqueId = null)
    {
        if (!is_array($item) && !($item instanceof DUPX_NOTICE_ITEM)) {
            throw new Exception('Invalid item param');
        }
        return self::addReportNoticeToList($this->finalReporNotices, $item, $mode, $uniqueId);
    }

    /**
     * addFinalReportNotice wrapper to add simple message with error level
     *
     * @param string $message
     * @param string|string[] $sections   // message sections on final report
     * @param int $level        // warning level
     * @param int $mode         // ADD_NORMAL | ADD_UNIQUE | ADD_UNIQUE_UPDATE | ADD_UNIQUE_APPEND
     * @param string $uniqueId  // used for ADD_UNIQUE or ADD_UNIQUE_UPDATE or ADD_UNIQUE_APPEND
     *
     * @return string   // notice insert id
     *
     * @throws Exception
     */
    public function addFinalReportNoticeMessage($message, $sections, $level = DUPX_NOTICE_ITEM::INFO, $mode = self::ADD_NORMAL, $uniqueId = null)
    {
        return $this->addFinalReportNotice(array(
                'shortMsg' => $message,
                'level' => $level,
                'sections' => $sections,
                ), $mode, $uniqueId);
    }

    /**
     *
     * @param array $list
     * @param array|DUPX_NOTICE_ITEM $item // if string add new notice obj with item message and level param
     *                                            // if array must be [
     *                                                                   'shortMsg' => text,
     *                                                                   'level' => level,
     *                                                                   'longMsg' => html text,
     *                                                                   'sections' => sections list,
     *                                                                   'faqLink' => [
     *                                                                                     'url' => external link
     *                                                                                     'label' => link text if empty get external url link
     *                                                                               ]
     *                                                                 ]
     * @param int $mode         // ADD_NORMAL | ADD_UNIQUE | ADD_UNIQUE_UPDATE | ADD_UNIQUE_APPEND
     * @param string $uniqueId  // used for ADD_UNIQUE or ADD_UNIQUE_UPDATE or ADD_UNIQUE_APPEND
     *
     * @return string   // notice insert id
     *
     * @throws Exception
     */
    private static function addReportNoticeToList(&$list, $item, $mode = self::ADD_NORMAL, $uniqueId = null)
    {
        switch ($mode) {
            case self::ADD_UNIQUE:
                if (empty($uniqueId)) {
                    throw new Exception('uniqueId can\'t be empty');
                }
                if (isset($list[$uniqueId])) {
                    return $uniqueId;
                }
            // no break -> continue on unique update
            case self::ADD_UNIQUE_UPDATE:
                if (empty($uniqueId)) {
                    throw new Exception('uniqueId can\'t be empty');
                }
                $insertId = $uniqueId;
                break;
            case self::ADD_UNIQUE_APPEND:
                if (empty($uniqueId)) {
                    throw new Exception('uniqueId can\'t be empty');
                }
                $insertId = $uniqueId;
                // if item id exist append long msg
                if (isset($list[$uniqueId])) {
                    $tempObj                  = self::getObjFromParams($item);
                    $list[$uniqueId]->longMsg .= $tempObj->longMsg;
                    $item                     = $list[$uniqueId];
                }
                break;
            case self::ADD_NORMAL:
            default:
                if (empty($uniqueId)) {
                    $insertId = self::getNewAutoUniqueId();
                } else {
                    $insertId = $uniqueId;
                }
        }

        $list[$insertId] = self::getObjFromParams($item);
        return $insertId;
    }

    /**
     *
     * @param string|array|DUPX_NOTICE_ITEM $item // if string add new notice obj with item message and level param
     *                                            // if array must be [
     *                                                                   'shortMsg' => text,
     *                                                                   'level' => level,
     *                                                                   'longMsg' => html text,
     *                                                                   'sections' => sections list,
     *                                                                   'faqLink' => [
     *                                                                                     'url' => external link
     *                                                                                     'label' => link text if empty get external url link
     *                                                                               ]
     *                                                                 ]
     * @param int $level message level considered only in the case where $item is a string.
     * @return \DUPX_NOTICE_ITEM
     *
     * @throws Exception
     */
    private static function getObjFromParams($item, $level = DUPX_NOTICE_ITEM::INFO)
    {
        if ($item instanceof DUPX_NOTICE_ITEM) {
            $newObj = $item;
        } else if (is_array($item)) {
            $newObj = DUPX_NOTICE_ITEM::getItemFromArray($item);
        } else if (is_string($item)) {
            $newObj = new DUPX_NOTICE_ITEM($item, $level);
        } else {
            throw new Exception('Notice input not valid');
        }

        return $newObj;
    }

    /**
     *
     * @param null|string $section if null is count global
     * @param int $level error level
     * @param string $operator > < >= <= = !=
     *
     * @return int
     */
    public function countFinalReportNotices($section = null, $level = DUPX_NOTICE_ITEM::INFO, $operator = '>=')
    {
        $result = 0;
        foreach ($this->finalReporNotices as $notice) {
            if (is_null($section) || in_array($section, $notice->sections)) {
                switch ($operator) {
                    case '>=':
                        $result        += (int) ($notice->level >= $level);
                        break;
                    case '>':
                        $result        += (int) ($notice->level > $level);
                        break;
                    case '=':
                        $result        += (int) ($notice->level = $level);
                        break;
                    case '<=':
                        $result        += (int) ($notice->level <= $level);
                        break;
                    case '<':
                        $result        += (int) ($notice->level < $level);
                        break;
                    case '!=':
                        $result        += (int) ($notice->level != $level);
                        break;
                }
            }
        }
        return $result;
    }

    /**
     * sort final report notice from priority and notice level
     */
    public function sortFinalReport()
    {
        uasort($this->finalReporNotices, 'DUPX_NOTICE_ITEM::sortNoticeForPriorityAndLevel');
    }

    /**
     * display final final report notice section
     *
     * @param string $section
     */
    public function displayFinalReport($section)
    {
        foreach ($this->finalReporNotices as $id => $notice) {
            if (in_array($section, $notice->sections)) {
                self::finalReportNotice($id, $notice);
            }
        }
    }

    /**
     *
     * @param string $section
     * @param string $title
     */
    public function displayFinalRepostSectionHtml($section, $title)
    {
        if ($this->haveSection($section)) {
            ?>
            <div id="report-section-<?php echo $section; ?>" class="section" >
                <div class="section-title" ><?php echo $title; ?></div>
                <div class="section-content">
                    <?php
                    $this->displayFinalReport($section);
                    ?>
                </div>
            </div>
            <?php
        }
    }

    /**
     *
     * @param string $section
     * @return boolean
     */
    public function haveSection($section)
    {
        foreach ($this->finalReporNotices as $notice) {
            if (in_array($section, $notice->sections)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param null|string $section  if null is a global result
     *
     * @return int // returns the worst level found
     *
     */
    public function getSectionErrLevel($section = null)
    {
        $result = DUPX_NOTICE_ITEM::INFO;

        foreach ($this->finalReporNotices as $notice) {
            if (is_null($section) || in_array($section, $notice->sections)) {
                $result = max($result, $notice->level);
            }
        }
        return $result;
    }

    /**
     *
     * @param string $section
     * @param bool $echo
     * @return void|string
     */
    public function getSectionErrLevelHtml($section = null, $echo = true)
    {
        return self::getErrorLevelHtml($this->getSectionErrLevel($section), $echo);
    }

    /**
     * Displa next step notice message
     *
     * @param bool $deleteListAfterDisaply
     * @return void
     */
    public function displayStepMessages($deleteListAfterDisaply = true)
    {
        if (empty($this->nextStepNotices)) {
            return;
        }
        ?>
        <div id="step-messages">
            <?php
            foreach ($this->nextStepNotices as $notice) {
                self::stepMsg($notice);
            }
            ?>
        </div>
        <?php
        if ($deleteListAfterDisaply) {
            $this->nextStepNotices = array();
            $this->saveNotices();
        }
    }

    /**
     *
     * @param DUPX_NOTICE_ITEM $notice
     */
    private static function stepMsg($notice)
    {
        $classes     = array(
            'notice',
            'next-step',
            self::getClassFromLevel($notice->level)
        );
        $haveContent = !empty($notice->faqLink) || !empty($notice->longMsg);
        ?>
        <div class="<?php echo implode(' ', $classes); ?>">
            <div class="title">
                <?php echo self::getNextStepLevelPrefixMessage($notice->level).': <b>'.htmlentities($notice->shortMsg).'</b>'; ?>
            </div>
            <?php if ($haveContent) { ?>
                <div class="title-separator" ></div>
                <?php
                ob_start();
                if (!empty($notice->faqLink)) {
                    ?>
                    See FAQ: <a href="<?php echo $notice->faqLink['url']; ?>" >
                        <b><?php echo htmlentities(empty($notice->faqLink['label']) ? $notice->faqLink['url'] : $notice->faqLink['label']); ?></b>
                    </a>
                    <?php
                }
                if (!empty($notice->faqLink) && !empty($notice->longMsg)) {
                    echo '<br><br>';
                }
                if (!empty($notice->longMsg)) {
                    switch ($notice->longMsgMode) {
                        case DUPX_NOTICE_ITEM::MSG_MODE_PRE:
                            echo '<pre>'.htmlentities($notice->longMsg).'</pre>';
                            break;
                        case DUPX_NOTICE_ITEM::MSG_MODE_HTML:
                            echo $notice->longMsg;
                            break;
                        case DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT:
                        default:
                            echo htmlentities($notice->longMsg);
                    }
                }
                $longContent = ob_get_clean();
                DUPX_U_Html::getMoreContent($longContent, 'info', 200);
            }
            ?>
        </div>
        <?php
    }

    /**
     *
     * @param string $id
     * @param DUPX_NOTICE_ITEM $notice
     */
    private static function finalReportNotice($id, $notice)
    {
        $classes        = array(
            'notice-report',
            'notice',
            self::getClassFromLevel($notice->level)
        );
        $haveContent    = !empty($notice->faqLink) || !empty($notice->longMsg);
        $contentId      = 'notice-content-'.$id;
        $iconClasses    = $haveContent ? 'fa fa-caret-right' : 'fa fa-toggle-empty';
        $toggleLinkData = $haveContent ? 'data-type="toggle" data-target="#'.$contentId.'"' : '';
        ?>
        <div class="<?php echo implode(' ', $classes); ?>">
            <div class="title" <?php echo $toggleLinkData; ?>>
                <i class="<?php echo $iconClasses; ?>"></i>  <?php echo htmlentities($notice->shortMsg); ?>
            </div>
            <?php
            if ($haveContent) {
                $infoClasses = array('info');
                if (!$notice->open) {
                    $infoClasses[] = 'no-display';
                }
                ?>
                <div id="<?php echo $contentId; ?>" class="<?php echo implode(' ', $infoClasses); ?>" >
                    <?php
                    if (!empty($notice->faqLink)) {
                        ?>
                        <b>See FAQ</b>: <a href="<?php echo $notice->faqLink['url']; ?>" >
                            <?php echo htmlentities(empty($notice->faqLink['label']) ? $notice->faqLink['url'] : $notice->faqLink['label']); ?>
                        </a>
                        <?php
                    }
                    if (!empty($notice->faqLink) && !empty($notice->longMsg)) {
                        echo '<br><br>';
                    }
                    if (!empty($notice->longMsg)) {
                        switch ($notice->longMsgMode) {
                            case DUPX_NOTICE_ITEM::MSG_MODE_PRE:
                                echo '<pre>'.htmlentities($notice->longMsg).'</pre>';
                                break;
                            case DUPX_NOTICE_ITEM::MSG_MODE_HTML:
                                echo $notice->longMsg;
                                break;
                            case DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT:
                            default:
                                echo htmlentities($notice->longMsg);
                        }
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     *
     * @param DUPX_NOTICE_ITEM $notice
     */
    private static function noticeToText($notice)
    {
        $result = '-----------------------'."\n".
            '['.self::getNextStepLevelPrefixMessage($notice->level, false).'] '.$notice->shortMsg;

        if (!empty($notice->sections)) {
            $result .= "\n\t".'SECTIONS: '.implode(',', $notice->sections);
        }
        if (!empty($notice->longMsg)) {
            $result .= "\n\t".'LONG MSG: '.$notice->longMsg;
        }
        return $result."\n";
    }

    public function nextStepLog()
    {
        if (!empty($this->nextStepNotices)) {
            DUPX_Log::info(
                '===================================='."\n".
                'NEXT STEP NOTICES'."\n".
                '====================================');
            foreach ($this->nextStepNotices as $notice) {
                DUPX_Log::info(self::noticeToText($notice));
            }
            DUPX_Log::info(
                '====================================');
        }
    }

    public function finalReportLog($sections = array())
    {
        if (!empty($this->finalReporNotices)) {
            DUPX_Log::info(
                '===================================='."\n".
                'FINAL REPORT NOTICES LIST'."\n".
                '====================================');
            foreach ($this->finalReporNotices as $notice) {
                if (count(array_intersect($notice->sections, $sections)) > 0) {
                    DUPX_Log::info(self::noticeToText($notice));
                }
            }
            DUPX_Log::info(
                '====================================');
        }
    }

    /**
     * get html class from level
     *
     * @param int $level
     * @return string
     */
    private static function getClassFromLevel($level)
    {
        switch ($level) {
            case DUPX_NOTICE_ITEM::INFO:
                return 'l-info';
            case DUPX_NOTICE_ITEM::NOTICE:
                return 'l-notice';
            case DUPX_NOTICE_ITEM::SOFT_WARNING:
                return 'l-swarning';
            case DUPX_NOTICE_ITEM::HARD_WARNING:
                return 'l-hwarning';
            case DUPX_NOTICE_ITEM::CRITICAL:
                return 'l-critical';
            case DUPX_NOTICE_ITEM::FATAL:
                return 'l-fatal';
        }
    }

    /**
     * get level label from level
     *
     * @param int $level
     * @param bool $echo
     * @return type
     */
    public static function getErrorLevelHtml($level, $echo = true)
    {
        switch ($level) {
            case DUPX_NOTICE_ITEM::INFO:
                $label = 'good';
                break;
            case DUPX_NOTICE_ITEM::NOTICE:
                $label = 'good';
                break;
            case DUPX_NOTICE_ITEM::SOFT_WARNING:
                $label = 'warning';
                break;
            case DUPX_NOTICE_ITEM::HARD_WARNING:
                $label = 'warning';
                break;
            case DUPX_NOTICE_ITEM::CRITICAL:
                $label = 'critical error';
                break;
            case DUPX_NOTICE_ITEM::FATAL:
                $label = 'fatal error';
                break;
            default:
                return;
        }
        $classes = self::getClassFromLevel($level);
        ob_start();
        ?>
        <span class="notice-level-status <?php echo $classes; ?>"><?php echo $label; ?></span>
        <?php
        if ($echo) {
            ob_end_flush();
        } else {
            return ob_get_clean();
        }
    }

    /**
     * get next step message prefix
     *
     * @param int $level
     * @param bool $echo
     * @return string
     */
    public static function getNextStepLevelPrefixMessage($level, $echo = true)
    {
        switch ($level) {
            case DUPX_NOTICE_ITEM::INFO:
                $label = 'INFO';
                break;
            case DUPX_NOTICE_ITEM::NOTICE:
                $label = 'NOTICE';
                break;
            case DUPX_NOTICE_ITEM::SOFT_WARNING:
                $label = 'WARNING';
                break;
            case DUPX_NOTICE_ITEM::HARD_WARNING:
                $label = 'WARNING';
                break;
            case DUPX_NOTICE_ITEM::CRITICAL:
                $label = 'CRITICAL ERROR';
                break;
            case DUPX_NOTICE_ITEM::FATAL:
                $label = 'FATAL ERROR';
                break;
            default:
                return;
        }

        if ($echo) {
            echo $label;
        } else {
            return $label;
        }
    }

    /**
     * get unique id
     *
     * @return string
     */
    private static function getNewAutoUniqueId()
    {
        self::$uniqueCountId ++;
        return self::DEFAULT_UNIQUE_ID_PREFIX.self::$uniqueCountId;
    }

    /**
     * function for internal test
     *
     * display all messages levels
     */
    public static function testNextStepMessaesLevels()
    {
        $manager = self::getInstance();
        $manager->addNextStepNoticeMessage('Level info ('.DUPX_NOTICE_ITEM::INFO.')', DUPX_NOTICE_ITEM::INFO);
        $manager->addNextStepNoticeMessage('Level notice ('.DUPX_NOTICE_ITEM::NOTICE.')', DUPX_NOTICE_ITEM::NOTICE);
        $manager->addNextStepNoticeMessage('Level soft warning ('.DUPX_NOTICE_ITEM::SOFT_WARNING.')', DUPX_NOTICE_ITEM::SOFT_WARNING);
        $manager->addNextStepNoticeMessage('Level hard warning ('.DUPX_NOTICE_ITEM::HARD_WARNING.')', DUPX_NOTICE_ITEM::HARD_WARNING);
        $manager->addNextStepNoticeMessage('Level critical error ('.DUPX_NOTICE_ITEM::CRITICAL.')', DUPX_NOTICE_ITEM::CRITICAL);
        $manager->addNextStepNoticeMessage('Level fatal error ('.DUPX_NOTICE_ITEM::FATAL.')', DUPX_NOTICE_ITEM::FATAL);
        $manager->saveNotices();
    }

    /**
     * test function
     */
    public static function testNextStepFullMessageData()
    {
        $manager = self::getInstance();
        $longMsg = <<<LONGMSG
            <b>Formattend long text</b><br>
            <ul>
            <li>Proin dapibus mi eu erat pulvinar, id congue nisl egestas.</li>
            <li>Nunc venenatis eros et sapien ornare consequat.</li>
            <li>Mauris tincidunt est sit amet turpis placerat, a tristique dui porttitor.</li>
            <li>Etiam volutpat lectus quis risus molestie faucibus.</li>
            <li>Integer gravida eros sit amet sem viverra, a volutpat neque rutrum.</li>
            <li>Aenean varius ipsum vitae lorem tempus rhoncus.</li>
            </ul>
LONGMSG;
        $manager->addNextStepNotice(array(
            'shortMsg' => 'Full elements next step message MODE HTML',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'faqLink' => array(
                'url' => 'http://www.google.it',
                'label' => 'google link'
            )
        ));

        $longMsg = <<<LONGMSG
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc a auctor erat, et lobortis libero.
                Suspendisse aliquet neque in massa posuere mollis. Donec venenatis finibus sapien in bibendum. Donec et ex massa.

   Aliquam venenatis dapibus tellus nec ullamcorper. Mauris ante velit, tincidunt sit amet egestas et, mattis non lorem. In semper ex ut velit suscipit,
       at luctus nunc dapibus. Etiam blandit maximus dapibus. Nullam eu porttitor augue. Suspendisse pulvinar, massa eget condimentum aliquet, dolor massa tempus dui, vel rhoncus tellus ligula non odio.
           Ut ac faucibus tellus, in lobortis odio.
LONGMSG;
        $manager->addNextStepNotice(array(
            'shortMsg' => 'Full elements next step message MODE PRE',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
            'faqLink' => array(
                'url' => 'http://www.google.it',
                'label' => 'google link'
            )
        ));

        $longMsg = <<<LONGMSG
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc a auctor erat, et lobortis libero.
                Suspendisse aliquet neque in massa posuere mollis. Donec venenatis finibus sapien in bibendum. Donec et ex massa.

   Aliquam venenatis dapibus tellus nec ullamcorper. Mauris ante velit, tincidunt sit amet egestas et, mattis non lorem. In semper ex ut velit suscipit,
       at luctus nunc dapibus. Etiam blandit maximus dapibus. Nullam eu porttitor augue. Suspendisse pulvinar, massa eget condimentum aliquet, dolor massa tempus dui, vel rhoncus tellus ligula non odio.
           Ut ac faucibus tellus, in lobortis odio.
LONGMSG;
        $manager->addNextStepNotice(array(
            'shortMsg' => 'Full elements next step message MODE DEFAULT',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
            'faqLink' => array(
                'url' => 'http://www.google.it',
                'label' => 'google link'
            )
        ));


        $longMsg = <<<LONGMSG
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam cursus porttitor consectetur. Nunc faucibus elementum nisl nec ornare. Phasellus sit amet urna in diam ultricies ornare nec sit amet nibh. Nulla a aliquet leo. Quisque aliquet posuere lectus sit amet commodo. Nullam tempus enim eget urna rutrum egestas. Aliquam eget lorem nisl. Nulla tincidunt massa erat. Phasellus lectus tellus, mollis sit amet aliquam in, dapibus quis metus. Nunc venenatis nulla vitae convallis accumsan.

Mauris eu ullamcorper metus. Aenean ultricies et turpis eget mollis. Aliquam auctor, elit scelerisque placerat pellentesque, quam augue fermentum lectus, vel pretium nisi justo sit amet ante. Donec blandit porttitor tempus. Duis vulputate nulla ut orci rutrum, et consectetur urna mollis. Sed at iaculis velit. Pellentesque id quam turpis. Curabitur eu ligula velit. Cras gravida, ipsum sed iaculis eleifend, mauris nunc posuere quam, vel blandit nisi justo congue ligula. Phasellus aliquam eu odio ac porttitor. Fusce dictum mollis turpis sit amet fringilla.

Nulla eu ligula mauris. Fusce lobortis ligula elit, a interdum nibh pulvinar eu. Pellentesque rhoncus nec turpis id blandit. Morbi fringilla, justo non varius consequat, arcu ante efficitur ante, sit amet cursus lorem elit vel odio. Phasellus neque ligula, vehicula vel ipsum sed, volutpat dignissim eros. Curabitur at lacus id felis elementum auctor. Nullam ac tempus nisi. Phasellus nibh purus, aliquam nec purus ut, sodales lobortis nulla. Cras viverra dictum magna, ac malesuada nibh dictum ac. Mauris euismod, magna sit amet pretium posuere, ligula nibh ultrices tellus, sit amet pretium odio urna egestas justo. Suspendisse purus erat, eleifend sed magna in, efficitur interdum nibh.

Vivamus nibh nunc, fermentum non tortor volutpat, consectetur vulputate velit. Phasellus lobortis, purus et faucibus mollis, metus eros viverra ante, sit amet euismod nibh est eu orci. Duis sodales cursus lacinia. Praesent laoreet ut ipsum ut interdum. Praesent venenatis massa vitae ligula consequat aliquet. Fusce in purus in odio molestie laoreet at ac augue. Fusce consectetur elit a magna mollis aliquet.

Nulla eros nisi, dapibus eget diam vitae, tincidunt blandit odio. Fusce interdum tellus nec varius condimentum. Fusce non magna a purus sodales imperdiet sit amet vitae ligula. Quisque viverra leo sit amet mi egestas, et posuere nunc tincidunt. Suspendisse feugiat malesuada urna sed tincidunt. Morbi a urna sed magna volutpat pellentesque sit amet ac mauris. Nulla sed ultrices dui. Etiam massa arcu, tempor ut erat at, cursus malesuada ipsum. Duis sit amet felis dolor.

Morbi gravida nisl nunc, vulputate iaculis risus vehicula non. Proin cursus, velit et laoreet consectetur, lacus libero sagittis lacus, quis accumsan odio lectus non erat. Aenean dolor lectus, euismod sit amet justo eget, dictum gravida nisl. Phasellus sed nunc non odio ullamcorper rhoncus non ut ipsum. Duis ante ligula, pellentesque sit amet imperdiet eget, congue vel dui. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla facilisi. Suspendisse luctus leo eget justo mollis, convallis convallis ex suscipit. Integer et justo eget odio lobortis sollicitudin. Pellentesque accumsan rhoncus augue, luctus suscipit ex accumsan nec. Maecenas lacinia consectetur risus at bibendum. Etiam venenatis purus lorem, sit amet elementum turpis tristique eu. Proin vulputate faucibus feugiat. Nunc vehicula congue odio consequat vulputate. Quisque bibendum augue id iaculis faucibus. Donec blandit cursus sem, eget accumsan orci commodo sed.

Suspendisse iaculis est quam, sed scelerisque purus tincidunt non. Cras hendrerit ante turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse purus ipsum, rutrum id sem in, venenatis laoreet metus. Aliquam ac bibendum mauris. Cras egestas rhoncus est, sed lacinia nibh vestibulum id. Proin diam quam, sagittis congue molestie ac, rhoncus et mauris. Phasellus massa neque, ornare vel erat a, rutrum pharetra arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi et nulla eget massa auctor fermentum. Quisque maximus tellus sed cursus cursus. Ut vehicula erat at purus aliquet, quis imperdiet dui sagittis. Nullam eget quam leo.

Nulla magna ipsum, congue nec dui ut, lacinia malesuada felis. Cras mattis metus non maximus venenatis. Aliquam euismod est vitae erat sollicitudin, at pellentesque augue sollicitudin. Curabitur euismod maximus cursus. In tortor dui, convallis sed sapien ac, varius congue metus. Nunc ullamcorper ac orci sit amet finibus. Vivamus molestie nibh vitae quam rhoncus, eu ultrices est molestie. Maecenas consectetur eu quam sit amet placerat.

Curabitur ut fermentum mauris. Donec et congue nibh. Sed cursus elit sit amet convallis varius. Donec malesuada porta odio condimentum varius. Pellentesque ornare tempor ante, ut volutpat nulla lobortis sed. Nunc congue aliquet erat ac elementum. Quisque a ex sit amet turpis placerat sagittis eget ac ligula. Etiam in augue malesuada, aliquam est non, lacinia justo. Vivamus tincidunt dolor orci, id dignissim lorem maximus at. Vivamus ligula mauris, venenatis vel nibh id, lacinia ultrices ipsum. Mauris cursus, urna ac rutrum aliquet, risus ipsum tincidunt purus, sit amet blandit nunc sem sit amet nibh.

Nam eleifend risus lacus, eu pharetra risus egestas eu. Maecenas hendrerit nisl in semper placerat. Vestibulum massa tellus, laoreet non euismod quis, sollicitudin id sapien. Morbi vel cursus metus. Aenean tincidunt nisi est, ut elementum est auctor id. Duis auctor elit leo, ac scelerisque risus suscipit et. Pellentesque lectus nisi, ultricies in elit sed, pulvinar iaculis massa. Morbi viverra eros mi, pretium facilisis neque egestas id. Curabitur non massa accumsan, porttitor sem vitae, ultricies lacus. Curabitur blandit nisl velit. Mauris sollicitudin ultricies purus sit amet placerat. Fusce ac neque sed leo venenatis laoreet ut non ex. Integer elementum rhoncus orci, eu maximus neque tempus eu. Curabitur euismod dignissim tellus, vitae lacinia metus. Mauris imperdiet metus vitae vulputate accumsan. Duis eget luctus nibh, sit amet finibus libero.

LONGMSG;
        $manager->addNextStepNotice(array(
            'shortMsg' => 'Full elements LONG LONG',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_DEFAULT,
            'faqLink' => array(
                'url' => 'http://www.google.it',
                'label' => 'google link'
            )
        ));




        $manager->saveNotices();
    }

    /**
     * test function
     */
    public static function testFinalReporMessaesLevels()
    {
        $section = 'general';

        $manager = self::getInstance();
        $manager->addFinalReportNoticeMessage('Level info ('.DUPX_NOTICE_ITEM::INFO.')', $section, DUPX_NOTICE_ITEM::INFO, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_0');
        $manager->addFinalReportNoticeMessage('Level notice ('.DUPX_NOTICE_ITEM::NOTICE.')', $section, DUPX_NOTICE_ITEM::NOTICE, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_1');
        $manager->addFinalReportNoticeMessage('Level soft warning ('.DUPX_NOTICE_ITEM::SOFT_WARNING.')', $section, DUPX_NOTICE_ITEM::SOFT_WARNING, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_2');
        $manager->addFinalReportNoticeMessage('Level hard warning ('.DUPX_NOTICE_ITEM::HARD_WARNING.')', $section, DUPX_NOTICE_ITEM::HARD_WARNING, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_3');
        $manager->addFinalReportNoticeMessage('Level critical error ('.DUPX_NOTICE_ITEM::CRITICAL.')', $section, DUPX_NOTICE_ITEM::CRITICAL, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_4');
        $manager->addFinalReportNoticeMessage('Level fatal error ('.DUPX_NOTICE_ITEM::FATAL.')', $section, DUPX_NOTICE_ITEM::FATAL, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_5');
        $manager->saveNotices();
    }

    /**
     * test function
     */
    public static function testFinalReportFullMessages()
    {
        $section = 'general';
        $manager = self::getInstance();

        $longMsg = <<<LONGMSG
            <b>Formattend long text</b><br>
            <ul>
            <li>Proin dapibus mi eu erat pulvinar, id congue nisl egestas.</li>
            <li>Nunc venenatis eros et sapien ornare consequat.</li>
            <li>Mauris tincidunt est sit amet turpis placerat, a tristique dui porttitor.</li>
            <li>Etiam volutpat lectus quis risus molestie faucibus.</li>
            <li>Integer gravida eros sit amet sem viverra, a volutpat neque rutrum.</li>
            <li>Aenean varius ipsum vitae lorem tempus rhoncus.</li>
            </ul>
LONGMSG;

        $manager->addFinalReportNotice(array(
            'shortMsg' => 'Full elements final report message',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections' => $section,
            'faqLink' => array(
                'url' => 'http://www.google.it',
                'label' => 'google link'
            )
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_full_1');

        $manager->addFinalReportNotice(array(
            'shortMsg' => 'Full elements final report message info high priority',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => $longMsg,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections' => $section,
            'faqLink' => array(
                'url' => 'http://www.google.it',
                'label' => 'google link'
            ),
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'test_fr_full_2');
        $manager->saveNotices();
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }
}

class DUPX_NOTICE_ITEM
{
    const INFO             = 0;
    const NOTICE           = 1;
    const SOFT_WARNING     = 2;
    const HARD_WARNING     = 3;
    const CRITICAL         = 4;
    const FATAL            = 5;
    const MSG_MODE_DEFAULT = 'def';
    const MSG_MODE_HTML    = 'html';
    const MSG_MODE_PRE     = 'pre';

    /**
     *
     * @var string text
     */
    public $shortMsg = '';

    /**
     *
     * @var string html text
     */
    public $longMsg = '';

    /**
     *
     * @var bool if true long msg can be html
     */
    public $longMsgMode = self::MSG_MODE_DEFAULT;

    /**
     *
     * @var null|array // null = no faq link
     *                    array( 'label' => link text , 'url' => faq url)
     */
    public $faqLink = array(
        'label' => '',
        'url' => ''
    );

    /**
     *
     * @var string[] notice sections for final report only
     */
    public $sections = array();

    /**
     *
     * @var int
     */
    public $level = self::NOTICE;

    /**
     *
     * @var int
     */
    public $priority = 10;

    /**
     *
     * @var bool if true notice start open. For final report only
     */
    public $open = false;

    /**
     *
     * @param string $shortMsg text
     * @param int $level
     * @param string $longMsg html text
     * @param string|string[] $sections
     * @param null|array $faqLink [
     *                              'url' => external link
     *                              'label' => link text if empty get external url link
     *                          ]
     * @param int priority
     * @param bool open
     * @param string longMsgMode MSG_MODE_DEFAULT | MSG_MODE_HTML | MSG_MODE_PRE
     */
    public function __construct($shortMsg, $level = self::INFO, $longMsg = '', $sections = array(), $faqLink = null, $priority = 10, $open = false, $longMsgMode = self::MSG_MODE_DEFAULT)
    {
        $this->shortMsg    = (string) $shortMsg;
        $this->level       = (int) $level;
        $this->longMsg     = (string) $longMsg;
        $this->sections    = is_array($sections) ? $sections : array($sections);
        $this->faqLink     = $faqLink;
        $this->priority    = $priority;
        $this->open        = $open;
        $this->longMsgMode = $longMsgMode;
    }

    /**
     *
     * @return array        [
     *                          'shortMsg' => text,
     *                          'level' => level,
     *                          'longMsg' => html text,
     *                          'sections' => string|string[],
     *                          'faqLink' => [
     *                              'url' => external link
     *                              'label' => link text if empty get external url link
     *                          ]
     *                          'priority' => int low first
     *                          'open' => if true the tab is opene on final report
     *                          'longMsgMode'=> MSG_MODE_DEFAULT | MSG_MODE_HTML | MSG_MODE_PRE
     *                      ]
     */
    public function toArray()
    {
        return array(
            'shortMsg' => $this->shortMsg,
            'level' => $this->level,
            'longMsg' => $this->longMsg,
            'sections' => $this->sections,
            'faqLink' => $this->faqLink,
            'priority' => $this->priority,
            'open' => $this->open,
            'longMsgMode' => $this->longMsgMode
        );
    }

    /**
     *
     * @return array        [
     *                          'shortMsg' => text,
     *                          'level' => level,
     *                          'longMsg' => html text,
     *                          'sections' => string|string[],
     *                          'faqLink' => [
     *                              'url' => external link
     *                              'label' => link text if empty get external url link
     *                          ],
     *                          priority
     *                          open
     *                          longMsgMode
     *                      ]
     * @return DUPX_NOTICE_ITEM
     */
    public static function getItemFromArray($array)
    {
        if (isset($array['sections']) && !is_array($array['sections'])) {
            if (empty($array['sections'])) {
                $array['sections'] = array();
            } else {
                $array['sections'] = array($array['sections']);
            }
        }
        $params = array_merge(self::getDefaultArrayParams(), $array);
        $result = new self($params['shortMsg'], $params['level'], $params['longMsg'], $params['sections'], $params['faqLink'], $params['priority'], $params['open'], $params['longMsgMode']);
        return $result;
    }

    /**
     *
     * @return array        [
     *                          'shortMsg' => text,
     *                          'level' => level,
     *                          'longMsg' => html text,
     *                          'sections' => string|string[],
     *                          'faqLink' => [
     *                              'url' => external link
     *                              'label' => link text if empty get external url link
     *                          ],
     *                          priority
     *                          open
     *                          longMsgMode
     *                      ]
     */
    public static function getDefaultArrayParams()
    {
        return array(
            'shortMsg' => '',
            'level' => self::INFO,
            'longMsg' => '',
            'sections' => array(),
            'faqLink' => null,
            'priority' => 10,
            'open' => false,
            'longMsgMode' => self::MSG_MODE_DEFAULT
        );
    }

    /**
     * before lower priority
     * before highest level
     *
     * @param DUPX_NOTICE_ITEM $a
     * @param DUPX_NOTICE_ITEM $b
     */
    public static function sortNoticeForPriorityAndLevel($a, $b)
    {
        if ($a->priority == $b->priority) {
            if ($a->level == $b->level) {
                return 0;
            } else if ($a->level < $b->level) {
                return 1;
            } else {
                return -1;
            }
        } else if ($a->priority < $b->priority) {
            return -1;
        } else {
            return 1;
        }
    }
}