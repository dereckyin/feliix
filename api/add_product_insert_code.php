<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$tags = (isset($_POST['tags']) ?  $_POST['tags'] : '');
$brand = (isset($_POST['brand']) ?  $_POST['brand'] : '');
$currency = (isset($_POST['currency']) ? $_POST['currency'] : 'NTD');
$code = (isset($_POST['code']) ?  $_POST['code'] : '');
$price_ntd = (isset($_POST['price_ntd']) ?  $_POST['price_ntd'] : '');
$price_ntd_change = (isset($_POST['price_ntd_change']) ?  $_POST['price_ntd_change'] : '');
$price = (isset($_POST['price']) ?  $_POST['price'] : '');
$price_change = (isset($_POST['price_change']) ?  $_POST['price_change'] : '');
$quoted_price = (isset($_POST['quoted_price']) ?  $_POST['quoted_price'] : '');
$quoted_price_change = (isset($_POST['quoted_price_change']) ?  $_POST['quoted_price_change'] : '');
$moq = (isset($_POST['moq']) ?  $_POST['moq'] : '');
$description = (isset($_POST['description']) ?  $_POST['description'] : '');
$notes = (isset($_POST['notes']) ? $_POST['notes'] : '');
$related_product = (isset($_POST['related_product']) ? $_POST['related_product'] : '');
$replacement_product = (isset($_POST['replacement_product']) ? $_POST['replacement_product'] : '');
$replacement_json = (isset($_POST['replacement_json']) ? $_POST['replacement_json'] : []);
$replacement_array = json_decode($replacement_json, true);

$out = (isset($_POST['out'])) ? $_POST['out'] : '';

$accessory_mode = (isset($_POST['accessory_mode']) ? $_POST['accessory_mode'] : 0);
$variation_mode = (isset($_POST['variation_mode']) ? $_POST['variation_mode'] : 0);

$attributes = (isset($_POST['attributes']) ?  $_POST['attributes'] : '[]');
$accessory = (isset($_POST['accessory']) ?  $_POST['accessory'] : '[]');
$accessory_array = json_decode($accessory, true);
$variation = (isset($_POST['variation']) ?  $_POST['variation'] : '[]');
$variation_array = json_decode($variation, true);

$p1_code = (isset($_POST['p1_code']) ?  $_POST['p1_code'] : '');
$p1_qty = (isset($_POST['p1_qty']) ?  $_POST['p1_qty'] : '');
$p1_id = (isset($_POST['p1_id']) ?  $_POST['p1_id'] : '');

$p2_code = (isset($_POST['p2_code']) ?  $_POST['p2_code'] : '');
$p2_qty = (isset($_POST['p2_qty']) ?  $_POST['p2_qty'] : '');
$p2_id = (isset($_POST['p2_id']) ?  $_POST['p2_id'] : '');

$p3_code = (isset($_POST['p3_code']) ?  $_POST['p3_code'] : '');
$p3_qty = (isset($_POST['p3_qty']) ?  $_POST['p3_qty'] : '');
$p3_id = (isset($_POST['p3_id']) ?  $_POST['p3_id'] : '');

$brand_handler  = (isset($_POST['brand_handler']) ?  $_POST['brand_handler'] : '');

if($p3_code == '')
{
    $p3_qty = '';
    $p3_id = '';
}

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';

include_once 'config/conf.php';
require_once '../vendor/autoload.php';

include_once 'mail.php';


