<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Ho_Chi_Minh');

require($filepath . "../vendor/autoload.php");
use \Firebase\JWT\JWT;

$response = array();
$json = file_get_contents("php://input");
$obj = json_decode($json,TRUE);
$token = $obj['access_token'];
try {
    $secret_key = "270165a28d03cb80c1b24edb752be86c9d952100"; //SHA-1 Hash
    if($token){
            //Token valid
        // decode jwt
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $user_id = ($decoded->data->user_id);
        $user_email = ($decoded->data->user_email);
        $response['token'] = 1;
        $response['message'] = 'Valid token';
        $response["user_email"] = $user_email;
        $response["user_id"] = $user_id;
    }
}catch (Exception $e) {
    //Check exist logs directory
    $dir = "logs";
    if(!is_dir( $dir)){
        mkdir($dir,0777,true);
    }
    //Check exist logs file
    $todate = date("m-d-Y H:i:s");
    $file_dir = "logs/logs.txt";
        if(!file_exists( $file_dir)){
            file_put_contents($file_dir, "[".$todate."]-".$e."\n");
        }else{
            file_put_contents($file_dir, "[".$todate."]-".$e."\n",FILE_APPEND);
        }

    $response['image_result'] = "";
    $response['type_result'] = "Fail";
    $response['code'] = 0;
    $response['msg'] = "Fail to process bill image !";    
    //Check token
    if ($e->getMessage() == "Expired token") {
        $response['token'] = 2;
        $response['message'] = 'Expired token';
    }   
    }
    echo json_encode($response);
?>

