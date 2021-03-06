<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
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
$conf = new Conf();

$uid = $user_id;

$task_id = (isset($_POST['task_id']) ?  $_POST['task_id'] : 0);
$uid = $user_id;
$title = (isset($_POST['title']) ?  $_POST['title'] : '');
$priority = (isset($_POST['priority']) ?  $_POST['priority'] : 0);
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);
$assignee = (isset($_POST['assignee']) ?  $_POST['assignee'] : '');
$collaborator = (isset($_POST['collaborator']) ?  $_POST['collaborator'] : '');
$due_date = (isset($_POST['due_date']) ?  $_POST['due_date'] : '');
$detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');

try{
    $query = "update project_other_task_r
    SET
        `title` = :title,
        `priority` = :priority,
        `status` = :status,
        `assignee` = :assignee,
        `collaborator` = :collaborator,
        `due_date` = :due_date,
        `detail` = :detail,
        updated_id = :updated_id,
        updated_at = now()
    where id = :id ";

    // prepare the query
    $stmt = $db->prepare($query);

    $stmt->bindParam(':id', $task_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':assignee', $assignee);
    $stmt->bindParam(':collaborator', $collaborator);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':detail', $detail);

    $stmt->bindParam(':updated_id', $uid);

    $jsonEncodedReturnArray = "";

    if ($stmt->execute()) {

        $returnArray = array('batch_id' => $task_id);
       
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    }
    else
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}

