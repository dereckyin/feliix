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

    $username = $decoded->data->username;
    $position = $decoded->data->position;
    $department = $decoded->data->department;

    $merged_results = array();
    $return_result = array();

    $access6 = false;

    $query = "SELECT pr.id, 
                pr.review_month, 
                pr.period, 
                pr.template_id,
                ud.department,  
                ut.title, 
                pt.version,
                pr.create_id,
                pr.user_id,
                pr.direct_access,
                pr.manager_access,
                pr.peer_access,
                pr.other_access,
                pr.outsider_name1,
                pr.outsider_email1,
                pr.outsider_name2,
                pr.outsider_email2,
                u.username manager,
                u1.username employee, 
                pr.status,
                COALESCE(pr.user_complete_at, '') user_complete_at, 
                COALESCE(pr.manager_complete_at, '') manager_complete_at,
                pr.created_at
                FROM leadership_assessment pr
                LEFT JOIN user u ON u.id = pr.create_id
                LEFT JOIN user u1 ON u1.id = pr.user_id
                LEFT JOIN leadership_template pt ON pr.template_id = pt.id
                LEFT JOIN user_title ut ON ut.id = u1.title_id
                LEFT JOIN user_department ud ON ud.id = u1.apartment_id
              WHERE pr.status <> -1  " . ($id != 0 ? " and pr.id=$id" : ' ');

    // if($sdate != '')
    // {
    //     $query .= " and pr.review_month >= '" . $sdate . "' ";
    // }

    // if($edate != '')
    // {
    //     $query .= " and case 
    //                         when period = 3 then DATE_FORMAT(STR_TO_DATE(CONCAT(review_month, '-01'), '%Y-%m-%d') + INTERVAL 2 MONTH,'%Y-%m')
    //                         when period = 0 then DATE_FORMAT(STR_TO_DATE(CONCAT(review_month, '-01'), '%Y-%m-%d') + INTERVAL 1 MONTH,'%Y-%m')
    //                         when period = 1 then review_month
    //                     end  <= '" . $edate . "' ";
    // }

    // if($access6 != true)
    // {
    //     $query .= " and (pr.create_id = " . $user_id . " or pr.user_id = " . $user_id . ") ";
    // }

    $query = $query . " order by pr.created_at desc ";

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

        $query = $query . " LIMIT " . $offset . "," . $size;
    }

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $review_month = $row['review_month'];
        $period = $row['period'];
        if($period == 3)
            $review_next_month = GetNextMonth3($review_month);
        else
            $review_next_month = GetNextMonth($review_month);

        $department = $row['department'];
        $title = $row['title'];
        $template_id = $row['template_id'];
        $employee = $row['employee'];
        $manager = $row['manager'];
        $version = $row['version'];
        $user_complete_at = $row['user_complete_at'];
        $manager_complete_at = $row['manager_complete_at'];

        $create_id = $row['create_id'];
        $user_id = $row['user_id'];

        $manager_access = $row['manager_access'];
        $peer_access = $row['peer_access'];
        $direct_access = $row['direct_access'];
        $other_access = $row['other_access'];

        $outsider_name1 = $row['outsider_name1'];
        $outsider_email1 = $row['outsider_email1'];
        $outsider_name2 = $row['outsider_name2'];
        $outsider_email2 = $row['outsider_email2'];

        $created_at = $row['created_at'];

        if($row['status'] == 0)
            $status = "Choose respondent for leadership assessment";
        if($row['status'] == 1)
            $status = "Assessed employee and respondents fill out survey";
        if($row['status'] == 2)
            $status = "Done";

    
        $merged_results[] = array(
            "id" => $id,
            "period" => $period,
            "review_month" => $review_month,
            "review_next_month" => $review_next_month,
            "department" => $department,
            "template_id" => $template_id,
            "title" => $title,
            "version" => $version,
            "employee" => $employee,
            "manager" => $manager,
            "create_id" => $create_id,
            "user_id" => $user_id,
            "direct_access" => $direct_access,
            "manager_access" => $manager_access,
            "peer_access" => $peer_access,
            "other_access" => $other_access,
            "outsider_name1" => $outsider_name1,
            "outsider_email1" => $outsider_email1,
            "outsider_name2" => $outsider_name2,
            "outsider_email2" => $outsider_email2,
            "user_complete_at" => $user_complete_at,
            "manager_complete_at" => $manager_complete_at,
            "status" => $status,

            "created_at" => $created_at,
        );
    }

    if ($kw != "") {
        foreach ($merged_results as &$value) {
            if (
                preg_match("/{$kw}/i", $value['employee']) ||
                preg_match("/{$kw}/i", $value['title']) ||
                preg_match("/{$kw}/i", $value['department']) ||
                preg_match("/{$kw}/i", $value['status']) 
            ) {
                $return_result[] = $value;
            }
        }
    } else
        $return_result = $merged_results;


    echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
}

function GetNextMonth($d)
{
    $date = date('Y-m', strtotime('+1 month', strtotime($d . '-01')));
    return $date;
}

function GetNextMonth3($d)
{
    $date = date('Y-m', strtotime('+2 month', strtotime($d . '-01')));
    return $date;
}

function GetAgenda($tid, $type, $db){
    $query = "
        SELECT pm.id,
            pm.`order`,
            pm.category,
            pm.criterion
          
        FROM   performance_template_detail pm
           
        WHERE  template_id = " . $tid . "
            AND pm.`type` = " . $type . "
            AND pm.`status` <> -1 
        ORDER BY `order`
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $order = $row['order'];
        $category = $row['category'];
        $criterion = $row['criterion'];

        $merged_results[] = array(
            "id" => $id,
            "order" => $order,
            "category" => $category,
            "criterion" => $criterion,
          
        );
    }

    return $merged_results;
}