$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        // 去除非商品資料
        $related_product = valid_id($related_product, $db);

        $srp_max = "";
        $srp_min = "";
        $qp_max = "";
        $qp_min = "";

        $smax = 0;
        $smin = 0;
        $qmax = 0;
        $qmin = 0;

        $p1_data = [];
        $p2_data = [];
        $p3_data = [];

        // now you can apply
        $uid = $user_id;

        if($p1_code != '')
        {
            $p1_data = get_product_info_from_code($p1_code, $db);
        }

        if($p2_code != '')
        {
            $p2_data = get_product_info_from_code($p2_code, $db);
        }

        if($p3_code != '')
        {
            $p3_data = get_product_info_from_code($p3_code, $db);
        }

        if(count($p1_data) > 0)
        {
            $smax += ($p1_data['srp_max'] + 0) * ($p1_qty + 0);
            $smin += ($p1_data['srp_min'] + 0) * ($p1_qty + 0);

            $qmax += ($p1_data['qp_max'] + 0) * ($p1_qty + 0);
            $qmin += ($p1_data['qp_min'] + 0) * ($p1_qty + 0);
        }

        if(count($p2_data) > 0)
        {
            $smax += ($p2_data['srp_max'] + 0) * ($p2_qty + 0);
            $smin += ($p2_data['srp_min'] + 0) * ($p2_qty + 0);

            $qmax += ($p2_data['qp_max'] + 0) * ($p2_qty + 0);
            $qmin += ($p2_data['qp_min'] + 0) * ($p2_qty + 0);
        }

        if(count($p3_data) > 0)
        {
            $smax += ($p3_data['srp_max'] + 0) * ($p3_qty + 0);
            $smin += ($p3_data['srp_min'] + 0) * ($p3_qty + 0);

            $qmax += ($p3_data['qp_max'] + 0) * ($p3_qty + 0);
            $qmin += ($p3_data['qp_min'] + 0) * ($p3_qty + 0);
        }

        if($smax != 0)
            $srp_max = $smax;
        if($smin != 0)
            $srp_min = $smin;
        if($qmax != 0)
            $qp_max = $qmax;
        if($qmin != 0)  
            $qp_min = $qmin;
    
        $query = "INSERT INTO product_category
        SET
            `category` = :category,
            `sub_category` = :sub_category,
            `tags` = :tags,
            `brand` = :brand,
            `currency` = :currency,
            `code` = :code, ";
            if($price_ntd != ''  && !is_null($price_ntd))
            {
                $query .= "`price_ntd` = :price_ntd, ";

                
            }

            if($price != ''  && !is_null($price))
            {
                $query .= "`price` = :price, ";

                $srp_max = $price;
                $srp_min = $price;
            }

            if($quoted_price != ''  && !is_null($quoted_price))
            {
                $query .= "`quoted_price` = :quoted_price, ";

                $qp_max = $quoted_price;
                $qp_min = $quoted_price;
            }


    $query .= "`price_ntd_change` = :price_ntd_change, ";

    $query .= "`price_change` = :price_change, ";

    $query .= "`quoted_price_change` = :quoted_price_change, ";

        $query .= "
            `moq` = :moq,
            `description` = :description,
            `out` = :out,
            `notes` = :notes,
            `accessory_mode` = :accessory_mode,
            `variation_mode` = :variation_mode,
            `attributes` = :attributes,
            `status` = 1,
            `create_id` = :create_id,

            `p1_code` = :p1_code,
            `p1_qty` = :p1_qty,
            `p1_id` = :p1_id,

            `p2_code` = :p2_code,
            `p2_qty` = :p2_qty,
            `p2_id` = :p2_id,

            `p3_code` = :p3_code,
            `p3_qty` = :p3_qty,
            `p3_id` = :p3_id,

            `brand_handler` = :brand_handler,

            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':sub_category', $sub_category);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':currency', $currency);
        $stmt->bindParam(':code', $code);
        if($price_ntd != '' && !is_null($price_ntd))
        {
            $stmt->bindParam(':price_ntd', $price_ntd);
        }

        if($price != '' && !is_null($price))
        {
            $stmt->bindParam(':price', $price);
        }

        if($quoted_price != '' && !is_null($quoted_price))
        {
            $stmt->bindParam(':quoted_price', $quoted_price);
        }

        $price_ntd_change = formate_date($price_ntd_change);
        $price_change = formate_date($price_change);
        $quoted_price_change = formate_date($quoted_price_change);

        $stmt->bindParam(':price_ntd_change', $price_ntd_change);
        $stmt->bindParam(':price_change', $price_change);
        $stmt->bindParam(':quoted_price_change', $quoted_price_change);

        $stmt->bindParam(':moq', $moq);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':out', $out);
        // $stmt->bindParam(':related_product', $related_product);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':accessory_mode', $accessory_mode);
        $stmt->bindParam(':variation_mode', $variation_mode);
        $stmt->bindParam(':attributes', $attributes);

        $stmt->bindParam(':p1_code', $p1_code);
        $stmt->bindParam(':p1_qty', $p1_qty);
        $stmt->bindParam(':p1_id', $p1_id);

        $stmt->bindParam(':p2_code', $p2_code);
        $stmt->bindParam(':p2_qty', $p2_qty);
        $stmt->bindParam(':p2_id', $p2_id);

        $stmt->bindParam(':p3_code', $p3_code);
        $stmt->bindParam(':p3_qty', $p3_qty);
        $stmt->bindParam(':p3_id', $p3_id);

        $stmt->bindParam(':brand_handler', $brand_handler);

        $stmt->bindParam(':create_id', $user_id);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        // update other related_product
        update_relative_ids($related_product, $last_id, $code, $db);
        insert_relative_product($last_id, $related_product, $db);

        // update other related_product
        if(count($replacement_array) > 0)
            update_replacement_ids_in_product_category($last_id, $replacement_array, $db);

        $batch_id = $last_id;
        $batch_type = "product_photo";

        if (array_key_exists('photo1', $_FILES))
        {
            $update_name = SaveImage('photo1', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'photo1', $batch_id, $db);
        }
        if (array_key_exists('photo2', $_FILES))
        {
            $update_name = SaveImage('photo2', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'photo2', $batch_id, $db);
        }
        if (array_key_exists('photo3', $_FILES))
        {
            $update_name = SaveImage('photo3', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'photo3', $batch_id, $db);
        }


        
        $batch_id = $last_id;
        $batch_type = "product_ics";

        try {
            $total = count($_FILES['file_ics']);
            // Loop through each file
            for( $i=0 ; $i < $total ; $i++ ) {

                if(isset($_FILES['file_ics']['name'][$i]))
                {
                    $image_name = $_FILES['file_ics']['name'][$i];
                    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","ies");
                    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                    if (in_array(strtolower($extension), $valid_extensions)) 
                    {
                        //$upload_path = 'img/' . time() . '.' . $extension;

                        $storage = new StorageClient([
                            'projectId' => 'predictive-fx-284008',
                            'keyFilePath' => $conf::$gcp_key
                        ]);

                        $bucket = $storage->bucket('feliiximg');

                        $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                        $file_size = filesize($_FILES['file_ics']['tmp_name'][$i]);
                        $size = 0;

                        $obj = $bucket->upload(
                            fopen($_FILES['file_ics']['tmp_name'][$i], 'r'),
                            ['name' => $upload_name]);

                        $info = $obj->info();
                        $size = $info['size'];

                        if($size == $file_size && $file_size != 0 && $size != 0)
                        {
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
                            $stmt->bindParam(':batch_type', $batch_type);
                            $stmt->bindParam(':filename', $image_name);
                            $stmt->bindParam(':gcp_name', $upload_name);
                
                            $stmt->bindParam(':create_id', $user_id);

                            try {
                                // execute the query, also check if query was successful
                                if ($stmt->execute()) {
                                    $last_id = $db->lastInsertId();
                                }
                                else
                                {
                                    $arr = $stmt->errorInfo();
                                    error_log($arr[2]);
                                }
                            }
                            catch (Exception $e)
                            {
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
                        }
                        else
                        {
                            $message = 'There is an error while uploading file';
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                            die();
                            
                        }
                    }
                    else
                    {
                        $message = 'Only Images or Office files allowed to upload';
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                        die();
                    }
                }

            }
        } catch (Exception $e) {
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
            die();
        }

        $batch_type = "product_skp";

        try {
            $total = count($_FILES['file_skp']);
            // Loop through each file
            for( $i=0 ; $i < $total ; $i++ ) {

                if(isset($_FILES['file_skp']['name'][$i]))
                {
                    $image_name = $_FILES['file_skp']['name'][$i];
                    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
                    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                    if (in_array(strtolower($extension), $valid_extensions)) 
                    {
                        //$upload_path = 'img/' . time() . '.' . $extension;

                        $storage = new StorageClient([
                            'projectId' => 'predictive-fx-284008',
                            'keyFilePath' => $conf::$gcp_key
                        ]);

                        $bucket = $storage->bucket('feliiximg');

                        $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                        $file_size = filesize($_FILES['file_skp']['tmp_name'][$i]);
                        $size = 0;

                        $obj = $bucket->upload(
                            fopen($_FILES['file_skp']['tmp_name'][$i], 'r'),
                            ['name' => $upload_name]);

                        $info = $obj->info();
                        $size = $info['size'];

                        if($size == $file_size && $file_size != 0 && $size != 0)
                        {
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
                            $stmt->bindParam(':batch_type', $batch_type);
                            $stmt->bindParam(':filename', $image_name);
                            $stmt->bindParam(':gcp_name', $upload_name);
                
                            $stmt->bindParam(':create_id', $user_id);

                            try {
                                // execute the query, also check if query was successful
                                if ($stmt->execute()) {
                                    $last_id = $db->lastInsertId();
                                }
                                else
                                {
                                    $arr = $stmt->errorInfo();
                                    error_log($arr[2]);
                                }
                            }
                            catch (Exception $e)
                            {
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
                        }
                        else
                        {
                            $message = 'There is an error while uploading file';
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                            die();
                            
                        }
                    }
                    else
                    {
                        $message = 'Only Images or Office files allowed to upload';
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                        die();
                    }
                }

            }
        } catch (Exception $e) {
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
            die();
        }

        
        $batch_type = "product_manual";

        try {
            $total = count($_FILES['file_manual']);
            // Loop through each file
            for( $i=0 ; $i < $total ; $i++ ) {

                if(isset($_FILES['file_manual']['name'][$i]))
                {
                    $image_name = $_FILES['file_manual']['name'][$i];
                    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
                    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                    if (in_array(strtolower($extension), $valid_extensions)) 
                    {
                        //$upload_path = 'img/' . time() . '.' . $extension;

                        $storage = new StorageClient([
                            'projectId' => 'predictive-fx-284008',
                            'keyFilePath' => $conf::$gcp_key
                        ]);

                        $bucket = $storage->bucket('feliiximg');

                        $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                        $file_size = filesize($_FILES['file_manual']['tmp_name'][$i]);
                        $size = 0;

                        $obj = $bucket->upload(
                            fopen($_FILES['file_manual']['tmp_name'][$i], 'r'),
                            ['name' => $upload_name]);

                        $info = $obj->info();
                        $size = $info['size'];

                        if($size == $file_size && $file_size != 0 && $size != 0)
                        {
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
                            $stmt->bindParam(':batch_type', $batch_type);
                            $stmt->bindParam(':filename', $image_name);
                            $stmt->bindParam(':gcp_name', $upload_name);
                
                            $stmt->bindParam(':create_id', $user_id);

                            try {
                                // execute the query, also check if query was successful
                                if ($stmt->execute()) {
                                    $last_id = $db->lastInsertId();
                                }
                                else
                                {
                                    $arr = $stmt->errorInfo();
                                    error_log($arr[2]);
                                }
                            }
                            catch (Exception $e)
                            {
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
                        }
                        else
                        {
                            $message = 'There is an error while uploading file';
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                            die();
                            
                        }
                    }
                    else
                    {
                        $message = 'Only Images or Office files allowed to upload';
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                        die();
                    }
                }

            }
        } catch (Exception $e) {
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
            die();
        }



        $product_id = $batch_id;

        // accessory
        for ($i = 0; $i < count($accessory_array); $i++) {
            $category = $accessory_array[$i]['category'];
            $cat_id = $accessory_array[$i]['cat_id'];
            $detail = $accessory_array[$i]['detail'];

            for($j=0; $j < count($detail); $j++)
            {
                $query = "INSERT INTO accessory
                SET
                    `category_id` = :category_id,
                    `product_id` = :product_id,
                    `accessory_type` = :accessory_type,
                    `code` = :code,
                    `accessory_name` = :accessory_name, ";

                    if($detail[$j]['price_ntd'] != '' && !is_null($detail[$j]['price_ntd']))
                    {
                        $query .= "`price_ntd` = :price_ntd, ";
                    }
                    if($detail[$j]['price'] != '' && !is_null($detail[$j]['price']))
                    {
                        $query .= "`price` = :price, ";
                    }
                 
                    $query .= "
                    `enabled` = :enabled,
                   
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";
    
                // prepare the query
                $stmt = $db->prepare($query);
    
                // bind the values
                $stmt->bindParam(':category_id', $cat_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':accessory_type', $category);
                $stmt->bindParam(':code', $detail[$j]['code']);
                $stmt->bindParam(':accessory_name', $detail[$j]['name']);
                if($detail[$j]['price_ntd'] != '' && !is_null($detail[$j]['price_ntd']))
                {
                    $stmt->bindParam(':price_ntd', $detail[$j]['price_ntd']);
                }
                if($detail[$j]['price'] != '' && !is_null($detail[$j]['price']))
                {
                    $stmt->bindParam(':price', $detail[$j]['price']);
                }
         
                $stmt->bindParam(':enabled', $detail[$j]['enabled']);
                $stmt->bindParam(':create_id', $uid);
    
                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $last_id = $db->lastInsertId();
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }

                $batch_id = $last_id;
                $batch_type = "accessory_photo";

                $key = "accessory_" . $cat_id . "_" . $detail[$j]['id'];
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameAccessory($update_name, $batch_id, $db);
                }
            }
            
        }

        // variation
        for ($i = 0; $i < count($variation_array); $i++) {
            $id = $variation_array[$i]['id'];
            // $code = $variation_array[$i]['code'];
            $k1 = $variation_array[$i]['k1'];
            $k2 = $variation_array[$i]['k2'];
            $k3 = $variation_array[$i]['k3'];
            $k4  = $variation_array[$i]['k4'];
            $v1 = $variation_array[$i]['v1'];
            $v2 = $variation_array[$i]['v2'];
            $v3 = $variation_array[$i]['v3'];
            $v4 = $variation_array[$i]['v4'];
            $price = $variation_array[$i]['price'];
            $price_change = $variation_array[$i]['price_change'];
            $quoted_price = $variation_array[$i]['quoted_price'];
            $quoted_price_change = $variation_array[$i]['quoted_price_change'];
            $price_ntd = $variation_array[$i]['price_ntd'];
            $price_ntd_change = $variation_array[$i]['price_ntd_change'];
            $enabled = $variation_array[$i]['status'];
            $category_id = '';

            $st_variation = $k1 . '=>' . $v1;
            $rd_variation = $k2 . '=>' . $v2;
            $th_variation = $k3 . '=>' . $v3;
            $ft_variation = $k4 . '=>' . $v4;
    
            $query = "INSERT INTO product
            SET
                `category_id` = :category_id,
                `product_id` = :product_id,
                `1st_variation` = :1st_variation,
                `2rd_variation` = :2rd_variation,
                `3th_variation` = :3th_variation,
                `4th_variation` = :4th_variation,
                `code` = :code, ";
                if($price_ntd != '' && !is_null($price_ntd))
                {
                    $query .= "`price_ntd` = :price_ntd, ";
                }

                if($price != '' && !is_null($price))
                {
                    $query .= "`price` = :price, ";

                    if(parseFloat($price) > parseFloat($srp_max))
                        $srp_max = $price;
                    
                    if(parseFloat($price) < parseFloat($srp_min))
                        $srp_min = $price;
                    
                    if(parseFloat($price) != 0 && $srp_min == '')
                        $srp_min = $price;
                }

                if($quoted_price != '' && !is_null($quoted_price))
                {
                    $query .= "`quoted_price` = :quoted_price, ";

                    if(parseFloat($quoted_price) > parseFloat($qp_max))
                        $qp_max = $quoted_price;
                    
                    if(parseFloat($quoted_price) < parseFloat($qp_min))
                        $qp_min = $quoted_price;

                    if(parseFloat($quoted_price) != 0 && $qp_min == '')
                        $qp_min = $quoted_price;
                }

            $query .= "`price_ntd_change` = :price_ntd_change, ";
            $query .= "`price_change` = :price_change, ";
            $query .= "`quoted_price_change` = :quoted_price_change, ";

            $query .= "
                `enabled` = :enabled,    
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':1st_variation', $st_variation);
            $stmt->bindParam(':2rd_variation', $rd_variation);
            $stmt->bindParam(':3th_variation', $th_variation);
            $stmt->bindParam(':4th_variation', $ft_variation);
            $stmt->bindParam(':code', $code);
            if($price_ntd != '' && !is_null($price_ntd))
            {
                $stmt->bindParam(':price_ntd', $price_ntd);
            }
            if($price != '' && !is_null($price))
            {
                $stmt->bindParam(':price', $price);
            }
            if($quoted_price != '' && !is_null($quoted_price))
            {
                $stmt->bindParam(':quoted_price', $quoted_price);
            }

            $price_ntd_change = formate_date($price_ntd_change);
            $price_change = formate_date($price_change);
            $quoted_price_change = formate_date($quoted_price_change);

            $stmt->bindParam(':price_ntd_change', $price_ntd_change);
            $stmt->bindParam(':price_change', $price_change);
            $stmt->bindParam(':quoted_price_change', $quoted_price_change);

            $stmt->bindParam(':enabled', $enabled);
            $stmt->bindParam(':create_id', $uid);

            $last_id = 0;
            // execute the query, also check if query was successful
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $last_id = $db->lastInsertId();
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            $batch_id = $last_id;
            $batch_type = "variation_photo";

            $key = "variation_" . $id;
            if (array_key_exists($key, $_FILES))
            {
                $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                if($update_name != "")
                    UpdateImageNameVariation($update_name, $batch_id, $db);
            }
        }

        
        // update srp_max, srp_min, qp_max, qp_min to product_category
        $query = "UPDATE product_category SET "; 

        if($srp_max != '')
        {
            $query .= "`srp_max` = :srp_max, ";
        }
        else
        {
            $query .= "`srp_max` = null, ";
        }
        
        if($srp_min != '')
        {
            $query .= "`srp_min` = :srp_min, ";
        }
        else
        {
            $query .= "`srp_min` = null, ";
        }

        if($qp_max != '')
        {
            $query .= "`qp_max` = :qp_max, ";
        }
        else
        {
            $query .= "`qp_max` = null, ";
        }

        if($qp_min != '')
        {
            $query .= "`qp_min` = :qp_min, ";
        }
        else
        {
            $query .= "`qp_min` = null, ";
        }

        $query .= "`updated_at` = now() WHERE `id` = :id";
        
           
        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        if($srp_max != '' && !is_null($srp_max))
        {
            $stmt->bindParam(':srp_max', $srp_max);
        }

        if($srp_min != '' && !is_null($srp_min))
        {
            $stmt->bindParam(':srp_min', $srp_min);
        }

        if($qp_max != '' && !is_null($qp_max))
        {
            $stmt->bindParam(':qp_max', $qp_max);
        }

        if($qp_min != '' && !is_null($qp_min))
        {
            $stmt->bindParam(':qp_min', $qp_min);
        }

        $stmt->bindParam(':id', $product_id);

        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
              
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
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

        if($user_id != 1 && $user_id != 2)
            EmailNotify($product_id, $db);

        update_product_category_tags_index($product_id, $db);
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") ));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

