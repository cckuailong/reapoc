<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Url;

/**
 * @property int $page;
 * @property string $pageUrl;
 * @property array $pageUrlParameters;
 */
class NormalizePaginationArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        parent::__construct($args);
        $this->normalizePage();
        $this->normalizePageUrl();
        $this->normalizePageUrlParameters();
    }

    /**
     * Get the page number.
     * @return void
     */
    protected function normalizePage()
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        $page = $args->get('page', 0);
        $this->page = $page
            ? $page
            : Helper::getPageNumber($args->url, $this->page);
    }

    /**
     * This should return an URL with the query string removed.
     * @return void
     */
    protected function normalizePageUrl()
    {
        if ($request = glsr()->retrieve(glsr()->paged_handle)) {
            $urlPath = Url::path($request->url);
            $this->pageUrl = Url::path(Url::home()) === $urlPath
                ? Url::home()
                : Url::home($urlPath);
        } elseif (empty($this->pageUrl = get_permalink())) {
            $this->pageUrl = Url::home(Url::path($_SERVER['REQUEST_URI']));
        }
    }

    /**
     * Store the query string of the URL so that we don't lose it in pagination.
     * @return void
     */
    protected function normalizePageUrlParameters()
    {
        $args = glsr()->args(glsr()->retrieve(glsr()->paged_handle));
        $parameters = Url::queries($args->url);
        unset($parameters[glsr()->constant('PAGED_QUERY_VAR')]);
        $this->pageUrlParameters = $parameters;
    }
}
