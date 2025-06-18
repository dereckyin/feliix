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

date_default_timezone_set('Asia/Taipei');

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $access7 = false;
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $position = $decoded->data->position;
        $department = $decoded->data->department;
        $username = $decoded->data->username;

        if(trim(strtoupper($department)) == '')
        {
            if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
            {
                $access7 = true;
            }
        }

        if($username == "Glendon Wendell Co")
        {
            $access7 = true;
        }

        if(!$access7)
        {
         http_response_code(401);

         echo json_encode(array("message" => "Access denied."));
         die();
        }
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

$total_amount = 0;
$total_ar = 0;
$total_d = 0;
$total_p = 0;
$total_net_amount = 0;
$total_tax_withheld = 0;

switch ($method) {
    case 'GET':
        $strDate = (isset($_GET['d']) ?  $_GET['d'] : "");
        $strEDate = (isset($_GET['e']) ?  $_GET['e'] : "");
        $sale_person = (isset($_GET['p']) ?  $_GET['p'] : "");
        $sale_person = urldecode($sale_person);
        $category = (isset($_GET['c']) ?  $_GET['c'] : "");
    
        if($strDate == '')
        {
            $strDate = date('Y-m-d');

            for($i = 0; $i < 2; $i++)
            {
                $strDate = date("Y-m-d",strtotime($strDate . "first day of " . $i . " month"));
                $merged_results[] =  GetOneMonth($strDate, $sale_person, $category, $db);

                $total_amount = 0;
                $total_ar = 0;
                $total_d = 0;
                $total_p = 0;
                $total_net_amount = 0;
                $total_tax_withheld = 0;
            }
        }

        if($strDate != '' && $strEDate != "")
        {
            $interval = DateInterval::createFromDateString('1 month');
            $period = new DatePeriod(new DateTime($strDate), $interval, new DateTime(date("Y-m-d",strtotime($strEDate . "first day of 1 month"))));

            foreach ($period as $dt) {
                $strDate = $dt->format("Y-m-d");
                $merged_results[] =  GetCurrentMonth($strDate, $sale_person, $category, $db);

                $total_amount = 0;
                $total_ar = 0;
                $total_d = 0;
                $total_p = 0;
                $total_net_amount = 0;
                $total_tax_withheld = 0;

            }
        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

function GetCurrentMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $total1 = array(
        "total_amount" => $GLOBALS["total_amount"],
        "total_ar" => $GLOBALS["total_ar"],
        "total_d" => $GLOBALS["total_d"],
        "total_p" => $GLOBALS["total_p"],
        "total_net_amount" => $GLOBALS["total_net_amount"],
        "total_tax_withheld" => $GLOBALS["total_tax_withheld"],
    );

    return array("date" => date("Y/m",strtotime($strDate . "first day of 0 month")), "report" => $report1, "total" => $total1,);
}

function GetOneMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -2 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 0 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $total1 = array(
        "total_amount" => $GLOBALS["total_amount"],
        "total_ar" => $GLOBALS["total_ar"],
        "total_d" => $GLOBALS["total_d"],
        "total_p" => $GLOBALS["total_p"],
        "total_net_amount" => $GLOBALS["total_net_amount"],
        "total_tax_withheld" => $GLOBALS["total_tax_withheld"],
    );

    return array("date" => date("Y/m",strtotime($strDate . "first day of -1 month")), "report" => $report1, "total" => $total1,);
}


function GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db){
    $sql = "SELECT pm.id pid, user.username,
                        pm.project_name,
                        pm.catagory_id,
                        CASE pm.catagory_id  
                                WHEN 1 THEN 'Office System'
                                WHEN 2 THEN 'Lighting'
                                WHEN 4 THEN 'Lighting'
                                WHEN 5 THEN 'Lighting'
                                WHEN 6 THEN 'Office System'
                                WHEN 7 THEN 'Office System'
                                ELSE ''  
                            END   catagory,
                        pm.`client`,
                        pm.final_amount,
                        pm.tax_withheld,    
                        pp.received_date,
                        CASE pp.kind
                                WHEN 0 THEN 'Down Payment'
                                WHEN 1 THEN 'Payment'
                                ELSE ''  
                            END   pay_type,
                        pp.kind,
                        pp.amount
                    FROM   project_proof pp
                    LEFT JOIN project_main pm
                            ON pp.project_id = pm.id
                    LEFT JOIN user
                            ON pm.create_id = user.id
                    WHERE pp.status = 1
                    AND pp.kind = 0
                    and pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "'
                    AND pp.id IN (SELECT * 
                                FROM (SELECT MIN(n.id)
                                        FROM project_proof n where STATUS = 1
                                    GROUP BY n.project_id , n.kind HAVING n.kind = 0) x) ";

        if($sale_person != "")
        {
            $sql = $sql . " and user.username = '" . $sale_person . "' ";
        }

        if($category != "")
        {
            $sql = $sql . " and pm.catagory_id = " . $category . " ";
        }
                
        
        $sql = $sql . " UNION 

                SELECT pm.id pid, user.username,
                        pm.project_name,
                        pm.catagory_id,
                        CASE pm.catagory_id  
                                WHEN 1 THEN 'Office System'
                                WHEN 2 THEN 'Lighting'
                                WHEN 4 THEN 'Lighting'
                                WHEN 5 THEN 'Lighting'
                                WHEN 6 THEN 'Office System'
                                WHEN 7 THEN 'Office System'
                                ELSE ''  
                            END   catagory,
                        pm.`client`,
                        pm.final_amount,
                        pm.tax_withheld,    
                        pp.received_date,
                        CASE pp.kind
                                WHEN 0 THEN 'Down Payment'
                                WHEN 1 THEN 'Payment'
                                ELSE ''  
                            END   pay_type,
                        pp.kind,
                        pp.amount
                    FROM   project_proof pp
                    LEFT JOIN project_main pm
                            ON pp.project_id = pm.id
                    LEFT JOIN user
                            ON pm.create_id = user.id
                    WHERE pp.status = 1
                    AND pp.kind = 1
                    and pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "'
                    AND pp.id IN (SELECT id 
                                FROM (SELECT MIN(n.id) id, SUM(amount) amt, kind, (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = n.project_id AND p.kind = 0) down_sum
                                        FROM project_proof n where STATUS = 1
                                    GROUP BY n.project_id , n.kind
                    HAVING down_sum = 0) x) ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
                    ORDER BY username, catagory
                    ";

        

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $username = "";
        
        $sub_amount = 0;
        $sub_ar = 0;
        $sub_d = 0;
        $sub_p = 0;
        $sub_net_amount = 0;
        $sub_tax_withheld = 0;

        $l_catagory = [];
        $o_catagory = [];

        $subtotal  = 0;
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($username != $row['username'] && $username != "") {
                
                $sub_amount = 0;
                $sub_ar = 0;
                $sub_d = 0;
                $sub_p = 0;
                $sub_net_amount = 0;
                $sub_tax_withheld = 0;

                $subtotal = 0;
/*
                if($o_catagory == []){
                    array_push($o_catagory, array(
                                            "username" => "",
                                            "project_name" => "",
                                            "client" => "",
                                            "dsum" => 0,
                                            "psum" => 0,
                                            "final_amount" => 0,
                                            "tax_withheld" => 0,
                                            "ar" => 0,
                                            "net_amount" => 0,));
                 }
    
                 if($l_catagory == []){
                    array_push($l_catagory, array(
                                            "username" => "",
                                            "project_name" => "",
                                            "client" => "",
                                            "dsum" => 0,
                                            "psum" => 0,
                                            "final_amount" => 0,
                                            "tax_withheld" => 0,
                                            "ar" => 0,
                                            "net_amount" => 0,));
                 }
*/
                foreach ($o_catagory as &$value) {
                    $sub_amount += $value['final_amount'];
                    $sub_ar += $value['ar'];
                    $sub_d += $value['dsum'];
                    $sub_p += $value['psum'];
                    $sub_net_amount += $value['net_amount'];
                    $sub_tax_withheld += $value['tax_withheld'];

                    $subtotal += $value['dsum'] + $value['psum'];

                    $GLOBALS['total_amount'] += $value['final_amount'];
                    $GLOBALS['total_ar'] += $value['ar'];
                    $GLOBALS['total_d'] += $value['dsum'];
                    $GLOBALS['total_p'] += $value['psum'];
                    $GLOBALS['total_net_amount'] += $value['net_amount'];
                    $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
                }
    
                foreach ($l_catagory as &$value) {
                    $sub_amount += $value['final_amount'];
                    $sub_ar += $value['ar'];
                    $sub_d += $value['dsum'];
                    $sub_p += $value['psum'];
                    $sub_net_amount += $value['net_amount'];
                    $sub_tax_withheld += $value['tax_withheld'];

                    $subtotal += $value['dsum'] + $value['psum'];

                    $GLOBALS['total_amount'] += $value['final_amount'];
                    $GLOBALS['total_ar'] += $value['ar'];
                    $GLOBALS['total_d'] += $value['dsum'];
                    $GLOBALS['total_p'] += $value['psum'];
                    $GLOBALS['total_net_amount'] += $value['net_amount'];
                    $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
                }

                $merged_results[] = array(
                    "username" => $username,
                    "l_catagory" => $l_catagory,
                    "o_catagory" => $o_catagory,
             
                    "sub_amount" => $sub_amount,
                    "sub_ar" => $sub_ar,
                    "sub_d" => $sub_d,
                    "sub_p" => $sub_p,
                    "sub_net_amount" => $sub_net_amount,
                    "sub_tax_withheld" => $sub_tax_withheld,

                    "subtotal" => $subtotal,
                
                );

                $l_catagory = [];
                $o_catagory = [];

                $sub_amount = 0;
                $sub_ar = 0;
                $sub_d = 0;
                $sub_p = 0;
                $sub_net_amount = 0;
                $sub_tax_withheld = 0;

                $subtotal = 0;
            }

            $username = $row['username'];
       
            if($row['catagory_id'] == 1)
                array_push($o_catagory, GetDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));
            if($row['catagory_id'] == 2)
                array_push($l_catagory, GetDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));

        }

        if ($username != "") {
            $sub_amount = 0;
            $sub_ar = 0;
            $sub_d = 0;
            $sub_p = 0;
            $sub_net_amount = 0;
            $sub_tax_withheld = 0;

            $subtotal = 0;
/*
            if($o_catagory == []){
                array_push($o_catagory, array(
                                        "username" => "",
                                        "project_name" => "",
                                        "client" => "",
                                        "dsum" => 0,
                                        "psum" => 0,
                                        "final_amount" => 0,
                                        "tax_withheld" => 0,
                                        "ar" => 0,
                                        "net_amount" => 0,));
             }

             if($l_catagory == []){
                array_push($l_catagory, array(
                                        "username" => "",
                                        "project_name" => "",
                                        "client" => "",
                                        "dsum" => 0,
                                        "psum" => 0,
                                        "final_amount" => 0,
                                        "tax_withheld" => 0,
                                        "ar" => 0,
                                        "net_amount" => 0,));
             }
*/
            foreach ($o_catagory as &$value) {
                $sub_amount += $value['final_amount'];
                $sub_ar += $value['ar'];
                $sub_d += $value['dsum'];
                $sub_p += $value['psum'];
                $sub_net_amount += $value['net_amount'];
                $sub_tax_withheld += $value['tax_withheld'];

                $subtotal += $value['dsum'] + $value['psum'];

                $GLOBALS['total_amount'] += $value['final_amount'];
                $GLOBALS['total_ar'] += $value['ar'];
                $GLOBALS['total_d'] += $value['dsum'];
                $GLOBALS['total_p'] += $value['psum'];
                $GLOBALS['total_net_amount'] += $value['net_amount'];
                $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
            }

            foreach ($l_catagory as &$value) {
                $sub_amount += $value['final_amount'];
                $sub_ar += $value['ar'];
                $sub_d += $value['dsum'];
                $sub_p += $value['psum'];
                $sub_net_amount += $value['net_amount'];
                $sub_tax_withheld += $value['tax_withheld'];

                $subtotal += $value['dsum'] + $value['psum'];

                $GLOBALS['total_amount'] += $value['final_amount'];
                $GLOBALS['total_ar'] += $value['ar'];
                $GLOBALS['total_d'] += $value['dsum'];
                $GLOBALS['total_p'] += $value['psum'];
                $GLOBALS['total_net_amount'] += $value['net_amount'];
                $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
            }

            $merged_results[] = array(
                "username" => $username,
                "l_catagory" => $l_catagory,
                "o_catagory" => $o_catagory,
  
                "sub_amount" => $sub_amount,
                "sub_ar" => $sub_ar,
                "sub_d" => $sub_d,
                "sub_p" => $sub_p,
                "sub_net_amount" => $sub_net_amount,
                "sub_tax_withheld" => $sub_tax_withheld,

                "subtotal" => $subtotal,
            );

        }

        if(count($merged_results) > 0)
        {
            usort($merged_results, function ($item1, $item2) {
                return $item2['sub_amount'] <=> $item1['sub_amount'];
            });
        }

        return $merged_results;
}

