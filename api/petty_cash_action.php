<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : '');
$crud = (isset($_POST['crud']) ?  $_POST['crud'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$amount = (isset($_POST['amount']) ?  $_POST['amount'] : 0);

$info_account = (isset($_POST['info_account']) ?  $_POST['info_account'] : '');
$info_category = (isset($_POST['info_category']) ?  $_POST['info_category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$info_remark = (isset($_POST['info_remark']) ?  $_POST['info_remark'] : '');
$info_remark_other = (isset($_POST['info_remark_other']) ?  $_POST['info_remark_other'] : '');

$items_to_delete = (isset($_POST['items_to_delete']) ?  $_POST['items_to_delete'] : "[]");
$items_array = json_decode(stripslashes($items_to_delete),true);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/apply_for_leave.php';
include_once 'objects/leave.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        $uid = $user_id;
        // now you can apply
        if ($crud == "Send To OP" || $crud == "Send To MD") {

            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now(),
                  `info_account` =  :info_account,
                  `info_category` =  :info_category,
                  `info_sub_category` =  :info_sub_category,
                  `info_remark` =  :info_remark,
                  `info_remark_other` =  :info_remark_other
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
            $stmt->bindParam(':info_account', $info_account);
            $stmt->bindParam(':info_category', $info_category);
            if($info_category == 'Marketing' || $info_category == 'Office Needs' || $info_category == 'Others' || $info_category == 'Projects' || $info_category == 'Store' )
                $sub_category = trim($sub_category);
            else
                $sub_category = "";
            $stmt->bindParam(':info_sub_category', $sub_category);
            $stmt->bindParam(':info_remark', $info_remark);
            if($info_remark == 'Cash' || $info_remark == 'Check')
                $info_remark_other = "";
            else
                $info_remark_other = trim($info_remark_other);
            $stmt->bindParam(':info_remark_other', $info_remark_other);
        } elseif ($crud == "Liquidated") {
            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now(),
                  `amount_liquidated` =  :amount_liquidated,
                  `remark_liquidated` =  :remark_liquidated
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            $remark_liquidated = $remark;

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
            $stmt->bindParam(':amount_liquidated', $amount);
            $stmt->bindParam(':remark_liquidated', $remark_liquidated);

            $remark = '';
        } elseif ($crud == "Verifier Verified") {
            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now(),
                  `amount_verified` =  :amount_verified
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
            $stmt->bindParam(':amount_verified', $amount);
        } else {
            $query = "update apply_for_petty
                   SET
                  `status` =  :status,
                  `updated_at` = now()
                   where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', GetAction($crud));
        }
        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        // items to delete
        for ($i = 0; $i < count($items_array); $i++) {
            $query = "DELETE FROM gcp_storage_file
                WHERE
                    `id` = :_id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':_id', $items_array[$i]);
   
            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

        $batch_id = $id;
        $batch_type = $crud;

        $_pic_url = "";
        $_real_url = "";

        if(isset($_FILES['files']['name']))
        {
            try {
                $total = count($_FILES['files']['name']);
                // Loop through each file
                for ($i = 0; $i < $total; $i++) {

                    if (isset($_FILES['files']['name'][$i])) {
                        $image_name = $_FILES['files']['name'][$i];
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
                        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $valid_extensions)) {
                            //$upload_path = 'img/' . time() . '.' . $extension;

                            $storage = new StorageClient([
                                'projectId' => 'predictive-fx-284008',
                                'keyFilePath' => $conf::$gcp_key
                            ]);

                            $bucket = $storage->bucket('feliiximg');

                            $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                            if ($bucket->upload(
                                fopen($_FILES['files']['tmp_name'][$i], 'r'),
                                ['name' => $upload_name]
                            )) {
                                $query = "INSERT INTO gcp_storage_file
                                SET
                                    batch_id = :batch_id,
                                    batch_type = :batch_type,
                                    filename = :filename,
                                    gcp_name = :gcp_name,

                                    create_id = :create_id,
                                    created_at = now()";

                                // prepare the query
                                $stmt = $db->prepare($query);

                                // bind the values
                                $stmt->bindParam(':batch_id', $batch_id);
                                $stmt->bindParam(':batch_type', GetDesc($batch_type));
                                $stmt->bindParam(':filename', $image_name);
                                $stmt->bindParam(':gcp_name', $upload_name);

                                $stmt->bindParam(':create_id', $user_id);

                                try {
                                    // execute the query, also check if query was successful
                                    if ($stmt->execute()) {
                                        $last_id = $db->lastInsertId();
                                    } else {
                                        $arr = $stmt->errorInfo();
                                        error_log($arr[2]);
                                    }
                                } catch (Exception $e) {
                                    error_log($e->getMessage());
                                    $db->rollback();
                                    http_response_code(501);
                                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                                    die();
                                }


                                $message = 'Uploaded';
                                $code = 0;
                                $upload_id = $last_id;
                                $image = $image_name;

                                // for expense record
                                $_pic_url .= $upload_name . ",";
                                $_real_url .= $image_name . ",";

                            } else {
                                $message = 'There is an error while uploading file';
                            }
                        } else {
                            $message = 'Only Images or Office files allowed to upload';
                        }
                    }
                }
            } catch (Exception $e) {
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Error uploading, Please use laptop to upload again."));
                die();
            }
        }


        // for expense record
        if ($crud == "Releasing" || $crud == "Finish Releasing") {

            $_record = GetRecordDetail($id, $db);
            $_record_list = GetList($id, $db);
            $_record_attachments = GetAttachments($id, $db);
            $_record_release_attachments = GetReleaseAttachments($id, $db);

            $_account = 0;
            $_related_account = "";
            $_category = "";
            $_sub_category = "";
            $_details = "";

            $_request_no = "";
            $_project_name = "";

            $_payee = "";

            $_cash_in = 0;
            $_cash_out = 0;
            $_remarks = "";
            $_is_marked = 0;
            $_is_locked = 0;
            $_created_by = "SYSTEM";

            $_id = 0;

            if(sizeof($_record) > 0)
            {
                switch ($_record[0]["info_account"]) {
                    case "Office Petty Cash":
                        $_account = 1;
                        break;
                    case "Security Bank":
                        $_account = 2;
                        break;
                    case "Online Transactions":
                        $_account = 3;
                        break;
                }

                $_category = $_record[0]["info_category"];
                $_subcategory = $_record[0]["info_sub_category"];
                $_payee = $_record[0]["username"];
                $_request_no = $_record[0]["request_no"];
                $_project_name = $_record[0]["project_name"];
                $_id = $_record[0]["id"];
            }

            $_details = 'Expense Application Request No.: <a target="_blank" href="expense_application_report?id=' . $_id . '">' . $_request_no . '</a><br>';

            $_details = $_details . 'Project Name / Reason: ' . $_project_name . '<br>';
            
            foreach ($_record_list as &$list) {
                $_details .= "● " . $list["payee"] . ", " . $list["particulars"] . ", Price = " . $list["price"]*1 . ", Qty = " . $list["qty"]*1 . ", Amount = " . $list["price"] * $list["qty"] . "<br>";

                $_cash_out += $list["price"] * $list["qty"];
            }
            
            foreach($_record_attachments as &$list){
                $_pic_url .= $list["gcp_name"] . ",";
                $_real_url .= $list["filename"] . ",";
            }

            $query = "INSERT INTO price_record
                (`account`,`category`, `sub_category`, `related_account`, `details`, `gcp_url`, `pic_url`, `payee`, `paid_date`, `cash_in`, `cash_out`, `remarks`,`is_locked`,`is_enabled`,`is_marked`,`created_at`,`created_by`) 
                VALUES (:account,:category, :sub_category, :related_account, :details, :gcp_url, :pic_url, :payee, :paid_date, :cash_in, :cash_out, :remarks, :is_locked, 1,:is_marked, now(),:created_by) ";

            // prepare the query
            $stmt = $db->prepare($query);

            $remark_liquidated = $remark;

            // bind the values
            $stmt->bindParam(':account', $_account);
            $stmt->bindParam(':category', $_category);
            $stmt->bindParam(':sub_category', $_subcategory);
            $stmt->bindParam(':related_account', $_related_account);

            $stmt->bindParam(':details', rtrim($_details, "<br>"));
            $stmt->bindParam(':gcp_url', rtrim($_gcp_url, ","));
            $stmt->bindParam(':pic_url', rtrim($_pic_url, ","));
            $stmt->bindParam(':payee', $_payee);


            $stmt->bindParam(':paid_date', date("Y-m-d"));
            $stmt->bindParam(':cash_in', $_cash_in);
            $stmt->bindParam(':cash_out', $_cash_out);
            $stmt->bindParam(':remarks', $_remarks);


            $stmt->bindParam(':is_locked', $_is_locked);
            $stmt->bindParam(':is_marked', $_is_marked);
            $stmt->bindParam(':created_by', $_created_by);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        } 


        if ($crud == "Verifier Verified") {

            $_record = GetRecordDetail($id, $db);
            $_record_list = GetList($id, $db);
            $_record_attachments = GetAttachments($id, $db);
            $_record_release_attachments = GetReleaseAttachments($id, $db);
            $_record_liquidate_attachments = GetLiquidateAttachments($id, $db);

            $_account = 0;
            $_related_account = "";
            $_category = "";
            $_sub_category = "";
            $_details = "";

            $_request_no = "";
            $_project_name = "";

            $_payee = "";

            $_cash_in = 0;
            $_cash_out = 0;
            $_amount_verified = 0;
            $_remarks = "";
            $_is_marked = 0;
            $_is_locked = 0;
            $_created_by = "SYSTEM";

            $_id = 0;

            if(sizeof($_record) > 0)
            {
                switch ($_record[0]["info_account"]) {
                    case "Office Petty Cash":
                        $_account = 1;
                        break;
                    case "Security Bank":
                        $_account = 2;
                        break;
                    case "Online Transactions":
                        $_account = 3;
                        break;
                }

                $_category = $_record[0]["info_category"];
                $_subcategory = $_record[0]["info_sub_category"];
                $_payee = $_record[0]["username"];
                $_request_no = $_record[0]["request_no"];
                $_project_name = $_record[0]["project_name"];
                $_id = $_record[0]["id"];
                $_amount_verified = (int)$_record[0]["amount_verified"];
            }

            $_details_list = "";

            foreach ($_record_list as &$list) {
                $_details_list .= "● " . $list["payee"] . ", " . $list["particulars"] . ", Price = " . $list["price"]*1 . ", Qty = " . $list["qty"]*1 . ", Amount = " . $list["price"] * $list["qty"] . "<br>";

                $_cash_out += $list["price"] * $list["qty"];
            }

            $_details = 'Liquidation was verified. <br>';
            $_details = $_details . 'Total Amount Requested: ' . $_cash_out . '<br>';
            $_details = $_details . 'Actual Amount After Verification: ' . $_amount_verified . '<br><br>';

            $_details = $_details . 'Expense Application Request No.: <a target="_blank" href="expense_application_report?id=' . $_id . '">' . $_request_no . '</a><br>';

            $_details = $_details . 'Project Name / Reason: ' . $_project_name . '<br>';

            $_details = $_details . $_details_list;

            foreach($_record_liquidate_attachments as &$list){
                $_pic_url .= $list["gcp_name"] . ",";
                $_real_url .= $list["filename"] . ",";
            }
/*
            foreach($_record_release_attachments as &$list){
                $_pic_url .= $list["gcp_name"] . ",";
                $_real_url .= $list["filename"] . ",";
            }
*/
            $query = "INSERT INTO price_record
                (`account`,`category`, `sub_category`, `related_account`, `details`, `gcp_url`, `pic_url`, `payee`, `paid_date`, `cash_in`, `cash_out`, `remarks`,`is_locked`,`is_enabled`,`is_marked`,`created_at`,`created_by`) 
                VALUES (:account,:category, :sub_category, :related_account, :details, :gcp_url, :pic_url, :payee, :paid_date, :cash_in, :cash_out, :remarks, :is_locked, 1,:is_marked, now(),:created_by) ";

            // prepare the query
            $stmt = $db->prepare($query);

            $remark_liquidated = $remark;

            if($_cash_out >= $_amount_verified)
            {
                $_cash_in = $_cash_out - $_amount_verified;
                $_cash_out = 0;
            }
            else
            {
                $_cash_in = 0;
                $_cash_out = $_amount_verified - $_cash_out;
            }

            // bind the values
            $stmt->bindParam(':account', $_account);
            $stmt->bindParam(':category', $_category);
            $stmt->bindParam(':sub_category', $_subcategory);
            $stmt->bindParam(':related_account', $_related_account);

            $stmt->bindParam(':details', rtrim($_details, "<br>"));
            $stmt->bindParam(':gcp_url', rtrim($_gcp_url, ","));
            $stmt->bindParam(':pic_url', rtrim($_pic_url, ","));
            $stmt->bindParam(':payee', $_payee);


            $stmt->bindParam(':paid_date', date("Y-m-d"));
            $stmt->bindParam(':cash_in', $_cash_in);
            $stmt->bindParam(':cash_out', $_cash_out);
            $stmt->bindParam(':remarks', $_remarks);


            $stmt->bindParam(':is_locked', $_is_locked);
            $stmt->bindParam(':is_marked', $_is_marked);
            $stmt->bindParam(':created_by', $_created_by);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        } 

        // for reject approval
        if ($crud == "Send To MD") {
            // save history
            $query = "INSERT INTO petty_history
            SET
                `petty_id` = :petty_id,
                `actor` = :actor,
                `action` = :_action,
                `reason` = :remark,
                `status` = -1,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':petty_id', $id);
            $stmt->bindParam(':actor', $user_name);
            $stmt->bindParam(':_action', GetDesc("OP Approved"));
            $stmt->bindParam(':remark', $remark);


            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

        // save history
        $query = "INSERT INTO petty_history
        SET
            `petty_id` = :petty_id,
            `actor` = :actor,
            `action` = :_action,
            `reason` = :remark,
            `status` = 1,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':petty_id', $id);
        $stmt->bindParam(':actor', $user_name);
        $stmt->bindParam(':_action', GetDesc($crud));
        $stmt->bindParam(':remark', $remark);

        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }


        $db->commit();
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function &GetAction($loc)
{
    $location = "";
    switch ($loc) {
        case "Revise":
            $location = 1;
            break;
        case "Withdraw":
            $location = -1;
            break;
        case "Checking Reject":
            $location = 0;
            break;
        case "Send To OP":
            $location = 3;
            break;
        case "OP Send To MD":
            $location = 4;
            break;
        case "Send To MD":
            $location = 4;
            break;
        case "OP Review Reject To User":
            $location = 0;
            break;
        case "OP Review Reject To Checker":
            $location = 2;
            break;
        case "MD Review Reject To User":
            $location = 0;
            break;
        case "MD Review Reject To Checker":
            $location = 2;
            break;
        case "MD Send To Releaser":
            $location = 5;
            break;
        case "Releasing":
            $location = 6;
            break;
        case "Liquidated":
            $location = 8;
            break;
        case "Finish Releasing":
            $location = 9;
            break;
        case "Verifier Rejected":
            $location = 7;
            break;
        case "Verifier Verified":
            $location = 9;
            break;
        case "Void":
            $location = -2;
            break;
    }

    return $location;
}

