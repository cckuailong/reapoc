<?php

namespace Paymill\Models\Request;

/**
 * Client Model
 * The clients object is used to edit, delete, update clients as well as to permit refunds, subscriptions,
 * insert credit card details for a client, edit client details and of course make transactions.
 * Clients can be created individually by you or they will be automatically generated with the transaction
 * if there is no client ID transmitted.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-clients
 */
class Client extends Base
{
    
    /**
     * @var string
     */
    private $_email;
    /**
     * @var string
     */
    private $_description;

    /**
     * Creates an instance of the client request model
     */
    public function __construct()
    {
        $this->_serviceResource = 'clients/';
    }

    /**
     * Returns the Mail address of this client.
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets the Mail address of this client.
     * @param string $email
     * @return \Paymill\Models\Request\Client
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * Returns the additional description for this client, perhaps the identifier from your CRM system?
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets an additional description for this client. We recommend some sort of identifier from your CRM system
     * @param string $description
     * @return \Paymill\Models\Request\Client
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
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
            case 'update':
                $parameterArray['email'] = $this->getEmail();
                $parameterArray['description'] = $this->getDescription();
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
            case 'delete':
                break;
        }

        return $parameterArray;
    }
}
