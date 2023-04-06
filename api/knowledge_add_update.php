<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$title = (isset($_POST['title']) ?  $_POST['title'] : '');
$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$access = (isset($_POST['access']) ?  $_POST['access'] : '');
$type = (isset($_POST['type']) ?  $_POST['type'] : '');
$link = (isset($_POST['link']) ?  $_POST['link'] : '');
$watch = (isset($_POST['watch']) ?  $_POST['watch'] : '');
$duration = (isset($_POST['duration']) ?  $_POST['duration'] : '');
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$cover = (isset($_POST['cover']) ?  $_POST['cover'] : '');
$filename = (isset($_POST['filename']) ?  $_POST['filename'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';

include_once 'config/conf.php';
require_once '../vendor/autoload.php';
include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        $uid = $user_id;

        $pre_knowledge = knowledge_get($id, $db);
    
        $query = "UPDATE knowledge
        SET
            `title` = :title, ";

        if($cover == '')
        {
            $query .= "`cover` = '', ";
        }

        if($filename == '')
        {
            $query .= "`attach` = '', ";
        }

        
        $query .= " `category` = :category,
            `access` = :access,
            `type` = :type,
            `link` = :link, 
            `watch` = :watch, 
            `duration` = :duration,
            `status` = 1,
            `updated_id` = :create_id,
            `updated_at` = now()
            where id = :id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':access', $access);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':link', $link);
      
        $stmt->bindParam(':watch', $watch);
        $stmt->bindParam(':duration', $duration);
      
        $stmt->bindParam(':create_id', $user_id);
        $stmt->bindParam(':id', $id);

        $last_id = $id;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                //$last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }


        $batch_id = $last_id;
        $batch_type = "knowledge_cover";

        if (array_key_exists('photo', $_FILES))
        {
            $update_name = SaveImage('photo', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'cover', $batch_id, $db);
        }

        $batch_type = "knowledge_attach";

        if (array_key_exists('file1', $_FILES))
        {
            $update_name = SaveImage('file1', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'attach', $batch_id, $db);
        }
        
        

            
        $db->commit();

        $users = knowledge_access_get($access, $db);
        $cc = array();
        
        knowledge_add_notification($user_name, date("Y/m/d") . " " . date("h:i:sa"), $users, $cc, $title, $pre_knowledge["created_by"], $pre_knowledge['created_at'], category_text($category), type_text($type), duration_text($duration), $last_id, "edit");
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") ));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

function knowledge_get($id, $db)
{
    $query = "SELECT pm.id,
                pm.cover, 
                pm.title, 
                pm.category, 
                pm.access, 
                pm.`type`, 
                pm.link, 
                pm.attach,
                pm.duration, 
                pm.watch,
                pm.desciption,
                pm.`status`,
                c_user.username AS created_by, 
                DATE_FORMAT(pm.created_at, '%Y/%m/%d %H:%i:%s') created_at
            FROM knowledge pm
                LEFT JOIN user c_user ON pm.create_id = c_user.id where pm.id = :id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    if($num > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    else
    {
        return null;
    }
}

function UpdateImageName($upload_name, $type, $batch_id, $db){
    
    $query = "update knowledge
    SET " . $type . " = :gcp_name where id=:id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $batch_id);

    $stmt->bindParam(':gcp_name', $upload_name);


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
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $valid_extensions)) 
            {
                //$upload_path = 'img/' . time() . '.' . $extension;

                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);

                $bucket = $storage->bucket('feliiximg');

                $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                $file_size = filesize($_FILES[$type]['tmp_name']);
                $size = 0;

                $obj = $bucket->upload(
                    fopen($_FILES[$type]['tmp_name'], 'r'),
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size == $file_size && $file_size != 0 && $size != 0)
                {
                    $query = "INSERT INTO gcp_storage_file
                    SET
                        batch_id = :batch_id,
                        batch_type = :batch_type,
                        filename = :filename,
                        gcp_name = :gcp_name,

                        updated_id = :updated_id,
                        updated_at = now()";

                    // prepare the query
                    $stmt = $db->prepare($query);
                
                    // bind the values
                    $stmt->bindParam(':batch_id', $batch_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
        
                    $stmt->bindParam(':updated_id', $user_id);

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
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }

                    return $upload_name;
                }
                else
                {
                    $message = 'There is an error while uploading file';
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                    die();
                    
                }
            }
            else
            {
                $message = 'Only Images or Office files allowed to upload';
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                die();
            }
        }

        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
        die();
    }
}

function knowledge_access_get($access, $db)
{
    $users = array();

    $query = "select 
                username , email, department from `user` 
            left join `user_department` on `user`.apartment_id = `user_department`.id
            where `user`.status = 1";

    $username = "";
    $email = "";
    $department = "";

    $access_up = strtoupper($access); 

    $stmt_cnt = $db->prepare( $query );
    $stmt_cnt->execute();
    while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
        $username = $row['username'];
        $email = $row['email'];
        $department = $row['department'];

        // if username or department part of access then add to uses
        if(strpos($access_up, strtoupper($username)) !== false || strpos($access_up, strtoupper($department)) !== false || strpos($access_up, "ALL") !== false)
        {
            $users[] = $username;
        }
    }

    return implode(",", $users);
}

function duration_text($duration){
    $duration_str = '';
    if($duration > 0){
        $duration_in_huours = round($duration/60, 1);
        $duration_in_minutes = floor($duration % 60);
        
        if($duration_in_huours > 1){
            $duration_str = $duration_in_huours . '-hr ';
        }
        else
        {
            $duration_str = $duration_in_minutes . '-min';
        }

        //if($duration_in_minutes > 0){
        //    $duration_str .= $duration_in_minutes . '-min';
        //}
    }

    return $duration_str;
}

function type_text($type)
{
    if($type == 'file')
        return 'File';
    else if($type == 'link')
        return 'Web Text';
    else if($type == 'video')
        return 'Web Video';
    else
        return '';
}

function category_text($category)
{
    // split by comma and concatenate by space and comma
    $category_arr = explode(",", $category);
    $category_str = '';
    foreach($category_arr as $cat)
    {
        $category_str .= $cat . ', ';
    }

    return rtrim($category_str, ", ");

}