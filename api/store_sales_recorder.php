<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$keyword = (isset($_GET['keyword']) ? $_GET['keyword'] : "");
$start_date = (isset($_GET['start_date']) ? $_GET['start_date'] : "");
$end_date = (isset($_GET['end_date']) ? $_GET['end_date'] : "");
$page = (isset($_GET['page']) ? $_GET['page'] : 1);

$merged_results = array();

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';


use Google\Cloud\Storage\StorageClient;

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $query = "SELECT  0 as is_checked, 
                                ss.id, 
                                ss.sales_date, 
                                ss.sales_name, 
                                ss.customer_name,
                                ss.discount,
                                ss.invoice,
                                ss.remark,
                                ss.payment_method,
                                ss.teminal,
                                ss.`status`,
                                DATE_FORMAT(ss.crt_time ,'%Y-%m-%d') crt_time
                                from store_sales ss
                                left join store_sales_record sr 
                                on sr.sales_id = ss.id
                                where 1=1 ";

        if($start_date!='') {
            $query = $query . " and ss.sales_date >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.sales_date <= '$end_date' ";
        }

        if($keyword != '')
            $query .= " AND (ss.sales_name like '%" . $keyword . "%' or ss.customer_name like '%" . $keyword . "%' or ss.remark like '%" . $keyword . "%' or sr.product_name like '%" . $keyword . "%') ";

        $query .= "group by
        ss.id, 
        ss.sales_date, 
        ss.sales_name, 
        ss.customer_name,
        ss.discount,
        ss.invoice,
        ss.remark,
        ss.payment_method,
        ss.teminal,
        ss.`status`,
        ss.crt_time ";
        
        $query .= " order by ss.id desc";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $id = $row['id'];
            $sales_date = $row['sales_date'];
            $sales_name = $row['sales_name'];
            $customer_name = $row['customer_name'];
            $discount = $row['discount'];
            $invoice = $row['invoice'];
            $remark = $row['remark'];
            $payment_method = $row['payment_method'];
            $teminal = $row['teminal'];
            $status = $row['status'];
            $crt_time = $row['crt_time'];

            $items = GetSalesDetail($id, $db);
           
            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "sales_date" => $sales_date,
                "sales_name" => $sales_name,
                "customer_name" => $customer_name,
                "discount" => $discount,
                "invoice" => $invoice,
                "remark" => $remark,
                "payment_method" => GetPayment($payment_method),
                "teminal" => $teminal,
                "status" => $status,
                "payment" => $items,
                "total_amount" => GetAmount($items),
                "crt_time"=> $crt_time,
            );
        }

        // response in json format
        echo json_encode($merged_results);
      
    }
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}

function GetSalesDetail($sales_id, $db){
    $query = "
            SELECT 0 as is_checked, id, product_name, qty, price, free, DATE_FORMAT(crt_time, '%Y/%m/%d') crt_time
                FROM store_sales_record
            WHERE  sales_id = " . $sales_id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $qty = $row['qty'] == 0 ? "" : $row['qty'];
        $price = $row['price'] == 0 ? "" : $row['price'];
        $free = $row['free'] == "" ? "" : $row['free'];
        $product_name = $row['product_name'] == "" ? "" : $row['product_name'];
        $crt_time = $row['crt_time'] == "" ? "" : $row['crt_time'];
    
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "qty" => $qty,
            "price" => $price,
            "free" => $free,
            "product_name" => $product_name,
           "crt_time" => $crt_time,
        );
    }

    return $merged_results;
}

function GetAmount($array)
{
    $amount = 0;

    foreach($array as $item) {
        if($item['free'] == '')
            $amount += ($item['qty'] == "" ? 0 : $item['qty']) * ($item['price'] == "" ? 0 : $item['price']);
    }

    return $amount;
}



function GetPayment($loc)
{
    $location = "";
    switch ($loc) {
        case "cash":
            $location = "Cash";
            break;
        case "visa":
            $location = "Visa Card";
            break;
        case "master":
            $location = "Master Card";
            break;
        case "jcb":
            $location = "JCB Card";
            break;
        case "debit":
            $location = "Debit Card";
            break;
        default:
            $location = "";
            break;
    }

    return $location;
}
