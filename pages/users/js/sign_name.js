Vue.createApp({
  data() {
    return {
      dataList: [],
      formData: {
        name: '',
        dep: '',
        dep2: '',
        dep3: '',
        role: '',
        st: '',
      },
      roleList: [{name:'ผู้พิพากษาหัวหน้าศาล', val:'Chief_Judge'}, {name:'ผู้อำนวยการ', val:'Director'}, {name:'การเงิน', val:'Finance'}, {name:'ชื่อศาล',val:'Court_Name'}],
      stList: [{ name: 'Active', val: true }, { name: 'Inactive', val: false }],
      action: 'insert',
      isLoading: false,
    };
  },
  mounted() {
    this.fetchSignNames();
  },
  methods: {
    async fetchSignNames() {
      this.isLoading = true;
      try {
        const response = await axios.get('../../server/users/sign_name/sign_names.php');
        this.dataList = response.data.respJSON;
      } catch (error) {
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchSignName(id) {
      this.isLoading = true;
      try {
        const response = await axios.post('../../server/users/sign_name/sign_name.php', { id: id });
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
      this.fetchSignName(id);
      this.$refs.showModalForm.click();
      this.action = 'update';
    },
    insert() {
      this.resetFormData();
      this.$refs.showModalForm.click();
      this.action = 'insert';
    },
    resetFormData() {
      this.formData = {
        name: '',
        dep: '',
        dep2: '',
        dep3: '',
        role: '',
        st: '',
      };
    },
    async save() {
      const messages = [];
      if (!this.formData.name) messages.push('Name');
      if (!this.formData.role) messages.push('Role');
      if (this.formData.st == '') messages.push('Status');
      
      if (messages.length > 0) {
        this.alert('warning', messages.join(', ') + ' cannot be empty');
        return;
      }

      this.isLoading = true;
      try {
        const response = await axios.post('../../server/users/sign_name/sign_name_save.php', { form: this.formData, act: this.action });
        if (response.data.status) {
          this.alert('success', response.data.message);
          this.$refs.closeModalForm.click();
          this.fetchSignNames();
        } else {
          this.alert('warning', response.data.message);
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
          const response = await axios.post('../../server/users/sign_name/sign_name_save.php', { id: id, act: 'del' });
          if (response.data.status) {
            this.alert('success', response.data.message);
            this.fetchSignNames();
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
}).mount('#signName');
