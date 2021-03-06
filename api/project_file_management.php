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

          $title = strtolower($decoded->data->position);
          //if(!$decoded->data->is_admin)
          //{
          //  http_response_code(401);
     
          //  echo json_encode(array("message" => "Access denied."));
          //  die();
          //}
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
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
            $pid = (isset($_GET['pid']) ?  $_GET['pid'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $merged_results = [];

            // comment
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_action_comment pac ON pm.id = pac.project_id
                                join gcp_storage_file on batch_id = pac.id and batch_type = 'comment'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Main Page',
                    "bucket" => $bucket,
                );
            }

            // additional detail
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_action_detail pac ON pm.id = pac.project_id
                                join gcp_storage_file on batch_id = pac.id and batch_type = 'action_detail'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];


                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Main Page',
                    "bucket" => $bucket,
                );
            }

            // downpayment
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_proof pac ON pm.id = pac.project_id
                                join gcp_storage_file on gcp_storage_file.batch_id = pac.id and batch_type = 'proof'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1 AND pac.`status` <> -2 ";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];


                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Downpayment',
                    "bucket" => $bucket,
                );
            }

            // approve
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_approve pac ON pm.id = pac.project_id
                                join gcp_storage_file on gcp_storage_file.batch_id = pac.id and batch_type = 'approve'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1 AND pac.`status` <> -2 ";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];


                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Approved Plan',
                    "bucket" => $bucket,
                );
            }


            if($title != "technician")
            {
                // quotation
                $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                    FROM project_main pm
                        JOIN project_quotation pac ON pm.id = pac.project_id
                                    join gcp_storage_file on gcp_storage_file.batch_id = pac.id and batch_type = 'quote'
                        join user on user.id = gcp_storage_file.create_id
                        where pm.id =" . $pid . " and pm.status <> -1";

                $stmt = $db->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $reply = $row['username'];
                    $reply_date = $row['created_at'];


                    $gcp_name = $row['gcp_name'];
                    $filename = $row['filename'];
                    $bucket = $row['bucketname'];

                    $merged_results[] = array(
                        "messager" => $reply,
                        "message_date" => explode(" ", $reply_date)[0],
                        "message_time" => explode(" ", $reply_date)[1],
                        "message_datetime" => $reply_date,
                        "gcp_name" => $gcp_name,
                        "filename" => $filename,
                        "url" => 'project02?p=' . $pid,
                        "stage" => 'Quotation',
                        "bucket" => $bucket,
                    );
                }
            }


            // stages
            $sql = "SELECT bucketname, filename, gcp_name, username, gcp_storage_file.created_at
                                FROM project_main pm
                    JOIN project_proof pac ON pm.id = pac.project_id
                                join gcp_storage_file on gcp_storage_file.batch_id = pac.id and batch_type = 'proof'
                    join user on user.id = gcp_storage_file.create_id
                    where pm.id =" . $pid . " and pm.status <> -1  AND pac.`status` <> -2 ";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];

                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project02?p=' . $pid,
                    "stage" => 'Downpayment',
                    "bucket" => $bucket,
                );
            }

            // loop for stages_status_id
            $sql = "SELECT ps.id, pst.stage
                            FROM project_main pm
                            JOIN project_stages ps ON pm.id = ps.project_id
                            JOIN project_stage pst ON ps.stage_id = pst.id
                    where pm.id = " . $pid . " and pm.status <> -1 ";

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pac_id = $row['id'];
                $stage = $row['stage'];

                $arr = GetAdditional($pac_id, $db, $pac_id, $stage);
                if(count($arr) > 0)
                    $merged_results = array_merge($merged_results, $arr);
             
                $arr = GetOtherTask($pac_id, $db, $pac_id, $stage);
                if(count($arr) > 0)
                    $merged_results = array_merge($merged_results, $arr);

                $arr = GetOtherTaskR($pac_id, $db, $pac_id, $stage);
                if(count($arr) > 0)
                    $merged_results = array_merge($merged_results, $arr);
                
            }

            $return_result = [];

            if(trim($keyword) == '')
            {
                if(count($merged_results) > 0)
                {
                    usort($merged_results, function ($item1, $item2) {
                        return $item2['message_datetime'] <=> $item1['message_datetime'];
                    });
                

                    // remove duplicate
                    $arry_keys = [];
                    $final_results = [];
                    foreach ($merged_results as $arr)
                    {
                        $str = $arr['message_datetime'] .  $arr['gcp_name'] . $arr['gcp_name'] . $arr['messager'];
                        if(!in_array($str, $arry_keys))
                            array_push($final_results, $arr);

                        array_push($arry_keys, $str);
                    }
                }
                else
                    $final_results = $merged_results;
                

                echo json_encode($final_results, JSON_UNESCAPED_SLASHES);
            }
            else
            {
                foreach ($merged_results as &$value) {
                    if(preg_match("/{$keyword}/i", $value['filename']) || preg_match("/{$keyword}/i", $value['messager']) || preg_match("/{$keyword}/i", $value['stage']) || preg_match("/{$keyword}/i", $value['message_date']) || preg_match("/{$keyword}/i", $value['message_time']))
                    {
                        $return_result[] = $value;
                    }
                }

                usort($return_result, function ($item1, $item2) {
                    return $item2['message_datetime'] <=> $item1['message_datetime'];
                });

                // remove duplicate
                $arry_keys = [];
                $final_results = [];
                foreach ($return_result as $arr)
                {
                    $str = $arr['message_datetime'] .  $arr['gcp_name'] . $arr['gcp_name'] . $arr['messager'];
                    if(!in_array($str, $arry_keys))
                        array_push($final_results, $arr);

                    array_push($arry_keys, $str);
                }

                echo json_encode($final_results, JSON_UNESCAPED_SLASHES);
            }
            break;
      }

      function GetAdditional($pac_id, $db, $pid, $stage)
      {
        $sql = "select bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_stage_client pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'additional'
        join user on user.id = gcp_storage_file.create_id
        where pm.stage_id = " . $pac_id;

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];

                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];
            
                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project03_client?sid=' . $pid,
                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            return $merged_results;
        }


    function GetOtherTask($pac_id, $db, $pid, $stage)
      {
        $sql = "select pm.id, bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_other_task pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'other_task'
        join user on user.id = gcp_storage_file.create_id
        where pm.stage_id = " . $pac_id;

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $reply = $row['username'];
            $reply_date = $row['created_at'];

            $gcp_name = $row['gcp_name'];
            $filename = $row['filename'];
            $bucket = $row['bucketname'];
        
            $merged_results[] = array(
                "messager" => $reply,
                "message_date" => explode(" ", $reply_date)[0],
                "message_time" => explode(" ", $reply_date)[1],
                "message_datetime" => $reply_date,
                "gcp_name" => $gcp_name,
                "filename" => $filename,
                "url" => 'project03_other?sid=' . $pid,
                "stage" => $stage,
                "bucket" => $bucket,
            );
        }

        $sql = "select pm.id from project_other_task pm where pm.stage_id = " . $pac_id;
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];

            $arr = GetOtherTaskMsg($id, $db, $pid, $stage);
            if(count($arr) > 0)
                $merged_results = array_merge($merged_results, $arr);
        }


        return $merged_results;

      }


      function GetOtherTaskR($pac_id, $db, $pid, $stage)
      {
        $sql = "select pm.id, bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_other_task_r pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'other_task_r'
        join user on user.id = gcp_storage_file.create_id
        where pm.stage_id = " . $pac_id;

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $reply = $row['username'];
            $reply_date = $row['created_at'];

            $gcp_name = $row['gcp_name'];
            $filename = $row['filename'];
            $bucket = $row['bucketname'];
        
            $merged_results[] = array(
                "messager" => $reply,
                "message_date" => explode(" ", $reply_date)[0],
                "message_time" => explode(" ", $reply_date)[1],
                "message_datetime" => $reply_date,
                "gcp_name" => $gcp_name,
                "filename" => $filename,
                "url" => 'project03_other?sid=' . $pid,
                "stage" => $stage,
                "bucket" => $bucket,
            );
        }

        $sql = "select pm.id from project_other_task_r pm where pm.stage_id = " . $pac_id;
        $stmt = $db->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];

            $arr = GetOtherTaskMsgR($id, $db, $pid, $stage);
            if(count($arr) > 0)
                $merged_results = array_merge($merged_results, $arr);
        }

        return $merged_results;
      }

      function GetOtherTaskMsg($pac_id, $db, $pid, $stage)
      {
        $sql = "select pm.id, bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_other_task_message pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'other_task_msg'
        join user on user.id = gcp_storage_file.create_id
        where pm.task_id = " . $pac_id;

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $reply = $row['username'];
                $reply_date = $row['created_at'];
    
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];
            
                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project03_other?sid=' . $pid,
                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }
            
            $sql = "select distinct pm.id from project_other_task_message pm where pm.task_id = " . $pac_id;
            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];

                $arr = GetOtherTaskMsgRep($id, $db, $pid, $stage);
                if(count($arr) > 0)
                    $merged_results = array_merge($merged_results, $arr);
            }
    
            return $merged_results;
      }

      function GetOtherTaskMsgR($pac_id, $db, $pid, $stage)
      {
        $sql = "select pm.id, bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_other_task_message_r pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'other_task_msg_r'
        join user on user.id = gcp_storage_file.create_id
        where pm.task_id = " . $pac_id;

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $reply = $row['username'];
                $reply_date = $row['created_at'];
    
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];
            
                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project03_other?sid=' . $pid,
                    "stage" => $stage,
                    "bucket" => $bucket,
                );
            }

            $sql = "select distinct pm.id from project_other_task_message_r pm where pm.task_id = " . $pac_id;
            $stmt = $db->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];

                $arr = GetOtherTaskMsgRepR($id, $db, $pid, $stage);
                if(count($arr) > 0)
                    $merged_results = array_merge($merged_results, $arr);
            }
    
            return $merged_results;
      }

      function GetOtherTaskMsgRep($pac_id, $db, $pid, $stage)
      {
        $sql = "select bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_other_task_message_reply pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'other_task_msg_rep'
        join user on user.id = gcp_storage_file.create_id
        where pm.message_id = " . $pac_id;

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();


            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];
    
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];
            
                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project03_other?sid=' . $pid,
                    "stage" => $stage,
                    "bucket" => $bucket,
                );

            }
    
            return $merged_results;
      }

      function GetOtherTaskMsgRepR($pac_id, $db, $pid, $stage)
      {
        $sql = "select bucketname, filename, gcp_name, username, gcp_storage_file.created_at
        from project_other_task_message_reply_r pm 
        join gcp_storage_file on batch_id = pm.id and batch_type = 'other_task_msg_rep_r'
        join user on user.id = gcp_storage_file.create_id
        where pm.message_id = " . $pac_id;

            $merged_results = array();

            $stmt = $db->prepare($sql);
            $stmt->execute();


            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reply = $row['username'];
                $reply_date = $row['created_at'];
    
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $bucket = $row['bucketname'];
            
                $merged_results[] = array(
                    "messager" => $reply,
                    "message_date" => explode(" ", $reply_date)[0],
                    "message_time" => explode(" ", $reply_date)[1],
                    "message_datetime" => $reply_date,
                    "gcp_name" => $gcp_name,
                    "filename" => $filename,
                    "url" => 'project03_other?sid=' . $pid,
                    "stage" => $stage,
                    "bucket" => $bucket,
                );
    
            }
    
            return $merged_results;
      }


?>
