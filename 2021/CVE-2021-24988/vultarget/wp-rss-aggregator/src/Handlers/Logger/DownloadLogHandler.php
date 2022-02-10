<?php

namespace RebelCode\Wpra\Core\Handlers\Logger;

use RebelCode\Wpra\Core\Logger\LogReaderInterface;
use WP_Post;

/**
 * Handles log download requests.
 *
 * @since 4.14
 */
class DownloadLogHandler
{
    /**
     * @var LogReaderInterface
     */
    protected $reader;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $nonceName;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param LogReaderInterface $reader    The log reader.
     * @param string             $nonceName The name of the nonce to verify requests.
     */
    public function __construct(LogReaderInterface $reader, $nonceName)
    {
        $this->reader = $reader;
        $this->nonceName = $nonceName;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke()
    {
        $downloadLog = filter_input(INPUT_POST, 'wpra-download-log', FILTER_DEFAULT);

        if (empty($downloadLog) || !check_admin_referer($this->nonceName)) {
            return;
        }

        $logs = $this->reader->getLogs();

        $output = '';
        foreach ($logs as $log) {
            $feed = isset($log['feed_id'])
                ? get_post($log['feed_id'])
                : null;
            $feedName = ($feed instanceof WP_Post)
                ? ' ' . $feed->post_title . ':'
                : '';
            $output .= sprintf('[%s] (%s)%s %s', $log['date'], $log['level'], $feedName, $log['message']) . PHP_EOL;
        }

        header('Content-Description: File Transfer');
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="wpra-log.txt"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($output));
        echo $output;
        exit;
    }
}
