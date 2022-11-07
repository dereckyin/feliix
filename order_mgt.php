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
if($user_id == 48 || $user_id == 2 || $user_id == 1 || $user_id == 6 || $user_id == 3)
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
    <title>Order Management</title>
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css">

    <!-- jQuery和js載入 -->
    <!-- <script defer src="//code.jquery.com/jquery-1.11.3.min.js"></script> -->

    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js" defer></script>
    <script defer src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
            // toggleme($('.list_function .new_project a.add'),$('.list_function .dialog'),'show');
            // toggleme($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'),'show');
            // toggleme($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'),'show');

            dialogshow($('.list_function .new_project a.add'), $('.list_function .dialog.d-add'));
            dialogshow($('.list_function .new_project a.filter'), $('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .new_project a.sort'), $('.list_function .dialog.d-sort'));
            dialogshow($('.list_function .new_project a.edit'), $('.list_function .dialog.d-edit'));

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

        })
    </script>
    <style>
        .list_function .new_project {
            margin-top: -15px;
        }

        body.fourth .mainContent > .block {
            margin-top: 20px;
        }

        body.fourth .bodybox {
            min-height: 120vh;
        }

        .list_function .new_project a.edit {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_edit_green.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_project a.filter {
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

        .list_function .new_project a.sort {
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

        .tablebox.lv1 i {
            padding: 0;
            color: #000;
        }

        .tablebox.lv1 ul li:nth-of-type(1) {
            width: 130px;
        }

        .tablebox.lv1 ul li:nth-of-type(2), .tablebox.lv1 ul li:nth-of-type(5) {
            width: 350px;
        }

        .tablebox.lv1 ul li:nth-of-type(3) {
            min-width: 170px;
        }

        .tablebox.lv1 ul li:nth-of-type(4) {
            min-width: 100px;
            color: #000;
        }

        .tablebox.lv1 ul li:nth-of-type(6), .tablebox.lv1 ul li:nth-of-type(7) {
            color: #000;
            min-width: 200px;
        }

        .tableframe .tablebox ul li:nth-of-type(1) input[type='text'] {
            width: 90%;
            border-color: #1e6ba8;
            background-color: white;
        }

        .tableframe .tablebox ul li:nth-of-type(2) select {
            width: 90%;
            border-color: #1e6ba8;
            background-color: white;
            background-image: url(../images/ui/icon_form_select_arrow_blue.svg)
        }

        .tableframe .tablebox ul li:nth-of-type(6) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px;
            margin: 0 3px;
        }


        dd.relate_to > input[type="radio"] {
            border: none;
            margin-right: 0;
            vertical-align: 1px;
        }

        dd.relate_to > input[type="radio"]::before {
            color: #00811e;
        }

        dd.relate_to > span {
            font-size: 14px;
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            font-weight: 500;
        }

        dd.relate_to > span:first-of-type {
            margin-right: 20px;
        }

        dd.relate_to > select {
            margin-top: 5px;
        }

    </style>
</head>

<body class="fourth">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="block">
            <div class="list_function">

                <!-- 修改 -->
                <div class="new_project">
                    <a class="edit"></a>

                    <div id="edit_dialog" class="dialog d-edit">
                        <h6>Edit Order:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Target Order</dt>
                                <dd>
                                    <select v-model="temp_order" style="width: calc( 100% - 100px);">
                                        <option v-for="od in orders" :value="od">{{ od.serial_name }} {{ od.od_name }}</option>
                                    </select>

                                    <a class="btn small green" @click="edit_load()">Load</a>
                                </dd>

                                <dt>Order Name</dt>
                                <dd><input type="text" placeholder="" v-model="order.od_name"></dd>
                                <dt>Order Status</dt>
                                <dd>
                                    <select v-model="order.status">
                                        <option value="0">Ongoing</option>
                                        <option value="1">Pending</option>
                                        <option value="2">Completed</option>
                                    </select>
                                </dd>

                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="cancel_edit">Cancel</a>
                                <a class="btn small green" @click="edit_save">Save</a>
                            </div>
                        </div>
                      </div>

                </div>


                <!-- 篩選 -->
                <div class="new_project">
                    <a class="filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <!--
                                <dt>Project Category</dt>
                                <dd>
                                    <select  v-model="fil_project_category">
                                    <option value=""></option>
                                    <option v-for="item in categorys" :value="item.id" :key="item.category">
                                        {{ item.category }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Client Type</dt>
                                <dd>
                                    <select v-model="fil_client_type">
                                    <option value=""></option>
                                    <option v-for="item in client_types" :value="item.id" :key="item.client_type">
                                        {{ item.client_type }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Priority</dt>
                                <dd>
                                    <select v-model="fil_priority">
                                    <option value=""></option>
                                    <option v-for="item in priorities" :value="item.id" :key="item.priority">
                                        {{ item.priority }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Project Group</dt>
                                <dd>
                                    <select v-model="fil_group">
                                    <option value=""></option>
                                    <option value="0">Not Belong to Any Group</option>
                                    <option v-for="item in groups"  :value="item.id" :key="item.project_group">
                                        {{ item.project_group }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Project Status</dt>
                                <dd>
                                    <select v-model="fil_status">
                                    <option value=""></option>
                                    <option v-for="item in statuses" v-if="item.id != 6" :value="item.id" :key="item.project_status">
                                        {{ item.project_status }}
                                    </option>
                                    </select>
                                </dd>
                                -->

                                <dt>Project Category</dt>
                                <dd>
                                    <select v-model="fil_project_category">
                                        <option value=""></option>
                                        <option value="2">Lighting</option>
                                        <option value="1">Office Systems</option>
                                    </select>
                                </dd>

                                <dt>Project Creator</dt>
                                <dd>
                                    <select v-model="fil_project_creator">
                                        <option value=""></option>
                                        <option v-for="item in users" :value="item.id" :key="item.id">
                                            {{ item.username }}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Project Group</dt>
                                <dd>
                                    <select v-model="fil_group">
                                    <option value=""></option>
                                    <option value="0">Not Belong to Any Group</option>
                                    <option v-for="item in groups"  :value="item.id" :key="item.project_group">
                                        {{ item.project_group }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Order Type</dt>
                                <dd>
                                    <select v-model="fil_kind">
                                        <option value=""></option>
                                        <option value="taiwan">Order – Taiwan</option>
                                        <option value="">Order – Warehouse</option>
                                        <option value="">Order – 3rd Party</option>
                                        <option value="">Order – Mockup</option>
                                        <option value="">Order – Stocks</option>
                                    </select>
                                </dd>

                                <dt>Order Creator</dt>
                                <dd>
                                    <select v-model="fil_creator">
                                        <option value=""></option>
                                        <option v-for="item in creators" :value="item.username" :key="item.username">
                                            {{ item.username }}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Keyword (only for order name, project name or order no.)</dt>
                                <dd><input type="text" v-model="fil_keyword"></dd>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_filters()">Cancel</a><a
                                    class="btn small" @click="clear_filters()">Clear</a> <a class="btn small green"
                                                                                            @click="apply_filters()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 排序 -->
                <div class="new_project">
                    <a class="sort"></a>
                    <div id="order_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt1">
                                            <option value=""></option>
                                            <option value="1">
                                                Created Time
                                            </option>
                                            <option value="2">
                                                Last Updated Time
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord1">
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
                                        <select v-model="od_opt2">
                                            <option value=""></option>
                                            <option value="1">
                                                Created Time
                                            </option>
                                            <option value="2">
                                                Last Updated Time
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord2">
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
                            <div class="btnbox"><a class="btn small" @click="cancel_orders()">Cancel</a><a
                                    class="btn small" @click="clear_orders()">Clear</a> <a class="btn small green"
                                                                                           @click="apply_orders()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Filter 
                <div class="filter">
                    <select name="" id="" v-model="fil_project_category">
                        <option value="">Project Category</option>
                        <option v-for="item in categorys" :value="item.id" :key="item.category">
                            {{ item.category }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_client_type">
                        <option value="">Client Type</option>
                        <option v-for="item in client_types" :value="item.id" :key="item.client_type">
                            {{ item.client_type }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_priority">
                        <option value="">Priority</option>
                        <option v-for="item in priorities" :value="item.id" :key="item.priority">
                            {{ item.priority }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_status">
                        <option value="">Status</option>
                        <option v-for="item in statuses" :value="item.id" :key="item.project_status">
                            {{ item.project_status }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_stage">
                        <option value="">Current Stage</option>
                        <option value="Empty">Empty</option>
                        <option v-for="item in stages" :value="item.stage" :key="item.stage">
                            {{ item.stage }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_creator">
                        <option value="">Project Creator</option>
                        <option v-for="item in creators" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>
                </div>

                    -->


                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
            <!-- list -->
            <div class="tableframe">
                <div class="tablebox lv1">
                    <ul class="head">
                        <li>Order Number</li>
                        <li>Order Name</li>
                        <li>Order Type</li>
                        <li>Status</li>
                        <li>Related Project</li>
                        <li>Created Time</li>
                        <li>Last Updated Time</li>
                    </ul>
                    <ul v-for='(receive_record, index) in displayedPosts'>

                        <li>{{ receive_record.serial_name }}</li>

                        <li>
                            <a v-show="receive_record.is_edited == 1" v-bind:href="'order_taiwan_p1?id=' + receive_record.id">{{
                                receive_record.od_name }}</a>
                            <input name="title" type="text"
                                   v-show="receive_record.is_edited == 0"
                                   v-model="title" maxlength="1024"></li>

                        <li> {{ receive_record.order_type == 'taiwan' ? 'Order – Taiwan' : '' }} </li>

                        <li>{{ receive_record.status == 0 ? 'Ongoing' : (receive_record.status == 1 ? 'Pending' : 'Completed') }}</li>

                        <li>
                            <a v-show="receive_record.is_edited == 1"
                               v-bind:href="'project03_other?sid='+ receive_record.stage_id">Project: {{ receive_record.project_name }}
                            </a>
                          
                            <!--
                            <select name="project_name" v-show="receive_record.is_edited == 0"
                                    class="limitedNumbChosen"
                                    v-model="receive_record.project_id">
                                <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name }}
                                </option>
                            </select>
                            -->

                            <dd class="relate_to" v-show="receive_record.is_edited == 0">
                                <input type="radio" class="alone" value="project" v-model="type"> <span>Project</span>
                                <input type="radio" class="alone" value="task" v-model="type"> <span>Task Management</span>

                                <!-- if choose Project -->
                                <select v-model="project_id" v-if="type == 'project'">
                                    <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>

                                <!-- if choose Task Management -->
                                <select v-model="kind" v-if="type == 'task'">
                                    <option :value="'a'">Admin Department</option>
                                    <option :value="'d'">Design Department</option>
                                    <option :value="'l'">Lighting Department</option>
                                    <option :value="'o'">Office Systems Department</option>
                                    <option :value="'sl'">Sales Department</option>
                                    <option :value="'sv'">Service Department</option>
                                </select>

                                <select v-model="task_id" v-if="kind == 'a' && type == 'task'">
                                    <option v-for="(item, index) in task_a" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'd' && type == 'task'">
                                    <option v-for="(item, index) in task_d" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'l' && type == 'task'">
                                    <option v-for="(item, index) in task_l" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'o' && type == 'task'">
                                    <option v-for="(item, index) in task_o" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'sl' && type == 'task'">
                                    <option v-for="(item, index) in task_sl" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'sv' && type == 'task'">
                                    <option v-for="(item, index) in task_sv" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                            </dd>

                        </li>

                        <li>{{receive_record.created_at}}<br>{{receive_record.created_by}}</li>
                        <li>{{receive_record.updated_at}}<br>{{receive_record.updated_by}}</li>
                    </ul>

                </div>
            </div>
            <!-- list end -->
            <div class="list_function">
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>
                    <a class="page" v-for="pg in pages_10" @click="page=pg"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>
                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/order_mgt.js"></script>

</html>

<script>

</script>