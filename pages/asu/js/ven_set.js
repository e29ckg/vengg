
Vue.createApp({
  data() {
    return {
      q: '',

      url_base: '',
      url_base_app: '',
      url_base_now: '',
      datas: [
        {
          id: 'a',
          title: 'my event',
          start: '2022-09-01',
          extendedProps: {
            user_id: 5555,
            uname: '',
            ven_date: '',
            ven_time: '',
            DN: '',
            ven_month: '',
            ven_com_id: '',
            st: '',

          }
        }
      ],
      data_event: {
        user_id: 5555,
        uname: '',
        ven_date: '',
        ven_time: '',
        DN: '',
        ven_month: '',
        ven_com_id: [],
        st: '',
      },
      data_event_ven_coms: [],

      months: [],
      ven_month: '',

      profiles: [],
      ven_name_subs: [],
      ven_coms: [],
      vc_index: '',
      vns_index: '',


      /** เตรียมส่ง */
      ven_com: '',
      ven_name_sub: '',

      vn_id: 0,
      vns_id: 0,

      label_message: '<--กรุณาเลือกคำสั่ง',
      isLoading: false,
    }
  },
  mounted() {
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.url_base_app = window.location.protocol + '//' + window.location.host + '/venset/';
    // const d = 
    this.ven_month = new Date();
    this.get_vens()
    this.get_ven_month1()


  },
  watch: {
    q() {
      this.ch_search_pro()
    }
  },
  methods: {
    // get_ven_names(){
    //   axios.post('../../server/asu/ven_set/get_ven_names.php')
    //     .then(response => {
    //       if (response.data.status) {
    //         this.ven_names = response.data.respJSON
    //       }else{
    //         this.alert('warning',response.data.message,0)
    //         this.ven_names = []

    //       }
    //     })
    //     .catch(function (error) {        
    //     console.log(error);
    //   });
    // },
    get_ven_coms() {
      axios.post('../../server/asu/ven_set/get_ven_coms.php', { ven_month: this.ven_month })
        .then(response => {
          if (response.data.status) {
            this.ven_coms = response.data.respJSON;
            // this.alert('success',response.data.message,1000)
          } else {
            this.ven_coms = []
            this.alert('warning', response.data.message, 0)
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    ch_sel_ven_month() {
      this.ven_coms = []
      this.ven_com = ''
      this.ven_name_subs = []
      this.ven_name_sub = ''
      this.vc_index = ''
      this.vns_index = ''
      this.profiles = []
      this.get_ven_coms()
      this.cal_render()

    },

    ch_sel_ven_name(index) {
      this.ven_name_sub = ''

      this.ven_name_subs = []
      this.ven_com = this.ven_coms[index]
      axios.post('../../server/asu/ven_set/get_vns_vs.php', { vn_id: this.ven_com.vn_id })
        .then(response => {
          if (response.data.status) {
            this.ven_name_subs = response.data.respJSON
          } else {
            this.vc_index = ''
            this.ven_name_sub = ''
            this.ven_name_subs = []
            this.alert('warning', response.data.message, 0)
          }
          this.vns_index = ''
          this.profiles = []
        })
        .catch(function (error) {
          console.log(error);
        });

    },

    ch_sel_vns(index) {
      console.log(index)
      this.ven_name_sub = this.ven_name_subs[index]

      axios.post('../../server/asu/ven_set/get_ven_users.php', { vn_id: this.ven_name_sub.vn_id, vns_id: this.ven_name_sub.vns_id })
        .then(response => {
          if (response.data.status) {
            this.profiles = response.data.respJSON
          } else {
            this.vns_index = ''
            this.ven_name_sub = ''
            this.profiles = []
            this.alert('warning', response.data.message, 0)

          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    cal_render() {
      var calendarEl = this.$refs['calendar'];
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: this.ven_month,
        firstDay: 1,
        height  : 2200,
        locale: 'th',
        events: this.datas,
        eventClick: (info) => {
          // console.log(info.event.id +' '+info.event.title)
          // console.log(info.event.extendedProps)
          this.cal_click(info.event.id)
        },
        dateClick: (date) => {
          // console.log(date)
          // this.cal_modal.date_meet = date.dateStr
          this.$refs['modal-show'].click();
        },
        editable: true,
        eventDrop: (info) => {
          // console.log(info.event)
          if (!this.event_drop(info.event.id, info.event.start)) {
            info.revert();
          }
        },
        droppable: true,
        drop: (info) => {
          // console.log(info.draggedEl.dataset.uid)              
          this.drop_insert(info.draggedEl.dataset.uid, info.dateStr)

        },
        eventRender: function (info) {
          if (info.event.allDay) {
            info.el.classList.add('fc-daygrid-block-event');
          }
        }
      });
      calendar.render();
    },

    cal_click(id) {
      axios.post('../../server/asu/ven_set/get_ven.php', { id: id })
        .then(response => {
          if (response.data.status) {
            this.data_event = response.data.respJSON
            this.data_event_ven_coms = response.data.ven_coms
            // this.data_event.ven_com_id = JSON.parse(response.data.respJSON.ven_com_id)
            this.$refs['show_modal'].click()
            // this.ven_month = response.data.respJSON.ven_month
            // this.get_ven_coms()

          } else {
            let icon = 'warning'
            let message = response.data.message
            this.alert(icon, message, 0)

          }
        })
        .catch(function (error) {
          console.log(error);

        });
    },

    drop_insert(uid, dateStr) {
      console.log(uid)
      axios.post('../../server/asu/ven_set/ven_insert.php', {
        uid: uid,
        ven_date: dateStr,
        ven_month: this.ven_month,
        vc_id: this.ven_com.vc_id,
        vn_id: this.ven_name_sub.vn_id,
        vns_id: this.ven_name_sub.vns_id,
        DN: this.ven_com.DN,
        ven_name: this.ven_com.name,
        ven_com_num: this.ven_com.ven_com_num,
        u_role: this.ven_name_sub.name,
        price: this.ven_name_sub.price,
        color: this.ven_name_sub.color,
        vn_srt: this.ven_name_sub.vn_srt,
        vns_srt: this.ven_name_sub.vns_srt,
        ven_com: this.ven_com,
        ven_name_sub: this.ven_name_sub,
        act: 'insert'
      })
        .then(response => {
          // console.log(response.data);
          if (response.data.status) {

            this.alert('success', response.data.message, 1000)
          } else {
            this.alert('warning', response.data.message, 0)
          }
          this.get_vens()
          // this.cal_render()
        })
        .catch(function (error) {
          console.log(error);

        });


    },
    event_drop(id, start) {
      axios.post('../../server/asu/ven_set/ven_move.php', { id: id, start: start })
        .then(response => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
            this.get_vens()            
            this.alert('success', response.data.message, 1000)
            return true
          } else {
            this.alert('warning', response.data.message, 0)
            return false
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    async get_vens() {
      await axios.get('../../server/asu/ven_set/get_vens.php')
        .then(response => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
            this.cal_render()
            this.$refs['calendar'].focus()
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    ven_save() {
      axios.post('../../server/asu/ven_set/ven_up_vcid.php', { data_event: this.data_event })
        .then(response => {

          if (response.data.status) {
            this.get_vens()
            this.cal_click(this.data_event.id)
            this.alert('success',response.data.message,1000)
            // this.$refs['close_modal'].click()
          } else {
            this.alert('warning', response.data.message, 0)
          }
          this.cal_render()
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    ven_save2() {
      axios.post('../../server/asu/ven_set/ven_up_vcid2.php', { data_event: this.data_event })
        .then(response => {

          if (response.data.status) {
            this.get_vens()
            this.cal_click(this.data_event.id)
            this.alert('success', response.data.message, 1000)
            // this.$refs['close_modal'].click()
          } else {
            // this.alert('warning', response.data.message, 0)
          }
          this.cal_render()
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    ven_del(id) {
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
          this.isLoading = true
          axios.post('../../server/asu/ven_set/ven_del.php', { id: id })
            .then(response => {

              if (response.data.status) {
                icon = "success";
                message = response.data.message;
                this.alert(icon, message, 1000)
                this.$refs['close_modal'].click()
              } else {
                icon = "warning";
                message = response.data.message;
                this.alert(icon, message)
              }
              this.get_vens()
            })
            .catch(function (error) {
              console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            });
        }
      })

    },
    ven_dis_open(id) {
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
          this.isLoading = true
          axios.post('../../server/asu/ven_set/ven_dis_open.php', { id: id })
            .then(response => {

              if (response.data.status) {
                icon = "success";
                message = response.data.message;
                this.alert(icon, message, 1000)
                this.$refs['close_modal'].click()
                this.get_vens()
              } else {
                icon = "warning";
                message = response.data.message;
                this.alert(icon, message)
              }
            })
            .catch(function (error) {
              console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            });
        }
      })

    },

    close_m() {
      this.get_vens()
    },
    test(){
      this.cal_render()
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

    reset_search() {
      this.q = ''
    },
    get_ven_month1() {
      let m = new Date();
      let y = m.getFullYear().toString()
      console.log(y)
      for (let i = -1; i < 10; i++) {
        const d = new Date(y, m.getMonth() + i);
        this.months.push({ 'ven_month': this.convertToYearMonthNum(d), 'name': this.convertToDateThai(d) })
      }
    },
    convertToYearMonthNum(date) {
      var months_num = ["", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
      return result = date.getFullYear() + "-" + (months_num[(date.getMonth() + 1)]);
    },
    convertToDateThai(date) {
      var month_th = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
      return result = month_th[(date.getMonth() + 1)] + " " + (date.getFullYear() + 543);
    }
  }
}).mount('#venSet')
