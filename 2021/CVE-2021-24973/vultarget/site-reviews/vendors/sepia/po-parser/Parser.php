<?php
/**
 * @package qcubed/i18n dev-master f73451a
 */
namespace GeminiLabs\Sepia\PoParser;

use GeminiLabs\Sepia\PoParser\FileHandler;
use GeminiLabs\Sepia\PoParser\HandlerInterface;

/**
 * Class to parse .po file and extract its strings.
 *
 * @method array headers() deprecated
 * @method null update_entry($original, $translation = null, $tcomment = array(), $ccomment = array()) deprecated
 * @method array read($filePath) deprecated
 * @version 5.0.0
 */
class Parser
{
    const OPTION_EOL_KEY = 'multiline-glue';
    const OPTION_EOC_KEY = 'context-glue';

    const OPTION_EOL_VALUE = '<##EOL##>';     // End of Line token.
    const OPTION_EOC_VALUE = '<##EOC##>';     // End of Context token.

    /**
     * @var array
     */
    protected $entries = array();

    /**
     * @var string[]
     */
    protected $headers = array();

    /**
     * @var null|HandlerInterface
     */
    protected $sourceHandle = null;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Reads and parses a file
     *
     * @param string $filepath
     * @param array $options
     *
     * @return $this
     * @throws \Exception.
     */
    public static function parseFile($filepath, $options = array())
    {
    	try {
			$parser = new Parser(new FileHandler($filepath), $options);
			$parser->parse();
		}
		catch (\Exception $e) {
    		throw new \Exception ($e->getMessage() . " in file: " . $filepath);
		}

        return $parser;
    }

    public function __construct(HandlerInterface $handler, $options = array())
    {
        $this->setSourceHandle($handler);
        $this->setOptions($options);
    }

    /**
     * Sets options.
     * Those options not set will the default value.
     *
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $defaultOptions = array(
            // Token used to separate lines in msgid
            self::OPTION_EOL_KEY => self::OPTION_EOL_VALUE,

            // Token used to separate ctxt from msgid
            self::OPTION_EOC_KEY => self::OPTION_EOC_VALUE
        );
        $this->options = array_merge($defaultOptions, $options);

        return $this;
    }

    /**
     * Get parser options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Gets source Handler.
     *
     * @return null|HandlerInterface
     */
    public function getSourceHandle()
    {
        return $this->sourceHandle;
    }

    /**
     * @param null|HandlerInterface $sourceHandle
     *
     * @return $this
     */
    public function setSourceHandle(HandlerInterface $sourceHandle)
    {
        $this->sourceHandle = $sourceHandle;

        return $this;
    }

    /**
     * Get headers from .po file
     *
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set new headers.
     *
     * @param array $newHeaders
     *
     * @return $this
     */
    public function setHeaders(array $newHeaders)
    {
        $this->headers = $newHeaders;

        return $this;
    }

