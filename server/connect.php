<?php
session_start();
error_reporting(E_ALL);
// error_reporting(0);

define("__GOOGLE_CALENDAR__", false);           // true : false
define("__LOGIN_BY__", "");                     // vengg : gdms
define("__VERSION__", "V 2.2.1");               // version
define("__COPYRIGHT__", "2023");          // copyright
define("__COMPANY__", "VENGG");          // company
define("__WEBSITE__", "www.vengg.com");          // website
define("__NAMEPROJECT__", "ระบบจัดการเวร"); // name project
define("__EMAIL__", "pkkjc@oj.go.th");          // email
define("__TEL__", "080-1234567");               // tel  

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
define("__HOSTNAME__", $_SERVER['HTTP_HOST']); // hostname
define("__BASEPATH__", "/vengg/");              // basepath
define("__FULLPATH__", $protocol . __HOSTNAME__ . __BASEPATH__); // fullpath

$config = [
    'GOOGLE_CALENDAR' => false,
    'LOGIN_BY' => '',
    'VERSION' => 'V 2.2.1',
    'COPYRIGHT' => '2023',
    'COMPANY' => 'VENGG',
    'WEBSITE' => 'https://www.vengg.com',
    'NAMEPROJECT' => 'ระบบจัดการเวร',
    'EMAIL' => 'pkkjc@oj.go.th',
    'TEL' => '080-1234567',
    'HOSTNAME' => $_SERVER['HTTP_HOST'],
    'BASEPATH' => '/vengg/',
    'FULLPATH' => $protocol . $_SERVER['HTTP_HOST'] . '/vengg/'
];

date_default_timezone_set("Asia/Bangkok");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "vengg";

/** เชื่อมต่อฐานข้อมูลด้วย PHP PDO */
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    http_response_code(200);    
    echo json_encode(array('status'=>false, 'message' => 'การเชื่อมต่อฐานข้อมูล VENGG ล้มเหลว:'  . $e->getMessage()));
    die();
}

//error handler function
function customError($errno, $errstr) {
    http_response_code(200);
    echo json_encode(array('status'=>false,'message' => "Error: [$errno] $errstr"));
    exit();
}

//set error handler
set_error_handler("customError");