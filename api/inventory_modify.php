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


$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case 'GET':
  
        $id = (isset($_GET['id']) ?  $_GET['id'] : '');
        $fru = (isset($_GET['fru']) ?  $_GET['fru'] : '');
        $frl = (isset($_GET['frl']) ?  $_GET['frl'] : '');

        $fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');
        $fc = urldecode($fc);
        $fch = (isset($_GET['fch']) ?  $_GET['fch'] : '');
        $fch = urldecode($fch);
        $fap = (isset($_GET['fap']) ?  $_GET['fap'] : '');
        $fap = urldecode($fap);

        $fp = (isset($_GET['fp']) ? $_GET['fp'] : '');
        $fp = urldecode($fp);

        $fk = (isset($_GET['fk']) ?  $_GET['fk'] : '');
        $fk = urldecode($fk);

        $ft = (isset($_GET['ft']) ?  $_GET['ft'] : '');
        $fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');
        $fat = (isset($_GET['fat']) ?  $_GET['fat'] : '');
        $fau = (isset($_GET['fau']) ?  $_GET['fau'] : '');
        $fal = (isset($_GET['fal']) ?  $_GET['fal'] : '');

        $ftd = (isset($_GET['ftd']) ?  $_GET['ftd'] : '');

        $fds = (isset($_GET['fds']) ?  $_GET['fds'] : '');
        $fds = str_replace('-', '/', $fds);
        $fde = (isset($_GET['fde']) ?  $_GET['fde'] : '');
        $fde = str_replace('-', '/', $fde);

        $fus = (isset($_GET['fus']) ?  $_GET['fus'] : '');
        $fus = str_replace('-', '/', $fus);
        $fue = (isset($_GET['fue']) ?  $_GET['fue'] : '');
        $fue = str_replace('-', '/', $fue);

        $of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
        $ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
        $of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
        $ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

        $page = (isset($_GET['page']) ?  $_GET['page'] : 1);
        $size = (isset($_GET['size']) ?  $_GET['size'] : 20);

        // check if can see petty expense list (Record only for himself)
        /*
        $sql = "select * from expense_flow where uid = " . $user_id . " where status <> -1";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $arry_apartment_id = [];
        $array_flow = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $apartment_id = $row['apartment_id'];
            $flow = $row['flow'];
            
            array_push($arry_apartment_id, $apartment_id);
            array_push($array_flow, $flow);
        }
        */

        $query_cnt = "SELECT  count(*) cnt
                from inventory_modify pm 
                LEFT JOIN user p ON p.id = pm.create_id 
                LEFT JOIN user c ON c.id = pm.checker
                LEFT JOIN user a ON a.id = pm.approver
                where 1=1  ";

        $sql = "SELECT  pm.id,
                        request_no, 
                        check_name,
                        date_requested,
                        pm.`status`,
                        create_id,
                        checker checker_id,
                        approver approver_id,
                        p.username,
                        p.username created_by,
                        u.username updated_by,
                        DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at,
                        DATE_FORMAT(pm.updated_at, '%Y/%m/%d %T') updated_at,
                        c.username checker,
                        a.username approver,
                        reason,
                        note_1,
                        receive_id,
                        listing,
                        which_pool,
                        as_sample,
                        location,
                        project_id
                from inventory_modify pm 
                LEFT JOIN user p ON p.id = pm.create_id 
                LEFT JOIN user u ON u.id = pm.updated_id
                LEFT JOIN user c ON c.id = pm.checker
                LEFT JOIN user a ON a.id = pm.approver
                where 1=1 ";

if($id != "" && $id != "0")
{
    $sql = $sql . " and pm.id = '" . $id . "' ";
    $query_cnt = $query_cnt . " and pm.id = '" . $id . "' ";
}

if($ft != "" && $ft != "0")
{
    $sql = $sql . " and pm.request_type = '" . $ft . "' ";
    $query_cnt = $query_cnt . " and pm.request_type = '" . $ft . "' ";
}

if($frl != "")
{
    $sql = $sql . " and pm.request_no >= 'IC-" . sprintf('%05d', $frl) . "' ";
    $query_cnt = $query_cnt . " and pm.request_no >= 'IC-" . sprintf('%05d', $frl) . "' ";
}

if($fru != "")
{
    $sql = $sql . " and pm.request_no <= 'IC-" . sprintf('%05d', $fru) . "' ";
    $query_cnt = $query_cnt . " and pm.request_no <= 'IC-" . sprintf('%05d', $fru) . "' ";
}

if($fc != "")
{
    $sql = $sql . " and p.username = '" . $fc . "' ";
    $query_cnt = $query_cnt . " and p.username = '" . $fc . "' ";
}

if($fch != "")
{
    $sql = $sql . " and c.username = '" . $fch . "' ";
    $query_cnt = $query_cnt . " and c.username = '" . $fch . "' ";
}

