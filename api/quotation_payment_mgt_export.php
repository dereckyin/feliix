<?php
ob_start();
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
require '../vendor/autoload.php';

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


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
$id = (isset($_GET['id']) ?  $_GET['id'] : '');
$fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');
$fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');
$ft = (isset($_GET['ft']) ?  $_GET['ft'] : '');

$ft = urldecode($ft);

$fal = (isset($_GET['fal']) ?  $_GET['fal'] : '');
$fau = (isset($_GET['fau']) ?  $_GET['fau'] : '');
$fpl = (isset($_GET['fpl']) ?  $_GET['fpl'] : '');
$fpu = (isset($_GET['fpu']) ?  $_GET['fpu'] : '');

$fk = (isset($_GET['fk']) ?  $_GET['fk'] : '');
$fk = urldecode($fk);

$fkp = (isset($_GET['fkp']) ?  $_GET['fkp'] : '');
$fkp = urldecode($fkp);

$of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
$ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
$of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
$ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : 1);
$size = (isset($_GET['size']) ?  $_GET['size'] : 5);

$merged_results = array();
$return_result = array();

$query = "SELECT pm.id,
            Coalesce(pc.category, '')                    category,
            pct.client_type,
            pct.class_name                               pct_class,
            pp.priority,
            pp.class_name                                pp_class,
            pm.project_name,
            pm.final_amount,
            pm.tax_withheld,
            pm.billing_name,
            (SELECT sum(pp.amount) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1) payment,
            (SELECT sum(pp.amount) FROM   project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0) dp_payment,
            Coalesce(ps.project_status, '')              project_status,
            Coalesce((SELECT project_est_prob.prob
                    FROM   project_est_prob
                    WHERE  project_est_prob.project_id = pm.id
                    ORDER  BY created_at DESC
                    LIMIT  1), pm.estimate_close_prob) estimate_close_prob,
            user.username,
            Date_format(pm.created_at, '%Y-%m-%d')       created_at,
            Date_format(pm.updated_at, '%Y-%m-%d')       updated_at,
            Coalesce((SELECT project_stage.stage
                    FROM   project_stages
                            LEFT JOIN project_stage
                                    ON project_stage.id = project_stages.stage_id
                    WHERE  project_stages.project_id = pm.id
                            AND project_stages.stages_status_id = 1
                    ORDER  BY `sequence` DESC
                    LIMIT  1), '')                     stage
            FROM   project_main pm
            LEFT JOIN project_category pc
                ON pm.catagory_id = pc.id
            LEFT JOIN project_client_type pct
                ON pm.client_type_id = pct.id
            LEFT JOIN project_priority pp
                ON pm.priority_id = pp.id
            LEFT JOIN project_status ps
                ON pm.project_status_id = ps.id
            LEFT JOIN project_stage pst
                ON pm.stage_id = pst.id
            LEFT JOIN user
                ON pm.create_id = user.id
            WHERE  1 = 1 ";

if($fc != "" && $fc != "0")
{
    $query = $query . " and pm.catagory_id = " . $fc . " ";
   
}

if($fs != "" && $fs != "0")
{
    $query = $query . " and pm.project_status_id = '" . $fs . "' ";
    
}

if($ft != "" && $ft != "0")
{
    $query = $query . " and user.username = '" . $ft . "' ";
  
}

if($id != "" && $id != "0")
{
    $query = $query . " and pm.id = " . $id . " ";
 
}

if($fal != "" && $fal != "0")
{
    $query = $query . " and pm.final_amount >= " . $fal . " ";
  
}

if($fau != "" && $fau != "0")
{
    $query = $query . " and pm.final_amount <= " . $fau . " ";
  
}

if($fpl != "" && $fpl != "0")
{
    $query = $query . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) >= " . $fpl . " ";
 
}

if($fpu != "" && $fpu != "0")
{
    $query = $query . " and Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) <= " . $fpu . " ";
    
}

if($fkp != "")
{
    $query = $query . " and pm.project_name like '%" . $fkp . "%' ";
}

