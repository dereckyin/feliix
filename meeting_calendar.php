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
            $user_id = "";
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

            $GLOBALS['username'] = $decoded->data->username;
            $GLOBALS['position'] = $decoded->data->position;
            $GLOBALS['department'] = $decoded->data->department;

            if(!is_numeric($user_id))
                header( 'location:index' );

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
    <meta charset='utf-8'/>
    <title>
        Meeting Calendar
    </title>

    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

    <link rel="stylesheet" href="css/vue-select.css" type="text/css">

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/fullcalendar@5.1.0/main.min.css">


    <script type="text/javascript" src="https://unpkg.com/fullcalendar@5.1.0/main.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <style>

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        body{
            height: 130vh;
        }

        #calendar {
            max-width: 90%;
            margin: 40px auto;
        }

        #addmeeting-form, #editmeeting-form {
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            font-weight: 300;
            max-width: 90%;
            margin: 0 auto 40px;
        }

        #addmeeting-form fieldset, #editmeeting-form fieldset {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px 30px;
        }

        #addmeeting-form legend, #editmeeting-form legend {
            margin-left: 10px;
            font-size: 24px;
            padding: 0 5px;
        }

        #addmeeting-form input, #editmeeting-form input {
            width: 160px;
            margin-right: 10px;
            height: 35px;
        }

        #addmeeting-form input[type="text"], #editmeeting-form input[type="text"], #addmeeting-form input[type="file"], #editmeeting-form input[type="file"] {
            width: 500px;
        }

        .meetingform-buttons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .meetingform-buttons a {
            margin: 0 20px;
            width: 80px;
            text-align: center;
        }

        .meetingform-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .meetingform-item label {
            color: #00811e;
            font-size: 14px;
            font-weight: 500;
            width: 100px;
        }

        .meetingform-item input,
        .meetingform-item select,
        .meetingform-item textarea {
            border: 1px solid #707070;
            font-size: 14px;
            outline: none;
        }

        .meetingform-item input:disabled,
        .meetingform-item select:disabled,
        .meetingform-item textarea:disabled {
            border: 1px solid #707070;
            font-size: 14px;
            outline: none;
            opacity: 1;
        }

        .file-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .file-element {
            margin-bottom: 5px;
            margin-left: 105px;
        }

        .file-element input[type="checkbox"] + label::before {
            color: #007bff;
            font-size: 20px;
        }

        .file-element input[type="checkbox"]:disabled + label::before {
            color: rgba(127, 189, 255, 0.8);
        }

        .file-element a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .file-element a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .fc-daygrid-event {
            white-space: initial !important;
        }

        .fc-event-title {
            display: inline !important;
        }

        @media (min-width: 576px) {

            .modal-xl {
                max-width: 90vw;
            }
        }

        @media (min-width: 992px) {
            .modal-xl {
                max-width: 800px;
            }

        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }
        }

    </style>

</head>
<body>


<div style="background: rgb(2,106,167); padding: 0.5vh; height:7.5vh;">

    <a href="default" style="margin-left:1vw; position: relative; top:-10%;"><span style="color: white;">&#9776;</span></a>

    <a href="default"><span
            style="margin-left:1vw; font-weight:700; font-size:xx-large; color: white;">FELIIX</span></a>

</div>


<div id='calendar'></div>

<div id='app' style='padding-bottom: 20px;'>
<form id="addmeeting-form" style="display: none;">
    <fieldset>
        <legend> Meeting Information</legend>

        <div class="meetingform-item">
            <label>Subject:</label>
            <input type="text" id="newSubject">
        </div>

        <div class="meetingform-item">
            <label>Project:</label>
            <input type="text" id="newProject" placeholder="Project name will be added ahead of subject if filled in">
        </div>

        <div class="meetingform-item">
            <label>Attendee:</label>
            <v-select id="newAttendee" :options="users" attach chips label="username" v-model="attendee"
                      multiple></v-select>
        </div>

        <div class="meetingform-item">
            <label>Time:</label>
            <input type="date" id="newDate">
            <input type="time" id="newStartTime">
            <input type="time" id="newEndTime">
        </div>

        <div class="meetingform-item">
            <label>Content:</label>
            <textarea style="flex-grow: 1; resize: none;" rows="3" id="newContent"></textarea>

        </div>

        <div class="meetingform-item" id="upload_input">
            <label>File:</label>
            <input type="file" ref="file" id="fileload" name="file[]" onChange="onChangeFileUpload(event)" multiple>
        </div>

        <div class="file-container" id="sc_product_files">

        </div>

        <input id="sc_product_files_hide" style="display: none;" value="">


        <div class="meetingform-buttons">
            <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#addmeeting-form')">Close</a>

            <a class="btn small green" id="btn_add">Add</a>
        </div>

    </fieldset>
