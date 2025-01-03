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

      $code = (isset($_GET['code']) ?  $_GET['code'] : "");


      $database = new Database();
      $db = $database->getConnection();

      $code = trim($code);

      switch ($method) {
          case 'GET':
            $merged_results = array();

            if($code == '')
            {
              echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
              break;
            }

            $sql = "SELECT distinct code  FROM product_category p  WHERE  (p.STATUS <> -1 or (p.status = -1 and (select count(*) from product_replacement pr where pr.product_id = p.id) > 0)) and p.code like ? and sub_category <> '10020000' order by code limit 10";

            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->bindValue(1, $code . "%", PDO::PARAM_STR);
          
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row["code"];
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }



?>
