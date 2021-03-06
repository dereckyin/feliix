<?php
error_reporting(E_ALL);

include_once 'api/config/database.php';
include_once 'api/config/conf.php';

require 'vendor/autoload.php';

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

use PhpOffice\PhpWord\IOFactory;

try {
    $jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );
}
    // if decode fails, it means jwt is invalid
catch (Exception $e){

    die();
}

$database = new Database();
$db = $database->getConnection();

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);

$sql = "select DAYNAME(start_time) weekday, DATE_FORMAT(start_time,'%d %M %Y') start_time, title, sales_executive, 
        project_in_charge, project_relevant, installer_needed, installer_needed_other, things_to_bring, installer_needed_location, things_to_bring_location, 
        products_to_bring, service, driver, driver_other,
		back_up_driver, back_up_driver_other, photoshoot_request, notes, location, agenda, DATE_FORMAT(appoint_time, '%I:%i %p') appoint_time, 
		DATE_FORMAT(detail.end_time, '%I:%i %p') end_time, products_to_bring_files
		from work_calendar_main main 
		left join work_calendar_details detail on detail.main_id = main.id where coalesce(detail.is_enabled, 1) = 1 and main.id = " . $id . " order by sort " ;

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $weekday = '';
    $start_time = '';
    $title = '';
    $sales_executive = '';
    $project_in_charge = '';
    $project_relevant = '';
    $installer_needed = '';
    $installer_needed_other = '';
    $things_to_bring = '';
    $installer_needed_location = '';
    $things_to_bring_location = '';
    $products_to_bring = '';
    $service = '';
    $driver = '';
    $driver_other = '';
    $back_up_driver = '';
    $back_up_driver_other = '';
    $photoshoot_request = '';
    $notes = '';

    $products_to_bring_files = '';

    $location = '';
    $agenda = '';
    $appoint_time = '';
    $end_time = '';

    $onrecord = 0;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
{
    $onrecord = $onrecord + 1;

    $weekday = $row['weekday'];
    $start_time = $row['start_time'];
    $title = $row['title'];
    $sales_executive = $row['sales_executive'];
    $project_in_charge = $row['project_in_charge'];
    $project_relevant = $row['project_relevant'];
    $installer_needed = $row['installer_needed'];
    $installer_needed_other = $row['installer_needed_other'];
    $things_to_bring = $row['things_to_bring'];
    $installer_needed_location = $row['installer_needed_location'];
    $things_to_bring_location = $row['things_to_bring_location'];
    $products_to_bring = $row['products_to_bring'];
    $service = $row['service'];
    $driver = $row['driver'];
    $driver_other = $row['driver_other'];
    $back_up_driver = $row['back_up_driver'];
    $back_up_driver_other = $row['back_up_driver_other'];
    $photoshoot_request = $row['photoshoot_request'];
    $notes = $row['notes'];

    $products_to_bring_files = $row['products_to_bring_files'];


    $location = $row['location'];
    $agenda = $row['agenda'];
    $appoint_time = $row['appoint_time'];
    $end_time = $row['end_time'];

    break;
}

if($onrecord == 0)
{
    $sql = "select DAYNAME(start_time) weekday, DATE_FORMAT(start_time,'%d %M %Y') start_time, title, sales_executive, 
        project_in_charge, project_relevant, installer_needed, installer_needed_other,things_to_bring, installer_needed_location, things_to_bring_location, 
        products_to_bring, service, driver, driver_other,
		back_up_driver, back_up_driver_other, photoshoot_request, notes, '' location, '' agenda, '' appoint_time, 
		'' end_time, products_to_bring_files
		from work_calendar_main main 
		where main.id = " . $id . " " ;

        $stmt = $db->prepare( $sql );
        $stmt->execute();


    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        $weekday = $row['weekday'];
        $start_time = $row['start_time'];
        $title = $row['title'];
        $sales_executive = $row['sales_executive'];
        $project_in_charge = $row['project_in_charge'];
        $project_relevant = $row['project_relevant'];
        $installer_needed = $row['installer_needed'];
        $installer_needed_other = $row['installer_needed_other'];
        $things_to_bring = $row['things_to_bring'];
        $installer_needed_location = $row['installer_needed_location'];
        $things_to_bring_location = $row['things_to_bring_location'];
        $products_to_bring = $row['products_to_bring'];
        $service = $row['service'];
        $driver = $row['driver'];
        $driver_other = $row['driver_other'];
        $back_up_driver = $row['back_up_driver'];
        $back_up_driver_other = $row['back_up_driver_other'];
        $photoshoot_request = $row['photoshoot_request'];
        $notes = $row['notes'];

        $products_to_bring_files = $row['products_to_bring_files'];


        $location = $row['location'];
        $agenda = $row['agenda'];
        $appoint_time = $row['appoint_time'];
        $end_time = $row['end_time'];

        break;
    }
}

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText($weekday . ", " . $start_time . " Schedule");

