Vue.createApp({
  data() {
    return {
      dataList: [],
      formData: {},
      action: 'insert',
      isLoading: false,
    };
  },
  mounted() {
    this.fetchVenNames();
  },
  methods: {
    async fetchVenNames() {
      this.isLoading = true;
      try {
        const response = await axios.get('../../server/users/ven_report/ven_names.php');
        this.dataList = response.data.respJSON;
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchVenName(id) {
      this.isLoading = true;
      try {
        const response = await axios.post('../../server/users/ven_report/ven_name.php', { id: id });
        if (response.data.status) {
          this.formData = response.data.responseData;
        }
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    update(id) {
      this.fetchVenName(id);
      this.$refs.showModalForm.click();
      this.action = 'update';
    },
    insert() {
      this.resetFormData();
      this.$refs.showModalForm.click();
      this.action = 'insert';
    },
    resetFormData() {
      this.formData = { id: '', name: '', word: ''};
    },
    validateFile(event) {
      const file = event.target.files[0];
      if (file && !['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'].includes(file.type)) {
        alert('กรุณาเลือกไฟล์ Word เท่านั้น');
        event.target.value = null;
      }
    } ,
    async save() {
      this.isLoading = true;
      try {
        var WordFile = this.$refs.myFiles.files
        if (WordFile.length > 0) {
          if(WordFile[0].type == 'application/msword' || WordFile[0].type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
            var vnid = this.formData.id;
            var formData = new FormData();
            formData.append("sendfile", WordFile[0]);
            formData.append("vnid", vnid);
            axios.post('../../server/users/ven_report/report_save.php', 
              formData, 
              {headers:{'Content-Type': 'multipart/form-data'}
            })
              .then(response => {
                  if (response.data.status) {
                    swal.fire({
                      icon: 'success',
                      title: response.data.message,
                      showConfirmButton: true,
                      timer: 1500
                    });
                    this.fetchVenNames();
                    this.$refs.closeModalForm.click()

                  }else {
                      swal.fire({
                          icon: 'error',
                          title: response.data.message,
                          showConfirmButton: true,
                          timer: 1500
                      });
                  }
              })
          } else{
            swal.fire({
              icon: 'error',
              title: "ไฟล์ที่อัพโหลดต้องเป็นไฟล์ jpeg หรือ png เท่านั้น",
              showConfirmButton: true,
              timer: 1500
            });
          }
        }
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    closeModalForm() {
      this.action = 'insert';
    },
    alert(icon, message, timer = 0) {
      swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: true,
        timer: timer
      });
    },
    async deleteRecord(id) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      });
      
      if (result.isConfirmed) {
        this.isLoading = true;
        try {
          const response = await axios.post('../../server/users/ven_report/report_del.php', { id: id});
          if (response.data.status) {
            this.alert('success', response.data.message);
            this.fetchVenNames();
          } else {
            this.alert('error', response.data.message);
          }
        } catch (error) {
          console.error(error);
        } finally {
          this.isLoading = false;
        }
      }
    },
    async updateStatus(index) {
      const data = this.dataList[index];
      this.isLoading = true;
      try {
        const response = await axios.post('../../server/users/sign_name/sign_name_st_update.php', { data: data });
        this.dataList[index].st = response.data.st;
        this.alert(response.data.status, response.data.message)
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    getRoleName(val) {
      const matchedRole = this.roleList.find(role => role.val === val);
      return matchedRole ? matchedRole.name : val;
    },
    alert(icon,message,timer=1500){
        swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: false,
        position: 'top-end',
        timer: timer,
        toast: true
      });
    },
  },
}).mount('#ven_name_report_app');
