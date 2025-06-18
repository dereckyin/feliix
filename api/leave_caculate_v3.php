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
use Illuminate\Support\Facades\Date;

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
    catch (Exception $e){
        http_response_code(401);
        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$is_manager = (isset($_POST['is_manager']) ?  $_POST['is_manager'] : '');

// 修改為接收小時相關的參數
$timeStart = (isset($_POST['timeStart']) ?  $_POST['timeStart'] : '');
$timeEnd = (isset($_POST['timeEnd']) ?  $_POST['timeEnd'] : '');
$startHour = (isset($_POST['startHour']) ?  $_POST['startHour'] : '');
$endHour = (isset($_POST['endHour']) ?  $_POST['endHour'] : '');

$leave_type = (isset($_POST['leave_type']) ?  $_POST['leave_type'] : '');

$leaves = array();
$applied = array();
$holiday = array();

// 驗證輸入
if($timeStart == '' || $timeEnd == '' || $startHour == '' || $endHour == '')
{
    http_response_code(401);
    echo json_encode(array("message" => "Apply Date and time not valid."));
    die();
}

// 驗證時間順序
$startDateTime = new DateTime($timeStart . ' ' . $startHour . ':00');
$endDateTime = new DateTime($timeEnd . ' ' . $endHour . ':00');

if($startDateTime > $endDateTime)
{
    http_response_code(401);
    echo json_encode(array("message" => "Apply Date and time not valid."));
    die();
}

// 修改假期額度變數為天數（原本的半天額度轉換為天數）
$query = "SELECT leave_level, sil, vl_sl, vl, sl, halfday, head_of_department from user where id = " . $user_id;
$stmt = $db->prepare($query);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // 將半天額度轉換為天數（假設半天=0.5天）
    $sil_credit = $row['sil'] * 0.5;
    $vl_sl_credit = $row['vl_sl'] * 0.5;
    $vl_credit = $row['vl'] * 0.5;
    $sl_credit = $row['sl'] * 0.5;
    $halfday_credit = $row['halfday'] * 0.5;
    $leave_level = $row['leave_level'];
    $head_of_department = $row['head_of_department'];
}

$startYear = substr($timeStart, 0, 4);
$endYear = substr($timeEnd, 0, 4);

// 檢查跨年度請假
if(($startYear != $endYear) && ($leave_level == "A"))
{
    http_response_code(401);
    echo json_encode(array("message" => "Leave across years should be divided into 2 leave applications, leave this year and leave next year."));
    die();
}

// 計算請假時數並轉換為天數
$interval = new DateInterval('PT1H'); // 1小時的間隔
$period = new DatePeriod($startDateTime, $interval, $endDateTime);

$totalHours = 0;
foreach ($period as $dt) {
    $dateStr = $dt->format("Ymd");
    $hourStr = $dt->format("H");
    array_push($leaves, $dateStr . " " . $hourStr);
    $totalHours++;
}

// 將小時轉換為天數（8小時=1天）
$totalDays = $totalHours / 8;

// 檢查重複申請
if($leave_level == "B" || $leave_level == "C")
{
    $headPeriodStart = date("Y-m-d",strtotime("last year Dec 1st"));
    $headPeriodEnd = date("Y-m-d",strtotime("this year Nov 30"));
    $tailPeriodStart = date("Y-m-d",strtotime("this year Dec 1st"));
    $tailPeriodEnd = date("Y-m-d",strtotime("next year Nov 30"));

    if($timeStart > $headPeriodEnd)
        $query = "SELECT apply_date, apply_start_hour, apply_end_hour, a.leave_type 
                 from `leave` l 
                 LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id 
                 where a.uid = " . $user_id . " 
                 and a.status in (0, 1) 
                 and apply_date >= '" . str_replace('-', '', $tailPeriodStart) . "' 
                 and apply_date <= '" . str_replace('-', '', $tailPeriodEnd) . "'";
    else
        $query = "SELECT apply_date, apply_start_hour, apply_end_hour, a.leave_type 
                 from `leave` l 
                 LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id 
                 where a.uid = " . $user_id . " 
                 and a.status in (0, 1) 
                 and apply_date >= '" . str_replace('-', '', $headPeriodStart) . "' 
                 and apply_date <= '" . str_replace('-', '', $headPeriodEnd) . "'";
}
else
    $query = "SELECT apply_date, apply_start_hour, apply_end_hour, a.leave_type 
             from `leave` l 
             LEFT JOIN `apply_for_leave` a ON l.apply_id = a.id 
             where a.uid = " . $user_id . " 
             and a.status in (0, 1) 
             and SUBSTRING(apply_date, 1, 4) = '" . $startYear . "'";

