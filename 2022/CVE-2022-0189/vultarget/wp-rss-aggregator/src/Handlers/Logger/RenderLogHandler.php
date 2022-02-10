<?php

namespace RebelCode\Wpra\Core\Handlers\Logger;

use Dhii\Output\TemplateInterface;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Logger\LogReaderInterface;

/**
 * The handler that renders the log.
 *
 * @since 4.14
 */
class RenderLogHandler
{
    /**
     * @since 4.14
     *
     * @var LogReaderInterface
     */
    protected $reader;

    /**
     * @since 4.14
     *
     * @var TemplateInterface
     */
    protected $template;

    /**
     * @since 4.14
     *
     * @var DataSetInterface
     */
    protected $config;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $nonceName;

    /**
     * @since 4.14
     *
     * @var int
     */
    protected $numLogs;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param LogReaderInterface $reader    The log reader.
     * @param TemplateInterface  $template  The template to render.
     * @param DataSetInterface   $config    The logging config.
     * @param string             $nonceName The name of the nonce to use for log operations.
     * @param int                $numLogs   The number of logs to show.
     */
    public function __construct(
        LogReaderInterface $reader,
        TemplateInterface $template,
        DataSetInterface $config,
        $nonceName,
        $numLogs = 200
    ) {
        $this->reader = $reader;
        $this->template = $template;
        $this->config = $config;
        $this->numLogs = $numLogs;
        $this->nonceName = $nonceName;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke()
    {
        echo $this->template->render([
            'logs'               => $this->reader->getLogs($this->numLogs),
            'nonce_name'         => $this->nonceName,
            'config'             => $this->config,
            'show_options'       => true,
        ]);
    }
}