if($fap != "")
{
    $sql = $sql . " and a.username = '" . $fap . "' ";
    $query_cnt = $query_cnt . " and a.username = '" . $fap . "' ";
}

if($fc != "")
{
    $sql = $sql . " and p.username = '" . $fc . "' ";
    $query_cnt = $query_cnt . " and p.username = '" . $fc . "' ";
}

if($fp != "")
{
    $sql = $sql . " and pm.project_name1 = '" . $fp . "' ";
    $query_cnt = $query_cnt . " and pm.project_name1 = '" . $fp . "' ";
}

if($fk != "")
{
    $sql = $sql . " and pm.check_name like '%" . $fk . "%' ";
    $query_cnt = $query_cnt . " and pm.check_name like '%" . $fk . "%' ";
}

$status_array = [];


if($fs != "" && $fs != "0")
{
    if(strpos($fs,"1") > -1)
        $status_array = array_merge($status_array, [1]);
    if(strpos($fs,"2") > -1)
        $status_array = array_merge($status_array, [2]);
    if(strpos($fs,"3") > -1)
        $status_array = array_merge($status_array, [3]);
    if(strpos($fs,"4") > -1)
        $status_array = array_merge($status_array, [4]);
}

if(count($status_array) > 0)
{
    $sql = $sql . " and pm.`status` in (" . implode(",", $status_array) . ") ";
    $query_cnt = $query_cnt . " and pm.`status` in (" . implode(",", $status_array) . ") ";
}

// if($fs != "" && $fs != "0")
// {
//     if(strpos($fs,"1") > -1)
//         $sql = $sql . " and pm.`status` in (1, 2) ";
//     if(strpos($fs,"2") > -1)
//         $sql = $sql . " and pm.`status` in (3, 4) ";
//     if(strpos($fs,"3") > -1)
//         $sql = $sql . " and pm.`status` in (5) ";
//     if(strpos($fs,"4") > -1)
//         $sql = $sql . " and pm.`status` in (6, 7) ";
//     if(strpos($fs,"5") > -1)
//         $sql = $sql . " and pm.`status` in (8) ";
//     if(strpos($fs,"6") > -1)
//         $sql = $sql . " and pm.`status` in (9) ";
//     if(strpos($fs,"7") > -1)
//         $sql = $sql . " and pm.`status` in (0) ";
//     if(strpos($fs,"8") > -1)
//         $sql = $sql . " and pm.`status` in (-1) ";

//     if(strpos($fs,"1") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (1, 2) ";
//     if(strpos($fs,"2") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (3, 4) ";
//     if(strpos($fs,"3") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (5) ";
//     if(strpos($fs,"4") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (6, 7) ";
//     if(strpos($fs,"5") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (8) ";
//     if(strpos($fs,"6") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (9) ";
//     if(strpos($fs,"7") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (0) ";
//     if(strpos($fs,"8") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (-1) ";
// }

if($fat == "1" && $fau != "")
{
    $sql = $sql . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) <= " . $fau . " ";
    $query_cnt = $query_cnt . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) <= " . $fau . " ";
}

if($fat == "1" && $fal != "")
{
    $sql = $sql . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) >= " . $fal . " ";
    $query_cnt = $query_cnt . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) >= " . $fal . " ";
}

if($fat == "2" && $fau != "")
{
    $sql = $sql . " and pm.amount_verified <= " . $fau . " ";
    $query_cnt = $query_cnt . " and pm.amount_verified <= " . $fau . " ";
}

if($fat == "2" && $fal != "")
{
    $sql = $sql . " and pm.amount_verified >= " . $fal . " ";
    $query_cnt = $query_cnt . " and pm.amount_verified >= " . $fal . " ";
}

if($fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
}

if($fus != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.updated_at, '%Y/%m/%d') >= '" . $fus . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.updated_at, '%Y/%m/%d') >= '" . $fus . "' ";
}

if($fue != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.updated_at, '%Y/%m/%d') <= '" . $fue . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.updated_at, '%Y/%m/%d') <= '" . $fue . "' ";
}


if($ftd == "2" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "2" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "3" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "3" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "4" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "4" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "5" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "5" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "6" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "6" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "7" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "7" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "8" && $fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($ftd == "8" && $fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
}


$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {
        case 1:
            if($ofd1 == 2)
                $sOrder = "pm.request_no desc";
            else
                $sOrder = "pm.request_no ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 7:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder != "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder .= ", pm.request_no desc";
            else
                $sOrder .= ", pm.request_no ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.updated_at, '0000-00-00') ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 7:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder == "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder = "pm.request_no desc";
            else
                $sOrder = "pm.request_no ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 7:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from inventory_modify_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        default:
    }
}


if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " order by pm.request_no desc ";


if (!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if (false === $page) {
        $page = 1;
    }
}


