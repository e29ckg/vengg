<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";




// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $vcid = $data->vcid;

    $datas = array();

    try{

        $sql = "SELECT * FROM ven_com WHERE id = $vcid";
        $query = $conn->prepare($sql);
        $query->execute();
        $vc = $query->fetch(PDO::FETCH_OBJ);

        

        $ven_name = [];
        $sql = "select * ,vn.name  v_name, vns.id vnd_id
                from vengg.ven_name vn 
                INNER JOIN vengg.ven_name_sub vns ON vn.id = vns.ven_name_id 
                where vn.id = $vc->vn_id
                order by vns.srt ";
        $query = $conn->prepare($sql);
        $query->execute();
        $ven_name = $query->fetchAll(PDO::FETCH_OBJ);


        $ven_name_arr = array();
        $col = count($ven_name);

        $ven_com = [
            "id" => $vc->id,
            "ven_name" => $ven_name[0]->v_name,
            "ven_com_num" => $vc->ven_com_num,
            "ven_com_date" => $vc->ven_com_date,
            "ven_com_date_th" => DateThai_full($vc->ven_com_date),
            "ven_month" => $vc->ven_month,
            "ven_month_th" => DateThai_MY($vc->ven_month),
            "vn_id" => $vc->vn_id,
            "status" => $vc->status,
        ];

        foreach($ven_name as $vn){
            array_push($ven_name_arr,array(
                "vns_id" => $vn->vnd_id,
                "name" => $vn->name
            ));
        }

        //ROW หัวตาราง
        $datas_header = array();
        array_push($datas_header,array(
            "ven_date" => "วัน/เดือน/ปี",
            "col_data" => $ven_name_arr,
            "col_num" => $col
        ));

        $sql = "SELECT 
                        v.id,
                        v.ven_date,
                        v.vn_id,
                        v.vns_id,
                        p.fname, p.name, p.sname
                FROM ven as v
                INNER JOIN `profile` AS p ON p.id = v.user_id
                WHERE v.ven_com_idb = '$vcid' 
                    AND (v.status=1 OR v.status=2) 
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $x  = 0;
        $n  = $query->rowCount();

        $vds = array();

        if($query->rowCount() > 0){                        //count($result)  for odbc
            $vd_o = '';
            foreach($result as $rs){
                if($vd_o != $rs->ven_date){
                    array_push($vds,$rs->ven_date);
                    $vd_o = $rs->ven_date;
                }                    
            }

            
            
            foreach($vds as $vd){                         /**    เวียนวัน  $r วันที่ 2022-11-01  */
                $col_data_arr = array();
                
                
                foreach($ven_name_arr as $c){
                    $names = array();

                    foreach($result as $rs){    
                             if($c["vns_id"] == $rs->vns_id && $vd == $rs->ven_date){
                                 array_push($names,array(
                                    "name" => $rs->fname.$rs->name.' '.$rs->sname
                                ));
                             }                    
             
                    }
                    array_push($col_data_arr,array(
                        "vns_id" => $c["vns_id"],
                        "names"=>$names
                    ));
                }


                // ROW
                array_push($datas,array(
                    'ven_date' => DateThai_full($vd),
                    'col_data' => $col_data_arr,
                ));


            }
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => ' สำเร็จ ', 
                'respJSON' => $datas , 
                'vc'=>$ven_com,
                'datas_header'=>$datas_header,
                // '$col'=>$col,
            ));
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