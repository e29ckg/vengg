<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

//---------------------------
// [    date
//          1
//          2
//          3
//          4->name->name
//  ]

//---------------------------

// The request is using the POST method
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // $vcid = 1702555878;
        
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $vcid = $data->vcid;

    $datas = array();

    try{

        $sql = "SELECT * FROM ven_com WHERE id = $vcid";
        $query = $conn->prepare($sql);
        $query->execute();
        $vc = $query->fetch(PDO::FETCH_OBJ);

        

// เวรอะไร
        $sql = "SELECT * 
                FROM ven_name AS vn
                WHERE vn.id = '$vc->vn_id'";
        $query = $conn->prepare($sql);
        $query->execute();
        $res_vn = $query->fetch(PDO::FETCH_OBJ);


        $ven_com = [
                "id" => $vc->id,
                "ven_name" => $res_vn->name,
                "ven_com_num" => $vc->ven_com_num,
                "ven_com_date" => $vc->ven_com_date,
                "ven_com_date_th" => DateThai_full($vc->ven_com_date),
                "ven_month" => $vc->ven_month,
                "ven_month_th" => DateThai_MY($vc->ven_month),
                "vn_id" => $vc->vn_id,
                "status" => $vc->status,
            ];

// หน้าที่ อะไรบ้าง
        $sql = "SELECT * 
                FROM ven_name_sub AS vns
                WHERE vns.ven_name_id = '$vc->vn_id'
                ORDER BY vns.srt";
        $query = $conn->prepare($sql);
        $query->execute();
        $res_vns = $query->fetchAll(PDO::FETCH_OBJ);

        $head_table = array();
        $head_t = array();
        foreach($res_vns as $vns){
                array_push($head_t,$vns->name);
        }

        array_push($head_table,array(
                'ven_date'  => "วัน/เดือน/ปี",
                'details'  => $head_t,
            ));


// วันที่มีเวรทั้งคำสั่ง
        $sql = "SELECT v.ven_date 
                FROM ven AS v
                WHERE v.ven_com_idb = '$vcid' AND (v.status=1 OR v.status=2) 
                GROUP BY v.ven_date
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $res_ven_date = $query->fetchAll(PDO::FETCH_OBJ);

//ดึงเวร ทั้งคำสั่ง
        $sql = "SELECT 
                        v.id, 
                        v.ven_date, 
                        v.ven_time, 
                        v.vn_id, 
                        v.vns_id, 
                        vns.name as u_role, 
                        p.fname, 
                        p.name, 
                        p.sname, 
                        v.user_id, 
                        p.dep 
                FROM ven AS v
                INNER JOIN `ven_name_sub` AS vns ON v.vns_id = vns.id
                INNER JOIN `profile` AS p ON p.id = v.user_id
                WHERE v.ven_com_idb = '$vcid' AND (v.status=1 OR v.status=2 OR v.status=4) 
                ORDER BY v.ven_date ASC, v.ven_time ASC, v.create_at ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $x  = 0;
        $n  = $query->rowCount();

        $u_row_num = array();

        if($query->rowCount() > 0){  

                foreach($res_ven_date as $rvd){
                        $ven_date = $rvd->ven_date;
                        $ven_to_day = searchVenDate($result, $ven_date);
                        $vns_name_arr = getVenName($res_vns); //หน้าที่

                        $details = array();

                        foreach($vns_name_arr as $vn){
                                
                               $u_name = getUserTodayByVN($ven_to_day,$vn);

                               array_push( $details, [
                                       "u_role"=>$vn,
                                       "tm"=>'',
                                       "name"=>$u_name
                               ]);
                        }

                        
                        array_push($datas, array(
                                "ven_date" => $ven_date,
                                "v_name" => $vn,
                                "details" => $details,
                        ));
                        // $details = searchInMatrix($result, $ven_date);

                        
                        // array_push($details,$rs->u_role.$rs->ven_time.$rs->create_at);
                       
                        // array_push($datas,$details);
                }

                
            
            
           
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => ' สำเร็จ ', 
                'respJSON' => $datas , 
                'head_table' => $head_table , 
                'vc'=>$ven_com));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล '));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}


function getVenName($vns) {
        $datas = array();
        foreach ($vns as $vn) {            
                    array_push($datas,$vn->name);
        }    
        return $datas;
    }

function getUserTodayByVN($ven_to_day,$vn) {
        $datas = array();
        
        $time_arr = array();
        $time ='';
        foreach ($ven_to_day as $vtd) { 
                if($time != $vtd->ven_time && $vn == $vtd->u_role){
                        array_push($time_arr,$vtd->ven_time);
                        $time = $vtd->ven_time;
                }
        }
        
        $u_name = array();
        $time = $ven_to_day[0]->ven_date;
        foreach ($time_arr as $t) { 
                $name = array();
                foreach ($ven_to_day as $vtd){
                        if($t == $vtd->ven_time){
                               array_push($name,' # '.$vtd->fname.$vtd->name.' '.$vtd->sname);
                        }
                }
                array_push($u_name,$name);
                
        }    
        return $u_name;
    }

function searchVenDate($vens, $ven_date) {
        $datas = array();
        foreach ($vens as $vn) {            
                if ($vn->ven_date == $ven_date) {
                    array_push($datas,$vn);
                }           
        }    
        return $datas;
    }

function searchUserToDayWithTime($ven_to_day, $ven_time) {
        $datas = array();
        foreach ($ven_to_day as $vtd) {            
                if ($vtd->time == $ven_time) {
                    array_push($datas,$vtd);
                }           
        }    
        return $datas;
    }