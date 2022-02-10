<?php

namespace Paymill\Models\Request;

/**
 * Webhook Model
 * With webhooks we give you the possibility to react automatically to certain events which happen within our system.
 * A webhook is basically a URL where we send an HTTP POST request to, every time one of the events attached to that
 * webhook is triggered. Alternatively you can define an email address where we send the event’s information to
 * You can manage your webhooks via the API as explained below or you can use the web interface inside our cockpit.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-webhooks
 */
class Webhook extends Base
{
    /**
     * @var string
     */
    private $_url = null;
    /**
     * @var string
     */
    private $_email = null;
    /**
     * @var array
     */
    private $_eventTypes;

    /**
     * @var boolean
     */
    private $_active = false;

    /**
     * Creates an instance of the webhook request model
     */
    function __construct()
    {
        $this->_serviceResource = 'Webhooks/';
    }

    /**
     * Returns the webhook url
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Sets the webhook url
     * @param string $url
     * @return \Paymill\Models\Request\Webhook
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Returns the event types as an array
     * @return array
     */
    public function getEventTypes()
    {
        return $this->_eventTypes;
    }

    /**
     * Sets the event types for the webhook.
     * There are a number of events you can react to. Each webhook can be configured to catch any kind of event
     * individually, so you can create different webhooks for different events. Each Webhook needs to be attached
     * to at least one event. For example the event subscription.succeeded is triggered every time a successful
     * transaction has been made in our system that is based on a subscription. Shortly after that has been triggered,
     * we will call every webhook you defined for this event and send detailed information to it.
     * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-webhooks
     * @param array $eventTypes
     * @return \Paymill\Models\Request\Webhook
     */
    public function setEventTypes($eventTypes)
    {
        $this->_eventTypes = $eventTypes;
        return $this;
    }

    /**
     * Returns the email registered for this webhook
     * @return string||null
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets the email for the webhook.
     * @param string $email Instead of setting the url parameter you can set the email parameter to create a webhook,
     *  where we send mails to in case of an event.
     * @return \Paymill\Models\Request\Webhook
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * Sets webhook active (or inactive)
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->_active = $active;
        return $this;
    }

    /**
     * Returns if webhook is active or inactive
     * @param boolean $active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->_active;
    }

    /**
     * Returns an array of parameters customized for the argumented methodname
     * @param string $method
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = array();
        switch ($method) {
            case 'create':
                if(!is_null($this->getUrl())){
                    $parameterArray['url'] = $this->getUrl();
                }else{
                    $parameterArray['email'] = $this->getEmail();
                }
                $parameterArray['event_types'] = $this->getEventTypes();
                if (!is_null($this->getActive())) {
                    $parameterArray['active'] = $this->getActive();
                }
                break;
            case 'update':
                if(!is_null($this->getUrl())){
                    $parameterArray['url'] = $this->getUrl();
                }else{
                    $parameterArray['email'] = $this->getEmail();
                }
                $parameterArray['event_types'] = $this->getEventTypes();
                if (!is_null($this->getActive())) {
                    $parameterArray['active'] = $this->getActive();
                }
                break;
            case 'delete':
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
        }

        return $parameterArray;
    }
}