    /**
     * Gets entries.
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Reads and parses strings of a .po file.
     *
     * @return array List of entries found in .po file.
     * @throws \Exception, \InvalidArgumentException
     */
    public function parse()
    {
        $handle = $this->sourceHandle;

        $headers         = array();
        $hash            = array();
        $entry           = array();
        $justNewEntry    = false; // A new entry has been just inserted.
        $firstLine       = true;
        $lastPreviousKey = null; // Used to remember last key in a multiline previous entry.
        $state           = null;
        $lineNumber      = 0;

        while (!$handle->ended()) {
            $line  = trim($handle->getNextLine());
            $split = preg_split('/\s+/ ', $line, 2);
            $key   = $split[0];

            // If a blank line is found, or a new msgid when already got one
            if ($line === '' || ($key=='msgid' && isset($entry['msgid']))) {
                // Two consecutive blank lines
                if ($justNewEntry) {
                    $lineNumber++;
                    continue;
                }

                if ($firstLine) {
                    $firstLine = false;
                    if (self::isHeader($entry)) {
                        array_shift($entry['msgstr']);
                        $headers = $entry['msgstr'];
                    } else {
                        $hash[] = $entry;
                    }
                } else {
                    // A new entry is found!
                    $hash[] = $entry;
                }

                $entry           = array();
                $state           = null;
                $justNewEntry    = true;
                $lastPreviousKey = null;
                if ($line==='') {
                    $lineNumber++;
                    continue;
                }
            }

            $justNewEntry = false;
            $data         = isset($split[1]) ? $split[1] : null;

			//var_dump($key); var_dump($state);

            switch ($key) {
                // Flagged translation
                case '#,':
                    $entry['flags'] = preg_split('/,\s*/', $data);
                    break;

                // # Translator comments
                case '#':
                    $entry['tcomment'] = !isset($entry['tcomment']) ? array() : $entry['tcomment'];
                    $entry['tcomment'][] = $data;
                    break;

                // #. Comments extracted from source code
                case '#.':
                    $entry['ccomment'] = !isset($entry['ccomment']) ? array() : $entry['ccomment'];
                    $entry['ccomment'][] = $data;
                    break;

                // Reference
                case '#:':
                    $entry['reference'][] = addslashes($data);
                    break;


                case '#|':      // #| Previous untranslated string
                case '#~':      // #~ Old entry
                case '#~|':     // #~| Previous-Old untranslated string. Reported by @Cellard

                    switch ($key) {
                        case '#|':
                            $key = 'previous';
                            break;

                        case '#~':
                            $key = 'obsolete';
                            break;

                        case '#~|':
                            $key = 'previous-obsolete';
                            break;
                    }

                    $tmpParts = explode(' ', $data);
                    $tmpKey   = $tmpParts[0];

                    if (!in_array($tmpKey, array('msgid','msgid_plural','msgstr','msgctxt'))) {
                        // If there is a multiline previous string we must remember what key was first line.
                        $tmpKey = $lastPreviousKey;
                        $str = $data;
                    } else {
                        $str = implode(' ', array_slice($tmpParts, 1));
                    }

                    $entry[$key] = isset($entry[$key])? $entry[$key]:array('msgid'=>array(),'msgstr'=>array());

                    if (strpos($key, 'obsolete')!==false) {
                        $entry['obsolete'] = true;
                        switch ($tmpKey) {
                            case 'msgid':
                                $entry['msgid'][] = $str;
                                $lastPreviousKey = $tmpKey;
                                break;

                            case 'msgstr':
                                if ($str == "\"\"") {
                                    $entry['msgstr'][] = trim($str, '"');
                                } else {
                                    $entry['msgstr'][] = $str;
                                }
                                $lastPreviousKey = $tmpKey;
                                break;

                            default:
                                break;
                        }
                    }

                    if ($key!=='obsolete') {
                        switch ($tmpKey) {
                            case 'msgid':
                            case 'msgid_plural':
                            case 'msgstr':
                                $entry[$key][$tmpKey][] = $str;
                                $lastPreviousKey = $tmpKey;
                                break;

                            default:
                                $entry[$key][$tmpKey] = $str;
                                break;
                        }
                    }
                    break;


                // context
                // Allows disambiguations of different messages that have same msgid.
                // Example:
                //
                // #: tools/observinglist.cpp:700
                // msgctxt "First letter in 'Scope'"
                // msgid "S"
                // msgstr ""
                //
                // #: skycomponents/horizoncomponent.cpp:429
                // msgctxt "South"
                // msgid "S"
                // msgstr ""
                case 'msgctxt':
                    // untranslated-string
                case 'msgid':
                    // untranslated-string-plural
                case 'msgid_plural':
                    $state = $key;
                    $entry[$state][] = $data;
                    break;
                // translated-string
                case 'msgstr':
                    $state = 'msgstr';
                    $entry[$state][] = $data;
                    break;

                default:
                    if (strpos($key, 'msgstr[') !== false) {
                        // translated-string-case-n
                        $state = $key;
                        $entry[$state][] = $data;
                    } else {
                        // "multiline" lines
                        switch ($state) {
                            case 'msgctxt':
                            case 'msgid':
                            case 'msgid_plural':
                            	if (!isset($entry[$state])) {
                            		throw new \Exception('Missing state:' . $state);
								}
                                if (is_string($entry[$state])) {
                                    // Convert it to array
                                    $entry[$state] = array($entry[$state]);
                                }
                                $entry[$state][] = $line;
                                break;

                            case 'msgstr':
                                // Special fix where msgid is ""
                                if ($entry['msgid'] == "\"\"") {
                                    $entry['msgstr'][] = trim($line, '"');
                                } else {
                                    $entry['msgstr'][] = $line;
                                }
                                break;

                            default:
                            	if (!empty($state) && (strpos($state, 'msgstr[') !== false)) {
									if (!isset($entry[$state])) {
										throw new \Exception('Missing state:' . $state);
									}
									if (is_string($entry[$state])) {
										// Convert it to array
										$entry[$state] = array($entry[$state]);
									}
									$entry[$state][] = $line;
								}
								elseif ($key[0] == '#' && $key[1] != ' ') {
									throw new \Exception(
										'PoParser: Parse error! Comments must have a space after them on line ' . ($lineNumber+1)
									);
								}
								else {
									throw new \Exception(
										'PoParser: Parse error! Unknown key "' . $key . '" on line ' . ($lineNumber+1)
									);
								}

                        }
                    }
                    break;
            }

            $lineNumber++;
        }
        $handle->close();

        // add final entry
        if ($state == 'msgstr') {
            $hash[] = $entry;
        }

        // - Cleanup header data
        $this->headers = array();
        foreach ($headers as $header) {
            $header = $this->clean($header);
            $this->headers[] = "\"" . preg_replace("/\\n/", '\n', $header) . "\"";
        }

        // - Cleanup data,
        // - merge multiline entries
        // - Reindex hash for ksort
        $temp = $hash;
        $this->entries = array();
        foreach ($temp as $entry) {
            foreach ($entry as &$v) {
                $or = $v;
                $v = $this->clean($v);
                if ($v === false) {
                    // parse error
                    throw new \Exception(
                        'PoParser: Parse error! poparser::clean returned false on "' . htmlspecialchars($or) . '"'
                    );
                }
            }

            // check if msgid and a key starting with msgstr exists
            if (isset($entry['msgid']) && count(preg_grep('/^msgstr/', array_keys($entry)))) {
                $id = $this->getEntryId($entry);
                $this->entries[$id] = $entry;
            }
        }

        return $this->entries;
    }

