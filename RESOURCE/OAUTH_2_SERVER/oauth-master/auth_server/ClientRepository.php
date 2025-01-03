<?php
class ClientRepository implements \League\OAuth2\Server\Repositories\ClientRepositoryInterface
{
    private $redirect_uri;

    public function __construct($redirect_uri)
    {   
        $this->redirect_uri = $redirect_uri;
    }
    /**
     * @inheritDoc
     */
    public function getClientEntity($clientIdentifier)
    {
        $theClient = new ClientEntity($this->redirect_uri);
        $theClient->setIdentifier($clientIdentifier);
        return $theClient;
    }

    /**
     * @inheritDoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        return true;
    }
}