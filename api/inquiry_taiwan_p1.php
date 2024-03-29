<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$type = (isset($_GET['type']) ?  $_GET['type'] : 0);
$confirm = (isset($_GET['confirm']) ?  $_GET['confirm'] : '');
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$pg = (isset($_GET['pg']) ?  $_GET['pg'] : 0);
$user_id = 0;


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
    $GLOBALS["user_id"] = $decoded->data->id;

    $merged_results = array();
    

    $query = "SELECT iq_item.id, b.serial_number, iq_id,
                    sn, 
                    confirm, 
                    brand, 
                    brand_other, 
                    photo1, 
                    photo2, 
                    photo3, 
                    code,
                    brief,
                    listing,
                    qty,
                    srp,
                    date_needed,
                    shipping_way,
                    shipping_number,
                    shipping_vendor,
                    pid,
                    eta,
                    arrive,
                    remark,
                    remark_t,
                    remark_d,
                    check_t,
                    check_d,
                    charge,
                    photo4,
                    photo5,
                    photo4_name,
                    photo5_name,
                    test,
                    delivery,
                    final,
                    `status`,
                    test_updated_name,
                    test_updated_at,
                    delivery_updated_name,
                    delivery_updated_at,
                    create_id
                    FROM iq_item, 
                    (SELECT @a:=@a+1 serial_number, id FROM iq_item, (SELECT @a:= 0) AS a WHERE status <> -1 and iq_id=$id order by ABS(sn)) b
                    WHERE status <> -1 and iq_id=$id and iq_item.id = b.id
                    ";
                    

    if($confirm != '')
        $query = $query . " and confirm = '$confirm' ";

    if($pg != 0)
    {
        if($pg == 1)
            $query = $query . " and status <= $pg ";
        else if($pg == 2)
            $query = $query . " and status = $pg ";
        else if($pg == 3)
            $query = $query . " and status >= $pg ";
    }
        


    $query = $query . " order by ABS(sn) ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $sn = $row['sn'];
        $confirm = $row['confirm'];

        // send to tw for note
        //if($row['status'] == 1)
        //    $confirm = "W";

        
        $confirm_text = GetConfirmText($confirm, $db);
        $brand = $row['brand'];
        $brand_other = $row['brand_other'];
        $photo1 = ($row['photo1'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo1'] : '';
        $photo2 = ($row['photo2'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo2'] : '';
        $photo3 = ($row['photo3'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo3'] : '';
        $code = $row['code'];
        $brief = $row['brief'];
        $listing = $row['listing'];
        $qty = $row['qty'];
        $srp = $row['srp'];
        $date_needed = $row['date_needed'];
        $shipping_way = $row['shipping_way'];
        $shipping_number = $row['shipping_number'];
        $shipping_vendor = $row['shipping_vendor'];
        $eta = $row['eta'];
        $arrive = $row['arrive'];
        $remark = $row['remark'];
        $remark_t = $row['remark_t'];
        $remark_d = $row['remark_d'];
        $check_t = $row['check_t'];
        $check_d = $row['check_d'];
        $charge = $row['charge'];
        $photo4 = ($row['photo4'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo4'] : '';
        $photo5 = ($row['photo5'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo5'] : '';
        $photo4_name = $row['photo4_name'];
        $photo5_name = $row['photo5_name'];
        $test = $row['test'];
        $delivery = $row['delivery'];
        $final = $row['final'];

        $pid = $row['pid'];

        $serial_number = $row['serial_number'];

        $test_updated_name = $row['test_updated_name'];
        $test_updated_at = $row['test_updated_at'];
        $delivery_updated_name = $row['delivery_updated_name'];
        $delivery_updated_at = $row['delivery_updated_at'];

        $status = $row['status'];
        $notes = GetNotes($row['id'], $db);

        $create_id = $row['create_id'];

        //$notes_a = GetNotesA($row['id'], $db);

        $iq_id = $row['iq_id'];
        $iq_main = GetIqMain($iq_id, $db);
        $iq_status = $iq_main[0]['status'];
        $iq_updated_id = $iq_main[0]['updated_id'];

        $iq_submit_id  = 0;
        $iq_submit = GetLatestSubmitId($iq_id, $db);
        if(count($iq_submit) > 0)
            $iq_submit_id = $iq_submit[0]['create_id'];
        
        $merged_results[] = array(
            "is_checked" => "",
            "is_edit" => false,
            "is_info" => false,
            "id" => $id,
            "sn" => $sn,
            "confirm" => $confirm,
            "brand" => $brand,
            "brand_other" => $brand_other,
            "photo1" => $photo1,
            "photo2" => $photo2,
            "photo3" => $photo3,
            "code" => $code,
            "brief" => $brief,
            "listing" => $listing,
            "qty" => $qty,
            "srp" => $srp,
            "date_needed" => $date_needed,
            "shipping_way" => $shipping_way,
            "shipping_number" => $shipping_number,
            "shipping_vendor" => $shipping_vendor,
            "pid" => $pid,
            "eta" => $eta,
            "arrive" => $arrive,
            "remark" => $remark,
            "remark_t" => $remark_t,
            "remark_d" => $remark_d,
            "check_t" => $check_t,
            "check_d" => $check_d,
            "charge" => $charge,
            "photo4" => $photo4,
            "photo5" => $photo5,
            "photo4_name" => $photo4_name,
            "photo5_name" => $photo5_name,
            "test" => $test,
            "delivery" => $delivery,
            "final" => $final,
            "status" => $status,
            "test_updated_name" => $test_updated_name,
            "test_updated_at" => $test_updated_at,
            "delivery_updated_name" => $delivery_updated_name,
            "delivery_updated_at" => $delivery_updated_at,
            "confirm_text" => $confirm_text,
            "notes" => $notes,
            //"notes_a" => $notes_a,
            "serial_number" => $serial_number,
            "create_id" => $create_id,

            "iq_status" => $iq_status,
            //"iq_updated_id" => $iq_updated_id,

            "iq_updated_id" => $iq_submit_id,
            
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}


function GetNotes($id, $db){
    $query = "
            SELECT n.id,
                n.status,
                n.message,
                n.create_id,
                u.username,
                n.created_at
            FROM   iq_message n
            left join user u on n.create_id = u.id
            WHERE  n.item_id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $status = $row['status'];
        $message = $row['message'];
        $create_id = $row['create_id'];
        $username = $row['username'];

        $created_at = $row['created_at'];
      
        $attachs = [];
        $got_it = [];
        $i_got_it = false;

        $attachs = GetAttach($id, $db);
        $got_it = GetGotIt($id, $db);

        foreach ($got_it as $g) {
            if ($g['uid'] == $GLOBALS["user_id"]) {
                $i_got_it = true;
                break;
            }
        }

        if($GLOBALS["user_id"] == $row["create_id"])
            $i_got_it = true;
    
        $merged_results[] = array(
            "id" => $id,
            "status" => $status,
            "message" => $message,
            "create_id" => $create_id,
            "username" => $username,
            "created_at" => $created_at,
            "attachs" => $attachs,
            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );
    }

    return $merged_results;
}



function GetAttach($id, $db)
{
    $sql = "select COALESCE(filename, '') filename, COALESCE(gcp_name, '') gcp_name
            from gcp_storage_file where batch_id = " . $id . " and batch_type = 'iq_message' 
            order by created_at ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = array(
            "filename" => $row['filename'],
            "gcp_name" => $row['gcp_name'],
        );
    }

    return $result;
}


function GetGotIt($msg_id, $db)
{
    $sql = "select  u.id uid, u.username username
            from iq_got_it g
            LEFT JOIN user u ON u.id = g.create_id
            where g.message_id = " . $msg_id . " order by g.created_at";

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

function GetIqMain($id, $db)
{
    $query = "select status, updated_id from iq_main where id = " . $id;
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = array(
            "status" => $row['status'],
            "updated_id" => $row['updated_id'],
        );
    }

    return $merged_results;
}

function GetLatestSubmitId($id, $db)
{
    $query = "select create_id from iq_process ip where iq_id = " . $id . " and action = 'send_note' order by id desc limit 1";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = array(
            "create_id" => $row['create_id'],
        );
    }

    return $merged_results;
}


function GetNotesA($id, $db){
    $query = "
            SELECT n.id,
                n.status,
                n.message,
                n.create_id,
                u.username,
                n.created_at
            FROM   iq_message_a n
            left join user u on n.create_id = u.id
            WHERE  n.item_id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $status = $row['status'];
        $message = $row['message'];
        $create_id = $row['create_id'];
        $username = $row['username'];

        $created_at = $row['created_at'];
      
        $attachs = [];
        $got_it = [];
        $i_got_it = false;

        //$attachs = GetAttachA($id, $db);
        //$got_it = GetGotItA($id, $db);

        foreach ($got_it as $g) {
            if ($g['uid'] == $GLOBALS["user_id"]) {
                $i_got_it = true;
                break;
            }
        }

        if($GLOBALS["user_id"] == $row["create_id"])
            $i_got_it = true;
    
        $merged_results[] = array(
            "id" => $id,
            "status" => $status,
            "message" => $message,
            "create_id" => $create_id,
            "username" => $username,
            "created_at" => $created_at,
            "attachs" => $attachs,
            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );
    }

    return $merged_results;
}



function GetAttachA($id, $db)
{
    $sql = "select COALESCE(filename, '') filename, COALESCE(gcp_name, '') gcp_name
            from gcp_storage_file where batch_id = " . $id . " and batch_type = 'iq_message_a' 
            order by created_at ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = array(
            "filename" => $row['filename'],
            "gcp_name" => $row['gcp_name'],
        );
    }

    return $result;
}


function GetGotItA($msg_id, $db)
{
    $sql = "select  u.id uid, u.username username
            from iq_got_it_a g
            LEFT JOIN user u ON u.id = g.create_id
            where g.message_id = " . $msg_id . " order by g.created_at";

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

function GetConfirmText($loc)
{
    $location = "";
    switch ($loc) {
        case "N":
            $location = "Price Inquiry";
            break;
        case "C":
            $location = "Stock Inquiry";
            break;
        case "D":
            $location = "Product Inquiry";
            break;
        case "W":
            $location = "Waiting Feedback from TW";
            break;
        default:
            $location = "";
            break;
                
    }

    return $location;
}
