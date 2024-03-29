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

header("Access-Control-Allow-Origin: *");

include_once "config/database.php";


$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case "GET":
        $uid = $user_id;
        $id = (isset($_GET["id"]) ?  $_GET["id"] : 0);
        $fs = (isset($_GET["fs"]) ?  $_GET["fs"] : "");
        $fp = (isset($_GET["fp"]) ?  $_GET["fp"] : 0);
        $fc = (isset($_GET["fc"]) ?  $_GET["fc"] : "");
        $fk = (isset($_GET["fk"]) ?  $_GET["fk"] : "");
        $ft = (isset($_GET["ft"]) ?  $_GET["ft"] : "");

        $fc = urldecode($fc);
        $fk = urldecode($fk);

        $page = (isset($_GET["page"]) ?  $_GET["page"] : "");
        $size = (isset($_GET["size"]) ?  $_GET["size"] : "");

        $sql = "SELECT pm.id task_id, pm.title, pm.priority, due_date, due_time, pm.`status` task_status, u.id uid, u.username creator, u.pic_url creator_pic, assignee, collaborator, detail, 
        pm.created_at task_date, COALESCE(f.filename, '') filename, COALESCE(f.gcp_name, '') gcp_name, ut.title creator_title, pp.class_name pp_class, pm.related_order, pm.related_tab, pm.related_kind, pm.related_category
        from project_other_task_o pm 
        LEFT JOIN user u ON u.id = pm.create_id 
        LEFT JOIN user_title ut ON u.title_id = ut.id
        LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'other_task_o'
        LEFT JOIN project_priority pp
            ON pm.priority = pp.id
        where pm.`status` <> -1 " . ($id != 0 ? " and pm.id=$id" : ' ');

        if ($fs != '') {
            $sql = $sql . " and pm.`status` = " . $fs . " ";
        }

        if ($fp != 0) {
            $sql = $sql . " and pm.priority = " . $fp . " ";
        }

        if ($fc != '') {
            $sql = $sql . " and u.username = '" . $fc . "' ";
        }

        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        $sql = $sql . " ORDER BY pm.created_at desc ";

        if (!empty($_GET['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $sql = $sql . " LIMIT " . $offset . "," . $size;
        }

        $merged_results = array();
        $return_result = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $task_id = 0;
        $title = "";
        $priority = "";
        $due_date = "";
        $due_time = "";
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

        // order
        $related_order = "";
        $related_tab = "";
        $related_kind = "";
        $related_category = "";
        $order = [];
        $od_name = "";
        $od_type = "";
        $related_order_name = "";
        $related_serial_name = "";

        $inquiry = [];
        $iq_name = "";
        $iq_type = "";
        $related_inquiry = "";
        $related_inquiry_name = "";
        $related_inquiry_serial_name = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($task_id != $row['task_id'] && $task_id != 0) {
                $merged_results[] = array(
                    "task_id" => $task_id,
                    "title" => $title,
                    "priority" => $priority,
                    "priority_id" => $priority_id,
                    "due_date" => $due_date,
                    "due_time" => $due_time,
                    "task_status" => $task_status,
                    "creator" => $creator,
                    "creator_id" => $creator_id,
                    "creator_pic" => $creator_pic,
                    "assignee" => $assignee,
                    "assignee_id" => $assignee_id,
                    "collaborator" => $collaborator,
                    "collaborator_id" => $collaborator_id,
                    "detail" => $detail,
                    "task_date" => $task_date,
                    "message" => $message,
                    "items" => $items,
                    "nearest_time" => $_nearest_time,
                    "nearest_user" => $_nearest_user,
                    "nearest_msg" => $_nearest_msg,
                    "creator_title" => $creator_title,
                    "pp_class" => $pp_class,

                    "order" => $order,
                    "od_type" => $od_type,
                    "od_name" => $od_name,
                    "related_order" => $related_order,
                    "related_tab" => $related_tab,
                    "related_kind" => $related_kind,
                    "related_category" => $related_category,
                    "related_order_name" => $related_order_name,
                    "related_serial_name" => $related_serial_name,

                    "inquiry" => $inquiry,
                    "iq_type" => $iq_type,
                    "iq_name" => $iq_name,
                    "related_inquiry" => $related_inquiry,
                    "related_inquiry_name" => $related_inquiry_name,
                    "related_inquiry_serial_name" => $related_inquiry_serial_name,
                );

                $message = [];
                $items = [];
                $collaborator = [];
                $assignee = [];
            }

            $task_id = $row['task_id'];
            $title = $row['title'];
            $priority = GetPriority($row['priority']);
            $priority_id = $row['priority'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $creator = $row['creator'];
            $creator_id = $row['uid'];
            $creator_pic = $row['creator_pic'];
            if(empty($assignee ))
                $assignee = GetUserInfo($row['assignee'], $db);
            $assignee_id = explode(",", $row['assignee']);
            if(empty($collaborator ))
                $collaborator = GetUserInfo($row['collaborator'], $db);
            $collaborator_id = explode(",", $row['collaborator']);
            $message = GetMessage($row['task_id'], $db, $uid);
            $gcp_name = $row['gcp_name'];
            $filename = $row['filename'];
            $detail = $row['detail'];
            $task_date = $row['task_date'];

            $creator_title = $row['creator_title'];

            $pp_class = $row['pp_class'];

            $_nearest_time = "";
            $_nearest_user = "";
            $_nearest_msg = "";
            foreach ($message as &$value) {
                $_time = $value['message_date'] . ' ' . $value['message_time'];
                if($_time > $_nearest_time)
                {
                    $_nearest_time = $_time;
                    $_nearest_user = $value['messager'];
                    $_nearest_msg = $value['message'];
                }
            }

            $items = GetItemInfo($task_id, 'other_task_o', $db);

            $related_order = $row['related_order'];
            $related_tab = $row['related_tab'];
            $related_kind = $row['related_kind'];
            $related_category = $row['related_category'];

            $order = GetOrderInfo($task_id, $db);


            if(count($order) > 0)
            {
                $od_name = $order[0]['od_name'];
                $od_type = $order[0]['order_type'];
            }

            $related_order_data = [];
            if($related_order != '')
                $related_order_data = GetRelatedOrderInfo($related_order, $db);

            if(count($related_order_data) > 0)
            {
                $related_order_name = $related_order_data[0]['od_name'];
                $related_serial_name = $related_order_data[0]['serial_name'];
            }
            else
            {
                $related_order_name = "";
                $related_serial_name = "";
            }

            $inquiry = GetInquiryInfo($task_id, $db);
            if(count($inquiry) > 0)
            {
                $iq_name = $inquiry[0]['iq_name'];
                $iq_type = $inquiry[0]['order_type'];
            }
            $related_inquiry_data = [];
            if($related_inquiry != '')
                $related_inquiry_data = GetRelatedInquiryInfo($related_inquiry, $db);

            if(count($related_inquiry_data) > 0)
            {
                $related_inquiry_name = $related_inquiry_data[0]['iq_name'];
                $related_inquiry_serial_name = $related_inquiry_data[0]['serial_name'];
            }
            else
            {
                $related_inquiry_name = "";
                $related_inquiry_serial_name = "";
            }
        }

        if ($task_id != 0) {
            $merged_results[] = array(
                "task_id" => $task_id,
                "title" => $title,
                "priority" => $priority,
                "priority_id" => $priority_id,
                "due_date" => $due_date,
                "due_time" => $due_time,
                "task_status" => $task_status,
                "creator" => $creator,
                "creator_id" => $creator_id,
                "creator_pic" => $creator_pic,
                "assignee" => $assignee,
                "assignee_id" => $assignee_id,
                "collaborator" => $collaborator,
                "collaborator_id" => $collaborator_id,
                "detail" => $detail,
                "task_date" => $task_date,
                "message" => $message,
                "items" => $items,
                "nearest_time" => $_nearest_time,
                "nearest_user" => $_nearest_user,
                "nearest_msg" => $_nearest_msg,
                "creator_title" => $creator_title,
                "pp_class" => $pp_class,

                "order" => $order,
                "od_type" => $od_type,
                "od_name" => $od_name,
                "related_order" => $related_order,
                "related_tab" => $related_tab,
                "related_kind" => $related_kind,
                "related_category" => $related_category,
                "related_order_name" => $related_order_name,
                "related_serial_name" => $related_serial_name,

                "inquiry" => $inquiry,
                "iq_type" => $iq_type,
                "iq_name" => $iq_name,
                "related_inquiry" => $related_inquiry,
                "related_inquiry_name" => $related_inquiry_name,
                "related_inquiry_serial_name" => $related_inquiry_serial_name,
            );
        }

        if($ft == "r")  // regular test
        {
            $_result = array();
            
            foreach ($merged_results as &$value) {
                if(count($value['order']) == 0 && count($value['inquiry']) == 0)
                {
                    $_result[] = $value;
                }
            }

                $merged_results = $_result;
        }

        if($ft == "o")  // Order Task
        {
            $_result = array();
            
            foreach ($merged_results as &$value) {
                if(count($value['order']) > 0)
                {
                    $_result[] = $value;
                }
            }

                $merged_results = $_result;
        }

        if($ft == "i")  // Inquiry Task
        {
            $_result = array();
            
            foreach ($merged_results as &$value) {
                if(count($value['inquiry']) > 0)
                {
                    $_result[] = $value;
                }
            }

                $merged_results = $_result;
        }

        
        if($fk != "" && $id == 0)
        {
            $_result = array();

            foreach ($merged_results as &$value) {
                if(
                    preg_match("/{$fk}/i", $value['title']) || 
                    preg_match("/{$fk}/i", $value['nearest_time']) || 
                    preg_match("/{$fk}/i", $value['nearest_user']) || 
                    preg_match("/{$fk}/i", $value['nearest_msg']))
                {
                    $_result[] = $value;
                }
            }

                $return_result = $_result;
            
        }
        else
            $return_result = $merged_results;

        echo json_encode($return_result, JSON_UNESCAPED_SLASHES);

        break;

    case 'POST':
        // get database connection
        $uid = $user_id;
        $stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
        $title = (isset($_POST['title']) ?  $_POST['title'] : '');
        $priority = (isset($_POST['priority']) ?  $_POST['priority'] : 0);
        $status = (isset($_POST['status']) ?  $_POST['status'] : 0);
        $assignee = (isset($_POST['assignee']) ?  $_POST['assignee'] : '');
        $collaborator = (isset($_POST['collaborator']) ?  $_POST['collaborator'] : '');
        $due_date = (isset($_POST['due_date']) ?  $_POST['due_date'] : '');
        $due_time = (isset($_POST['due_time']) ?  $_POST['due_time'] : '');
        $detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');


        $query = "INSERT INTO project_other_task_o
        SET
            `stage_id` = :stage_id,
            `title` = :title,
            `priority` = :priority,
            `status` = :status,
            `assignee` = :assignee,
            `collaborator` = :collaborator,
            `due_date` = :due_date,
            `due_time` = :due_time,
            `detail` = :detail,
            
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':stage_id', $stage_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':assignee', $assignee);
        $stmt->bindParam(':collaborator', $collaborator);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':due_time', $due_time);
        $stmt->bindParam(':detail', $detail);

        $stmt->bindParam(':create_id', $uid);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                // send notify mail
                SendNotifyMail($last_id);
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

