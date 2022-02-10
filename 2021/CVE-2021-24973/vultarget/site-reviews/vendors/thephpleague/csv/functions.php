<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\League\Csv;

use ReflectionClass;
use function array_fill_keys;
use function array_filter;
use function array_reduce;
use function array_unique;
use function count;
use function iterator_to_array;
use function rsort;
use function strlen;
use function strpos;
use const COUNT_RECURSIVE;

/**
 * Returns the BOM sequence found at the start of the string.
 *
 * If no valid BOM sequence is found an empty string is returned
 * 
 * @param string $str
 * @return string
 */
function bom_match($str)
{
    static $list;
    if (null === $list) {
        $list = (new ReflectionClass(ByteSequence::class))->getConstants();

        rsort($list);
    }

    foreach ($list as $sequence) {
        if (0 === strpos($str, $sequence)) {
            return $sequence;
        }
    }

    return '';
}

/**
 * Detect Delimiters usage in a {@link Reader} object.
 *
 * Returns a associative array where each key represents
 * a submitted delimiter and each value the number CSV fields found
 * when processing at most $limit CSV records with the given delimiter
 *
 * @param string[] $delimiters
 * @param int $limit
 *
 * @return int[]
 */
function delimiter_detect(Reader $csv, array $delimiters, $limit = 1)
{
    $delimiter_filter = static function ($value) {
        return 1 === strlen($value);
    };

    $record_filter = static function (array $record) {
        return 1 < count($record);
    };

    $stmt = Statement::create(null, 0, $limit);

    $delimiter_stats = static function (array $stats, $delimiter) use ($csv, $stmt, $record_filter) {
        $csv->setDelimiter($delimiter);
        $found_records = array_filter(
            iterator_to_array($stmt->process($csv)->getRecords(), false),
            $record_filter
        );

        $stats[$delimiter] = count($found_records, COUNT_RECURSIVE);

        return $stats;
    };

    $current_delimiter = $csv->getDelimiter();
    $current_header_offset = $csv->getHeaderOffset();
    $csv->setHeaderOffset(null);

    $stats = array_reduce(
        array_unique(array_filter($delimiters, $delimiter_filter)),
        $delimiter_stats,
        array_fill_keys($delimiters, 0)
    );

    $csv->setHeaderOffset($current_header_offset);
    $csv->setDelimiter($current_delimiter);

    return $stats;
}