function EmailNotify($id, $db){
    $_record = GetProductCategory($id, $db);
    if(count($_record) > 0 && $_record[0]['category'] != "20000000")
        product_notify("add", $_record[0]);
}

function GetProductCategory($id, $db){
    $query = "SELECT p.id, p.category, p.sub_category, p.brand, p.code, p.photo1, p.created_at, p.create_id, p.updated_at, p.updated_id, p.attributes, c.username creator, u.username updator  FROM product_category  p left join user c on p.create_id = c.id left join user u on p.updated_id = u.id  WHERE p.id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $merged_results = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $product = GetProduct($row["id"], $db);

        $variation1_value = [];
        $variation2_value = [];
        $variation3_value = [];
        $variation4_value = [];

        $variation1_text = "";
        $variation2_text = "";
        $variation3_text = "";
        $variation4_text = "";

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

        $special_info_json = json_decode($row["attributes"]);

        $sub_category = $row["sub_category"];

        $special_information = GetSpecialInfomation($sub_category, $db, $special_info_json);
        $accessory_information = GetAccessoryInfomation($sub_category, $db, $id);

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
                    $attribute_list[] = array("category" => $special_info_json[$i]->category,
                                    "value" => $value,
                                );
                }
            }
        }

        
        if($variation1 == "custom" && $variation1_custom != "1st Variation")
        {
            $attribute_list[] = array("category" => $variation1_text,
                                   "value" => $variation1_value,
                                );
        }

        if($variation2 == "custom" && $variation2_custom != "2nd Variation")
        {
            $attribute_list[] = array("category" => $variation2_text,
                                   "value" => $variation2_value,
                                );
        }

        if($variation3 == "custom" && $variation3_custom != "3rd Variation")
        {
            $attribute_list[] = array("category" => $variation3_text,
                                   "value" => $variation3_value,
                                );
        }

        if($variation4 == "custom" && $variation4_custom != "4th Variation")
        {
            $attribute_list[] = array("category" => $variation4_text,
                                   "value" => $variation4_value,
                                );
        }


        $merged_results[] = array( "id" => $row["id"],
                            "category" => $row["category"],
                            "tags" => explode(',', $row["tags"]),
                            "brand" => $row["brand"],
                            "code" => $row["code"],
                        
                            "photo1" => $row["photo1"],
                
                            "created_at" => $row["created_at"],
                            "create_id" => $row["create_id"],
                            "updated_at" => $row["updated_at"],
                            "updated_id" => $row["updated_id"],
                            "creator" => $row["creator"],
                            "updator" => $row["updator"],
                           
                            "attribute_list" => $attribute_list,
                           

        );
    }

    return $merged_results;
}

