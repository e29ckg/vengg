<?php 

require_once('../../server/authen.php'); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    
<?php require_once('../includes/_header.php') ?>
<!-- Styles -->
<link rel="stylesheet" href="../../node_modules/select2-bootstrap-5-theme/dist/css/select2.min.css" />
<link rel="stylesheet" href="../../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .dropdown-menu{
        min-width: 1rem;
    }
    .dropdown-item-edit:hover{
        background-color: yellow; /* เปลี่ยนสีข้อความเป็นสีเหลืองเมื่อเมาส์ชี้ */
    }
    .dropdown-item-delete:hover{
        background-color: red; /* เปลี่ยนสีข้อความเป็นสีเหลืองเมื่อเมาส์ชี้ */
    }
</style>
<!-- Or for RTL support -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" /> -->

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
                <h3>เตรียมผู้อยู่เวร </h3>
            </div>
            
            <div class="page-content" id="venUser" v-cloak> 
                <!-- {{datas}}             -->
                <!-- {{users}} -->
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <!-- {{ven_names}} -->
                        <div class="row">
                            <div class="col col-6 " v-for="item, index in datas">  
                                <div class="card ">
                                <!-- {{item}} -->
                                <div class="card-header text-white" :style="'background-color: ' + item.color + ';'">
                                    <span><b>{{item.vn_name}}</b></span> < {{item.DN}} > <br>{{item.vns_name}}                                     
                                </div>
                                <div class="card-body" > 
                                    <table class="table table-striped">
                                        
                                        <tbody>
                                            <tr v-for="d_user in item.users" >
                                                <td >{{d_user.order}}</td>
                                                <td class="text-start fs-6">
                                                    <span><b>{{d_user.name}}</b></span><br>
                                                    <span class="text-sm">{{d_user.workgroup}}</span>
                                                </td>
                                                <td class="text-end">
                                                    <!-- <button @click="vu_up(d_user.vu_id)" class="btn btn-warning btn-sm me-1">แก้ไข</button>
                                                    <button @click="vu_del(d_user.vu_id)" class="btn btn-danger btn-sm">ลบ</button> -->
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-md  d-flex align-middle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Menu">
                                                            <i class="bi bi-three-dots-vertical align-self-center"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item dropdown-item-edit d-flex align-middle" href="#" @click="vu_up(d_user.vu_id)">        
                                                                    <i class="bi bi-pencil me-2 align-self-center"></i> <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item dropdown-item-delete d-flex align-middle" href="#"  @click="vu_del(d_user.vu_id)">
                                                                    <i class="bi bi-trash me-2 align-self-center"></i> <span>Delete</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end" >
                                                    <button class="btn btn-success me-2" @click="vu_add(index)">เพิ่ม {{item.vns_name}}</button>
                                                </td>
                                            </tr>
                        

                                        </tfoot>
                                    </table>                                                                               
                                    
                                    <!-- <li class="list-group-item">
                                        <!-- <button class="btn btn-success" @click="vu_add_user_all(vni,vnsi)">
                                            {{isLoading ? 'Loading...' : 'เพิ่ม USER ทั้งหมด'}}
                                        </button>
                                    </li>                                   -->
                                    
                                </div>
                                </div>

                            </div>
                        </div>
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
                                <h5 class="modal-title" id="staticBackdropLabel">{{vu_form_act == 'insert' ? 'เพิ่มชื่อผู้อยู่เวร' : 'แก้ไขชื่อผู้อยู่เวร'}} {{workgroup}}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="clear_vu_form" ref="close_vu"></button>
                            </div>
                            <div class="modal-body">
                                <!-- {{vu_form}} -->
                                <!-- {{vu_form_act}} -->
                                <form @submit.prevent="vu_save">                                    
                                    <div class="row mb-3">                                        
                                        <div class="col mb-3">
                                            <label for="srt" class="form-label">ลำดับ</label>
                                            <input type="number" min="0" class="form-control" id="srt" v-model="vu_form.order">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="nameuf" class="form-label">ชื่อ</label>
                                            <select class="form-select" id="basic-usage"  aria-label="Default select example" v-model="vu_form.user_id" >
                                                <optgroup label="ผู้พิพากษา" v-if="workgroup =='ผู้พิพากษา'">
                                                    <option v-for="j in judge" :value="j.uid" >{{j.name}}</option>
                                                </optgroup>
                                                <optgroup label="ผู้พิพากษาสมทบ" v-if="workgroup =='ผู้พิพากษาสมทบ'" >
                                                    <option v-for="j in judge_somtop" :value="j.uid" > {{j.name}}</option>
                                                </optgroup>
                                                <optgroup label="เจ้าหน้าที่" v-if="workgroup !== 'ผู้พิพากษา' && workgroup !== 'ผู้พิพากษาสมทบ'">
                                                    <option v-for="u in not_judge" :value="u.uid" >{{u.name}}</option>
                                                </optgroup>
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
    
    <?php require_once('../includes/_footer_sc.php') ?>
    <!-- <script src="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script> -->    
    <!-- <script src="../../assets/js/main.js"></script> -->

    <!--  -->
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <!-- Scripts -->
    <script src="./js/ven_user.js"></script>
    
</body>

</html>