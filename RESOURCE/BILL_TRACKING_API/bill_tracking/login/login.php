<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Ho_Chi_Minh');

//require JWT library
require($filepath . "../vendor/autoload.php");
use \Firebase\JWT\JWT;

//require Database connection
require_once($filepath . "../db_connect.php");
// Connecting to database 
$db = new DB_CONNECT();
$db_dir = $db->connect();

$response = array();
$json = file_get_contents("php://input");
$obj = json_decode($json,TRUE);
$user_email = $obj['user_email'];
$user_password = $obj['password'];
$oauth = $obj['oauth'];
$oauth_user_email = $obj['oauth_user_email'];
$oauth_user_id = $obj['oauth_user_id'];
$secret_key = "270165a28d03cb80c1b24edb752be86c9d952100"; //SHA-1 Hash
$response["a"] =  $oauth;
// try {
    if(!empty($user_email)&&!empty($user_password)&&($oauth==0)){
        $user_password = md5($user_password);
        $check_user = mysqli_query($db_dir, "SELECT *FROM user_table WHERE `user_email` = '$user_email' AND `user_password` = '$user_password' "); 
        if (mysqli_num_rows($check_user) >= 1) {
            while ($retrieve_user_info = mysqli_fetch_array($check_user)) {
                $get_user_id = $retrieve_user_info['user_id'];
                $get_user_email = $retrieve_user_info['user_email'];
            }

            //Create camera app token 
            $issuer_claim = "camera_app"; // this can be the servername
		    $audience_claim = "user_token";
		    $issuedat_claim = time(); // issued at
		    $notbefore_claim = $issuedat_claim + 1; //not before in seconds
		    $expire_claim = $issuedat_claim + 86400; // expire time in 86400 seconds (24 hour)
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "user_email" => $get_user_email,
                    "user_id" => $get_user_id,
                    "nbf" => $notbefore_claim,				
                )
            );
            $jwt = JWT::encode($token, $secret_key);
            $response["login_msg"] = "Success";
            $response["login"] = 1;
            $response["app_access_token"] = $jwt;

        }else{
            $response["login_msg"] = "Fail";
            $response["login"] = 0;
        }
    } else if($oauth==1){
        //if available user_id
    $check_available_user = mysqli_query($db_dir, "SELECT *FROM user_table WHERE `user_id` = '$oauth_user_id' "); 
    if (mysqli_num_rows($check_available_user) >= 1) {
        while ($retrieve_user_info = mysqli_fetch_array($check_available_user)) {
            $get_user_id = $retrieve_user_info['user_id'];
            $get_user_email = $retrieve_user_info['user_email'];
            //Create camera app token 
            $issuer_claim = "camera_app"; // this can be the servername
		    $audience_claim = "user_token";
		    $issuedat_claim = time(); // issued at
		    $notbefore_claim = $issuedat_claim + 1; //not before in seconds
		    $expire_claim = $issuedat_claim + 86400; // expire time in 86400 seconds (24 hour)
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "user_email" => $get_user_email,
                    "user_id" => $get_user_id,
                    "nbf" => $notbefore_claim,				
                )
            );
            $jwt = JWT::encode($token, $secret_key);
            $response["login_msg"] = "Success";
            $response["login"] = 1;
            $response["app_access_token"] = $jwt;
        }
    }else{
        //if login via oauth2 (new user) -> add new user and generate token
        $add_new_user = mysqli_query($db_dir, "INSERT INTO `user_table` (`id`, `user_email`, `user_password`, `user_id`) VALUES (NULL, '$oauth_user_email', '123456', '$oauth_user_id');"); 
        //get new user info to generate token
        $get_new_user = mysqli_query($db_dir, "SELECT *FROM user_table WHERE `user_id` = '$oauth_user_id' "); 
        while ($retrieve_user_info = mysqli_fetch_array($get_new_user)) {
            $get_user_id = $retrieve_user_info['user_id'];
            $get_user_email = $retrieve_user_info['user_email'];
            //Create camera app token 
            $issuer_claim = "camera_app"; // this can be the servername
		    $audience_claim = "user_token";
		    $issuedat_claim = time(); // issued at
		    $notbefore_claim = $issuedat_claim + 1; //not before in seconds
		    $expire_claim = $issuedat_claim + 86400; // expire time in 86400 seconds (24 hour)
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "user_email" => $get_user_email,
                    "user_id" => $get_user_id,
                    "nbf" => $notbefore_claim,				
                )
            );
            $jwt = JWT::encode($token, $secret_key);
            $response["login_msg"] = "Success";
            $response["login"] = 1;
            $response["app_access_token"] = $jwt;
        }
    }
    }
    
    echo json_encode($response);
?>