if (!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if (false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $sql = $sql . " LIMIT " . $offset . "," . $size;
}


$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();

while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $request_no = "";
        $check_name = "";
        $date_requested = "";

        $status = "";
        $requestor = "";
        $created_at = "";
        $updated_at = "";

        $checker = "";
        $approver = "";

        $reason = "";
        $note_1 = "";
        $listing = "[]";
        
        $which_pool = "";
        $as_sample = "";
        $location = "";
        $project_id = "";
        $attachment = [];

        $receive_id = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $request_no = $row['request_no'];
            $check_name = $row['check_name'];
            $status = $row['status'];
            $date_requested = $row['date_requested'];
            $requestor = $row['username'];
            $created_at = $row['created_at'];
            $updated_at = $row['updated_at'];

            $create_by = $row['created_by'];
            $updated_by = $row['updated_by'];

            $create_id = $row['create_id'];
            $checker_id = $row['checker_id'];
            $approver_id = $row['approver_id'];

            $checker = $row['checker'];
            $approver = $row['approver'];
            
            $reason = $row['reason'];
            $note_1 = $row['note_1'];
            $listing = $row['listing'];
            $which_pool = $row['which_pool'];
            $as_sample = $row['as_sample'];
            $location = $row['location'];
            $project_id = $row['project_id'];

            $receive_id = $row['receive_id'];

            if($listing == null || $listing == "")
            {
                $listing = "[]";
            }

            $attachment = GetAttachment($id, $db);

            if($status > 1)
            {
                $history_item = getHistoryRecord($listing, $db, $id);

                if($history_item != null)
                {
                    //$obj1 = json_decode($history_item["listing"]);
                    $obj2 = json_decode($listing);

                    foreach($history_item as $key => $item)
                    {
                        $obj1 = json_decode($history_item[$key]["listing"]);
                        $diff_items = [];

                        // find in obj2 where id = $key
                        $obj_now = null;
                        for($i = 0; $i < count($obj2); $i++)
                        {
                            if($obj2[$i]->id == $key)
                            {
                                $diff_items = show_diff($obj1, $obj2[$i]);

                                if($diff_items != null)
                                {
                                    // add new value to the object
                                    for($j = 0; $j < count($diff_items); $j++)
                                    {
                                        $key = $diff_items[$j]['key'];
                                        $value = $diff_items[$j]['value'];

                                        foreach($obj2[$i] as $key2 => $value2)
                                        {
                                            if($key == $key2)
                                            {
                                                $key3 = $key2 . "_new";
                                                if($key2 == "project_id" || $key2 == "project_name" || $key2 == "updated_at" || $key2 == "updated_by")
                                                {
                                                    $obj2[$i]->$key3 = $obj2[$i]->$key2;
                                                }
                                                else
                                                    $obj2[$i]->$key3 = " => " . $obj2[$i]->$key2;
                                                $obj2[$i]->$key2 = $value;
                                            }
                                        }
                                    }

                                }
                                break;
                            }
                        }
                    }
                    
                    $listing = json_encode($obj2);
                }
            }

            // $update_product_list = json_decode($listing, true);
            
            // // update listing product infomation
            // for($i = 0; $i < count($update_product_list); $i++)
            // {
            //     $product_id = $update_product_list[$i]['product_id'];

            //     $listing = $update_product_list[$i]['listing'];
            //     $v1 = $update_product_list[$i]['v1'];
            //     $v2 = $update_product_list[$i]['v2'];
            //     $v3 = $update_product_list[$i]['v3'];
            //     $v4 = $update_product_list[$i]['v4'];

            //     if($product_id != 0 && $listing == "")
            //     {
            //         // if in cache
            //         if(isset($_SESSION['product_cache'][$product_id]))
            //         {
            //             $update_product_list[$i]['product'] = $_SESSION['product_cache'][$product_id];
            //         }
            //         else
            //         {
            //             $productAttributeList = GetProductAttributeList($product_id, $v1, $v2, $v3, $v4, $db);
            //             if($productAttributeList != null)
            //             {
            //                 $update_product_list[$i]['product'] = $productAttributeList;
            //                 $_SESSION['product_cache'][$product_id] = $productAttributeList; // cache it
            //             }
            //         }
            //     }
            // }
            
            // $listing = json_encode($update_product_list);

            $merged_results[] = array(
                "is_edited" => 1,
                "followup" => "",
                "id" => $id,
                "request_no" => $request_no,
                "check_name" => $check_name,
                "create_id" => $create_id,
                "checker_id" => $checker_id,
                "approver_id" => $approver_id,
                "date_requested" => $date_requested,
                "attachment" => $attachment,
                "status" => $status,
                "requestor" => $requestor,
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                "create_by" => $create_by,
                "updated_by" => $updated_by,
                "checker" => $checker,
                "approver" => $approver,
                "reason" => $reason,
                "note_1" => $note_1,
                "listing" => json_decode($listing),
                "which_pool" => $which_pool,
                "as_sample" => $as_sample,
                "location" => $location,
                "project_id" => $project_id,

                "receive_id" => $receive_id,
            
                "cnt" => $cnt,
            );

        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

function GetAttachment($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'inventory_modify'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function UpdateQty($list, $db)
{
    foreach($list as &$item)
    {
        $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

        $sql = "select qty, reserve_qty from office_items_stock where code = '" . $code . "'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qty = $row['qty'];
            $reserve_qty = $row['reserve_qty'];

            $item['qty'] = $qty;
            $item['reserve_qty'] = $reserve_qty;
        }
    }

    return $list;
}

function GetReleaseAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'inventory_modify_release'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case 1:
            $location = "PHASE 1: User Chooses Reason and Creates List of Affected Item(s)";
            break;
        case 2:
            $location = "PHASE 2: Inventory Modification Completed";
            break;
    }

    return $location;
}

