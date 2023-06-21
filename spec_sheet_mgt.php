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
    <title>Spec Sheet Management</title>
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

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
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

        body.gray .mainContent{
            padding-top: 100px;
        }

        body.gray .mainContent > h6.title {
            font-size: 36px;
            font-weight: 700;
            color: #707071;
            border-bottom: 2px solid #707071;
            padding: 0 0 7px 7px;
            margin: -10px 0 30px;
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
            width: 220px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) {
            width: 430px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) {
            width: 480px;
        }

        #tb_product_list tbody tr td:nth-of-type(4) {
            width: 180px;
        }

        #tb_product_list tbody tr td:nth-of-type(1) img {
            max-width: 160px;
            max-height: 160px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) ul {
            margin-bottom: 0;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul {
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 3px;
        }

        #tb_product_list tbody tr td:nth-of-type(4) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px
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
            vertical-align: top;
        }

        #tb_product_list ul li:nth-of-type(2) span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px;
        }

        #tb_product_list ul li:nth-of-type(2) span + span{
            margin-left: 5px;
        }

        #tb_product_list tbody td ul li:nth-of-type(2) a {
            color: #007bff;
        }

        #tb_product_list tbody tr td:nth-of-type(2) ul li span.phasedout{
            background-color: red;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list tbody td div.phasedout_variant {
            text-align: left;
            color: red;
            font-size: 16px;
            font-weight: 600;
            padding: 5px 0 0 3px;
        }

        #tb_product_list tbody td div.phasedout_variant button {
            font-size: 14px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-left: 3px;
            padding: 0 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list tbody td div.phasedout_variant button:focus {
            outline-color: transparent;
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

        button.btn.dropdown-toggle {
            background-color: white;
            border: 1px solid #999;
            border-radius: 0;
        }

        button.btn_switch{
            position: fixed;
            right: 10px;
            top: 10px;
            width: 50px;
            height: 50px;
            border: 1px solid rgb(153,153,153);
            border-radius: 25px;
            font-size: 15px;
            font-weight: 700;
            background-color: rgba(7, 220, 237, 0.8);
            z-index: 999;
        }

        ul.dropdown-menu.inner li {
            display: block;
            border-right: none;
        }

        .dropdown-menu > .bs-searchbox > input[type='search'] {
            border: 1px solid #ced4da;
        }


    </style>

</head>

<body class="gray">

<div id="app" class="bodybox">
    <div class="mask" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->

    <button @click="toggle_price()" class="btn_switch" v-show="show_ntd === true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="bi bi-toggles"><path d="M4.5 9a3.5 3.5 0 1 0 0 7h7a3.5 3.5 0 1 0 0-7h-7zm7 6a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm-7-14a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm2.45 0A3.49 3.49 0 0 1 8 3.5 3.49 3.49 0 0 1 6.95 6h4.55a2.5 2.5 0 0 0 0-5H6.95zM4.5 0h7a3.5 3.5 0 1 1 0 7h-7a3.5 3.5 0 1 1 0-7z"></path></svg></button>

    <div class="mainContent">

        <h6 class="title">Specification Sheet Management</h6>

        <div class="block">
            <div class="list_function">

                <!-- 點擊後跳轉去建立產品頁面 -->
                <div class="new_function">
                    <a class="add" href="product_spec_sheet" target="_blank"></a>
                </div>

                <!-- 篩選功能 -->
                <div class="new_function">
                    <a class="filter" id="btn_filter"></a>
                    <div id="filter_dialog" class="dialog A"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <!-- <dt>ID</dt>
                                <dd><input type="text" v-model="fil_id"></dd> -->

                                <dt style="margin-bottom: -15px;">ID</dt>
<div class="half"><dt>From</dt> <dd><input type="number" min="1" step="1" v-model="fil_id"></dd></div>
<div class="half"><dt>To</dt> <dd><input type="number" min="1" step="1" v-model="fil_id_1"></dd></div>

                                <dt>Code</dt>
                                <dd><input type="text" v-model="fil_code"></dd>

                                <dt>Category</dt>
                                <dd>
                                <select v-model="fil_category">
                                <option></option>
                                <option value="10000000">Lighting</option>
                                <option value="20000000">Systems Furniture</option>
                                <option value="20010000">Systems Furniture >> Cabinet</option>
                                <option value="20020000">Systems Furniture >> Chair</option>
                                <option value="20030000">Systems Furniture >> Table</option>
                                <option value="20040000">Systems Furniture >> Workstation</option>
                                <option value="20050000">Systems Furniture >> Partition</option>
                                </select>
                                </dd>

                                <dt>Tag</dt>
                                <dd>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="No tag selected" id="tag01" v-model="fil_tag">

                                        
                                            <optgroup label="BY INSTALL LOCATION">
                <option value="BLDG. FAÇADE">BLDG. FAÇADE</option>
                <option value="CABINET">CABINET</option>
                <option value="CEILING">CEILING</option>
                <option value="FLOOR">FLOOR</option>
                <option value="INDOOR">INDOOR</option>
                <option value="INGROUND">INGROUND</option>
                <option value="OTHER FURNITURES ">OTHER FURNITURES</option>
                <option value="OUTDOOR">OUTDOOR</option>
                <option value="STREET">STREET</option>
                <option value="TABLE">TABLE</option>
                <option value="UNDERWATER">UNDERWATER</option>
                <option value="WALL">WALL</option>
            </optgroup>

            <optgroup label="INSTALL METHOD">
                <option value="POLE-MOUNTED">POLE-MOUNTED</option>
                <option value="RECESSED">RECESSED</option>
                <option value="STAND-ALONE">STAND-ALONE</option>
                <option value="SURFACE-MOUNTED">SURFACE-MOUNTED</option>
                <option value="SUSPENDED">SUSPENDED</option>
            </optgroup>

            <optgroup label="BY TYPE / FUNCTION">
    <option value="ALUMINUM PROFILE">ALUMINUM PROFILE</option>
    <option value="ASSEMBLED">ASSEMBLED</option>
    <option value="AUDIO EQUIPMENT">AUDIO EQUIPMENT</option>
    <option value="BATTEN LIGHT">BATTEN LIGHT</option>
    <option value="BOLLARD">BOLLARD</option>
    <option value="CONTROLLER">CONTROLLER</option>
    <option value="CUSTOMIZED">CUSTOMIZED</option>
    <option value="DIMMABLE">DIMMABLE</option>
    <option value="DIMMER">DIMMER</option>
    <option value="DIRECTIONAL">DIRECTIONAL</option>
    <option value="DISPLAY SPOTLIGHT">DISPLAY SPOTLIGHT</option>
    <option value="DMX">DMX</option>
    <option value="EMERGENCY LIGHT">EMERGENCY LIGHT</option>
    <option value="FLOOD LIGHT">FLOOD LIGHT</option>
    <option value="HIGHBAY LIGHT">HIGHBAY LIGHT</option>
    <option value="LED BULB">LED BULB</option>
    <option value="LED DRIVER">LED DRIVER</option>
    <option value="LED STRIP">LED STRIP</option>
    <option value="LED TUBE">LED TUBE</option>
    <option value="LIGHTBOX">LIGHTBOX</option>
    <option value="LINEAR LIGHT">LINEAR LIGHT</option>
    <option value="MAGNETIC TRACK BAR">MAGNETIC TRACK BAR</option>
    <option value="MAGNETIC TRACK LIGHT">MAGNETIC TRACK LIGHT</option>
    <option value="PANEL LIGHT">PANEL LIGHT</option>
    <option value="PROJECTION LIGHT">PROJECTION LIGHT</option>
    <option value="RECEIVER">RECEIVER</option>
    <option value="SPECIALTY LIGHT">SPECIALTY LIGHT</option>
    <option value="STAGE LIGHT">STAGE LIGHT</option>
    <option value="SWITCH">SWITCH</option>
    <option value="TRACK BAR">TRACK BAR</option>
    <option value="TRACK LIGHT">TRACK LIGHT</option>
    <option value="TROFFER LIGHT">TROFFER LIGHT</option>
    <option value="UV LED">UV LED</option>
    <option value="WALL WASHER">WALL WASHER</option>
</optgroup>

            <optgroup label="ACCESSORY">
                <option value="FUNCTIONAL ACCESSORY">FUNCTIONAL ACCESSORY</option>
                <option value="INSTALL ACCESSORY">INSTALL ACCESSORY</option>
                <option value="REPLACEMENT PART">REPLACEMENT PART</option>
            </optgroup>

                                    </select>
                                </dd>

                                <dt>Brand</dt>
                                <dd>
                                    <select v-model="fil_brand">
                                        <option value="">
                                        <option v-for="(item, index) in brands">{{ item.brand }}</option>
                                    </select>
                                </dd>
<!--
                                <dt>Keyword (only for description and notes)</dt>
                                <dd><input type="text" v-model="fil_keyword"></dd>

                                <div class="half">
                                    <dt></dt>
                                    <dd><input type="text"></dd>
                                </div>
    -->
                                <!--
                                <div class="half">
                                    <dt>Category</dt>
                                    <dd>
                                        <select>
                                            <option value="0">
                                            <option>Lighting</option>
                                            <option>Systems Furniture</option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select>
                                            <option value="0">
                                            <option>Indoor</option>
                                            <option>Outdoor</option>
                                            <option>Accessory</option>
                                            <option>Cabinet</option>
                                            <option>Chair</option>
                                            <option>Table</option>
                                            <option>Workstation</option>
                                            <option>Partition</option>
                                        </select>
                                    </dd>
                                </div>

                                <dt style="margin: 20px 0 -18px;">Price</dt>
                                <div class="half">
                                    <dt>min</dt>
                                    <dd><input type="number" v-model="fil_amount_lower"></dd>
                                </div>

                                <div class="half">
                                    <dt>max</dt>
                                    <dd><input type="number" v-model="fil_amount_upper"></dd>
                                </div>
                                -->

                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="filter_clear">Cancel</a>
                                <a class="btn small" @click="filter_remove">Clear</a>
                                <a class="btn small green" @click="filter_apply">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 排序功能 -->
                <div class="new_function">
                    <a class="sort" id="btn_sort"></a>
                    <div id="sort_dialog" class="dialog B"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_factor1">
                                            <option value="0"></option>
                                            <option value="1">
                                                ID
                                            </option>
                                            <option value="2">
                                                Created Time
                                            </option>
                                            <option value="3">
                                                Updated Time
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
                                                ID
                                            </option>
                                            <option value="2">
                                                Created Time
                                            </option>
                                            <option value="3">
                                                Updated Time
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

                <!-- 分頁功能 -->
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply_new();">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply_new();" v-bind:style="[pg == page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply_new();">Next 10</a>
                </div>
            </div>
        </div>


        <div>
            <table id="tb_product_list" class="table  table-sm table-bordered" >

                <thead>
                <tr>

                    <th>Image</th>

                    <th>Information</th>

                    <th>Specification</th>

                    <th>Action</th>

                </tr>

                </thead>

                <tbody>
                <tr v-for="(item, index) in displayedPosts">

                    <td>
                        <a target="_blank" :href="'product_display_code?id='+item.product_id"><img :src="baseURL + item.photo1" v-if="item.photo1"></a>
                    </td>
                    <td>
                        <ul v-if="item.out == 'Y'">
                        <li>
                                <span class="phasedout">Phased Out</span>
                        </li>
                        <li></li>
                        </ul>
                        <ul>
                            <li>
                                ID:
                            </li>
                            <li>
                               {{ item.product_id }}
                            </li>

                        </ul>
                        <ul>
                            <li>
                                Code:
                            </li>
                            <li>
                               <a target="_blank" :href="'product_display_code?id='+item.product_id">{{ item.code }}</a>
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
                                {{ item.created_at }} {{ item.created_name !== null ? '(' + item.created_name + ')' : '' }}
                            </li>

                        </ul>

                        <ul>
                            <li>
                                Updated:
                            </li>
                            <li>
                                {{ item.updated_name !== null ? item.updated_at : '' }} {{ item.updated_name !== null ? '(' + item.updated_name + ')' : '' }}
                            </li>

                        </ul>
                    </td>

                    <td>
                        <ul v-for="(att, index) in item.variation_array">
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
                        <button id="edit01" @click="btnEditClick(item.product_id, item.p_id)"><i class="fas fa-edit"></i></button>
                        <button @click="btnDelClick(item.id)"><i class="fas fa-times"></i></button>

                    </td>
                </tr>

                </tbody>

            </table>
        </div>


        <div class="block">
            <div class="list_function">
                <!-- 分頁功能 -->
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply_new();">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply_new();" v-bind:style="[pg == page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply_new();">Next 10</a>
                </div>
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
</script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/spec_sheet_mgt.js"></script>

</html>