$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {
        case 1:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.final_amount, 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount, 99999999)";
            break;  
        case 4:
            if($ofd1 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 99999999)";
            break;
        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 99999999)";
            break;
        case 6:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 99999999) ";
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
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.final_amount, 0) desc";
            else
                $sOrder .= ", Coalesce(pm.final_amount, 99999999)";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) desc";
            else
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 99999999)";
            break;
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) desc";
            else
                $sOrder .= ", Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 99999999)";
            break;
        case 6:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 0) desc";
            else
                $sOrder .= ", Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 99999999) ";
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
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.final_amount, 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount, 99999999)";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 0), 99999999)";
            break;
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 0) desc";
            else
                $sOrder = "Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1  AND pp.kind = 1), 99999999)";
            break;
        case 6:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 0) desc";
            else
                $sOrder = "Coalesce(pm.final_amount - Coalesce((SELECT sum(pp.amount) FROM  project_proof pp  WHERE  pp.project_id = pm.id  AND pp.status = 1), 0), 99999999) ";
            break;
        default:
    }
}

if($sOrder != "")
    $query = $query . " order by  " . $sOrder;
else
    $query = $query . " order by pm.created_at desc ";

$stmt = $db->prepare( $query );
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $category = $row['category'];
    $category_name = GetCategory($row['category'], $db);
    $client_type = $row['client_type'];
    $pct_class = $row['pct_class'];
    $priority = $row['priority'];

    $pp_class = $row['pp_class'];
    $project_name = $row['project_name'];
    $project_status = $row['project_status'];
    $estimate_close_prob = $row['estimate_close_prob'];
    $username = $row['username'];

    $pm = $row['payment'];
    $dpm = $row['dp_payment'];

    $final_amount = $row['final_amount'];
    $tax_withheld = $row['tax_withheld'];
    $billing_name = $row['billing_name'];

    $quote_file_string = GetQuoteFileString($row['id'], $db);

    $payment_amount = GetPaymentAmount($row['id'], $db);
    $down_payment_amount = GetDownPaymentAmount($row['id'], $db);

    $ar = null;
    if($final_amount != null)
    {
        $pay = 0;
        if($payment_amount != null)
            $pay = $payment_amount;
        $down_pay = 0;
        if($down_payment_amount != null)
            $down_pay = $down_payment_amount;

        $ar = $final_amount - $pay - $down_pay - $tax_withheld;
    }

    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];
    $stage = $row['stage'];
    $quote = GetQuote($row['id'], $db);
    $payment = GetPayment($row['id'], $db);

    $client_po = GetClientPO($row['id'], $db);
    $client_other = GetClientOther($row['id'], $db);

    $client_po_files = GetClientPOFile($row['id'], $db);
    $client_other_files = GetClientOtherFile($row['id'], $db);

    $final_quotation = GetFinalQuote($row['id'], $db);

    $down_pay_amount = RetriveDownPaymentAmount($payment);
    $down_pay_date = RetriveDownPaymentDate($payment);

    $full_pay_amount = RetrivePaymentAmount($payment);
    $full_pay_date = RetrivePaymentDate($payment);

    $invoice = RetrieveInvoice($payment);


    $merged_results[] = array(
        "id" => $id,
        "category" => $category,
        "category_name" => $category_name,
        "client_type" => $client_type,
        "pct_class" => $pct_class,
        "priority" => $priority,
        "pp_class" => $pp_class,
        "project_name" => $project_name,
        "project_status" => $project_status,
        "payment_amount" => $payment_amount,
        "down_payment_amount" => $down_payment_amount,
        "ar" => $ar,
        "final_quotation" => $final_quotation,
        "username" => $username,
        "created_at" => $created_at,
        "updated_at" => $updated_at,
        "stage" => $stage,
        "final_amount" => $final_amount,
        "tax_withheld" => $tax_withheld,
        "net_amount" => ($final_amount - $tax_withheld == 0 ? "" : $final_amount - $tax_withheld),
        "billing_name" => $billing_name,
        "quote" => $quote,
        "client_po" => $client_po,
        "client_other" => $client_other,
        "client_po_file" => $client_po_files,
        "client_other_file" => $client_other_files,
        "payment" => $payment,
        "down_pay_amount" => $down_pay_amount,
        "down_pay_date" => $down_pay_date,
        "full_pay_amount" => $full_pay_amount,
        "full_pay_date" => $full_pay_date,
        "invoice" => $invoice,
        "pm" => $pm,
        "dpm" => $dpm,
        "quote_file_string" => $quote_file_string,
        
    );
}

