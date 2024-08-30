<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas      = array();
    
    try{
        $sql = "SELECT * FROM ven_name ORDER BY srt ASC";
        
        $query = $conn->prepare($sql);
        $query->execute();
        $resps_ven_name = $query->fetchAll(PDO::FETCH_OBJ);
        
        $sql = "SELECT * FROM ven_name_sub ORDER BY srt ASC";        
        $query = $conn->prepare($sql);
        $query->execute();
        $resps_ven_name_sub = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){ 
            $ven_names  = array(); 
            foreach($resps_ven_name as $rs){
                array_push($ven_names, array(
                    'vn_id'     => $rs->id,
                    'vn_name'   => $rs->name,
                    'DN'        => $rs->DN,
                    'vn_srt'    => $rs->srt,
                ));                
            

                $ven_name_subs  = array();
                foreach($resps_ven_name_sub as $rs_vns){
                    if($rs_vns->ven_name_id == $rs->id){
                        array_push($ven_name_subs,array(
                            "vn_name"   => $rs->name,
                            "vns_DN"    => $rs->DN,
                            "vns_name"  => $rs_vns->name,
                            "color"     => $rs_vns->color,
                            "price"     => $rs_vns->price,
                            "vns_id"    => $rs_vns->id,
                            "vn_id"     => $rs->id,
                            "vn_srt"    => $rs->srt,
                            "vns_srt"   => $rs_vns->srt
                        ));
                    }
                }
                $DN = '';
                if($rs->DN ==='กลางวัน'){
                    $DN = '☀️ '.$rs->DN;
                }elseif($rs->DN ==='กลางคืน'){
                    $DN = '🌙 '.$rs->DN;
                }elseif($rs->DN ==='nightCourt'){
                    $DN = '✨ '.$rs->DN;
                }else {
                    $DN = $rs->DN;
                }


                array_push($datas,array(
                    'vn_id'     => $rs->id,
                    'vn_name'   => $rs->name,
                    'DN'        => $DN,
                    'vn_srt'    => $rs->srt,
                    'ven_name_subs'   => $ven_name_subs
                ));
            }
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'respJSON' => $datas
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array(
            'status' => false, 
            'message' => 'ไม่พบข้อมูล'
        ));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}