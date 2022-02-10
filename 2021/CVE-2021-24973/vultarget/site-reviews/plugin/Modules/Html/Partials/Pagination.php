<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class Pagination implements PartialContract
{
    /**
     * @var array
     */
    protected $args;

    /**
     * {@inheritdoc}
     */
    public function build(array $args = [])
    {
        $this->args = $this->normalize($args);
        if ($this->args['total'] > 1) {
            return glsr(Template::class)->build('templates/pagination', [
                'context' => [
                    'links' => glsr()->filterString('paginate_links', $this->buildLinks(), $this->args),
                    'loader' => '<div class="glsr-loader"><div class="glsr-spinner"></div></div>',
                    'screen_reader_text' => _x('Site Reviews navigation', 'screen reader text', 'site-reviews'),
                ],
            ]);
        }
    }

    /**
     * @return string
     */
    protected function buildFauxLinks()
    {
        $links = (array) paginate_links(wp_parse_args(['type' => 'array'], $this->args));
        $pattern = '/(href=["\'])([^"\']*?)(["\'])/i';
        foreach ($links as &$link) {
            if (preg_match($pattern, $link, $matches)) {
                $page = Helper::getPageNumber(Arr::get($matches, 2));
                $replacement = sprintf('data-page="%d" href="#"', $page);
                $link = str_replace(Arr::get($matches, 0), $replacement, $link);
            }
        }
        return implode("\n", $links);
    }

    /**
     * @return string
     */
    protected function buildLinks()
    {
        $useUrlParams = glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter');
        return ($useUrlParams || $this->args['type'] !== 'ajax')
            ? paginate_links(wp_parse_args(['type' => 'plain'], $this->args))
            : $this->buildFauxLinks();
    }

    /**
     * @return array
     */
    protected function normalize(array $args)
    {
        if ($baseUrl = Arr::get($args, 'baseUrl')) {
            $args['base'] = $baseUrl.'%_%';
        }
        $args = wp_parse_args(array_filter($args), [
            'current' => Helper::getPageNumber(),
            'total' => 1,
        ]);
        return glsr(Style::class)->paginationArgs($args);
    }
}
