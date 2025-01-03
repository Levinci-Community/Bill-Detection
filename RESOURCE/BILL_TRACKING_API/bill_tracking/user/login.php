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


$response = array();
$day = 1;
$expired_day = 86400*$day;
$response = array();

$input = file_get_contents("php://input");
$data = json_decode($input, true);
$response["input"] = $input;
// Lấy email và password
$email = $data['user_email'];
$password = $data['user_password'];
$response['email'] = $email;

$sql_user_info = mysqli_query($db_dir, "SELECT * FROM user WHERE user_email = '$email' and user_password = '$password'");


$result_user = mysqli_fetch_assoc($sql_user_info);


if (!empty($data['user_email']) && !empty($data['user_password']))
{
    $sql_check_valid = mysqli_query($db_dir,  "SELECT * FROM user WHERE user_email = '$email' AND user_password = '$password'");
    $result_valid = mysqli_num_rows($sql_check_valid);
        if($result_valid > 0)
        {
                if($result_valid == 1)
                {
                    $result_info_user = mysqli_fetch_assoc($sql_check_valid);
                    $response['Result'] = "2";
                    $response['Message'] = "Login Success";
                    $Token=array(
                        "user_email" => $email,
                        "iat" => time(),
                        "expire" =>time() + $expired_day // $day
                    );
                    $Jwt = JWT::encode($Token, $secretKey , 'HS256');
                    $response['Token']= $Jwt; //create Token
                }
        }
        else
        {
            $response['Result']="1";
            $response['Message'] = "Invalid email or password";
        };
}

echo json_encode($response);