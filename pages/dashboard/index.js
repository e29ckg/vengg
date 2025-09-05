Vue.createApp({
  data() {
    return {
      q: "2254",
      url_base: "",
      url_base_app: "",
      url_base_now: "",
      profile: "",
      datas: [
        {
          id: "a",
          title: "my event",
          start: "2022-09-01",
          extendedProps: {
            uid: 5555,
            uname: "",
            ven_date: "",
            ven_time: "",
            DN: "",
            ven_month: "",
            ven_com_id: "",
            st: "",
          },
        },
      ],
      data_event: {
        uid: 5555,
        uname: "",
        ven_date: "",
        ven_time: "",
        DN: "",
        ven_month: "",
        ven_com_id: "",
        st: "",
      },
      profiles: [],

      ven_coms: [],
      ven_coms_index: "",

      ven_com_id: "",
      ven_month: "",
      ven_com_name: "",
      ven_com_num: "",
      DN: "",
      u_role: "",
      price: "",

      ssid: "",
      my_v: [],
      changeHistory: [],
      d_now: "",
      my_v_show: "false",
      ch_v1: "",
      ch_v2: "",
      users: [],
      u_id2: "",
      u_name2: "",
      u_img2: "",
      act: "a",
      ch_a: false,
      ch_b: false,
      year: 0,
      month: 0,
      usersForChange: [],
      vensForChange: [],
      btnUsersForChange: false,
      btnVensForChange: false,

      isLoading: false,
      isLoading_modal: false,
    };
  },
  mounted() {
    this.url_base = window.location.protocol + "//" + window.location.host;
    this.ven_month = this.handleTodayMonth();
    // this.getProfile()
    this.get_vens();
  },
  watch: {
    q() {
      this.ch_search_pro();
    },
  },
  methods: {
    cal_render() {
      var calendarEl = this.$refs["calendar"];
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        initialDate: this.ven_month,
        height: "auto",
        locale: "th",
        firstDay: 1,
        // allDay      : true,
        eventOrder: "start",
        // allDayDefault : true,
        // allDayContent : true,
        // displayEventTime: false,
        events: this.datas,
        eventColor: "#378006",
        eventClick: (info) => {
          // console.log(info.event.id +' '+info.event.title)
          // console.log(info.event.extendedProps)
          this.cal_click(info.event.id);
        },
        customButtons: {
          customToDayButton: {
            text: "today",
            click: () => {
              this.ven_month = this.handleTodayMonth();
              this.get_vens();
            },
          },
          customPrevButton: {
            text: "<< prev",
            click: () => {
              this.ven_month = this.handlePrevMonth();
              this.get_vens();
            },
          },
          customNextButton: {
            text: "next >>",
            click: () => {
              this.ven_month = this.handleNextMonth();
              this.get_vens();
            },
          },
        },
        headerToolbar: {
          start: "title", // will normally be on the left. if RTL, will be on the right
          center: "",
          end: "customToDayButton customPrevButton customNextButton", // will normally be on the right. if RTL, will be on the left
        },
      });
      calendar.render();
    },
    handleTodayMonth() {
      const currentDate = new Date();
      this.year = currentDate.getFullYear();
      this.month = String(currentDate.getMonth() + 1).padStart(2, "0");
      return `${this.year}-${this.month}`;
    },

    handleNextMonth() {
      let nextMonth = parseInt(this.month) + 1;
      let nextYear = parseInt(this.year);
      if (nextMonth > 12) {
        nextMonth = 1;
        nextYear++;
      }
      this.month = nextMonth;
      this.year = nextYear;
      console.log(`${this.year}-${String(this.month).padStart(2, "0")}`);
      return `${this.year}-${String(this.month).padStart(2, "0")}`;
    },

    handlePrevMonth() {
      let year = parseInt(this.year);
      let month = parseInt(this.month) - 1;

      if (month < 1) {
        month = 12;
        year--;
      }
      this.month = month;
      this.year = year;
      console.log(`${this.year}-${String(this.month).padStart(2, "0")}`);
      return `${this.year}-${String(this.month).padStart(2, "0")}`;
    },

    async cal_click(id) {
      this.isLoading = true;
      await axios
        .post("../../server/dashboard/get_ven.php", { id })
        .then((response) => {
          // console.log(response.data);
          // if (response.data.status) {
          this.data_event = response.data.respJSON;
          this.my_v = [];
          this.changeHistory = response.data.changeHistory;
          this.d_now = response.data.d_now;
          this.users = response.data.users;
          this.btnUsersForChange = response.data.btnUsersForChange;
          this.btnVensForChange = response.data.btnVensForChange;
          this.usersForChange = [];
          this.vensForChange = [];
          this.$refs.show_modal.click();
          // }else{
          //   this.alert('warning',response.data.message ,0)
          // }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
      // this.$refs.show_modal.click()
    },

    getProfile() {
      // this.isLoading = true;
      axios
        .get("../../server/dashboard/get_profile.php")
        .then((response) => {
          if (response.data.status) {
            this.profile = response.data.profile[0];
          } else {
            window.open("../../login.php", "_self");
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    get_vens() {
      this.isLoading = true;
      axios
        .get("../../server/dashboard/get_vens.php?month=" + this.ven_month)
        .then((response) => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
            this.ssid = response.data.ssid;
            this.cal_render();
          } else {
            this.cal_render();
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    change_a(my_v_index) {
      this.act = "a";
      this.ch_v1 = this.my_v[my_v_index];
      this.ch_v2 = this.data_event;
    },

    change_b(uid, u_name, img) {
      console.log(uid);
      console.log(u_name);
      console.log(img);
      this.act = "b";
      this.ch_v1 = this.data_event;
      this.user_id2 = uid;
      this.u_name2 = u_name;
      this.u_img2 = img;
      this.$refs.show_modal_b.click();
    },

    change_save() {
      this.isLoading = true;
      axios
        .post("../../server/dashboard/change_save.php", {
          ch_v1: this.ch_v1,
          ch_v2: this.ch_v2,
        })
        .then((response) => {
          if (response.data.status) {
            this.get_vens();
            this.$refs.close_modal.click();
            this.$refs.close_modal_b.click();
            this.alert("success", response.data.message, 1000);
            window.open("../history/index.php", "_self");
          } else {
            this.alert("warning", response.data.message, 0);
          }
          this.act = "a";
        })
        .catch(function (error) {
          this.alert("warning", error, 0);
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    change_save_bb() {
      this.isLoading = true;
      axios
        .post("../../server/dashboard/change_save_b.php", {
          ch_v1: this.ch_v1,
          user_id2: this.user_id2,
          u_name2: this.u_name2,
        })
        .then((response) => {
          console.log(response.data);
          if (response.data.status) {
            this.get_vens();
            this.$refs.close_modal.click();
            this.$refs.close_modal_b.click();
            this.alert("success", response.data.message, 1000);
            window.open("../history/index.php", "_self");
          } else {
            this.alert("warning", response.data.message, 0);
          }
          // this.act = 'a'
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    admin_ch_a() {
      this.ch_a = true;
      this.ch_b = false;
      this.get_vens2();
    },
    admin_ch_b() {
      this.ch_a = false;
      this.ch_b = true;
      this.get_users();
    },

    get_users() {
      axios
        .post("../../server/dashboard/get_users.php", {
          data_event: this.data_event,
        })
        .then((response) => {
          // console.log(response.data);
          if (response.data.status) {
            this.usersForChange = response.data.users;
            // this.alert('success',response.data.message,1000)
          } else {
            // this.alert('warning',response.data.message,0)
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          // this.isLoading = false;
        });
    },
    get_vens2() {
      // this.isLoading = true;
      axios
        .post("../../server/dashboard/get_vens2.php", {
          data_event: this.data_event,
        })
        .then((response) => {
          // console.log(response.data);
          if (response.data.status) {
            this.my_v = response.data.my_v;
          } else {
            // this.alert('warning',response.data.message,0)
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          // this.isLoading = false;
        });
    },

    close_m() {
      this.ch_a = false;
      this.ch_b = false;
      this.usersForChange = [];
      this.get_vens();
    },
    close_m_b() {
      // this.$refs.close_modal1.click()
      this.ch_a = false;
      this.ch_b = false;
      this.$refs.close_modal1.focus();
    },
    send_user_up(v, u) {
      this.isLoading = true;
      axios
        .post("../../server/dashboard/change_save_b2.php", { v, u })
        .then((response) => {
          if (response.data.status) {
            this.ch_a = false;
            this.ch_b = false;
            this.btnUsersForChange = false;
            this.btnVensForChange = false;
            this.usersForChange = [];
            this.vensForChange = [];
            this.$refs.ven_ch_board.focus();
            this.alert("success", response.data.message, (timer = 1000));

            this.cal_click(response.data.v_id, response.data.uid);
          } else {
            this.alert("error", response.data.message, (timer = 0));
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    send_user_ck(data_event, my_ven) {
      this.act = "a";
      this.ch_v1 = data_event;
      this.ch_v2 = my_ven;

      this.isLoading = true;
      axios
        .post("../../server/dashboard/change_save2.php", {
          ch_v1: this.ch_v1,
          ch_v2: this.ch_v2,
        })
        .then((response) => {
          if (response.data.status) {
            this.ch_a = false;
            this.ch_b = false;
            this.$refs.ven_ch_board.focus();
            this.alert("success", response.data.message, (timer = 1000));
            this.cal_click(response.data.v_id, response.data.uid);
          } else {
            this.alert("error", response.data.message, (timer = 0));
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    print(id) {
      this.isLoading = true;
      axios
        .post("../../server/history/print2.php", { id })
        .then((response) => {
          if (response.data.status) {
            // this.alert("success",response.data.message,timer=1000)
            window.open(response.data.url, "_blank");
          } else {
            this.alert("error", response.data.message, (timer = 0));
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    cancle(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, is it!",
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios
            .post("../../server/history/change_cancle.php", { id: id })
            .then((response) => {
              // console.log(response.data.respJSON);
              if (response.data.status) {
                this.alert("success", response.data.message, (timer = 1000));
                this.$refs.close_modal1.click();
              } else {
                this.alert("error", response.data.message, (timer = 0));
              }
            })
            .catch(function (error) {
              console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            });
        }
      });
    },

    report_jk(ven_date) {
      this.isLoading = true;
      axios
        .post("../../server/dashboard/report_jk.php", { ven_date: ven_date })
        .then((response) => {
          if (response.data.status) {
            this.alert("success", response.data.message, (timer = 1000));
            window.open("../../uploads/ven_jk.docx", "_blank");
          } else {
            this.alert("warning", response.data.message, (timer = 0));
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    report_vn(ven_date, vn_id, vns_id, user_id) {
      this.isLoading = true;
      axios
        .post("../../server/dashboard/report_vn.php", {
          ven_date: ven_date,
          vn_id: vn_id,
          vns_id: vns_id,
          user_id: user_id,
        })
        .then((response) => {
          if (response.data.status == "success") {
            this.alert("success", response.data.message, (timer = 1000));
            window.open(response.data.fileUrl, "_blank");
          } else {
            this.alert("warning", response.data.message, (timer = 0));
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    async getUsersForChange(data_event) {
      await axios
        .post("../../server/dashboard/get_users_for_change.php", {
          data_event: data_event,
        })
        .then((response) => {
          this.usersForChange = response.data.users;
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {}).users;
    },

    async getUsersForChange2(id) {
      await axios
        .post("../../server/dashboard/get_users_for_change2.php", { id: id })
        .then((response) => {
          this.vensForChange = response.data.my_vens;
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {});
    },

    alert(icon, message, timer = 1500) {
      swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: false,
        position: "top-end",
        timer: timer,
        toast: true,
      });
    },
  },
}).mount("#dashboard");
