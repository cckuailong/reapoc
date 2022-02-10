<?php

namespace Paymill\Models\Response;

/**
 * Client Response Model
 * The clients object is used to edit, delete, update clients as well as to permit refunds, subscriptions, 
 * insert credit card details for a client, edit client details and of course make transactions. 
 * Clients can be created individually by you or they will be automatically generated with the transaction 
 * if there is no client ID transmitted.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-clients
 */
class Client extends Base
{
    /**
     * Email address of the customer
     * @var string 
     */
    private $_email;
    
    /**
     * Additional description for this client
     * @var string 
     */
    private $_description;
    
    /**
     * Instance of the payment response model class representing the payment stored in the client data
     * @var array|\Paymill\Models\Response\Payment
     */
    private $_payment;
    
    /**
     * Instance of the subscription response model class representing the subscription stored in the client
     * @var \Paymill\Models\Response\Subscription|null
     */
    private $_subscription;

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
     * @return \Paymill\Models\Response\Client
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
     * @return \Paymill\Models\Response\Client
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Returns a list of payment objects associated with this client
     * @return \Paymill\Models\Response\Payment
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Sets the payment list stored in the client model
     * @param \Paymill\Models\Response\Payment $payment
     * @return \Paymill\Models\Response\Client
     */
    public function setPayment($payment)
    {
        $this->_payment = $payment;
        return $this;
    }

    /**
     * Returns a list of subscription objects associated with this client
     * @return array
     */
    public function getSubscription()
    {
        return $this->_subscription;
    }

    /**
     * Sets the subscription list stored in the client model
     * @param array $subscription
     * @return \Paymill\Models\Response\Client
     */
    public function setSubscription($subscription)
    {
        $this->_subscription = $subscription;
        return $this;
    }

}
