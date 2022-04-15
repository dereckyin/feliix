<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/config/database.php';


use \Firebase\JWT\JWT;

$test_manager = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

$GLOBALS['position'] = $decoded->data->position;
$GLOBALS['department'] = $decoded->data->department;

if($GLOBALS['department'] == 'Lighting' || $GLOBALS['department'] == 'Office' || $GLOBALS['department'] == 'Sales'){
$test_manager = "1";
}

//  ('Kuan', 'Dennis Lin', 'dereck', 'Ariel Lin', 'Kristel Tan');
if($user_id == 48 || $user_id == 2 || $user_id == 11 || $user_id == 6 ||  $user_id == 1 || $user_id == 3)
$test_manager = "1";
}


catch (Exception $e){

header( 'location:index' );
}


//if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
//    header( 'location:index.php' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e){

header( 'location:index' );
}

?>
<!DOCTYPE html>
<html>
<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>FELIIX template</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX"/>
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');

            dialogshow($('.list_function .new_function a.filter'), $('.list_function .dialog.A'));
            dialogshow($('.list_function .new_function a.sort'), $('.list_function .dialog.B'));

            $('.qn_page').click(function () {
                app.close_all();
            })

        })

    </script>

    <style>

        body.gray header > .headerbox {
            background-color: #707071;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.gray header nav a, body.gray header nav a:link {
            color: #000;
        }

        body.gray header nav a:hover {
            color: #333;
        }

        body.gray header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.gray header nav ul.info {
            margin-bottom: 0;
        }

        body.gray header nav ul.info b {
            font-weight: bold;
        }

        body.gray select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        }

        body.gray .mainContent > .block {
            display: block;
            width: 100%;
            border: none;
            margin: 0 0 15px;
        }

        body.gray .list_function .new_function {
            float: left;
            display: inline-block;
            position: relative;
            vertical-align: bottom;
            margin-right: 20px;
            margin-top: -15px;
        }

        body.gray .list_function .new_function a.add {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_add_green.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            cursor: pointer;
        }

        body.gray .list_function .new_function a.filter {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            cursor: pointer;
        }

        body.gray .list_function .new_function a.sort {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            cursor: pointer;
        }

        body.gray .dialog .formbox .half {
            width: 48.5%;
        }

        body.gray .list_function .pagenation a {
            color: #707071;
            border-color: #707071;
        }

        body.gray .list_function .pagenation a:hover {
            background-color: #707071;
            color: #FFF;
        }

        body.gray input.alone[type=checkbox]::before, body.gray input[type=checkbox] + Label::before {
            color: #414042;
        }

        #tb_product_list {
            width: 100%;
        }

        #tb_product_list thead th, #tb_product_list tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        #tb_product_list thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_product_list tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_product_list tbody tr td:nth-of-type(1) {
            width: 115px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) {
            width: 400px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) {
            width: 460px;
        }

        #tb_product_list tbody tr td:nth-of-type(4) {
            width: 220px;
        }

        #tb_product_list tbody tr td:nth-of-type(5) {
            width: 80px;
        }

        #tb_product_list tbody tr td:nth-of-type(1) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) ul {
            margin-bottom: 0;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul {
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 3px;
        }

        #tb_product_list tbody tr td:nth-of-type(5) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px;
            font-size: 20px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul:last-of-type {
            border-bottom: none;
        }

        #tb_product_list ul li {
            display: table-cell;
            text-decoration: none;
            text-align: left;
        }

        #tb_product_list ul li:first-of-type {
            font-weight: 600;
            padding: 1px 7px 1px 5px;
            max-width: 230px;
        }

        #tb_product_list ul li:nth-of-type(2) span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px 2px 6px;
            margin: 0 2px;
        }

        .NTD_price {

        }


        .one_half {
            width: 48%;
            display: inline-block;
        }

        .one_third {
            width: 32%;
            display: inline-block;
        }

        .one_whole {
            width: 96%;
            display: inline-block;
        }

        .table_template {
            text-align: center;
        }

        .table_template thead th {
            background-color: #E0E0E0;
            padding: 10px;
            text-align: center;
        }

        .table_template tbody td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #E2E2E2;
        }

        .table_template tbody tr:nth-of-type(even) {
            background-color: #F7F7F7
        }

        .table_template .itembox .photo {
            width: 100px;
            height: 100px;
        }

        .btnbox {
            text-align: center;
        }

        .btnbox > button, .heading-and-btn button {
            margin: 0 10px;
            width: 80px;
        }

        .bodybox .mask {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }


        .qn_page {
            width: 1200px;
            height: 1697px;
            background-color: white;
            position: relative;
            margin-bottom: 80px;
        }

        .qn_page .qn_header {
            width: 100%;
            height: 275px;
            background: url('images/Feliix-QuoteBG-03.png');
            background-size: 100% auto;
            position: absolute;
            top: 0;
            left: 0;
        }

        .qn_header .left_block {
            width: 71%;
            float: left;
            padding-left: 30px;
        }

        .qn_header .left_block img.logo {
            display: block;
            width: 166px;
            margin-top: 35px;
        }

        .qn_header .left_block .qn_title {
            margin-top: 50px;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            height: 76.8px;
        }

        .qn_header .left_block .qn_title > div {
            height: 38.4px;
        }

        .qn_header .left_block .project_category {
            margin-top: 15px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            height: 33.6px;
        }

        .qn_header .right_block {
            width: 29%;
            float: right;
            padding-right: 3px;
        }

        .qn_header .right_block .qn_number_date {
            margin-top: 35px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
        }

        .qn_header .right_block .qn_number_date .qn_number,
        .qn_header .right_block .qn_number_date .qn_date,
        .qn_header .right_block .qn_for div,
        .qn_header .right_block .qn_by div {
            font-weight: 700;
        }

        .qn_header .right_block .qn_for {
            margin-top: 45px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
            height: 67.2px;
        }

        .qn_header .right_block .qn_for > div {
            height: 16.8px;
            width: 100%;
            overflow: hidden;
        }

        .qn_header .right_block .qn_by {
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
            height: 50.4px;
        }

        .qn_header .right_block .qn_by > div {
            height: 16.8px;
        }

        .qn_page .qn_body {
            padding: 305px 30px 30px;
        }

        .qn_page .qn_footer {
            width: 100%;
            height: 107px;
            padding: 30px;
            position: absolute;
            bottom: 0;
            left: 0;
            background-color: white;
        }

        .qn_footer .foot_divider {
            border-top: 2px solid black;
            width: 100%;
            margin-bottom: 6px;
        }

        .qn_footer .line1 {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.1;
            height: 16.5px;
        }

        .qn_footer .line2 {
            font-size: 15px;
            height: 22.5px;
        }

        .qn_footer .qn_page_number {
            position: absolute;
            font-weight: 700;
            right: 45px;
            top: 45px;
        }

        .qn_body .area_conforme {
            width: 100%;
        }

        .area_conforme .conforme {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: -35px;
        }

        .area_conforme .client_signature, .area_conforme .company_signature {
            display: flex;
            justify-content: space-around;
        }

        .area_conforme .signature {
            text-align: center;
            padding-top: 20px;
        }

        .area_conforme .signature .pic {
            width: 230px;
            height: 150px;
            padding-bottom: 5px;
            text-align: center;
            vertical-align: bottom;
            display: table-cell;
        }

        .area_conforme .signature .name {
            font-weight: 700;
            border-top: 2px solid black;
            padding-top: 5px;
            margin-bottom: -3px;
        }

        .area_conforme .signature .line1, .area_conforme .signature .line2, .area_conforme .signature .line3 {
            height: 24px;
            margin-bottom: -3px;
        }

        .area_terms {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .area_terms .terms {
            display: inline-block;
            width: 48%;
            border: 2px dotted black;
            margin: 10px;
        }

        .area_terms .terms .title {
            text-align: center;
            padding: 3px;
            border-bottom: 2px dotted black;
            font-size: 18px;
            font-weight: 700;
        }

        .area_terms .terms .brief {
            text-align: center;
            padding: 3px;
            border-bottom: 2px dotted black;
            font-size: 16px;
        }

        .area_terms .terms .listing {
            font-size: 14px;
            padding: 7px 7px 7px 14px;
            margin-bottom: 0;
        }

        .area_payment .tb_payment {
            width: 100%;
            margin: 10px;
        }

        .tb_payment td {
            text-align: left;
            padding: 5px 10px;
            border-right: 1px solid #A0A0A0;
            border-bottom: 1px solid #A0A0A0;
            height: 37px;
        }

        .tb_payment tbody tr td:first-of-type {
            border-left: 1px solid #A0A0A0;
            font-style: italic;
            vertical-align: top;
        }

        .tb_payment tbody tr:nth-of-type(1) td {
            border-top: 1px solid #A0A0A0;
        }

        .tb_payment tbody tr:nth-of-type(1) td:first-of-type {
            border-right: none;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) {
            padding: 5px 100px 5px 30px;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) > div{
            display: flex;
            justify-content: space-between;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) > div > span {
            position: relative;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) > div > span::before {
            content: "";
            width: 20px;
            height: 20px;
            border: 1px solid black;
            display: inline-block;
            position: absolute;
            top: 2.5px;
            left: -25px;
        }

        .tb_payment tbody tr:nth-of-type(2) td {
            text-align: center;
            font-weight: 700;

        }

        .tb_payment tbody tr:nth-of-type(3) td {
            vertical-align: top;
            line-height: 1.8;
        }

        .tb_payment tbody tr:nth-of-type(3) td:first-of-type {
            text-align: center;
            width: 90px;
        }

        .tb_payment tbody tr:nth-of-type(3) td:nth-of-type(2) {
            width: 220px;
        }

        .acount_info > span:first-of-type {
            font-weight: 700;
            text-decoration: underline;
        }

        .acount_info > .first_line {
            display: inline-block;
        }

        .area_total .tb_total {
            width: 100%;
        }

        .tb_total td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_total tbody tr:nth-of-type(1) td:first-of-type {
            border-left: 2px solid #A0A0A0;
            font-style: italic;
            vertical-align: bottom;
            padding-left: 10px;
        }

        .tb_total tbody tr:nth-of-type(1) td {
            border-top: 2px solid #A0A0A0;
        }

        .tb_total tbody tr td span.numbers, .tb_total tfoot tr td span.numbers {
            font-weight: 800;
        }

        .tb_total tfoot td {
            color: red;
        }

        .tb_total tfoot tr td:nth-of-type(1) {
            border-left: 2px solid #A0A0A0;
            border-right: none;
            font-style: italic;
            padding-left: 10px;
        }

        .tb_total tbody tr td:nth-last-of-type(2), .tb_total tfoot tr td:nth-last-of-type(2) {
            font-weight: 800;
            text-align: center;
            width: 225px;
        }

        .tb_total tbody tr td:nth-last-of-type(1), .tb_total tfoot tr td:nth-last-of-type(1) {
            width: 225px;
            padding: 5px 15px;
            text-align: right;
        }

        .qn_body .area_subtotal {
            width: 100%;
        }

        .area_subtotal .tb_format1 {
            width: 100%;
            margin-bottom: 30px;
        }


        .tb_format1 th, .tb_format1 td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format1 thead tr th:first-of-type,
        .tb_format1 tbody tr td:first-of-type,
        .tb_format1 tfoot tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format1 thead tr th.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format1 thead tr:nth-of-type(2) th {
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }


        .tb_format1 tbody tr td img {
            max-height: 90px;
            max-width: 90px;
        }

        .tb_format1 tbody tr td.pic {
            width: 125px;
            text-align: center;
        }

        .tb_format1 tbody tr td {
            font-size: 14px;
            vertical-align: top;
            padding: 15px;
        }

        .tb_format1 tfoot tr td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format1 tfoot tr td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format1 tfoot tr td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
            padding: 5px 15px;
        }

        .tb_format1 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format1 tbody tr td div.code {
            font-size: 16px;
            font-weight: 800;
        }

        .tb_format1 tbody tr td div.brief {
            font-size: 16px;
            font-weight: 400;
        }

        .tb_format1 tbody tr td div.listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
        }

        .tb_format1 tbody tr td span.numbers {
            font-weight: 800;
            font-size: 15px;
        }

        .tb_format1 tbody tr td span.numbers.red {
            color: red;
        }

        .tb_format1 tbody tr td span.numbers.deleted {
            position: relative;
        }

        .tb_format1 tbody tr td span.numbers.deleted::before {
            content: "";
            width: 100%;
            height: 1px;
            background-color: red;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .tb_format1 tbody tr td span.numbers.deleted span {
            position: absolute;
            color: white;
            background-color: red;
            padding: 0px 5px 1px;
            border-radius: 7px;
            top: 10px;
            left: -75px;
            font-size: 15px;
            font-weight: 500;
        }


        .tb_format1 thead tr:nth-of-type(2) td:nth-of-type(1), .tb_format1 tbody tr td:nth-of-type(1) {
            width: 64px;
            text-align: center;
        }

        .tb_format1 thead tr:nth-of-type(2) td:nth-last-of-type(3), .tb_format1 tbody tr td:nth-last-of-type(3) {
            width: 75px;
            text-align: center;
        }

        .tb_format1 thead tr:nth-of-type(2) td:nth-last-of-type(2),
        .tb_format1 tbody tr td:nth-last-of-type(2) {
            width: 150px;
            text-align: right;
        }

        .tb_format1 thead tr:nth-of-type(2) td:nth-last-of-type(1),
        .tb_format1 tbody tr td:nth-last-of-type(1) {
            width: 225px;
            text-align: right;
        }

        .tb_format1.vat thead tr:nth-of-type(2) td:nth-last-of-type(3), .tb_format1.vat tbody tr td:nth-last-of-type(3) {
            width: 150px;
            text-align: right;
        }

        .tb_format1.vat thead tr:nth-of-type(2) td:nth-last-of-type(4), .tb_format1.vat tbody tr td:nth-last-of-type(4) {
            width: 75px;
            text-align: center;
        }

        .area_subtotal .tb_format2 {
            width: 100%;
            margin-bottom: 30px;
        }

        .tb_format2 th, .tb_format2 td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format2 thead tr th:first-of-type,
        .tb_format2 tbody tr td:first-of-type,
        .tb_format2 tfoot tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format2 thead tr th.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tbody tr td {
            font-size: 14px;
            vertical-align: top;
            padding: 15px;
        }

        .tb_format2 tfoot tr td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format2 tfoot tr td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tfoot tr td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format2 tbody tr td div.code {
            font-size: 16px;
            font-weight: 800;
        }

        .tb_format2 tbody tr td div.brief {
            font-size: 16px;
            font-weight: 400;
        }

        .tb_format2 tbody tr td div.listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
        }

        .tb_format2 tbody tr td span.numbers {
            font-weight: 800;
            font-size: 15px;
        }

        .tb_format2 tbody tr td span.numbers.red {
            color: red;
        }

        .tb_format2 tbody tr td span.numbers.deleted {
            position: relative;
        }

        .tb_format2 tbody tr td span.numbers.deleted::before {
            content: "";
            width: 100%;
            height: 1px;
            background-color: red;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .tb_format2 tbody tr td span.numbers.deleted span {
            position: absolute;
            color: white;
            background-color: red;
            padding: 0px 5px 1px;
            border-radius: 7px;
            top: 10px;
            left: -75px;
            font-size: 15px;
            font-weight: 500;
        }

        .tb_format2 tbody tr td:nth-of-type(1) {
            width: 64px;
            text-align: center;
        }

        .tb_format2 tbody tr td:nth-last-of-type(1) {
            width: 225px;
            text-align: right;
        }

        .tb_format2 tfoot tr td:nth-last-of-type(1) {
            width: 225px;
            text-align: right;
            padding: 5px 15px;
        }

        .tb_format2 tfoot tr td:nth-last-of-type(2) {
            width: 160px;
        }

        .tb_format2.vat tbody tr td:nth-last-of-type(2) {
            width: 150px;
            text-align: right;
        }


        .pagebox {
            border: 1px solid black;
            margin-bottom: 20px;
        }

        .pagebox .title_box {
            border-bottom: 1px solid black;
        }

        .pagebox .title_box ul {
            width: 100%;
            margin-bottom: 0;
            display: flex;
        }

        .pagebox .title_box ul li:nth-of-type(1) {
            width: 80%;
            padding: 3px 3px 3px 10px;
            font-weight: 700;
        }

        .pagebox .title_box ul li:nth-of-type(2) {
            border-left: 1px solid black;
            width: 20%;
            padding: 3px;
            text-align: center;
        }

        .pagebox .title_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            cursor: pointer;
        }

        .pagebox .function_box {
            padding: 10px 10px 15px 10px;
        }

        .pagebox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .pagebox .content_box {
            padding: 0 10px 10px 10px;
        }

        .pagebox .content_box ul {
            width: 100%;
            border-bottom: none;
            display: flex;
            margin-bottom: 0;
            align-items: center;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .pagebox .content_box ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .pagebox .content_box ul li:nth-of-type(1) {
            width: 75%;
            padding: 3px 3px 10px 10px;
            line-height: 2;
            border-right: 1px solid black;
        }

        .pagebox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
        }

        .pagebox .content_box ul li:nth-of-type(1) input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 50px;
        }

        .pagebox .content_box ul li:nth-of-type(1) input[type='checkbox'] {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid black;
            margin-left: 50px;
        }

        .pagebox .content_box ul li:nth-of-type(2) {
            width: 25%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .pagebox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        #page_dialog, #subtotal_dialog, #terms_dialog, #payment_dialog {
            min-width: 1000px;
            pointer-events: auto;
        }

        #signature_dialog {
            min-width: 700px;
            pointer-events: auto;
        }

        #total_dialog {
            pointer-events: auto;
        }

        #page_dialog h6 {
            margin-bottom: 15px;
        }

        #page_dialog h6 a.add_page {
            background-image: url(images/ui/file-plus.svg);
            width: 30px;
            height: 30px;
            float: right;
            text-decoration: none;
            border-bottom: none;
        }

        #page_dialog .page_form, #subtotal_dialog .subtotalbox, #terms_dialog .termsbox{
            max-height: 400px;
            overflow-y: auto;
        }

        #payment_dialog .termsbox {
            max-height: 300px;
            overflow-y: auto;
        }

        .subtotalbox {
            margin-bottom: 5px;
        }

        .subtotalbox .title_box {
            border: 1px solid black;
            padding: 7px;
            font-weight: 700;
        }

        .subtotalbox .function_box {
            padding: 10px 10px 15px 10px;
        }

        .subtotalbox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .subtotalbox .content_box {
            padding: 0 10px 10px 10px;
        }

        .subtotalbox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .subtotalbox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) {
            width: 85%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 95px;
            padding-right: 5px;
            text-align: right;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            margin: 5px 0;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 95px;
            margin: 5px 10px 5px 0;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) textarea {
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 110px);
            resize: none;
            margin: 5px 0;
        }

        .subtotalbox .content_box ul li:nth-of-type(2) {
            width: 15%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .subtotalbox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        .subtotalbox .content_box .itembox {
            display: inline-block;
            margin: 5px 0;
        }

        .subtotalbox .content_box .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 90px;
            height: 90px;
            padding: 3px;
            position: relative;
        }

        .subtotalbox .content_box .itembox .photo::before {
            content: "+";
            display: block;
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 36px;
            font-weight: 300;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }

        .subtotalbox .content_box .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .subtotalbox .content_box .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .subtotalbox .content_box .itembox.chosen .photo::before {
            content: none;
        }

        .subtotalbox .content_box .itembox .photo > div {
            display: none;
        }

        .subtotalbox .content_box .itembox.chosen .photo > div {
            display: inline-block;
            margin: 8px auto 5px;
            width: 36px;
            height: 36px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 28px;
            text-align: center;
            color: #EA0029;
            font-size: 24px;
            font-weight: 400;
            cursor: pointer;
            position: absolute;
            top: 18px;
            right: -50px;
        }

        #terms_dialog .formbox dl {
            margin-bottom: 0px;
            border-bottom: 1px solid black;
        }

        #terms_dialog .formbox dl dd select {
            width: 370px;
        }

        .termsbox {
            margin: 10px 0 5px;

        }

        .termsbox .function_box {
            padding: 5px 10px 10px 10px;
        }

        .termsbox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .termsbox .content_box {
            padding: 0 10px 10px 10px;
        }

        .termsbox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .termsbox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .termsbox .content_box ul li:nth-of-type(1) {
            width: 85%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .termsbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 75px;
            padding-right: 5px;
            text-align: right;
        }

        .termsbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 110px);
            margin: 5px 0;
        }

        .termsbox .content_box ul li:nth-of-type(1) textarea {
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 110px);
            resize: none;
            margin: 5px 0;
        }

        .termsbox .content_box ul li:nth-of-type(2) {
            width: 15%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .termsbox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        #payment_dialog .formbox dl:first-of-type {
            margin-bottom: 0px;
            border-bottom: 1px solid black;
        }

        #payment_dialog .formbox dl:nth-of-type(2) {
            margin-bottom: 0px;
            padding: 0 10px;
        }

        #payment_dialog .formbox dl dd select {
            width: 370px;
        }

        #payment_dialog .termsbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 120px;
            padding-right: 5px;
            text-align: right;
        }

        #payment_dialog .termsbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 155px);
            margin: 5px 0;
        }


        .signaturebox {
            margin-bottom: 5px;
        }

        .signaturebox .title_box {
            border: 1px solid black;
            padding: 7px;
            font-weight: 700;
        }

        .signaturebox .function_box {
            padding: 8px 10px 8px 0px;
        }

        .signaturebox .content_box {
            padding: 0 10px 10px 10px;
        }

        .signaturebox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .signaturebox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .signaturebox .content_box ul li:nth-of-type(1) {
            width: 80%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .signaturebox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 95px;
            padding-right: 5px;
            text-align: right;
        }

        .signaturebox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            margin: 5px 0;
        }

        .signaturebox .content_box ul li:nth-of-type(2) {
            width: 20%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .signaturebox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        .signaturebox .content_box .itembox {
            display: inline-block;
            margin: 5px 0;
        }

        .signaturebox .content_box .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 90px;
            height: 90px;
            padding: 3px;
            position: relative;
        }

        .signaturebox .content_box .itembox .photo::before {
            content: "+";
            display: block;
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 36px;
            font-weight: 300;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }

        .signaturebox .content_box .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .signaturebox .content_box .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .signaturebox .content_box .itembox.chosen .photo::before {
            content: none;
        }

        .signaturebox .content_box .itembox .photo > div {
            display: none;
        }

        .signaturebox .content_box .itembox.chosen .photo > div {
            display: inline-block;
            margin: 8px auto 5px;
            width: 36px;
            height: 36px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 28px;
            text-align: center;
            color: #EA0029;
            font-size: 24px;
            font-weight: 400;
            cursor: pointer;
            position: absolute;
            top: 18px;
            right: -50px;
        }

        #signature_dialog .formbox dl {
            margin-bottom: 15px;
            border-bottom: 1px solid black;
        }

        #signature_dialog .formbox dl dd select {
            width: 310px;
        }

        .list_function.main {
            border-color: #00811e;
        }

        .list_function.main .block.fn a {
            border-bottom-color: rgb(230, 230, 230);
        }

        .list_function.main a.print {
            width: 30px;
            height: 30px;
            background-color: #00811e;
            position: relative;
        }

        .list_function.main a.print::after {
            content: " ";
            background: url(images/ui/btn_print.svg);
            background-size: 45px 45px;
            width: 45px;
            height: 45px;
            position: absolute;
            top: -7px;
            left: -7px;
        }

        .list_function.main a.print:hover {
            background-color: #707071;
        }


        .modal .modal_function input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 250px;
            margin: 5px 10px 5px 0;
        }

        .modal .modal_function select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 14px;
            height: 30px;
            width: 170px;
            margin-right: 10px;
        }

        .modal .modal_function select:nth-of-type(2) {
            width: 350px;
        }

        .modal .modal_function > a.btn {
            margin-left: 10px;
            color: #FFF !important;
        }

        .upper_section {
            margin: 20px 20px 0;
            border: 2px solid rgb(225, 225, 225);
            display: flex;
        }

        .imagebox {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .imagebox .selected_image {
            padding: 20px;
            text-align: center;
            width: 300px;
            height: 300px;
        }

        .imagebox .selected_image img {
            object-fit: contain;
            width: 100%;
            height: 100%;
        }

        .imagebox .image_list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 0 10px 20px 20px;
        }

        .imagebox .image_list img {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
            margin: 5px 10px;
            cursor: pointer;
            border: 2px solid #ced4da;
        }

        .imagebox .image_list img:hover {
            border-color: #F0502F;
        }

        .infobox {
            width: 50%;
        }

        .infobox .basic_info {
            border-bottom: 2px solid rgb(225, 225, 225);
            margin-left: 20px;
            padding: 30px 20px 5px;
        }

        .infobox .basic_info div.tags {
            margin-bottom: 0.5rem;
        }

        .infobox .basic_info div.tags span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px;
            margin: 0 3px;
        }

        .infobox .basic_info div.tags span:first-of-type {
            margin-left: 0;
        }

        .infobox .basic_info div.tags span:last-of-type {
            margin-right: 0;
        }

        .infobox .price_stock {
            border-bottom: 2px solid rgb(225, 225, 225);
            margin-left: 20px;
            padding: 15px 20px;
        }

        .infobox .price_stock > li {
            color: rgb(83, 132, 155);
            font-size: 18px;
            font-weight: 600;
            padding: 8px 0;
        }

        .infobox .price_stock > li span:nth-of-type(1) {
            font-size: 22px;
            color: #6C757D;
            display: inline-block;
            margin-left: 10px;
        }

        .infobox .price_stock > li span:nth-of-type(2) {
            font-size: 16px;
            font-weight: 400;
            color: #6C757D;
            display: inline-block;
            margin-left: 10px;
        }

        .infobox .variants {
            margin-left: 20px;
            padding: 0 20px;
        }

        .infobox .variants li {
            color: #212529;
            font-size: 16px;
            margin-left: 15px;
        }

        .infobox .variants li:nth-of-type(odd) {
            margin-bottom: 15px;
        }

        .infobox .variants li:nth-of-type(1) {
            font-weight: 700;
            margin-left: 0;
            margin-bottom: 5px;
        }

        .infobox .variants .dropdown-menu li {
            margin-left: 0;
        }

        .infobox .variants .btn-light {
            background-color: #fff;
            border: 1px solid #ced4da;
            outline: none;
        }

        .infobox .variants .btn-light:focus {
            outline: none !important;
            box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
        }

        .infobox .btnbox ul {
            width: 100%;
            display: flex;
            justify-content: space-evenly;
        }

        .infobox .btnbox ul li > button {
            width: 230px;

        }

        .middle_section {
            margin: 0 20px;
            border: 2px solid rgb(225, 225, 225);
            border-top: none;
        }

        .middle_section h5 {
            background-color: #E0E0E0;
            text-align: center;
            padding: 5px 0 8px;
            margin-bottom: 0;
        }

        .middle_section table {
            margin: 5px 20px;
            width: calc(100% - 40px);
        }

        .middle_section tbody tr:nth-of-type(n+2) {
            border-top: 1px solid rgb(225, 225, 225);
        }

        .middle_section tbody tr td:nth-of-type(odd) {
            color: #B3B3B3;
            padding: 10px;
            width: 20%;
        }

        .middle_section tbody tr td:nth-of-type(even) {
            width: 30%;
        }

        .lower_section {
            margin: 0 20px 20px;
            border: 2px solid rgb(225, 225, 225);
            border-top: none;
            text-align: left;
        }

        .lower_section h5 {
            background-color: #E0E0E0;
            text-align: center;
            padding: 5px 0 8px;
            margin-bottom: 0;
        }

        .lower_section p {
            margin: 15px 20px;
        }

        .lower_section .desc_imgbox {
            width: 100%;
            padding: 0 15px;
            margin: 10px 0 20px;
        }

        .lower_section .desc_imgbox img {
            width: calc(50% - 8px);
            margin: 5px 0;
        }

        .lower_section .desc_imgbox img:nth-of-type(odd) {
            margin-right: 10px;
        }


        .row.custom {
            margin: 5px 0 0 0;
        }

        .col.custom {
            width: 24%;
            padding-left: 5px;
            padding-right: 5px;
            text-align: center;
        }

        .col.custom > img {
            max-height: 200px;
            max-width: 100%;
        }

        .col.custom > div > a {
            text-decoration: none;
            color: blue;
            cursor: pointer;
            font-size: 16px;
            padding: 5px 10px;
        }

        .carousel-control-next, .carousel-control-prev {
            opacity: 0.7;
            top: 35%;
            width: 4%;
        }


        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
            }

            .mainContent {
                padding: 0;
                background-color: #FFF !important;
            }

            .qn_page {
                zoom: 82%;
                margin: 1px 0px 0px 7px;
                page-break-after: always;
                overflow-y: hidden;
            }

            .noPrint {
                display: none;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

    </style>

</head>

<body class="gray">

<div class="bodybox" id="app">

    <div class="mask" :ref="'mask'"></div>

    <!-- header -->
    <header class="noPrint">header</header>
    <!-- header end -->
    <div class="mainContent" style="background-color: rgb(230,230,230)">

        <div class="list_function main noPrint">

            <div class="block">
                <!-- print -->
                <div class="popupblock">
                    <a id="" class="print" @click="print_page()"></a>
                </div>
            </div>

            <div class="block fn">
                <div class="popupblock">
                    <?php
                if ($test_manager[0]  == "1")
                {
                ?>
                    <a id="status_fn1" class="fn1" :ref="'a_fn1'" @click="show_header = !show_header">Header</a>
                    <?php
                } else {
                ?>
                    <a>Header</a>
                    <?php
                }
                ?>
                    <div id="header_dialog" class="dialog fn1 show" :ref="'dlg_fn1'" v-show="show_header">
                        <h6>Header</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Quotation Title:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line" v-model="temp_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_second_line">
                                </dd>
                                <dt>Project Category:</dt>
                                <dd>
                                    <select v-model="temp_project_category">
                                        <option value="Lighting">Lighting</option>
                                        <option value="Office Systems">Office Systems</option>
                                    </select>
                                </dd>
                                <dt>Quotation Number:</dt>
                                <dd>
                                    <input type="text" v-model="temp_quotation_no">
                                </dd>
                                <dt>Quotation Date:</dt>
                                <dd>
                                    <input type="date" v-model="temp_quotation_date">
                                </dd>
                                <dt>Prepare for:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line" v-model="temp_prepare_for_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_prepare_for_second_line">
                                    <input type="text" placeholder="Third Line" v-model="temp_prepare_for_third_line">
                                </dd>
                                <dt>Prepare by:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line" v-model="temp_prepare_by_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_prepare_by_second_line">
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_header()">Close</a>
                                    <a class="btn small green" @click="save_header()">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock">
                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="status_fn1" class="fn1" :ref="'a_fn1'" @click="show_footer = !show_footer">Footer</a>
                    <?php
                    } else {
                    ?>
                    <a>Footer</a>
                    <?php
                    }
                    ?>
                    <div id="footer_dialog" class="dialog fn1 show" :ref="'dlg_fn1'" v-show="show_footer">
                        <h6>Footer</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">First Line (bold font):</dt>
                                <dd>
                                    <input type="text" v-model="temp_footer_first_line">
                                </dd>
                                <dt>Second Line:</dt>
                                <dd>
                                    <input type="text" v-model="temp_footer_second_line">
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_footer()">Close</a>
                                    <a class="btn small green" @click="save_footer()">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_page = !show_page">Page</a>
                    <?php
                    } else {
                    ?>
                    <a>Page</a>
                    <?php
                    }
                    ?>
                    <div id="page_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_page">
                        <h6>Page
                            <a class="add_page" @click="add_page()"></a>
                        </h6>

                        <div class="page_form">

                            <div class="pagebox" v-for="(page, page_index) in temp_pages">

                                <div class="title_box">
                                    <ul>
                                        <li>Page {{page_index + 1}}</li>
                                        <li><i class="fas fa-arrow-alt-circle-up"
                                               @click="page_up(page_index, page.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="page_down(page_index, page.id)"></i>
                                            <i class="fas fa-trash-alt" @click="page_del(page.id)"></i>
                                        </li>
                                    </ul>
                                </div>
                                <div class="function_box">
                                    <select :id="'block_type_' + page.id">
                                        <option value="A">Type-A Subtotal Block</option>
                                        <option value="B">Type-B Subtotal Block</option>
                                    </select>
                                    <a class="btn small green" @click="add_item(page.id)">Add</a>
                                </div>
                                <div class="content_box">

                                    <ul v-for="(block, block_index) in page.types">
                                        <li>
                                            Type-{{block.type}} Subtotal Block<br>
                                            Name: <input type="text" v-model="block.name">
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="set_up(page.id, block_index, block.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="set_down(page.id, block_index, block.id)"></i>
                                            <i class="fas fa-file-upload"
                                               @click="set_up_page(page.id, page_index, block_index, block.id)"></i>
                                            <i class="fas fa-file-download"
                                               @click="set_down_page(page.id, page_index, block_index, block.id)"></i>
                                            <!--
                                            <i class="fas fa-save" @click="page_save()"></i>
                                            -->
                                            <i class="fas fa-trash-alt" @click="del_block(page.id, block.id)"></i>
                                        </li>
                                    </ul>


                                </div>

                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="show_page = false">Close</a>
                                <a class="btn small green" @click="page_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_subtotal = !show_subtotal">Subtotal</a>
                    <?php
                    } else {
                    ?>
                    <a>Subtotal</a>
                    <?php
                    }
                    ?>
                    <div id="subtotal_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_subtotal">
                        <h6>Subtotal</h6>

                        <div class="tablebox s2 edit"
                             style="padding-bottom: 3px; border-bottom: 1px solid black; margin-bottom: 12px;">
                            <ul>
                                <li class="head" style="width: 160px;">Choose Subtotal Block:</li>
                                <li class="mix">
                                    <select v-model="block_value">
                                        <option v-for="(block, index) in block_names" :value="block">{{ block.name }}
                                        </option>

                                    </select>

                                    <a class="btn small green" @click="load_block()">Load</a>
                                </li>
                            </ul>
                        </div>


                        <div class="subtotalbox Type-A" v-if="edit_type_a">

                            <div class="title_box">
                                {{block_value.name}}
                            </div>

                            <div class="function_box">
                                <select id="with_image">
                                    <option value="image">Item with Image</option>
                                    <option value="noimage">Item without Image</option>
                                </select>
                                <a class="btn small green" @click="add_block_a()">Add</a>
                                <a class="btn small green" @click="product_catalog_a()">Product Catalog</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(block, index) in temp_block_a">
                                    <li>
                                        <span>Code:</span> <input type="text" v-model="block.code"><br>
                                        <span v-if="block.type == 'image' ">Image:</span>
                                        <div v-if="block.type == 'image' "
                                             :class="['itembox', (block.url !== '' ? 'chosen' : '')]">
                                            <div class="photo">
                                                <input type="file" :name="'block_image_' + block.id"
                                                       @change="onFileChangeImage($event, block.id)"
                                                       :id="'block_image_' + block.id">
                                                <img v-if="block.url" :src="block.url"/>
                                                <div @click="clear_photo(block.id)">x</div>
                                            </div>

                                        </div>

                                        <br v-if="block.type == 'image' ">
                                        <span>Qty:</span> <input type="number" min="1" step="1" v-model="block.qty"
                                                                 @change="chang_amount(block)" oninput="this.value|=0">
                                        Product Price: <input type="number" v-model="block.price"
                                                              @change="chang_amount(block)">
                                        Discount: <input type="number" v-model="block.discount" min="0" max="100"
                                                         @change="chang_amount(block)" oninput="this.value|=0"> Amount:
                                        <input type="number" v-model="block.amount"><br>
                                        <span>Description:</span> <textarea rows="2"
                                                                            v-model="block.desc"></textarea><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="block.list"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_a_up(index, block.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="block_a_down(index, block.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_a_del(block.id)"></i>
                                    </li>
                                </ul>


                            </div>
                        </div>


                        <div class="subtotalbox Type-B" v-if="edit_type_b">

                            <div class="title_box">
                                {{block_value.name}}
                            </div>

                            <div class="function_box">
                                <a class="btn small green" @click="add_block_b()">Add Item</a>
                                <a class="btn small green" @click="product_catalog_b()">Product Catalog</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(block, index) in temp_block_b">
                                    <li>
                                        <span>Code:</span> <input type="text" v-model="block.code"><br>
                                        <span>Price:</span> <input type="number" v-model="block.price"
                                                                   @change="chang_discount(block)"> Discount: <input
                                            type="number" v-model="block.discount" @change="chang_discount(block)"
                                            min="0" max="100" oninput="this.value|=0"> Amount: <input type="number"
                                                                                                      v-model="block.amount"><br>
                                        <!-- <span>Discount:</span> <input type="number" v-model="block.discount" @change="chang_discount(block)" min="0" max="100" oninput="this.value|=0"> Amount: <input type="number" v-model="block.amount"><br> -->
                                        <span>Description:</span> <textarea rows="2"
                                                                            v-model="block.desc"></textarea><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="block.list"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_b_up(index, block.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="block_b_down(index, block.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_b_del(block.id)"></i>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="subtotal_close()">Close</a>
                                <a class="btn small green" @click="subtotal_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_total = !show_total">Total</a>
                    <?php
                    } else {
                    ?>
                    <a>Total</a>
                    <?php
                    }
                    ?>
                    <div id="total_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_total">
                        <h6>Total</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of total locates at:</dt>
                                <dd>
                                    <select v-model="total.page">
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>

                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Discount:</dt>
                                <dd>
                                    <input type="number" v-model="total.discount" min="0" max="100" step="1"
                                           oninput="this.value|=0" @change="change_total_amount(total)">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">12% VAT:</dt>
                                <dd>
                                    <select v-model="total.vat" @change="change_total_amount(total)">
                                        <option value="P">Yes (12% VAT is shown in each individual product)</option>
                                        <option value="Y">Yes (12% VAT is shown in the block of Total)</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Show "*price inclusive of VAT" in the Quotation:</dt>
                                <dd>
                                    <select v-model="total.show_vat">
                                        <option value="Y">Yes</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Quotation Valid for:</dt>
                                <dd>
                                    <input type="text" v-model="total.valid"
                                           placeholder="Input like 1 month, 45 days, ...">
                                </dd>
                            </dl>


                            <!-- 系統會先自動算出折扣後加稅後的總價，但使用者還是可以針對總價做後續修改(例如取整數等) -->
                            <dl>
                                <dt class="head">Grand Total:</dt>
                                <dd>
                                    <input type="number" v-model="total.total">
                                </dd>
                            </dl>

                            <div class="btnbox">
                                <a class="btn small" @click="close_total()">Close</a>
                                <a class="btn small green" @click="save_total()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_term = !show_term">Terms and
                        Condition</a>
                    <?php
                    } else {
                    ?>
                    <a>Terms and Condition</a>
                    <?php
                    }
                    ?>
                    <div id="terms_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_term">
                        <h6>Terms and Condition</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of terms and condition locates at:</dt>
                                <dd>
                                    <select v-model="term.page">
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>
                                    </select>
                                </dd>
                            </dl>
                        </div>


                        <div class="termsbox">
                            <div class="function_box">
                                <a class="btn small green" @click="add_term_item()">Add Item</a>
                            </div>

                            <div class="content_box">
                                <ul v-for="(item, index) in term.item">
                                    <li>
                                        <span>Title:</span> <input type="text" v-model="item.title"><br>
                                        <span>Brief:</span> <input type="text" v-model="item.brief"><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="item.list"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="term_item_up(index, item.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="term_item_down(index, item.id)"></i>
                                        <i class="fas fa-trash-alt" @click="term_item_del(index)"></i>
                                    </li>
                                </ul>


                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_term()">Close</a>
                                <a class="btn small green" @click="term_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_payment_term = !show_payment_term">Payment Terms</a>
                    <?php
                    } else {
                    ?>
                    <a>Payment Terms</a>
                    <?php
                    }
                    ?>
                    <div id="payment_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_payment_term">
                        <h6>Payment Terms</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of payment terms locates at:</dt>
                                <dd>
                                    <select v-model="payment_term.page">
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Payment Method:</dt>
                                <dd>
                                    <!-- <input type="text" value="Cash; Cheque; Credit Card; Bank Wiring;"> -->

                                    <input type="text" v-model="payment_term.payment_method">
                                </dd>

                                <dt class="head">Brief:</dt>
                                <dd>
                                    <input type="text" v-model="payment_term.brief">
                                </dd>
                            </dl>
                        </div>

                        <div class="termsbox">
                            <div class="function_box">
                                <a class="btn small green" @click="add_payment_term_item()">Add Account</a>
                            </div>

                            <div class="content_box">
                                <ul v-for="(item, index) in payment_term.item">
                                    <li>
                                        <span>Bank Name:</span> <input type="text" v-model="item.bank_name"><br>
                                        <span>First Line:</span> <input type="text" v-model="item.first_line"><br>
                                        <span>Second Line:</span> <input type="text" v-model="item.second_line"><br>
                                        <span>Third Line:</span> <input type="text" v-model="item.third_line"><br>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="payment_term_item_up(index, item.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="payment_term_item_down(index, item.id)"></i>
                                        <i class="fas fa-trash-alt" @click="payment_term_item_del(index)"></i>
                                    </li>
                                </ul>
                                
                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_payment_term()">Close</a>
                                <a class="btn small green" @click="payment_term_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'"
                       @click="show_signature = !show_signature">Signature</a>
                    <?php
                    } else {
                    ?>
                    <a>Signature</a>
                    <?php
                    }
                    ?>
                    <div id="signature_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_signature">
                        <h6>Signature</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of signature locates at:</dt>
                                <dd>
                                    <select v-model="sig.page">
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>
                                    </select>
                                </dd>
                            </dl>
                        </div>


                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="signaturebox client">

                                <div class="title_box">
                                    Client
                                </div>

                                <div class="function_box">
                                    <a class="btn small green" @click="add_sig_client_item()">Add</a>
                                </div>

                                <div class="content_box">
                                    <ul v-for="(item, index) in sig.item_client">
                                        <li>
                                            <span>Name:</span> <input type="text" v-model="item.name"><br>
                                            <span>Line 1:</span> <input type="text" placeholder="Position"
                                                                        v-model="item.position"><br>
                                            <span>Line 2:</span> <input type="text" placeholder="Phone Number"
                                                                        v-model="item.phone"><br>
                                            <span>Line 3:</span> <input type="text" placeholder="Email"
                                                                        v-model="item.email"><br>
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="sig_item_client_up(index, item.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="sig_item_client_down(index, item.id)"></i>
                                            <i class="fas fa-trash-alt"
                                               @click="sig_item_client_del(index, item.id)"></i>
                                        </li>
                                    </ul>


                                </div>
                            </div>


                            <div class="signaturebox company">

                                <div class="title_box">
                                    Feliix
                                </div>

                                <div class="function_box">
                                    <a class="btn small green" @click="add_sig_company_item()">Add</a>
                                </div>

                                <div class="content_box">
                                    <ul v-for="(item, index) in sig.item_company">
                                        <li>
                                            <span>Name:</span> <input type="text" v-model="item.name"><br>
                                            <span>Line 1:</span> <input type="text" placeholder="Position"
                                                                        v-model="item.position"><br>
                                            <span>Line 2:</span> <input type="text" placeholder="Phone Number"
                                                                        v-model="item.phone"><br>
                                            <span>Line 3:</span> <input type="text" placeholder="Email"
                                                                        v-model="item.email"><br>
                                            <span>Signature:</span>
                                            <div :class="['itembox', (item.url !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :name="'sig_image_' + item.id"
                                                           @change="onSigFileChangeImage($event, item.id)"
                                                           :id="'sig_image_' + item.id">
                                                    <img v-if="item.url" :src="item.url"/>
                                                    <div @click="clear_sig_photo(item.id)">x</div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="sig_item_company_up(index, item.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="sig_item_company_down(index, item.id)"></i>
                                            <i class="fas fa-trash-alt"
                                               @click="sig_item_company_del(index, item.id)"></i>
                                        </li>
                                    </ul>


                                </div>
                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_sig()">Close</a>
                                <a class="btn small green" @click="sig_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <div class="qn_page" v-for="(pg, index) in pages">

            <div class="qn_header" v-if="show_title">

                <div class="left_block">

                    <img class="logo" src="images/Feliix-Logo-Black.png">

                    <div class="qn_title">
                        <div class="line1">{{ first_line }}</div>
                        <div class="line2">{{ second_line }}</div>
                    </div>

                    <div class="project_category">
                        <div class="line1">Architectural</div>
                        <div class="line2">{{ project_category }} Quotation</div>
                    </div>

                </div>

                <div class="right_block">

                    <div class="qn_number_date">
                        Quotation No.: <span class="qn_number">{{ quotation_no }}</span><br>
                        Date: <span class="qn_date">{{ quotation_date }}</span>
                    </div>

                    <div class="qn_for">
                        Prepared for:<br>
                        <div class="line1">{{ prepare_for_first_line }}</div>
                        <div class="line2">{{ prepare_for_second_line }}</div>
                        <div class="line3">{{ prepare_for_third_line }}</div>
                    </div>

                    <div class="qn_by">
                        <br>
                        <div class="line1">{{ prepare_by_first_line }}</div>
                        <div class="line2">{{ prepare_by_second_line }}</div>
                    </div>

                </div>

            </div>

            <div class="qn_body">

                <div class="area_subtotal">

                    <table :class="[tp.type == 'A' ? 'tb_format1' : 'tb_format2', product_vat == 'P' ? 'vat' : '']" v-for="(tp, index) in pg.types">
                        <thead v-if="tp.type == 'A'">

                        <tr>
                            <th class="title" :colspan="product_vat == 'P' ? 7 : 6">{{ tp.name }}</th>
                        </tr>

                        <tr>
                            <th>No</th>
                            <th colspan="2">Description</th>
                            <th>Qty.</th>
                            <th>Product Price</th>
                            <th v-if="product_vat == 'P'">12% VAT</th>
                            <th>Amount</th>
                        </tr>

                        </thead>

                        <thead v-if="tp.type == 'B'">

                        <tr>
                            <th class="title" :colspan="product_vat == 'P' ? 5 : 4">{{ tp.name }}</th>
                        </tr>

                        </thead>

                        <tbody v-if="tp.type == 'A'">
                        <tr v-for="(bk, index) in tp.blocks">
                            <td>{{ index + 1 }}</td>
                            <td class="pic" v-if="bk.type == 'image'">
                                <img v-show="bk.photo !== ''" :src=" bk.photo !== '' ? img_url + bk.photo : ''">
                            </td>
                            <td v-if="bk.type == 'image'">
                                <div class="code">{{ bk.code }}</div>
                                <div class="brief" style="white-space: pre-line;">{{ bk.desc }}</div>
                                <div class="listing" style="white-space: pre-line;">{{ bk.list }}</div>
                            </td>
                            <td colspan="2" v-if="bk.type == ''">
                                <div class="code">{{ bk.code }}</div>
                                <div class="brief" style="white-space: pre-line;">{{ bk.desc }}
                                </div>
                                <div class="listing" style="white-space: pre-line;">{{ bk.list }}</div>
                            </td>
                            <td><span class="numbers">{{ bk.qty !== undefined ? Math.floor(bk.qty).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}</span>
                            </td>
                            <td><span class="numbers">₱ {{ bk.price !== undefined ? Number(bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                            </td>
                            <td v-if="product_vat == 'P'"><span class="numbers">₱ {{ bk.price !== undefined ? (Number(bk.price) * 0.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                            </td>

                            <td v-if="bk.amount != '0.00' && product_vat == 'P'">
                                <span class="numbers deleted" v-if="bk.discount != 0">₱ {{ (bk.qty * bk.price  !== undefined ? Number(bk.qty * bk.price * 1.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                    v-if="bk.discount != 0">
                                <span class="numbers">₱ {{ bk.amount !== undefined ? Number(bk.amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat == 'P'">
                                <span class="numbers deleted">₱ {{ (bk.qty * bk.price  !== undefined ? Number(bk.qty * bk.price * 1.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>


                            <td v-if="bk.amount != '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted" v-if="bk.discount != 0">₱ {{ (bk.qty * bk.price  !== undefined ? Number(bk.qty * bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                    v-if="bk.discount != 0">
                                <span class="numbers">₱ {{ bk.amount !== undefined ? Number(bk.amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted">₱ {{ (bk.qty * bk.price  !== undefined ? Number(bk.qty * bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>
                        </tr>

                        </tbody>

                        <tbody v-if="tp.type == 'B'">
                        <tr v-for="(bk, index) in tp.blocks">
                            <td>{{ index + 1 }}</td>
                            <td colspan="2">
                                <div class="code">{{ bk.code }}</div>
                                <div class="brief" style="white-space: pre-line;">{{ bk.desc }}</div>
                                <div class="listing" style="white-space: pre-line;">{{ bk.list }}</div>
                            </td>
                            <td v-if="product_vat == 'P'"><span class="numbers">₱ {{ bk.price !== undefined ? (Number(bk.price)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                            </td>
                            <td v-if="bk.amount != '0.00' && product_vat == 'P'">
                                <span class="numbers deleted" v-if="bk.discount != 0">₱ {{ (bk.price  !== undefined ? (Number(bk.price) * 1.12 ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                    v-if="bk.discount != 0">
                                <span class="numbers">₱ {{ bk.amount !== undefined ? (Number(bk.amount)).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat == 'P'">
                                <span class="numbers deleted">₱ {{ (bk.price  !== undefined ? (Number(bk.price) * 1.12 ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>


                            <td v-if="bk.amount != '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted" v-if="bk.discount != 0">₱ {{ (bk.price  !== undefined ? Number(bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                    v-if="bk.discount != 0">
                                <span class="numbers">₱ {{ bk.amount !== undefined ? Number(bk.amount).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted">₱ {{ (bk.price  !== undefined ? Number(bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>
                        </tr>

                        </tbody>

                        <tfoot v-if="tp.type == 'A'">
                        <tr>
                            <td :colspan="product_vat == 'P' ? 5 : 4"></td>
                            <td>SUBTOTAL</td>
                            <td>₱ {{ tp.subtotal !== undefined ?
                                Number(tp.subtotal).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                "$1,") : '0.00' }}
                            </td>
                        </tr>

                        </tfoot>

                        <tfoot v-if="tp.type == 'B'">
                        <tr>
                            <td :colspan="product_vat == 'P' ? 3 : 2"></td>
                            <td>SUBTOTAL</td>
                            <td>₱ {{ tp.subtotal !== undefined ?
                                Number(tp.subtotal).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                "$1,") : '0.00' }}
                            </td>
                        </tr>

                        </tfoot>

                    </table>

                </div>

                <div class="area_total">
                    <table class="tb_total" v-for="(tt, index) in pg.total">
                        <tbody>
                        <tr>
                            <td :rowspan="(tt.vat == 'Y' && tt.discount !== '0' ? 3 :  2)">
                                <div>Remarks: Quotation valid for <span class="valid_for">{{ tt.valid }}</span></div>
                                <div></div>
                            </td>
                            <td>SUBTOTAL</td>
                            <td><span class="numbers">₱ {{ subtotal !== undefined ? Number(subtotal).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                            </td>
                        </tr>

                        <tr class="total_discount" v-if="tt.discount !== '0'">
                            <td>{{ tt.discount !== undefined ?
                                Math.floor(tt.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}%
                                DISCOUNT
                            </td>
                            <td><span class="numbers">₱ {{ (subtotal * tt.discount / 100) !== undefined ? (subtotal * tt.discount / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                        </tr>
                        <!--
                                                <tr class="total_vat" v-if="tt.vat == 'Y'">
                                                    <td>(12% VAT)</td>
                                                    <td><span class="numbers">₱ {{ (subtotal * 12 / 100) !== undefined ? (subtotal * 12 / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span></td>
                                                </tr>
                            -->
                        <tr class="total_vat" v-if="tt.vat == 'Y'">
                            <td>(12% VAT)</td>
                            <td><span class="numbers">₱ {{ ((subtotal * (100 - tt.discount) / 100) * 12 / 100) !== undefined ? ((subtotal * (100 - tt.discount) / 100) * 12 / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                        </tr>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td><span class="total_discount" v-if="tt.show_vat == 'Y'">*price inclusive of VAT</span>
                            </td>
                            <td>GRAND TOTAL</td>
                            <td><span class="numbers">₱ {{ tt.total !== "" ? Number(tt.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="area_terms">
                    <div class="terms" v-for="(tt, index) in pg.term">
                        <div class="title">{{ tt.title }}</div>
                        <div class="brief" style="white-space: pre-line;">{{ tt.brief }}</div>
                        <div class="listing" style="white-space: pre-line;">{{ tt.list }}</div>
                    </div>
                </div>

                <div class="area_payment" v-if="pg.payment_term.page !== undefined">
                    <table class="tb_payment">
                        <tbody>
                        <tr>
                            <td colspan="2">Payment Terms:</td>
                            <td>
                                <div>
                                    <span v-for="(tt, index) in pg.payment_term.payment_method">{{ tt }}</span>
                                </div>
                               
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                            {{ pg.payment_term.brief }}
                            </td>
                        </tr>
                        <tr>
                            <td>Notes:</td>
                            <td>
                                <b>For Cheque</b><br>
                                Kindly Address to<br>
                                Feliix Inc.
                            </td>
                            <td>
                                <b>For Bank Details for Wiring</b>

                                <div class="acount_info" v-for="(tt, index) in pg.payment_term.list">
                                    <span class="account_name">{{ tt.bank_name }}</span>
                                    <span>: </span>
                                    <div class="first_line">
                                    {{ tt.first_line }}
                                    </div>
                                    <div class="second_line">{{ tt.second_line }}</div>
                                    <div class="third_line">{{ tt.third_line }}</div>
                                </div>

                           
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="area_conforme" style="margin-top: 60px;">
                    <div class="conforme"
                         v-if="(pg.sig != undefined ? pg.sig.item_client.length : 0)  + (pg.sig != undefined ?  pg.sig.item_company.length : 0) > 0">
                        CONFORME
                    </div>

                    <div class="client_signature" v-if="(pg.sig != undefined ? pg.sig.item_client.length : 0) > 0">

                        <div class="signature" v-for="(tt, index) in pg.sig.item_client">
                            <div class="pic"></div>
                            <div class="name">{{ tt.name }}</div>
                            <div class="line1">{{ tt.position }}</div>
                            <div class="line2">{{ tt.phone }}</div>
                            <div class="line3">{{ tt.email }}</div>
                        </div>

                    </div>

                    <div class="company_signature" v-if="(pg.sig != undefined ? pg.sig.item_company.length : 0) > 0">

                        <div class="signature" v-for="(tt, index) in pg.sig.item_company">
                            <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                            <div class="name">{{ tt.name }}</div>
                            <div class="line1">{{ tt.position }}</div>
                            <div class="line2">{{ tt.phone }}</div>
                            <div class="line3">{{ tt.email }}</div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="qn_footer">

                <div class="foot_divider"></div>
                <div class="line1">{{ footer_first_line }}</div>
                <div class="line2">{{ footer_second_line }}</div>
                <div class="qn_page_number">{{ index + 1 }}</div>

            </div>

        </div>


    </div>


    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_product_catalog">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1200px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

                <div class="modal-header">

                    <h4 class="modal-title" id="myLargeModalLabel">Product Catalog</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                        <input type="text" placeholder="Code" v-model="fil_code">

                        <select v-model="fil_brand">
                            <option value="">Choose Brand...</option>
                            <option v-for="(item, index) in brands">{{ item.brand }}</option>
                        </select>

                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                data-width="100%" title="Choose Tag(s)..." id="tag01" v-model="fil_tag">

                            <optgroup label="BY INSTALL LOCATION">
                                <option value="CEILING">CEILING</option>
                                <option value="FLOOR">FLOOR</option>
                                <option value="INDOOR">INDOOR</option>
                                <option value="INGROUND">INGROUND</option>
                                <option value="OUTDOOR">OUTDOOR</option>
                                <option value="STREET">STREET</option>
                                <option value="TABLE">TABLE</option>
                                <option value="WALL">WALL</option>
                                <option value="BLDG. FAÇADE">BLDG. FAÇADE</option>
                                <option value="CABINET">CABINET</option>
                                <option value="OTHER FURNITURES ">OTHER FURNITURES</option>
                                <option value="UNDERWATER">UNDERWATER</option>
                            </optgroup>

                            <optgroup label="INSTALL METHOD">
                                <option value="POLE-MOUNTED">POLE-MOUNTED</option>
                                <option value="RECESSED">RECESSED</option>
                                <option value="SURFACE-MOUNTED">SURFACE-MOUNTED</option>
                                <option value="SUSPENDED">SUSPENDED</option>
                                <option value="STAND-ALONE">STAND-ALONE</option>
                            </optgroup>

                            <optgroup label="BY TYPE / FUNCTION">
                                <option value="ASSEMBLED">ASSEMBLED</option>
                                <option value="BOLLARD">BOLLARD</option>
                                <option value="LED BULB">LED BULB</option>
                                <option value="CUSTOMIZED">CUSTOMIZED</option>
                                <option value="DIMMER">DIMMER</option>
                                <option value="DIRECTIONAL">DIRECTIONAL</option>
                                <option value="DISPLAY SPOTLIGHT">DISPLAY SPOTLIGHT</option>
                                <option value="LED DRIVER">LED DRIVER</option>
                                <option value="FLOOD LIGHT">FLOOD LIGHT</option>
                                <option value="HIGHBAY LIGHT">HIGHBAY LIGHT</option>
                                <option value="LED STRIP">LED STRIP</option>
                                <option value="LINEAR LIGHT">LINEAR LIGHT</option>
                                <option value="PANEL LIGHT">PANEL LIGHT</option>
                                <option value="PROJECTION LIGHT">PROJECTION LIGHT</option>
                                <option value="TRACK LIGHT">TRACK LIGHT</option>
                                <option value="TROFFER LIGHT">TROFFER LIGHT</option>
                                <option value="LINEAR LIGHT">LINEAR LIGHT</option>
                                <option value="WALL WASHER">WALL WASHER</option>
                                <option value="LIGHTBOX">LIGHTBOX</option>
                                <option value="EMERGENCY LIGHT">EMERGENCY LIGHT</option>
                                <option value="UV LED">UV LED</option>
                                <option value="ALUMINUM PROFILE">ALUMINUM PROFILE</option>
                                <option value="SPECIALTY LIGHT">SPECIALTY LIGHT</option>
                                <option value="LED TUBE">LED TUBE</option>
                                <option value="STAGE LIGHT">STAGE LIGHT</option>
                                <option value="AUDIO EQUIPMENT">AUDIO EQUIPMENT</option>
                            </optgroup>

                            <optgroup label="ACCESSORY">
                                <option value="FUNCTIONAL ACCESSORY">FUNCTIONAL ACCESSORY</option>
                                <option value="INSTALL ACCESSORY">INSTALL ACCESSORY</option>
                                <option value="REPLACEMENT PART">REPLACEMENT PART</option>
                            </optgroup>

                        </select>

                        <a class="btn small green" @click="filter_apply_new()">Search</a>

                    </div>

                    <div class="list_function" style="margin: 7px 0;">
                        <div class="pagenation">
                            <a class="prev" :disabled="product_page == 1" @click="pre_page(); filter_apply();">Prev 10</a>
                            <a class="page" v-for="pg in product_pages_10" @click="product_page=pg; filter_apply(pg);"
                               v-bind:style="[pg == product_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg
                                }}</a>
                            <a class="next" :disabled="product_page == product_pages.length" @click="nex_page(); filter_apply();">Next
                                10</a>
                        </div>
                    </div>


                    <div>
                        <table id="tb_product_list" class="table  table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Information</th>
                                <th>Specification</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in displayedPosts">

                                <td><img
                                    :src="img_url + item.photo1">
                                </td>
                                <td>
                                    <ul>
                                        <li>
                                            ID:
                                        </li>
                                        <li>
                                        {{ item.id }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Code:
                                        </li>
                                        <li>{{ item.code }}</li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Category:
                                        </li>
                                        <li v-if="item.category == 'Lighting'">
                                            {{ item.category}}
                                        </li>
                                        <li v-if="item.category != 'Lighting'">
                                            {{ item.category}} >> {{ item.sub_category_name}}
                                        </li> <!----></ul>
                                    <ul>
                                        <li>
                                            Tags:
                                        </li>
                                        <li><span v-for="(it, index) in item.tags">{{ it }}</span></li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Brand:
                                        </li>
                                        <li>
                                        {{ item.brand }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Created:
                                        </li>
                                        <li>
                                        {{ item.created_at }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Updated:
                                        </li>
                                        <li>
                                        {{ item.updated_at }}
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul v-for="(att, index) in item.attribute_list">
                                        <li>
                                            {{ att.category }}:
                                        </li>
                                        <li v-if="att.value.length > 1">
                                            <span v-for="(att_value, index) in att.value">{{att_value}}</span>
                                        </li>
                                        <li v-if="att.value.length == 1">
                                            <template v-for="(att_value, index) in att.value">{{att_value}}</template>
                                        </li>

                                    </ul>
                                </td>
                                <td>
                                    <span v-show="show_ntd === true">CP: {{ item.price_ntd }}<br></span>
                                    <span>SRP: {{ item.price }}<br></span>
                                    <span>QP: {{ item.quoted_price }}<br></span>
                                </td>
                                <td>
                                    <button id="edit01" @click="btnEditClick(item)"><i aria-hidden="true" class="fas fa-caret-right"></i></button>
                                </td>
                            </tr>
                            

                            </tbody>
                        </table>

                    </div>

<!--
                    <div>
                        <table id="tb_product_list" class="table  table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Information</th>
                                <th>Specification</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in displayedPosts">
                                <td>
                                    <img :src="baseURL + item.photo1" v-if="item.photo1">
                                </td>
                                <td>
                                    <ul>
                                        <li>
                                            ID:
                                        </li>
                                        <li>
                                            {{ item.id }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Code:
                                        </li>
                                        <li>
                                            {{ item.code }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Category:
                                        </li>
                                        <li v-if="item.category == 'Lighting'">
                                            {{ item.category}}
                                        </li>
                                        <li v-if="item.category != 'Lighting'">
                                            {{ item.category}} >> {{ item.sub_category_name}}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Tags:
                                        </li>
                                        <li>
                                            <span v-for="(it, index) in item.tags">{{ it }}</span>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Brand:
                                        </li>
                                        <li>
                                            {{ item.brand }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Created:
                                        </li>
                                        <li>
                                            {{ item.created_at }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Updated:
                                        </li>
                                        <li>
                                            {{ item.updated_at }}
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <ul v-for="(att, index) in item.attribute_list">
                                        <li>
                                            {{ att.category }}:
                                        </li>
                                        <li v-if="att.value.length > 1">
                                            <span v-for="(att_value, index) in att.value">{{att_value}}</span>
                                        </li>
                                        <li v-if="att.value.length == 1">
                                            <template v-for="(att_value, index) in att.value">{{att_value}}</template>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <span>SRP: {{ item.price }}<br></span>
                                    <span>QP: {{ item.quoted_price }}<br></span>
                                </td>
                                <td>
                                    <button id="edit01"><i
                                            class="fas fa-caret-right"></i></i>
                                    </button>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                            -->

                </div>

            </div>


        </div>

    </div>


    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_product_display">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1200px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">
                
            <template v-if="product.variation_mode != 1">
            <div class="upper_section">
                    <div class="imagebox">
                        <div class="selected_image">
                        <img :src="url">
                    </div>
                    <div class="image_list">
                        <img v-if="product.photo1" :src="img_url + product.photo1" @click="change_url(product.photo1)"/>
                        <img v-if="product.photo2" :src="img_url + product.photo2" @click="change_url(product.photo2)"/>
                        <img v-if="product.photo3" :src="img_url + product.photo3" @click="change_url(product.photo3)"/>
                        <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                    </div>
                    </div>
                    <div class="infobox">
                        <div class="basic_info"><h3>{{product.code}}</h3> <h6>{{product.brand}}</h6> 
                        <h6 v-if="category == 'Lighting'">{{ product.category}}</h6>
                        <h6 v-if="category != 'Lighting'">{{ product.category}} >> {{ product.sub_category_name}}</h6>
                            <!---->
                            <div class="tags"><span v-for="(it, index) in product.tags">{{ it }}</span></div>
                        </div>
                        <ul class="price_stock">
                            <li>
                                Retail Price: <span>{{product.price}}</span><span></span></li>
                            <li>
                                Quoted Price: <span>{{product.quoted_price}}</span><span></span></li>
                        </ul>
                        
                        <ul class="variants" style="display: none;">
                            <li>
                                Select:
                            </li>
                            <li>Beam Angle</li><!---->
                            <li><select class="form-control">
                                <option value=""></option>
                            </select></li>
                             <li>CCT</li><!---->
                            <li style="display: none;"><select class="form-control">
                                <option value=""></option>
                            </select></li> <!---->
                            <li>Color Finish</li>
                            <li style="display: none;"><select class="form-control">
                                <option value=""></option>
                            </select></li> <!----><!----><!----></ul>


                        <div class="btnbox">
                            <ul>
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image()">Add with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image()">Add without Image</button>
                                </li>
                            </ul>

                            <ul v-if="product.variation_mode == 1">
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image('all')">Add all spec. with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image('all')">Add all spec. without Image</button>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="middle_section"><h5>Specification</h5>
                    <table>
                        <tbody>
                            <template v-for="(item, index) in specification">
                                <tr>
                                    <td>
                                        {{item.k1}}
                                    </td>
                                    <td>
                                        {{item.v1}}
                                    </td>
                                    <td>
                                        {{item.k2}}
                                    </td>
                                    <td> {{item.v2}}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div> <!---->
                <div class="lower_section"><h5>Description</h5>
                    <p>
                    {{ product.description }}
                    </p>
                </div>
                </template>
<template v-if="product.variation_mode == 1">
                <div class="upper_section">

                    <div class="imagebox">
                        <div class="selected_image">
                            <img :src="url">
                        </div>
                        <div class="image_list">
                        <img v-if="product.photo1" :src="img_url + product.photo1" @click="change_url(product.photo1)"/>
                        <img v-if="product.photo2" :src="img_url + product.photo2" @click="change_url(product.photo2)"/>
                        <img v-if="product.photo3" :src="img_url + product.photo3" @click="change_url(product.photo3)"/>
                            <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                        </div>

                    </div>


                    <div class="infobox">
                        <div class="basic_info">
                        <h3>{{product.code}}</h3> <h6>{{product.brand}}</h6> 
                            <h6 v-if="category == 'Lighting'">{{ product.category}}</h6>
                            <h6 v-if="category != 'Lighting'">{{ product.category}} >> {{ product.sub_category_name}}</h6>
                            <div class="tags">
                                <span v-for="(it, index) in product.tags">{{ it }}</span>
                            </div>
                        </div>

                        <ul class="price_stock">

                            <li>
                                Retail Price: <span>{{product.price}}</span><span></span>
                            </li>

                            <li>
                                Quoted Price: <span>{{product.quoted_price}}</span><span></span>
                            </li>

                        </ul>

                        <ul class="variants">
                            <li>
                                Select:
                            </li>
                            <li v-if="product.variation1_value[0] !== '' && product.variation1_value[0] !== undefined">
                                {{ product.variation1 !== 'custom' ? product.variation1 : product.variation1_custom}}
                            </li>
                            <li v-show="product.variation1_value[0] !== '' && product.variation1_value[0] !== undefined">
                                <select class="form-control" v-model="v1" @change="change_v()">
                                    <option value=""></option>
                                    <option v-for="(item, index) in product.variation1_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>
                            <li v-if="product.variation2_value[0] !== '' && product.variation2_value[0] !== undefined">
                                {{ product.variation2 !== 'custom' ? product.variation2 : product.variation2_custom }}
                            </li>
                            <li v-show="product.variation2_value[0] !== '' && product.variation2_value[0] !== undefined">
                                <select class="form-control" v-model="v2" @change="change_v()">
                                    <option value=""></option>
                                    <option v-for="(item, index) in product.variation2_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>
                            <li v-if="product.variation3_value[0] !== '' && product.variation3_value[0] !== undefined">
                                {{ product.variation3 !== 'custom' ? product.variation3 : product.variation3_custom }}
                            </li>
                            <li v-show="product.variation3_value[0] !== '' && product.variation3_value[0] !== undefined">
                                <select class="form-control" v-model="v3" @change="change_v()">
                                    <option value=""></option>
                                    <option v-for="(item, index) in product.variation3_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>

                            <template v-for="(item, index) in product.accessory_infomation" v-if="show_accessory">
                                <li>{{ item.category }}</li>
                                <li>
                                    <select class="selectpicker" data-width="100%" :id="'tag'+index">
                                        <option :data-thumbnail="detail.url" v-for="(detail, index) in item.detail[0]">
                                            {{detail.code}}
                                        </option>
                                    </select>
                                </li>
                            </template>

                        </ul>

                        <div class="btnbox">
                            <ul>
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image()">Add with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image()">Add without Image</button>
                                </li>
                            </ul>

                            <ul>
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image('all')">Add all spec. with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image('all')">Add all spec. without Image</button>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>


                <div class="middle_section" v-if="specification.length > 0">
                    <h5>Specification</h5>

                    <table>
                        <tbody>
                        <template v-for="(item, index) in specification">
                            <tr>
                                <td>
                                    {{item.k1}}
                                </td>
                                <td>
                                    {{item.v1}}
                                </td>
                                <td>
                                    {{item.k2}}
                                </td>
                                <td> {{item.v2}}</td>
                            </tr>
                        </template>

                        </tbody>

                    </table>

                </div>

                <div class="middle_section" v-if="product.related_product">
                    <h5>Related Products</h5>

                    <div id="carouselExampleControls" class="carousel slide">

                        <div class="carousel-inner">

                            <div v-for='(g, groupIndex) in product.groupedItems'
                                 :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                <div class="row custom">
                                    <div class="col custom" v-for='(item, index) in g'>
                                        <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                        <div>
                                            <a :href="'product_display?id=' + item.id">
                                                {{ item.code }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                           data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                           data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>


                <div class="lower_section" v-if="(product.notes != null && product.notes != '') || product.description != ''">
                    <h5>Description</h5>
                    <p>
                        {{ product.description }}
                    </p>

                    <p v-if="product.notes != null && product.notes != ''">
                        Notes: {{ product.notes }}
                    </p>
                    <!--
                    <div class="desc_imgbox">
                        <img src="images/realwork.png">
                        <img src="images/realwork.png">
                        <img src="images/wash_hands.png">
                        <img src="images/realwork.png">
                    </div>
                    -->
                </div>
                </template>

            </div>
            
        </div>


    </div>


</div>


</body>

<script>
    $(".btn").click(function () {

        if ($("#collapseme").hasClass("show")) {
            $("#collapseme").removeClass("show");
        } else {
            $("#collapseme").addClass("show");
        }
    });

    window.onafterprint = (event) => {
        app.show_title = true;
    };


</script>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/quotation_v2.js"></script>
</html>