function SendNotifyMail($last_id)
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

    $due_date = str_replace("-", "/", $_record[0]["due_date"]) . " " . $_record[0]["due_time"];
    $detail = $_record[0]["detail"];

    task_notify_admin_o("create", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $last_id, 0, 0, $created_at);

}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT 'Ongoing' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.created_at,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            detail
            FROM project_other_task_o pt
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

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case "0":
            $location = "Ongoing";
            break;
        case "1":
            $location = "Pending";
            break;
        case "2":
            $location = "Close";
            break;
                
    }

    return $location;
}


function GetMessage($task_id, $db, $uid)
{
    $sql = "select pmsgrp.id message_id, pmsgrp.message message, pmsgrp.`status` message_status, r.id uid, r.username messager, r.pic_url messager_pic, pmsgrp.created_at message_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from project_other_task_message_o pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            LEFT JOIN user p ON p.id = pmsgrp.updated_id 
            LEFT JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_o' 
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
    
    $got_it = [];
    $i_got_it = false;

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
                "messager_id" => $messager_id,
                "messager_pic" => $messager_pic,

                "message_date" => explode(" ", $message_date)[0],
                "message_time" => explode(" ", $message_date)[1],

                "updator" => $updator,
                "update_date" => $update_date,

                "items" => $items,
                "i_got_it" => $i_got_it,
                "got_it" => $got_it,
            );

            if(!empty($reply))
                $merged_results = array_merge($merged_results, $reply);

            $reply = [];
            $items = [];

            $got_it = [];
            $i_got_it = false;
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
            $reply = GetReply($row['message_id'], $db, $message_id, $messager, $message, $uid);
     
        $gcp_name = $row['gcp_name'];
        $filename = $row['filename'];

        // got it
        $got_it = GetGotIt($row['message_id'], 'os', $db);
        foreach ($got_it as $g) {
            if ($g['uid'] == $uid) {
                $i_got_it = true;
                break;
            }
        }

        if($uid == $row["uid"])
            $i_got_it = true;
      

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
            "messager_id" => $messager_id,
            "messager_pic" => $messager_pic,

            "message_date" => explode(" ", $message_date)[0],
            "message_time" => explode(" ", $message_date)[1],

            "updator" => $updator,
            "update_date" => $update_date,
        
            "items" => $items,

            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );

        if(!empty($reply))
            $merged_results = array_merge($merged_results, $reply);
    }

    return $merged_results;
}


