Vue.createApp({
  data() {
    return {
      q:'2254',
      ven_coms_g :'',
      ven_coms :'',
      vc_form  :{
        ven_com_num :'',
        ven_com_date :'',
        ven_month :'',
        ven_com_name :'',
        ven_name : ''
      },
      vc_form_act :'insert',
      sel_ven_month :[],
      ven_names :'',

      isLoading : false,
    }
  },
  mounted(){
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/adminphp/';
    // const d = 
    this.get_ven_coms()
    this.get_ven_month()
    this.get_ven_names()
    // this.get_ven_users()
    // this.get_users()
  },
  watch: {
    
  },
  methods: {
    // this.$refs.show_vc_form.click()
    // this.$refs.close_vc.click()
    ven_com_add(){
      // console.log('ven_com_Add')
      
      this.vc_form_act = 'insert'
      this.$refs.show_vc_form.click()
    },
    ven_com_del(){
      console.log('ven_com_del')
    },
    clear_vc_form(){
      console.log('clear_vc_form')
      this.vc_form  = {ven_com_num :'', ven_com_date :'', ven_month :'', ven_com_name:'', ven_name:''}
    },
    vc_save(){
      if(this.vc_form.ven_com_num != '' && this.vc_form.ven_com_date != '' && this.vc_form.ven_month != '' && this.vc_form.ven_name != ''){
        this.isLoading = true
        axios.post('../../server/asu/ven_com_act.php',{vc:this.vc_form, act:this.vc_form_act})
        .then(response => {
            if (response.data.status) {
              this.$refs.close_vc.click()
              this.get_ven_coms()
              this.alert('success',response.data.message,1500)
            }else{
              this.alert('warning',response.data.message,0)
            }
        })
        .catch(function (error) {
            console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        })
      }else{
        const message = []
        if(this.vc_form.ven_com_num == ''){message.push('เลขคำสั่ง')}
        if(this.vc_form.ven_com_date == ''){message.push('ลงวันที่')}
        if(this.vc_form.ven_month == ''){message.push('เวรเดือน')}
        if(this.vc_form.ven_name == ''){message.push('ชื่อเวร')}
        this.alert('warning',message,0)
      }
    },
    ven_com_up(id){
      this.vc_form_act = 'update'
      this.get_ven_com(id)
      this.$refs.show_vc_form.click()
    },
    ven_com_del(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/ven_com_act.php',{id:id, act:'delete'})
            .then(response => {
                if (response.data.status) { 
                  this.$refs.close_vc.click()
                  this.get_ven_coms()
                  this.alert('success',response.data.message,1500)
                }else{
                  this.alert('warning',response.data.message,0)
                } 
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            })
        }
      })
    },
    vc_status(id,st){
      this.isLoading = true;
      axios.post('../../server/asu/ven_com_act.php',{id:id, act:'status', st:st})
            .then(response => {
                if (response.data.status) { 
                  this.get_ven_coms()
                  this.alert('success',response.data.message,1500)
                }else{
                  this.alert('warning',response.data.message,0)
                } 
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            })
    },  


    get_ven_coms(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_coms.php')
      .then(response => {
          if (response.data.status) {
              this.ven_coms = response.data.respJSON;
              this.ven_coms_g = response.data.respJSON_G;

          }else{
            this.ven_coms   = []
            this.ven_coms_g = []
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_ven_names(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_names.php')
      .then(response => {
          if (response.data.status) {
              this.ven_names = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_ven_com(id){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_com.php',{id:id})
      .then(response => {
          if (response.data.status) {
              this.vc_form = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_ven_month(){
      let   m = new Date();
      let y = m.getFullYear().toString()
      for (let i = 0; i < 5; i++) {  
        const d = new Date(y,m.getMonth()+i);
        this.sel_ven_month.push({'ven_month':this.convertToYearMonthNum(d),'name': this.convertToDateThai(d)})
      }
    },
    convertToYearMonthNum(date) {
      var months_num = ["","01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
      return result   = date.getFullYear() + "-" + (months_num[( date.getMonth()+1 )]);
    },
    convertToDateThai(date) {
      var month_th = ["","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม"];
      return result = month_th[( date.getMonth()+1 )]+" "+( date.getFullYear()+543 );
    },






    get_ven_name_subs(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_name_subs.php')
      .then(response => {
          if (response.data.status) {
              this.ven_name_subs = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_ven_users(){
      this.isLoading = true
      axios.post('../../server/asu/get_ven_users.php')
      .then(response => {
          if (response.data.status) {
              this.ven_users = response.data.respJSON;
          } 
      })
      .catch(function (error) {
          console.log(error);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    get_users(){
      axios.post('../../server/users/users.php')
      .then(response => {
          if (response.data.status) {
              this.users = response.data.respJSON;
          } 
      })
    },
    vu_add(vni,vnsi){
      this.$refs.show_vu_form.click()
      this.vu_form.ven_name  = this.ven_names[vni].name
      this.vu_form.uvn    = this.ven_name_subs[vnsi].name
      this.vu_form.DN     = this.ven_names[vni].DN
      this.vu_form.v_time = this.DN[this.ven_names[vni].DN] +':'+this.ven_names[vni].srt + this.ven_name_subs[vnsi].srt
      this.vu_form.price  = this.ven_name_subs[vnsi].price
      this.vu_form.color  = this.ven_name_subs[vnsi].color
      
      
    },
    clear_vu_form(){
      this.vu_form = {user_id :'',order : '',DN : '',price : '',ven_name : '',uvn : '',v_time : '',color : ''}
    },

    vu_save(){
      if(this.vu_form.user_id != '' && this.vu_form.order != ''){
        this.isLoading = true;
        axios.post('../../server/asu/user_ven_act.php',{ven_user:this.vu_form, act:this.vu_form_act})
        .then(response => {
            if (response.data.status) {            
              this.$refs.close_vu.click()
              this.get_ven_names()
              this.get_ven_name_subs()
              this.get_ven_users()
              this.get_users()
              this.alert('success',response.data.message,1500)
                // this.ven_name_subs = response.data.respJSON;
            } 
        })
        .catch(function (error) {
            console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        })
      }else{
        const message = []
        if(this.vu_form.user_id == ''){message.push('กรุณาเลือกชื่อ')}
        if(this.vu_form.order == ''){message.push('กรุณากรอกลำดับที่')}
        this.alert('warning',message,0)
      }
    },
    vu_del(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('../../server/asu/user_ven_act.php',{id:id, act:'delete'})
            .then(response => {
                if (response.data.status) {  
                  this.get_ven_names()
                  this.get_ven_name_subs()
                  this.get_ven_users()
                  this.get_users()
                  this.alert('success',response.data.message,1500)
                }else{
                  this.alert('warning',response.data.message,0)
                } 
            })
            .catch(function (error) {
                console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            })
        }
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


    



    
  },
  
        

}).mount('#venCom')
