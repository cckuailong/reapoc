<?php

namespace Paymill;

use Exception;
use Paymill\API\CommunicationAbstract;
use Paymill\API\Curl;
use Paymill\Models\Request\Base;
use Paymill\Models\Response\Error;
use Paymill\Services\PaymillException;
use Paymill\Services\ResponseHandler;

/**
 * Base
 * @version 3.2.1
 */
class Request
{

    /**
     * @var \Paymill\API\CommunicationAbstract|Curl
     */
    private $_connectionClass;

    /**
     * @var array
     */
    private $_lastResponse;

    /**
     * @var array
     */
    private $_lastRequest;

    /**
     * @var string
     */
    private $_version = "3.2.1";

    /**
     * @var string
     */
    private $_source;

  /**
     * @var \Paymill\Services\Util
     */
    private $_util;


    /**
     * Creates a Request object instance
     * @param string|null $privateKey
     */
    public function __construct($privateKey = null)
    {
        $this->_util = new \Paymill\Services\Util();
    if(!is_null($privateKey)){
            $this->setConnectionClass(new Curl($privateKey));
        }
    }

    /**
     * @param \Paymill\API\CommunicationAbstract|Curl $communicationClass
     * @return $this
     */
    public function setConnectionClass(CommunicationAbstract $communicationClass = null)
    {
        $this->_connectionClass = $communicationClass;
        return $this;
    }

    /**
     * Sends a creation request using the provided model
     * @param \Paymill\Models\Request\Base $model
     * @throws PaymillException
     * @return \Paymill\Models\Response\Base
     */
    public function create($model)
    {
        return $this->_request($model, __FUNCTION__);
    }

    /**
     * Sends an update request using the provided model
     * @param \Paymill\Models\Request\Base $model
     * @throws PaymillException
     * @return \Paymill\Models\Response\Base
     */
    public function update($model)
    {
        return $this->_request($model, __FUNCTION__);
    }

    /**
     * Sends a delete request using the provided model
     * @param \Paymill\Models\Request\Base $model
     * @throws PaymillException
     * @return \Paymill\Models\Response\Base
     */
    public function delete($model)
    {
        return $this->_request($model, __FUNCTION__);
    }

    /**
     * Sends a getAll request using the provided model
     * @param \Paymill\Models\Request\Base $model
     * @throws PaymillException
     * @return array
     */
    public function getAll($model)
    {
        return $this->_request($model, __FUNCTION__);
    }

    /**
     * Sends a getAll request using the provided model
     * @param \Paymill\Models\Request\Base $model
     * @throws PaymillException
     * @return array of \Paymill\Models\Request\Base
     */
    public function getAllAsModel($model)
    {
        return $this->_request($model, __FUNCTION__);
    }

    /**
     * Sends a getOne request using the provided model
     * @param \Paymill\Models\Request\Base $model
     * @throws PaymillException
     * @return \Paymill\Models\Response\Base
     */
    public function getOne($model)
    {
        return $this->_request($model, __FUNCTION__);
    }

    /**
     * Returns the response of the last request
     * @return array
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    /**
     * Returns the parameter which were used for the last request
     * @return array
     */
    public function getLastRequest()
    {
        return $this->_lastRequest;
    }

    /**
     * Returns the Version of this Lib
     *
     * @return string
     */
    public function getVersion(){
        return $this->_version;
    }

        /**
     * Returns the LastResponse as StdClassObject. Returns false if no request was made earlier.
     *
     * @return false | stdClass
     */
    public function getJSONObject(){
        $result = false;
        $responseHandler = new ResponseHandler();
        if(is_array($this->_lastResponse)){
            $result = $responseHandler->arrayToObject($this->_lastResponse['body']);
        }
        return $result;
    }

    /**
     *
     * @param string $method
     * @return string
     */
    private function _getHTTPMethod($method)
    {
        $httpMethod = 'POST';
        switch ($method) {
            case 'create':
                $httpMethod = 'POST';
                break;
            case 'update':
                $httpMethod = 'PUT';
                break;
            case 'delete':
                $httpMethod = 'DELETE';
                break;
            case 'getAll':
            case 'getAllAsModel':
            case 'getOne':
                $httpMethod = 'GET';
                break;
        }
        return $httpMethod;
    }

    /**
     * Sends a request based on the provided request model and according to the argumented method
     * @param \Paymill\Models\Request\Base $model
     * @param string $method (Create, update, delete, getAll, getOne)
     * @throws PaymillException
     * @return \Paymill\Models\Response\Base|\Paymill\Models\Response\Error
     */
    private function _request(Base $model, $method)
    {
        if(!is_a($this->_connectionClass, '\Paymill\API\CommunicationAbstract')){
            throw new PaymillException(null,'The connection class is missing!');
        }

        $convertedResponse = null;
        $httpMethod = $this->_getHTTPMethod($method);
        $parameter = $model->parameterize($method);

        $serviceResource = $model->getServiceResource() . $model->getId();

        if ((is_a($model, '\Paymill\Models\Request\Transaction')
                || is_a($model, '\Paymill\Models\Request\Preauthorization'))
            && $method === "create"
        ) {
            $source = !array_key_exists('source', $parameter) ?
                "PhpLib" . $this->getVersion() :
                "PhpLib" . $this->getVersion() . "_" . $parameter['source'];
            $parameter['source'] = $source;
        }

        try {
            $this->_lastRequest = $parameter;
            $response = $this->_connectionClass->requestApi(
                $serviceResource, $parameter, $httpMethod
            );
            $this->_lastResponse = $response;
            $responseHandler = new ResponseHandler();
            if($method === "getAllAsModel"
                && $responseHandler->validateResponse($response)
                && $this->_util->isNumericArray($response['body']['data'])
            ) {
                foreach($response['body']['data'] as $object){
                    $convertedResponse[] = $responseHandler->convertResponse($object, $model->getServiceResource());
                }
            } elseif($method === "getAll" && $responseHandler->validateResponse($response)) {
                $convertedResponse = $response['body']['data'];
            } elseif($responseHandler->validateResponse($response)) {
                $convertedResponse = $responseHandler->convertResponse(
                    $response['body']['data'], $model->getServiceResource()
                );
            } else {
                $convertedResponse = $responseHandler->convertErrorToModel($response, $model->getServiceResource());
            }
        } catch (Exception $e) {
            $errorModel = new Error();
            $convertedResponse = $errorModel->setErrorMessage($e->getMessage());
        }

        if (is_a($convertedResponse, '\Paymill\Models\Response\Error')) {
            throw new PaymillException(
                $convertedResponse->getResponseCode(),
                $convertedResponse->getErrorMessage(),
                $convertedResponse->getHttpStatusCode(),
                $convertedResponse->getRawObject(),
                $convertedResponse->getErrorResponseArray()
            );
        }

        return $convertedResponse;
    }

    /**
     * Sets the source for requests
     *
     * @param string $source
     * @return \Paymill\Request
     */
    public function setSource($source){
        if(is_string($source)){
            $this->_source = $source;
        }
        return $this;
    }

    /**
     * Returns the source for requests
     *
     * @return string
     */
    public function getSource(){
        return $this->_source;
    }

}
