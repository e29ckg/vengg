<?php

require_once('../../server/authen.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <?php require_once('../includes/_header.php') ?>

    <link rel="stylesheet" href="../../assets/fullcalendar/main.css">
    <script src="../../assets/fullcalendar/main.min.js"></script>
    <style>
        .modalCenter {
            top: 10% !important;
            /* tramsform:translateY(-25%) !important; */
        }

        /* .list-group-item-secondary{ cursor: pointer; } */
        .list-group-item-secondary:hover {
            cursor: pointer;
            background: #FFEBCD;
        }
    </style>
</head>

<body class="theme-dark">
    <div id="app">


        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>หน้าแรก </h3>

            </div>

            <!-- Content wrapper -->
            <div class="content-wrapper" id="dashboard">

                <!-- ========================= preloader start ========================= -->
                <div class="preloader" ref="loading" v-if="isLoading">
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

                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row">
                        <div class="col-12 ">
                            <div class="card">
                                <div class="card-body">
                                    <div id='calendar' ref="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" ref="show_modal" hidden>
                    Launch static backdrop modal
                </button>

                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"> {{data_event.id}}
                                    <span class="badge bg-warning" v-if="data_event.status ==2">รออนุมัติ</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="close_m" ref="close_modal1"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card mb-2">
                                    <div class="row g-0">
                                        <div class="col-md-2">
                                            <img v-else :src="data_event.img" class="img-fluid rounded-start" alt="data_event.img">
                                        </div>
                                        <div class="col-md-10">
                                            <div class="card-body">
                                                <h4 class="card-title">{{data_event.u_name}} </h4>

                                                <h6>
                                                    <span class="badge bg-secondary me-2">{{data_event.u_role}}</span>
                                                    <span class="badge bg-warning" v-if="data_event.status == 2">รออนุมัติ</span>
                                                </h6>
                                                <p class="card-text">
                                                    {{data_event.ven_date_th}} ({{data_event.ven_time}})<br>
                                                    {{data_event.DN}} {{data_event.ven_com_name}} <br>
                                                    {{data_event.ven_com_num_all ? 'คำสั่งที่ '+data_event.ven_com_num_all: ''}}
                                                </p>
                                                <p><button @click="report_vn(data_event.ven_date, data_event.vn_id, data_event.vns_id, data_event.user_id)">รายงานเวร</button></p>
                                                <!-- เวรปฏิบัติหน้าที่ออกหมายจับและหมายค้นนอกเวลาราชการ (เวรกลางคืน) -->
                                                <div v-if="data_event.vns_id == 110">
                                                    <button class="btn btn-success btn-sm mt-2" @click="report_jk(data_event.ven_date)">
                                                        รายงานเวรหมายจับหมายค้น
                                                    </button>
                                                    <!-- <button class="btn btn-success btn-sm mt-2" @click="report_jk(data_event.ven_date)">
                                                        บัญชีลงเวลา
                                                    </button> -->

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="basic-table">
                                    <div class="col-12 col-md-12">
                                        <div class="card">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-lg table-bordered" ref="ven_ch_board" v-if="changeHistory">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th colspan="3">ประวัติการเปลี่ยน</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(v,index) in changeHistory">
                                                                    <td>{{v.id}}</td>
                                                                    <td class="text-bold-500">{{v.u_name}}</td>
                                                                    <td>
                                                                        <div class="" v-if="v.status == 2 && (v.print)">
                                                                            <button class="btn btn-warning me-2" type="button" @click="print(v.id)">
                                                                                <i class="bi bi-printer"></i></button>
                                                                            <button class="btn btn-danger" type="button" @click="cancle(v.id)">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                        <?php if ($_SESSION['AD_ROLE'] == '9') { ?>
                                                                            <div v-if="changeHistory.length !== (index + 1)">
                                                                                <button class="btn btn-warning me-2" type="button" @click="print(v.id)">
                                                                                    <i class="bi bi-printer"></i> </button>
                                                                                <button class="btn btn-danger" v-if="v.status == 2" type="button" @click="cancle(v.id)">
                                                                                    <i class="bi bi-trash"></i>
                                                                                </button>

                                                                            </div>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <button type="button" class="btn btn-warning m-2" @click="getUsersForChange(data_event)" v-if="btnUsersForChange">ยกให้</button>
                                                        <button type="button" class="btn btn-primary" @click="getUsersForChange2(data_event.id)" v-if="btnVensForChange">ขอเปลี่ยน</button>
                                                        <span class="bg-danger text-white" v-if="data_event.resp_text">{{data_event.resp_text}}</span>
                                                    </div>

                                                    <!-- ADMIN -->
                                                    <!-- <div class="d-flex justify-content-center align-items-center">                                                            
                                                            <div class="col-6 text-center" >
                                                                <button class="btn btn-warning" @click="admin_ch_b()">admin_ยกให้ </button>  
                                                            </div>
                                                            <div class="col-6 text-center" >
                                                                <button class="btn btn-primary" @click="admin_ch_a()">admin_ขอเปลี่ยน</button>  
                                                            </div>
                                                        </div> -->
                                                    <!-- /admin -->

                                                    <div class="row">
                                                        <div class="table-responsive">
                                                            <table class="table table-lg table-bordered">
                                                                <tbody>
                                                                    <tr v-for="item in usersForChange">
                                                                        <td><img :src="item.img" alt="toUser" height="50"></td>
                                                                        <td>
                                                                            {{item.u_name}}<br>
                                                                            <span v-if="item.changeStatus.status === false">{{item.changeStatus.text}}</span>
                                                                        </td>
                                                                        <td v-if="item.changeStatus.status" class="text-center">
                                                                            <button class="btn btn-success" @click="send_user_up(data_event,item)">เลือก</button>
                                                                        </td>
                                                                        <td v-else class="text-center">-</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-lg table-bordered">
                                                                <tbody>
                                                                    <tr v-for="item in vensForChange">
                                                                        <td><img :src="item.img" alt="toUser" height="50"></td>
                                                                        <td>{{item.u_name}}</td>
                                                                        <td>{{item.ven_date}}</td>
                                                                        <td v-if="item.changeStatus.status" class="text-center"><button class="btn btn-success" @click="send_user_ck(data_event,item)">เลือก</button></td>
                                                                        <td v-else>{{item.changeStatus.status === false ? data_event.u_name + ' ' + item.changeStatus.text : ''}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>


                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
            <div class="content-backdrop fade"></div>

            <?php require_once('../includes/_footer.php') ?>
        </div>


    </div>

    <?php require_once('../includes/_footer_sc.php') ?>

    <!-- <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script> -->
    <!-- <script src="../../assets/js/bootstrap.bundle.min.js"></script> -->

    <!-- <script src="../../assets/js/main.js"></script> -->
    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./index.js"></script>
</body>

</html>