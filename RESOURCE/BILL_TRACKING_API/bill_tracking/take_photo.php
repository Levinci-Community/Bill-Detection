<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Ho_Chi_Minh');
$response = array();
$json = file_get_contents("php://input");
$obj = json_decode($json, TRUE);
$token =  $obj['access_token'];
$port = $obj['port_number'];
include('CreateKey.php');
include('vendor/autoload.php');

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


// Include data base connect class
// $filepath = realpath(dirname(FILE));
// require_once($filepath . "../db_connect.php");
include('db_connect.php');
// Connecting to database 
$db = new DB_CONNECT();
$db_dir = $db->connect();

include('sys_config.php');
try {
    if ($token) {
        //Token valid
        // decode jwt
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        $response['decoded'] = $decoded;

        if ($decoded->expire < time()) {
            $response['result'] = 2;
            $response['message'] = " Token is no longer valid ";
        } else {
            $user_email = $decoded->user_email;
            $response['user_email'] = $user_email;
            $result_check_user_email = mysqli_query($db_dir, "SELECT * FROM user WHERE user_email = '$user_email'");
            while ($get_value_info = mysqli_fetch_array($result_check_user_email)) {
                $user_id = $get_value_info['user_id'];
            }
            $count_result = mysqli_num_rows($result_check_user_email);

            $response['count_user'] = $count_result;
            if ($count_result > 0) {
                $user_direactory = "ACCESS_PYTHON_API/" . $user_id;
                if (!is_dir($user_direactory)) {
                    mkdir($user_direactory, 0777, true);
                }
                $image_text = "ACCESS_PYTHON_API/" . $user_id . "/base64_imgstring.txt";
                $myfile = fopen($image_text, "w") or die("Unable to open file!");
                fwrite($myfile, $obj['image']);
                fclose($myfile);

                $get_key = file_get_contents("ACCESS_PYTHON_API/key.txt");
                $get_key_array = explode(",", $get_key);
                $response['get_key'] = $get_key_array;

                $output_exec = exec($python_env_path . " " . dirname(__FILE__) . "/ACCESS_PYTHON_API/decode_base64_to_image.py $user_id");
                $response['output_url'] = dirname(__FILE__) . "/ACCESS_PYTHON_API/decode_base64_to_image.py";
                $result_image = json_decode($output_exec, TRUE);
                //Check exist logs directory
                $dir = "logs";
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                //Check exist logs file
                $todate = date("m-d-Y H:i:s");
                $file_dir = "logs/logs.txt";
                if (!file_exists($file_dir)) {
                    file_put_contents($file_dir, "[" . $todate . "]-" . " Upload bill image " . "\n");
                } else {
                    file_put_contents($file_dir, "[" . $todate . "]-" . " Upload bill image " . "\n", FILE_APPEND);
                }
                $response['check_result'] = $output_exec;
                $result_array = array();
                $response['test'] = array();
                $response['get_value'] = array();
                for ($i = 0; $i < count($get_key_array); $i++) {
                    $result_array[(string)$get_key_array[$i]] = $result_image[(string)$get_key_array[$i]];
                    // $result_array[] = array((string)$get_key_array[$i]=> $result_image[(string)$get_key_array[$i]]);
                    $response[(string)$get_key_array[$i]] = $result_image[(string)$get_key_array[$i]];
                    // $response['test'] [] = $get_key_array[$i];
                    $response['get_value'][] = $result_image[(string)$get_key_array[$i]];
                }
                $response['count_result_array'] = count($result_array);
                $response['result_array'] = $result_array;
                // $response['trans_no'] = $result_image["Trans#"];
                // $response['service'] = $result_image["Total"];
                // $response['BillDate'] = $result_image["Date"];
                // $response['sub_total'] = $result_image["Sub Total"];
                // $response['discount'] = $result_image["Discount"];
                // $response['vat'] = $result_image["VAT"];
                // $response['total'] = $result_image["Total"];
                $response['image_url'] = "http://hydra-cam0.ddns.net" . $port . "/API/bill_capture/ACCESS_PYTHON_API/" . $user_id . "/bill_image.JPG";

                $response['type_result'] = "Complete";
                $response['code'] =  1;
                $response['msg'] = "Success to process bill image !";
                $response['result_string'] = $output_exec;
            }
            $response['token'] = 1;
            $response['message'] = 'Valid token';
            $response["user_email"] = $user_email;
            $response["user_id"] = $user_id;
        }
    }
} catch (Exception $e) {
    //Check exist logs directory
    $dir = "logs";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    //Check exist logs file
    $todate = date("m-d-Y H:i:s");
    $file_dir = "logs/logs.txt";
    if (!file_exists($file_dir)) {
        file_put_contents($file_dir, "[" . $todate . "]-" . $e . "\n");
    } else {
        file_put_contents($file_dir, "[" . $todate . "]-" . $e . "\n", FILE_APPEND);
    }

    $response['image_result'] = "";
    $response['type_result'] = "Fail";
    $response['code'] = 0;
    $response['msg'] = "Fai