    /**
     * Updates an entry.
     * If entry not found returns false. If $createNew is true, a new entry will be created.
     * $entry is an array that can contain following indexes:
     *  - msgid: String Array. Required.
     *  - msgstr: String Array. Required.
     *  - reference: String Array.
     *  - msgctxt: String. Disambiguating context.
     *  - tcomment: String Array. Translator comments.
     *  - ccomment: String Array. Source comments.
     *  - msgid_plural: String Array.
     *  - flags: Array. List of entry flags. Example: array('fuzzy','php-format')
     *  - previous: Array: Contains previous untranslated strings in a sub array with msgid and msgstr.
     *
     * @param String  $msgid    Id of entry. Be aware that some entries have a multiline msgid.
     *                          In that case \n must be replaced by the value of 'multiline-glue'
     *                          option (by default "<##EOL##>").
     * @param array   $entry     Array with all entry data. Fields not setted will be removed.
     * @param boolean $createNew If msgid not found, it will create a new entry. By default true.
     *                           You want to set this to false if need to change the msgid of an entry.
     */
    public function setEntry($msgid, $entry, $createNew = true)
    {
        // In case of new entry
        if (!isset($this->entries[$msgid])) {
            if ($createNew===false) {
                return;
            }

            $this->entries[$msgid] = $entry;
        } else {
            // Be able to change msgid.
            if ($msgid!==$entry['msgid']) {
                unset($this->entries[$msgid]);
                $new_msgid = is_array($entry['msgid'])? implode($this->options[self::OPTION_EOL_KEY], $entry['msgid']):$entry['msgid'];
                $this->entries[$new_msgid] = $entry;
            } else {
                $this->entries[$msgid] = $entry;
            }
        }
    }

