<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 20");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Ho_Chi_Minh');
$response = array();
$json = file_get_contents("php://input");
$obj = json_decode($json,TRUE);


try {
    $image_text = "ACCESS_PYTHON_API/base64_imgstring.txt";
    $myfile = fopen($image_text, "w") or die("Unable to open file!");
    fwrite($myfile, $obj['image']);
    fclose($myfile);


    $output_exec = exec("C:/Users/HYDRA_AI_SERVER/AppData/Local/Programs/Python/Python310/python.exe C:/Apache24/htdocs/VHost/hydra-cyborg.com/API/bill_capture/ACCESS_PYTHON_API/decode_base64_to_image.py");

    //Check exist logs directory
    $dir = "logs";
    if(!is_dir( $dir)){
        mkdir($dir,0777,true);
    }
    //Check exist logs file
    $todate = date("m-d-Y H:i:s");
    $file_dir = "logs/logs.txt";
        if(!file_exists( $file_dir)){
            file_put_contents($file_dir, "[".$todate."]-".$output_exec."\n");
        }else{
            file_put_contents($file_dir, "[".$todate."]-".$output_exec."\n",FILE_APPEND);
        }

    $response['image_result'] = $output_exec;
    $response['type_result'] = "Complete";
    $response['code'] =  1;
    $response['msg'] = "Success to process bill image !";

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
?>

