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

include_once 'mail.php';

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);
    
    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
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
            
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }
    }
    
    header('Access-Control-Allow-Origin: *');
    
    include_once 'config/database.php';
    
    
    switch ($method) {
        
        case 'POST':
            
            $database = new Database();
            $db = $database->getConnection();
            $db->beginTransaction();
            $conf = new Conf();
            
            $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
            
            $petty_list = (isset($_POST['level1']) ?  $_POST['level1'] : '[]');
            $petty_array = json_decode($petty_list, true);
            
            try {
                
                
                // petty_list
                $query = "update tag_group
                set status = -1, sn = 0";
                
                
                // prepare the query
                $stmt = $db->prepare($query);
                
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
                
                for ($i = 0; $i < count($petty_array); $i++) {

                    $sn = $i+1;

                    if($petty_array[$i]['id'] == '0')
                    {
                        $query = "INSERT INTO tag_group
                        SET
                        `sn` = :sn,
                        `group_name` = :group_name,
                        `create_id` = :create_id,
                        `created_at` = now()";
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        
                        // bind the values
                        $stmt->bindParam(':create_id', $user_id);
                        $stmt->bindParam(':group_name', $petty_array[$i]['group_name']);
                        $stmt->bindParam(':sn', $sn);
                        
                        
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
                    else
                    {
                        $query = "update tag_group
                        set `group_name` = :group_name,
                        `sn` = :sn,
                        `status` = 1,
                        `create_id` = :create_id,
                        `created_at` = now()
                        where id = :id";
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        
                        $stmt->bindParam(':create_id', $user_id);
                        $stmt->bindParam(':group_name', $petty_array[$i]['group_name']);
                        $stmt->bindParam(':sn', $sn);
                        $stmt->bindParam(':id', $petty_array[$i]['id']);
                        
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
                }
                
                $db->commit();
                
                http_response_code(200);
                echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
            } catch (Exception $e) {
                
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
            break;
        }
        