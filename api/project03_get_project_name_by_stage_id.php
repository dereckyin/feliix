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
          $user_id = $decoded->data->id;
          //if(!$decoded->data->is_admin)
          //{
          //  http_response_code(401);
     
          //  echo json_encode(array("message" => "Access denied."));
          //  die();
          //}
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
            $stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);
         
            $sql = "SELECT project_id, project_name, pp.stage, pm.special, pc.category
            FROM project_stages ps 
            JOIN project_main pm ON ps.project_id = pm.id 
            LEFT JOIN project_stage pp ON ps.stage_id = pp.id  
            LEFT JOIN project_category pc  ON pm.catagory_id = pc.id
            where ps.id = " . $stage_id . " and pm.status <> -1 ";

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