function GetDetail($_pid, $sdate, $edate, $sale_person, $category, $db)
{
    $sql = "SELECT user.username,
                pm.project_name,
                pm.`client`,
                sum(CASE WHEN pp.kind = 1  THEN amount ELSE 0 END) psum,
                sum(CASE WHEN pp.kind = 0  THEN amount ELSE 0 END) dsum,
                COALESCE(pm.final_amount, 0) final_amount,
                COALESCE(pm.tax_withheld, 0) tax_withheld
            FROM   project_proof pp
            LEFT JOIN project_main pm
                    ON pp.project_id = pm.id
            LEFT JOIN user
                    ON pm.create_id = user.id
            WHERE pp.status = 1
            and pp.received_date > '" . $sdate . " 23:59:59' AND pp.received_date < '" . $edate . "'
            ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
            AND pm.id = " . $_pid;

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = array(
            "username" => $row["username"],
            "project_name" => $row["project_name"],
            "client" => $row["client"],
            "dsum" => $row["dsum"],
            "psum" => $row["psum"],
            "final_amount" => $row["final_amount"],
            "tax_withheld" => $row["tax_withheld"],
            "ar" => $row["final_amount"] - $row["dsum"] - $row["psum"] - $row["tax_withheld"],
            "net_amount" => $row["final_amount"] - $row["tax_withheld"],
        );
    }

    return $merged_results;
}
