<?php include 'check.php';?>
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
<title>Management of Employee Basic Info</title>
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

<style>

    div.btnbox a.btn {
        width: 120px;
    }

    a.btn.red {
        background-color: var(--pri01a)!important;
    }

    a.btn.red:hover {
        background-color:var(--pri01b)!important;
    }

    body input.alone.cyan[type=radio]::before, .block input.cyan[type=radio]+Label::before {
        font-size: 25px;
        color: var(--cyan01);
    }

    .bodybox .mask {
        position: absolute;
        background: rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100%;
        top: 0;
        z-index: 1;
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
        width: 65%;
        margin: auto;
        border: 3px solid var(--cyan01);
        padding: 20px 0 0;
        background-color: white;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
    }

    .modal .modal-content .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 25px 15px;
        border-bottom: 2px solid var(--cyan01);
    }

    .modal .modal-content .modal-header h6 {
        color: var(--cyan01);
        border-bottom: none;
        padding: 0;
    }

    .modal .modal-content .modal-header a {
        color: var(--cyan01);
        font-size: 20px;
    }

    .modal .modal-content .box-content {
        padding: 20px 25px 25px;
        border-bottom: 2px solid var(--cyan01);
    }

    .modal .box-content ul li:nth-of-type(even) {
        margin-bottom: 15px;
    }

    .modal .modal-content .box-content select,
    .modal .modal-content .box-content input[type="date"] {
        border: 2px solid var(--cyan01);
        width: 250px;
        padding: 8px 35px 8px 15px;
    }

    .modal .modal-content .box-content input[type="text"] {
        width: 100%;
    }

    .modal .box-content ul li.content {
        padding: 3px 0;
        font-weight: 500;
        font-size: 18px;
        text-align: left;
        border-bottom: 2px solid var(--cyan01);
        vertical-align: middle;
    }

    .modal .box-content div.subtitle {
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 10px;
        text-decoration: 1px underline;
        color: var(--cyan01);
    }

    .modal .box-content .info_sheet {
        width: 100%;
    }


    .modal .box-content .info_sheet tr td {
        width: 100%;
        font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
        font-size: 16px;
        font-weight: 400;
        padding: 4px 7px;
    }

    .modal .box-content .info_sheet tr td span.caption {
        font-weight: 700;
    }

    @media screen and (min-width: 0px) and (max-width: 767px) {
        #my-content { display: none; }  /* hide it on small screens */
    }

    @media screen and (min-width: 768px) and (max-width: 1024px) {
        #my-content { display: block; }   /* show it elsewhere */
    }

</style>

</head>


<body class="cyan">
 	
<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display: none;"></div>

    <!-- header -->
	<header>header</header>
    <!-- header end -->

    <div class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="">Employee Data Sheet</a>
            <a class="tag B" focus>Basic Info</a>
        </div>

        <!-- Blocks -->
        <div class="block B focus">
            <h6>Management of Employee Basic Info</h6>

            <div class="box-content">

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Employee Name</li>
                        <li>Department</li>
                        <li>Position</li>
                        <li>Updated Time</li>
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone cyan" :value="record.index"
                                   v-model="record.is_checked">
                        </li>
                        <li>{{record.username}}</li>
                        <li>{{record.department}}</li>
                        <li>{{record.title}}</li>
                        <li>{{ }}</li>
                    </ul>

                </div>

                <div class="btnbox">
                    <a class="btn" @click="editRecord()">View</a>
                    <a class="btn" @click="editRecord()">Edit</a>
                    <a class="btn" @click="resetRecord()">Reset</a>
                </div>

            </div>


            <!-- Input Modal start -->
            <div id="Modal_input" class="modal" style="display: none;">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Basic Info</h6>
                        <a href="javascript: void(0)" onclick="ToggleModal(2)">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <ul>
                            <li><b>Employee Number:</b></li>
                            <li>
                                <input type="text">
                            </li>

                            <li><b>First Name:</b></li>
                            <li>
                                <input type="text">
                            </li>

                            <li><b>Middle Name:</b></li>
                            <li>
                                <input type="text">
                            </li>

                            <li><b>Surname:</b></li>
                            <li>
                                <input type="text">
                            </li>

                            <li><b>Date Hired:</b></li>
                            <li>
                                <input type="date">
                            </li>

                            <li><b>Regularization Date:</b></li>
                            <li>
                                <input type="date">
                            </li>

                            <li><b>Employment Status:</b></li>
                            <li>
                                <select>
                                    <option value="">PROBATION</option>
                                    <option value="">REGULAR</option>
                                </select>
                            </li>

                            <li><b>Company:</b></li>
                            <li>
                                <input type="text">
                            </li>

                            <-- 會載入目前系統上所建立的所有部門名稱當作 option，然後系統根據這位使用者在 user 資料表中已經設定的部門值，把它當作這個欄位的預選值  -->
                            <li><b>Department:</b></li>
                            <li>
                                <select>
                                    <option value=""></option>
                                </select>
                            </li>

                            <-- 會載入目前系統上所建立的該部門之下所有的職稱名稱當作 option，然後系統根據這位使用者在 user 資料表中已經設定的職稱，把它當作這個欄位的預選值  -->
                            <li><b>Position Title:</b></li>
                            <li>
                                <select>
                                    <option value=""></option>
                                </select>
                            </li>

                            <li><b>Employee Category:</b></li>
                            <li>
                                <select>
                                    <option value="">STAFF</option>
                                    <option value="">RANK & FILE</option>
                                    <option value="">SENIOR</option>
                                    <option value="">ASSISTANT DEPARTMENT MANAGER</option>
                                    <option value="">DEPARTMENT MANAGER</option>
                                </select>
                            </li>

                            <-- 系統會載入目前網站上已經註冊且 status=1 的使用者名稱當作 option  -->
                            <li><b>Next Level Manager/Superior:</b></li>
                            <li>
                                <select>
                                    <option value="">Male</option>
                                </select>
                            </li>

                        </ul>

                        <div class="btnbox">
                            <a class="btn red" @click="cancel">Cancel</a>
                            <a class="btn" @click="save">Save</a>
                        </div>

                    </div>

                </div>

            </div>
            <!-- Input Modal end -->



            <!-- Input Modal start -->
            <div id="Modal_view" class="modal">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Basic Info</h6>
                        <a href="javascript: void(0)" onclick="ToggleModal(2)">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <table class="info_sheet">

                            <-- 以下欄位載入值時，系統都需要把載入的值轉換成英文大寫，再放入欄位中  -->
                            <tbody>
                            <tr>
                                <td>
                                    <span class="caption">Employee Number</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">First Name</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Middle Name</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Surname</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <-- 日期載入值的格式為： 完整月份英文 日, 四位數西元年分，例如： DECEMBER 12, 2023  -->
                            <tr>
                                <td>
                                    <span class="caption">Date Hired</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Regularization Date</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Employment Status</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Company</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Department</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Position Title</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Employee Category</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Next Level Manager/Superior</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="content">{{ }}</span>
                                </td>
                            </tr>

                            </tbody>

                        </table>

                        <div class="btnbox" style="margin-bottom: -20px;">
                            <a class="btn red" @click="cancel">Close</a>
                        </div>

                    </div>

                </div>

            </div>
            <!-- Input Modal end -->


        </div>
        
    </div>
</div>
</body>
<script defer src="../js/npm/vue/dist/vue.js"></script> 
<script defer src="../js/axios.min.js"></script> 
<script defer src="../js/npm/sweetalert2@9.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script defer src="../js/admin/user.js"></script>
</html>