function GetHistory($_id, $db)
{
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from inventory_modify_apply_history pm 
            where `status` <> -1 and request_id = " . $_id . " order by created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetHistoryDesc($_id, $db)
{
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from inventory_modify_apply_history pm 
            where `status` <> -1 and request_id = " . $_id . " order by created_at desc ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetReleaseHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Releaser Released' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetApprove1History($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') dt, `status` from petty_history pm 
            where  `status` <> -1 and petty_id = " . $_id . " and `action` = 'OP Approved' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['status'] == -1)
            $merged_results = "";
        else
            $merged_results = $row['dt'];
    }

    return $merged_results;
}

function GetApprove2History($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'MD Approved' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetCheckedHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Checker Checked' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetLiquidateHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Liquidated' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetVerifiedHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Verifier Verified' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetAmountPettyLiquidate($_id, $db)
{
    $sql = "select pm.id, sn, vendor payee, particulars, price, qty, `status`
    from apply_for_petty_liquidate pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetDepartment($dept_name)
{
    $department = "";

    if($dept_name == 'admin')
        $department = 'Admin Department';

    if($dept_name == 'design')
        $department = 'Design Department';

    if($dept_name == 'engineering')
        $department = 'Engineering Department';

    if($dept_name == 'lighting')
        $department = 'Lighting Department';
    
    if($dept_name == 'office')
        $department = 'Office Department';
    
    if($dept_name == 'sales')
        $department = 'Sales Department';

    return $department;
}

// get the previous recode
function getHistoryRecord($listing, $db, $request_id)
{
    $items = json_decode($listing, true);
    $item_str = "";

    $items_with_largest_version = array();

    foreach ($items as $item) {
        $item_id = $item['id'];
        if ($item_str == "") {
            $item_str = $item_id;
        } else {
            $item_str .= "," . $item_id;
        }
    }

    if ($item_str == "") {
        return $items_with_largest_version;
    }

    $query = "SELECT * FROM inventory_modify_history WHERE item_id in (" . $item_str . ") and request_id = " . $request_id . " order by item_id, version desc";
    $stmt = $db->prepare($query);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // if no record found, insert one and return it

    // if no record found, insert one and return false
    

    if ($row) {
        // get each item id with largest version
        while ($row) {
            $item_id = $row['item_id'];
            if (!isset($items_with_largest_version[$item_id]) || $row['version'] > $items_with_largest_version[$item_id]['version']) {
                $items_with_largest_version[$item_id] = array(
                    'id' => $item_id,
                    'version' => $row['version'],
                    'listing' => json_encode($row['listing'], true),
                );
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

    }

    return $items_with_largest_version;
}

function show_diff($pre_item, $item)
{
    $diff = [];

    $pre_item = json_decode($pre_item);

    foreach($item as $key => $value)
    {
        if($key == "id" || $key == "created_at" || $key == "request_id")
            continue;

        foreach($pre_item as $key2 => $value2)
        {
            if($key == $key2)
            {
                if($value == $value2)
                    continue;
                else
                    $diff[] = [
                        'key' => $key,
                        'value' => $value2
                    ];
            }

        }


    }

    return $diff;
}

function getProductAttributeList($product_id, $v1, $v2, $v3, $v4, $db)
{
    // product main
    $sql = "SELECT p.*, cu.username created_name, uu.username updated_name FROM product_category p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  p.STATUS <> -1";
    
    if($product_id != "")
    {
        $sql = $sql . " and p.id = " . $product_id . " ";
    }
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $variation1 = "";
            $variation1_custom = "";
            $variation2 = "";
            $variation2_custom = "";
            $variation3 = "";
            $variation3_custom = "";
            $variation4 = "";
            $variation4_custom = "";
            $cat = "";
            $related_product = [];
            $attribute_list = [];
            $product_id = 0;
            $id = 0;
            $code = "";
            $photo1 = "";
            $photo2 = "";
            $photo3 = "";
            $photo4 = "";
            $photo5 = "";
            $photo6 = "";
            $description = "";
            $reserved = [];
            $indoor = "";
            $type = "";
            $grade = "";
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $id = 0;
                $product_id = $product_id;
                $p_id = $product_id;
                $code = $row['code'];
                $photo1 = $row['photo1'];
                $photo2 = $row['photo2'];
                if($row['description'] != '' && $row['notes'] != '')
                    $description = "Description:" . PHP_EOL . $row['description'] . PHP_EOL . PHP_EOL . "Notes:" . PHP_EOL .$row['notes'];
                else if($row['description'] != '')
                    $description = "Description:" . PHP_EOL . $row['description'];
                else if($row['notes'] != '')
                    $description = "Notes:" . PHP_EOL . $row['notes'];
                else
                    $description = '';
                
                $accessory_mode = '';
                $attributes = '';
                $variation_mode = '';
                $variation = '';
                $status = '';
                $create_id = '';
                $created_at = '';
                $product = [];
                $accessory = [];
                
                $variation1_text = "1st Variation";
                $variation2_text = "2nd Variation";
                $variation3_text = "3rd Variation";
                $variation4_text = "4th Variation";
                
                $special_infomation = [];
                $accessory_information = [];
                $related_product = [];
                
                $sub_cateory_item = [];
                $cat = $row['category'];
                
                //$category = GetCategory($row['category'], $db);
                $sub_category = $row['sub_category'];
                $tags = $row['tags'];
                //$sub_category_name = GetCategory($row['sub_category'], $db);
                
                $brand = $row['brand'];
                
                $price_ntd = $row['price_ntd'];
                $price_org = $row['price'];
                $price_ntd_org = $row['price_ntd'];
                $price = $row['price'];

                $out = $row['out'];
                $notes = $row['notes'];
                
                $photo3 = $row['photo3'];
                $accessory_mode = $row['accessory_mode'];
                $attributes = $row['attributes'];
                $variation_mode = $row['variation_mode'];
                $variation = $row['variation'];
                $status = $row['status'];
                $create_id = $row['create_id'];
                $created_at = $row['created_at'];
                $updated_id = $row['updated_id'];
                $updated_at = $row['updated_at'];
                
                $created_name = $row['created_name'];
                $updated_name = $row['updated_name'];
                $currency = $row['currency'];
                
                // if($d != '')
                //     $product = GetProductWithId($sd, $d, $db);
                // else
                //     $product = GetProduct($sd, $db);
                
                // $related_product = GetRelatedProductCode($sd, $db);

                $product = GetProduct($id, $db, $currency);

                for($i = 0; $i < count($product); $i++)
                {
                    if($product[$i]['enabled'] != 1)
                    {
                        $key_value_text = "";

                        //$phased_out_cnt++;
                        if($product[$i]['v1'] != "")
                            $key_value_text .= $product[$i]['k1'] . " = " . $product[$i]['v1'] . ", ";
                        if($product[$i]['v2'] != "")
                            $key_value_text .= $product[$i]['k2'] . " = " . $product[$i]['v2'] . ", ";
                        if($product[$i]['v3'] != "")
                            $key_value_text .= $product[$i]['k3'] . " = " . $product[$i]['v3'] . ", ";
                        if($product[$i]['v4'] != "")
                            $key_value_text .= $product[$i]['k4'] . " = " . $product[$i]['v4'] . ", ";

                        $key_value_text = substr($key_value_text, 0, -2);

                        array_push($phased_out_text, $key_value_text);
                    }
                        
                    if($product[$i]['last_order_name'] != '')
                    {
                        $order_info = getOrderInfo($product[$i]['last_order'], $db);
                        $url = "";

                        if(isset($order_info["order_type"]))
                        {
                            if($order_info["order_type"] == "taiwan")
                                $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $product[$i]['last_order'];
                            
                            if($order_info["order_type"] == "mockup")
                                $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $product[$i]['last_order'];
                            
                            if($order_info["order_type"] == "sample")
                                $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $product[$i]['last_order'];
                            
                            if($order_info["order_type"] == "stock")
                                $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $product[$i]['last_order'];
                        }

                        $params = str_replace("=>", " = ", $product[$i]['1st_variation']);
                        if($product[$i]['2rd_variation'] != "=>")
                            $params .= ", " . str_replace("=>", " = ", $product[$i]['2rd_variation']);
                        if($product[$i]['3th_variation'] != "=>")
                            $params .= ", " . str_replace("=>", " = ", $product[$i]['3th_variation']);
                        if($product[$i]['4th_variation'] != "=>")
                            $params .= ", " . str_replace("=>", " = ", $product[$i]['4th_variation']);

                        //$is_last_order_product .= "(" . $order_sn++ . ") " . $params . ": <br>" . substr($product[$i]['last_order_at'], 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $product[$i]['last_order_name'] . "</a><br><br>";
                    }
                }
                //$phased_out_cnt = $phased_out_cnt;

                //$related_product = GetRelatedProductCode($id, $db);
                //$replacement_product = GetRelacementProductCode($id, $db);
                
                $variation1_value = [];
                $variation2_value = [];
                $variation3_value = [];
                $variation4_value = [];
                
                if(count($product) > 0)
                {
                    $variation1_text = $product[0]['k1'];
                    $variation2_text = $product[0]['k2'];
                    $variation3_text = $product[0]['k3'];
                    $variation4_text = $product[0]['k4'];
                    
                    $variation1_value = [];
                    $variation2_value = [];
                    $variation3_value = [];
                    $variation4_value = [];
                    
                    for($i = 0; $i < count($product); $i++)
                    {
                        if (!in_array($product[$i]['v1'],$variation1_value))
                        {
                            array_push($variation1_value,$product[$i]['v1']);
                        }
                        if (!in_array($product[$i]['v2'],$variation2_value))
                        {
                            array_push($variation2_value,$product[$i]['v2']);
                        }
                        if (!in_array($product[$i]['v3'],$variation3_value))
                        {
                            array_push($variation3_value,$product[$i]['v3']);
                        }
                        if (!in_array($product[$i]['v4'],$variation4_value))
                        {
                            array_push($variation4_value,$product[$i]['v4']);
                        }
                    }
                }
                
                // $accessory = GetAccessory($sd, $db);
                // $sub_category_item = GetSubCategoryItem($category, $db);
                
                $special_info_json = json_decode($attributes);
                
                $special_information = GetSpecialInfomation($sub_category, $db, $special_info_json);
                //$accessory_information = GetAccessoryInfomation($sub_category, $db, $sd);
                
                $variation1 = 'custom';
                $variation1_custom = $variation1_text;
                $variation2 = 'custom';
                $variation2_custom = $variation2_text;
                $variation3 = 'custom';
                $variation3_custom = $variation3_text;
                $variation4 = 'custom';
                $variation4_custom = $variation4_text;
                
                for($i = 0; $i < count($special_information); $i++)
                {
                    if ($special_information[$i]['cat_id'] == $sub_category)
                    {
                        $lv3 = $special_information[$i]['lv3'][0];
                        for($j = 0; $j < count($lv3); $j++)
                        {
                            if($lv3[$j]['category'] == $variation1_text)
                            {
                                $variation1 = $variation1_text;
                                $variation1_custom = "";
                            }
                            
                            if($lv3[$j]['category'] == $variation2_text)
                            {
                                $variation2 = $variation2_text;
                                $variation2_custom = "";
                            }
                            
                            if($lv3[$j]['category'] == $variation3_text)
                            {
                                $variation3 = $variation3_text;
                                $variation3_custom = "";
                            }

                            if($lv3[$j]['category'] == $variation4_text)
                            {
                                $variation4 = $variation4_text;
                                $variation4_custom = "";
                            }
                        }
                    }
                    
                }
                
                
                if($variation1_text == "1st Variation")
                {
                    $variation1 = "";
                    $variation1_custom = "";
                }
                
                if($variation2_text == "2nd Variation")
                {
                    $variation2 = "";
                    $variation2_custom = "";
                }
                
                if($variation3_text == "3rd Variation")
                {
                    $variation3 = "";
                    $variation3_custom = "";
                }

                if($variation4_text == "4th Variation")
                {
                    $variation4 = "";
                    $variation4_custom = "";
                }
                
                if($variation1_text == "")
                {
                    $variation1 = "";
                    $variation1_custom = "";
                }
                
                if($variation2_text == "")
                {
                    $variation2 = "";
                    $variation2_custom = "";
                }
                
                if($variation3_text == "")
                {
                    $variation3 = "";
                    $variation3_custom = "";
                }

                if($variation4_text == "")
                {
                    $variation4 = "";
                    $variation4_custom = "";
                }
                
                $attribute_list = [];
                if($special_info_json != null)
                {
                    for($i=0; $i<count($special_info_json); $i++)
                    {
                        $value = [];
                        $_category = $special_info_json[$i]->category;
                        
                        if($special_info_json[$i]->value != "")
                        {
                            array_push($value, $special_info_json[$i]->value);
                            
                        }
                        
                        if($variation1_text == $special_info_json[$i]->category)
                        {
                            $value = $variation1_value;
                        }
                        if($variation2_text == $special_info_json[$i]->category)
                        {
                            $value = $variation2_value;
                        }
                        if($variation3_text == $special_info_json[$i]->category)
                        {
                            $value = $variation3_value;
                        }

                        if($variation4_text == $special_info_json[$i]->category)
                        {
                            $value = $variation4_value;
                        }
                        
                        if(count($value) > 0)
                        {
                            $attribute_list[] = array("category" => $special_info_json[$i]->category, "value" => $value,);
                        }
                    }
                }
            }
            
            if($variation1 == "custom" && $variation1_custom != "1st Variation")
            {
                $attribute_list[] = array("category" => $variation1_text, "value" => $variation1_value,);
            }
            
            if($variation2 == "custom" && $variation2_custom != "2nd Variation")
            {
                $attribute_list[] = array("category" => $variation2_text, "value" => $variation2_value,);
            }
            
            if($variation3 == "custom" && $variation3_custom != "3rd Variation")
            {
                $attribute_list[] = array("category" => $variation3_text,"value" => $variation3_value,);
            }

            if($variation4 == "custom" && $variation4_custom != "4th Variation")
            {
                $attribute_list[] = array("category" => $variation4_text,"value" => $variation4_value,);
            }

            //$cat_text = GetCategoryText($cat);
            $this_year = date("Y");
            
            //$reserved = array("Tel" => "(+63) 2 8525-6288", "Email" => "info@feliix.com", "Website" => "www.feliix.com", "Copyright" => $this_year, "Feliix" => $cat_text, "Note" => "Specification are subject to change at any time without notice");
            
            $legend = "";
            $option = "";
            $type = "";
            $grade = "";
            $indoor = "";

            $photo3 = "";
            $photo4 = "";
            $photo5 = "";
            $photo6 = "";

            // only tak 4 of related_product
            for($i=0; $i < count($related_product); $i++)
            {
                if($i > 3)
                    break;
                $product = $related_product[$i];
                
                if($i == 0)
                    $photo3 = $product['photo1'];
                if($i == 1)
                    $photo4 = $product['photo1'];
                if($i == 2)
                    $photo5 = $product['photo1'];
                if($i == 3)
                    $photo6 = $product['photo1'];
                

            }

            // $attribute_list_by_two = [];
            // $two_array = [];
            // for($i=0; $i<count($attribute_list); $i++)
            // {
            //     if($i % 2 == 0)
            //     {
            //         $two_array = [];
            //         $two_array[] = $attribute_list[$i];
            //     }
            //     else
            //     {
            //         $two_array[] = $attribute_list[$i];
            //         $attribute_list_by_two[] = $two_array;
            //     }
            // }
            // if(count($two_array) == 1)
            // {
            //     $attribute_list_by_two[] = $two_array;
            // }
            
            $merged_results = array( 
            "id" => $id,
            "legend" => $legend,
            "option" => $option,
            "product_id" => $product_id,
            "p_id" => $p_id,
            "code" => $code,
            "photo1" => ($photo1 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo1: '',
            "photo2" => ($photo2 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo2: '',
            "photo3" => ($photo3 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo3: '',
            "photo4" => ($photo4 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo4: '',
            "photo5" => ($photo5 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo5: '',
            "photo6" => ($photo6 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo6: '',
            "description" => $description,
            "variation" => GetVariantAsText($attribute_list),
            "attribute_list" => $attribute_list,
            "reserved" => $reserved,

            "related_product" => $related_product,

            "indoor" => $indoor,
            "type" => $type,
            "grade" => $grade
        );

        return $merged_results;
}


function GetSpecialInfomation($cat_id, $db, $special_info_json){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 2 AND left(cat_id, 1) = '". substr($cat_id, 0, 1) . "' and STATUS <> -1";
    
    $sql = $sql . " ORDER BY cat_id ";
    
    $merged_results = array();
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    $cat_id = "";
    $category = "";
    
    $lv3 = [];
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        if($cat_id != $row['cat_id'] && $cat_id != "")
        {
            $merged_results[] = array( "cat_id" => $cat_id,
            "category" => $category,
            "lv3" => $lv3,
        );
        
        $lv3 = [];
        
    }
    
    $cat_id = $row['cat_id'];
    $category = $row['category'];
    
    $lv3[] = GetLevel3_value($cat_id, $db, $special_info_json);
}

if($cat_id != "")
{
    $merged_results[] = array( "cat_id" => $cat_id,
    "category" => $category,
    "lv3" => $lv3,
);
}

return $merged_results;

}


function GetVariantAsText($variants)
{
    $strVariant = "";

    if(count($variants) == 0)
    return $strVariant;

    // category as key : values as value
    foreach($variants as $variant)
    {
        $str_value = "";
        foreach($variant['value'] as $value)
        {
            $str_value .= $value . ",";
        }
        // remove last comma
        $str_value = substr($str_value, 0, -1);
        $strVariant .= $variant['category'] . ":" . $str_value . PHP_EOL;
    }

    // remove last /r/n ifhas newline
    if(substr($strVariant, -2) == PHP_EOL)
        $strVariant = substr($strVariant, 0, -2);

    return $strVariant;
}


function GetLevel3_value($cat_id, $db, $special_info_json){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 3 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";
    
    $sql = $sql . " ORDER BY cat_id ";
    
    $merged_results = array();
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    $cat_id = "";
    $category = "";
    
    $lv2 = [];
    
    $value = '';
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        if($cat_id != $row['cat_id'] && $cat_id != "")
        {
            $merged_results[] = array( "cat_id" => $cat_id,
            "category" => $category,
            "detail" => $lv2,
            "value" => $value,
        );
        
        $lv2 = [];
        
    }
    
    $cat_id = $row['cat_id'];
    $category = $row['category'];
    
    $value = '';
    if($special_info_json != null)
    {
        for($i=0; $i<count($special_info_json); $i++)
        {
            if($special_info_json[$i]->cat_id == $cat_id)
            {
                $value = $special_info_json[$i]->value;
                break;
            }
        }
    }
    
    $lv2[] = GetDetail($cat_id, $db);
}

if($cat_id != "")
{
    $merged_results[] = array( "cat_id" => $cat_id,
    "category" => $category,
    "detail" => $lv2,
    "value" => $value,
);
}

return $merged_results;

}


function GetLevel3($cat_id, $db){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 3 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";
    
    $sql = $sql . " ORDER BY cat_id ";
    
    $merged_results = array();
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    $cat_id = "";
    $category = "";
    
    $lv2 = [];
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        if($cat_id != $row['cat_id'] && $cat_id != "")
        {
            $merged_results[] = array( "cat_id" => $cat_id,
            "category" => $category,
            "detail" => $lv2,
        );
        
        $lv2 = [];
        
    }
    
    $cat_id = $row['cat_id'];
    $category = $row['category'];
    
    $lv2[] = GetDetail($cat_id, $db);
}

if($cat_id != "")
{
    $merged_results[] = array( "cat_id" => $cat_id,
    "category" => $category,
    "detail" => $lv2,
);
}

return $merged_results;

}

function GetDetail($cat_id, $db){
    $sql = "SELECT cat_id, sn, `option` FROM product_category_attribute_detail WHERE cat_id = '". $cat_id . "' and STATUS <> -1";
    
    $sql = $sql . " ORDER BY sn ";
    
    $merged_results = array();
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }
    
    return $merged_results;
    
}


function GetProduct($id, $db, $currency){
    $sql = "SELECT *, CONCAT('https://storage.googleapis.com/feliiximg/' , photo) url FROM product WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $k1 = GetKey($row['1st_variation']);
        $k2 = GetKey($row['2rd_variation']);
        $k3 = GetKey($row['3th_variation']);
        $k4 = GetKey($row['4th_variation']);

        $v1 = GetValue($row['1st_variation']);
        $v2 = GetValue($row['2rd_variation']);
        $v3 = GetValue($row['3th_variation']);
        $v4 = GetValue($row['4th_variation']);

        $fir = $row['1st_variation'];
        $sec = $row['2rd_variation'];
        $thi = $row['3th_variation'];
        $fou = $row['4th_variation'];

        $checked = '';
        $code = $row['code'];
        $price = $row['price'];
        
        $price_ntd = $row['price_ntd'];
        $price_org = $row['price'];
        $price_ntd_org = $row['price_ntd'];
        $price_change = $row['price_change'];
        $price_ntd_change = $row['price_ntd_change'];

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'];

        $last_order = $row['last_order'];
        $last_order_name = $row['last_order_name'];
        $last_order_at = $row['last_order_at'];

        $status = $row['enabled'];
        $photo = trim($row['photo']);
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

            $enabled = $row['enabled'];

            if($last_order != "")
            {
                $order_info = getOrderInfo($last_order, $db);
                $last_order_url = "";
                if(isset($order_info["order_type"]))
                {
                    if($order_info["order_type"] == "taiwan")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $last_order;
                    
                    if($order_info["order_type"] == "mockup")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $last_order;
                    
                    if($order_info["order_type"] == "sample")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $last_order;
                    
                    if($order_info["order_type"] == "stock")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $last_order;
                }
            }
            else
            {
                $last_order_url = "";
            }

        $merged_results[] = array(  "id" => $id, 
                                    "k1" => $k1, 
                                    "k2" => $k2, 
                                    "k3" => $k3, 
                                    "k4" => $k4,
                                    "v1" => $v1, 
                                    "v2" => $v2, 
                                    "v3" => $v3, 
                                    "v4" => $v4,
                                    "1st_variation" => $fir,
                                    "2rd_variation" => $sec,
                                    "3th_variation" => $thi,
                                    "4th_variation" => $fou,
                                    "checked" => $checked, 
                                    "code" => $code, 
                                    "price" => $price, 
                                    "price_ntd" => $price_ntd, 
                                    "price_org" => $price_org, 
                                    "price_ntd_org" => $price_ntd_org, 
                                    "price_change" => substr($price_change, 0, 10), 
                                    "price_ntd_change" => substr($price_ntd_change, 0, 10), 
                                    "quoted_price" => $quoted_price, 
                                    "quoted_price_org" => $quoted_price, 
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10), 
                                    "status" => $status, 
                                    "url" => $url, 
                                    "photo" => $photo, 
                                    "currency" => $currency,
                                    "enabled" => $enabled,
                                   
                                    "file" => array( "value" => ''),
                                   "last_order" => $last_order,
                                    "last_order_name" => $last_order_name,
                                    "last_order_at" => substr($last_order_at,0, 10),
                                    "last_order_url" => $last_order_url,
                                    "last_have_spec" => true,

            );
    }
    
    return $merged_results;
}