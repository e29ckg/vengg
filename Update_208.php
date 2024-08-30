<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
// header("Content-Type: application/json; charset=utf-8");

include "./server/connect.php";
include "./server/function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	
try {
	$sms = [];

    // ตรวจสอบว่ามีคอลัมน์ name_full อยู่ในตาราง ven_name หรือไม่
    $sql_check = "SHOW COLUMNS FROM `ven_name` LIKE 'name_full';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
        $sql = "ALTER TABLE `ven_name`
            ADD COLUMN `name_full` TEXT;";
        $query = $conn->prepare($sql);  
        $query->execute();
		array_push($sms,'ALTER Table ven_name '. $query->fetch(PDO::FETCH_ASSOC));
	}
	
    $sql = "UPDATE `ven_name`
			SET `name_full` = `name`;";
    $query = $conn->prepare($sql);  
    $query->execute();
	array_push($sms,"UPDATE VEN_NAME");
	
	
	$sql_check = "SHOW COLUMNS FROM `ven_com` LIKE 'vn_id';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven_com`
		ADD COLUMN `vn_id` INT(11) NOT NULL;";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, $query->fetch(PDO::FETCH_ASSOC));
	}

	$sql_check = "SHOW COLUMNS FROM `ven_com` LIKE 'ven_name';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount()) {
		$sql = "UPDATE `ven_com`
		JOIN `ven_name` ON `ven_com`.`ven_name` COLLATE utf8_unicode_ci = `ven_name`.`name` COLLATE utf8_unicode_ci
		SET `ven_com`.`vn_id` = `ven_name`.`id`;";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "UPDATE VEN_COM");
		
	}else{
		array_push($sms, "***NOT UPDATE VEN_COM vn_id***");
	}
	
	
	$sql_check = "SHOW COLUMNS FROM `ven_change` LIKE 'ven_com_idb';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven_change` ADD COLUMN `ven_com_idb` INT(11);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "ALTER TABLE VEN_CHANGE ven_com_idb");
	}
	
	$sql_check = "SHOW COLUMNS FROM `ven_change` LIKE 'vn_id';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven_change` ADD COLUMN `vn_id` INT(11);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "ALTER TABLE VEN_CHANGE vn_id");
	}
	
	$sql_check = "SHOW COLUMNS FROM `ven_change` LIKE 'vns_id';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven_change` ADD COLUMN `vns_id` INT(11);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "ALTER TABLE VEN_CHANGE vnS_id");
	}
	
    $sql = "UPDATE ven_change
	JOIN `ven_com` ON CONVERT(`ven_com`.`ven_com_num` USING utf8) = CONVERT(`ven_change`.`ven_com_num_all` USING utf8)
	SET `ven_change`.`ven_com_idb` = `ven_com`.id;";
    $query = $conn->prepare($sql);  
    $query->execute();
	array_push($sms, "UPDATE VEN_CHANGE ven_com_idb");
	
    $sql = "UPDATE ven_change
			JOIN ven_com ON ven_com.id = ven_change.ven_com_idb
			SET ven_change.vn_id = ven_com.vn_id;";
    $query = $conn->prepare($sql);  
    $query->execute();
	array_push($sms, "UPDATE VEN_CHANGE VN_ID");
	
    $sql = "UPDATE `ven_change`
			JOIN `ven_name_sub` AS `sub1` ON `sub1`.`name` COLLATE utf8_unicode_ci = `ven_change`.`u_role` COLLATE utf8_unicode_ci
			JOIN `ven_name_sub` AS `sub2` ON `sub2`.`ven_name_id` = `ven_change`.`vn_id`
			SET `ven_change`.`vns_id` = `sub1`.`id`;";
    $query = $conn->prepare($sql);  
    $query->execute();
	array_push($sms, "UPDATE VEN_CHANGE VNS_ID");
	
	$sql_check = "SHOW COLUMNS FROM `ven` LIKE 'vn_id';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven` ADD COLUMN `vn_id` INT(11);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "ALTER TABLE VEN VN_ID");
	}
	$sql_check = "SHOW COLUMNS FROM `ven` LIKE 'vns_id';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven` ADD COLUMN `vns_id` INT(11);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "ALTER TABLE VEN VNS_ID");
	}
	
    $sql = "UPDATE ven
			JOIN ven_com ON ven_com.id = ven.ven_com_idb
			SET ven.vn_id = ven_com.vn_id;";
    $query = $conn->prepare($sql);  
    $query->execute();
	array_push($sms, "UPDATE VEN VN_ID");
	
	$sql_check = "SHOW TABLES LIKE 'sign_name';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() == 0) {
		$sql = "CREATE TABLE `sign_name` (
			`id` INT PRIMARY KEY,
			`name` VARCHAR(255),
			`dep` VARCHAR(255),
			`dep2` VARCHAR(255),
			`dep3` VARCHAR(255),
			`role` VARCHAR(255),
			`st` INT
		);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "CREATE TABLE SIGN_NAME");
	}
	
	$sql_check = "SHOW TABLES LIKE 'sign_boss_name';";
    $query_check = $conn->prepare($sql_check);
    $query_check->execute();
    
    if ($query_check->rowCount() > 0) {		
		$sql = "DROP TABLE `sign_boss_name`;";
		$query = $conn->prepare($sql);  
		$query->execute();	
		array_push($sms, "DROP TABLE SIGN_BOSS_NAME");
	}
	
	
	//  update 
	$sql_check = "SHOW COLUMNS FROM `ven_user` LIKE 'vn_id';";
	$query_check = $conn->prepare($sql_check);
	$query_check->execute();
	
	if ($query_check->rowCount() == 0) {
		$sql = "ALTER TABLE `ven_user` ADD COLUMN `vn_id` INT(11);";
		$query = $conn->prepare($sql);  
		$query->execute();
		array_push($sms, "ALTER TABLE VEN_USER VU_ID");
	}
			
	$sql_check = "SHOW COLUMNS FROM `ven_user` LIKE 'vns_id';";
	$query_check = $conn->prepare($sql_check);
	$query_check->execute();
	
	if ($query_check->rowCount() == 0) {
			$sql = "ALTER TABLE `ven_user` ADD COLUMN `vns_id` INT(11);";
			$query = $conn->prepare($sql);  
			$query->execute();
			array_push($sms, "ALTER TABLE VEN_USER VNS_ID");
		}
		
	$sql = "UPDATE `ven_user`
			JOIN `ven_name` ON `ven_user`.`ven_name` COLLATE utf8_unicode_ci = `ven_name`.`name` COLLATE utf8_unicode_ci
			SET `ven_user`.`vn_id` = `ven_name`.`id`;";
	$query = $conn->prepare($sql);
	$query->execute();
	array_push($sms, "UPDATE VEN_USER VN_ID");
	
	
	$sql_check = "SHOW COLUMNS FROM `ven_user` LIKE 'id';";
	$query_check = $conn->prepare($sql_check);
	$query_check->execute();
	
	if ($query_check->rowCount() == 1) {
		// $sql = "DESCRIBE ven_user;";
		// $query = $conn->prepare($sql);
		// $query->execute();
		// array_push($sms, "DESCRIBE VEN_USER");
		
		$sql = "ALTER TABLE `ven_user` CHANGE `id` `vu_id` INT(11) NOT NULL AUTO_INCREMENT;";
		$query = $conn->prepare($sql);
		$query->execute();
		array_push($sms, "ALTER TABLE VEN_USER ID");
	}

	
	$sql = "SELECT 
				vn.id AS vn_id,
				vns.id AS vns_id,	
				vn.name AS vn_name,
				vns.name AS u_role,
				vn.DN,
				vns.price,
				vns.color
			FROM ven_name AS vn
			INNER JOIN ven_name_sub AS vns ON vns.ven_name_id = vn.id
			ORDER BY vn.srt, vns.srt";
	$query = $conn->prepare($sql);  
	$query->execute();
	$result = $query->fetchAll(PDO::FETCH_OBJ);
	if ($result) {
			foreach ($result as $row) {	
					$vn_id =  $row->vn_id;
					$vns_id =  $row->vns_id;
					$vn_name =  $row->vn_name;
					$u_role =  $row->u_role;
			
						
				$sql2 = "UPDATE ven_user 
						SET vns_id = $vns_id
						WHERE uvn = '$u_role' AND ven_name = '$vn_name'";
				$query = $conn->prepare($sql2);  
				$query->execute();
				array_push($sms, "UPDATE VEN_USER UVN = $u_role VEN_NAME = $vn_name");

				$sql3 = "UPDATE ven
						SET vns_id = $vns_id
						WHERE u_role = '$u_role' AND ven_name = '$vn_name'";
				$query = $conn->prepare($sql3);  
				$query->execute();
			array_push($sms, "UPDATE VEN UVN = $u_role VEN_NAME = $vn_name");
		}		
	}
    http_response_code(200);
    echo json_encode(array('status' => true, 'message' => 'successfully', 'data' => $sms));
	exit;
} catch (PDOException $e) {
	http_response_code(500);
    echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
	exit;
}

	
	
	
	// if($query_g->rowCount()){
	// 	$result = $query_g->fetchAll(PDO::FETCH_OBJ);		
		
	// 	foreach($result as $rs){
	// 		$gcal_id = $rs->gcal_id;
	// 		gcal_remove($gcal_id);	
	// 	}
	// }

		// http_response_code(200);
		// echo json_encode(array('status' => true, 'massege' => 'สำเร็จ', 'respJSON' => $result));
		// exit;
	// }else{
	// 	http_response_code(200);
	// 	echo json_encode(array('status' => false, 'massege' => 'null',));
	// 	exit;
	// }

}    

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// 	/**
// 	 * 	post
// 	 * 
// 	 * 	token or username
// 	 * 	message
// 	 * 
// 	 */
	
// 	$date_now = date("Y-m-d H:i:s");
// 	$sMessage = '';

// 	if(isset($data->token)){
// 		$sToken 	= $data->token;
// 	}else{
// 		if(isset($data->username)){
// 			$sql = "SELECT * FROM line WHERE name = '$data->username'";
// 			$query = $conn->prepare($sql);
// 			$query->execute();
// 			$res = $query->fetch(PDO::FETCH_OBJ);
// 			$sToken = $res->token;
// 		}else{
// 			http_response_code(200);
// 			echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล Token'));
// 			exit;
// 		}
// 	}

// 	$sMessage .= $data->message;
// 	$sMessage .= "\n";
// 	$sMessage .= $date_now;

	
// 	http_response_code(200);
// 	echo sendLine($sToken,$sMessage);
	    
// }    

// $a = "2023-03-01 08:30:61";

// echo date("Y-m-d H:i:s",strtotime($a)). "\n";

// echo number_format("1000000",2);
