<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Ho_Chi_Minh');

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

include('../CreateKey.php');
include('../vendor/autoload.php');


// Include data base connect class
$filepath = realpath(dirname(FILE));
// require_once($filepath . "../db_connect.php");
include('../db_connect.php');
// Connecting to database 
$db = new DB_CONNECT();
$db_dir = $db->connect();


$response = array();
$json = file_get_contents("php://input");
$obj = json_decode($json,TRUE);
$token =  $obj['access_token'];

$response["test"] = "AAAA";

try {
    if($token){
        $response["test"] = "BBBB";
        $response["token"] = $token;
    	$decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    	$response['decoded'] = $decoded;
        	if($decoded->expire < time()){
                $response['result']=2;
        		$response['message'] = " Token is no longer valid ";
        	}
        	else{
                $user_email = $decoded -> user_email;
                $response['user_email'] = $user_email;
                // $result_check_user_email = mysqli_query($db_dir,"SELECT * FROM user WHERE user_email = 'admin@gmail.com'");
                $result_check_user_email = mysqli_query($db_dir,"SELECT * FROM user WHERE user_email = '$user_email'");
                $count_result = mysqli_num_rows($result_check_user_email);
                $response['count_user'] = $count_result;
                if($count_result > 0 ){
                    $user_info = array();
                    while($row = mysqli_fetch_assoc($result_check_user_email)){
                        $user_info[] =array(
                            'user_email' => $row['user_email'],
                            'name' => $row['name']
                        );
                        $user_id = $row['user_id'];
                    };
                    $response['user_id'] = $user_id;
                    $sql_bill_info = mysqli_query($db_dir,"SELECT * FROM history_bill WHERE user_id = '$user_id' ORDER BY id DESC ");
                    if ($sql_bill_info) {
                        // Lấy dữ liệu bằng mysqli_fetch_array
                        while ($row = mysqli_fetch_array($sql_bill_info, MYSQLI_ASSOC)) {
                            // Truy cập các cột trong bảng
                            $response['status'] = $row['status'];
                            $response['CheckingDate'] = $row['checking_date'];
                            $response['service'] = $row['service'];
                            $response['trans_no'] = $row['trans_no'];
                            $response['BillDate'] = $row['bill_date'];
                            $response['sub_total'] = $row['sub_total'];
                            $response['discount'] = $row['discount'];
                            $response['vat'] = $row['vat'];
                            $response['total'] = $row['total'];
                        }
                    } else {
                        echo "Lỗi truy vấn: " . mysqli_error($db_dir);
                    }
                }else{
                    $response["message"]="Error: No user found";
                }
            }
    }else{
        $response["result"]=0;
        $response["message"]="Empty Token";
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
       
    }
    echo json_encode($response);