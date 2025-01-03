<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json ; charset=utf-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Ho_Chi_Minh');

include('../CreateKey.php');
include('../vendor/autoload.php');

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Include data base connect class
// $filepath = realpath(dirname(FILE));
// require_once($filepath . "/db_connect.php");
include('../db_connect.php');
// Connecting to database 
$db = new DB_CONNECT();
$db_dir = $db->connect();


$day = 7;
$expired_day = 86400*$day;

$response = array();
$json = file_get_contents("php://input");
$obj = json_decode($json,TRUE);
$user_email = $obj['user_email'];
$user_password = $obj['password'];

// Tạo user_id mới dạng User1, User2, ...
$sql_get_next_id = "SELECT MAX(id) as max_id FROM user";
$result_get_next_id = mysqli_query($db_dir, $sql_get_next_id);
$row = mysqli_fetch_assoc($result_get_next_id);
$max_id = $row['max_id'];
$new_user_id = "uid0" . ($max_id + 1);

// Lấy ngày và giờ hiện tại
// $register_date = date('Y-m-d H:i:s');
$response['test'] = $user_email;
$sql_check_email = "SELECT * FROM user WHERE user_email = '$user_email'";
$result_check_email = mysqli_query($db_dir, $sql_check_email);
$count_result_check_email = mysqli_num_rows($result_check_email);
$response['user_mailK'] = $count_result_check_email;
if($count_result_check_email == 0) {
    $sql_insert_user = "INSERT INTO user 
    (
    id,
    user_id,
    user_email,
    user_password,
    user_name
    ) 
    VALUES 
    (
    NULL,
    '$new_user_id',
    '$user_email',
    '$user_password', 
    'USER'
    )";
    $result_insert_user = mysqli_query($db_dir, $sql_insert_user);
    $response['test'] = $result_insert_user;
    $response['sql_error'] = mysqli_error($db_dir);
    if($result_insert_user){
        $response["result"] = "1";
        $response["message"] = "Register Success";
        $Token = array(
            "user_id" => $new_user_id,
            "prj" => "AIBill",
            "iat" => time(),
            "expire" => time() + $expired_day 
        );
        $Jwt = JWT::encode($Token, $secretKey, 'HS256');
        $response['Token'] = $Jwt; //create Token
    }else{
        $response["result"] = "2";
        $response["message"] = "Error while registering user";
    };
}else{
    $response["result"] = "0";
    $response["message"] = "Email already exists";
};
echo json_encode($response);
?>