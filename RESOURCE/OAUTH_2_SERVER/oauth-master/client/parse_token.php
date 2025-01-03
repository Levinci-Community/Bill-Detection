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

$json = file_get_contents("php://input");
$obj = json_decode($json,TRUE);

$code = $obj['code'];
$state = $obj['state'];
$response = array();

require_once 'bootstrap.php';
if (empty($state) || (isset($_SESSION['oauth2state']) && $state !== $_SESSION['oauth2state'])) {
     if (isset($_SESSION['oauth2state'])) {
         unset($_SESSION['oauth2state']);
     }
}else{
    try{
        $accessToken = $provider->getAccessToken('authorization_code', [
          'code' => urldecode($code),
        ]);
        $resourceOwner = $provider->getResourceOwner($accessToken);
        $user_id = $resourceOwner->toArray()['Owner']['oauth_info']['oauth_user_id'];
        //$user_id = $resourceOwner->toArray()['Owner'];
        $oauth_user = mysqli_query($db_dir, "SELECT *FROM user WHERE `user_id` = '$user_id' ");
        while ($retrieve_user_info = mysqli_fetch_array($oauth_user)) {
            $user_email = $retrieve_user_info['email'];
            $user_id =  $retrieve_user_info['user_id'];
            $user_name = $retrieve_user_info['user_name'];
        }
    }catch(Exception $e){
        $response['message'] = $e;
    }
}


$response['user_id'] = $user_id;
$response['access_token'] = $accessToken;
echo json_encode($response);
   
