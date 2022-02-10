<?php

namespace GeminiLabs\SiteReviews\Modules;

class Rating
{
    /**
     * The more sure we are of the confidence interval (the higher the confidence level), the less
     * precise the estimation will be as the margin for error will be higher.
     * @see http://homepages.math.uic.edu/~bpower6/stat101/Confidence%20Intervals.pdf
     * @see https://www.thecalculator.co/math/Confidence-Interval-Calculator-210.html
     * @see https://www.youtube.com/watch?v=grodoLzThy4
     * @see https://en.wikipedia.org/wiki/Standard_score
     * @var array
     */
    const CONFIDENCE_LEVEL_Z_SCORES = [
        50 => 0.67449,
        70 => 1.04,
        75 => 1.15035,
        80 => 1.282,
        85 => 1.44,
        90 => 1.64485,
        92 => 1.75,
        95 => 1.95996,
        96 => 2.05,
        97 => 2.17009,
        98 => 2.326,
        99 => 2.57583,
        '99.5' => 2.81,
        '99.8' => 3.08,
        '99.9' => 3.29053,
    ];

    /**
     * @var int
     */
    const MAX_RATING = 5;

    /**
     * @var int
     */
    const MIN_RATING = 0;

    /**
     * @param int $roundBy
     * @return float
     */
    public function average(array $ratingCounts, $roundBy = 1)
    {
        $average = array_sum($ratingCounts);
        if ($average > 0) {
            $average = $this->totalSum($ratingCounts) / $average;
        }
        $roundedAverage = round($average, intval($roundBy));
        return glsr()->filterFloat('rating/average', $roundedAverage, $average, $ratingCounts);
    }

    /**
     * @return array
     */
    public function emptyArray()
    {
        return array_fill_keys(range(0, glsr()->constant('MAX_RATING', __CLASS__)), 0);
    }

    /**
     * @param int|string $rating
     * @return bool
     */
    public function isValid($rating)
    {
        return array_key_exists($rating, $this->emptyArray());
    }

    /**
     * Get the lower bound for up/down ratings
     * Method receives an up/down ratings array: [1, -1, -1, 1, 1, -1].
     * @see http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
     * @see https://news.ycombinator.com/item?id=10481507
     * @see https://dataorigami.net/blogs/napkin-folding/79030467-an-algorithm-to-sort-top-comments
     * @see http://julesjacobs.github.io/2015/08/17/bayesian-scoring-of-ratings.html
     * @param int $confidencePercentage
     * @return int|float
     */
    public function lowerBound(array $upDownCounts = [0, 0], $confidencePercentage = 95)
    {
        $numRatings = array_sum($upDownCounts);
        if ($numRatings < 1) {
            return 0;
        }
        $z = static::CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
        $phat = 1 * $upDownCounts[1] / $numRatings;
        return ($phat + $z * $z / (2 * $numRatings) - $z * sqrt(($phat * (1 - $phat) + $z * $z / (4 * $numRatings)) / $numRatings)) / (1 + $z * $z / $numRatings);
    }

    /**
     * @param array $noopedPlural The result of _n_noop()
     * @param int $minRating
     * @return array
     */
    public function optionsArray($noopedPlural, $minRating = 1)
    {
        $options = [];
        foreach (range(glsr()->constant('MAX_RATING', __CLASS__), $minRating) as $rating) {
            $options[$rating] = sprintf(translate_nooped_plural($noopedPlural, $rating, 'site-reviews'), $rating);
        }
        return $options;
    }

    /**
     * @return int|float
     */
    public function overallPercentage(array $ratingCounts)
    {
        return round($this->average($ratingCounts) * 100 / glsr()->constant('MAX_RATING', __CLASS__), 2);
    }

    /**
     * @return array
     */
    public function percentages(array $ratingCounts)
    {
        if (empty($ratingCounts)) {
            $ratingCounts = $this->emptyArray();
        }
        $total = array_sum($ratingCounts);
        foreach ($ratingCounts as $index => $count) {
            if (empty($count)) {
                continue;
            }
            $ratingCounts[$index] = $count / $total * 100;
        }
        return $this->roundedPercentages($ratingCounts);
    }

    /**
     * @return float
     */
    public function ranking(array $ratingCounts)
    {
        return glsr()->filterFloat('rating/ranking',
            $this->rankingUsingImdb($ratingCounts),
            $ratingCounts,
            $this
        );
    }

