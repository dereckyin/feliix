<?php include 'check.php';?>
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

<style type="text/css">
    .box-content  table {border-top: 2px solid var(--pri01a);
    border-left: 2px solid var(--pri01a);
    width: 100%;}
    .box-content  table  tr   th { background-color: var(--pri01c); font-weight: 800; border-bottom: 2px solid var(--pri01a); border-right: 2px solid var(--pri01a);text-align: center; padding: 10px;}
    .box-content   table   tr   td { font-weight: 800; border-bottom: 2px solid var(--pri01a); border-right: 2px solid var(--pri01a);text-align: center; padding: 10px;}
</style>

</head>

<body class="primary">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Apply for Leave</a>
            <a class="tag B" href="leave_record">Leave Record</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Leaves Summary</h6>
            <div class="box-content">
                <!-- 表格樣式 -->
                <div class="title">
                
                    <div class="function">
                        <input type="month" id="start" name="start" @change="getLeaveCredit()">

                        <input type="month" id="end" name="end" @change="getLeaveCredit()">
                    </div>
                </div>

                <table v-if="is_manager === '1'">
                  <tr>
                    <th>Leave Type</th>
                    <th>Yearly Credits</th>
                    <th>Taken</th>
                    <th>Waiting for Approval</th>
                  </tr>
                  <tr>
                    <td>Vacation Leave</td>
                    <td rowspan="2">{{ manager_leave }} Days</td>
                    <td>{{ al_taken }} Days</td>
                    <td>{{ al_approval }} Days</td>
                  </tr>
                  <tr>
                    <td>Emerency/Sick Leave</td>
               
                    <td>{{ sl_taken }} Days</td>
                    <td>{{ sl_approval }} Days</td>
                  </tr>
                  <tr>
                    <td>Unpaid Leave</td>
                    <td>--</td>
                    <td>{{ pl_taken }} Days</td>
                    <td>{{ pl_approval }} Days</td>
                  </tr>

                  <tr>
                    <td>Absence</td>
                    <td>--</td>
                    <td>{{ ab_taken }} Days</td>
                    <td>{{ ab_approval }} Days</td>
                  </tr>
                </table>

                <div class="tablebox" v-else>
                    <ul class="head">
                        <li>Leave Type</li>
                        <li>Yearly Credits</li>
                        <li>Taken</li>
                        <li>Waiting for Approval</li>
      
                    </ul>
                    <ul>
                        <li>Vacation Leave</li>
                        <li>{{ al_credit }} Days</li>
                        <li>{{ al_taken }} Days</li>
                        <li>{{ al_approval }} Days</li>
                  
                    </ul>
                    <ul>
                        <li>Emerency/Sick Leave</li>
                        <li>{{ sl_credit }} Days</li>
                        <li>{{ sl_taken }} Days</li>
                        <li>{{ sl_approval }} Days</li>
                
                    </ul>
                    <ul>
                        <li>Unpaid Leave</li>
                        <li>--</li>
                        <li>{{ pl_taken }} Days</li>
                        <li>{{ pl_approval }} Days</li>
                
                    </ul>

                    <ul>
                        <li>Absence</li>
                        <li>--</li>
                        <li>{{ ab_taken }} Days</li>
                        <li>{{ ab_approval }} Days</li>
                
                    </ul>
                </div>
                <!-- 表單樣式 -->
                <div class="title">
                    <b>Leave Application Form</b>
                </div>
                <div class="formbox">
                    <ul>
                        <li class="head" style="border-top-left-radius: 7px; border-bottom-left-radius: 7px;">Employee Name</li>
                        <li>{{ name }}</li>
                    </ul>
                    <ul style="display:flex;">
                        <li class="head" style="border-top-left-radius: 7px; border-bottom-left-radius: 7px; line-height: 44px;">Leave Type</li>
                        <li style="flex-grow:1; flex-shrink:1;">
                            <select name="" id="" v-model="leave_type" style="width: 100%; border: 2px solid var(--pri01a);">
                                <option value="A">Vacation Leave</option>
                                <option value="B">Emerency/Sick Leave</option>
                                <option value="C">Unpaid Leave</option>
                              
                            </select>
                        </li>
                    </ul>

                    <ul v-if="showExtra" style="display:flex;">
                        <li class="head" v-if="showExtra" style="border-top-left-radius: 7px; border-bottom-left-radius: 7px; line-height: 44px;">Certificate of Diagnosis</li>
                        <li v-if="showExtra" style="flex-grow:1; flex-shrink:1; flex-basis: 30%; display: flex; align-items: center;"><input type="file" id="file" style="width: 100%;" ref="file" v-on:change="onChangeFileUpload()" accept="image/*" capture="camera"></li>
                    </ul>
                    
                    <div class="group">
                        <ul style="display:flex;">
                            <li class="head" style="border-top-left-radius: 12px; border-bottom-left-radius: 0px; line-height: 36px;">Start Time</li>
                            <li style="flex-grow:1; flex-shrink:1;">
                            <input type="datetime-local" v-model="apply_start" style="width: 100%; border: 2px solid var(--pri01a);" />
                            </li>
                        </ul>
                        <ul style="display:flex;">
                            <li class="head" style="border-top-left-radius: 0px; border-bottom-left-radius: 12px; line-height: 36px;">End Time</li>
                            <li style="flex-grow:1; flex-shrink:1;">
                            <input type="datetime-local" v-model="apply_end" style="width: 100%; border: 2px solid var(--pri01a);" />
                            </li>
                        </ul>
                    </div>
                    <ul>
                        <li class="head" style="border-top-left-radius: 7px; border-bottom-left-radius: 7px;">Leave Length</li>
                        <li>{{ period }}</li>
                    </ul>
                    <ul style="display:flex;">
                        <li class="head" style="border-top-left-radius: 7px; border-bottom-left-radius: 7px; line-height: 88px;">Reason</li>
                        <li style="flex-grow:1; flex-shrink:1;">
                            <textarea name="message" rows="3" cols="20" v-model="reason" style="width: 100%;" >
                
                            </textarea>
                        </li>
                    </ul>
                   
                    <div class="btnbox">
                    <a class="btn" @click="reset">Reset</a>
                    <a class="btn" @click="apply" :disabled="submit">Submit</a>
                    </div>
                </div>
                <!-- 表單樣式 -->
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

<script>
  ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/apply_for_leave.js"></script>
</html>
