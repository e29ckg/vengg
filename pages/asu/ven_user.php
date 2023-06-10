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
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>เตรียมผู้อยู่เวร</h3>
            </div>
            
            <div class="page-content" id="venUser" v-cloak> 
                <!-- {{ven_names}}             -->
                <!-- {{ven_name_subs}}             -->
                <!-- {{ven_users}}             -->
                <!-- {{users}} -->
                <section class="row" v-for="vn, index in ven_names">
                    <div class="card-body" >
                        <h5 class="card-title" :style="'background-color: '+vn.color+' ; color:white;'" >{{vn.vn_name}} ({{vn.DN == 'กลางวัน' ? '☀️' : '🌙'}} {{vn.DN}}) {{vn.vns_name}} </h5>
                                
                        <!-- {{vn}} -->
                        <div v-if="ven_users" v-for="vu in ven_users" >
                            <li class="list-group-item" v-if="vu.ven_name == vn.vn_name && vn.vns_name == vu.uvn">
                                <!-- {{vu}} -->
                                {{vn.vns_DN == 'กลางวัน' ? '☀️' : '🌙'}}{{vu.order + ' ' +vu.u_name + ' '}}  
                                <!-- {{vu.id}}  -->
                                <button @click="vu_up(vu.id)" class="btn btn-warning btn-sm me-1">แก้ไข</button>
                                <button @click="vu_del(vu.id)" class="btn btn-danger btn-sm">ลบ</button>
                            </li>     
                        </div>
                        <li class="list-group-item">
                            <button class="btn btn-success me-2" @click="vu_add(index)">เพิ่มที่ละคน</button>
                            <!-- <button class="btn btn-success" @click="vu_add_user_all(vni,vnsi)">
                                {{isLoading ? 'Loading...' : 'เพิ่ม USER ทั้งหมด'}}
                            </button> -->
                        </li>                                  

                    </div>
                </section>
                

                <!-- Modal venUser Form -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ven_user" ref="show_vu_form" hidden >
                        เพิ่มผู้อยู่เวร
                </button>
                <!-- Modal venUser Form -->
                <div class="modal fade" id="ven_user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">{{vu_form_act == 'insert' ? 'เพิ่มชื่อผู้อยู่เวร' : 'แก้ไขชื่อผู้อยู่เวร'}}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clear_vu_form" ref="close_vu"></button>
                            </div>
                            <div class="modal-body">
                                <!-- {{vu_form}}
                                {{vu_form_act}} -->
                                <form @submit.prevent="vu_save">                                    
                                    <div class="row mb-3">                                        
                                        <div class="col mb-3">
                                            <label for="srt" class="form-label">ลำดับ</label>
                                            <input type="number" min="0" class="form-control" id="srt" v-model="vu_form.order">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="nameuf" class="form-label">ชื่อ</label>
                                            <select class="form-select" id="basic-usage"  aria-label="Default select example" v-model="vu_form.user_id" >
                                                <optgroup label="ผู้พิพากษา" v-if="judge.length > 0">
                                                    <option v-for="j in judge" :value="j.uid" >  
                                                    {{j.name}}
                                                </option>
                                                <optgroup label="เจ้าหน้าที่" v-if="not_judge.length > 0">
                                                    <option v-for="u in not_judge" :value="u.uid" >  
                                                    {{u.name}}
                                                </option>
                                                <!-- <option v-for="u in users" :value="u.uid" >{{u.name}}</option> -->
                                                  
                                            </select>
                                        </div> 
                                                                            
                                    </div>
                                    <div class="d-grid gap-2">
                                        <!-- <button type="button" class="col-auto me-auto btn btn-danger" v-if="vu_form_act !='insert'" @click.prevent="ven_name_del()">ลบ {{ven_name_form.id}}</button> -->
                                        <button type="submit" class="col-auto btn btn-primary">บันทึก</button>
                                    </div>
                                </form>                                
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>
    
    <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    
    <script src="../../assets/js/main.js"></script>

    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <!-- Scripts -->
    <script src="./js/ven_user.js"></script>
    
</body>

</html>