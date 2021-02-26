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
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- import CSS -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">



<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
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

        body.green .mainContent > .block .tablebox ul.head,
        body.green .mainContent > .block .formbox li.head {
            background-color: #2F9A57;
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
            background-image: url(../images/ui/icon_form_select_arrow_green.svg);
        }

        body.green a.btn {
            background-color: #2F9A57;
        }

        body.green a.btn:hover {
            background-color: #A9E5BF;
        }

        body.green input[type=date] {
            border: 2px solid #2F9A57;
            padding: 5px;
            background-color: transparent;
        }

        #modal_EditListing {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
        }

        #modal_EditListing > .modal-content {
            width: 90%;
            margin: auto;
            border: 2px solid #2F9A57;
            padding: 20px 25px;
            background-color: white;
            max-height: 850px;
            overflow-y: auto;
        }

        #modal_EditListing .modal-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #modal_EditListing .modal-heading h6 {
            color: #2F9A57;
            border-bottom: none;
        }

        #modal_EditListing .modal-heading a {
            color: #2F9A57;
            font-size: 20px;
        }

        #modal_EditListing .box-content {
            padding: 20px 20px 30px;
        }

        #modal_EditListing .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        #modal_EditListing .tablebox li input[type="checkbox"] {
            -webkit-appearance: checkbox;
            -moz-appearance: checkbox;
            appearance: checkbox;
            display: inline-block;
        }

        .block.A .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
        }


        .block .function input[type="month"] {
            border: 2px solid #2F9A57;
        }

        div.details{
            margin-top: 40px;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #25a2b8;
            cursor: pointer;
            margin: 3px 0;
            display: block;
        }

        div.tablebox.listing {
            margin-top: 15px;
        }

        div.tablebox.listing ul.head li{
            background-color: #E5F7EB!important;
        }

        div.box-content form{
            border: 3px solid #2F9A57;
            margin-top: 40px;
            padding: 15px 15px 0;
            box-sizing: border-box;
        }

        div.details>span {
            font-size: x-large;
            font-weight: 700;
            color: var(--sec03);
            display: block;
            margin-top: 20px;
        }
        
        div.btnbox a.btn.red{
            background-color: var(--pri01a);
        }

        body input.alone.green[type=radio]::before{
            font-size: 25px; color: #2F9A57;}

    </style>



</head>

<body class="green">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
             <a class="tag A" href="apply_for_expense">Apply</a>
             <a class="tag F focus">Liquidate</a>
            <a class="tag B" href="expense_application_records">Records</a>
        </div>
        <!-- Blocks -->
        <div class="block F focus">
            <h6>Expense Application Liquidating</h6>
            <div class="box-content">
                <div class="title">

                    <div class="function">
                    </div>

                </div>
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Request No.</li>
                        <li>Application Time</li>
                        <li>Total Amount Requested</li>
                        <li>Date Released</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone green" :value="record.id" v-model="proof_id">
                        </li>
                        <li>{{ record.request_no }}</li>
                        <li>{{ record.created_at }}</li>
                        <li>{{ isNaN(record.total) ? 0 : Number(record.total).toLocaleString() }}</li>
                        <li>{{ record.date_requested }}</li>
                        
                    </ul>

                </div>


                <div class="details" v-if="proof_id != 0">
                    <div class="tablebox">
                        <ul class="head">
                            <li class="head">Request No.</li>
                            <li>{{record.request_no}}</li>
                        </ul>
                        <ul>
                            <li class="head">Application Time</li>
                            <li>{{record.created_at}}</li>
                        </ul>
                        <ul>
                            <li class="head">Status</li>
                            <li>{{ record.desc }}</li>
                        </ul>
                        <ul>
                            <li class="head">Processing History
                            </li>
                            <li><p v-for='(item, index) in record.history' :key="index">
                                    {{ item.action }} <a v-if="item.reason != ''">: {{ item.reason }}</a> ({{ item.actor }} at {{ item.created_at }})
                                </p>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Date Requested</li>
                            <li>{{ record.date_requested }}</li>
                        </ul>
                        <ul>
                            <li class="head">Type</li>
                            <li>{{record.request_type}}</li>
                        </ul>
                        <ul>
                            <li class="head">Project Name / Reason</li>
                            <li>{{ record.project_name}}</li>
                        </ul>
                        <ul>
                            <li class="head">Total Amount Requested
                            </li>
                            <li>{{ isNaN(record.total) ? "" : Number(record.total).toLocaleString() }}</li>
                        </ul>
                        <ul>
                            <li class="head">Attachments</li>
                            <li><a v-for='(item, index) in record.items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Payable to
                            </li>
                            <li>{{ (record.payable_other == "") ? "Requestor" : (( typeof record.payable_other == "undefined" ) ? "":  "Other:" + record.payable_other) }}</li>
                        </ul>
                        <ul>
                            <li class="head">Remarks or Payment Instructions
                            </li>
                            <li>{{ record.remark }}</li>
                        </ul>
                    </div>

                    <div class="tablebox listing">
                        <ul class="head">
                            <li>Payee</li>
                            <li>Particulars</li>
                            <li>Price</li>
                            <li>Qty</li>
                            <li>Amount</li>
                        </ul>
                        <ul v-for='(item, index) in record.list' :key="index" >
                            <li>{{ item.payee }}</li>
                            <li>{{ item.particulars }}</li>
                            <li>{{ Number(item.price).toLocaleString() }}</li>
                            <li>{{ Number(item.qty).toLocaleString() }}</li>
                            <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                        </ul>
                    </div>


                    <div class="tablebox" style="margin-top: 60px;">
                        <ul class="head">
                            <li class="head">Request No.</li>
                            <li>{{record.request_no}}</li>
                        </ul>
                        <ul>
                            <li class="head">Total Amount Requested
                            </li>
                            <li>{{ isNaN(record.total) ? "" : Number(record.total).toLocaleString() }}</li>
                        </ul>
                        <ul>
                            <li class="head">Date Released</li>
                            <li>{{record.release_date}}</li>
                        </ul>
                        <ul>
                            <li class="head">Proof of Release</li>
                            <li><a v-for='(item, index) in record.release_items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                            </li>
                        </ul>
                         <ul>
                            <li class="head">Date Liquidated</li>
                            <li>{{record.liquidate_date}}</li>
                        </ul>
                        <ul>
                            <li class="head">Amount Liquidated</li>
                            <li>{{ (!record.amount_liquidated) ? "" : Number(record.amount_liquidated).toLocaleString() }}</li>
                        </ul>
                        <ul>
                            <li class="head">Liquidation Files</li>
                            <li><a v-for='(item, index) in record.liquidate_items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a></li>
                            
                        </ul>
                         <ul>
                            <li class="head">Remarks</li>
                            <li>{{ record.remark_liquidated }}</li>
                        </ul>
                    </div>


                    <form>
                        <ul>
                            <li><b>Amount Liquidated</b></li>
                            <li><input type="text" style="width:100%"  v-model="amount_liquidated"></li>

                            <li style="margin-top: 15px;"><b>Upload Liquidation Files</b></li>
                            <li>
                                <input type="file" style="width:100%" ref="file" name="file[]" multiple>
                                <div class="list_attch" v-for="(item,index) in record.liquidate_items" :key="index" >
                                    <input type="checkbox" :id="'file' + item.id" v-model="item.is_checked">
                                    <label :for="'file' + item.id"><a class="attch" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a></label>
                                </div>
                            </li>

                            <li style="margin-top: 15px;"><b>Remarks</b></li>
                            <li><textarea style="width:100%;" placeholder="Require to write the reason here if no liquidation file was attached in the above." v-model="reject_reason"></textarea></li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn" v-if="record.status == 6" @click="approve_op">Submit</a>
                            <a class="btn" v-if="record.status == 7" @click="approve_op">Re-Submit</a>
                        </div>
                    </form>


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
<script defer src="js/a076d05399.js"></script>

<script>
  ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/expense_liquidating.js"></script>
</html>