<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'mail.php';

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


$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case 'GET':
        $stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);
        $page = (isset($_GET['page']) ?  $_GET['page'] : "");
        $size = (isset($_GET['size']) ?  $_GET['size'] : "");

        $sql = "SELECT pm.id task_id, title, priority, due_date, pm.`status` task_status, u.id uid, u.username creator, u.pic_url creator_pic, assignee, collaborator, detail, 
        pm.created_at task_date, COALESCE(f.filename, '') filename, COALESCE(f.gcp_name, '') gcp_name
        from project_other_task_r pm 
        LEFT JOIN user u ON u.id = pm.create_id 
        LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'other_task_r'
        where pm.stage_id = " . $stage_id . " ";

        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        $sql = $sql . " ORDER BY pm.id ";

        if (!empty($_GET['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $sql = $sql . " LIMIT " . $offset . "," . $size;
        }

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $task_id = 0;
        $title = "";
        $priority = "";
        $due_date = "";
        $task_status = "";
        $creator = "";
        $creator_id = 0;
        $creator_pic = "";
        $assignee = [];
        $collaborator = [];
        $detail = "";
        $task_date = "";
        $gcp_name = "";
        $filename = "";
        

        $items = [];
        $message = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($task_id != $row['task_id'] && $task_id != 0) {
                $merged_results[] = array(
                    "task_id" => $task_id,
                    "title" => $title,
                    "priority" => $priority,
                    "due_date" => $due_date,
                    "task_status" => $task_status,
                    "creator" => $creator,
                    "creator_id" => $creator_id,
                    "creator_pic" => $creator_pic,
                    "assignee" => $assignee,
                    "assignee_id" => $assignee_id,
                    "collaborator" => $collaborator,
                    "detail" => $detail,
                    "task_date" => $task_date,
                    "message" => $message,
                    "items" => $items,
                    
                );

                $message = [];
                $items = [];
                $collaborator = [];
                $assignee = [];
            }

            $task_id = $row['task_id'];
            $title = $row['title'];
            $priority = GetPriority($row['priority']);
            $due_date = $row['due_date'];
            $task_status = $row['task_status'];
            $creator = $row['creator'];
            $creator_id = $row['uid'];
            $creator_pic = $row['creator_pic'];
            if(empty($assignee ))
                $assignee = GetUserInfo($row['assignee'], $db);
            $assignee_id = explode(",", $row['assignee']);
            if(empty($collaborator ))
                $collaborator = GetUserInfo($row['collaborator'], $db);
            $message = GetMessage($row['task_id'], $db);
            $gcp_name = $row['gcp_name'];
            $filename = $row['filename'];
            $detail = $row['detail'];
            $task_date = $row['task_date'];

            

            $items = GetItemInfo($task_id, 'other_task_r', $db);
        }

        if ($task_id != 0) {
            $merged_results[] = array(
                "task_id" => $task_id,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "task_status" => $task_status,
                "creator" => $creator,
                "creator_id" => $creator_id,
                "creator_pic" => $creator_pic,
                "assignee" => $assignee,
                "assignee_id" => $assignee_id,
                "collaborator" => $collaborator,
                "detail" => $detail,
                "task_date" => $task_date,
                "message" => $message,
                "items" => $items,

            );
        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

    case 'POST':
        // get database connection
        $uid = $user_id;
        $stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
        $title = (isset($_POST['title']) ?  $_POST['title'] : '');
        $priority = (isset($_POST['priority']) ?  $_POST['priority'] : 0);
        $assignee = (isset($_POST['assignee']) ?  $_POST['assignee'] : '');
        $collaborator = (isset($_POST['collaborator']) ?  $_POST['collaborator'] : '');
        $due_date = (isset($_POST['due_date']) ?  $_POST['due_date'] : '');
        $detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');


        $query = "INSERT INTO project_other_task_r
        SET
            `stage_id` = :stage_id,
            `title` = :title,
            `priority` = :priority,
            `assignee` = :assignee,
            `collaborator` = :collaborator,
            `due_date` = :due_date,
            `detail` = :detail,
            
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':stage_id', $stage_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':assignee', $assignee);
        $stmt->bindParam(':collaborator', $collaborator);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':detail', $detail);

        $stmt->bindParam(':create_id', $uid);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                // send notify mail
                SendNotifyMail($last_id, $stage_id);

            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }


        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
}



