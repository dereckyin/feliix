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

            $database = new Database();
            $db = $database->getConnection();

            $access_attendance = false;

            $vote1 = false;

            //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
            //    $access2 = true;
            $query = "SELECT * FROM access_control WHERE (vote1 LIKE '%" . $username . "%' or vote2 LIKE '%" . $username . "%') ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vote1 = true;
            }

            // 可以存取Expense Recorder的人員名單如下：Dennis Lin(2), Glendon Wendell Co(4), Kristel Tan(6), Kuan(3), Mary Jude Jeng Articulo(9), Thalassa Wren Benzon(41), Stefanie Mika C. Santos(99)
            // 為了測試先加上testmanager(87) by BB
            if($vote1 != true) 
            {
                header( 'location:index' );
            }
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
    <title>Voting Topic Management</title>
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


    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })

        function ToggleModal(target) {
            $(".mask").toggle();

            if (target == 1) {
                $("#Modal_1").toggle();
            }else if (target == 2) {
                $("#Modal_2").toggle();
            }else if (target == 3) {
                $("#Modal_3").toggle();
            }
        }
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
        }

        .list_function .searching select {
            width: 150px;
            font-size: 15px;
            padding: 4px 7px;
            margin-right: 10px;
        }

        .list_function .searching input {
            width: 210px;
            font-size: 15px;
            padding: 4px 7px;
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
            width: 90%;
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
        }

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=text], .modal .modal-content .box-content input[type=date], .modal .modal-content .box-content textarea {
            border: 1px solid black;
            width: 100%;
            padding: 8px 35px 8px 15px;
        }

        .modal .modal-content .box-content .itembox {
            display: inline-block;
            margin: 5px 0;
        }

        .modal .modal-content .box-content .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 90px;
            height: 90px;
            padding: 3px;
            position: relative;
        }

        .modal .modal-content .box-content .itembox .photo::before {
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

        .modal .modal-content .box-content .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .modal .modal-content .box-content .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .modal .modal-content .box-content .itembox.chosen .photo::before {
            content: none;
        }

        .modal .modal-content .box-content .itembox .photo > div {
            display: none;
        }

        .modal .modal-content .box-content .itembox.chosen .photo > div {
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

        .list_table {
            border: 2px solid #2F9A57;
            width: 100%;
        }

        .list_table th, .list_table td{
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table td i {
            font-size: 25px;
        }

        .list_table td i:nth-of-type(odd) {
            margin-right: 5px;
        }

        .list_table tr td:nth-of-type(1){
            width: 300px;
            font-weight: 600;
            background-color: #E5F7EB;
        }

        .list_table tr td:nth-of-type(2){
            width: 250px;
        }

        .list_table tr td:nth-of-type(2) img{
             width: 80%;
             object-fit: contain;
         }

        .list_table tr td:nth-of-type(3){
            width: 500px;
            text-align: left;
        }

        .list_table tr td:nth-of-type(4){
            width: 350px;
            text-align: left;
        }

        .list_table tr td:nth-of-type(5){
            width: 110px;
            line-height: 2;
        }


    </style>




</head>

<body class="green">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="voting_system">Voting System</a>
            <a class="tag B focus">Voting Topic Management</a>
        </div>
        <!-- Blocks -->
        <div class="block B focus">
            <h6>Voting Topic Management</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create" href="javascript: void(0)" onclick="ToggleModal(1)"></a>

                            <div class="searching">
                                <select v-model="fil_status">
                                    <option value="all">All</option>
                                    <option value="not_yet">Not Yet Start</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="finished">Finished</option>
                                </select>
                                <input type="text" placeholder="Searching Keyword Here" v-model="keyword">
                                <button style="border: none;" @click="search()"><i class="fas fa-search-plus"></i></button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1"  @click="pre_page(); filter_apply();">Prev 10</a>
                            <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[page==pg ? { 'background':'#2F9A57', 'color': 'white'} : { }]" >{{ pg }}</a>
                            <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Topic</li>
                        <li>Status</li>
                        <li>Voting Time</li>
                        <li>Created Time</li>
                        <li>Updated Time</li>
                        <li>Voter Turnout</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                        <input type="radio" name="record_id" class="alone green" :value="record.id" v-model="proof_id">
                        </li>
                        <li>{{ record.topic }}</li>
                        <li>{{ record.vote_status}}</li>
                        <li>{{ record.start_date }} ~ {{ record.end_date }}</li>
                        <li>{{ record.created_by }}<br>{{ record.created_at }}</li>
                        <li>{{ record.updated_by }}<br>{{ record.updated_at }}</li>
                        <li>{{ record.votes.length }}</li>
                    </ul>


                </div>

                <div class="btnbox" style="display: flex; justify-content: center;">
                    <a class="btn green" @click="view_detai()">Detail</a>
                    <a class="btn green" @click="duplicate()">Duplicate</a>
                    <a class="btn green" @click="edit_detail()">Edit</a>
                    <a class="btn" @click="remove()">Delete</a>
                </div>


                <div id="Modal_1" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Create New Voting Topic</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(1)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Voting Topic general description -->
                        <div class="box-content" >

                            <ul>
                                <li><b>Topic Name</b></li>
                                <li><input type="text" required style="width:100%" v-model="topic"></li>

                                <li><b>Voting Time</b></li>
                                <li>
                                    <input type="date" v-model="start_date" style="width: 49%; margin-right: 1.5%;">
                                    <input type="date" v-model="end_date" style="width: 49%;">
                                </li>

                                <li><b>Who Can Vote</b></li>
                                <li>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="Choose Access..." id="access" v-model="access">

                                        <optgroup label="By Group">
                                            <option value="all">All</option>
                                            <option value="sales">Sales Department</option>
                                            <option value="lighting">Lighting Department</option>
                                            <option value="office">Office Department</option>
                                            <option value="design">Design Department</option>
                                            <option value="engineering">Engineering Department</option>
                                            <option value="admin">Admin Department</option>
                                            <option value="store">Store Department</option>
                                        </optgroup>

                                        <optgroup label="By Person">
                                            <?php foreach ($user_results as $user) { ?>
                                            <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                            <?php } ?>
                                        </optgroup>

                                    </select>
                                    
                                </li>

                                <li><b>Voting Rule</b></li>
                                <li>
                                    <select v-model="rule">
                                        <option value="1">one person - one vote</option>
                                        <option value="2">one person - two votes</option>
                                        <option value="3">one person - three votes</option>
                                    </select>
                                </li>

                                <li><b>Rule of Displaying Result</b></li>
                                <li>
                                    <select v-model="display" style="width: 49%; margin-right: 1.5%;">
                                        <option value="1">Top 1</option>
                                        <option value="2">Top 3</option>
                                        <option value="3">Top 5</option>
                                        <option value="4">Top 10</option>
                                        <option value="all">All</option>
                                    </select>

                                    <select v-model="sort" style="width: 49%;">
                                        <option value="1">Descending</option>
                                        <option value="2">Ascending</option>
                                    </select>
                                </li>
                            </ul>

                        </div>


                        <!-- Option content -->
                        <div class="box-content" :ref="'addto'">

                            <ul>
                                <li><b>Option Name</b></li>
                                <li><input type="text" required style="width:100%" v-model="title"></li>

                                <li><b>Image</b></li>
                                <li>
                                    <!-- "['itembox', (block.url !== '' ? 'chosen' : '')]" -->
                                    <div :class="['itembox', (block.url !== '' ? 'chosen' : '')]">
                                        <div class="photo">
                                            <input type="file" :name="'block_image_' + 0" ref="block_image_0"
                                                   @change="onFileChangeImage($event, block, 0)"
                                                   :id="'block_image_' + 0">
                                            <img v-if="block.url" :src="block.url"/>
                                            <div @click="clear_photo(block, 0)">x</div>
                                        </div>
                                    </div>
                                </li>

                                <li><b>Description</b></li>
                                <li><textarea type="text" style="width:100%" v-model="description"></textarea></li>

                                <li><b>Web Link</b></li>
                                <li><input type="text" style="width:100%" v-model="link"></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green" @click="add_criterion" v-if="!editing">Add Option</a>
                                <a class="btn" v-if="editing" @click="cancel_criterion" >Cancel</a>
                                <a class="btn green" v-if="editing" @click="update_criterion()">Update Option</a>
                            </div>


                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th>Option Name</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Web Link</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in blocks'>
                                    <td>
                                        {{ record.title }}
                                    </td>
                                    <td>
                                    <img v-if="record.url" :src="record.url"/>
                                    </td>
                                    <td>
                                        {{ record.description }}
                                    </td>
                                    <td>
                                        <a>{{ record.link }}</a>
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="set_up(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="set_down(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="edit(record)"></i>
                                        <i class="fas fa-trash-alt" @click="del(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                        </div>
                        
                        <!-- Button to Create Voting Topic -->
                        <div class="modal-footer">
                            <div class="btnbox">
                                <a class="btn green" :disabled="submit == true" @click="create_template()">Create Voting Topic</a>
                            </div>
                        </div>

                    </div>

                </div>



                <div id="Modal_2" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Detail of Voting Topic</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(2)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Voting Topic general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Topic Name</b></li>
                                <li class="content">{{ record.topic }}</li>

                                <li><b>Status</b></li>
                                <li class="content">{{ record.vote_status }}</li>

                                <li><b>Voting Time</b></li>
                                <li class="content">{{ record.start_date }} ~ {{ record.end_date }}</li>

                                <li><b>Who Can Vote</b></li>
                                <li class="content">{{ record.access_text }}</li>

                                <li><b>Voting Rule</b></li>
                                <li class="content">{{ record.rule_text }}</li>

                                <li><b>Rule of Displaying Result</b></li>
                                <li class="content">{{ record.displa_texty }}</li>

                                <li><b>Created Time</b></li>
                                <li class="content">{{ record.created_by + " at " + record.created_at }}</li>

                                <li><b>Updated Time</b></li>
                                <li class="content">{{ ( record.updated_by == null ) ? "": record.updated_by + " at " + record.updated_at }}</li>
                            </ul>

                        </div>


                        <!-- Option content -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th>Option Name</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Web Link</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for='(item, index) in record.details' :key="index">
                                    <td>
                                        {{ item.title }}
                                    </td>
                                    <td>
                                        <img v-if="item.url" :src="item.url"/>
                                    </td>
                                    <td>
                                        {{ item.description }}
                                    </td>
                                    <td>
                                        <a v-if="item.link" :href="item.link">{{ item.link }}</a>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                <div id="Modal_3" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Edit Voting Topic</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(3)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Voting Topic general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Topic Name</b></li>
                                <li><input type="text" required style="width:100%" v-model="record.topic"></li>

                                <li><b>Voting Time</b></li>
                                <li>
                                    <input type="date" v-model="record.start_date" style="width: 49%; margin-right: 1.5%;">
                                    <input type="date" v-model="record.end_date" style="width: 49%;">
                                </li>

                                <li><b>Who Can Vote</b></li>
                                <li>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="Choose Access..." id="access_edit" v-model="record.access_array">

                                        <optgroup label="By Group">
                                            <option value="all">All</option>
                                            <option value="sales">Sales Department</option>
                                            <option value="lighting">Lighting Department</option>
                                            <option value="office">Office Department</option>
                                            <option value="design">Design Department</option>
                                            <option value="engineering">Engineering Department</option>
                                            <option value="admin">Admin Department</option>
                                            <option value="store">Store Department</option>
                                        </optgroup>

                                        <optgroup label="By Person">
                                            <?php foreach ($user_results as $user) { ?>
                                            <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                            <?php } ?>
                                        </optgroup>

                                    </select>
    
                                </li>

                                <li><b>Voting Rule</b></li>
                                <li>
                                    <select v-model="record.rule">
                                        <option value="1">one person - one vote</option>
                                        <option value="2">one person - two votes</option>
                                        <option value="3">one person - three votes</option>
                                    </select>
                                </li>

                                <li><b>Rule of Displaying Result</b></li>
                                <li>
                                    <select v-model="record.display" style="width: 49%; margin-right: 1.5%;">
                                        <option value="1">Top 1</option>
                                        <option value="2">Top 3</option>
                                        <option value="3">Top 5</option>
                                        <option value="4">Top 10</option>
                                        <option value="5">All</option>
                                    </select>

                                    <select v-model="record.sort" style="width: 49%;">
                                        <option value="1">Descending</option>
                                        <option value="2">Ascending</option>
                                    </select>
                                </li>
                            </ul>

                        </div>


                        <!-- Option content -->
                        <div class="box-content" :ref="'porto'">

                            <ul>
                                <li><b>Option Name</b></li>
                                <li><input type="text" required style="width:100%" v-model="title"></li>

                                <li><b>Image</b></li>
                                <li>
                                    <div :class="['itembox', (block.url !== '' ? 'chosen' : '')]">
                                        <div class="photo">
                                            <input type="file" :name="'block_image_' + 0" ref="block_image_1"
                                                   @change="onFileChangeImage($event, block,0)"
                                                   :id="'block_image_' + 0">
                                            <img v-if="block.url" :src="block.url"/>
                                            <div @click="clear_photo(block, 0)">x</div>
                                        </div>
                                    </div>
                                </li>

                                <li><b>Description</b></li>
                                <li><textarea type="text" style="width:100%" v-model="description"></textarea></li>

                                <li><b>Web Link</b></li>
                                <li><input type="text" style="width:100%" v-model="link"></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green" @click="e_add_criterion" v-if="!e_editing">Add Option</a>
                                <a class="btn" v-if="e_editing" @click="e_cancel_criterion" >Cancel</a>
                                <a class="btn green" v-if="e_editing" @click="e_update_criterion">Update Option</a>
                            </div>


                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th>Option Name</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Web Link</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(item, index) in record.details'>
                                    <td>
                                        {{ item.title }}
                                    </td>
                                    <td>
                                        <img v-if="item.url" :src="item.url">
                                    </td>
                                    <td>
                                        {{ item.description }}
                                    </td>
                                    <td>
                                        <a v-if="item.link" :href="item.link">{{ item.link }}</a>
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="e_set_up(index, item.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="e_set_down(index, item.id)"></i>
                                        <i class="fas fa-edit" @click="e_edit(item)"></i>
                                        <i class="fas fa-trash-alt" @click="e_del(item.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>


                        <!-- Button to Update Voting Topic -->
                        <div class="modal-footer">
                            <div class="btnbox">
                                <a class="btn green" @click="update_template()">Update Voting Topic</a>
                            </div>
                        </div>

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
<script src="js/voting_topic_mgt.js"></script>
</html>