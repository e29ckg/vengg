<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include '../../vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $data->id;
    
    $datas = array();

    try{
        $sql = "SELECT 
                        vc.id,
                        ven_com.ven_month AS ven_month, 
                        vc.ven_com_id, 
                        vc.ven_com_idb, 
                        vc.ven_date1, 
                        vc.ven_date2,  
                        vc.user_id1, 
                        vc.ven_id1, 
                        vc.ven_id2, 
                        vc.user_id2, 
                        vc.ven_id1_old, 
                        vc.ven_id2_old, 
                        vc.comment, 
                        vc.status, 
                        vc.create_at,
                        ven_com.ven_com_num AS ven_com_num_all, 
                        vn.DN AS DN, 
                        vns.name AS u_role
                FROM ven_change as vc
                INNER JOIN `ven_com` ON vc.ven_com_idb = ven_com.id 
                INNER JOIN `ven_name` AS vn ON vc.vn_id = vn.id 
                INNER JOIN `ven_name_sub` AS vns ON vc.vns_id = vns.id 
                WHERE vc.id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$id, PDO::PARAM_STR);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_OBJ);
       
        
        
        if($query->rowCount() > 0){                       
            $doc_date           = thainumDigit(DateThai_full($res->create_at));
            $ven_com_num_all    = "";
            $ven_com_date       = "";  
            $ven_com_idb        = "";   
            $ven_com_name       = ""; 
            $com_name           = "";  
            $ven_month          = $res->ven_month;   
            $ven_month_th       = DateThai_MY($ven_month);   
            $topic              = "ขอเปลี่ยนเวรปฏิบัติราชการ";
            $name1              = "";   
            $name2              = "";   
            $name_dep1          = "";   
            $name_dep2          = ""; 
            $ven_date1          = "";   
            $ven_date2          = "";  
            $change_to_comment  = "ตามวันและเวลาดังกล่าว";

            $ven1_comment       = "";

            $boss_name          = "";   
            $boss_dep           = "";    
            $boss_dep2          = "";   
            $boss_dep3          = "";  
            $boss_sign_comment  = "";  
            $boss_sign_comment2 = "";  
            $boss_sign_comment3 = "อนุญาต";  

            $po_name          = "";   
            $po_dep           = "";   
            $po_dep2          = "";   
            $po_dep3          = "";  
            $po_sign_comment  = "- ทราบ พิจารณาแล้ว";  
            $po_sign_comment2 = "- อนุญาต";  
            $po_sign_comment3 = "- ส่งงานสารบรรณ เพื่อทราบและรวบรวมไว้กับบัญชีลงลายมือชื่อ"; 

            $court_name         ="ศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์";
            $court_name_full    ="สำนักงานประจำศาลศาลเยาวชนและครอบครัวจังหวัดประจวบคีรีขันธ์";
            $send_to            ="ผู้พิพากษาหัวหน้า" .$court_name;
          
            // $ven_com_idb = preg_replace('/[^0-9]/', '', $res->ven_com_idb);
        
            $sql = "SELECT 
                            vc.ven_com_num,
                            vc.ven_com_date,
                            vn.name AS ven_name,
                            vn.name_full AS ven_name_full
                    FROM ven_com as vc
                    INNER JOIN ven_name as vn ON vn.id = vc.vn_id
                    WHERE vc.id = :vc_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':vc_id',$res->ven_com_idb, PDO::PARAM_INT);
            $query->execute();
            $res_ven_com = $query->fetch(PDO::FETCH_OBJ);

            $ven_com_name   = $res_ven_com->ven_name;
            $ven_com        = $res_ven_com->ven_name_full;
            $ven_com_num_all = thainumDigit($res_ven_com->ven_com_num);
            $ven_com_date    = thainumDigit(DateThai_full($res_ven_com->ven_com_date)); 
            
            $user1 = getProfile($res->user_id1);    // $user1['name']; $user1['dep'];
            $user2 = getProfile($res->user_id2);

            
            if ($res->DN == 'กลางวัน') {
                $time = '08.30 – 16.30';
            } elseif ($res->DN == 'กลางคืน') {
                $time = '16.30 – 08.30';
            } elseif ($res->DN == 'nightCourt') {
                $time = '16.30 – 20.30';
            } else {
                $time = ''; // หรือให้ $time เป็นค่าว่าง
            }  
            $time =  thainumDigit($time);              
            
            $ven_date1 = thainumDigit(DateThai_full($res->ven_date1));             
            $ven_date2 = thainumDigit(DateThai_full($res->ven_date2));
            
            

            $sql = "SELECT id, create_at
                    FROM ven_change 
                    WHERE (ven_id1 = :ven_id1_old) OR (ven_id2 = :ven_id11_old) 
                    OR (ven_id1 = :ven_id2_old) OR (ven_id2 = :ven_id22_old)";
            
            $query = $conn->prepare($sql);
            $query->bindParam(':ven_id1_old', $res->ven_id1_old, PDO::PARAM_INT);
            $query->bindParam(':ven_id11_old', $res->ven_id1_old, PDO::PARAM_INT);
            $query->bindParam(':ven_id2_old', $res->ven_id2_old, PDO::PARAM_INT);
            $query->bindParam(':ven_id22_old', $res->ven_id2_old, PDO::PARAM_INT);
            $query->execute();
    
            $res_vc_old = $query->fetchAll(PDO::FETCH_OBJ);
            $vcod = [];
            $vcod_doc = '';
            if($query->rowCount()){
                foreach($res_vc_old as $rs){                    
                    $vcod_doc .= ' และตามบันทึกข้อความลงวันที่ '.DateThai_full($rs->create_at).' ['.$rs->id.']';
                    array_push($vcod,' และตามบันทึกข้อความลงวันที่ '.DateThai_full($rs->create_at).' ['.$rs->id.']');
                }
            }

            if($res->ven_date1 <> $res->ven_date2){
                $change_to_comment  = " และข้าพเจ้าจะมาปฏิบัติหน้าที่แทนในวันที่ ".$ven_date2." เวลา " . $time." นาฬิกา";
            }
            $ven1_comment  = "ข้าพเจ้า ".$user2['name']." ตำแหน่ง ".$user2['dep']." จะมาปฏิบัติหน้าที่แทน ".$user1['name']." ในวันที่ ".$ven_date1." ตั้งแต่ เวลา " . $time." นาฬิกา";



            $sql = "SELECT * FROM sign_name WHERE st = 1 AND role='Court_Name'";
            $query = $conn->prepare($sql);
            $query->execute();
            $res_cort_name = $query->fetch(PDO::FETCH_OBJ);

            if($query->rowCount()){
                $court_name = $res_cort_name->name;
                $court_name_full = $res_cort_name->dep;
            } 

            $send_to = "อธิบดีผู้พิพากษา".$court_name;
            $sql = "SELECT * FROM sign_name WHERE st = 1 AND role='Chief_Judge'";
            $query = $conn->prepare($sql);
            $query->execute();
            $res_boss = $query->fetch(PDO::FETCH_OBJ);
            if($query->rowCount()){
                $boss_name = "(".$res_boss->name.")";
                $boss_dep = $res_boss->dep;
                $boss_dep2 = $res_boss->dep2;
                $boss_dep3 = $res_boss->dep3;                    
            }
            if($user1['workgroup'] == 'ผู้พิพากษา'){
                $po_name = "";
                $po_dep  = "";
                $po_dep2 = "";
                $po_dep3 = "";

                $po_sign_comment = "";
                $po_sign_comment2 = "";
                $po_sign_comment3 = "";
            }else{
                $send_to = "ผู้อำนวยการ".$court_name_full;
                $sql = "SELECT * FROM sign_name WHERE st = 1 AND role='Director'";
                $query = $conn->prepare($sql);
                $query->execute();
                $res_po = $query->fetch(PDO::FETCH_OBJ);
    
                if($query->rowCount()){                    
                    $po_name = "(".$res_po->name.")";
                    $po_dep     = $res_po->dep;
                    $po_dep2    = $res_po->dep2;
                    $po_dep3    = $res_po->dep3;
                }

            }
 

            /**สร้างเอกสาร docx */
            // $path = '../../uploads';
            // $templatePath = $path.'/template_docx/ven_tm.docx';
            $basePath = '../../uploads';
            $templatePath = $basePath . DIRECTORY_SEPARATOR . 'template_docx' . DIRECTORY_SEPARATOR . 'ven_tm.docx';
            $tempChangePath = $basePath . DIRECTORY_SEPARATOR . 'temp_change';
            $fileName = $id . time() . ".docx";
            $fullFilePath = $tempChangePath . DIRECTORY_SEPARATOR . $fileName;

            // ตรวจสอบว่าไดเร็กทอรีว่างเปล่าหรือไม่ ถ้าไม่ว่างเราจะลบไดเร็กทอรีทั้งหมด
           if (!is_dir($tempChangePath)) {
                mkdir($tempChangePath, 0755, true);
            } else {
                // Clear the temp_change directory
                $files = glob($tempChangePath . DIRECTORY_SEPARATOR . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }

            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('doc_date', $doc_date);
            $templateProcessor->setValue('ven_ch_id', $res->id);
            $templateProcessor->setValue('vcn', $ven_com_num_all);

            $templateProcessor->setValue('topic', $topic);//เรือง
            $templateProcessor->setValue('ven_com_date', $ven_com_date);
            $templateProcessor->setValue('ven_com_name', $ven_com_name);
            $templateProcessor->setValue('com_name', $ven_com);
            $templateProcessor->setValue('comment', $vcod_doc);
            $templateProcessor->setValue('ven_month', $ven_month);
            $templateProcessor->setValue('ven_month_th', $ven_month_th);

            $templateProcessor->setValue('name1', $user1['name']);
            $templateProcessor->setValue('name_dep1', $user1['dep']);
            $templateProcessor->setValue('name2', $user2['name']);
            $templateProcessor->setValue('name_dep2', $user2['dep']);

            $templateProcessor->setValue('ven_date1', $ven_date2);
            $templateProcessor->setValue('ven_date2', $ven_date1);
            $templateProcessor->setValue('time', $time);

            $templateProcessor->setValue('change_to_comment', $change_to_comment); 
            $templateProcessor->setValue('ven1_comment', $ven1_comment); 

            $templateProcessor->setValue('boss_name', $boss_name);   
            $templateProcessor->setValue('boss_dep', $boss_dep);   
            $templateProcessor->setValue('boss_dep2', $boss_dep2); 
            $templateProcessor->setValue('boss_dep3', $boss_dep3); 
            $templateProcessor->setValue('boss_sign_comment', $boss_sign_comment); 
            $templateProcessor->setValue('boss_sign_comment2', $boss_sign_comment2); 
            $templateProcessor->setValue('boss_sign_comment3', $boss_sign_comment3); 
            
            $templateProcessor->setValue('po_name', $po_name);   
            $templateProcessor->setValue('po_dep',  $po_dep);   
            $templateProcessor->setValue('po_dep2', $po_dep2); 
            $templateProcessor->setValue('po_dep3', $po_dep3); 
            $templateProcessor->setValue('po_sign_comment',  $po_sign_comment); 
            $templateProcessor->setValue('po_sign_comment2', $po_sign_comment2); 
            $templateProcessor->setValue('po_sign_comment3', $po_sign_comment3); 
            
            $templateProcessor->setValue('send_to', $send_to); 
            $templateProcessor->setValue('court_name', $court_name);   
            $templateProcessor->setValue('court_name_full', $court_name_full);   

            $file_name = "ven.docx";
            $file_name = "/temp_change/".$id.time().".docx";           

            // $templateProcessor->saveAs($path.$file_name);  //สั่งให้บันทึกข้อมูลลงไฟล์ใหม่
            $templateProcessor->saveAs($fullFilePath);

            // Dynamically get the host and construct the base URL
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $baseURL = $protocol . $host . '/vengg/uploads/temp_change/';

            // Generate the URL
            $url = $baseURL . $fileName;
           
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'OK', 
                'url'   => $url,
                'respJSON' => [
                    'doc_date'  => $doc_date,
                    'id'   => $res->id,
                    'ven_com_idb'   => $ven_com_idb,
                    'vcn'=> $ven_com_num_all,
                    'topic'=> $topic,
                    'ven_com_date'   => $ven_com_date,
                    'ven_com_name'   => $ven_com_name,
                    'com_name'   => $com_name,
                    'comment'  => $vcod_doc,
                    'time'  => $time,
                    'DN'  => $res->DN,
                    'change_to_comment'  => $change_to_comment,
                    'name1'   => $user1['name'],
                    'name2'   => $user2['name'],
                    'name_dep1'   => $user1['dep'],
                    'name_dep2'   => $user2['dep'],
                    'ven_date1'   => $ven_date1,
                    'ven_date2'   => $ven_date2,
                    "sign_name" => [
                        "boss"=>[
                            "boss_name"=>$boss_name,
                            "boss_dep"=>$boss_dep,
                            "boss_dep2"=>$boss_dep2,
                            "boss_dep3"=>$boss_dep3,
                            "boss_sign_comment"     =>$boss_sign_comment,
                            "boss_sign_comment2"    =>$boss_sign_comment2,
                            "boss_sign_comment3"    =>$boss_sign_comment3,
                        ],
                        "po"=>[
                            "po_name"   =>$po_name,
                            "po_dep"    =>$po_dep,
                            "po_dep2"   =>$po_dep2,
                            "po_dep3"   =>$po_dep3,
                            "po_sign_comment"    =>$po_sign_comment,
                            "po_sign_comment2"   =>$po_sign_comment2,
                            "po_sign_comment3"   =>$po_sign_comment3,
                        ],
                    ]                 
                ]
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array(
            'status' => false, 
            '$res ' => $res , 
            'message' => 'ไม่พบข้อมูล '
        ));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}

function getProfile($uid){
    global $conn;
    $sql = "SELECT p.fname, p.name, p.sname, p.dep, p.workgroup
            FROM profile as p  
            WHERE user_id = :user_id";
    $query = $conn->prepare($sql);
    $query->bindParam(':user_id',$uid, PDO::PARAM_INT);
    $query->execute();
    $res_p = $query->fetch(PDO::FETCH_OBJ);
    return [
        "name" => $res_p->fname.$res_p->name.' '.$res_p->sname, 
        "dep"  => $res_p->dep,
        "workgroup"  => $res_p->workgroup
    ];
}


