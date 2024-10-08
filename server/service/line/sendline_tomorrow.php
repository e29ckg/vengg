<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$date_now = date("Y-m-d");
	$date_tomorrow = date("Y-m-d", strtotime("+1 day"));
	$sToken = "";
	$sMessage = "";

	$sql = "SELECT * FROM line WHERE name = 'ven' AND status=1";
	$query = $conn->prepare($sql);
	$query->execute();
	$res = $query->fetch(PDO::FETCH_OBJ);

	if ($query->rowCount()) {
		$sToken = $res->token;
		$sMessage .= 'ตารางเวรวันพรุ้งนี้ ' . DateThai($date_tomorrow) . "\n";
		$sql = "SELECT v.*, vn.name AS ven_com_name, vn.DN AS DN, p.fname, p.name, p.sname
				FROM ven as v
				INNER JOIN `profile` AS p ON v.user_id = p.id
				INNER JOIN `ven_name` AS vn ON v.vn_id = vn.id
				WHERE v.ven_date = '$date_tomorrow' AND (v.status=1 OR v.status=2)
				ORDER BY v.ven_time ASC";
		$query = $conn->prepare($sql);
		$query->execute();
		$result = $query->fetchAll(PDO::FETCH_OBJ);

		$ven_name = "";
		foreach ($result as $rs) {
			if ($ven_name !== $rs->ven_com_name) {
				$sMessage .= "#" . $rs->ven_com_name . "\n";
				$ven_name = $rs->ven_com_name;
			}
			$d = '';
			if ($rs->DN == 'กลางวัน') {
				$d = '☀️';
			} elseif ($rs->DN == 'กลางคืน') {
				$d = '🌙';
			} elseif ($rs->DN == 'nightCourt') {
				$d = '✨';
			} else {
				// Default value if none of the conditions are true
				$d = '';
			}
			$sMessage .= $d . ' ' . $rs->fname . $rs->name . ' ' . $rs->sname;
			$sMessage .= "\n";
		}

		http_response_code(200);
		echo sendLine($sToken, $sMessage);
		echo $sMessage;
		exit;

	} else {
		$sql = "SELECT * FROM line WHERE name = 'admin'";
		$query = $conn->prepare($sql);
		$query->execute();
		$res = $query->fetch(PDO::FETCH_OBJ);
		$sToken = $res->token;

		$sMessage = 'ไม่สามารถแจ้งผ่านกลุ่ม ven ได้';
		http_response_code(200);
		echo sendLine($sToken, $sMessage);
		exit;
	}

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	/**
	 * 	post
	 * 
	 * 	token or username
	 * 	message
	 * 
	 */

	$date_now = date("Y-m-d H:i:s");
	$sMessage = '';

	if (isset($data->token)) {
		$sToken = $data->token;
	} else {
		if (isset($data->username)) {
			$sql = "SELECT * FROM line WHERE name = '$data->username'";
			$query = $conn->prepare($sql);
			$query->execute();
			$res = $query->fetch(PDO::FETCH_OBJ);
			$sToken = $res->token;
		} else {
			http_response_code(200);
			echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล Token'));
			exit;
		}
	}

	$sMessage .= $data->message;
	$sMessage .= "\n";
	$sMessage .= $date_now;


	http_response_code(200);
	echo sendLine($sToken, $sMessage);

}
?>