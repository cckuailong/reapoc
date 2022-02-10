<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport;


/**
 * Something that can create multiple feed sources from plain text.
 *
 * @since 4.11
 */
class PlainTextImporter extends AbstractWpImporter implements ImporterInterface
{
    /**
     * @since 4.11
     * 
     * @param array $data Data members map.
     * @param callable $translator A translator.
     */
    public function __construct($data, $translator = null)
    {
        parent::__construct($data);

        if (!is_null($translator)) {
            $this->_setTranslator($translator);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     *
     * @param string $input The plain text to import from.
     * The current format is as follows:
     * - Feed sources are separated by new lines;
     * - Every line represents one feed source's fields;
     * - Fields are separated with a coma, and can optionally be surrounded by whitespace which will be trimmed;
     * - The first fields will be interpreted as the feed source's title;
     * - The second field will be interpreted as the feed source's URL.
     */
    public function import($input)
    {
        $list = $this->_inputToSourcesList($input);
        $results = $this->_importFromSourcesList($list);

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    protected function _inputToSourcesList($input)
    {
        $lines = explode("\n", $input);
        $sources = array();
        foreach ($lines as $_line) {
            $parts = array_map('trim', explode(',', $_line) );
            $sources[] = array(
                ImporterInterface::SK_TITLE        => isset($parts[0]) ? $parts[0] : null,
                ImporterInterface::SK_URL          => isset($parts[1]) ? $parts[1] : null,
            );
        }

        return $sources;
    }
}