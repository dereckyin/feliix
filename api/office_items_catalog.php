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

$key = (isset($_GET['key']) ?  $_GET['key'] : '');
$key = urldecode($key);

$merged_results = array();

$query = "SELECT m.code code1, m.category cat1, s.code code2, s.category cat2, b.code code3, b.category cat3, d.code code4, d.category cat4, '' qty
            FROM office_items_main_category m
            left join office_items_sub_category s on m.code = s.parent_code
            left join office_items_brand b on CONCAT(m.code, s.code) = b.parent_code
            left join office_items_description d on CONCAT(m.code,s.code,b.code) = d.parent_code 
                where m.status <> -1 and s.status <> -1 and b.status <> -1 and d.status <> -1
                ";

$query_cnt = "SELECT count(*) cnt
                FROM office_items_main_category m
                left join office_items_sub_category s on m.code = s.parent_code
                left join office_items_brand b on CONCAT(m.code, s.code) = b.parent_code
                left join office_items_description d on CONCAT(m.code,s.code,b.code) = d.parent_code 
                    where m.status <> -1 and s.status <> -1 and b.status <> -1 and d.status <> -1
                     ";

if($key != "")
{
    $query = $query . " and parent_code = '" . $key . "' ";
    $query_cnt = $query_cnt . " and parent_code = '" . $key . "' ";
}


$query = $query . " order by m.sn, s.sn, b.sn, d.sn ";


if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();

$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();
while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

if(count($merged_results) > 0)
{
    $merged_results[0]['cnt'] = $cnt;
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


?>