function &GetDesc($loc)
{
    $location = $loc;
    switch ($loc) {
        case "Withdraw":
            $location = "Withdrew";
            break;
        case "Checking Reject":
            $location = "Checker Rejected";
            break;
        case "Send To OP":
            $location = "Checker Checked";
            break;
        case "Send To MD":
            $location = "Checker Checked";
            break;
        case "OP Send To MD":
            $location = "OP Approved";
            break;
        case "OP Review Reject To User":
            $location = "OP Rejected";
            break;
        case "OP Review Reject To Checker":
            $location = "OP Rejected";
            break;
        case "MD Review Reject To User":
            $location = "MD Rejected";
            break;
        case "MD Review Reject To Checker":
            $location = "MD Rejected";
            break;
        case "MD Send To Releaser":
            $location = "MD Approved";
            break;
        case "Releasing":
            $location = "Releaser Released";
            break;
        case "Void":
            $location = "Releaser Voided";
            break;
        case "Finish Releasing":
            $location = "Releaser Released";
            break;
    }

    return $location;
}

function GetRecordDetail($_id, $db)
{
    $sql = "SELECT ap.id, request_no, project_name, info_account, info_category, info_sub_category, u.username, amount_verified
            FROM apply_for_petty ap 
            LEFT JOIN user u ON ap.uid = u.id 
            where ap.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $_id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetReleaseAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Releaser Released'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetLiquidateAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Liquidated'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'petty'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetList($_id, $db)
{
    $sql = "select pm.id, sn, payee, particulars, price, qty, `status`
    from petty_list pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}