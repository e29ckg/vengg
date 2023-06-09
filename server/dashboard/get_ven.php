<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datas = array();
    $id = $data->id; //id_ven ที่เลือก
    $user_id = $_SESSION['AD_ID'];     //user_id ของผู้ใชระบบ
    // $user_id = $data->uid;     //user_id ของผู้ใชระบบ
    $date_now = Date("Y-m-d");
    
    try{
        $sql = "SELECT v.*,p.name,p.img 
                FROM ven as v 
                INNER JOIN profile as p
                ON v.user_id = p.user_id
                WHERE v.id = $id 
                ORDER BY v.ven_date DESC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        /** ประวัติการเปลี่ยน */
        $sql = "SELECT vc.id, vc.ven_id1, vc.ven_id2 
                FROM ven_change as vc  
                -- LEFT JOIN profile as p1
                -- ON p1.user_id = vc.user_id1
                WHERE ven_date1=:ven_date1 OR ven_date2 =:ven_date2 
                ORDER BY vc.id DESC";
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_date1',$result->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_date2',$result->ven_date, PDO::PARAM_STR);
        $query->execute();
        $res_vh0 = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT v.*
                FROM ven as v 
                WHERE ven_date=:ven_date AND ven_month=:ven_month AND ven_com_idb = :ven_com_idb 
                AND DN=:DN AND u_role=:u_role AND ven_time=:ven_time  AND (v.status = 1 OR v.status = 2 OR v.status = 4)
                ORDER BY v.id DESC";
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_date',$result->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_month',$result->ven_month, PDO::PARAM_STR);
        $query->bindParam(':ven_com_idb',$result->ven_com_idb, PDO::PARAM_STR);
        $query->bindParam(':DN',$result->DN, PDO::PARAM_STR);
        $query->bindParam(':u_role',$result->u_role, PDO::PARAM_STR);
        $query->bindParam(':ven_time',$result->ven_time, PDO::PARAM_STR);
        $query->execute();
        // $res_vh = $query->fetchAll(PDO::FETCH_OBJ);
        $res_vh = array();
        foreach($query->fetchAll(PDO::FETCH_OBJ) as $rs){
            $ven_change_id = '';
            foreach($res_vh0 as $rsvh0){
                if($rsvh0->ven_id1 == $rs->id){
                    $ven_change_id = $rsvh0->id;
                }elseif($rsvh0->ven_id2 == $rs->id){
                    $ven_change_id = $rsvh0->id;
                }
            }
            if( $ven_change_id == ''){
                $ven_change_id = $rs->id;

            }
            array_push($res_vh,array(
                // 'id'=>$rs->id,
                'id'=>$ven_change_id,
                'u_name'=>$rs->u_name
            )); 
        }

        /** เวรที่สามารถเปลี่ยนได้ */
        $sql = "SELECT v.*, p.img
                FROM ven as v  
                INNER JOIN profile as p
                ON v.user_id = p.user_id               
                WHERE v.user_id = :user_id AND ven_month=:ven_month  AND ven_com_idb = :ven_com_idb 
                AND DN=:DN  AND u_role=:u_role AND ven_date >= :ven_date AND v.status =1
                ORDER BY ven_date ASC";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id',$user_id, PDO::PARAM_STR);
        $query->bindParam(':ven_month',$result->ven_month, PDO::PARAM_STR);
        $query->bindParam(':ven_com_idb',$result->ven_com_idb, PDO::PARAM_STR);
        $query->bindParam(':DN',$result->DN, PDO::PARAM_STR);
        $query->bindParam(':u_role',$result->u_role, PDO::PARAM_STR);
        $query->bindParam(':ven_date',$date_now, PDO::PARAM_STR);
        $query->execute();
        $res_vfu = $query->fetchAll(PDO::FETCH_OBJ);
        
        $vfu_arr =array();
        foreach($res_vfu as $rsvfu){

            $img_link = $_SERVER['REQUEST_SCHEME'].'://'. $_SERVER['HTTP_HOST'] ;
            $img_link .= $rsvfu->img != null && $rsvfu->img != '' && file_exists('../../uploads/users/' . $rsvfu->img ) 
                            ? '/vengg/uploads/users/'. $rsvfu->img 
                            : '/vengg/assets/images/profiles/nopic.png';

            array_push($vfu_arr,array(
                "id" => $rsvfu->id,
                "DN" => $rsvfu->DN,
                "img" => $img_link,
                "price" => $rsvfu->price,
                "status" => $rsvfu->status,
                "u_name" => $rsvfu->u_name,
                "u_role" => $rsvfu->u_role,
                "user_id" => $rsvfu->user_id,
                "ven_com_id" => $rsvfu->ven_com_id,
                "ven_com_idb" => $rsvfu->ven_com_idb,
                "ven_com_name" => $rsvfu->ven_com_name,
                "ven_com_num_all" => $rsvfu->ven_com_num_all,
                "ven_date" => $rsvfu->ven_date,
                "ven_date_th" => DateThai_full($rsvfu->ven_date),
                "ven_month" => $rsvfu->ven_month,
                "ven_name" => $rsvfu->ven_name,
                "ven_time" => $rsvfu->ven_time
            ));
        }
          
        // $img = '' ;
        $img = $result->img != null && $result->img != '' && file_exists('../../uploads/users/' . $result->img ) 
                ? '../../uploads/users/'. $result->img 
                : '../../assets/images/profiles/nopic.png';

        $ven_select = [
            "id" => $result->id,
            "DN" => $result->DN,
            "u_name" => $result->u_name,
            "u_role" => $result->u_role,
            "img" => $img,
            "price" => $result->price,
            "user_id" => $result->user_id,
            "ven_com_id" => $result->ven_com_id,
            "ven_com_idb" => $result->ven_com_idb,
            "ven_com_name" => $result->ven_com_name,
            "ven_com_num_all" => $result->ven_com_num_all,
            "ven_date" => $result->ven_date,
            "ven_date_th" => DateThai_full($result->ven_date),
            "ven_month" => $result->ven_month,
            "ven_name" => $result->ven_name,
            "ven_time" => $result->ven_time,
            "status" => $result->status,
        ];

        http_response_code(200);
        echo json_encode(array(
            'status' => true, 
            'message' => 'สำเร็จ',
            'respJSON' => $ven_select ,
            // 'respJSON' => $result ,
            // 'vh0'   => $res_vh0,
            'my_v'  => $vfu_arr,
            'vh'    => $res_vh,
            'd_now' => $date_now
            ));
        exit;
        
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}