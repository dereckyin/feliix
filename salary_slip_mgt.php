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
    <title>Template Management</title>
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

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })

        /*
        function ToggleModal(target) {
            $(".mask").toggle();

            if (target == 1) {
                app.reset_all();
                $("#Modal_1").toggle();
            } else if (target == 2) {
                $("#Modal_2").toggle();
            } else if (target == 3) {
                $("#Modal_3").toggle();
            }
        }*/
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">

        /* -------------------------- */
        /* body.green Style (Yellow) */
        /* -------------------------- */
        body.green .mainContent > .block,
        body.green .mainContent > .block h6,
        body.green .mainContent > .block .tablebox,
        body.green .mainContent > .block .tablebox > ul > li,
        body.green .mainContent > .block .tablebox2,
        body.green .mainContent > .block .formbox,
        body.green .mainContent > .block .formbox dd,
        body.green .mainContent > .tags a {
            border-color: #2F9A57;
        }

        body.green .mainContent > .block h6 {
            color: #2F9A57;
        }

        body.green .mainContent > .block .tablebox > ul.head > li,
        body.green .mainContent > .tags a {
            background-color: #E5F7EB;
        }

        body.green .mainContent > .tags a.focus {
            background-color: #2F9A57;
        }

        body.green .mainContent > .block .tablebox {
            border-top: 2px solid #2F9A57;
            border-left: 2px solid #2F9A57;
            width: 100%;
        }

        body.green .mainContent > .block .tablebox > ul > li {
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid #2F9A57;
            border-right: 2px solid #2F9A57;
            font-weight: 500;
            font-size: 16px;
            vertical-align: middle;
        }

        body.green .mainContent > .block .tablebox.salary, body.green .mainContent > .block .tablebox.loan {
            border-top: none;
        }

        body.green .mainContent > .block .tablebox.salary > ul.head > li, body.green .mainContent > .block .tablebox.loan > ul.head > li {
            border-top: 2px solid #2F9A57;
        }

        body.green .mainContent > .block .tablebox.salary > ul > li:nth-of-type(4), body.green .mainContent > .block .tablebox.loan > ul > li:nth-of-type(5) {
            border: none;
            width: 40px;
            min-width: 40px;
            background-color: white;
        }

        body.green .mainContent > .block .tablebox.salary > ul > li:nth-of-type(4) span, body.green .mainContent > .block .tablebox.loan > ul > li:nth-of-type(5) span {
            display: block;
            color: white;
            font-size: 18px;
            font-weight: 500;
            width: 28px;
            height: 28px;
            border-radius: 14px;
            line-height: 24px;
            background-color: rgb(205,92,92);
            text-align: center;
            cursor: pointer;
        }


        body.green .mainContent > .block .tablebox.salary > ul > li:nth-of-type(3) {
            text-align: left;
        }

        body.green .mainContent > .block .tablebox.salary > ul.head > li:nth-of-type(3) {
            text-align: center;
        }

        body.green .mainContent > .block .tablebox.loan > ul > li:nth-of-type(3) {
            text-align: center;
        }

        body.green .mainContent > .block .tablebox ul.head,
        body.green .mainContent > .block .formbox li.head {
            background-color: #2F9A57;
            font-weight: 800;
        }

        body.green .mainContent > .block .tablebox ul.footer li {
            background-color: #F4F4F4;
            font-weight: 800;
        }

        body.green .mainContent > .block .tablebox ul.head li {
            font-weight: 800;
        }

        body.green input.alone[type=radio]::before,
        body.green input.alone[type=checkbox]::before,
        body.green input[type=checkbox] + Label::before,
        body.green input[type=radio] + Label::before {
            color: #2F9A57;
        }

        body.green input[type=range],
        body.green input[type=text],
        body.green input[type=password],
        body.green input[type=file],
        body.green input[type=number],
        body.green input[type=url],
        body.green input[type=email],
        body.green input[type=tel],
        body.green input[list],
        body.green input[type=button],
        body.green input[type=submit],
        body.green button,
        body.green textarea,
        body.green select,
        body.green output {
            border-color: #2F9A57;
        }

        body.green select {
            background-image: url(images/ui/icon_form_select_arrow_black.svg);
        }

        body.green a.btn.green {
            background-color: #2F9A57;
        }

        body.green a.btn.green:hover {
            background-color: #A9E5BF;
        }


        .block.A .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #2F9A57;
            cursor: pointer;
            margin: 3px 6px 3px 0;
        }

        .list_function {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .list_function::after {
            display: none;
        }

        .list_function .front {
            display: flex;
            align-items: center;
        }

        .list_function .front a.create {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_add_green.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 35px;
            height: 35px;
            line-height: 35px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            margin-right: 25px;
            flex-grow: 0;
            flex-shrink: 0;
            margin-top: 5px;
        }

        .list_function .searching input {
            font-size: 15px;
            padding: 4px 7px;
            height: 34px;
            width: 201px;
            margin-top: 5px;
        }

        .list_function .searching input[type=month] {
            border: 2px solid #2F9A57;
            background-color: transparent;
            vertical-align: middle;
        }

        .list_function .searching i {
            color: #2F9A57;
            font-size: 22px;
        }

        .list_function .pagenation {
            float: none;
        }

        .list_function .pagenation a {
            color: #2F9A57;
            border-color: #2F9A57;
        }

        .list_function .pagenation a:hover {
            background-color: #2F9A57;
            color: #FFF;
        }

        body input.alone.green[type=radio]::before {
            font-size: 25px;
            color: #2F9A57;
        }

        .bodybox .mask {
            position: absolute;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }

        .modal {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        .modal .modal-content {
            width: 94%;
            height: calc(100vh - 40px);
            margin: auto;
            border: 3px solid #2F9A57;
            padding: 20px 0 0;
            background-color: white;
            max-height: 850px;
            overflow-y: auto;
        }

        .modal .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px 15px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .modal-content .modal-header h6 {
            color: #2F9A57;
            border-bottom: none;
            padding: 0;
        }

        .modal .modal-content .modal-header a {
            color: #2F9A57;
            font-size: 20px;
        }

        .modal .modal-content .box-content {
            padding: 20px 25px 25px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }

        .modal .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
            height: 38px;
        }

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=text], .modal .modal-content .box-content input[type=number], .modal .modal-content .box-content input[type=date] {
            border: 1px solid black;
            width: 100%;
            padding: 8px 35px 8px 15px;
            height: 39px;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(1) {
            width: 35%;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(2) {
            width: 25%;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(3) {
            width: 40%;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(1) {
            width: 40%;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(n+2) {
            width: 20%;
            min-width: 100px;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(1) {
            width: 40%;
        }

        .modal .modal-content .box-content .tablebox ul > li input {
            border-color: #D0D0D0;
            text-align: center;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(3) input {
            text-align: left;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(3) input {
            text-align: center;
        }

        .modal .modal-content .box-content ul > li.datebox {
            display: flex;
            align-items: center;
        }

        .modal .modal-content .box-content ul > li.datebox > input {
            flex-grow: 1
        }

        .modal .modal-content .box-content ul > li.datebox > span {
            margin: 0 10px;
            flex-grow: 0;
            flex-shrink: 0;
            font-weight: 600;
        }

        .modal .modal-content .box-content .tablebox.tb_salary{
            border-top: 1px solid black;
            border-left: 1px solid black;
        }

        .modal .modal-content .box-content .tablebox.tb_salary ul> li{
            width: 33.3%;
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        .modal .modal-content .box-content .heading {
            color: #2F9A57;
            font-size: 30px;
            font-weight: 600;
        }

        .modal .modal-content .box-content .tablebox ul.footer li:nth-of-type(3){
            text-align: center!important;
            background-color: white;
            border-bottom: none;
            border-right: none;
        }

        .modal .modal-content .box-content .tablebox ul.footer li:nth-of-type(3) i{
            color: rgb(32,103,102);
            font-size: 28px;
            cursor: pointer;
        }

        .modal .modal-content .box-content .tablebox.loan{
            margin-top: 10px;
            border-left: none;
        }

        .modal .modal-content .box-content .tablebox.loan ul li:nth-of-type(1){
            border-left: 2px solid #2F9A57;
        }

        .modal .modal-content .box-content .tablebox.loan ul.add_row li:nth-of-type(1){
            border-left: none;
        }

        ul.add_row li{
            border-bottom: none!important;
            border-right: none!important;
        }

        ul.add_row li:nth-of-type(4) i{
            color: rgb(32,103,102);
            font-size: 28px;
            cursor: pointer;
        }


    </style>


</head>

<body class="green">

<div class="bodybox">
    
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="mask" :ref="'mask'" style="display:none"></div>
        <div class="tags">
            <a class="tag A" href="performance_dashboard">Salary Management</a>
            <a class="tag B" href="performance_review">Salary Slip Management</a>
            <a class="tag C" href="template_library">Salary Slip</a>
        </div>
        <!-- Blocks -->
        <div class="block B focus">
            <h6>Salary Slip Management</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create" href="javascript: void(0)" @click="ToggleModal(1, 'o')"></a>

                            <div class="searching">
                                <input type="month" v-model="sdate">
                                <input type="month" v-model="edate">
                                <input type="text" placeholder="Searching Keyword Here" v-model="keyword">
                                <button style="border: none;" @click="search()"><i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1"
                               @click="page < 1 ? page = 1 : page--; filter_apply();">Previous</a>
                            <a class="page" v-for="pg in pages" @click="page=pg"
                               v-bind:style="[page==pg ? { 'background':'#2F9A57', 'color': 'white'} : { }]"
                               v-on:click="filter_apply();">{{ pg }}</a>
                            <a class="next" :disabled="page == pages.length" @click="page++; filter_apply();">Next</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Status</li>
                        <li>Employee</li>
                        <li>Position</li>
                        <li>Salary for</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone green" :value="record.id"
                                   v-model="proof_id">
                        </li>
                        <li>{{ record.status == 0 ? "For Confirm" : ( record.status == 1 ? "Confirmed" : (record.status == 2 ? "Rejected" : (record.status == 3 ? "Withdraw" : ""))) }}</li>
                        <li>{{ record.username }}</li>
                        <li>{{ record.title }} ({{ record.department }})</li>
                        <li>{{ record.start_date }} ~ {{ record.end_date }}</li>
                    </ul>

                </div>



                <div id="Modal_1" class="modal" :ref="'Modal_1'">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Create Salary Slip</h6>
                            <a href="javascript: void(0)" @onclick="ToggleModal(1, 'c')"><i class="fa fa-times fa-lg"
                                                                                      aria-hidden="true"></i></a>
                        </div>

                        <!-- Form beginning -->
                        <form>

                            <!-- Salary slip general description -->
                            <div class="box-content">

                                <ul>
                                    <li><b>Employee Name</b></li>
                                    <li>
                                        <select v-model="employee">
                                            <option></option>
                                            <option v-for="(item, index) in salary_records" :value="item.uid" :key="item.username">{{ item.username }}</option>
                                        </select>
                                    </li>

                                    <li><b>Salary for</b></li>
                                    <li class="datebox">
                                        <input type="date" v-model="date_start">
                                        <span>~</span>
                                        <input type="date" v-model="date_end">
                                    </li>

                                    <li>
                                        <div class="tablebox tb_salary">
                                            <ul>
                                                <li><b>Salary per Month</b></li>
                                                <li><b>Salary per Day</b></li>
                                                <li><b>Salary per Minute</b></li>
                                            </ul>

                                            <ul>
                                                <li>{{salary_per_month}}</li>
                                                <li>{{salary_per_day}}</li>
                                                <li>{{salary_per_minute}}</li>
                                            </ul>
                                        </div>
                                     </li>
                                </ul>

                            </div>


                            <!-- Earning and Deduction -->
                            <div class="box-content">
                                <div class="heading">Salary Detail</div>

                                <div class="tablebox salary" style="margin-top: 10px;">
                                    <ul class="head">
                                        <li>Earnings</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in detail_plus' :key="index">
                                        <li v-if="item.type == 1">{{ item.category }}</li>
                                        <li v-if="item.type == 0"><input type="text" v-model="item.category" placeholder="Other Earning"></li>
                                        <li><input type="number" v-model="item.amount"></li>
                                        <li><input type="text" v-model="item.remark"></li>
                                        <li><span v-if="item.type == 0" @click=del_plus_detail(item.order)>x</span></li>
                                    </ul>

                                    <ul class="footer">
                                        <li>Total Earnings</li>
                                        <li>{{ detail_plus_sum }}</li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_plus_detail()></i></li>
                                        <li></li>
                                    </ul>
                                </div>


                                <div class="tablebox salary" style="margin-top: 40px;">
                                    <ul class="head">
                                        <li>Deductions</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in detail_minus' :key="index">
                                        <li v-if="item.type == 1">{{ item.category }}</li>
                                        <li v-if="item.type == 0"><input type="text" v-model="item.category" placeholder="Other Deduction"></li>
                                        <li><input type="number" v-model="item.amount"></li>
                                        <li><input type="text" v-model="item.remark"></li>
                                        <li><span v-if="item.type == 0" @click=del_minus_detail(item.order)>x</span></li>
                                    </ul>


                                    <ul class="footer">
                                        <li>Total Deductions</li>
                                        <li>{{ detail_minus_sum }}</li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_minus_detail()></i></li>
                                        <li></li>
                                    </ul>
                                </div>

                                <ul>
                                    <li style="margin-top: 40px;"><b>Total Pay:</b></li>
                                    <li class="content" style="font-weight: 700;">{{ detail_sum }}</li>
                                </ul>

                            </div>

                            <!-- Other Information -->
                            <div class="box-content">
                                <div class="heading">Other Information</div>

                                <div class="tablebox loan">
                                    <ul class="head">
                                        <li></li>
                                        <li>Previous</li>
                                        <li>Payment</li>
                                        <li>Balance</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in other' :key="index">
                                        <li><input type="text"  v-model="item.category"></li>
                                        <li><input type="number" min="0" v-model="item.previous" @change=refresh_other()></li>
                                        <li><input type="number" min="0" v-model="item.payment" @change=refresh_other()></li>
                                        <li>{{ item.remark }}</li>
                                        <li><span @click=del_other_detail(item.order)>x</span></li>
                                    </ul>

                                    <ul class="add_row">
                                        <li></li>
                                        <li></li>
                                        <li></li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_other_detail()></i></li>
                                        <li></li>
                                    </ul>

                                </div>

                            </div>


                            <!-- Action Buttons -->
                            <div class="modal-footer">
                                <div class="btnbox">
                                    <a class="btn" @click="cancel()">Cancel</a>
                                    <a class="btn" @click="reset_all()">Reset</a>
                                    <a class="btn green" @click="create_slip()">Submit</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>


                <div id="Modal_2" class="modal" :ref="'Modal_2'">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Salary Slip</h6>
                            <a href="javascript: void(0)" @click="ToggleModal(2, 'c')"><i class="fa fa-times fa-lg"
                                                                                      aria-hidden="true"></i></a>
                        </div>

                        <!-- Salary slip general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Employee Name</b></li>
                                <li class="content">{{ record.username }}</li>

                                <li><b>Position</b></li>
                                <li class="content">{{ record.title }} ({{ record.department }})</li>

                                <li><b>Salary for</b></li>
                                <li class="content">{{ record.start_date }} ~ {{ record.end_date }}</li>

                                <li>
                                    <div class="tablebox tb_salary">
                                        <ul>
                                            <li><b>Salary per Month</b></li>
                                            <li><b>Salary per Day</b></li>
                                            <li><b>Salary per Minute</b></li>
                                        </ul>

                                        <ul>
                                            <li>{{salary_per_month}}</li>
                                            <li>{{salary_per_day}}</li>
                                            <li>{{salary_per_minute}}</li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>

                        </div>

                        <!-- Earning and Deduction -->
                        <div class="box-content">
                            <div class="heading">Salary Detail</div>

                            <div class="tablebox salary" style="margin-top: 10px;">
                                <ul class="head">
                                    <li>Earnings</li>
                                    <li>Amount</li>
                                    <li>Remarks</li>
                                </ul>

                                <ul v-for='(item, index) in record.detail_plus' :key="index">
                                    <li>{{ item.category }}</li>
                                    <li>{{ (item.amount == 0) ? "" : item.amount }}</li>
                                    <li>{{ item.remark }}</li>
                                </ul>


                                <ul class="footer">
                                    <li>Total Earnings</li>
                                    <li>{{ detail_plus_sum }}</li>
                                </ul>
                            </div>


                            <div class="tablebox salary" style="margin-top: 40px;">
                                <ul class="head">
                                    <li>Deductions</li>
                                    <li>Amount</li>
                                    <li>Remarks</li>
                                </ul>

                                <ul v-for='(item, index) in record.detail_minus' :key="index">
                                    <li>{{ item.category }}</li>
                                    <li>{{ (item.amount == 0) ? "" : item.amount }}</li>
                                    <li>{{ item.remark }}</li>
                                </ul>


                                <ul class="footer">
                                    <li>Total Deductions</li>
                                    <li>{{ detail_minus_sum }}</li>
                                </ul>
                            </div>

                            <ul>
                                <li style="margin-top: 40px;"><b>Total Pay:</b></li>
                                <li class="content" style="font-weight: 700;">{{ detail_sum }}</li>
                            </ul>

                        </div>


                        <!-- Other Information -->
                        <div class="box-content">
                            <div class="heading">Other Information</div>

                            <div class="tablebox loan" style="margin-top: 10px;">
                                <ul class="head">
                                    <li></li>
                                    <li>Previous</li>
                                    <li>Payment</li>
                                    <li>Balance</li>
                                </ul>

                                <ul v-for='(item, index) in record.other' :key="index">
                                    <li>{{ item.category }}</li>
                                    <li>{{ (item.previous == 0) ? "" : item.previous }}</li>
                                    <li>{{ (item.payment == 0) ? "" : item.payment }}</li>
                                    <li>{{ item.remark }}</li>
                                </ul>
                            </div>

                        </div>


                        <!-- Action Buttons -->
                        <div class="modal-footer">

                            <ul style="padding: 25px 25px 5px;" v-if="record.status == 1 || record.status == 2">
                                <li><b>Remarks from Employee</b></li>
                                <li>
                                    <textarea rows="3" style="width: 100%;" readonly>{{ record.remark }}</textarea>
                                </li>
                            </ul>

                            <div class="btnbox">
                                <a class="btn green"  @click="duplicate()">Duplicate</a>
                                <a class="btn green" v-if="record.status == 2 || record.status == 3" @click="revise()">Revise</a>
                                <a class="btn" v-if="record.status == 0" @click="remove(3)">Withdraw</a>
                                <a class="btn" v-if="record.status == 0 || record.status == 2 || record.status == 3" @click="remove(-1)">Delete</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div id="Modal_3" class="modal" :ref="'Modal_3'">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Revise Salary Slip</h6>
                            <a href="javascript: void(0)" @click="ToggleModal(3, 'c')"><i class="fa fa-times fa-lg"
                                                                                      aria-hidden="true"></i></a>
                        </div>

                        <!-- Form beginning -->
                        <form>

                            <!-- Salary slip general description -->
                            <div class="box-content">

                                <ul>
                                    <li><b>Employee Name</b></li>
                                    <li>
                                        <select v-model="employee" disabled>
                                            <option></option>
                                            <option v-for="(item, index) in salary_records" :value="item.uid" :key="item.username">{{ item.username }}</option>
                                        </select>
                                    </li>

                                    <li><b>Salary for</b></li>
                                    <li class="datebox">
                                        <input type="date" v-model="date_start">
                                        <span>~</span>
                                        <input type="date" v-model="date_end">
                                    </li>

                                    <li>
                                        <div class="tablebox tb_salary">
                                            <ul>
                                                <li><b>Salary per Month</b></li>
                                                <li><b>Salary per Day</b></li>
                                                <li><b>Salary per Minute</b></li>
                                            </ul>

                                            <ul>
                                                <li>{{salary_per_month}}</li>
                                                <li>{{salary_per_day}}</li>
                                                <li>{{salary_per_minute}}</li>
                                            </ul>
                                        </div>
                                     </li>
                                </ul>

                            </div>


                            <!-- Earning and Deduction -->
                            <div class="box-content">
                                <div class="heading">Salary Detail</div>

                                <div class="tablebox salary" style="margin-top: 10px;">
                                    <ul class="head">
                                        <li>Earnings</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in detail_plus' :key="index">
                                        <li v-if="item.type == 1">{{ item.category }}</li>
                                        <li v-if="item.type == 0"><input type="text" v-model="item.category" placeholder="Other Earning"></li>
                                        <li><input type="number" v-model="item.amount"></li>
                                        <li><input type="text" v-model="item.remark"></li>
                                        <li><span v-if="item.type == 0" @click=del_plus_detail(item.order)>x</span></li>
                                    </ul>

                                    <ul class="footer">
                                        <li>Total Earnings</li>
                                        <li>{{ detail_plus_sum }}</li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_plus_detail()></i></li>
                                        <li></li>
                                    </ul>
                                </div>


                                <div class="tablebox salary" style="margin-top: 40px;">
                                    <ul class="head">
                                        <li>Deductions</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in detail_minus' :key="index">
                                        <li v-if="item.type == 1">{{ item.category }}</li>
                                        <li v-if="item.type == 0"><input type="text" v-model="item.category" placeholder="Other Deduction"></li>
                                        <li><input type="number" v-model="item.amount"></li>
                                        <li><input type="text" v-model="item.remark"></li>
                                        <li><span v-if="item.type == 0" @click=del_minus_detail(item.order)>x</span></li>
                                    </ul>


                                    <ul class="footer">
                                        <li>Total Deductions</li>
                                        <li>{{ detail_minus_sum }}</li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_minus_detail()></i></li>
                                        <li></li>
                                    </ul>
                                </div>

                                <ul>
                                    <li style="margin-top: 40px;"><b>Total Pay:</b></li>
                                    <li class="content" style="font-weight: 700;">{{ detail_sum }}</li>
                                </ul>

                            </div>

                            <!-- Other Information -->
                            <div class="box-content">
                                <div class="heading">Other Information</div>

                                <div class="tablebox loan">
                                    <ul class="head">
                                        <li></li>
                                        <li>Previous</li>
                                        <li>Payment</li>
                                        <li>Balance</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in other' :key="index">
                                        <li><input type="text"  v-model="item.category"></li>
                                        <li><input type="number" min="0" v-model="item.previous" @change=refresh_other()></li>
                                        <li><input type="number" min="0" v-model="item.payment" @change=refresh_other()></li>
                                        <li>{{ item.remark }}</li>
                                        <li><span @click=del_other_detail(item.order)>x</span></li>
                                    </ul>

                                    <ul class="add_row">
                                        <li></li>
                                        <li></li>
                                        <li></li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_other_detail()></i></li>
                                        <li></li>
                                    </ul>

                                </div>

                            </div>


                            <!-- Action Buttons -->
                            <div class="modal-footer">
                                <div class="btnbox">
                                    <a class="btn" @click="cancel_3()">Cancel</a>
                                    <a class="btn" @click="reset_all()">Reset</a>
                                    <a class="btn green" @click="edit_slip()">Re-Submit</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>


                <div id="Modal_4" class="modal" :ref="'Modal_4'">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Duplicate Salary Slip and Edit</h6>
                            <a href="javascript: void(0)" @click="ToggleModal(4, 'c')"><i class="fa fa-times fa-lg"
                                                                                      aria-hidden="true"></i></a>
                        </div>

                        <!-- Form beginning -->
                        <form>

                            <!-- Salary slip general description -->
                            <div class="box-content">

                                <ul>
                                    <li><b>Employee Name</b></li>
                                    <li>
                                        <select v-model="employee">
                                            <option></option>
                                            <option v-for="(item, index) in salary_records" :value="item.uid" :key="item.username">{{ item.username }}</option>
                                        </select>
                                    </li>

                                    <li><b>Salary for</b></li>
                                    <li class="datebox">
                                        <input type="date" v-model="date_start">
                                        <span>~</span>
                                        <input type="date" v-model="date_end">
                                    </li>

                                    <li>
                                        <div class="tablebox tb_salary">
                                            <ul>
                                                <li><b>Salary per Month</b></li>
                                                <li><b>Salary per Day</b></li>
                                                <li><b>Salary per Minute</b></li>
                                            </ul>

                                            <ul>
                                                <li>{{salary_per_month}}</li>
                                                <li>{{salary_per_day}}</li>
                                                <li>{{salary_per_minute}}</li>
                                            </ul>
                                        </div>
                                     </li>
                                </ul>

                            </div>


                            <!-- Earning and Deduction -->
                            <div class="box-content">
                                <div class="heading">Salary Detail</div>

                                <div class="tablebox salary" style="margin-top: 10px;">
                                    <ul class="head">
                                        <li>Earnings</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in detail_plus' :key="index">
                                        <li v-if="item.type == 1">{{ item.category }}</li>
                                        <li v-if="item.type == 0"><input type="text" v-model="item.category" placeholder="Other Earning"></li>
                                        <li><input type="number" v-model="item.amount"></li>
                                        <li><input type="text" v-model="item.remark"></li>
                                        <li><span v-if="item.type == 0" @click=del_plus_detail(item.order)>x</span></li>
                                    </ul>

                                    <ul class="footer">
                                        <li>Total Earnings</li>
                                        <li>{{ detail_plus_sum }}</li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_plus_detail()></i></li>
                                        <li></li>
                                    </ul>
                                </div>


                                <div class="tablebox salary" style="margin-top: 40px;">
                                    <ul class="head">
                                        <li>Deductions</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in detail_minus' :key="index">
                                        <li v-if="item.type == 1">{{ item.category }}</li>
                                        <li v-if="item.type == 0"><input type="text" v-model="item.category" placeholder="Other Deduction"></li>
                                        <li><input type="number" v-model="item.amount"></li>
                                        <li><input type="text" v-model="item.remark"></li>
                                        <li><span v-if="item.type == 0" @click=del_minus_detail(item.order)>x</span></li>
                                    </ul>


                                    <ul class="footer">
                                        <li>Total Deductions</li>
                                        <li>{{ detail_minus_sum }}</li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_minus_detail()></i></li>
                                        <li></li>
                                    </ul>
                                </div>

                                <ul>
                                    <li style="margin-top: 40px;"><b>Total Pay:</b></li>
                                    <li class="content" style="font-weight: 700;">{{ detail_sum }}</li>
                                </ul>

                            </div>

                            <!-- Other Information -->
                            <div class="box-content">
                                <div class="heading">Other Information</div>

                                <div class="tablebox loan">
                                    <ul class="head">
                                        <li></li>
                                        <li>Previous</li>
                                        <li>Payment</li>
                                        <li>Balance</li>
                                        <li></li>
                                    </ul>

                                    <ul v-for='(item, index) in other' :key="index">
                                        <li><input type="text"  v-model="item.category"></li>
                                        <li><input type="number" min="0" v-model="item.previous" @change=refresh_other()></li>
                                        <li><input type="number" min="0" v-model="item.payment" @change=refresh_other()></li>
                                        <li>{{ item.remark }}</li>
                                        <li><span @click=del_other_detail(item.order)>x</span></li>
                                    </ul>

                                    <ul class="add_row">
                                        <li></li>
                                        <li></li>
                                        <li></li>
                                        <li><i class="fas fa-plus-circle" aria-hidden="true" @click=add_other_detail()></i></li>
                                        <li></li>
                                    </ul>

                                </div>

                            </div>


                            <!-- Action Buttons -->
                            <div class="modal-footer">
                                <div class="btnbox">
                                    <a class="btn" @click="cancel_4()">Cancel</a>
                                    <a class="btn" @click="reset_all()">Reset</a>
                                    <a class="btn green" @click="create_slip()">Submit</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
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
<script src="js/salary_slip_mgt.js"></script>
</html>