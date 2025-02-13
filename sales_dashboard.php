<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/project03_is_creator.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    
if($decoded->data->limited_access == true)
header( 'location:index' );

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );

    $access6 = false;
    $access7 = false;

    if(trim(strtoupper($department)) == 'SALES')
    {
        $access6 = true;
    }

    if($username == "Kristel Tan" || $username == "Kuan" || $username == "Dennis Lin" || $username == "dereck")
    {
        $access6 = true;
    }

    if($username == "Marie Kayla Patricia Dequina" || $username == "Gina Donato" || $username == "Aiza Eisma" || $username == "Johmar Maximo")
    {
        $access7 = true;
    }

    if($access6 == false && $access7 == false)
        header( 'location:index' );

    
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header('location:index');
}

?>
<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

    <!-- SEO -->
    <title>Sales Dashboard</title>
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
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous" />

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">



    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function() {
            $('header').load('include/header.php');
            
            <?php
            if ($access6 == true) {
            ?>
                dialogshow($('.list_function a.add.red'), $('.list_function .dialog.r-add'));
                dialogshow($('.list_function a.edit.red'), $('.list_function .dialog.r-edit'));
            <?php
            }
            ?>
            dialogshow($('.list_function a.filtering'), $('.list_function .dialog.d-filter'));
            //dialogshow($('.list_function a.add.blue'), $('.list_function .dialog.d-add'));
            //dialogshow($('.list_function a.edit.blue'), $('.list_function .dialog.d-edit'));
            // left block Reply
            dialogshow($('.btnbox a.reply.r1'), $('.btnbox .dialog.r1'));
            dialogshow($('.btnbox a.reply.r2'), $('.btnbox .dialog.r2'));
            dialogshow($('.btnbox a.reply.r3'), $('.btnbox .dialog.r3'));
            dialogshow($('.btnbox a.reply.r4'), $('.btnbox .dialog.r4'));
            // 套上 .dialogclear 關閉所有的跳出框
            $('.dialogclear').click(function() {
                dialogclear()
            });
            // 根據 select 分類
            $('#opType').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType2').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType2").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType3').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType3").val();
                $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
            })

            $('.selectbox').on('click', function() {
                $.fancybox.open({
                    src: '#pop-multiSelect',
                    type: 'inline'
                });
            });
        })
    </script>