function GetKey($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[0]) ? $obj[0] : "";
}

function GetValue($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[1]) ? $obj[1] : "";
}

function GetProduct($id, $db){
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
        $checked = '';
        $code = $row['code'];
        $price = $row['price'];
        $price_ntd = $row['price_ntd'];
        $price_org = $row['price'];
        $price_ntd_org = $row['price_ntd'];
        $price_change = $row['price_change'];
        $price_ntd_change = $row['price_ntd_change'];
        $status = $row['enabled'];
        $photo = trim($row['photo']);
        $enabled = $row['enabled'];
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'];

        $merged_results[] = array(  "id" => $id, 
                                    "k1" => $k1, 
                                    "k2" => $k2, 
                                    "k3" => $k3, 
                                    "k4" => $k4,
                                    "v1" => $v1, 
                                    "v2" => $v2, 
                                    "v3" => $v3, 
                                    "v4" => $v4,
                                    "checked" => $checked, 
                                    "code" => $code, 
                                    "price" => $price, 
                                    "price_ntd" => $price_ntd, 
                                    "price_org" => $price_org, 
                                    "price_ntd_org" => $price_ntd_org, 
                                    "price_change" => $price_change, 
                                    "price_ntd_change" => $price_ntd_change, 
                                    "status" => $status, 
                                    "url" => $url, 
                                    "photo" => $photo, 
                                    "enabled" => $enabled,

                                    "quoted_price" => $quoted_price, 
                                    "quoted_price_org" => $quoted_price, 
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10), 
                                   
                                    "file" => array( "value" => ''),
                                   
            );
    }
    
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

