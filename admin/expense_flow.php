<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header( 'location:../index' );
}

include_once '../api/config/core.php';
include_once '../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../api/libs/php-jwt-master/src/JWT.php';
include_once '../api/config/database.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    if($decoded->data->limited_access == true)
    header( 'location:../index' );

    $database = new Database();
    $db = $database->getConnection();

    $for_user = false;

    $query = "SELECT * FROM access_control WHERE `for_user` LIKE '%" . $username . "%' ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $for_user = true;
    }

    if ($for_user == false)
        header( 'location:../index' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header( 'location:../index' );
}

?>
<!DOCTYPE html>
<html>
<head>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="../images/favicon.ico" />
<link rel="Bookmark" href="../images/favicon.ico" />
<link rel="icon" href="../images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="../images/iosicon.png"/>

<!-- SEO -->
<title>FELIIX template</title>
<meta name="keywords" content="FELIIX">
<meta name="Description" content="FELIIX">
<meta name="robots" content="all" />
<meta name="author" content="FELIIX" />

<!-- Open Graph protocol -->
<meta property="og:site_name" content="FELIIX" />
<!--<meta property="og:url" content="分享網址" />-->
<meta property="og:type" content="website" />
<meta property="og:description" content="FELIIX" />
<!--<meta property="og:image" content="分享圖片(1200×628)" />-->
<!-- Google Analytics -->

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="../css/default.css"/>
<link rel="stylesheet" type="text/css" href="../css/ui.css"/>
<link rel="stylesheet" type="text/css" href="../css/case.css"/>
<link rel="stylesheet" type="text/css" href="../css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="../js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="../js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="../js/main.js" defer></script>

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>

</head>

<body class="cyan">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent" id="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="user">User</a>
            <a class="tag B" href="department">Department</a>
            <a class="tag C" href="position">Position</a>
            <a class="tag D" href="leave_flow">Leave Flow</a>
            <a class="tag E focus">Expense Flow</a>
        </div>
        <!-- Blocks -->
        <div class="block E focus">
            <h6>Expense Flow Management
                
            </h6>
            
            <div class="box-content">
                <div class="box-content">
                    <ul>
                        <li>
                            <div class="function" style="float:left; margin-right:10px;">
                                Choose Department: <select v-model="department_id">
                                    <option v-for="item in departments" :value="item.id" :key="item.department">
                                        {{ item.department }}
                                    </option>
                                </select>
                            </div>
                        </li>

                        <li>
                            <div class="function" style="float:left; margin-right:10px;">
                                Choose User: <select v-model="user_id">
                                    <option v-for="item in user_list" :value="item.id" :key="item.username">
                                        {{ item.username }}
                                    </option>
                                </select>
                            </div>
                        </li>

                        <li>
                            <div class="function" style="float:left; margin-right:10px;">
                                Choose Role: <select v-model="flow_type">
                                    <option value="1">
                                        Checker
                                    </option>
                                    <option value="2">
                                        Approver (OP)
                                    </option>
                                    <option value="3">
                                        Approver (MD)
                                    </option>
                                    <option value="4">
                                        Releaser (Office Petty Cash)
                                    </option>
                                    <option value="5">
                                        Releaser (Online Transactions)
                                    </option>
                                    <option value="6">
                                        Releaser (Security Bank)
                                    </option>
                                    <option value="7">
                                        Verifier
                                    </option>

                                </select>
                            </div>
                        </li>

                    </ul>
             
                    <ul>
                        <li>
                            <div style="padding-top:80px;">
                                <div>
                                    <button type="button" @click="cancelReceiveRecord($event)"><p>CLEAR</p></button>
                                    <button type="button" @click="createReceiveRecord()"><p>ADD</p></button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Department</li>
                        <li>Name</li>
                     	<li>Role</li>
                        
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.index" :true-value="1" v-model:checked="record.is_checked"></li>
                        <li>{{record.department}}</li>
                        <li>{{record.username}}</li>
                        <li>{{ (record.flow == 1) ? "Checker" : (record.flow == 2) ? "Approver (OP)" : (record.flow == 3) ? "Approver (MD)" : (record.flow == 4) ? "Releaser (Office Petty Cash)" : (record.flow == 5) ? "Releaser (Online Transactions)" : (record.flow == 6) ? "Releaser (Security Bank)" : (record.flow == 7) ? "Verifier" : "" }}</li>
                        
                    </ul>
                    
                </div>
                <div class="btnbox">
                    <a class="btn" @click="deleteRecord()">Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="../js/npm/vue/dist/vue.js"></script> 
<script defer src="../js/axios.min.js"></script> 
<script defer src="../js/npm/sweetalert2@9.js"></script>
<script defer src="../js/admin/expense_flow.js"></script>
</html>
