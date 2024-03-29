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
        $user_name = $decoded->data->username;
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

$iq_id = (isset($_POST['iq_id']) ?  $_POST['iq_id'] : 0);
$items = (isset($_POST['items']) ?  $_POST['items'] : 0);

$comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
$iq_name = (isset($_POST['iq_name']) ? $_POST['iq_name'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

$action = 'finish_notes';

$items_array = json_decode($items,true);


    $task = GetTaskInfo($iq_id, $db);
    if(count($task) == 0) {
        $task_table_info = "";
        $task_department_info = "";
    }
    else {
        $task_table_info = GetTaskTableInfo($task[0]['task_type']);
        $task_department_info = GetTaskDepartmentInfo($task[0]['task_type']);
        }
    $users = GetTaskUsersInfo($iq_id, $db, $task_table_info);

    $users .= ",48" ;
    
    $last_id = GetLatestSubmitId($iq_id, $db);

    inquiry_partial_complete_notification($user_name, $last_id, $users, $project_name, $serial_name, $iq_name, 'Inquiry - Taiwan', $comment, $action, $items_array, $iq_id, $task_department_info);

    echo $jsonEncodedReturnArray;

function GetLatestSubmitId($id, $db)
{
    $last_id = 0;

    $query = "select create_id from iq_process ip where iq_id = " . $id . " and action = 'send_note' order by id desc limit 1";
    $stmt = $db->prepare($query);
    $stmt->execute();


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $last_id = $row['create_id'];
        
    }

    return $last_id;
}


function GerPreStatus($id, $db)
{
    $query = "SELECT items FROM iq_process WHERE iq_id = :id and action = 'send_note' order by created_at desc limit 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['items'];
}

function GetTaskInfo($id, $db)
{
    $query = "SELECT task_id, task_type FROM iq_main WHERE id = :id ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    $merged_results = array();

    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetTaskUsersInfo($pid, $db, $task_type)
{
    $users = "";

    $query = "select create_id, assignee, collaborator from project_other_task" . $task_type . " where id = (select task_id from iq_main where id = :pid)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row['create_id'] != '')
    {
        $users .= $row['create_id'] . ',';
    }
    if($row['assignee'] != '')
    {
        $users .= $row['assignee'] . ',';
    }
    if($row['collaborator'] != '')
    {
        $users .= $row['collaborator'] . ',';
    }

    $users = rtrim($users, ",");

    return $users;
}


function GetTaskTableInfo($task_type)
{
    $info = "";

    if($task_type == "LT")
    {
        $info = "_l";
    }
    else if($task_type == "OS")
    {
        $info = "_o";
    }
    else if($task_type == "SLS")
    {
        $info = "_sl";
    }

    return $info;
}

function GetTaskDepartmentInfo($task_type)
{
    $info = "";

    if($task_type == "LT")
    {
        $info = "Lighting";
    }
    else if($task_type == "OS")
    {
        $info = "Office System";
    }
    else if($task_type == "SLS")
    {
        $info = "Sales";
    }

    return $info;
}