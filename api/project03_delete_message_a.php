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
include_once 'mail.php';
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

$message_id = (isset($_POST['message_id']) ?  $_POST['message_id'] : '');
$item_id = (isset($_POST['item_id']) ?  $_POST['item_id'] : '');

try{

    if($item_id != 0)
    {
        $query = "update project_other_task_message_reply_a
        SET
            status = -1,
            updated_id = :updated_id,
            updated_at = now()
        where id = :id ";

        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $message_id);
    }
    else 
    {
        $query = "update project_other_task_message_a
        SET
            status = -1,
            updated_id = :updated_id,
            updated_at = now()
        where id = :id ";

        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $message_id);
    }

    $jsonEncodedReturnArray = "";
    if ($stmt->execute()) {
        $returnArray = array('ret' => $stage_id_to_edit);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        if($item_id != 0)
            SendNotifyReplyMail($message_id, $uid);
        else
            SendNotifyMail($message_id, $uid);
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


function SendNotifyMail($last_id, $uid)
{
    $project_name = "";
    $task_name = "";
    $stages_status = "";
    $create_id = "";

    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $stage_id = 0;

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);
 
    $task_name = $_record[0]["task_name"];
    $created_at = $_record[0]["created_at"];
    $stages = "";
    $create_id = $_record[0]["create_id"];

    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $stage_id = $_record[0]["id"];

    $msg = $_record[0]["message"];

    //$due_date = str_replace("-", "/", $_record[0]["due_date"]) . " " . $_record[0]["due_time"];
    $detail = $_record[0]["detail"];

    $username = $_record[0]["username"];
    $_id = $_record[0]["_id"];

    message_notify_dept("del", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, "", $detail, $stage_id, $msg, $username, $created_at, $_id, 'AD');

}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT pt.id, title task_name, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            detail,
            message,
            u.username,
            pmsg.created_at,
            pmsg.create_id _id
            FROM project_other_task_message_a pmsg
            LEFT JOIN project_other_task_a pt ON pmsg.task_id = pt.id
            LEFT JOIN user u ON u.id = pmsg.create_id
            WHERE pmsg.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}



function SendNotifyReplyMail($last_id, $uid)
{
    $project_name = "";
    $task_name = "";
    $stages_status = "";
    $create_id = "";

    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $stage_id = 0;

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetReplyTaskDetail($last_id, $db);
 
    $task_name = $_record[0]["task_name"];
    $created_at = $_record[0]["created_at"];
    $stages = "";
    $create_id = $_record[0]["create_id"];

    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $stage_id = $_record[0]["id"];

    $msg = $_record[0]["message"];

    //$due_date = str_replace("-", "/", $_record[0]["due_date"]) . " " . $_record[0]["due_time"];
    $detail = $_record[0]["detail"];

    $username = $_record[0]["username"];
    $_id = $_record[0]["_id"];

    message_notify_dept("del", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, "", $detail, $stage_id, $msg, $username, $created_at, $_id, 'AD');

}

function GetReplyTaskDetail($id, $db)
{
    $sql = "SELECT pt.id, title task_name, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            detail,
            pmsgr.message,
            u.username,
            pmsgr.created_at,
            pmsgr.create_id _id
            FROM project_other_task_message_reply_a pmsgr
            LEFT JOIN project_other_task_message_a pmsg ON pmsg.id = pmsgr.message_id
            LEFT JOIN project_other_task_a pt ON pmsg.task_id = pt.id
            LEFT JOIN user u ON u.id = pmsg.create_id
            WHERE pmsgr.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}