$stmt = $db->prepare($query);
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $apply_date = $row['apply_date'];
    $start_hour = $row['apply_start_hour'];
    $end_hour = $row['apply_end_hour'];
    
    // 將已申請的時間加入陣列
    for($h = $start_hour; $h < $end_hour; $h++) {
        array_push($applied, $apply_date . " " . str_pad($h, 2, "0", STR_PAD_LEFT));
    }

    // 更新假期額度
    if($row['leave_type'] == 'N')
    {
        if($sil_credit > 0)
            $sil_credit -= ($end_hour - $start_hour) / 8;
        else if($vl_credit > 0)
            $vl_credit -= ($end_hour - $start_hour) / 8;
        else if($vl_sl_credit > 0)
            $vl_sl_credit -= ($end_hour - $start_hour) / 8;
    }
    
    if($row['leave_type'] == 'S')
    {
        if($sil_credit > 0)
            $sil_credit -= ($end_hour - $start_hour) / 8;
        else if($sl_credit > 0)
            $sl_credit -= ($end_hour - $start_hour) / 8;
        else if($vl_sl_credit > 0)
            $vl_sl_credit -= ($end_hour - $start_hour) / 8;
    }

    if($row['leave_type'] == 'H')
    {
        $halfday_credit -= ($end_hour - $start_hour) / 8;
    }
}

// 檢查重疊
$inter = array_intersect($leaves, $applied);
if(count($inter) > 0)
{
    http_response_code(401);
    echo json_encode(array("message" => "Duplicate apply."));
    die();
}

// 排除假日
$query = "SELECT from_date FROM holiday where location = 'Philippines'";
$stmt = $db->prepare($query);
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $from_date = $row['from_date'];
    // 將假日整天加入陣列
    for($h = 0; $h < 24; $h++) {
        array_push($holiday, $from_date . " " . str_pad($h, 2, "0", STR_PAD_LEFT));
    }
}

// 排除假日時間
$result = array_diff($leaves, $holiday);

// 計算消耗的假期額度
$sil_consume = 0;
$vl_consume = 0;
$sl_consume = 0;
$vl_sl_consume = 0;
$halfday_consume = 0;

$totalHours = count($result);

if($leave_type == 'N')
{
    if($sil_credit >= $totalDays)
    {
        $sil_consume = $totalDays;
        $sil_credit -= $totalDays;
    }
    else if($vl_credit >= $totalDays)
    {
        $vl_consume = $totalDays;
        $vl_credit -= $totalDays;
    }
    else
    {
        $vl_sl_consume = $totalDays;
        $vl_sl_credit -= $totalDays;
    }
}
else if($leave_type == 'S')
{
    if($sil_credit >= $totalDays)
    {
        $sil_consume = $totalDays;
        $sil_credit -= $totalDays;
    }
    else if($sl_credit >= $totalDays)
    {
        $sl_consume = $totalDays;
        $sl_credit -= $totalDays;
    }
    else
    {
        $vl_sl_consume = $totalDays;
        $vl_sl_credit -= $totalDays;
    }
}
else if($leave_type == 'H')
{
    $halfday_consume = $totalDays;
    $halfday_credit -= $totalDays;
}

// 檢查假期額度是否足夠
if($sil_credit < 0 || $vl_sl_credit < 0 || $vl_credit < 0 || $sl_credit < 0 || $halfday_credit < 0)
{
    echo json_encode(array(
        "message" => "Leave credit is not enough.", 
        "sil_consume" => $sil_consume, 
        "vl_consume" => $vl_consume, 
        "sl_consume" => $sl_consume, 
        "vl_sl_consume" => $vl_sl_consume, 
        "halfday_consume" => $halfday_consume, 
        "period" => $totalDays,
        "hours" => $totalHours
    ));
    die();
}

echo json_encode(array(
    "message" => "", 
    "sil_consume" => $sil_consume, 
    "vl_consume" => $vl_consume, 
    "sl_consume" => $sl_consume, 
    "vl_sl_consume" => $vl_sl_consume, 
    "halfday_consume" => $halfday_consume, 
    "period" => $totalDays,
    "hours" => $totalHours
)); 