    /**
     * @param string $msgid Message Id.
     * @param bool   $plural
     */
    public function setEntryPlural($msgid, $plural = false)
    {
        if ($plural) {
            $this->entries[$msgid]['msgid_plural'] = $plural;
        } else {
            unset($this->entries[$msgid]['msgid_plural']);
        }
    }

    /**
     * @param string $msgid Message Id.
     * @param bool   $context
     */
    public function setEntryContext($msgid, $context = false)
    {
        if ($context) {
            $this->entries[$msgid]['msgctxt'][0] = $context;
        } else {
            unset($this->entries[$msgid]['msgctxt']);
        }
    }

    /**
     * Saves current translation back into source.
     *
     * @param mixed $params Parameters to pass to the source handler.
     *
     * @return $this
     * @throws \Exception
     */
    public function save($params)
    {
        $compiled = $this->compile();
        call_user_func(array($this->sourceHandle, 'save'), $compiled, $params);

        return $this;
    }

    /**
     * Compiles entries into a string
     *
     * @return string
     * @throws \Exception
     */
    public function compile()
    {
        $output = '';

        if (count($this->headers) > 0) {
            $output.= "msgid \"\"\n";
            $output.= "msgstr \"\"\n";
            foreach ($this->headers as $header) {
                $output.= $header . "\n";
            }
            $output.= "\n";
        }


        $entriesCount = count($this->entries);
        $counter = 0;
        foreach ($this->entries as $entry) {
            $isObsolete = isset($entry['obsolete']) && $entry['obsolete'];
            $isPlural = isset($entry['msgid_plural']);

            if (isset($entry['previous'])) {
                foreach ($entry['previous'] as $key => $data) {
                    if (is_string($data)) {
                        $output.= "#| " . $key . " " . $this->cleanExport($data) . "\n";
                    } elseif (is_array($data) && count($data)>0) {
                        foreach ($data as $line) {
                            $output.= "#| " . $key . " " . $this->cleanExport($line) . "\n";
                        }
                    }
                }
            }

            if (isset($entry['tcomment'])) {
                $output.= '# ' . implode("\n".'# ', $entry['tcomment']) . "\n";
            }

            if (isset($entry['ccomment'])) {
                $output.= '#. ' . implode("\n#. ", $entry['ccomment']) . "\n";
            }

            if (isset($entry['reference'])) {
                foreach ($entry['reference'] as $ref) {
                    $output.= '#: ' . $ref . "\n";
                }
            }

            if (isset($entry['flags']) && !empty($entry['flags'])) {
                $output.= "#, " . implode(', ', $entry['flags']) . "\n";
            }

            if (isset($entry['@'])) {
                $output.= "#@ " . $entry['@'] . "\n";
            }

            if (isset($entry['msgctxt'])) {
                $output.= 'msgctxt ' . $this->cleanExport($entry['msgctxt'][0]) . "\n";
            }


            if ($isObsolete) {
                $output.= "#~ ";
            }

            if (isset($entry['msgid'])) {
                // Special clean for msgid
                if (is_string($entry['msgid'])) {
                    $msgid = explode("\n", $entry['msgid']);
                } elseif (is_array($entry['msgid'])) {
                    $msgid = $entry['msgid'];
                } else {
                    throw new \Exception('msgid not string or array');
                }

                $output.= 'msgid ';
                foreach ($msgid as $i => $id) {
                    if ($i > 0 && $isObsolete) {
                        $output.= "#~ ";
                    }
                    $output.= $this->cleanExport($id) . "\n";
                }
            }

            if (isset($entry['msgid_plural'])) {
                // Special clean for msgid_plural
                if (is_string($entry['msgid_plural'])) {
                    $msgidPlural = explode("\n", $entry['msgid_plural']);
                } elseif (is_array($entry['msgid_plural'])) {
                    $msgidPlural = $entry['msgid_plural'];
                } else {
                    throw new \Exception('msgid_plural not string or array');
                }

                $output.= 'msgid_plural ';
                foreach ($msgidPlural as $plural) {
                    $output.= $this->cleanExport($plural) . "\n";
                }
            }

            if (count(preg_grep('/^msgstr/', array_keys($entry)))) { // checks if there is a key starting with msgstr
                if ($isPlural) {
                    foreach ($entry as $key => $value) {
                        if (strpos($key, 'msgstr[') === false) continue;
                        $output.= $key." ";
                        foreach ($value as $i => $t) {
                            $output.= $this->cleanExport($t) . "\n";
                        }
                    }
                } else {
                    foreach ((array)$entry['msgstr'] as $i => $t) {
                        if ($i == 0) {
                            if ($isObsolete) {
                                $output.= "#~ ";
                            }

                            $output.= 'msgstr ' . $this->cleanExport($t) . "\n";
                        } else {
                            if ($isObsolete) {
                                $output.= "#~ ";
                            }

                            $output.= $this->cleanExport($t) . "\n";
                        }
                    }
                }
            }

            $counter++;
            // Avoid inserting an extra newline at end of file
            if ($counter < $entriesCount) {
                $output.= "\n";
            }
        }

        return $output;
    }