<style scoped>
    header .headerbox {

background-color: #94BABB;

}
    </style>

    <!-- CSS for current webpage -->
    <style type="text/css">



        body.fifth .mainContent>.tags a {
            background-color: #DFEAEA;
            border: 2px solid #94BABB;
            border-bottom: none;
        }

        body.fifth .mainContent>.tags a.focus {
            background-color: #94BABB;
        }

        body.fifth .mainContent>.block,  body.fifth .mainContent>.block.focus{
            border: none;
            border-top: 2px solid #94BABB;
            margin-bottom: 0;
        }

        body.fifth .list_function .popupblock {
            position: relative;
            float: left;
            margin-right: 10px;
        }

        body.fifth .list_function .popupblock dl div.half:nth-of-type(odd){
            width: 48.5%;
        }

        .list_function a.filtering, .list_function a.exporting {
            width: 30px;
            height: 30px;
            background-color: #94BABB;
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url(images/ui/btn_filter_white.svg);
        }


        .list_function a.exporting {
            background-image: url(images/ui/btn_export_white.svg);
        }

        .list_function .dialog {
            border-color: #94BABB;
        }

        .list_function .dialog::before {
            border-color: transparent transparent transparent #94BABB;
        }

        .list_function .dialog h6, .list_function .dialog dt{
            color: #000000;
        }

        .list_function .dialog dt{
            margin-top: 15px;
            padding-bottom: 0px;
        }

        table.spantable{
            border: 2px solid  #94BABB;
            border-radius: 12px;
            border-collapse: separate;
            font-size: 14px;
            width: 100%;
            margin: 5px 0 50px;
        }

        table.spantable tr.emphasize{
            font-weight: 500;
        }

        table.spantable th, table.spantable td{
            border-right: 1px solid  #94BABB;
            border-bottom: 1px solid  #94BABB;
            padding: 5px;
            text-align: center;
        }

        table.spantable thead tr:first-of-type th{
            border-top-left-radius: 9px;
            border-top-right-radius: 9px;
        }

        table.spantable thead tr th:last-of-type{
           /* border-right: none; */
        }

        table.spantable tbody tr td:last-of-type{
           /* border-right: none; */
        }

        table.spantable tbody tr:last-of-type td{
           /* border-bottom: none; */
        }

        table.spantable tfoot tr th {
            border-bottom: none;
        }

        table.spantable tfoot tr th:first-of-type {
            border-bottom-left-radius: 9px;
        }

        table.spantable tfoot tr th:last-of-type {
            border-bottom-right-radius: 9px;
        }

        table.spantable th{
            background-color: #DFEAEA;
        }

        table.spantable td.money, table.spantable th.money {
            text-align: right;
            min-width: 125px;
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(1){
            width: 200px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(2){
            width: 140px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(3){
            width: 140px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(4){
            width: 400px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(5){
            width: 180px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(6){
            width: 180px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(7){
            width: 180px
        }

        table.spantable thead tr:nth-of-type(2) > th:nth-of-type(8){
            width: 200px
        }
    </style>


</head>

<body class="fifth">

    <div id="app" class="bodybox" style="min-height: 150vh;">
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <a class="tag A focus">Sales Dashboard</a>
                <?php if($access6) echo '<a class="tag B"  href="sales_dashboard_1">Sales Dashboard 1</a>'; ?>
            </div>
            <!-- Blocks -->
            <div class="block A focus">

                <div class="list_function" style="margin-top: 10px;">

                    <!-- 篩選 -->
                    <div class="popupblock">
                        <a class="filtering" id="btn_filter"></a>
                        <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt>Account Executive</dt>
                                    <dd>
                                        <select v-model="fil_creator">
                                            <option value="">All</option>
                                            <option v-for="item in creators" :value="item.username"
                                                :key="item.username">
                                            {{ item.username }}
                                        </option>
                                        </select>
                                    </dd>

                                    <dt>Category</dt>
                                    <dd>
                                        <select v-model="fil_category">
                                            <option value="">All</option>
                                            <option value="2">Lighting</option>
                                            <option value="1">Office Systems</option>
                                        </select>
                                    </dd>

                                    <dt style="margin-bottom: -15px;">Period of Time</dt>
                                    <div class="half">
                                        <dt>From</dt>
                                        <dd><input type="month" id="start" name="start" ></dd>
                                    </div>
                                    <div class="half">
                                        <dt>To</dt>
                                        <dd><input type="month" id="end" name="end" ></dd>
                                    </div>

                                </dl>

                                <div class="btnbox"><a class="btn small" @click="filter_clear()">Cancel</a><a
                                        class="btn small" @click="filter_remove()">Clear</a> <a class="btn small green"
                                                                                                @click="filter_apply()">Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 匯出 -->
                    <div class="popupblock">
                        <a class="exporting" id="btn_export" @click="export_petty()"></a>
                    </div>


                </div>

                <div style="width: 100%; overflow-x: auto;">

                <template v-for="(record, x) in receive_records">
                    <!-- 按照每個業務員Amount總和進行排序，第一名放最上面 -->
                    <table class="spantable">

                        <thead>
                        <tr>
                            <th colspan="8">
                                {{ record.date }}
                            </th>
                        </tr>

                        <tr>
                            <th>Account Executive</th>
                            <th>Monthly Quota</th>
                            <th>Category</th>
                            <th>Project Name</th>
                            <th>Collected Payments</th>
                            <th>Total Collected Payments</th>
                            <th>Remaining Amount to Quota</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>

                        <tbody>
                        <template v-for="(item, i) in record.report">
                            <tr v-for="(it, j) in item.l_catagory">
                                <td v-if="j == 0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ item.username }}</td>
                                <td v-if="j == 0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{(record.date > '2025/01' ? '6,600,000.00' : '2,200,000.00')}}</td>
                                    <td v-if="j == 0" :rowspan="item.l_catagory.length">{{ it.catagory == '' ? "" : 'Lighting' }}</td>
                                    <td>{{ it.project_name }}</td>
                                    <!-- 下面這各td要放的是: 這個月份收到的 down payment 和 full payment 的加總金額 -->
                                    <td class="money">{{ it.amount == 0 ? "" : Number(it.amount).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</td>
                                    <!-- 下面這各td要放的是: 這個月份收到的 down payment 和 full payment 的加總金額 -->
                                    <td class="money"  v-if="j == 0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ item.subtotal == 0 ? "0.00" : Number(item.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</td>
                                    <!-- 下面這各td要放的是: 6,600,000.00 減掉 這個月份收到的 down payment 和 full payment 的加總金額 -->
                                    <td class="money"  v-if="j == 0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ item.subtotal == 0 ? (record.date > '2025/01' ? '6,600,000.00' : '2,200,000.00') : Number((record.date > '2025/01' ? '6600000' : '2200000') - item.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</td>
                                    <!-- 如果上面的td裡面的金額 小於或等於 0，則下面這各td要放的是字串「Achieved Monthly Quota」；如果金額是大於0，則下面這各td裡不用放任和內容 -->
                                    <td  v-if="j == 0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ (record.date > '2025/01' ? '6600000' : '2200000') - item.subtotal <= 0 ? 'Achieved Monthly Quota' : '' }}</td>
                            </tr>

                            <tr v-for="(it, j) in item.o_catagory">
                                <td v-if="item.l_catagory.length == 0 && j==0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ item.username }}</td>
                                <td v-if="item.l_catagory.length == 0 && j==0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ (record.date > '2025/01' ? '6,600,000.00' : '2,200,000.00') }}</td>
                                    <td v-if="j == 0" :rowspan="item.o_catagory.length">Office Systems</td>
                                    <td>{{ it.project_name }}</td>
                                    <!-- 下面這各td要放的是: 這個月份收到的 down payment 和 full payment 的加總金額 -->
                                    <td class="money">{{ it.amount == 0 ? "" : Number(it.amount).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</td>
                                    <!-- 下面這各td要放的是: 這個月份收到的 down payment 和 full payment 的加總金額 -->
                                    <td class="money" v-if="item.l_catagory.length == 0 && j==0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ item.subtotal == 0 ? "0.00" : Number(item.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</td>
                                    <!-- 下面這各td要放的是: 6,600,000.00 減掉 這個月份收到的 down payment 和 full payment 的加總金額 -->
                                    <td class="money" v-if="item.l_catagory.length == 0 && j==0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ item.subtotal == 0 ? "(record.date > '2025/01' ? '6,600,000.00' : '2,200,000.00')" : Number((record.date > '2025/01' ? '6600000' : '2200000') - item.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</td>
                                    <!-- 如果上面的td裡面的金額 小於或等於 0，則下面這各td要放的是字串「Achieved Monthly Quota」；如果金額是大於0，則下面這各td裡不用放任和內容 -->
                                    <td v-if="item.l_catagory.length == 0 && j==0" :rowspan="item.l_catagory.length + item.o_catagory.length">{{ (record.date > '2025/01' ? '6600000' : '2200000') - item.subtotal <= 0 ? 'Achieved Monthly Quota' : '' }}</td>
                            </tr>

                        </template>
                        

                        </tbody>

                        <tfoot>
                        <tr>
                            <th colspan="5">Total</th>
                            <th class="money">{{ Number(record.sum).toLocaleString(undefined, {minimumFractionDigits: 2,maximumFractionDigits: 2}) }}</th>
                            <th colspan="2"></th>
                        </tr>
                        </tfoot>

                    </table>
                    </template>



                </div>


            </div>
        </div>
    </div>
</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>
<script defer src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/sales_dashboard.js"></script>

</html>