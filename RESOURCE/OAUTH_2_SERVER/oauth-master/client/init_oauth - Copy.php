<?php

require_once 'bootstrap.php';

$authorizationUrl = $provider->getAuthorizationUrl(
   ['scope' => 'protected_resource_access']
);

$_SESSION['oauth2state'] = $provider->getState();
echo $client_id;
echo "===========";
echo $client_secret;
header('Location: ' . $authorizationUrl);