function GetAccessoryInfomation($cat_id, $db, $product_id){
    $sql = "SELECT * FROM accessory_category_attribute WHERE LEVEL = 3 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";

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

        $lv2[] = GetAccessoryInfomationDetail($cat_id, $product_id, $db);
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

function GetAccessoryInfomationDetail($cat_id, $product_id, $db){

    $sql = "SELECT id, code, accessory_name `name`, price, price_ntd, category_id cat_id, photo, CONCAT('https://storage.googleapis.com/feliiximg/', photo) url FROM accessory WHERE product_id = ". $product_id . " and category_id = '" . $cat_id . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY id ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $photo = trim($row['photo']);
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

        $merged_results[] = array(  "id" => $row['id'], 
                                    "code" => $row['code'],
                                    "name" => $row['name'],
                                    "price" => $row['price'],
                                    "price_ntd" => $row['price_ntd'],
                                    "cat_id" => $row['cat_id'],
                                    "url" => $url,
                                    "photo" => $photo,
                                    "file" => array( "value" => ''),
                                   
            );
    }

    return $merged_results;

}

function parseFloat($value) {
    $ret_value = 0.0;

    // parse $value as float
    if (is_numeric($value)) {
        $ret_value = floatval($value);
    } else {
        // parse $value as string
        $value = str_replace(',', '.', $value);
        $value = str_replace(' ', '', $value);
        $value = str_replace(' ', '', $value); // this is a non-breaking space (0xC2A0 hex)
        $value = preg_replace('/[^0-9\.]/', '', $value);

        if (is_numeric($value)) {
            $ret_value = floatval($value);
        }
    }

    return $ret_value;
    //return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,3}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $value));
}

