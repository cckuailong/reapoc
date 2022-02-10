<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

abstract class ColumnFilter
{
    protected $enabled = false;
    protected $maxWidth = 160;

    /**
     * @return string|void
     */
    abstract public function handle(array $enabledFilters = []);

    /**
     * @param string $id
     * @param string $placeholder
     * @return string
     */
    protected function filter($id, array $options, $placeholder)
    {
        return glsr(Builder::class)->select([
            'class' => ($this->enabled ? '' : 'hidden'),
            'name' => $id,
            'id' => $this->id($id),
            'options' => $options,
            'placeholder' => $placeholder,
            'style' => sprintf('max-width:%spx;', $this->maxWidth),
            'value' => $this->value($id),
        ]);
    }

    /**
     * @param string $id
     * @return string
     */
    protected function id($id)
    {
        return 'glsr-filter-by-'.$id;
    }

    /**
     * @param string $id
     * @param string $text
     * @return string
     */
    protected function label($id, $text)
    {
        return glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => $this->id($id),
            'text' => $text,
        ]);
    }

    /**
     * @param string $id
     * @return int|string
     */
    protected function value($id)
    {
        return filter_input(INPUT_GET, $id);
    }
}
