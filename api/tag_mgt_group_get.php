<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
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

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();


use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {

    $merged_results = array();

    $query = "SELECT id, sn, group_name 
                FROM tag_group
            where status <> -1 order by sn ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items = GetItems($row['id'], $db);
        $row['items'] = $items;
        $merged_results[] = $row;
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}


function GetItems($group_id, $db) 
{
    $query = "SELECT id,
                    `item_name`, 
                    sn
                    FROM tag_item
                    WHERE status <> -1 and group_id = :group_id  order by sn
                    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':group_id', $group_id);
    $stmt->execute();

    $items = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items[] = $row;
    }

    return $items;
}