function UpdateImageName($upload_name, $type, $batch_id, $db){
    
    $query = "update product_category
    SET " . $type . " = :gcp_name where id=:id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $batch_id);

    $stmt->bindParam(':gcp_name', $upload_name);


    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();
        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    }
    catch (Exception $e)
    {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function UpdateImageNameAccessory($upload_name, $batch_id, $db){
    
    $query = "update accessory
    SET photo = :gcp_name where id=:id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $batch_id);

    $stmt->bindParam(':gcp_name', $upload_name);


    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();
        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    }
    catch (Exception $e)
    {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function UpdateImageNameVariation($upload_name, $batch_id, $db){
    
    $query = "update product
    SET photo = :gcp_name where id=:id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':id', $batch_id);

    $stmt->bindParam(':gcp_name', $upload_name);


    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();
        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }
    }
    catch (Exception $e)
    {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), $valid_extensions)) 
            {
                //$upload_path = 'img/' . time() . '.' . $extension;

                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);

                $bucket = $storage->bucket('feliiximg');

                $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                $file_size = filesize($_FILES[$type]['tmp_name']);
                $size = 0;

                $obj = $bucket->upload(
                    fopen($_FILES[$type]['tmp_name'], 'r'),
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size == $file_size && $file_size != 0 && $size != 0)
                {
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
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
        
                    $stmt->bindParam(':create_id', $user_id);

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                        $db->rollback();
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }

                    return $upload_name;
                }
                else
                {
                    $message = 'There is an error while uploading file';
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                    die();
                    
                }
            }
            else
            {
                $message = 'Only Images or Office files allowed to upload';
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                die();
            }
        }

        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
        die();
    }
}

