<?php

class UserEntity implements \League\OAuth2\Server\Entities\UserEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        //get user id
        $user_id = $_SESSION['user_id'];
        return $user_id;
    }
}