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


$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");

$pid = (isset($_GET['pid']) ?  $_GET['pid'] : 0);

$merged_results = array();

$query = "SELECT pm.id,
            Coalesce(pg.project_group, '')              project_group,
            Coalesce(pg.id, 0)                                  group_id,
            Coalesce(pc.category, '')              category,
            pc.id                                  category_id,
            pct.client_type,
            pct.id                                 client_type_id,
            pct.class_name                         pct_class,
            pp.priority,
            pp.id                                  priority_id,
            pp.class_name                          pp_class,
            pm.project_name,
            Coalesce(ps.project_status, '')        project_status,
            pm.estimate_close_prob,
            pm.designer,
            pm.`type`,
            pm.special,
            pm.scope,
            pm.scope_other,
            pm.office_location,
            pm.background_client,
            pm.background_project,
            pm.contractor,
            pm.send_mail,
            user.username,
            user.id                                uid,
            Date_format(pm.created_at, '%Y-%m-%d') created_at,
            Date_format(pm.updated_at, '%Y-%m-%d') updated_at,
            Coalesce((SELECT project_stage.stage
                    FROM   project_stages
                            LEFT JOIN project_stage
                                    ON project_stage.id = project_stages.stage_id
                    WHERE  project_stages.project_id = pm.id
                            AND project_stages.stages_status_id = 1
                    ORDER  BY `sequence` DESC
                    LIMIT  1), '')               stage,
            pm.location,
            pm.contactor,
            pm.contact_number,
            pm.client,
            pm.edit_reason,
            pic1.username                         pic1,
            pm.pic1                               uid_pic1,
            pic2.username                         pic2,
            pm.pic2                               uid_pic2,
            pm.target_date,
            pm.real_date
            FROM   project_main pm
            LEFT JOIN project_group pg
                ON pm.group_id = pg.id
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
            LEFT JOIN user pic1
                On pm.pic1 = pic1.id
            LEFT JOIN user pic2
                On pm.pic2 = pic2.id
            WHERE  1 = 1 ";

if($pid != 0)
{
    $query = $query . " and pm.id = " . $pid . " ";
}

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$query = $query . " order by pm.created_at desc ";

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


