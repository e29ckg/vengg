<?php 

require_once('../../server/authen.php'); 

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print-Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>
        [v-cloak] > * { display:none; }
        [v-cloak]::before { content: "loading..."; }
        @font-face {
            font-family: Sarabun;
            src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf);
            /* font-weight: bold; */
        }

        * {
            font-family : Sarabun;
            font-size   : small;
        }
    </style>  
    </head>
  <body>
    <div id="appReports" v-cloak>
        <div class="text-center">
            <h5>บัญชีแนบท้ายคำสั่งศาลเยาวชนและครอบครัวจังหวัดสตูล ที่ {{datas.vc.ven_com_num}} ลงวันที่ {{date_thai(datas.vc.ven_com_date)}}</h5>
            <h5>รายชื่อ ผู้พิพากษาและเจ้าหน้าที่ (ตรวจสอบการจับม รับและส่งตัวผู้ถูกจับตามหมายจับ, ปล่อยชั่วคราว, <br>
                ผัดฟ้องตาม พ.ร.บ.คุ้มครองเด็กฯ และ พ.ร.บ.ผู้ถูกกระทำด้วยความรุนแรงฯ หมายค้น-จับ)
              </h5>
            <h5>ประจำเดือน {{date_thai_my(datas.vc.ven_month)}} ตั้งแต่เวลา 08.30-16.30 น.</h5>
            <!-- {{datas.vc}} -->
        </div>
        <table class="table table-bordered d-print-inline d-print-table ">
            <thead>
                <tr class="text-center">
                    <th>วัน เดือน ปี</th>
                    <th>ชื่อ-สกุล</th>
                    <th>ตำแหน่ง</th>
                    <th>ปฏิบัติงาน</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="d in datas.respJSON">
                    <td>{{date_thai_dt(d.ven_date)}}</td>
                    <td>
                      <li class="list-group-item" v-for="dun,index in d.u_name">{{index+1}}. {{dun}}</li>
                    </td>                    
                    <td>
                      <li class="list-group-item" v-for="dud in d.u_dep"> {{dud}}</li>
                        
                    </td>
                    <td> 
                      <li class="list-group-item" v-for="dur in d.cmt">{{dur}}</li>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                  <td colspan="5"></td>
                </tr>
            </tbody>
            <tfoot>
              <tr>
                <td class="text-end">
                  หมายเหตุ : 
                </td>
                <td colspan="4">
                  * ให้ปฏิบัติเวรผัดฟ้องตาม พ.ร.บ.คุ้มครองเต็ก, พ.ร.บ.ผู้ถูกระทำด้วยความรุนแรงฯ และรับและส่งตัวผู้ถูกจับตามหมายจับ<br>
                  ตั้งแต่เวลา ๑๘.๓๐ - ๑๒.๓๐ น.<br>
                  ๑. ผู้มีรายชื่อลำดับที่ ๑-๓ ให้ปฏิบัติหน้าหน้าที่กรรมการเก็บรักษาเงินด้วย<br>
                  ๒. ผู้มีรายชื่อลำดับที่ ๒ ให้ปฏิบัติหน้าที่ตรวจเวรรักษาการณ์<br>
                  ๓. ผู้มีรายชื่อลำดับที่ ๓-๔ ให้ปฏิบัติหน้าที่เวรรักษาการณ์

                </td>
              </tr>
              <tr>
                <td colspan="2">
                </td>
                <td colspan="3" class="text-center">
                  <br>
                  <br>
                  <br>
                    (นายไพโรจน์ ไพมณี)<br>
                    ผู้พิพากษาหัวหน้าศาลเยาวชนและครอบครัวจังหวัดสตูล
                </td>
              </tr>
            </tfoot>
        </table> 
        
<!-- {{datas}} -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script>
 
  Vue.createApp({
    data() {
      return {
        datas:''     
      }
    },
    mounted(){   
      this.datas = JSON.parse(localStorage.getItem("print"))
      // localStorage.removeItem("print")
      // window.print()
    },
    methods: {    
      
      formatCurrency(number) {
          number = parseFloat(number);
          return number.toFixed(2).replace(/./g, function(c, i, a) {
              return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
          });
        }, 
        getYM(dat){
            let MyDate = new Date(dat);
            let MyDateString;
            // MyDate.setDate(MyDate.getDate() + 20);
            MyDateString = MyDate.getFullYear() + '-' + ("0" + (MyDate.getMonth()+1)).slice(-2)
            return ("0" + MyDate.getDate()).slice(-2)+ '-' + ("0" + (MyDate.getMonth()+1)).slice(-2) + '-' + (MyDate.getFullYear() + 543)
        },
        date_thai(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
        },    
        date_thai_dt(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return dayNames[d.getDay()] + ' '+ d.getDate() + ' ' + monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
        },    
        date_thai_my(day){
          var monthNamesThai = ["มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
          var dayNames = ["วันอาทิตย์ที่","วันจันทร์ที่","วันอังคารที่","วันพุธที่","วันพฤหัสบดีที่","วันศุกร์ที่","วันเสาร์ที่"];
          var monthNamesEng = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
          var dayNamesEng = ['Sunday','Monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
          var d = new Date(day);
          return monthNamesThai[d.getMonth()] + "  " + (d.getFullYear() + 543)
        },    
      

    },
  }).mount('#appReports');
</script>
  </body>
</html>