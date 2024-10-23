<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : '');
$kw = (isset($_GET['kw']) ?  $_GET['kw'] : '');
$sdate = (isset($_GET['sdate']) ?  $_GET['sdate'] : '');
$edate = (isset($_GET['edate']) ?  $_GET['edate'] : '');
$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$kw = urldecode($kw);

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

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_id = $decoded->data->id;

    $pid = (isset($_GET['pid']) ?  $_GET['pid'] : 0);

    $merged_results = array();

    // add if not exists

    $query = "SELECT *
                FROM leadership_assessment_review pr
              WHERE pr.status <> -1  " . ($pid != 0 ? " and pr.pid=$pid" : ' ')  . ($user_id != 0 ? " and pr.user_id=$user_id" : ' ');


    $stmt = $db->prepare($query);
    $stmt->execute();
    $last_id = 0;
    
    if($stmt->rowCount() == 0)
    {
        $query_insert = "insert into leadership_assessment_review(pid, user_id, period, answer, status, create_id) values($pid, $user_id, 1, '[]', 0, $user_id)";
        $stmt = $db->prepare($query_insert);
        $stmt->execute();

        $last_id = $db->lastInsertId();
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    if(count($merged_results) == 0)
    {
        $merged_results[] = array("id" => $last_id, "pid" => $pid, "user_id" => $user_id, "period" => 1, "answer" => "[]", "status" => 0, "create_id" => $user_id);
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}