</form>


<form id="editmeeting-form" style="display: none;">
    <fieldset disabled>
        <legend> Meeting Information</legend>

        <div class="meetingform-item">
            <label>Subject:</label>
            <input type="text" id="oldSubject">
        </div>

        <div class="meetingform-item">
            <label>Project:</label>
            <input type="text" id="oldProject" placeholder="Project name will be added ahead of subject if filled in">
        </div>

        <div class="meetingform-item">
            <label>Creator:</label>
            <input type="text" style="width: 330px" value="Joyza Jane Julao Semilla at 2020/10/18 15:09"
                   id="oldCreator">
        </div>

        <div class="meetingform-item">
            <label>Attendee:</label>
            <v-select id="oldAttendee" :options="users" attach chips label="username" v-model="old_attendee"
                      multiple></v-select>
        </div>

        <div class="meetingform-item">
            <label>Time:</label>
            <input type="date" id="oldDate">
            <input type="time" id="oldStartTime">
            <input type="time" id="oldEndTime">
        </div>

        <div class="meetingform-item">
            <label>Content:</label>
            <textarea style="flex-grow: 1; resize: none;" rows="3" id="oldContent"></textarea>

        </div>

        <div class="meetingform-item" id="upload_input">
            <label>File:</label>
            <input type="file" ref="file_old" id="fileload_old" name="file_old[]" onChange="onChangeFileUploadOld(event)" multiple>
        </div>

        <div class="file-container" id="sc_product_files_old">


        </div>

        <input id="sc_product_files_hide" style="display: none;" value="">



        <div class="meetingform-buttons">
            <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editmeeting-form')"
               id="btn_close">Close</a>
            <a class="btn small" id="btn_delete">Delete</a>
            <a class="btn small green" id="btn_edit">Edit</a>
            <a class="btn small" id="btn_cancel">Cancel</a>
            <a class="btn small green" id="btn_save">Save</a>
        </div>

    </fieldset>
</form>
</div>

</body>