function formate_date($date)
{
    $v_date = trim($date);
    
    if(valid_date($v_date) == 1)
        return $v_date;
    else
        return null;
}

function valid_date($date) {
    return (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date));
}

function valid_id($ids, $db) {
    $id_array = explode(',', $ids);
    $code_array = [];

    for($i = 0; $i < count($id_array); $i++)
    {
        $query = "SELECT code FROM product_category WHERE code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $id_array[$i]);

        try {
            if ($stmt->execute()) {
         
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $code_array[] = $row['code'];
        }
    }
    
    return $code_array;
}

// update_relative_ids 
function update_relative_ids($id_array, $me_id, $me_code, $db) {

    if(count($id_array) == 0)
        return;
  
    $clear_array = [];

    for($i = 0; $i < count($id_array); $i++)
    {
        $query = "SELECT id, code FROM product_category WHERE code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $id_array[$i]);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_array = get_relatived_id_from_other_product($row['id'], $db);
            array_push($_array, $me_code);
            // remove duplicate from array
            $related_product = array_unique($_array);
            // remove empty from array
            $related_product = array_filter($related_product, "not_empty");
            // order array
            sort($related_product);
    
            update_relative_ids_in_product_category($row['id'], $related_product, $db);

            array_push($clear_array, $row['code']);

        }
    }

}

