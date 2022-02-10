<?php

namespace GeminiLabs\SiteReviews\Modules\Avatars;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class InitialsAvatar extends SvgAvatar
{
    /**
     * @param string $from
     * @return string
     */
    public function generate($from)
    {
        $colors = [
            ['background' => '#e3effb', 'color' => '#134d92'], // blue
            ['background' => '#e1f0ee', 'color' => '#125960'], // green
            ['background' => '#ffeff7', 'color' => '#ba3a80'], // pink
            ['background' => '#fcece3', 'color' => '#a14326'], // red
            ['background' => '#faf7d9', 'color' => '#da9640'], // yellow
        ];
        $colors = glsr()->filterArray('avatar/colors', $colors);
        shuffle($colors);
        $color = Cast::toArray(Arr::get($colors, 0));
        $data = wp_parse_args($color, [
            'background' => '#dcdce6',
            'color' => '#6f6f87',
            'text' => $this->filename($from),
        ]);
        return trim(glsr()->build('avatar', $data));
    }

    /**
     * @param string $from
     * @return string
     */
    protected function filename($from)
    {
        $initials = Str::convertToInitials($from);
        if (mb_strlen($initials) === 1) {
            $initials = mb_substr(trim($from), 0, 2, 'UTF-8');
            $initials = mb_strtoupper($initials, 'UTF-8');
        }
        $initials = mb_substr($initials, 0, 2, 'UTF-8');
        return $initials;
    }
}