<script>
    var eventObj;

    document.addEventListener('DOMContentLoaded', function () {

        let calendarEl = document.getElementById('calendar');

        let _app1 = app1;
        let event_array = [];
        /* 會議加入array的格式如下： */
        var token = localStorage.getItem('token');

        localStorage.getItem('token');
        var form_Data = new FormData();
        form_Data.append('jwt', token);
        form_Data.append('action', 1);

        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if(obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for(var j = 0; j < obj[i].attach.length; j++)
                        {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if(attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach:attach,
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: obj[i].id,
                            title: title,
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        event_array.push(obj_meeting);
                    }
                }

                //初始化 fullcalendar 物件
                calendar = new FullCalendar.Calendar(calendarEl, {

                    contentHeight: 'auto',

                    titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                        month: '2-digit',
                        year: 'numeric',
                        day: '2-digit'
                    },

                    headerToolbar: {
                        left: 'prev,next addEventButton',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },

                    //Add Meeting被點擊的方法
                    customButtons: {
                        addEventButton: {
                            text: 'Add Meeting',
                            click: function () {
                                $('#addmeeting-form').trigger("reset");
                                $('#editmeeting-form').hide();
                                $('#addmeeting-form').show();

                                _app1.old_attendee = [];
                                _app1.attendee = [];
                                _app1.attachments = [];

                                $('#newProject').val(_app1.project_name);
                                $('#fileload').val('');
                                $('#sc_product_files').empty();
                                $('#newProject').attr("placeholder", _app1.project_name);

                            }
                        }
                    },


                    //日曆上meeting被點擊的方法
                    eventClick: function (info) {
                        $('#editmeeting-form').trigger("reset");
                        $('#addmeeting-form').hide();
                        $('#editmeeting-form > fieldset').prop('disabled', true);
                        $("#oldAttendee").addClass("select_disabled");
                        $('#sc_product_files').empty();
                        _app1.attachments = [];

                        //取得點擊的meeting資訊並載入表單
                        eventObj = info.event;
                        var obj_meeting = eventObj.extendedProps.description;

                        if (obj_meeting === undefined)
                            return;

                        $("#oldSubject").val(obj_meeting.title);
                        $("#oldProject").val(obj_meeting.project_name);
                        $('#oldProject').attr("placeholder", obj_meeting.project_name);
                        $("#oldCreator").val(info.event.extendedProps.description.creator);
                        $("#oldAttendee").val(info.event.extendedProps.description.items);
                        _app1.old_attendee = info.event.extendedProps.description.items;
                        $("#oldDate").val(obj_meeting.start.split("T")[0]);
                        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
                        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
                        $("#oldContent").val(obj_meeting.content);

                        var container = $("#sc_product_files_old");
                        container.empty();

                        if(obj_meeting.attach !== "")
                        {
                            var files = obj_meeting.attach.split(",");
                            files.forEach((element) => {
                                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + element + '" name="file_elements_old" value="' + element + '" checked disabled>' +
                                    '<label for="' + element + '">' + 
                                        '<a href="https://storage.cloud.google.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                                $(elm).appendTo(container);
                            });
                        }

                        //設定出現和隱藏按鈕，和出現視窗
                        $("#btn_close").show();
                        $("#btn_delete").show();
                        $("#btn_edit").show();
                        $("#btn_cancel").hide();
                        $("#btn_save").hide();
                        $("#editmeeting-form").show();

                    },

                    editable: false,
                    events: event_array

                });

                calendar.render();

            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });


    });


    $(document).on("click", "#btn_edit", function () {

        if ($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        //表單變成可以修改
        $('#editmeeting-form > fieldset').prop('disabled', false);
        $("#oldCreator").prop('disabled', true);

        $("#oldAttendee").removeClass("select_disabled");

        //$("oldAttendee").prop('disabled', false);
        var file_elements = document.getElementsByName("file_elements_old");

        var item = 0;
        for(let i = 0;i < file_elements.length; i++)
        {
            file_elements[i].disabled = false;
        
        }

        //按鈕也會改變
        $("#btn_close").hide();
        $("#btn_delete").hide();
        $("#btn_edit").hide();
        $("#btn_cancel").show();
        $("#btn_save").show();

    });


    $(document).on("click", "#btn_cancel", function () {

        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        // $("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //修改到一半的內容也會放棄並載入原先未修改的內容
        var obj_meeting = eventObj.extendedProps.description;
        $("#oldSubject").val(obj_meeting.title);
        $("#oldProject").val(obj_meeting.project_name);
        $("#oldCreator").val(obj_meeting.creator);
        $("#oldAttendee").val(obj_meeting.attendee);
        $("#oldDate").val(obj_meeting.start.split("T")[0]);
        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
        $("#oldContent").val(obj_meeting.content);
        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_save", function () {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#oldDate").val() + " " + $("#oldStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#oldDate").val() + " " + $("#oldEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        if ($("#oldDate").val() === '') {
            app1.warning('Please select Date!');
            return;
        }

        if ($("#oldEndTime").val() === '') {
            app1.warning('Please select End time!');
            return;
        }

        if ($("#oldStartTime").val() === '') {
            app1.warning('Please select Start time!');
            return;
        }

        if ($("#oldSubject").val() === '') {
            app1.warning('Please enter subject!');
            return;
        }

        var names = app1.old_attendee.map(function (item) {
            return item['username'];
        });

        if (names.toString().trim() === '') {
            app1.warning('Please select attendee!');
            return;
        }

        if ($("#oldContent").val().trim() === '') {
            app1.warning('Please enter content!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        //$("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //##修改後的內容 update到資料庫
        var id = eventObj.id;

        var file_elements = document.getElementsByName("file_elements_old");

        var attach = "";
        var remove = "";
        //##利用 id變數到資料庫中update裡面舊的obj_meeting
        // UPDATE table_name  SET meeting_data = obj_meeting WHERE ID = id;

        token = localStorage.getItem('token');
        var form_Data = new FormData();

        form_Data.append('action', 3);

        form_Data.append('id', id);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#oldSubject").val().trim());
        form_Data.append('project_name', $("#oldProject").val().trim());
        form_Data.append('message', $("#oldContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('start_time', $("#oldDate").val() + "T" + $("#oldStartTime").val());
        form_Data.append('end_time', $("#oldDate").val() + "T" + $("#oldEndTime").val());
        form_Data.append('is_enabled', true);

        var item = 0;
        for(let i = 0;i < file_elements.length; i++)
        {
            if(file_elements[i].checked)
            {
                attach += file_elements[i].value + ",";
                for( var j = 0; j < app1.attachments.length; j++ ){
                    let file = app1.attachments[j];
                    if(file.name === file_elements[i].value)
                    {
                        form_Data.append('files[' + item++ + ']', file);
                        break;
                    }
                }
            }
            else
            {
                remove += "'" + file_elements[i].value + "',";
            }
        }

        if(attach !== "")
            attach = attach.slice(0, -1);

        if(remove !== "")
            remove = remove.slice(0, -1);

        form_Data.append('remove', remove);

        var _func = app1;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);

                _func.notify_mail(id, 2);

                var obj_meeting = {
                    title: $("#oldSubject").val().trim(),
                    project_name: $("#oldProject").val().trim(),
                    attendee: names.toString().trim(),
                    items: _func.old_attendee,
                    start: $("#oldDate").val() + "T" + $("#oldStartTime").val(),
                    end: $("#oldDate").val() + "T" + $("#oldEndTime").val(),
                    content: $("#oldContent").val(),
                    attach:attach,
                    //creator: "創建人的系統名字" + " " + "按下save鈕的日期時間(小時:分即可)"
                    creator: "<?php echo $GLOBALS['username'] ?>"
                };
                $("#oldCreator").val(obj_meeting.creator);

                var title = $("#oldSubject").val().trim();
                if($("#oldProject").val().trim() !== "")
                    title = '[ ' + $("#oldProject").val().trim() + ' ] ' + $("#oldSubject").val().trim();

                //把修改後的會議資訊 update 到日曆上
                eventObj.setStart(obj_meeting.start);
                eventObj.setEnd(obj_meeting.end);
                eventObj.setProp("title", title);
                eventObj.setExtendedProp("description", obj_meeting);

                refreshFileList(attach);

            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });


        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_delete", function () {

        var _app1 = app1;
        if ($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        Swal.fire({
            title: "Delete",
            text: "Are you sure to delete?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {

                $("#editmeeting-form").hide();

                //##從資料庫中刪除該會議
                var id = eventObj.id;

                token = localStorage.getItem('token');
                var form_Data = new FormData();
                form_Data.append('jwt', token);
                form_Data.append('action', 7);

                form_Data.append('id', id);

                //DELETE table_name WHERE ID=id;
                $.ajax({
                    url: "api/work_calender_meetings",
                    type: "POST",
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: form_Data,

                    success: function (result) {
                        console.log(result);

                        //從日曆中刪除該會議
                        eventObj.remove();

                        _app1.notify_mail(id, 3);
                    },

                    // show error message to user
                    error: function (xhr, resp, text) {

                    }
                });

            } else {

            }
        });


    });


    $(document).on("click", "#btn_add", function () {
        //結束時間須晚於開始時間
        let start = moment($("#newDate").val() + " " + $("#newStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#newDate").val() + " " + $("#newEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

        //##任一欄位如果為空則提示欄位不得為空
        if ($("#newDate").val() === '') {
            app1.warning('Please select Date!');
            return;
        }

        if ($("#newEndTime").val() === '') {
            app1.warning('Please select End time!');
            return;
        }

        if ($("#newStartTime").val() === '') {
            app1.warning('Please select Start time!');
            return;
        }

        if ($("#newSubject").val() === '') {
            app1.warning('Please enter subject!');
            return;
        }

        var names = app1.attendee.map(function (item) {
            return item['username'];
        });

        if (names.toString().trim() === '') {
            app1.warning('Please select attendee!');
            return;
        }

        if ($("#newContent").val().trim() === '') {
            app1.warning('Please enter content!');
            return;
        }


        var file_elements = document.getElementsByName("file_elements");

        var attach = "";
        for(let i = 0;i < file_elements.length; i++)
        {
            if(file_elements[i].checked)
            {
                attach += file_elements[i].value + ",";
            }
        }

        if(attach !== "")
            attach = attach.slice(0, -1);

        //##obj_meeting 內容寫入資料庫
        //資料庫欄位 (ID, meeting_data)  其中ID為自動計數
        //INSERT table_name (meeting_data) VALUES (obj_meeting)
        //##將該obj_meeting在資料庫給的id返回回來，並設定到前端的id變數
        //##寄送通知信件給會議參與者
        token = localStorage.getItem('token');
        var form_Data = new FormData();
    
        form_Data.append('action', 2);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#newSubject").val().trim());
        form_Data.append('project_name', $("#newProject").val().trim());
        form_Data.append('message', $("#newContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('start_time', $("#newDate").val() + "T" + $("#newStartTime").val());
        form_Data.append('end_time', $("#newDate").val() + "T" + $("#newEndTime").val());
        form_Data.append('is_enabled', true);
        form_Data.append('created_by', "<?php echo $GLOBALS['username'] ?>");

        var file_elements = document.getElementsByName("file_elements");
        var item = 0;
        for(let i = 0;i < file_elements.length; i++)
        {
            if(file_elements[i].checked)
            {
                for( var j = 0; j < app1.attachments.length; j++ ){
                let file = app1.attachments[j];
                if(file.name === file_elements[i].value)
                {
                    form_Data.append('files[' + item++ + ']', file);
                    break;
                }
                }
            }
                
        }

        var _app1 = app1;


        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function(response) {
                var obj = JSON.parse(response);
        
                //##寄送通知信件給會議參與者,告知修改後訊息
                _app1.notify_mail(obj.id, 1);

                var title = $("#newSubject").val().trim();
                if($("#newProject").val().trim() !== "")
                    title = '[ ' + $("#newProject").val().trim() + ' ] ' + $("#newSubject").val().trim();


                //把新增會議 呈現於日曆上
                if(obj.id != 0)
                {
                    var obj_meeting = {
                        id: obj.id,
                        title: $("#newSubject").val().trim(),
                        project_name: $("#newProject").val().trim(),
                        attendee: names.toString().trim(),
                        items: _app1.attendee,
                        start: $("#newDate").val() + "T" + $("#newStartTime").val(),
                        end: $("#newDate").val() + "T" + $("#newEndTime").val(),
                        content: $("#newContent").val(),
                        attach:attach,
                        //creator: "創建人的系統名字" + " " + "按下Add按鈕的日期時間(小時:分即可)"
                        creator: "<?php echo $GLOBALS['username'] ?>"
                    };

                    calendar.addEvent({
                        id: obj.id,
                        title: title,
                        start: obj_meeting.start,
                        end: obj_meeting.end,
                        description: obj_meeting
                    });
                }

            },

            // show error message to user
            error: function(xhr, resp, text) {

            }
        });

        $("#addmeeting-form").hide();

    });


    function hideWindow(target) {
        $(target).hide();
    }

    
    function onChangeFileUpload(target) {
        
        var fileTarget = $("#fileload");
        var container = $("#sc_product_files");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (app1.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                app1.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                app1.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }

    function refreshFileList(attach) {
        $('#sc_product_files_old').empty();

        var container = $("#sc_product_files_old");

        if(attach !== "")
        {
            var files = attach.split(",");
            files.forEach((element) => {
                var elm = '<div class="file-element">' +
                    '<input type="checkbox" id="' + element + '" name="file_elements_old" value="' + element + '" checked disabled>' +
                    '<label for="' + element + '">' + 
                        '<a href="https://storage.cloud.google.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' + 
                    '</label>' +
                    '</div>';

                $(elm).appendTo(container);
            });
        }
    }

    function onChangeFileUploadOld(target) {
        
        var fileTarget = $("#fileload_old");
        var container = $("#sc_product_files_old");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (app1.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                app1.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements_old" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                app1.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }
</script>

<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript" src="js/meeting_calendar.js" defer></script>

</html>
