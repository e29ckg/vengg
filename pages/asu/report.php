<?php 

require_once('../../server/authen.php'); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once('../includes/_header.php') ?>

</head>
<body>
    <div id="app">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3 d-print-none">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading d-print-none">
                <h3>Report</h3>
            </div>
            <div class="page-content" id="asuReport" v-cloak>

                <!-- ========================= preloader start ========================= -->
                <div class="preloader"  ref="loading" v-if="isLoading">
                    <div class="loader">
                        <div class="spinner">
                            <div class="spinner-container">
                                <div class="spinner-rotator">
                                    <div class="spinner-left">
                                        <div class="spinner-circle"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text">
                            กำลังประมวลผล...
                        </div>
                    </div>                        
                </div>
                <!-- preloader end -->
                
                <section class="row" >           
                    <div class="col-12 col-lg-12">
                        <!-- {{ven_coms}} -->
                        <!-- {{ven_coms_g}} -->
                        <div class="row">
                            <div class="col-12 text-end mb-2">
                            </div>
                            <div class="col col-12">                                
                                <div class="card" v-for='cvg in ven_coms_g'>
                                    <div class="card-body" >
                                        
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="text-start">
                                                        เวรเดือน {{cvg.ven_month_th}} 
                                                        <button type="button" class="btn btn-danger" :disabled='isLoading' @click="con_f(cvg.ven_month)">
                                                            {{isLoading ? 'Londing..': 'เผยแพร่'}}</button>
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody  v-for="vc in ven_coms">
                                                <tr v-if="vc.ven_month == cvg.ven_month">
                                                        <td >  
                                                            เลขคำสั่งที่ {{vc.ven_com_num}} | ลงวันที่ {{vc.ven_com_date_th}} | {{vc.ven_com_name}} ({{vc.ven_name}})
                                                            <!-- | {{vc.ref}} | {{vc.status}} -->
                                                            <button class="btn btn-warning btn-sm me-2" @click="uptogcal(vc.id)">up to google calendar</button>
                                                            
                                                        </td>
                                                        
                                                        <td class="text-end col " style="width: 250px;">
                                                            <button class="btn btn-warning btn-sm me-2" @click="view(vc.id)">view</button>
                                                            <button class="btn btn-primary btn-sm m-2" @click="print(vc.id)">พิมพ์เอกสารแนบท้าย</button>
                                                            <button class="btn btn-primary btn-sm m-2" @click="print2(vc.id)">แนบท้าย2</button>
                                                        </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>                           
                        </div>    
                    </div>
                 
                </section>

                <!-- Modal venUser Form -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#view" ref="show_modal" hidden >
                        view
                </button>
                <!-- Modal venUser Form -->
                <div class="modal fade" id="view"  data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel" >view</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="close_modal"></button>
                            
                            </div>
                            <div class="modal-body ">
                                <p class="text-center">
                                    เลขคำสั่งที่ {{vc.ven_com_num}} | ลงวันที่ {{vc.ven_com_date}} | {{vc.ven_com_name}} 
                                </p>
                            <table class="table table-bordered ">
                                <thead>
                                    <tr class="text-center">
                                        <th>วัน เดือน ปี</th>
                                        <th>เวลา</th>
                                        <th>รายชื่อผู้พิพากษา</th>
                                        <th>รายชื่อเจ้าหน้าที่</th>
                                        <th>หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    

                                    <tr v-for="d,i in datas">
                                        <td>{{date_thai_dt(d.ven_date)}}</td>
                                        <td>
                                            <!-- {{d.ven_time}} -->

                                            <li class="list-group-item h-100" v-for="dvt in d.ven_time">
                                                {{dvt == '08:30' ? '08.30 - 16.30 น.' : '16.30 - 08.30 น.'}}
                                            </li>
                                        </td>
                                        <td>
                                                <li class="list-group-item mt-0" v-for="dunj in d.u_namej">
                                                    {{dunj}}
                                                </li>

                                        </td>
                                        <td> 
                                            <li class="list-group-item" v-for="dun in d.u_name">
                                                {{dun}}
                                            </li>
                                        </td>
                                        <td>
                                                <li class="list-group-item" v-for="dur in d.cmt">
                                                    {{dur}} 
                                                </li>
                                            
                                            <!-- <li v-for="dur in d.cmt">
                                                {{dur}}
                                            </li> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 

                                
                                
                                
                            </div>
                            <div class="modal-footer">
                               

                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>

    <?php require_once('../includes/_footer_sc.php') ?>

    <!-- <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script> -->
    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/report.js"></script>
</body>

</html>