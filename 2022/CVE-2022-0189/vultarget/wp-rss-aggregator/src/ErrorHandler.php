<?php /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */

namespace RebelCode\Wpra\Core;

use Dhii\I18n\StringTranslatingTrait;
use Exception;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Handles errors.
 *
 * @since 4.14
 */
class ErrorHandler
{
    /*
     * Provides string translating functionality.
     *
     * @since 4.14
     */
    use StringTranslatingTrait;

    /**
     * The callback to invoke.
     *
     * @since 4.14
     *
     * @var callable
     */
    protected $callback;

    /*
     * The previous exception handler.
     *
     * @since 4.14
     */
    protected $previous;

    /**
     * The root directory for which to limit exception handling.
     *
     * @since 4.14
     *
     * @var string
     */
    protected $rootDir;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param string   $rootDir  The root directory for which to limit exception handling.
     * @param callable $callback The callback to invoke when an exception is handled. The callback will receive the
     *                           exception or PHP7 {@see \Throwable} as argument.
     */
    public function __construct($rootDir, callable $callback)
    {
        $this->rootDir = $rootDir;
        $this->callback = $callback;
        $this->previous = null;
    }

    /**
     * Registers the handler.
     *
     * @since 4.14
     */
    public function register()
    {
        $this->previous = set_exception_handler($this);
    }

    /**
     * De-registers the handler.
     *
     * @since 4.14
     */
    public function deregister()
    {
        set_exception_handler($this->previous);
    }

    /**
     * @since 4.14
     */
    public function __invoke()
    {
        if ($this->previous) {
            call_user_func_array($this->previous, func_get_args());
        }

        $throwable = func_get_arg(0);

        if (!($throwable instanceof Exception) && !($throwable instanceof Throwable)) {
            return;
        }

        if ($this->isErrorFromRootDir($throwable->getFile())) {
            $this->handleError($throwable);

            return;
        }

        // Detect an exception thrown from within the root directory
        foreach ($throwable->getTrace() as $trace) {
            if (array_key_exists('file', $trace) && $this->isErrorFromRootDir($trace['file'])) {
                $this->handleError($throwable);
            }
        }
    }

    /**
     * Checks if an error path is from the root directory.
     *
     * @since 4.14
     *
     * @param string $path The path of the error.
     *
     * @return bool
     */
    protected function isErrorFromRootDir($path)
    {
        return stripos($path, $this->rootDir) === 0;
    }

    /**
     * Handles errors.
     *
     * @since 4.14
     *
     * @param Exception|Throwable $throwable
     */
    protected function handleError($throwable)
    {
        // Attempt to log the error
        try {
            wpra_get_logger()->log(
                LogLevel::ERROR,
                'Exception: "{msg}", at {file} line {line}',
                [
                    'msg' => $throwable->getMessage(),
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                ]
            );
        } catch (Exception $exception) {
            // Ignore
        }

        if (defined('REST_REQUEST')) {
            wp_send_json_error(['error' => $throwable->getMessage(), 'trace' => $throwable->getTrace()], 500);

            return;
        }

        if (is_callable($this->callback)) {
            call_user_func_array($this->callback, [$throwable]);
        }
    }
}
