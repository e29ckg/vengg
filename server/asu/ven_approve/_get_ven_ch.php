<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$datas = array();

    // The request is using the POST method
    try{
        if($data->q != ''){
            $sql = "SELECT p1.fname as p1_fname, p1.name as p1_name, p1.sname as p1_sname, p2.fname as p2_fname, p2.name as p2_name, p2.sname as p2_sname, vc.*
                    FROM ven_change AS vc
                    INNER JOIN `profile` AS p1 ON vc.user_id1 = p1.user_id 
                    INNER JOIN `profile` AS p2 ON vc.user_id2 = p2.user_id 

                    WHERE vc.id LIKE '%$data->q%' AND (vc.status=1 OR vc.status=2) 
                    ORDER BY vc.ven_month DESC, vc.id DESC" ;

        }else{
            $sql = "SELECT p1.fname as p1_fname, p1.name as p1_name, p1.sname as p1_sname, p2.fname as p2_fname, p2.name as p2_name, p2.sname as p2_sname, vc.*
                    FROM ven_change AS vc
                    INNER JOIN `profile` AS p1 ON vc.user_id1 = p1.user_id 
                    INNER JOIN `profile` AS p2 ON vc.user_id2 = p2.user_id 

                    WHERE (vc.status=1 OR vc.status=2) 
                    ORDER BY vc.ven_month DESC, vc.id DESC" ;

        }
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $res_g = array();
        $ven_month = '';
        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                if($rs->ven_month != $ven_month){
                    array_push($res_g,array(
                        "ven_month" => $rs->ven_month,
                        "ven_month_th" => DateThai_MY($rs->ven_month)
                    ));
                    $ven_month = $rs->ven_month;
                }
                            
                array_push($datas,array(
                    'id'            => $rs->id,
                    'ven_month'     => $rs->ven_month,
                    'ven_month_th'  => DateThai_MY($rs->ven_month),
                    'ven_date1'     => $rs->ven_date1,
                    'ven_date2'     => $rs->ven_date2,
                    'ven_date1_th'  => DateThai_full($rs->ven_date1),
                    'ven_date2_th'  => DateThai_full($rs->ven_date2),
                    'user_id1'      => $rs->user_id1,
                    'user_id2'      => $rs->user_id2,
                    'name1'         => $rs->p1_fname.$rs->p1_name.' '.$rs->p1_sname,
                    'name2'         => $rs->p2_fname.$rs->p2_name.' '.$rs->p2_sname,
                    'DN'            => $rs->DN,
                    'create_at'     =>  date("Y-m-d",strtotime($rs->create_at)),
                    'create_at_th'  =>  DateThai_full($rs->create_at),
                    'status'        => $rs->status
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas ,'respJSON_G' => $res_g ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล ','respJSON' => $datas ,'respJSON_G' => $res_g ));
    
    }catch(PDOException $e){
        // echo "Faild to connect to database" . $e->getMessage();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
    }
}