if($fk != "")
{
    $_result = array();

    foreach ($merged_results as &$value) {
        if(
            preg_match("/{$fk}/i", $value['project_name']) || 
            preg_match("/{$fk}/i", $value['quote_file_string']))
        {
            $_result[] = $value;
        }
    }

    $return_result = $_result;
    
}
else
    $return_result = $merged_results;

$baseURL = "https://storage.googleapis.com/feliiximg/";

// response in json format
$styleArray = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => array('rgb' => '000000'),
        ),
    ),
);

$spreadsheet = new Spreadsheet();

$spreadsheet->getProperties()->setCreator('PhpOffice')
        ->setLastModifiedBy('PhpOffice')
        ->setTitle('Office 2007 XLSX Test Document')
        ->setSubject('Office 2007 XLSX Test Document')
        ->setDescription('PhpOffice')
        ->setKeywords('PhpOffice')
        ->setCategory('PhpOffice');

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Sheet 1");
$sheet->getMergeCells();

$i = 1;
// title 
$sheet->setCellValue('A'. $i, 'Project Category');
$sheet->setCellValue('B'. $i, 'Project Name');
$sheet->setCellValue('C'. $i, 'Status');
$sheet->setCellValue('D'. $i, 'Project Creator');
$sheet->setCellValue('E'. $i, 'Execution Period');
$sheet->setCellValue('F'. $i, 'Amount');
$sheet->setCellValue('G'. $i, 'Tax Withheld');
$sheet->setCellValue('H'. $i, 'Down Payment');
$sheet->setCellValue('I'. $i, 'Payment');
$sheet->setCellValue('J'. $i, 'A/R');
$sheet->setCellValue('K'. $i, 'File');

$sheet->getStyle('A' . $i . ':' . 'K' . $i)->getFont()->setBold(true);


foreach($return_result as $row)
{
    $i = $i + 1;
    $sheet->setCellValue('A' . $i, $row['category']);
    $sheet->setCellValue('B' . $i, $row['project_name']);
    $sheet->setCellValue('C' . $i, $row['project_status']);
    $sheet->setCellValue('D' . $i, $row['username']);
    $sheet->setCellValue('E' . $i, $row['created_at'] . " ~ " . $row['updated_at']);
    $sheet->setCellValue('F' . $i, $row['final_amount'] === null ? '' : number_format((float)$row['final_amount'], 2, '.', ''));
    $sheet->setCellValue('G' . $i, $row['tax_withheld'] === null ? '' : number_format((float)$row['tax_withheld'], 2, '.', ''));
    $sheet->setCellValue('H' . $i, $row['down_payment_amount'] === null ? '' : number_format((float)$row['down_payment_amount'], 2, '.', ''));
    $sheet->setCellValue('I' . $i, $row['payment_amount'] === null ? '' : number_format((float)$row['payment_amount'], 2, '.', ''));
    $sheet->setCellValue('J' . $i, $row['ar'] === null ? '' : number_format((float)$row['ar'], 2, '.', ''));

    $files = $row['payment'];

    $j = 0;
    foreach ($files as $file)
    {
        $items = $file['items'];
        $checked = $file['checked'];

        if($checked == 1)
        {
            foreach ($items as $item)
            {
                $file_name = $item['filename'];
                $file_path = $item['bucket'];
                $file_url = $item['gcp_name'];

                if($file_url != '')
                {
                    $link = $baseURL . $file_url;
                    $sheet->setCellValue(chr(75+$j) . $i, $file_name);
                    $sheet->getCellByColumnAndRow($j + 11, $i)->getHyperlink()->setUrl($link);

                    $j++;
                }
                else
                    $sheet->setCellValue(chr(75+$j) . $i, '');

                
            }
        }
    }
}

  

// $sheet->getStyle('A1:' . 'J' . --$i)->applyFromArray($styleArray);

ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="file.xlsx"');

