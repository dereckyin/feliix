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
          if(!$decoded->data->is_admin)
          {
            http_response_code(401);
     
            echo json_encode(array("message" => "Access denied."));
            die();
          }

          $user_id = $decoded->data->id;
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
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
            $id = (isset($_GET['id']) ?  $_GET['id'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $apartment_id = (isset($_GET['apartment_id']) ? $_GET['apartment_id'] : "");

            $sql = "SELECT 0 as is_checked, user.id, user.id user_id, user.username, user.email, user.status,  COALESCE(department, '') department, apartment_id, title_id, COALESCE(title, '') title, 
                        COALESCE(eds.id, 0) data_id,
                        COALESCE(user.first_name , '') first_name,
                        COALESCE(user.middle_name , '') middle_name,
                        COALESCE(user.surname , '') surname,
                        COALESCE(eds.emp_number , '') emp_number,
                        COALESCE(eds.date_hired , '') date_hired,
                        COALESCE(eds.regular_hired , '') regular_hired,
                        COALESCE(eds.emp_status , '') emp_status,
                        COALESCE(eds.company , '') company,
                        COALESCE(eds.emp_category , '') emp_category,
                        COALESCE(eds.superior , '') superior,

                        COALESCE(eds.updated_at , '') updated_at,
                        '' updated_str
                    FROM user 
                    LEFT JOIN user_department ON user.apartment_id = user_department.id 
                    LEFT JOIN user_title ON user.title_id = user_title.id 
                    LEFT JOIN employee_basic_info eds ON user.id = eds.user_id and eds.status <> -1
                    where user.status <> -1 ".($id ? " and id=$id" : '') . ($apartment_id ? " and user.apartment_id=$apartment_id" : '');

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY username ";

            if(!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if(false === $size) {
                    $size = 10;
                }

                $offset = ($page - 1) * $size;

                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }

            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->execute();


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }



?>
