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

            $username = $decoded->data->username;
            $position = $decoded->data->position;
            $department = $decoded->data->department;

            if($decoded->data->limited_access == true)
                header( 'location:index' );

            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;

        }
        catch (Exception $e){

            header( 'location:index' );
        }

        $access6 = false;

        // QOUTE AND PAYMENT Management
        if(trim(strtoupper($department)) == 'SALES')
        {
            if(trim(strtoupper($position)) == 'SALES COORDINATOR'
            || trim(strtoupper($position)) == 'JR. ACCOUNT EXECUTIVE'
            || trim(strtoupper($position)) == 'ACCOUNT EXECUTIVE'
            || trim(strtoupper($position)) == 'SR. ACCOUNT EXECUTIVE'
            || trim(strtoupper($position)) == 'ASSISTANT SALES MANAGER'
            || trim(strtoupper($position)) == 'SALES MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'LIGHTING')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING MANAGER' || trim(strtoupper($position)) == 'LIGHTING MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'OFFICE')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT OFFICE SYSTEMS MANAGER' || trim(strtoupper($position)) == 'OFFICE SYSTEMS MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'DESIGN')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT BRAND MANAGER' || trim(strtoupper($position)) == 'BRAND MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'ENGINEERING')
        {
            if(trim(strtoupper($position)) == "ENGINERING MANAGER" || trim(strtoupper($position)) == "ENGINEERING MANAGER")
            {
                $access6 = true;
            }
     
        }

        if(trim(strtoupper($department)) == 'ADMIN')
        {
            if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'TW')
        {
            if(trim(strtoupper($position)) == 'SUPPLY CHAIN MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim($department) == '')
        {
            if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
            {
                $access6 = true;
            }
        }

        if($user_id == 1 || $user_id == 99 || $user_id == 41 || $user_id == 9 || $user_id == 190)
            $access6 = true;

        if($access6 == false)
            header( 'location:index' );


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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="Bookmark" href="images/favicon.ico" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="images/iosicon.png"/>

<!-- SEO -->
<title>Quotation and Payment Management version 2</title>
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
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
    //toggleme($('.list_function .new_function a.filter'),$('.list_function .dialog.A'),'show');
    //toggleme($('.list_function .new_function a.sort'),$('.list_function .dialog.B'),'show');

    dialogshow($('.list_function .new_function a.filter'),$('.list_function .dialog.A'));
    dialogshow($('.list_function .new_function a.sort'),$('.list_function .dialog.B'));

    $('.tablebox').click(function(){
        $('.list_function .dialog').removeClass('show');
    })
    
})
</script>

    <style>
        input.alone[type=radio]::before, input.alone[type=checkbox]::before {
            color: black;
        }

        .tableframe .tablebox.lv1 a, .tableframe .tablebox.lv1 a:link {
            color: #1e6ba8;
            display: inline-block;
        }

        .tableframe .tablebox.lv1 li{
            min-width: 150px;
            color:#14456c;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(1) {
                    min-width: 40px;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(2) {
                    min-width: 130px;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(3), .tableframe .tablebox.lv1 li:nth-of-type(4), .tableframe .tablebox.lv1 li:nth-of-type(16), .tablebox.lv1 li:nth-of-type(17), .tableframe .tablebox.lv1 li:nth-of-type(19), .tableframe .tablebox.lv1 li:nth-of-type(20){
                    min-width: 280px;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(5) {
                    min-width: 100px;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(6) {
                    min-width: 160px;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(7) {
                    min-width: 200px;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(20) {
                    padding-left: 10px;
                    padding-right: 10px;
                    text-align: left;
                }

        .tableframe .tablebox.lv1 li:nth-of-type(11) span, .tableframe .tablebox.lv1 li:nth-of-type(12) span, .tableframe .tablebox.lv1 li:nth-of-type(13) span, .tableframe .tablebox.lv1 li:nth-of-type(14) span, .tableframe .tablebox.lv1 li:nth-of-type(18) span {
                    display: block;
                }

        .block.B .tablebox.lv1 li, .block.D .tablebox.lv1 li, .block.E .tablebox.lv1 li, .block.F .tablebox.lv1 li {
                    min-width: auto;
                }

        .tableframe .tablebox.lv1 ul.head li:nth-of-type(20) {
            text-align: center;
        }

        .bodybox {
            min-height: 130vh!important;
        }

        .bodybox .mask {
            position: absolute;
            background: rgba(0, 0, 0, 0.6);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            
        }

        #modal_Details {
           
            position: absolute;
            top: 150px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        #modal_Details > .modal-content {
            width: 90%;
            max-height: 95vh;
            margin: auto;
            padding: 20px 25px;
            background-color: white;
            overflow-y: auto;
            position: relative;
        }

        .modal-content > a{
            font-size: 18px;
            position: absolute;
            top: 20px;
            right: 25px;
        }

        .modal-content > .tags a {
            font-size: 18px;
            color: black;
            font-weight: 700;
            padding: 8px 16px;
            background-color: #E4F1F6;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            border: 2px solid #1E6BA8;
            border-bottom: 0;
        }

        .modal-content > .tags a.focus {
            color: #FFF;
            background-color: #1E6BA8;
        }

        .modal-content > .block {
            width: 100%;
            border: 2px solid #1E6BA8;
            background-color: #FFF;
            padding: 15px;
        }

        .modal-content .block .formbox dt {
            font-size: 16px;
            font-weight: 700;
            margin-top: 0;
            padding-bottom: 8px;
        }

        .modal-content .block .formbox {
            border: none;
            padding: 0;
        }

        .modal-content .block .formbox dd {
            border: none;
            margin-bottom: 10px;
            text-align: left;
        }

        .modal-content .block .formbox dd select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        }

        .modal-content .block .formbox dd select {
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 14px;
            height: 30px;
        }

        .modal-content .block .formbox dd input[type="date"] {
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 14px;
            height: 30px;
            margin-bottom: 10px;
            width: 200px;
        }

        .modal-content .block .formbox dd textarea {
            border: 1px solid #707070;
            padding: 3px;
            font-size: 14px;
            height: 60px;
        }

        .block.B .box-amount div{
            display: flex;
            align-items: center;
            padding: 15px 0;
        }

        .block.B .box-amount span{
            display: inline-block;
            font-size:16px;
            font-weight: 700;
            width: 120px;
            text-align: center;
        }

        .block.B .box-amount input[type="number"]{
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 16px;
            height: 30px;
            margin: 0 20px 0 10px;
            width: 130px;
        }

        .block.B .box-amount input[type="text"]{
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 16px;
            height: 30px;
            margin: 0 20px 0 10px;
            width: 410px;
        }

        .block.B .box-amount a{
            font-size: 16px;
        }


        .block.B .list_function, .block.D .list_function{
            margin-top: 10px;
            margin-bottom: -8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .block.B .list_function::after, .block.D .list_function::after {
            display: none;
        }

        .box-search input[type="text"]{
            width: 180px;
            font-size: 12px;
            border: 1px solid #707070;
        }

        .box-search button{
            margin-left: 15px;
            font-size: 14px;
            padding: 1px 5px;
            font-weight: 800;
            color: #707070;
            text-align: center;
            border: 2px solid #707070;
            border-left-width: 1px;
            border-right-width: 1px;
        }

        .modal .tags a {
            margin-right: 10px;
        }

        .modal .tags a:last-of-type {
            margin-right: 0;

        }

        .list_function .new_function {
            float: left;
            display: inline-block;
            position: relative;
            vertical-align: bottom;
            margin-right: 20px;
            margin-top: -20px;
        }

        .list_function .new_function a.filter {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_function a.sort  {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }


        .list_function .new_function a.exporting {
            width: 46px;
            height: 46px;
            background-color: #00811e;
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url(images/ui/btn_export_white.svg);
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .pub-con {
            box-sizing: border-box;
            background-size: 100%;
            text-align: center;
            position: relative;
        }

        .input-zone {
            width: 5rem;
            background-size: 2.13rem;
            border-radius: 0.38rem;
            border: 0.06rem solid rgba(112, 112, 112, 1);
            position: relative;
            color: #1E6BA8;
            font-size: 0.88rem;
            box-sizing: border-box;
        }

        .input {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            z-index: 2;
        }

        .pad {
            padding: 0.5rem 1.7rem 0 0rem;
            font-size: 0.88rem;
        }

        .btn-container {
            margin: 0.69rem auto;
            text-align: center;
        }

        .btn-container .btn {
            width: 10.56rem;
            height: 2.5rem;
            border-radius: 1.25rem;
            border: none;
            color: #ffffff;
        }

        .btn-container .btn.btn-gray {
            background: rgba(201, 201, 201, 1);
        }

        .btn-container .btn.btn-blue {
            background: linear-gradient(
                    180deg,
                    rgba(128, 137, 229, 1) 0%,
                    rgba(87, 84, 196, 1) 100%
            );
            font-size: 1rem;
        }

        .tips {
            margin-top: 1.69rem;
        }

        .file-list {
            font-size: 0.88rem;
            color: #1E6BA8;
        }

        .file-list .file-item {
            margin-top: 0.63rem;
        }

        .file-list .file-item p {
            line-height: 1.25rem;
            position: relative;
        }

        .file-list img {
            width: 1.25rem;
            cursor: pointer;
        }

        .file-list img.upload-delete {
            position: absolute;
            bottom: 0;
            margin: 0 auto;
            margin-left: 1rem;
        }

        .progress-wrapper {
            position: relative;
            height: 0.5rem;
            border: 0.06rem solid #1E6BA8;
            border-radius: 1px;
            box-sizing: border-box;
            width: 87%;
        }

        .progress-wrapper .progress-progress {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0%;
            border-radius: 1px;
            background-color: #1E6BA8;
            z-index: 1;
        }

        .progress-rate {
            font-size: 14px;
            height: 100%;
            z-index: 2;
            width: 12%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-rate span {
            display: inline-block;
            width: 100%;
            text-align: right;
        }

        .progress-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-list img.upload-success {
            margin-left: 0;
        }

        body.fourth .mainContent > .block {
            margin-top: 20px;
        }

        .block .tablebox > ul > li.display_file{
            padding-left: 10px;
            padding-right: 10px;
            text-align: left;
        }

        .block .tablebox > ul > li.display_file span{
            display: block;
        }

        .block .tablebox.lv1 ul.x_deal li {
            background: #E5F7EB !important;
        }

        .block .tablebox.lv1 ul.no_dp li {
            background: #EF88BE4D !important;
        }

        div.formbox dd.range {
            display: flex;
            justify-content: space-between;
        }

        div.formbox dd.range input {
            display: inline-block;
            width: 110px;
        }

        div.formbox dd.range select {
            display: inline-block;
            width: 60px;
            padding-left: 15px;
        }

        div.formbox dd.range span {
            display: inline-block;
            font-size: 14px;
            font-weight: 500;
            color: var(--fth04);
        }

f
    </style>



    <script>
        function ToggleWindow(mode, obj) {

            if (mode == 1) {
                $(".mask").toggle();
                $(obj).toggle();
                $("#modal_Details .block.A").show();
                $("#modal_Details .block.B").hide();
                $("#modal_Details .block.C").hide();
                $("#modal_Details .block.D").hide();
                $("#modal_Details .block.E").hide();
                $("#modal_Details .block.F").hide();
            } else if (mode == 2) {

                if (obj == "A") {
                    $("#modal_Details .block.A").show();
                    $("#modal_Details .block.B").hide();
                    $("#modal_Details .block.C").hide();
                    $("#modal_Details .block.D").hide();
                    $("#modal_Details .block.E").hide();
                    $("#modal_Details .block.F").hide();
                } else if (obj == "B") {
                    $("#modal_Details .block.B").show();
                    $("#modal_Details .block.A").hide();
                    $("#modal_Details .block.C").hide();
                    $("#modal_Details .block.D").hide();
                    $("#modal_Details .block.E").hide();
                    $("#modal_Details .block.F").hide();
                } else if (obj == "C") {
                    $("#modal_Details .block.C").show();
                    $("#modal_Details .block.A").hide();
                    $("#modal_Details .block.B").hide();
                    $("#modal_Details .block.D").hide();
                    $("#modal_Details .block.E").hide();
                    $("#modal_Details .block.F").hide();
                } else if (obj == "D") {
                    $("#modal_Details .block.D").show();
                    $("#modal_Details .block.A").hide();
                    $("#modal_Details .block.B").hide();
                    $("#modal_Details .block.C").hide();
                    $("#modal_Details .block.E").hide();
                    $("#modal_Details .block.F").hide();
                }
             else if (obj == "E") {
                    $("#modal_Details .block.E").show();
                    $("#modal_Details .block.A").hide();
                    $("#modal_Details .block.B").hide();
                    $("#modal_Details .block.C").hide();
                    $("#modal_Details .block.D").hide();
                    $("#modal_Details .block.F").hide();
                }
             else if (obj == "F") {
                    $("#modal_Details .block.F").show();
                    $("#modal_Details .block.A").hide();
                    $("#modal_Details .block.B").hide();
                    $("#modal_Details .block.C").hide();
                    $("#modal_Details .block.E").hide();
                    $("#modal_Details .block.D").hide();
                }
            }
        }
    </script>

</head>

<body class="fourth">
 	
<div id="app" class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="block ">
            <div class="list_function">
                <div class="new_function">
                    <a id="dialog_f1" class="filter"></a>
                    <div class="dialog A" id="add_f1"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Project Category</dt>
                                <dd>
                                    <select v-model="fil_category">
                                      <option value="0">
                                      <option v-for="item in categorys" :value="item.id" :key="item.category">
                                          {{ item.category }}
                                      </option>
                                    </select>
                                </dd>

                                <dt>Project Status</dt>
                                <dd>
                                    <select v-model="fil_status">
                                        <option value="0">
                                          <option v-for="item in statuses" :value="item.id" :key="item.project_status">
                                              {{ item.project_status }}
                                          </option>
                                          <option value="v">Verified Downpayment</option>
                                          <option value="w">Without Verified Downpayment</option>
                                        </select>
                                </dd>

                                <dt>Project Creator</dt>
                                <dd>
                                    <select v-model="fil_creator">
                                        <option value="">
                                        <option v-for="item in creators" :value="item.username"
                                                :key="item.username">
                                            {{ item.username }}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Has Verified Payment Proof?</dt>
                                <dd>
                                    <select v-model="fil_proof">
                                        <option value=""></option>
                                        <option value="A">Has Verified Downpayment Proof and Has Verified Fullpayment Proof</option>
                                        <option value="D">Has Verified Downpayment Proof but Not Have Verified Fullpayment Proof</option>
                                        <option value="F">Not Have Verified Downpayment Proof but Has Verified Fullpayment Proof</option>
                                        <option value="N">Not Have Verified Downpayment Proof and Not Have Verified Fullpayment Proof</option>
                                    </select>
                                </dd>

                                <dt>Range of Amount</dt>
                                <dd class="range">
                                    <input type="number" v-model="fil_amount_lower">

                                    <select v-model="fil_amount_lower_eq">
                                        <option value=""></option>
                                        <option value="s"><</option>
                                        <option value="se">≤</option>
                                    </select>

                                    <span>Amount</span>

                                    <select v-model="fil_amount_upper_eq">
                                        <option value=""></option>
                                        <option value="s"><</option>
                                        <option value="se">≤</option>
                                    </select>

                                    <input type="number" style="margin-right: 0;"  v-model="fil_amount_upper">
                                </dd>

                                <dt>Range of Down Payment</dt>
                                <dd class="range">
                                    <input type="number" v-model="fil_payment_lower">

                                    <select v-model="fil_payment_lower_eq">
                                        <option value=""></option>
                                        <option value="s"><</option>
                                        <option value="se">≤</option>
                                    </select>

                                    <span>Down Payment</span>

                                    <select v-model="fil_payment_upper_eq">
                                        <option value=""></option>
                                        <option value="s"><</option>
                                        <option value="se">≤</option>
                                    </select>

                                    <input type="number" style="margin-right: 0;" v-model="fil_payment_upper">
                                </dd>

                                <dt>Range of A/R</dt>
                                <dd class="range">
                                    <input type="number" v-model="fil_ar_lower">

                                    <select v-model="fil_ar_lower_eq">
                                        <option value=""></option>
                                        <option value="s"><</option>
                                        <option value="se">≤</option>
                                    </select>

                                    <span>A/R</span>

                                    <select v-model="fil_ar_upper_eq">
                                        <option value=""></option>
                                        <option value="s"><</option>
                                        <option value="se">≤</option>
                                    </select>

                                    <input type="number" style="margin-right: 0;" v-model="fil_ar_upper">
                                </dd>

                                <dt>Aging</dt>
                                <dd>
                                    <select v-model="fil_aging">
                                        <option value=""></option>
                                        <option value="0">Aging < 30 days</option>
                                        <option value="30">30 days ≤ Aging < 60 days</option>
                                        <option value="60">60 days ≤ Aging < 90 days</option>
                                        <option value="90">90 days ≤ Aging < 120 days</option>
                                        <option value="120">120 days ≤ Aging</option>
                                    </select>
                                </dd>


                   <!--         <dt style="margin-bottom:-18px;">Amount</dt>
                                <div class="half">
                                    <dt>lower bound</dt>
                                    <dd><input type="number" v-model="fil_amount_lower"></dd>
                                </div>

                                <div class="half">
                                    <dt>upper bound</dt>
                                    <dd><input type="number" v-model="fil_amount_upper"></dd>

                                </div>

                                <dt style="margin-bottom:-18px; margin-top: 3px;">Down Payment</dt>
                                <div class="half">
                                    <dt>lower bound</dt>
                                    <dd><input type="number" v-model="fil_payment_lower"></dd>
                                </div>

                                <div class="half">
                                    <dt>upper bound</dt>
                                    <dd><input type="number" v-model="fil_payment_upper"></dd>

                                </div>

                                <dt style="margin-bottom:-18px; margin-top: 3px;">A/R</dt>
                                <div class="half">
                                    <dt>lower bound</dt>
                                    <dd><input type="number" v-model="fil_ar_lower"></dd>
                                </div>

                                <div class="half">
                                    <dt>upper bound</dt>
                                    <dd><input type="number" v-model="fil_ar_upper"></dd>

                                </div>
                      -->

                                <dt>Keyword (only for project name)</dt>
                                <dd><input type="text" v-model="fil_keyowrd_p"></dd>

                                <dt>Keyword (only for final quotation)</dt>
                                <dd><input type="text" v-model="fil_keyowrd"></dd>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="filter_clear">Cancel</a><a class="btn small" @click="filter_remove">Clear</a><a class="btn small green" @click="filter_apply_new">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="new_function">
                    <a id="dialog_a1" class="sort"></a>
                    <div class="dialog B" id="add_a1"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_factor1">
                                            <option value="0"></option>
                                            <option value="1">
                                                Execution Period -- Start Date
                                            </option>
                                            <option value="2">
                                                Execution Period -- End Date
                                            </option>
                                            <option value="3">
                                                Amount
                                            </option>
                                            <option value="4">
                                                Down Payment
                                            </option>
                                            <option value="5">
                                                Payment
                                            </option>
                                            <option value="6">
                                                A/R
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_factor1_order">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt>2nd Criterion</dt>
                                    <dd>
                                        <select v-model="od_factor2">
                                            <option value="0"></option>
                                            <option value="1">
                                                Execution Period -- Start Date
                                            </option>
                                            <option value="2">
                                                Execution Period -- End Date
                                            </option>
                                            <option value="3">
                                                Amount
                                            </option>
                                            <option value="4">
                                                Down Payment
                                            </option>
                                            <option value="5">
                                                Payment
                                            </option>
                                            <option value="6">
                                                A/R
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_factor2_order">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="order_clear">Cancel</a><a class="btn small" @click="order_remove">Clear</a><a class="btn small green" @click="filter_apply">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="new_function"><a id="btn_export" class="exporting" @click="order_export()"></a></div>

                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply();">Prev 10</a>
                  
                    <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>
                  
                    <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                </div>
            </div>


            <!-- list -->
            <div class="tableframe">
              <div class="tablebox lv1">
                 <ul class="head">
                     <li><i class="micons">view_list</i></li>
                     <li>Project Category</li>
                     <li>Billing Name</li>
                     <li>Project Name</li>
                     <li>Status</li>
                     <li>Project Creator</li>
                     <li>Execution Period</li>
                     <li>Amount</li>
                     <li>Tax Withheld</li>
                     <li>Net Amount</li>
                     <li>Down Payment</li>
                     <li>DP Date</li>
                     <li>Full Payment</li>
                     <li>FP Date</li>
                     <li>A/R</li>
                     <li>Date of Data Submission<br>Aging</li>
                     <li>Expense (Commission)</li>
                     <li>Expense (Others)</li>
                     <li>PO from Client</li>
                     <li>Inv. Numbers</li>
                     <li>Other Files</li>
                     <li>Remarks/Status</li>
                 </ul>
                  <ul v-for='(receive_record, index) in displayedPosts' :class="[(receive_record.special == 's' ? 'x_deal' : ( receive_record.special == 'sn' ? 'no_dp' : ''))]">
                      <li><input type="radio" name="project_id" class="alone black"
                            @click="show_detail(receive_record.id)"></li>
                      <li>{{ receive_record.category }}</li>
                      <li>{{ receive_record.billing_name }}</li>
                      <li><a :href="'project02?p=' + receive_record.id" target="_blank">{{ receive_record.project_name }}</a></li>
                      <li>{{ receive_record.project_status }}</li>
                      <li>{{ receive_record.username }}</li>
                      <li>{{ receive_record.created_at }} ~ {{ receive_record.updated_at }}</li>
                      <li>{{ isNaN(parseFloat(receive_record.final_amount)) ? "" : Number(receive_record.final_amount).toLocaleString() }}</li>
                      <li>{{ isNaN(parseFloat(receive_record.tax_withheld)) ? "" : Number(receive_record.tax_withheld).toLocaleString() }}</li>
                      <li>{{ isNaN(parseFloat(receive_record.net_amount)) ? "" : Number(receive_record.net_amount).toLocaleString() }}</li>
                      <li>{{ isNaN(parseInt(receive_record.down_payment_amount)) ? "" : Number(receive_record.down_payment_amount).toLocaleString() }}</li>
                      <li><span v-for="(item,index) in receive_record.down_pay_date">{{ item }}</span></li>
                      <li>{{ isNaN(parseInt(receive_record.payment_amount)) ? "" : Number(receive_record.payment_amount).toLocaleString() }}</li>
                      <li><span v-for="(item,index) in receive_record.full_pay_date">{{ item }}</span></li>
                      <li>{{ isNaN(parseInt(receive_record.ar)) ? "" : Number(receive_record.ar).toLocaleString() }}</li>
                      <li v-if="receive_record.date_data_submission != ''">{{ receive_record.date_data_submission }}<br>{{ receive_record.aging }} days</li>
                      <li v-if="receive_record.date_data_submission == ''"></li>
                      <li>{{ parseInt(receive_record.apply_for_petty_commission) == 0 ? "" : Number(receive_record.apply_for_petty_commission).toLocaleString() }}</li>
                      <li>{{ parseInt(receive_record.apply_for_petty - receive_record.apply_for_petty_commission) == 0 ? "" : Number(receive_record.apply_for_petty - receive_record.apply_for_petty_commission).toLocaleString()  }}</li>
                      <li class="display_file">
                        <span v-for="(item,index) in receive_record.client_po_file">
                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">•{{item.filename}}</a>
                        </span>
                      </li>
                      <li><span v-for="(item,index) in receive_record.invoice">{{ item }}</span></li>
                      <li class="display_file">
                        <span v-for="(item,index) in receive_record.client_other_file">
                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">•{{item.filename}}</a>
                        </span>
                      </li>
                      <li><span v-for="(item,index) in receive_record.client_other">
                            <a v-if="item.kind == '3'">•{{ item.comment }} ({{ item.username }} at {{ item.created_at }})</a>
                        </span></li>
                  </ul>

                
             </div>
           </div>
           <!-- list end -->




            <!-- modal begin -->
            <div id="modal_Details" class="modal" v-if="view_detail == true">

                <!-- Modal content -->
                <div class="modal-content">

                    <a style="font-size: 18px;" @click="hide_detail()"><i aria-hidden="true" class="fa fa-times fa-lg" ></i></a>

                    <!-- tags js在 main.js -->
                    <div class="tags">
                        <a class="tag A" v-bind:class="{ focus: view_a }" @click="togle_a()">Upload Quotation</a>
                        <a class="tag B" v-bind:class="{ focus: view_b }" @click="togle_b()">Manage Quotation</a>
                        <a class="tag C" v-bind:class="{ focus: view_c }" @click="togle_c()">Submit Payment Proof</a>
                        <a class="tag D" v-bind:class="{ focus: view_d }" @click="togle_d()">Manage Payment Proof</a>
                        <a class="tag e" v-bind:class="{ focus: view_e }" @click="togle_e()">Manage Client's PO</a>
                        <a class="tag f" v-bind:class="{ focus: view_f }" @click="togle_f()">Other</a>
                    </div>


                    <div class="block A"  v-if="view_a == true">

                        <div class="formbox">
                            <dl>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" v-model="quote_remark"></textarea></dd>
                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: #000000; font-size: 16px; font-weight: 700; padding-bottom: 5px; margin-right: 10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input
                                                    class="input"
                                                    type="file"
                                                    name="quote_file"
                                                    value
                                                    placeholder="choose file"
                                                    ref="quote_file"
                                                    v-show="quote_canSub"
                                                    @change="quote_changeFile()"
                                                    multiple
                                            />
                                        </div>
                                </dd>

                                <div class="file-list">
                                    <div class="file-item" v-for="(item,index) in quote_fileArray" :key="index">
                                        <p>
                                            {{item.name}}
                                            <span
                                                    @click="quote_deleteFile(index)"
                                                    v-show="item.progress==0"
                                                    class="upload-delete"
                                            ><i class="fas fa-backspace"></i>
                                        </span>
                                        </p>
                                        <div class="progress-container" v-show="item.progress!=0">
                                            <div class="progress-wrapper">
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
                                            </div>
                                            <div class="progress-rate">
                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btnbox">
                                    <a class="btn" @click="quote_clear">Cancel</a>
                                    <a class="btn green" @click="quote_create">Upload</a>
                                </div>
                            </dl>

                        </div>
                    </div>


                    <div class="block B"  v-if="view_b == true">

                        <div class="box-amount">
                            <div>
                                <span>Amount: </span>
                                <input type="number" id="final_amount" v-model="record.final_amount">
                                <span>Tax Withheld: </span>
                                <input type="number" id="tax_withheld" v-model="record.tax_withheld">
                            </div>
                            <div>
                                <span>Billing Name: </span>
                                <input type="text" id="billing_name" v-model="record.billing_name">
                                <a class="btn small green" @click="final_amount()">Save</a>
                            </div>
                        </div>

                        <hr style="margin: 15px 0 30px;">

                        <div class="list_function">

                            <div class="box-search">
                                <input type="text" placeholder="Searching Keyword Here" v-model="quote_keyword">
                                <button @click="quote_search()"><i class="fas fa-filter"></i></button>
                            </div>

                            <div class="pagenation">
                                <a class="prev" :disabled="quote_page == 1" @click="quote_page < 1 ? quote_page = 1 : quote_page--">Previous</a>

                                <a class="page" v-for="pg in quote_pages" @click="quote_page=pg" v-bind:style="[pg == quote_page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                                <a class="next" :disabled="quote_page == quote_pages.length" @click="quote_page++">Next</a>
                            </div>
                        </div>

                        <div class="tableframe">
                            <div class="tablebox lv1">
                                <ul class="head">
                                    <li><i class="micons">view_list</i></li>
                                    <li>File Name</li>
                                    <li>Description</li>
                                    <li>Uploader</li>
                                    <li>Final Quotation</li>

                                </ul>
                                <ul v-for='(receive_record, index) in displayedQuote'>
                                    <li><input type="checkbox" name="quotation_id" class="alone black" :value="receive_record.id"></li>
                                    <li class="display_file">
                                        <span v-for="item in receive_record.items" style="display:block;">
                                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">•{{item.filename}}</a>
                                        </span>
                                    </li>
                                    <li>{{ receive_record.comment }}</li>
                                    <li>{{ receive_record.username }} at {{ receive_record.created_at }}</li>
                                    <li>{{ (receive_record.final_quotation == 0) ? "N" : "Y" }}</li>
                                </ul>
                                
                            </div>
                        </div>

                        <div class="btnbox">
                            <a class="btn green" @click="final_quotation()">Final Quotation</a>
                            <a class="btn red" @click="delete_quotation()">Delete</a>
                        </div>

                    </div>


                    <div class="block C"  v-if="view_c == true">

                        <div class="formbox">
                            <dl>
                                <dt class="head">Type:</dt>
                                <dd>
                                    <select v-model="payment_type">
                                        <option value="0">Down Payment</option>
                                        <option value="1">Full Payment</option>
                                        <option value="2">2307</option>
                                    </select>
                                </dd>
                                <dt class="head">Remarks:</dt>
                                <dd><textarea name="" id="" v-model="prof_remark"></textarea></dd>
                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: #000000; font-size: 16px; font-weight: 700; padding-bottom: 5px; margin-right: 10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input
                                                    class="input"
                                                    type="file"
                                                    name="prof_file"
                                                    value
                                                    placeholder="choose file"
                                                    ref="prof_file"
                                                    v-show="prof_canSub"
                                                    @change="prof_changeFile()"
                                                    multiple
                                            />
                                        </div>
                                </dd>

                                <div class="file-list">
                                    <div class="file-item" v-for="(item,index) in prof_fileArray" :key="index">
                                        <p>
                                            {{item.name}}
                                            <span
                                                    @click="prof_deleteFile(index)"
                                                    v-show="item.progress==0"
                                                    class="upload-delete"
                                            ><i class="fas fa-backspace"></i>
                                        </span>
                                        </p>
                                        <div class="progress-container" v-show="item.progress!=0">
                                            <div class="progress-wrapper">
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
                                            </div>
                                            <div class="progress-rate">
                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btnbox">
                                    <a class="btn" @click="prof_clear">Cancel</a>
                                    <a class="btn green" @click="prof_create">Submit</a>
                                </div>
                            </dl>

                        </div>
                    </div>


                    <div class="block D"  v-if="view_d == true">

                        <div class="list_function">

                            <div class="box-search">
                                <input type="text" placeholder="Searching Keyword Here" v-model="payment_keyword">
                                <button @click="payment_search()"><i class="fas fa-filter"></i></button>
                            </div>

                            <div class="pagenation">
                                <a class="prev" :disabled="pay_page == 1" @click="pay_page < 1 ? pay_page = 1 : pay_page--">Previous</a>

                                <a class="page" v-for="pg in pay_pages" @click="pay_page=pg" v-bind:style="[pg == pay_page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                                <a class="next" :disabled="pay_page == pay_pages.length" @click="pay_page++">Next</a>
                            </div>
                        </div>

                        <div class="tableframe">
                            <div class="tablebox lv1">
                                <ul class="head">
                                    <li><i class="micons">view_list</i></li>
                                    <li>Type</li>
                                    <li>Remarks</li>
                                    <li>Proof</li>
                                    <li>Uploader</li>
                                    <li>Status</li>
                                    <li>Amount Received</li>

                                </ul>
                                <ul v-for='(receive_record, index) in displayedPayment'>
                                    <li><input type="checkbox" name="payment_id" class="alone black" :value="receive_record.id"></li>
                                    <li>{{ (receive_record.kind == 0) ? "Down Payment" : ((receive_record.kind == 1) ? "Full Payment" : "2307") }}</li>
                                    <li>{{ receive_record.remark }}</li>
                                    <li class="display_file">
                                        <span v-for="item in receive_record.items" style="display: block;" v-if="view_proof || (item.username.toLowerCase() == username.toLowerCase())">
                                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">•{{item.filename}}</a>
                                        </span>
                                    </li>
                                    <li>{{ receive_record.username }} at {{ receive_record.created_at }}</li>
                                    <li>{{ (receive_record.checked == 0) ? "Under Checking" : ((receive_record.checked == 1) ? "Checked: True" : ((receive_record.checked == -1) ? "Checked: False" : "Under Checking")) }}</li>
                                    <li>{{ isNaN(parseInt(receive_record.amount)) ? "" : Number(receive_record.amount).toLocaleString() }}</li>
                                </ul>
                            

                            </div>
                        </div>

                        <div class="btnbox">
                            <a class="btn red" @click="payment_withdraw()">Delete</a>
                        </div>

                    </div>


                    <div class="block E" v-if="view_e == true">

                        <div class="formbox">
                            <dl>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" v-model="po_remark"></textarea></dd>
                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: #000000; font-size: 16px; font-weight: 700; padding-bottom: 5px; margin-right: 10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input
                                                    class="input"
                                                    type="file"
                                                    name="po_file"
                                                    value
                                                    placeholder="choose file"
                                                    ref="po_file"
                                                    v-show="po_canSub"
                                                    @change="po_changeFile()"
                                                    multiple
                                            />
                                        </div>
                                </dd>

                                <div class="file-list">
                                    <div class="file-item" v-for="(item,index) in po_fileArray" :key="index">
                                        <p>
                                            {{item.name}}
                                            <span
                                                    @click="po_deleteFile(index)"
                                                    v-show="item.progress==0"
                                                    class="upload-delete"
                                            ><i class="fas fa-backspace"></i>
                                        </span>
                                        </p>
                                        <div class="progress-container" v-show="item.progress!=0">
                                            <div class="progress-wrapper">
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
                                            </div>
                                            <div class="progress-rate">
                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btnbox">
                                    <a class="btn" @click="po_clear">Cancel</a>
                                    <a class="btn green" @click="po_create">Upload</a>
                                </div>

                            </dl>
                        </div>

                        <hr style="margin-bottom: 20px;">

                        <div class="list_function">

                            <div class="box-search">
                                <input type="text" placeholder="Searching Keyword Here" v-model="po_keyword">
                                <button @click="po_search()"><i class="fas fa-filter"></i></button>
                            </div>

                            <div class="pagenation">
                                <a class="prev" :disabled="po_page == 1" @click="po_page < 1 ? po_page = 1 : po_page--">Previous</a>

                                <a class="page" v-for="pg in po_pages" @click="po_page=pg" v-bind:style="[pg == po_page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                                <a class="next" :disabled="po_page == po_pages.length" @click="po_page++">Next</a>
                            </div>
                        </div>

                        <div class="tableframe">
                            <div class="tablebox lv1">
                                <ul class="head">
                                    <li><i class="micons">view_list</i></li>
                                    <li>File Name</li>
                                    <li>Description</li>
                                    <li>Uploader</li>
                                </ul>
                                <ul v-for='(receive_record, index) in displayedPo'>
                                    <li><input type="checkbox" name="po_id" class="alone black" :value="receive_record.id"></li>
                                    <li class="display_file">
                                        <span v-for="item in receive_record.items" style="display:block;">
                                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">•{{item.filename}}</a>
                                        </span>
                                    </li>
                                    <li>{{ receive_record.comment }}</li>
                                    <li>{{ receive_record.username }} at {{ receive_record.created_at }}</li>
                                </ul>

                            </div>
                        </div>

                        <div class="btnbox">
                            <a class="btn red" @click="delete_user_po()">Delete</a>
                        </div>

                    </div>



                    <div class="block F" v-if="view_f == true">

                        <div class="formbox">
                            <dl>
                                <dt class="head">Type:</dt>
                                <dd>
                                    <select v-model="other_type">
                                        <option value="2">Other Files</option>
                                        <option value="3">Remarks/Status</option>
                                        <option value="4">Date of Data Submission</option>
                                    </select>
                                </dd>
                                <dt class="head">Description:</dt>
                                <dd>
                                    <input type="date" v-if="other_type == 4" v-model="date_data_submission">
                                    <textarea name="" id="" v-model="other_remark"></textarea>
                                </dd>
                                <dd style="display: flex; justify-content: flex_start;" v-if="other_type == '2' || other_type == '4' ">
                                    <span style="color: #000000; font-size: 16px; font-weight: 700; padding-bottom: 5px; margin-right: 10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input
                                                    class="input"
                                                    type="file"
                                                    name="other_file"
                                                    value
                                                    placeholder="choose file"
                                                    ref="other_file"
                                                    v-show="other_canSub"
                                                    @change="other_changeFile()"
                                                    multiple
                                            />
                                        </div>
                                </dd>

                                <div class="file-list"  v-if="other_type == '2' || other_type == '4' ">
                                    <div class="file-item" v-for="(item,index) in other_fileArray" :key="index">
                                        <p>
                                            {{item.name}}
                                            <span
                                                    @click="other_deleteFile(index)"
                                                    v-show="item.progress==0"
                                                    class="upload-delete"
                                            ><i class="fas fa-backspace"></i>
                                        </span>
                                        </p>
                                        <div class="progress-container" v-show="item.progress!=0">
                                            <div class="progress-wrapper">
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
                                            </div>
                                            <div class="progress-rate">
                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btnbox">
                                    <a class="btn" @click="other_clear">Cancel</a>
                                    <a class="btn green" @click="other_create">Submit</a>
                                </div>
                            </dl>

                        </div>

                        <hr style="margin-bottom: 20px;">

                        <div class="list_function">

                            <div class="box-search">
                                <input type="text" placeholder="Searching Keyword Here" v-model="other_keyword">
                                <button @click="other_search()"><i class="fas fa-filter"></i></button>
                            </div>

                            <div class="pagenation">
                                <a class="prev" :disabled="other_page == 1" @click="other_page < 1 ? other_page = 1 : other_page--">Previous</a>

                                <a class="page" v-for="pg in other_pages" @click="other_page=pg" v-bind:style="[pg == other_page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                                <a class="next" :disabled="other_page == other_pages.length" @click="other_page++">Next</a>
                            </div>
                        </div>

                        <div class="tableframe">
                            <div class="tablebox lv1">
                                <ul class="head">
                                    <li><i class="micons">view_list</i></li>
                                    <li>Type</li>
                                    <li>File Name</li>
                                    <li>Description</li>
                                    <li>Submitter</li>
                                </ul>
                                <ul v-for='(receive_record, index) in displayedOther'>
                                    <li><input type="checkbox" name="other_id" class="alone black" :value="receive_record.id"></li>
                                    <li>{{ (receive_record.kind == 2) ? "Other Files" : (receive_record.kind == 3) ? "Remarks/Status" : "Date of Data Submission" }}</li>
                                    <li class="display_file">
                                        <span v-for="item in receive_record.items" style="display:block;">
                                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">•{{item.filename}}</a>
                                        </span>
                                    </li>
                                    <li>{{ receive_record.comment }}</li>
                                    <li>{{ receive_record.username }} at {{ receive_record.created_at }}</li>
                                </ul>

                            </div>
                        </div>

                        <div class="btnbox">
                            <a class="btn red" @click="delete_other()">Delete</a>
                        </div>

                    </div>



                </div>
            </div>
            <!-- modal end -->






        </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/quotation_and_payment_mgt_v2.js"></script>
<script src="js/a076d05399.js"></script>




</html>
