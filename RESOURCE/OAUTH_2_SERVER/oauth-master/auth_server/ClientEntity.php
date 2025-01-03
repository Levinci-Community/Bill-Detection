<?php

class ClientEntity implements \League\OAuth2\Server\Entities\ClientEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\ClientTrait;
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;
    private $redirect_uri;
    public function __construct($redirect_uri)
    {   
        $this->redirect_uri = $redirect_uri;
        $this->name = 'The client App';
        $this->setIdentifier(uniqid());
        $this->redirectUri = $this->redirect_uri;
    }
}