header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');


exit;





function GetFinalQuote($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM   project_quotation pm
           
        LEFT JOIN gcp_storage_file f 
            ON f.batch_id = pm.id AND f.batch_type = 'quote' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.final_quotation = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetClientOtherFile($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM gcp_storage_file f 
           
        LEFT JOIN   project_client_po pm
            ON f.batch_id = pm.id AND f.batch_type = 'client_other' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
  
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetClientPOFile($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename, 
            COALESCE(f.bucketname, '') bucket, 
            COALESCE(f.gcp_name, '') gcp_name
        FROM  gcp_storage_file f 
           
        LEFT JOIN  project_client_po pm 
            ON f.batch_id = pm.id AND f.batch_type = 'client_po' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetQuote($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark comment,
            u.username,
            pm.created_at,
            final_quotation
        FROM   project_quotation pm
            LEFT JOIN user u
                    ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
        ORDER BY final_quotation desc, created_at
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $comment = $row['comment'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $final_quotation = $row['final_quotation'];

        $create = substr($row['created_at'], 0, 10);

        $items = GetItem($row['id'], $db, 'quote');

        $searchstr = $comment . " " . GetItemString($row['id'], $db, 'quote') . " " . $username . " " . ($final_quotation ==  '0' ? 'N' : 'Y');
       
        $merged_results[] = array(
            "id" => $id,
            "comment" => $comment,
            "username" => $username,
            "created_at" => $created_at,
            "final_quotation" => $final_quotation,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetPaymentAmount($project_id, $db){
    $amount = null;
    $query = "
        SELECT 
            pm.amount
        FROM   project_proof pm
        WHERE  project_id = " . $project_id . "
            AND pm.status = 1
            AND pm.kind = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['amount'] != null)
            $amount += $row['amount'];
    }

    return $amount;
}

function GetDownPaymentAmount($project_id, $db){
    $amount = null;
    $query = "
        SELECT 
            pm.amount
        FROM   project_proof pm
        WHERE  project_id = " . $project_id . "
            AND pm.status = 1
            AND pm.kind = 0
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['amount'] != null)
            $amount += $row['amount'];
    }

    return $amount;
}


function GetClientPO($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.kind
        FROM   project_client_po pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.`kind` = 1 AND pm.status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $kind = $row['kind'];
     
        $items = GetItem($row['id'], $db, 'client_po');

        $searchstr = $remark . " "  . GetItemString($row['id'], $db, 'client_po') . " " . $username;
        $create = substr($created_at, 0, 10);
        $searchstr = $create . " " . $searchstr;
        $merged_results[] = array(
            "id" => $id,
            "comment" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "kind" => $kind,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetClientRemarks($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.kind
        FROM   project_client_po pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.`kind` = 3 AND pm.status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $kind = $row['kind'];
     
        $items = GetItem($row['id'], $db, 'client_other');

        $searchstr = ($kind ==  '2' ? 'Other Files' : 'Remarks/Status') . " " . $remark . " "  . GetItemString($row['id'], $db, 'client_po') . " " . $username;
        $create = substr($created_at, 0, 10);
        $searchstr = $create . " " . $searchstr;
        $merged_results[] = array(
            "id" => $id,
            "comment" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "kind" => $kind,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetClientOther($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.kind
        FROM   project_client_po pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.`kind` <> 1 AND pm.status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $kind = $row['kind'];
     
        $items = GetItem($row['id'], $db, 'client_other');

        $searchstr = ($kind ==  '2' ? 'Other Files' : 'Remarks/Status') . " " . $remark . " "  . GetItemString($row['id'], $db, 'client_po') . " " . $username;
        $create = substr($created_at, 0, 10);
        $searchstr = $create . " " . $searchstr;
        $merged_results[] = array(
            "id" => $id,
            "comment" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "kind" => $kind,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetPayment($project_id, $db){
    $query = "
        SELECT pm.id,
            pm.remark,
            u.username,
            pm.created_at,
            pm.received_date,
            pm.kind,
            pm.amount,
            pm.invoice,
            pm.detail,
            pm.status checked,
            pm.checked_id,
            pm.checked_at
        FROM   project_proof pm
            LEFT JOIN user u
                ON u.id = pm.create_id
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -2
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $remark = $row['remark'];
        $username = $row['username'];
        $created_at = $row['created_at'];
        $received_date = $row['received_date'];
        $kind = $row['kind'];
        $amount = $row['amount'];
        $invoice = $row['invoice'];
        $detail = $row['detail'];
        $checked = $row['checked'];
        $checked_id = $row['checked_id'];
        $checked_at = $row['checked_at'];

        $items = GetItem($row['id'], $db, 'proof');

        $status = "";
        switch ($checked)
        {
            case "0":
                $status = "Under Checking";
                break;
            case "1":
                $status = "Checked: True";
                break;
            case "-1":
                $status = "Checked: False";
                break;
            default:
                $status = "Under Checking";
        }

        $searchstr = ($kind ==  '0' ? 'Down Payment' : 'Full Payment') . " " . $remark . " " . $status . " " . GetItemString($row['id'], $db, 'proof') . " " . $username . " " . $amount;
        $create = substr($created_at, 0, 10);
        $merged_results[] = array(
            "id" => $id,
            "remark" => $remark,
            "username" => $username,
            "created_at" => $created_at,
            "received_date" => $received_date,
            "kind" => $kind,
            "amount" => $amount,
            "invoice" => $invoice,
            "detail" => $detail,
            "checked" => $checked,
            "checked_id" => $checked_id,
            "checked_at" => $checked_at,
            "items" => $items,
            "create" => $create,
            "searchstr" => strtolower($searchstr),
        );
    }

    return $merged_results;
}

function GetItemString($batch_id, $db, $type){
    $query = "
        
        SELECT f.id,
            coalesce(f.filename, '')   filename 
        FROM   gcp_storage_file f

            LEFT JOIN user u
                ON u.id = f.create_id
        WHERE batch_id = " . $batch_id . "
        AND f.batch_type = '" . $type . "'
            AND f.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results .= " " . $row['filename'];
    }

    return $merged_results;
}

function GetItem($batch_id, $db, $type){
    $query = "
        
        SELECT f.id,
            coalesce(f.filename, '')   filename,
            coalesce(f.bucketname, '') bucket,
            coalesce(f.gcp_name, '')   gcp_name,
            u.username,
            f.created_at
        FROM   gcp_storage_file f

            LEFT JOIN user u
                ON u.id = f.create_id
        WHERE batch_id = " . $batch_id . "
        AND f.batch_type = '" . $type . "'
            AND f.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetCategory($id, $db)
{
    $category = "";
    $query = "SELECT category FROM project_category  where status <> -1 ".($id ? " and id=$id" : '');

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['category'] != null)
            $category = $row['category'];
    }

    return $category;
}

function GetQuoteFileString($project_id, $db){
    $query = "
        SELECT 
            COALESCE(f.filename, '') filename
        FROM   project_quotation pm
           
        LEFT JOIN gcp_storage_file f 
            ON f.batch_id = pm.id AND f.batch_type = 'quote' 
        WHERE  project_id = " . $project_id . "
            AND pm.status <> -1 
            AND pm.final_quotation = 1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results .= $row['filename'] . " ";
    }

    return $merged_results;
}

function RetriveDownPaymentAmount($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 0)
            array_push($merged_results,$pay["amount"]);
    }
    return $merged_results;
}

function RetriveDownPaymentDate($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 0)
            array_push($merged_results, $pay["received_date"] == "" ? "N/A" : $pay["received_date"]);
    }
    return $merged_results;
}

function RetrivePaymentAmount($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 1)
            array_push($merged_results,$pay["amount"]);
    }
    return $merged_results;
}

function RetrivePaymentDate($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        if($pay["kind"] == 1)
            array_push($merged_results, $pay["received_date"] == "" ? "N/A" : $pay["received_date"]);
    }
    return $merged_results;
}

function RetrieveInvoice($payment) {
    $merged_results = [];

    foreach($payment as $pay)
    {
        array_push($merged_results, $pay["invoice"] == "" ? "N/A" : $pay["invoice"]);
    }
    return $merged_results;
}