function update_replacement_ids_in_product_category($id, $id_array, $db) {

    $query = "DELETE FROM product_replacement WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // loop to insert 
    for($i = 0; $i < count($id_array); $i++)
    {
        $query = "INSERT INTO product_replacement SET product_id = :id, replacement_id = :replacement_id, code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':replacement_id', $id_array[$i]['id']);
        $stmt->bindParam(':code', $id_array[$i]['code']);
        $stmt->execute();
    }
}

function update_relative_ids_in_product_category($id, $related_product, $db) {
    $query = "DELETE FROM product_related WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // loop to insert 
    for($i = 0; $i < count($related_product); $i++)
    {
        $query = "INSERT INTO product_related SET product_id = :id, code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':code', $related_product[$i]);
        $stmt->execute();
    }
}

function not_empty($array) 
{ 
    // returns if the input integer is even 
    if($array!="") 
       return TRUE; 
    else 
       return FALSE;  
} 

function get_relatived_id_from_other_product($id, $db) {
    $query = "SELECT code FROM product_related WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $new_ids = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $new_ids[] = $row['code'];
    }

    return $new_ids;
}

function insert_relative_product($id, $related_product, $db) {
      // loop to insert 
    for($i = 0; $i < count($related_product); $i++)
    {
        $query = "INSERT INTO product_related SET product_id = :id, code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':code', $related_product[$i]);
        $stmt->execute();
    }
}

function get_product_info_from_code($code, $db) {
    $query = "SELECT id, code, srp_max, srp_min, qp_max, qp_min FROM product_category WHERE code = :code and STATUS <> -1 limit 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':code', $code);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row;
    }

}

function update_product_category_tags_index($id, $db) {
    $sql = "SELECT id, tags, attributes, variation_mode FROM product_category where `status` <> -1";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $tags = explode(',', $row['tags']);
        $attributes = json_decode($row['attributes'], true);

        $sql = "insert into product_category_tags_index (pid, `type`, `key`, `value`) values (:product_category_id, 0, :tag, '')";
        $stmt2 = $db->prepare( $sql );

        foreach ($tags as $tag) {
            $stmt2->bindParam(':product_category_id', $id);
            $stmt2->bindParam(':tag', $tag);
            $stmt2->execute();

            if($stmt2->errorInfo()[0] != "00000") {
                echo $stmt2->errorInfo()[2];
            }
        }

        foreach ($attributes as $att) {
            $key = $att['category'];
            $value = $att['value'];
            if($value != "") {
                $sql = "insert into product_category_tags_index (pid, `type`, `key`, `value`) values (:product_category_id, 1, :key, :value)";
                $stmt2 = $db->prepare( $sql );
                $stmt2->bindParam(':product_category_id', $id);
                $stmt2->bindParam(':key', $key);
                $stmt2->bindParam(':value', $value);
                $stmt2->execute();

                if($stmt2->errorInfo()[0] != "00000") {
                    echo $stmt2->errorInfo()[2];
                }
            }
        }
    }
}