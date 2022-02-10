<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport;

use Exception;

/**
 * A simple feed source importer that imports from an array of URLs mapping to feed source names.
 *
 * @since 4.12
 */
class ArrayImporter extends AbstractWpImporter implements ImporterInterface
{
    /**
     * Constructor.
     *
     * @since 4.12
     *
     * @param array    $data       Data members map.
     * @param callable $translator A translator.
     *
     * @throws Exception If the translator is invalid.
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
     * @since 4.12
     */
    protected function _inputToSourcesList($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sources = array();
        foreach ($input as $k => $v) {
            $sources[] = array(
                ImporterInterface::SK_URL => $k,
                ImporterInterface::SK_TITLE => $v,
                'status' => 'publish',
            );
        }

        return $sources;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.12
     */
    public function import($input)
    {
        $list = $this->_inputToSourcesList($input);
        $results = $this->_importFromSourcesList($list);

        return $results;
    }
}