function GetGotIt($msg_id, $kind, $db)
{
    $sql = "select  u.id uid, u.username username
            from project_got_it g
            LEFT JOIN user u ON u.id = g.create_id
            where g.message_id = " . $msg_id . " AND g.kind = '" . $kind . "' order by g.created_at";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $got_it = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $got_it[] = array(
            "uid" => $row['uid'],
            "username" => $row['username'],
        );
    }

    return $got_it;
}

function GetGotItReply($reply_id, $kind, $db)
{
    $sql = "select  u.id uid, u.username username
            from project_got_it g
            LEFT JOIN user u ON u.id = g.create_id
            where g.reply_id = " . $reply_id . " AND g.kind = '" . $kind . "' order by g.created_at";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $got_it = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $got_it[] = array(
            "uid" => $row['uid'],
            "username" => $row['username'],
        );
    }

    return $got_it;
}

function GetReply($msg_id, $db, $id, $name, $msg, $uid)
{
    $sql = "select pmsgrp.id replay_id, pmsgrp.message reply, pmsgrp.`status` reply_status, r.id uid, r.username replyer, r.pic_url replyer_pic, pmsgrp.created_at reply_date, COALESCE(p.username, '') updator, COALESCE(pmsgrp.updated_at, '') update_date,
            COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name, pmsgrp.reply_id ref_id
            from project_other_task_message_reply_o pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            LEFT JOIN user p ON p.id = pmsgrp.updated_id 
            LEFT JOIN gcp_storage_file h ON h.batch_id = pmsgrp.id AND h.batch_type = 'other_task_msg_rep_o' 
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

    $got_it = [];
    $i_got_it = false;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $ref_name = $name;
        $ref_reply = $msg;

        if ($replay_id != $row['replay_id'] && $replay_id != 0) {
            $merged_results[] = array(
                "message_id" => $replay_id,
                "ref_id" => $id,
                "ref_name" => $ref_name,
                "ref_msg" => $ref_reply,
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

                "got_it" => $got_it,
                "i_got_it" => $i_got_it,
            );

            $items = [];

            $got_it = [];
            $i_got_it = false;
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

        $ref_id = $row['ref_id'];

        if($ref_id != 0)
        {
            $ref_name = GetRefMsgName($ref_id, $db);
            $ref_reply = GetRefMsgValue($ref_id, $db);
        }

        // got it
        $got_it = GetGotItReply($row['replay_id'], 'os', $db);
        // look for user_id in got_it
        foreach ($got_it as $g) {
            if ($g['uid'] == $uid) {
                $i_got_it = true;
                break;
            }
        }

        if($uid == $row["uid"])
            $i_got_it = true;

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
            "ref_name" => $ref_name,
            "ref_msg" => $ref_reply,
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

            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );
    }

    return $merged_results;
}


