<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\Exception;
use GeminiLabs\League\Csv\Reader;
use GeminiLabs\League\Csv\Statement;
use GeminiLabs\League\Csv\TabularDataReader;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Upload;

class ImportReviews extends Upload implements Contract
{
    const ALLOWED_DATE_FORMATS = [
        'd-m-Y', 'd-m-Y H:i', 'd-m-Y H:i:s',
        'd/m/Y', 'd/m/Y H:i', 'd/m/Y H:i:s',
        'm-d-Y', 'm-d-Y H:i', 'm-d-Y H:i:s',
        'm/d/Y', 'm/d/Y H:i', 'm/d/Y H:i:s',
        'Y-m-d', 'Y-m-d H:i', 'Y-m-d H:i:s',
        'Y/m/d', 'Y/m/d H:i', 'Y/m/d H:i:s',
    ];

    const ALLOWED_DELIMITERS = [
        ',', ';',
    ];

    const REQUIRED_KEYS = [
        'content', 'date', 'rating',
    ];

    /**
     * @var string
     */
    protected $date_format = 'Y-m-d';

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * @var string[]
     */
    protected $errors = [];

    /**
     * @var int
     */
    protected $totalRecords = 0;

    public function __construct(Request $request)
    {
        $this->date_format = Str::restrictTo(static::ALLOWED_DATE_FORMATS, $request->date_format, 'Y-m-d');
        $this->delimiter = Str::restrictTo(static::ALLOWED_DELIMITERS, $request->delimiter, ',');
        $this->errors = [];
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!$this->validateUpload()
            || !$this->validateExtension('.csv')) {
            return;
        }
        glsr()->store('import', true);
        $result = $this->import();
        glsr()->discard('import');
        if (false !== $result) {
            $this->notify($result);
        }
    }

    /**
     * @return int|bool
     */
    protected function import()
    {
        define('WP_IMPORTING', true);
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }
        require_once glsr()->path('vendors/thephpleague/csv/functions_include.php');
        try {
            wp_raise_memory_limit('admin');
            $reader = Reader::createFromPath($this->file()->tmp_name);
            $reader->setDelimiter($this->delimiter);
            $reader->setHeaderOffset(0);
            $reader->skipEmptyRecords();
            $header = array_map('trim', $reader->getHeader());
            if (!empty(array_diff(static::REQUIRED_KEYS, $header))) {
                throw new Exception('The CSV import header is missing some of the required columns (or maybe you selected the correct delimiter).');
            }
            $this->totalRecords = count($reader);
            $records = Statement::create()
                ->where(function ($record) {
                    return $this->validateRecord($record);
                })
                ->process($reader, $header);
            return $this->importRecords($records);
        } catch (Exception $e) {
            glsr(Notice::class)->addError($e->getMessage());
            return false;
        }
    }

    /**
     * @return int
     */
    protected function importRecords(TabularDataReader $records)
    {
        foreach ($records as $offset => $record) {
            $date = \DateTime::createFromFormat($this->date_format, $record['date']);
            $record['date'] = $date->format('Y-m-d H:i:s'); // format the provided date
            $request = new Request($record);
            $command = new CreateReview($request);
            glsr(ReviewManager::class)->create($command);
        }
        glsr(Queue::class)->async('queue/recalculate-meta');
        return count($records);
    }

    /**
     * @return void
     */
    protected function notify($result)
    {
        $skippedRecords = max(0, $this->totalRecords - $result);
        $notice = sprintf(
            _nx('%s review was imported.', '%s reviews were imported.', $result, 'admin-text', 'site-reviews'),
            number_format_i18n($result)
        );
        if (0 === $skippedRecords) {
            glsr(Notice::class)->addSuccess($notice);
            return;
        }
        $skipped = sprintf(
            _nx('%s entry was skipped.', '%s entries were skipped.', $skippedRecords, 'admin-text', 'site-reviews'),
            number_format_i18n($skippedRecords)
        );
        $consoleLink = sprintf(_x('See the %s for more details.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">%s</a>)',
                glsr_admin_url('tools', 'console'),
                _x('Console', 'admin-text', 'site-reviews')
            )
        );
        glsr(Notice::class)->addWarning(sprintf('%s (%s)', sprintf('%s %s', $notice, $skipped), $consoleLink));
        glsr_log()->warning(sprintf('One or more of the following errors were encountered during import: %s', Str::naturalJoin($this->errors)));
    }

    /**
     * @return bool
     */
    protected function validateRecord(array $record)
    {
        $required = [
            'date' => glsr(Date::class)->isDate(Arr::get($record, 'date'), $this->date_format),
            'content' => !empty($record['content']),
            'rating' => glsr(Rating::class)->isValid(Arr::get($record, 'rating')),
        ];
        if (3 === count(array_filter($required))) {
            return true;
        }
        $errorMessages = [
            'date' => _x('wrong date format', 'admin-text', 'site-reviews'),
            'content' => _x('empty or missing content', 'admin-text', 'site-reviews'),
            'rating' => _x('empty or invalid rating', 'admin-text', 'site-reviews'),
        ];
        $errors = array_intersect_key($errorMessages, array_diff_key($required, array_filter($required)));
        $this->errors = array_merge($this->errors, $errors);
        return false;
    }
}
