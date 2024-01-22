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

<style>
   @media screen and (min-width: 0px) and (max-width: 767px) {
    #my-content { display: none; }  /* hide it on small screens */
    }

    @media screen and (min-width: 768px) and (max-width: 1024px) {
    #my-content { display: block; }   /* show it elsewhere */
    }
</style>

<body class="cyan">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent" id="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Employee Data Sheet</a>
            <a class="tag F" href="user_profile">User Profile</a>
            <a class="tag B" href="department">Department</a>
            <a class="tag C" href="position">Position</a>
            <a class="tag D" href="leave_flow">Leave Flow</a>
            <a class="tag E" href="expense_flow">Expense Flow</a>
        </div>

        <!-- Blocks -->
        <div class="block A focus">
            <h6>Employee Data Sheet Management</h6>

            <div class="box-content">

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li style="font-size:10px;">Employee Name</li>
                        <li style="font-size:10px;">Department</li>
                        <li style="font-size:10px;">Position</li>
                        <li style="font-size:10px;">Updated Time</li>
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone green" :value="record.index"
                                   v-model="record.is_checked">
                        </li>
                        <li style="font-size:10px;">{{record.username}}</li>
                        <li style="font-size:10px;">{{record.department}}</li>
                        <li style="font-size:10px;">{{record.title}}</li>
                        <li style="font-size:10px;">{{ }}</li>
                    </ul>

                </div>

                <div class="btnbox">
                    <a class="btn" @click="editRecord()">Edit</a>
                    <a class="btn" @click="resetRecord()">Reset</a>
                </div>

            </div>


            <!-- Input Modal start -->
            <div id="Modal_2" class="modal">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Data Sheet</h6>
                        <a href="javascript: void(0)" onclick="ToggleModal(2)">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <ul>
                            <li><b>Position:</b></li>
                            <li class="content">{{ user.department }} >> {{ user.position }}</li>

                            <li><b>Date:</b></li>
                            <li class="user_input">
                                <input type="date" readonly>
                            </li>

                            <li><b>Name:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Gender:</b></li>
                            <li class="user_input">
                                <select>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </li>

                            <li><b>Present Address:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Permanent Address:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Telephone Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Cellphone Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Email Address:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Date of Birth:</b></li>
                            <li class="user_input">
                                <input type="date">
                            </li>

                            <li><b>Place of Birth:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Civil Status:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Citizenship:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Height:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Weight:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Religion:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Language/Dialect Spoken:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Medical Condition/Allergies:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Spouse:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Spouse's Occupation:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Name of Children:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Father's Name:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Father's Occupation:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Mother's Name:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Mother's Occupation:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Name of Siblings:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>TIN Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>SSS Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Philhealth Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Pag-ibig Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                        </ul>

                    </div>

                    <div class="box-content">
                        <span>Person to contact in case of emergency</span>

                        <ul>
                            <li><b>Name:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Address:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>His/Her Contact Number:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Relationship:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                        </ul>

                    </div>

                    <div class="box-content">
                        <span>Educational Background</span>

                        <ul>
                            <li><b>Elementary:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Year Graduated:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>High School:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Year Graduated:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>College:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Year Graduated:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                        </ul>

                    </div>

                    <div class="box-content">
                        <span>Employment Record</span>

                        <ul>
                            <li><b>Company:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Position:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Period:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Company:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Position:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>

                            <li><b>Period:</b></li>
                            <li class="user_input">
                                <input type="text">
                            </li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn" @click="cancel">Cancel</a>
                            <a class="btn green" @click="save">Save</a>
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
<script defer src="../js/admin/user.js"></script>
</html>
