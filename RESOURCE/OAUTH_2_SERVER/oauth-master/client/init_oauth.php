<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$filepath = realpath(dirname(__FILE__));
require_once($filepath . "../../db_connect.php");
$db = new DB_CONNECT();
$db_dir = $db->connect();
require_once 'vendor/autoload.php';


$response = array();
//require_once 'bootstrap.php';

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$cliend_id_request = $_GET['client_id'];

$check_client = mysqli_query($db_dir, "SELECT *FROM client WHERE `client_id` = '$cliend_id_request'");
while ($retrieve_client_info = mysqli_fetch_array($check_client)) {
    $client_id = $retrieve_client_info['client_id'];
    $client_secret = $retrieve_client_info['client_secret'];
    $client_redirect_uri = $retrieve_client_info['redirect_uri'];
}

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    //'clientId'                => getenv('OAUTH_CLIENT_ID'),    // The client ID assigned to you by the provider
    //'clientSecret'            => getenv('OAUTH_CLIENT_SECRET'),    // The client password assigned to you by the provider
    'clientId'                => $client_id,    // The client ID assigned to you by the provider
    'clientSecret'            => $client_secret,    // The client password assigned to you by the provider
    'redirectUri'             => $client_redirect_uri,
    'urlAuthorize'            => getenv('AUTHORIZATION_SERVER_AUTHORIZE_URL'),
    'urlAccessToken'          => getenv('AUTHORIZATION_SERVER_ACCESS_TOKEN_URL'),
    'urlResourceOwnerDetails' => getenv('RESOURCE_OWNER_URL'),
]);

session_start();


$authorizationUrl = $provider->getAuthorizationUrl(
   ['scope' => 'protected_resource_access']
);

$_SESSION['oauth2state'] = $provider->getState();
$response['authorize_url'] = $authorizationUrl;
header("Location: $authorizationUrl");
// echo json_encode($response);