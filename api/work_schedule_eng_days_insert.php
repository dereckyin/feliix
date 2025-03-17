<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$_id = isset($_POST['id']) ? $_POST['id'] : 0;

$period = isset($_POST['period']) ? $_POST['period'] : '';
$rate_leadman = isset($_POST['rate_leadman']) ? $_POST['rate_leadman'] : '';
$rate_sr_technician = isset($_POST['rate_sr_technician']) ? $_POST['rate_sr_technician'] : '';
$rate_technician = isset($_POST['rate_technician']) ? $_POST['rate_technician'] : '';
$rate_electrician = isset($_POST['rate_electrician']) ? $_POST['rate_electrician'] : '';
$rate_helper = isset($_POST['rate_helper']) ? $_POST['rate_helper'] : '';

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

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
        
        // now you can apply
        $uid = $user_id;
        
        $is_existed = false;
        
        if($_id != 0)
        {
            $is_existed = false;
            
            $query = "SELECT id
            FROM work_schedule_eng
            where id = :id";
            
            $stmt = $db->prepare( $query );
            $stmt->bindParam(':id', $_id);
            
            // execute the query
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $is_existed = true;
            }
        }
        
        if($_id == 0 || !$is_existed)
        {
            
            $query = "INSERT INTO work_schedule_eng
            SET
                `period` = :period,
                `rate_leadman` = :rate_leadman,
                `rate_sr_technician` = :rate_sr_technician,
                `rate_technician` = :rate_technician,
                `rate_electrician` = :rate_electrician,
                `rate_helper` = :rate_helper,
            
                `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':period', $period);
            $stmt->bindParam(':rate_leadman', $rate_leadman);
            $stmt->bindParam(':rate_sr_technician', $rate_sr_technician);
            $stmt->bindParam(':rate_technician', $rate_technician);
            $stmt->bindParam(':rate_electrician', $rate_electrician);
            $stmt->bindParam(':rate_helper', $rate_helper);
            
            $stmt->bindParam(':create_id', $user_id);
            
            $last_id = 0;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
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
        }
        else
        {
            $query = "update work_schedule_eng
                SET
                    `period` = :period,
                    `rate_leadman` = :rate_leadman,
                    `rate_sr_technician` = :rate_sr_technician,
                    `rate_technician` = :rate_technician,
                    `rate_electrician` = :rate_electrician,
                    `rate_helper` = :rate_helper,
            
                    `updated_id` = :updated_id,
                    `updated_at` = now()
                    where id = :id";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':period', $period);
            $stmt->bindParam(':rate_leadman', $rate_leadman);
            $stmt->bindParam(':rate_sr_technician', $rate_sr_technician);
            $stmt->bindParam(':rate_technician', $rate_technician);
            $stmt->bindParam(':rate_electrician', $rate_electrician);
            $stmt->bindParam(':rate_helper', $rate_helper);
            
            $stmt->bindParam(':updated_id', $user_id);
            
            $stmt->bindParam(':id', $_id);
            
            $last_id = $_id;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }
        
        
        $db->commit();
        
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa"), "id" => $last_id));
        
    }
    catch (Exception $e){
        
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();
        
    }
}
?>