    /**
     * Prepares a string to be output into a file.
     *
     * @param string $string The string to be converted.
     * @return string
     */
    protected function cleanExport($string)
    {
        $quote = '"';
        $slash = '\\';
        $newline = "\n";

        $replaces = array(
            "$slash" => "$slash$slash",
            "$quote" => "$slash$quote",
            "\t" => '\t',
        );

        $string = str_replace(array_keys($replaces), array_values($replaces), $string);

        $po = $quote . implode("${slash}n$quote$newline$quote", explode($newline, $string)) . $quote;

        // remove empty strings
        return str_replace("$newline$quote$quote", '', $po);
    }

    /**
     * Generates the internal key for a msgid.
     *
     * @param array $entry
     *
     * @return string
     */
    protected function getEntryId(array $entry)
    {
        if (isset($entry['msgctxt'])) {
            $id = implode($this->options[self::OPTION_EOL_KEY], (array)$entry['msgctxt']) . $this->options[self::OPTION_EOC_KEY] . implode($this->options[self::OPTION_EOL_KEY], (array)$entry['msgid']);
        } else {
            $id = implode($this->options[self::OPTION_EOL_KEY], (array)$entry['msgid']);
        }

        return $id;
    }

    /**
     * Undo `cleanExport` actions on a string.
     *
     * @param string|array $x
     *
     * @return string|array
     */
    protected function clean($x)
    {
        if (is_array($x)) {
            foreach ($x as $k => $v) {
                $x[$k] = $this->clean($v);
            }
        } else {
            // Remove double quotes from start and end of string
            if ($x == '') {
                return '';
            }

            if ($x[0] == '"') {
                $x = substr($x, 1, -1);
            }

            // Escapes C-style escape sequences (\a,\b,\f,\n,\r,\t,\v) and converts them to their actual meaning
            $x = stripcslashes($x);
        }

        return $x;
    }

    /**
     * Checks if entry is a header by
     *
     * @param array $entry
     * @return bool
     */
    protected static function isHeader(array $entry)
    {
        if (empty($entry) || !isset($entry['msgstr'])) {
            return false;
        }

        $headerKeys = array(
            'Project-Id-Version:' => false,
            'Report-Msgid-Bugs-To:' => false,
            'POT-Creation-Date:' => false,
            'PO-Revision-Date:' => false,
            'Last-Translator:' => false,
            'Language-Team:' => false,
            'MIME-Version:' => false,
            'Content-Type:' => false,
            'Content-Transfer-Encoding:' => false,
            'Plural-Forms:' => false
        );
        $keys = array_keys($headerKeys);

        $headerItems = 0;
        foreach ($entry['msgstr'] as $str) {
            $tokens = explode(':', $str);
            $tokens[0] = trim($tokens[0], "\"") . ':';

            if (in_array($tokens[0], $keys)) {
                $headerItems++;
                unset($headerKeys[$tokens[0]]);
                $keys = array_keys($headerKeys);
            }
        }
        return ($headerItems>0) ? true : false;
    }
}