function SendNotifyMail($last_id, $stage_id)
{
    $project_name = "";
    $task_name = "";
    $stages_status = "";
    $create_id = "";

    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);
 
    $project_name = $_record[0]["project_name"];
    $task_name = $_record[0]["task_name"];
    $stages_status = $_record[0]["stages_status"];
    $stages = $_record[0]["stage"];
    $create_id = $_record[0]["create_id"];
    $created_at = $_record[0]["created_at"];

    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $due_date = str_replace("-", "/", $_record[0]["due_date"]);
    $detail = $_record[0]["detail"];

    task_notify_r("create", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at);

}


function GetTaskDetail($id, $db)
{
    $sql = "SELECT  project_name, title task_name, 
            (CASE `stages_status_id` WHEN '1' THEN 'Ongoing' WHEN '2' THEN 'Pending' WHEN '3' THEN 'Close' END ) as `stages_status`, 
            pt.created_at,
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            stage,
            detail
            FROM project_other_task_r pt
            LEFT JOIN project_stages ps ON pt.stage_id = ps.id
            LEFT JOIN project_stage psg ON ps.stage_id = psg.id
            left JOIN project_main pm ON ps.project_id = pm.id 
            WHERE pt.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetUserInfo($users, $db)
{
    $sql = "SELECT id, username, pic_url FROM user WHERE id IN (" . $users . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetPriority($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "No Priority";
            break;
        case "2":
            $location = "Low";
            break;
        case "3":
            $location = "Normal";
            break;
        case "4":
            $location = "High";
            break;
        case "5":
            $location = "Urgent";
            break;
        
    }

    return $location;
}

function GetItemInfo($id, $batch_type, $db)
{
    $sql = "SELECT id,  COALESCE(f.filename, '') filename, COALESCE(f.gcp_name, '') gcp_name FROM gcp_storage_file f WHERE batch_id = :id AND batch_type = :batch_type and f.status <> -1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->bindParam(':batch_type',  $batch_type);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetMessage($task_id, $db)
{
    $sql = "select pmsgrp.id message_id, pmsgrp.message message, pmsgrp.`status` message_status, r.id uid, r.username messager, r.pic_url messager_pic, pmsgrp.created_at message_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from project_other_task_message_r pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            LEFT JOIN user p ON p.id = pmsgrp.updated_id 
            LEFT JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_r' 
            where pmsgrp.task_id = " . $task_id . " order by pmsgrp.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $message_id = 0;
    $message = "";
    $message_status = "";
    $messager = "";
    $messager_id = 0;
    $messager_pic = "";
    $message_date = "";
    $gcp_name = "";
    $filename = "";
    $updator = "";
    $update_date = "";

    $reply = [];
    $items = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($message_id != $row['message_id'] && $message_id != 0) {
            $merged_results[] = array(
                "message_id" => $message_id,
                "ref_id" => 0,
                "ref_name" => "",
                "ref_msg" => "",
                "message" => $message,
               
                "message_status" => $message_status,
                "messager" => $messager,
                "messager_pic" => $messager_pic,
                "messager_id" => $messager_id,

                "message_date" => explode(" ", $message_date)[0],
                "message_time" => explode(" ", $message_date)[1],

                "updator" => $updator,
                "update_date" => $update_date,

                "items" => $items,
            );

            if(!empty($reply))
                $merged_results = array_merge($merged_results, $reply);

            $reply = [];
            $items = [];
        }

        $message_id = $row['message_id'];
        $message = $row['message'];
        $message_status = $row['message_status'];
        $messager = $row['messager'];
        $messager_id = $row['uid'];
        $messager_pic = $row['messager_pic'];
        $message_date = $row['message_date'];

        $updator = $row['updator'];
        $update_date = $row['update_date'];

        if(empty($reply))
            $reply = GetReply($row['message_id'], $db, $message_id, $messager, $message);
     
        $gcp_name = $row['gcp_name'];
        $filename = $row['filename'];
      

        if ($filename != "")
            $items[] = array(
                'filename' => $filename,
                'gcp_name' => $gcp_name
            );
    }

    if ($message_id != 0) {
        $merged_results[] = array(
            "message_id" => $message_id,
            "ref_id" => 0,
            "ref_name" => "",
            "ref_msg" => "",
            "message" => $message,
          
            "message_status" => $message_status,
            "messager" => $messager,
            "messager_pic" => $messager_pic,
            "messager_id" => $messager_id,

            "message_date" => explode(" ", $message_date)[0],
            "message_time" => explode(" ", $message_date)[1],

            "updator" => $updator,
            "update_date" => $update_date,
        
            "items" => $items,
        );

        if(!empty($reply))
            $merged_results = array_merge($merged_results, $reply);
    }

    return $merged_results;
}

function GetReply($msg_id, $db, $id, $name, $msg)
{
    $sql = "select pmsgrp.id replay_id, pmsgrp.message reply, pmsgrp.`status` reply_status, r.id uid, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from project_other_task_message_reply_r pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            LEFT JOIN user p ON p.id = pmsgrp.updated_id 
            LEFT JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_rep_r' 
            where pmsgrp.message_id = " . $msg_id . " order by pmsgrp.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $replay_id = 0;
    $reply = "";
    $reply_status = "";
    $replyer = "";
    $replyer_id = 0;
    $replyer_pic = "";
    $reply_date = "";
    $gcp_name = "";
    $filename = "";
    $items = [];
    $updator = "";
    $update_date = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($replay_id != $row['replay_id'] && $replay_id != 0) {
            $merged_results[] = array(
                "message_id" => $replay_id,
                "ref_id" => $id,
                "ref_name" => $name,
                "ref_msg" => $msg,
                "message" => $reply,
              
                "message_status" => $reply_status,
                "messager" => $replyer,
                "messager_id" => $replyer_id,
                "messager_pic" => $replyer_pic,

                "message_date" => explode(" ", $reply_date)[0],
                "message_time" => explode(" ", $reply_date)[1],

                "updator" => $updator,
                "update_date" => $update_date,
         
                "items" => $items,
            );

            $items = [];
        }

        $replay_id = $row['replay_id'];
        $reply = $row['reply'];
        $reply_status = $row['reply_status'];
        $replyer = $row['replyer'];
        $replyer_id = $row['uid'];
        $replyer_pic = $row['replyer_pic'];
        $reply_date = $row['reply_date'];

        $updator = $row['updator'];
        $update_date = $row['update_date'];
     
        $gcp_name = $row['gcp_name'];
        $filename = $row['filename'];
      

        if ($filename != "")
            $items[] = array(
                'filename' => $filename,
                'gcp_name' => $gcp_name
            );
    }

    if ($replay_id != 0) {
        $merged_results[] = array(
            "message_id" => $replay_id,
            "ref_id" => $id,
            "ref_name" => $name,
            "ref_msg" => $msg,
            "message" => $reply,
            "message_status" => $reply_status,
            "messager" => $replyer,
            "messager_id" => $replyer_id,
            "messager_pic" => $replyer_pic,
            
            "message_date" => explode(" ", $reply_date)[0],
            "message_time" => explode(" ", $reply_date)[1],

            "updator" => $updator,
            "update_date" => $update_date,
        
            "items" => $items,
        );
    }

    return $merged_results;
}
