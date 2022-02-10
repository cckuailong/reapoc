<?php
namespace Braintree;

/**
 * Braintree GraphQL Client
 * process GraphQL requests using curl
 */
class GraphQLClient
{
    public function __construct($config)
    {
        $this->_service = new GraphQL($config);
    }

    public function query($definition, $variables = Null)
    {
        return $this->_service->request($definition, $variables);
    }
}
