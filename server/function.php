<?php

/** Clean Data */
function cleanData($input)
{
    /** เปลี่ยน predefined characters เป็น HTML entities ด้วยฟังก์ชัน htmlspecialchars() */
    // $data = trim($data);
    // $data = stripslashes($data);
    $data = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to sanitize input data
function sanitize($data)
{
    // Remove leading and trailing whitespaces
    $data = trim($data);

    // Remove backslashes
    $data = stripslashes($data);

    // Convert special characters to HTML entities
    $data = htmlspecialchars($data);

    return $data;
}


/** Method สำหรับการเช็ครูปภาพ Mime Image */
function isMimeValid($tmp_name)
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mtype = finfo_file($finfo, $tmp_name);
    if (strpos($mtype, 'image/') !== false) {
        return true;
    }
    finfo_close($finfo);
    return false;
}

/** เปลี่ยนวันที่เป็นภาษาไทย */
function DateThai($strDate)
{
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
    $strMonthThai = $strMonthCut[$strMonth];
    $strYearCut = substr($strYear, 2, 2);
    return "$strDay $strMonthThai $strYearCut";
}
function DateThai_N_full($strDate)
{
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strD = date("N", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strMonthCut = array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );
    $strDayCut = array("", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์", "อาทิตย์");
    $strMonthThai = $strMonthCut[$strMonth];
    $strDayThai = $strDayCut[$strD];
    return "วัน" . $strDayThai . "ที่ " . "$strDay $strMonthThai $strYear";
}
function DateThai_full($strDate)
{
    if ($strDate == '') {
        return "-";
    }
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strMonthCut = array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}

function DateThai_for_kh($strDate)
{
    if ($strDate == '') {
        return "-";
    }
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strMonthCut = array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay เดือน $strMonthThai พุทธศักราช $strYear";
}

function DateThai_MY($strDate)
{
    if ($strDate == '') {
        return "-";
    }
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strHour = date("H", strtotime($strDate));
    $strMinute = date("i", strtotime($strDate));
    $strSeconds = date("s", strtotime($strDate));
    $strMonthCut = array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strMonthThai $strYear";
}

function generateRandomString($length = 20)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

function generateRandomNumber($min = 1000, $max = 9999)
{
    return rand($min, $max);
}

function Num_f($num)
{
    return thainumDigit(number_format($num));
}
function Convert($amount_number)
{
    $amount_number = number_format($amount_number, 2, ".", "");
    $pt = strpos($amount_number, ".");
    $number = $fraction = "";
    if ($pt === false) {
        $number = $amount_number;
    } else {
        $number = substr($amount_number, 0, $pt);
        $fraction = substr($amount_number, $pt + 1);
    }

    $ret = "";
    $baht = ReadNumber($number);
    if ($baht != "") {
        $ret .= $baht . "บาท";
    }

    $satang = ReadNumber($fraction);
    if ($satang != "") {
        $ret .= $satang . "สตางค์";
    } else {
        $ret .= "ถ้วน";
    }

    return $ret;
}

function ReadNumber($number)
{
    $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
    $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
    $number = $number + 0;
    $ret = "";
    if ($number == 0) {
        return $ret;
    }

    if ($number > 1000000) {
        $ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
        $number = intval(fmod($number, 1000000));
    }

    $divider = 100000;
    $pos = 0;
    while ($number > 0) {
        $d = intval($number / $divider);
        $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : ((($divider == 10) && ($d == 1)) ? "" : ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
        $ret .= ($d ? $position_call[$pos] : "");
        $number = $number % $divider;
        $divider = $divider / 10;
        $pos++;
    }
    return $ret;
}

function thainumDigit($num)
{
    return str_replace(
        array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
        array("๐", "๑", "๒", "๓", "๔", "๕", "๖", "๗", "๘", "๙"),
        $num
    );
};

function sendLine($sToken, $sMessage)
{
    $chat_id = '-1002281262112';
    $botToken = '7785178042:AAHGfC4wPtdlMLCPHwpR_Y12CkELO5FaOUI';
    sendTelegram($sMessage, $chat_id);

    // if (isInternetAvailable()) {
    //     $access_token = $sToken;
    //     $message = $sMessage;


    //     $data = array(
    //         'message' => $message
    //     );

    //     $options = array(
    //         'http' => array(
    //             'method' => 'POST',
    //             'header' => "Authorization: Bearer {$access_token}\r\n" .
    //                 "Content-Type: application/x-www-form-urlencoded\r\n",
    //             'content' => http_build_query($data),
    //         ),
    //     );

    //     $context = stream_context_create($options);
    //     $response = file_get_contents('https://notify-api.line.me/api/notify', false, $context);
    //     $response_decoded = json_decode($response, true);

    //     if ($response_decoded['status'] == 200) {
    //         // notification was sent successfully
    //         return true;
    //     } else {
    //         // an error occurred
    //         return $response_decoded['message'];
    //     }
    // }
}

function gcal_insert($name, $start, $desc = null)
{
    $message_data = [
        "action" => "insert",
        "dataEvent" => [
            "summary" => $name,
            "description" => $desc,
            "start" => $start,
            "end" => $start,
            "colorId" => 1
        ]
    ];
    return gcal_send_date($message_data);
}
function gcal_remove($gcal_id)
{
    $message_data = [
        "action"    => "remove",
        "eventId"   => $gcal_id,

    ];
    return gcal_send_date($message_data);
}
function gcal_update($gcal_id, $name, $desc = null, $colerId = 1)
{
    $message_data = [
        "action"    => "update",
        "eventId"   => $gcal_id,
        "dataEvent" => [
            "summary" => $name,
            "description" => $desc,
            // "start" => $start,
            // "end" => $start,
            "colorId" => $colerId
        ]
    ];
    return gcal_send_date($message_data);
}

function gcal_send_date($message_data)
{
    $url = 'http://127.0.0.1/service/google/calendar/calendar.php';
    $headers = array('Method: POST', 'Content-type: application/json');
    $message_data = json_encode($message_data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $message_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    // // close the connection, release resources used
    curl_close($ch);
    return $res ? $res : false;
}

function isInternetAvailable($url = 'https://www.google.com')
{
    $headers = @get_headers($url);

    // Check if there are headers
    if ($headers && is_array($headers)) {
        // Iterate through headers and look for the "200 OK" status
        foreach ($headers as $header) {
            if (strpos($header, '200 OK') !== false) {
                return true; // Internet is available
            }
        }
    }

    return false; // Internet is not available
}



function sendTelegram($sMessage, $chat_id = 787363591, $botToken = '7785178042:AAHGfC4wPtdlMLCPHwpR_Y12CkELO5FaOUI')  // Replace with your actual token
{
    

    $curl = curl_init();

    $postFields = json_encode([
        'chat_id' => $chat_id,
        'text' => $sMessage,
        'disable_notification' => true
    ]);

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.telegram.org/bot$botToken/sendMessage",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
}