function GetRefMsgName($ref_id, $db)
{
    $sql = "select r.username replyer
            from project_other_task_message_reply_o pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            where pmsgrp.id = " . $ref_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $replyer = "";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $replyer = $row['replyer'];
    }

    return $replyer;
}

function GetRefMsgValue($ref_id, $db)
{
    $sql = "select message reply
            from project_other_task_message_reply_o pmsgrp 
            LEFT JOIN user r ON r.id = pmsgrp.create_id 
            where pmsgrp.id = " . $ref_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $reply = "";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reply = $row['reply'];
    }
    
    return $reply;
}



function GetOrderInfo($task_id, $db)
{
    $sql = "select id, od_name, order_type, serial_name
            from od_main
            where task_type = 'OS' and task_id = " . $task_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "od_name" => $row['od_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}



function GetRelatedOrderInfo($_id, $db)
{
    $sql = "select id, od_name, order_type, serial_name
            from od_main
            where id = " . $_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "od_name" => $row['od_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}


function GetInquiryInfo($task_id, $db)
{
    $sql = "select id, iq_name, order_type, serial_name
            from iq_main
            where task_type = 'OS' and task_id = " . $task_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "iq_name" => $row['iq_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}

function GetRelatedInquiryInfo($_id, $db)
{
    $sql = "select id, iq_name, order_type, serial_name
            from iq_main
            where id = " . $_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "iq_name" => $row['iq_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}