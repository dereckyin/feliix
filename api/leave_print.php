<?php
ob_start();
// required headers
 error_reporting(0);
 
 require '../vendor/autoload.php';
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/conf.php';

use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$leave_start = (isset($_POST['leave_start']) ?  $_POST['leave_start'] : '');
$leave_end = (isset($_POST['leave_end']) ?  $_POST['leave_end'] : '');

$department = (isset($_POST['department']) ?  $_POST['department'] : '');

$leave_start = str_replace('-', '', $leave_start);
$leave_end = str_replace('-', '', $leave_end);

$leave_start = str_replace('/', '', $leave_start);
$leave_end = str_replace('/', '', $leave_end);

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $sql = "SELECT ap.uid, u.username, leave_type,  
                    CASE
                        when ap.STATUS = -3 then 'V' 
                        when ap.STATUS = -1 then 'W'
                        when leave_type = 'D' then 'D' 
                        WHEN reject_id + re_reject_id > 0 THEN 'R' 
                        WHEN approval_id * re_approval_id > 0 THEN 'A'  
                        WHEN approval_id * re_approval_id = 0 THEN 'P' 
                    END approval,
                    start_date, start_time, end_date, end_time, `leave`, reason, 
                          approval_id, a.username approval_name, approval_at, 
                          reject_id, b.username reject_name, reject_at,
                    re_approval_id, c.username re_approval_name, re_approval_at, 
                          re_reject_id, d.username re_reject_name, re_reject_at, 
                          ap.pic_url, ap.created_at, ud.department, ut.title,

                          ap.leave_level, ap.sil, ap.vl_sl, ap.vl, ap.sl 

                    FROM apply_for_leave ap LEFT JOIN user u ON u.id = ap.uid 

                    LEFT JOIN user_department ud ON u.apartment_id = ud.id 
                    LEFT JOIN user_title ut ON u.title_id = ut.id 
                    
                    left JOIN user a ON a.id = ap.approval_id
                    left JOIN user b ON b.id = ap.reject_id
                    left JOIN user c ON c.id = ap.re_approval_id
                    left JOIN user d ON d.id = ap.re_reject_id
                          
                          WHERE ap.STATUS <> -1 and ap.id IN
                    (
                    SELECT distinct apply_id FROM `leave` WHERE 1=1 ";

            if(!empty($leave_start)) {
                $sql = $sql . " and apply_date >= '$leave_start' ";
            }

            if(!empty($leave_end)) {
                $sql = $sql . " and apply_date <= '$leave_end' ";
            }

            if(!empty($department)) {
                $sql = $sql . " and ud.id = $department ";
            }

            $sql = $sql . " ) ORDER BY u.username, ap.start_date  ";

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                $merged_results[] = $row;
          
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


            $sheet->setCellValue('A1', 'Applicant');
            $sheet->setCellValue('B1', 'Leave Type');
            $sheet->setCellValue('C1', 'Status');
            $sheet->setCellValue('D1', 'Start Time');
            $sheet->setCellValue('E1', 'End Time');
            $sheet->setCellValue('F1', 'Leave Lenght');
            $sheet->setCellValue('G1', 'Reason');
            $sheet->setCellValue('H1', 'Certificate of Diagnosis');

            $sheet->setCellValue('I1', 'Consumed SIL');
            $sheet->setCellValue('J1', 'Consumed VL/SL');
            $sheet->setCellValue('K1', 'Consumed VL');
            $sheet->setCellValue('L1', 'Consumed SL');
            $sheet->setCellValue('M1', 'Consumed UL');

            $sheet->setCellValue('N1', 'Application Time');
            $sheet->setCellValue('O1', '1st Approver');
            $sheet->setCellValue('P1', 'Decision');
            $sheet->setCellValue('Q1', 'Decision Time');
            $sheet->setCellValue('R1', '2nd Approver');
            $sheet->setCellValue('S1', 'Decision');
            $sheet->setCellValue('T1', 'Decision Time');

            $conf = new Conf();


            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $row['username']);
                $sheet->setCellValue('B' . $i, getLeaveType($row['leave_type']));
                $sheet->setCellValue('C' . $i, getLeaveStatus($row['approval']));
                $sheet->setCellValue('D' . $i, formateDate($row['start_date']) . " " . $row['start_time']);
                $sheet->setCellValue('E' . $i, formateDate($row['end_date']) . " " . $row['end_time']);
                $sheet->setCellValue('F' . $i, $row['leave']);
                $sheet->setCellValue('G' . $i, $row['reason']);

                if($row['pic_url'] != '')
                {
                    $link = $conf::$mail_ip . 'img/' . $row['pic_url'];
                    $sheet->setCellValue('H' . $i, 'Photo');
                    $sheet->getCellByColumnAndRow(8,$i)->getHyperlink()->setUrl($link);
                }
                else
                    $sheet->setCellValue('H' . $i, '');


                $sil = $row['sil'];
                $vl_sl = $row['vl_sl'];
                $vl = $row['vl'];
                $sl = $row['sl'];
                $ul = "0";

                if($row['leave_type'] == 'U')
                    $ul = $row['leave'];

                if($row['leave_type'] == 'A' || $row['leave_type'] == 'B' || $row['leave_type'] == 'C' || $row['leave_type'] == 'D')
                {
                    $sil = "";
                    $vl_sl = "";
                    $vl = "";
                    $sl = "";
                    $ul = "";
                }

                $sheet->setCellValue('I' . $i, $sil);
                $sheet->setCellValue('J' . $i, $vl_sl);
                $sheet->setCellValue('K' . $i, $vl);
                $sheet->setCellValue('L' . $i, $sl);
                $sheet->setCellValue('M' . $i, $ul);

                $sheet->setCellValue('N' . $i, $row['created_at']);

                // first decisioner
                if($row['approval_id'] != 0 && $row['uid'] != $row['approval_id'])
                {
                    $sheet->setCellValue('O' . $i, $row['approval_name']);
                    $sheet->setCellValue('P' . $i, 'Approved');
                    $sheet->setCellValue('Q' . $i, $row['approval_at']);
                }

                if($row['reject_id'] != 0 && $row['uid'] != $row['reject_id'])
                {
                    $sheet->setCellValue('O' . $i, $row['reject_name']);
                    $sheet->setCellValue('P' . $i, 'Rejected');
                    $sheet->setCellValue('Q' . $i, $row['reject_at']);
                }

                // second decisioner
                if($row['re_approval_id'] != 0 && $row['uid'] != $row['re_approval_id'])
                {
                    $sheet->setCellValue('R' . $i, $row['re_approval_name']);
                    $sheet->setCellValue('S' . $i, 'Approved');
                    $sheet->setCellValue('T' . $i, $row['re_approval_at']);
                }

                if($row['re_reject_id'] != 0 && $row['uid'] != $row['re_reject_id'])
                {
                    $sheet->setCellValue('R' . $i, $row['re_reject_name']);
                    $sheet->setCellValue('S' . $i, 'Rejected');
                    $sheet->setCellValue('T' . $i, $row['re_reject_at']);
                }


                $sheet->getStyle('A'. $i. ':' . 'T' . $i)->applyFromArray($styleArray);

                $i++;
            }

            $sheet->getStyle('A1:' . 'T1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'T' . --$i)->applyFromArray($styleArray);

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
    }
 
    // if decode fails, it means jwt is invalid
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
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}

function getLeaveStatus($type){
    $leave_type = '';

    if($type =="A")
        $leave_type = "Approved";
    if($type =="P")
        $leave_type = "Waiting for Approval";
    if($type =="R")
        $leave_type = "Rejected";
    if($type =="D")
        $leave_type = "Archived";
    if($type =="W")
        $leave_type = "Withdrawn";
    if($type =="V")
        $leave_type = "Void";
    
    return $leave_type;
}

function getLeaveType($type){
    $leave_type = '';

    if($type =="A")
        $leave_type = "Service Incentive Leave";
    if($type =="N")
        $leave_type = "Vaction Leave";
    if($type =="B" || $type =="S")
        $leave_type = "Sick Leave";
    if($type =="C" || $type =="U")
        $leave_type = "Unpaid Leave";
    if($type =="D")
        $leave_type = "Absence";
    if($type =="H")
        $leave_type = "Manager Halfday Planning";
    
    return $leave_type;
}

function formateDate($_date){
    return substr($_date, 0, 4)."/".substr($_date, 4, 2)."/".substr($_date, 6, 2);
}


?>