$section->addText("");

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($start_time);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Project:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($title);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Sales Executive:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($sales_executive);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("project_in_charge:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($project_in_charge);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("project_relevant:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($project_relevant);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Installer needed:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($installer_needed . " " . $installer_needed_other);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Things to Bring:", array('bold' => true));
if($things_to_bring_location != "")
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, "From " . $things_to_bring_location . "\n" . $things_to_bring);
}
else
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, $things_to_bring);
}

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Products to Bring:", array('bold' => true));
if($installer_needed_location != "")
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, "From " . $installer_needed_location . "\n" . $products_to_bring);
}
else
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, $products_to_bring);
}

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Service:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getService($service));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getDriver($driver) . ' ' . $driver_other);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Back-up Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getDriver($back_up_driver) . ' ' . $back_up_driver_other);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Photoshoot Request:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getRequest($photoshoot_request));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Note/s:", array('bold' => true));
$cell = $table->addCell(8500, ['borderSize' => 6]);
addMultiLineText($cell, $notes);

$section->addText("");

$table1 = $section->addTable('table1', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0,
    'textAlign' => 'center'
]);

$table1->addRow();
$table1->addCell(2600, ['borderSize' => 6])->addText("Location", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Agenda",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Appoint Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("End Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($location,  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
{
    $location = $row['location'];
    $agenda = $row['agenda'];
    $appoint_time = $row['appoint_time'];
    $end_time = $row['end_time'];

    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($location, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

}

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
//$myTextElement->setFontStyle($fontStyle);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);

if(trim($products_to_bring_files) != "")
{
    $attachment = explode(",", $products_to_bring_files);

    $conf = new Conf();

    $path = $conf::$upload_path . "tmp/";

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    else
    {
        $files = glob($path . "*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    foreach ($attachment as &$value) {
        grab_image($value, $path . $value);
    }
    // $arr is now array(2, 4, 6, 8)
    unset($value); // break the reference with the last element

    $objWriter->save($path . "schedule.docx");

    $time = microtime(true);
    $zipname = $path . $time . 'schedule.zip';
    $zip = new ZipArchive();
    
    // touch($zipname); 
    //$zip->open($zipname, ZipArchive::CREATE);
    if ($zip->open($zipname, (ZipArchive::CREATE)) !== true)
        die("Failed to create archive\n");

    if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $r = $zip->addFile($path . $entry, basename($entry));
        }
    }
    closedir($handle);
    }

    $zip->close();

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='schedule.zip'");
    header('Content-Length: ' . filesize($zipname));
    header("Content-Transfer-Encoding: Binary");
    while (ob_get_level()) {
        ob_end_clean();
      }
    readfile($zipname);
    exit;
}
else
{
    header("Content-Disposition: attachment; filename=schedule.docx");

    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter->save('php://output');
}

    function getService($type){
        $leave_type = '';
    
        if($type =="1")
            $leave_type = "innova";
        if($type =="2")
            $leave_type = "avanza gold";
        if($type =="3")
            $leave_type = "avanza gray";
        if($type =="4")
            $leave_type = "L3001";
        if($type =="5")
            $leave_type = "L3002";
        if($type =="6")
            $leave_type = "Grab";
        
        return $leave_type;
    }

    function getDriver($type){
        $leave_type = '';
    
        if($type =="1")
            $leave_type = "MG";
        if($type =="2")
            $leave_type = "AY";
        if($type =="3")
            $leave_type = "EV";
        if($type =="4")
            $leave_type = "JB";
        if($type =="5")
            $leave_type = "MA";
        if($type =="6")
            $leave_type = "Other";
    
        return $leave_type;
    }

    function getRequest($type){
        $leave_type = '';
    
        if($type =="0")
            $leave_type = "No";
        if($type =="1")
            $leave_type = "Yes";
        
        return $leave_type;
    }

    function addMultiLineText($cell, $text)
    {
        // break from line breaks
        $strArr = explode("\n", $text);

        // add text line together
        foreach ($strArr as $v) {
            $cell->addText($v);
        }
       
    }

    function grab_image($image_url,$image_file){

        /*
        $storage = new StorageClient([
            'projectId' => 'predictive-fx-284008',
            'keyFilePath' => $key
        ]);

        $bucket = $storage->bucket('feliiximg');

        $object = $bucket->object($image_url);
        $object->downloadToFile($image_file);
        */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://storage.googleapis.com/calendarfile/' . $image_url);
        //Create a new file where you want to save
        $fp = fopen($image_file, 'w');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec ($ch);
        curl_close ($ch);
        fclose($fp);
    }

?>