    /**
     * Get the bayesian ranking for an array of reviews
     * This formula is the same one used by IMDB to rank their top 250 films.
     * @see https://www.xkcd.com/937/
     * @see https://districtdatalabs.silvrback.com/computing-a-bayesian-estimate-of-star-rating-means
     * @see http://fulmicoton.com/posts/bayesian_rating/
     * @see https://stats.stackexchange.com/questions/93974/is-there-an-equivalent-to-lower-bound-of-wilson-score-confidence-interval-for-va
     * @param int $confidencePercentage
     * @return int|float
     */
    public function rankingUsingImdb(array $ratingCounts, $confidencePercentage = 70)
    {
        $avgRating = $this->average($ratingCounts);
        // Represents a prior (your prior opinion without data) for the average star rating. A higher prior also means a higher margin for error.
        // This could also be the average score of all items instead of a fixed value.
        $bayesMean = ($confidencePercentage / 100) * glsr()->constant('MAX_RATING', __CLASS__); // prior, 70% = 3.5
        // Represents the number of ratings expected to begin observing a pattern that would put confidence in the prior.
        $bayesMinimal = 10; // confidence
        $numOfReviews = array_sum($ratingCounts);
        return $avgRating > 0
            ? (($bayesMinimal * $bayesMean) + ($avgRating * $numOfReviews)) / ($bayesMinimal + $numOfReviews)
            : 0;
    }

    /**
     * The quality of a 5 star rating depends not only on the average number of stars but also on
     * the number of reviews. This method calculates the bayesian ranking of a page by its number
     * of reviews and their rating.
     * @see http://www.evanmiller.org/ranking-items-with-star-ratings.html
     * @see https://stackoverflow.com/questions/1411199/what-is-a-better-way-to-sort-by-a-5-star-rating/1411268
     * @see http://julesjacobs.github.io/2015/08/17/bayesian-scoring-of-ratings.html
     * @param int $confidencePercentage
     * @return float
     */
    public function rankingUsingZScores(array $ratingCounts, $confidencePercentage = 90)
    {
        $ratingCountsSum = array_sum($ratingCounts) + glsr()->constant('MAX_RATING', __CLASS__);
        $weight = $this->weight($ratingCounts, $ratingCountsSum);
        $weightPow2 = $this->weight($ratingCounts, $ratingCountsSum, true);
        $zScore = static::CONFIDENCE_LEVEL_Z_SCORES[$confidencePercentage];
        return $weight - $zScore * sqrt(($weightPow2 - pow($weight, 2)) / ($ratingCountsSum + 1));
    }

    /**
     * Returns array sorted by key DESC.
     * @param int $totalPercent
     * @return array
     */
    protected function roundedPercentages(array $percentages, $totalPercent = 100)
    {
        array_walk($percentages, function (&$percent, $index) {
            $percent = [
                'index' => $index,
                'percent' => floor($percent),
                'remainder' => fmod($percent, 1),
            ];
        });
        $indexes = wp_list_pluck($percentages, 'index');
        $remainders = wp_list_pluck($percentages, 'remainder');
        array_multisort($remainders, SORT_DESC, SORT_STRING, $indexes, SORT_DESC, $percentages);
        $i = 0;
        if (array_sum(wp_list_pluck($percentages, 'percent')) > 0) {
            while (array_sum(wp_list_pluck($percentages, 'percent')) < $totalPercent) {
                ++$percentages[$i]['percent'];
                ++$i;
            }
        }
        array_multisort($indexes, SORT_DESC, $percentages);
        return array_combine($indexes, wp_list_pluck($percentages, 'percent'));
    }

    /**
     * @return int
     */
    protected function totalSum(array $ratingCounts)
    {
        return array_reduce(array_keys($ratingCounts), function ($carry, $index) use ($ratingCounts) {
            return $carry + ($index * $ratingCounts[$index]);
        });
    }

    /**
     * @param int|float $ratingCountsSum
     * @param bool $powerOf2
     * @return float
     */
    protected function weight(array $ratingCounts, $ratingCountsSum, $powerOf2 = false)
    {
        return array_reduce(array_keys($ratingCounts),
            function ($count, $rating) use ($ratingCounts, $ratingCountsSum, $powerOf2) {
                $ratingLevel = $powerOf2
                    ? pow($rating, 2)
                    : $rating;
                return $count + ($ratingLevel * ($ratingCounts[$rating] + 1)) / $ratingCountsSum;
            }
        );
    }
}
