<?php

namespace GeminiLabs\Sepia\PoParser;

/**
 * Class FileHandler
 * @package Sepia\PoParser\Handler
 */
class FileHandler implements HandlerInterface
{
    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @param string $filepath
     *
     * @throws \Exception
     */
    public function __construct($filepath)
    {
        if (file_exists($filepath) === false) {
        	// is it a valid url?
        	$body = @file_get_contents($filepath, NULL, stream_context_create(array(
        		'http' => array(
					'method' => "HEAD",
					'ignore_errors' => 1,
					'max_redirects' => 0
				)
        	)));
        	if (!empty($http_response_header[0]) ){
        		// should we check if response is 200?
				/*
				sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $code);
				if ($code !== 200) {
					throw new \Exception('PoParser: Input File does not exists: "' . htmlspecialchars($filepath) . '"');
				}
				*/
            } else { // nah...
            	throw new \Exception('PoParser: Input File does not exists: "' . htmlspecialchars($filepath) . '"');
            }

        } elseif (is_readable($filepath) === false) {
            throw new \Exception('PoParser: File is not readable: "' . htmlspecialchars($filepath) . '"');
        }

        $this->fileHandle = @fopen($filepath, "r");
        if ($this->fileHandle===false) {
            throw new \Exception('PoParser: Could not open file: "' . htmlspecialchars($filepath) . '"');
        }
    }

    /**
     * @return string
     */
    public function getNextLine()
    {
        return fgets($this->fileHandle);
    }

    /**
     * @return bool
     */
    public function ended()
    {
        return feof($this->fileHandle);
    }

    /**
     * @return bool
     */
    public function close()
    {
        return @fclose($this->fileHandle);
    }

    /**
     * @inheritdoc
     *
     * @param string $output
     * @param array  $params
     */
    public function save($output, $params)
    {
        $outputFilePath = isset($params['filepath']) ? $params['filepath'] : null;

        if ($outputFilePath) {
            $fileHandle = @fopen($params['filepath'], 'w');
            if ($fileHandle === false) {
                throw new \RuntimeException(
                    sprintf(
                        'Could not open filename "%s" for writing.',
                        $params['filepath']
                    )
                );
            }
        } else {
            $fileHandle = $this->fileHandle;
            if (is_resource($fileHandle) === false) {
                throw new \RuntimeException(
                    'No source file opened nor `filepath` parameter specified in FileHandler::save method.'
                );
            }
        }

        $bytesWritten = @fwrite($fileHandle, $output);
        if ($bytesWritten === false) {
            throw new \RuntimeException('Could not write data into file.');
        }

        if (isset($params['filepath'])) {
            @fclose($fileHandle);
        }
    }
}
