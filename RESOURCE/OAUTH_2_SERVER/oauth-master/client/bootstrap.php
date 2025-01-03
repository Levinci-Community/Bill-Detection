<?php
$filepath = realpath(dirname(__FILE__));
require_once($filepath . "../../db_connect.php");
$db = new DB_CONNECT();
$db_dir = $db->connect();
require_once 'vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$client_email = "admin@bill-capture.com";

$check_client = mysqli_query($db_dir, "SELECT *FROM client WHERE `client_email` = '$client_email'");
while ($retrieve_client_info = mysqli_fetch_array($check_client)) {
    $client_id = $retrieve_client_info['client_id'];
    $client_secret = $retrieve_client_info['client_secret'];
}

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    //'clientId'                => getenv('OAUTH_CLIENT_ID'),    // The client ID assigned to you by the provider
    //'clientSecret'            => getenv('OAUTH_CLIENT_SECRET'),    // The client password assigned to you by the provider
    'clientId'                => $client_id,    // The client ID assigned to you by the provider
    'clientSecret'            => $client_secret,    // The client password assigned to you by the provider
    'redirectUri'             => getenv('CLIENT_REDIRECT_URI'),
    'urlAuthorize'            => getenv('AUTHORIZATION_SERVER_AUTHORIZE_URL'),
    'urlAccessToken'          => getenv('AUTHORIZATION_SERVER_ACCESS_TOKEN_URL'),
    'urlResourceOwnerDetails' => getenv('RESOURCE_OWNER_URL'),
]);

session_start();