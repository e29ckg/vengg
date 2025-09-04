<?php 

require_once('../../server/authen.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('../includes/_header.php') ?>
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
                <h3>ตั้งค่า รายงาน</h3>
            </div>
            <div class="page-content" id="ven_name_report_app" v-cloak>
                <section class="row">
                    <div class="col-12 col-lg-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <div>
                                                <!-- <button class="btn btn-success btn-md d-flex align-middle" @click="insert()">
                                                    <i class="bi bi-plus-circle align-self-center me-2"></i> <span> เพิ่ม</span>
                                                </button> -->
                                               
                                            </div>
                                        </div>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">id</th>
                                                    <th scope="col">name</th>
                                                    <th scope="col">word</th>
                                                    <th scope="col">act</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in dataList">
                                                    <td><b>{{item.id}}</b></td>
                                                    <td>
                                                        <b>{{ item.name }}</b>
                                                    </td>
                                                    <td>
                                                        <a v-if="item.word_link" :href="item.word_link" target="_blank" rel="noopener noreferrer">
                                                            <i class="bi bi-file-earmark-word"></i>
                                                            </a>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm dropdown-toggle d-flex align-middle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical align-self-center"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item dropdown-item-edit d-flex align-middle" href="#" @click="update(item.id)">
                                                                        <i class="bi bi-pencil me-2 align-self-center"></i> <span>Edit</span> 
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li v-if="item.word_link" >
                                                                    <a class="dropdown-item dropdown-item-delete d-flex align-middle" href="#" @click="deleteRecord(item.id)">
                                                                        <i class="bi bi-trash me-2 align-self-center"></i> <span>Delete</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Button to toggle modal -->
                        <button type="button" class="btn btn-primary" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop_form"  ref="showModalForm" hidden>
                            Launch static backdrop modal
                        </button>
                        <!-- Modal view -->
                        <div class="modal fade" id="staticBackdrop_form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">ชื่อเวร</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="closeModalForm"></button>
                                    </div>
                                    <form @submit.prevent="save">
                                        <div class="modal-body">
                                                                                        
                                            <!-- Name Input -->
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="name" class="form-label">ชื่อ </label>
                                                    <input type="text" class="form-control" id="name" v-model="formData.name" readonly>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label for="dep" class="form-label">word : </label>
                                                    <input type="file" class="form-control-file" ref="myFiles"  id="WordFile"  @change="validateFile"  accept=".doc,.docx"/>
                                                </div>
                                            </div>
                                                                                            
                                        </div>
                                        
                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <div class="row">
                                                <button type="submit" class="btn btn-primary">บันทึก</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> 
                    </div>
                </section>
                
            </div>
            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>
    <?php require_once('../includes/_footer_sc.php') ?>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/ven_report.js"></script>
</body>

</html>