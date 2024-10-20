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
        $pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
        $direct_access = (isset($_POST['direct_access']) ?  $_POST['direct_access'] : '');
        $manager_access = (isset($_POST['manager_access']) ?  $_POST['manager_access'] : '');
        $peer_access = (isset($_POST['peer_access']) ?  $_POST['peer_access'] : '');
        $other_access = (isset($_POST['other_access']) ?  $_POST['other_access'] : '');
        $outsider_name1 = (isset($_POST['outsider_name1']) ?  $_POST['outsider_name1'] : '');
        $outsider_email1 = (isset($_POST['outsider_email1']) ?  $_POST['outsider_email1'] : '');
        $outsider_name2 = (isset($_POST['outsider_name2']) ?  $_POST['outsider_name2'] : '');
        $outsider_email2 = (isset($_POST['outsider_email2']) ?  $_POST['outsider_email2'] : '');
      
        if ($pid == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        try {
            // now you can apply
            $query = "update leadership_assessment
                SET
                    `direct_access` = :direct_access,
                    `manager_access` = :manager_access,
                    `peer_access` = :peer_access,
                    `other_access` = :other_access,
                    `outsider_name1` = :outsider_name1,
                    `outsider_email1` = :outsider_email1,
                    `outsider_name2` = :outsider_name2,
                    `outsider_email2` = :outsider_email2
                    where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':direct_access', $direct_access);
            $stmt->bindParam(':manager_access', $manager_access);
            $stmt->bindParam(':peer_access', $peer_access);
            $stmt->bindParam(':other_access', $other_access);
            $stmt->bindParam(':outsider_name1', $outsider_name1);
            $stmt->bindParam(':outsider_email1', $outsider_email1);
            $stmt->bindParam(':outsider_name2', $outsider_name2);
            $stmt->bindParam(':outsider_email2', $outsider_email2);

            $stmt->bindParam(':id', $pid);

            $last_id = $pid;
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
