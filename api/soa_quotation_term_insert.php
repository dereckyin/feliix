<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : 0;
$detail = isset($_POST['detail']) ? $_POST['detail'] : [];
$detail_array = json_decode($detail, true);

$pixa = isset($_POST['pixa']) ? $_POST['pixa'] : 0;
$show = isset($_POST['show']) ? $_POST['show'] : '';
$pageless = isset($_POST['pageless']) ? $_POST['pageless'] : '';

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

        // quotation_page
        $query = "UPDATE soa_quotation set pixa_t = :pixa_t, show_t = :show_t, pageless = :pageless
                WHERE
                `id` = :quotation_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':pixa_t', $pixa);
        $stmt->bindParam(':show_t', $show);
        $stmt->bindParam(':pageless', $pageless);
        $stmt->bindParam(':quotation_id', $quotation_id);

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

        // quotation_page
        $query = "DELETE FROM soa_quotation_term
                WHERE
                `quotation_id` = :quotation_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':quotation_id', $quotation_id);

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

        $page = $detail_array['page'];
        $page == '' ? $page = 1 : $page = $page;
     
        for($i=0 ; $i < count($detail_array['item']) ; $i++)
        {
            $query = "INSERT INTO soa_quotation_term
            SET
                `quotation_id` = :quotation_id,
                `page` = :page,
                `title` = :title,
                `brief` = :brief,
                `list` = :list,
                                
                `status` = 0,
                `create_id` = :create_id,
                `created_at` =  now() ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':quotation_id', $quotation_id);
            $stmt->bindParam(':page', $page);
            $stmt->bindParam(':title', $detail_array['item'][$i]['title']);
            $stmt->bindParam(':brief', $detail_array['item'][$i]['brief']);
            $stmt->bindParam(':list', $detail_array['item'][$i]['list']);
          
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
