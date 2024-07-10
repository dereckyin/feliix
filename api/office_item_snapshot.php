<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';
include_once 'config/conf.php';
include_once 'config/database.php';


use Google\Cloud\Storage\StorageClient;


use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];
$user_id = 0;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

switch ($method) {
 
    case 'POST':
        // get database connection

        $batch_type = "office_item_release";

        $uid = $user_id;

        $data = json_decode(file_get_contents('php://input'), true);
        
        $item_id = $data['item_id'];
        $sig_date = $data['sig_date'];
        $sig_name = $data['sig_name'];
        $releaser_sig_date = $data['releaser_sig_date'];
        $releaser_sig_name = $data['releaser_sig_name'];
        $list = $data['list'];


        $sig_date = str_replace(' ', '+', $sig_date[1]);
        if($sig_date != "")
            $file_sig_date = base64_decode($sig_date);

        $sig_name = str_replace(' ', '+', $sig_name[1]);
        if($sig_name != "")
            $file_sig_name = base64_decode($sig_name);

        $releaser_sig_date = str_replace(' ', '+', $releaser_sig_date[1]);
        if($releaser_sig_date != "")
            $file_releaser_sig_date = base64_decode($releaser_sig_date);

        $releaser_sig_name = str_replace(' ', '+', $releaser_sig_name[1]);
        if($releaser_sig_name != "")
            $file_releaser_sig_name = base64_decode($releaser_sig_name);

        $file_name_sig_name = "";
        $file_name_sig_date = "";

        $file_name_releaser_sig_name = "";
        $file_name_releaser_sig_date = "";

        try {
            if (isset($file_sig_date)) {
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                $ext = "jpg";
                $filename = $time . $hash . "." . $ext;
                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);
                $bucket = $storage->bucket('feliiximg');
                $upload_name = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
                $image_name = "sig_date.jpg";
                $obj = $bucket->upload(
                    $file_sig_date,
                    ['name' => $upload_name]);
                $info = $obj->info();
                $size = $info['size'];
                if($size)
                {
                    $sig_name = $upload_name;
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,
                        create_id = :create_id,
                        created_at = now()";
                    // prepare the query
                    $stmt = $db->prepare($query);
                    // bind the values
                    $stmt->bindParam(':batch_id', $item_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
                    $stmt->bindParam(':create_id', $user_id);
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                    }
                }
                else
                {
                    $code = 502;
                    $message = 'There is an error while uploading file';
                    $image = $image_name;
                }
            }

            if(isset($file_sig_name))
            {
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                $ext = "jpg";
                $filename = $time . $hash . "." . $ext;
                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);
                $bucket = $storage->bucket('feliiximg');
                $upload_name = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
                $image_name = "sig_name.jpg";
                $obj = $bucket->upload(
                    $file_sig_name,
                    ['name' => $upload_name]);
                $info = $obj->info();
                $size = $info['size'];
                if($size)
                {
                    $sig_date = $upload_name;
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,
                        create_id = :create_id,
                        created_at = now()";
                    // prepare the query
                    $stmt = $db->prepare($query);
                    // bind the values
                    $stmt->bindParam(':batch_id', $item_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
                    $stmt->bindParam(':create_id', $user_id);
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                    }
                }
                else
                {
                    $code = 502;
                    $message = 'There is an error while uploading file';
                    $image = $image_name;
                }
            }

            if(isset($file_releaser_sig_date))
            {
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                $ext = "jpg";
                $filename = $time . $hash . "." . $ext;
                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);
                $bucket = $storage->bucket('feliiximg');
                $upload_name = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
                $image_name = "releaser_sig_date.jpg";
                $obj = $bucket->upload(
                    $file_releaser_sig_date,
                    ['name' => $upload_name]);
                $info = $obj->info();
                $size = $info['size'];
                if($size)
                {
                    $sig_date = $upload_name;
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,
                        create_id = :create_id,
                        created_at = now()";
                    // prepare the query
                    $stmt = $db->prepare($query);
                    // bind the values
                    $stmt->bindParam(':batch_id', $item_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
                    $stmt->bindParam(':create_id', $user_id);
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                    }
                }
                else
                {
                    $code = 502;
                    $message = 'There is an error while uploading file';
                    $image = $image_name;
                }
            }

            if(isset($file_releaser_sig_name))
            {
                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                $ext = "jpg";
                $filename = $time . $hash . "." . $ext;
                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);
                $bucket = $storage->bucket('feliiximg');
                $upload_name = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
                $image_name = "releaser_sig_name.jpg";
                $obj = $bucket->upload(
                    $file_releaser_sig_name,
                    ['name' => $upload_name]);
                $info = $obj->info();
                $size = $info['size'];
                if($size)
                {
                    $sig_date = $upload_name;
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,
                        create_id = :create_id,
                        created_at = now()";
                    // prepare the query
                    $stmt = $db->prepare($query);
                    // bind the values
                    $stmt->bindParam(':batch_id', $item_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
                    $stmt->bindParam(':create_id', $user_id);
                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                    }
                }
                else
                {
                    $code = 502;
                    $message = 'There is an error while uploading file';
                    $image = $image_name;
                }
            }


            
        }catch (Exception $e){

            //http_response_code(401);

            //echo json_encode(array("message" => "Access denied."));
            //die();
        }


        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
}

