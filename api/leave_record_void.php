<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$merged_results = [];

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

$query = "
    update apply_for_leave a LEFT JOIN user u ON a.uid = u.id 
    set void_id = " . $user_id . ", void_at = NOW(), a.STATUS = -3
    WHERE a.STATUS <> -1 AND approval_id <> 0 AND reject_id = 0 AND re_approval_id <> 0 AND re_reject_id = 0 
    AND a.id in (" . $id . ")
";

$stmt = $db->prepare( $query );

if (!$stmt->execute())
{
    $arr = $stmt->errorInfo();
    error_log($arr[2]);
}


echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
