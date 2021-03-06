<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
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

$uid = (isset($_GET['uid']) ?  $_GET['uid'] : '');
$sdate1 = (isset($_GET['sdate1']) ?  $_GET['sdate1'] : '');
$edate1 = (isset($_GET['edate1']) ?  $_GET['edate1'] : '');

$sdate2 = (isset($_GET['sdate2']) ?  $_GET['sdate2'] : '');
$edate2 = (isset($_GET['edate2']) ?  $_GET['edate2'] : '');

$merged_results = array();

if($sdate1 == '' && $sdate2 == '')
{
    $merged_results[] = array( 
        "al_credit" => 0,
        "al_taken" => 0,
        "al_approval" => 0,
        
        "sl_credit" => 0,
        "sl_taken" => 0,
        "sl_approval" => 0,

        "pl_taken" => 0,
        "pl_approval" => 0,

        "ab_taken" => 0,
        "ab_approval" => 0,
    );

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES); 
    die();
}

// leave credit!

$al_credit = 0;
$sl_credit = 0;

$manager_leave = 0;
$is_manager = 0;

$query = "SELECT annual_leave, sick_leave, manager_leave, is_manager from user where id = " . $user_id ;

$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $al_credit = $row['annual_leave'];
    $sl_credit = $row['sick_leave'];

    $manager_leave = $row['manager_leave'];
    $is_manager = $row['is_manager'];
}


/* fetch data */
if($edate2 != "")
    $query = "SELECT SUM(`leave`) le, leave_type, CASE  WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END approval FROM apply_for_leave WHERE start_date >= '" . $sdate1 . "' AND start_date <= '" . $edate2 . "' and status in (0, 1) and uid = " . $user_id . " group by leave_type,  CASE WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END";
else
    $query = "SELECT SUM(`leave`) le, leave_type, CASE  WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END approval FROM apply_for_leave WHERE and start_date >= '" . $sdate1 . "' AND start_date <= '" . $edate1 . "' and status in (0, 1) and uid = " . $user_id . " group by leave_type,  CASE WHEN re_approval_id > 0 THEN 'A'  WHEN re_approval_id = 0 THEN 'P' END";

$stmt = $db->prepare( $query );
$stmt->execute();



$al_taken = 0;
$al_approval = 0;

$sl_taken = 0;
$sl_approval = 0;

$pl_taken = 0;
$pl_approval = 0;

$ab_taken = 0;
$ab_approval = 0;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $le = $row['le'];
    $leave_type = $row['leave_type'];
    $approval = $row['approval'];

    switch ($leave_type) {
        case "A":
            if($approval == 'A')
                $al_taken += $le;
            else
                $al_approval += $le;
            break;
        case "B":
            if($approval == 'A')
                $sl_taken += $le;
            else
                $sl_approval += $le;
            break;
        case "C":
            if($approval == 'A')
                $pl_taken += $le;
            else
                $pl_approval += $le;
            break;
        case "D":
            if($approval == 'A')
                $ab_taken += $le;
            else
                $ab_approval += $le;
            break;
    }
}

$merged_results[] = array(
    "al_credit" => $al_credit,
    "al_taken" => $al_taken,
    "al_approval" => $al_approval,

    "sl_credit" => $sl_credit,
    "sl_taken" => $sl_taken,
    "sl_approval" => $sl_approval,

    "pl_taken" => $pl_taken,
    "pl_approval" => $pl_approval,

    "ab_taken" => $ab_taken,
    "ab_approval" => $ab_approval,

    "manager_leave" => $manager_leave,
);

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
