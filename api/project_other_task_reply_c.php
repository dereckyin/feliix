<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
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

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';
include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();

switch ($method) {
    
    case 'POST':
        // get database connection
        $uid = $user_id;
        $msg_id = (isset($_POST['msg_id']) ?  $_POST['msg_id'] : 0);
        $reply_id = (isset($_POST['reply_id']) ?  $_POST['reply_id'] : 0);
        $message = (isset($_POST['reply']) ?  $_POST['reply'] : '');
    
        $query = "INSERT INTO project_other_task_message_reply_c
        SET
            `message_id` = :msg_id,
            `reply_id` = :reply_id,
            `message` = :message,
          
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':msg_id', $msg_id);
        $stmt->bindParam(':reply_id', $reply_id);
        $stmt->bindParam(':message', $message);
       
        $stmt->bindParam(':create_id', $uid);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        // send notify mail
        SendNotifyMail($last_id, $uid);

        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
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

    $stage_id = $_record[0]["stage_id"];

    $msg = $_record[0]["message"];

    //$due_date = str_replace("-", "/", $_record[0]["due_date"]) . " " . $_record[0]["due_time"];
    $detail = $_record[0]["detail"];

    $username = $_record[0]["username"];
    $_id = $_record[0]["_id"];

    $project_name = $_record[0]["project_name"];

    message_notify_dept("create", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, "", $detail, $stage_id, $msg, $username, $created_at, $_id, 'C');

}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT pt.stage_id, pt.id, title task_name,  project_name,
                pt.create_id,
                pt.assignee,
                pt.collaborator,
                due_date,
                detail,
                potmr.message,
                u.username,
                potmr.created_at,
                potmr.create_id _id
                FROM project_other_task_message_reply_c potmr
                left join project_other_task_message_c pmsg on potmr.message_id = pmsg.id 
                LEFT JOIN project_other_task_c pt ON pmsg.task_id = pt.id
                LEFT JOIN project_stages ps ON pt.stage_id = ps.id
                LEFT JOIN project_stage psg ON ps.stage_id = psg.id
                left JOIN project_main pm ON ps.project_id = pm.id 
                LEFT JOIN user u ON u.id = potmr.create_id
            where potmr.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}
