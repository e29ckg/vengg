<?php 
  
  session_start(); // ประกาศ การใช้งาน session
  session_destroy(); // ลบตัวแปร session ทั้งหมด
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
  <title>Login Page | App</title>
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/uploads/icon.ico">
  <!-- stylesheet -->
  <!-- <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" > -->
  <!-- <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" > -->
  <!-- <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" > -->
  <!-- <link rel="stylesheet" href="assets/css/demo.css" > -->
  <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" >
  <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300&display=swap" rel="stylesheet">
  <style>
    .divider:after,
    .divider:before {
      content: "";
      flex: 1;
      height: 1px;
      background: #eee;
    }
    .h-custom {
      height: calc(100% - 73px);
    }
    @media (max-width: 450px) {
      .h-custom {
        height: 100%;
      }
    }
   
    body{
      font-family: 'Mitr', sans-serif;
		}
    .form-label {
      font-size: smaller;
    }
    [v-cloak] > * { display:none; }
    [v-cloak]::before { content: "loading..."; }

    script {
        display: none;
    }
  </style>
</head>
<body>
  <div class="container-xxl" id="applogin" v-cloak>    
      <section class="vh-100">
      <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-md-9 col-lg-6 col-xl-5 d-none d-lg-block">
            <img src="./assets/images/logo/login-logo.png"
              class="img-fluid" alt="Sample image">
          </div>
          <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
            <form  @submit.prevent="login()">         
              <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start mb-5 ">
                <p class="lead fw-normal  mb-0 me-3">โปรแกรมบริหารจัดการเวรนอกเวลาราชการ</p>
              </div>
              <div class="divider d-flex align-items-center my-4">                
                <p class="text-center fw-bold mx-3 mb-0" v-if="!admin_login">
                  บุคคลทั่วไป
                </p>
                <p class="text-center fw-bold mx-3 mb-0" v-if="admin_login">
                  Admin
                </p>
              </div>
              <!-- Email input -->
              <div class="form-outline mb-4" v-if="!admin_login">                
                  <input id="form3Example3"  type="text" class="form-control form-control-lg" placeholder="Enter your phone number" autofocus v-model="phoneNumber">
                <label class="form-label" for="form3Example3">PHONE NUMBER</label>
              </div>

              <!-- Admin login -->
              <div class="form-outline mb-4" v-if="admin_login">                
                  <input id="form3Example3"  type="text" class="form-control form-control-lg" placeholder="username" autofocus v-model="user">
                <label class="form-label" for="form3Example3">Username</label>
              </div>
              <!-- Password input -->
              <div class="form-outline mb-3" v-if="admin_login">
                  <input
                        :type="pass_type"
                        id="form3Example4"
                        class="form-control form-control-lg"
                        name="password"
                        v-model="pass"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      >
                      <!-- <span @click="click_hide()" id="show_pass" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span> -->
                <label class="form-label" for="form3Example4">Password</label>
              </div>

             
              <div class="text-center text-lg-start mt-4 pt-2">
                <!-- <button type="button" class="btn btn-primary btn-lg"
                  style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button> -->
                  <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100 mute" type="submit" name="authen" :disabled="isLoading"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">
                            {{ !isLoading ? 'Sign in':'Loading...'}}
                    </button>
                    <!-- <button class="btn btn-primary d-grid w-100 mute" type="submit" name="authen" disabled v-else>Loading...</button> -->
                  </div>
                <p class="small fw-bold mt-2 pt-1 mb-0" >
                  <!-- สำหรับ -->
                  <a href="#!" class="link-danger" v-if="!admin_login" @click="admin_login = true">สำหรับ Admin</a>
                  <a href="#!" class="link-danger" v-if="admin_login" @click="admin_login = false">สำหรับบุคคลทั่วไป</a>
                </p>
              </div>

            </form>
          </div>
        </div>
      </div>
      
    </section>



    
  </div>
  <!-- script -->
  <!-- <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/popper/popper.js"></script> -->
  <script src="assets/vendor/js/bootstrap.js"></script>
  <script src="./node_modules/vue/dist/vue.global.js"></script>
  <script src="./node_modules/vue/dist/vue.global.prod.js"></script>
  <script src="./node_modules/axios/dist/axios.js"></script>
  <script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
  <script>
    Vue.createApp({
    data() {
      return {
        user:'',        
        pass:'',        
        phoneNumber:'',        
        isLoading: false,
        pass_type:'password',
        admin_login: false
      }
    },
    mounted(){     
      localStorage.removeItem("ss_uid")      
    },
    watch: {
      
    },
    methods: {
      click_hide(){
        if(this.pass_type == 'password'){
          this.pass_type = 'text'
        }else{
          this.pass_type = 'password'
        }
      }, 
      login(){
        this.isLoading = true;
        axios.post('./server/auth/login.php',{
          username:this.user,
          password:this.pass,
          phoneNumber:this.phoneNumber,
          admin_login:this.admin_login
        })
        .then(response => {
          if (response.data.status) {
            localStorage.setItem("ss_uid",response.data.ss_uid);
            Swal.fire({
              // position: 'top-end',
              icon: 'success',
              title: 'เข้าสู่ระบบ',
              showConfirmButton: false,
              timer: 3000
            })
            setTimeout(()=> {
							var url_success = "pages/dashboard";
              window.open(url_success,'_self')
						}, 1001);
            
          } else{
            var icon    = 'warning'
            var message = response.data.message                
            this.alert(icon,message,0)

          }
        })
        .catch(function (error) {        
          console.log(error);
        })
        .finally(() => {
            this.isLoading = false;
        })
      },     
      alert(icon,message,timer=0){
        swal.fire({
          icon: icon,
          title: message,
          showConfirmButton: true,
          timer: timer
        });
      },
          
    }
  }).mount('#applogin')
  
  
  </script>
</body>
</html>
