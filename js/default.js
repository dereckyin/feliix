var app = new Vue({
  el: "#app",
  data: {
    submit: false,
    sn: 0,
    sn1: 0,
    sn2: 0,
    agenda: [],
    agenda1: [],
    agenda2: [],

    position: [],
    title: [],
    department: "",
    title_id: 0,

    // data
    type: 0,
    version: "",
    category: "",
    criterion: "",

    org_category: "",
    org_criterion: "",
    org_id: 0,
    org_type: 0,

    editing: false,

    // paging
    page: 1,
    //perPage: 5,
    pg:0,
    pages: [],
    pages_10: [],

    perPage: 10,

    receive_records: [],
    view_detail: false,
    record: {},
    proof_id: 0,

    // editing
    e_title: [],

    e_sn: 0,
    e_sn1: 0,
    e_sn2: 0,

    e_org_category: "",
    e_org_criterion: "",
    e_org_id: 0,
    e_org_type: 0,

    e_type: 0,
    e_version: "",
    e_category: "",
    e_criterion: "",

    e_department: "",

    e_tid: 0,

    e_editing: false,

    // search
    keyword: "",
    fil_status: "",
    fil_votes: "",


    // attributes
    topic: "",
    start_date: "",
    end_date: "",
    access: [],
    rule: "",
    display: "",
    sort: "",

    block: { 
      url: "",
      photo: "",
     },

    id:0,

    // detail attributes
    title: "",
    pic: "",
    url: "",
    link: "",
    description: "",

    blocks:[],

    details: [],

    fileArray: [],
    edit_fileArray: {},

    item:[],

    vote_rule:"",
    vote_sort:"",
    voting_time_start:"",
    voting_time_end:"",

  },

  created() {

    this.getLeaveCredit();
  },

  computed: {
 
  },

  mounted() {
    
  },

  watch: {

  },

  methods: {
  

    filter_apply: function() {
        let _this = this;
  
        window.location.href =
          "voting_system?" +
          "kw=" +
          _this.keyword +
          "&st=" +
          _this.fil_status +
          "&vt=" +
          _this.fil_votes +
          "&pg=" +
          _this.page;
      },

    getLeaveCredit: function() {
      let _this = this;

      const params = {
        kw: _this.keyword,
        st: _this.fil_status,
        vt: _this.fil_votes,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/voting_system", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if (_this.receive_records.length > 0) {
            //_this.proof_id = _this.receive_records[0].id;
            //_this.detail();
            
            // filter record that has been voted
            _this.receive_records = _this.receive_records.filter(function (el) {
                return (el.vote_status == 'Ongoing' && el.review == 0);
            });



            if(_this.receive_records.length > 0)
            {
                $("#vote_modal").toggle();
                $(".mask").toggle();
            }
            
            
          }
        })
        .catch(function(error) {
          console.log(error);
        });

      _this.proof_id = 0;
    },

    view_detai:function(){
        if (this.proof_id == 0) {
            Swal.fire({
                text: "Please select row to view",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }else
          {
            window.jQuery(".mask").toggle();
            window.jQuery('#Modal_2').toggle();
          }
        
    },

    edit_detai:function(){
        if (this.proof_id == 0) {
            Swal.fire({
                text: "Please select row to edit",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }
          
          if(this.record.cited != 0)
          {
            Swal.fire({
              text: "This template has been used by someone's evaluation, so it cannot be edited. Instead, user might try to duplicate it and do editing on the duplicated template.",
              icon: "warning",
              confirmButtonText: "OK",
            });
            return;
          };

          
          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_3').toggle();
          
        
    },

    detail: function() {
      let _this = this;

      if (this.proof_id == 0) {
        //this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

    },

    create_template() {
      if (this.title_id == 0 || this.version.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("title_id", this.title_id);
      form_Data.append("version", this.version);

      form_Data.append("agenda", JSON.stringify(this.agenda));
      form_Data.append("agenda1", JSON.stringify(this.agenda1));
      form_Data.append("agenda2", JSON.stringify(this.agenda2));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/performance_template_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        });

        window.jQuery(".mask").toggle();
        window.jQuery('#Modal_1').toggle();
    },

    duplicate() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to duplicate",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("title_id", this.record.tid);
      form_Data.append("version", this.record.version);

      form_Data.append("agenda", JSON.stringify(this.record.agenda));
      form_Data.append("agenda1", JSON.stringify(this.record.agenda1));
      form_Data.append("agenda2", JSON.stringify(this.record.agenda2));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/performance_template_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        });
    },

    remove() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to delete",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.record.cited != 0)
      {
        Swal.fire({
          text: "This template has been used for someone's evaluation, so cannot be deleted.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      };

      let _this = this;

      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          if (_this.submit == true) return;

          _this.submit = true;

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("pid", _this.record.id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/performance_template_delete",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.reset();
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.reset();
            });
        } else {
          return;
        }
      });
    },

    update_template() {
      if (this.e_tid == 0 || this.record.version.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("pid", this.record.id);
      form_Data.append("title_id", this.e_tid);
      form_Data.append("version", this.record.version);

      form_Data.append("agenda", JSON.stringify(this.record.agenda));
      form_Data.append("agenda1", JSON.stringify(this.record.agenda1));
      form_Data.append("agenda2", JSON.stringify(this.record.agenda2));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/performance_template_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        });

        window.jQuery(".mask").toggle();
        window.jQuery('#Modal_3').toggle();
        
    },

    add_criterion: function() {
      if (
        this.type == 0 ||
        this.category.trim() == "" ||
        this.criterion.trim() == ""
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.type == 1) {
        var ad = {
          id: ++this.sn,
          category: this.category,
          criterion: this.criterion,
        };
        this.agenda.push(ad);
      }

      if (this.type == 2) {
        var ad = {
          id: ++this.sn1,
          category: this.category,
          criterion: this.criterion,
        };
        this.agenda1.push(ad);
      }

      if (this.type == 3) {
        var ad = {
          id: ++this.sn2,
          category: this.category,
          criterion: this.criterion,
        };
        this.agenda2.push(ad);
      }

      this.criterion = "";
    },

    clear_edit: function() {
      this.org_type = 0;
      this.org_id = 0;
      this.org_category = "";
      this.org_criterion = "";
      this.editing = false;
    },

    cancel_criterion: function() {
      this.type = this.org_type;
      this.category = "";
      this.criterion = "";

      this.clear_edit();
    },

    update_criterion: function() {
      if (this.category.trim() == "" || this.criterion.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.org_type == 1) {
        var element = this.agenda.find(({ id }) => id === this.org_id);
        element.category = this.category;
        element.criterion = this.criterion;
      }

      if (this.org_type == 2) {
        var element = this.agenda1.find(({ id }) => id === this.org_id);
        element.category = this.category;
        element.criterion = this.criterion;
      }

      if (this.org_type == 3) {
        var element = this.agenda2.find(({ id }) => id === this.org_id);
        element.category = this.category;
        element.criterion = this.criterion;
      }

      this.criterion = "";

      this.clear_edit();
    },

    get_positions: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/position_get", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.position = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    set_agenda: function() {
      this.agenda = [];
      this.agenda1 = [];
      this.agenda2 = [];
    },

    set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.agenda.find(({ id }) => id === eid);
      this.agenda.splice(fromIndex, 1);
      this.agenda.splice(toIndex, 0, element);
    },

    set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.agenda.length - 1) toIndex = this.agenda.length - 1;

      var element = this.agenda.find(({ id }) => id === eid);
      this.agenda.splice(fromIndex, 1);
      this.agenda.splice(toIndex, 0, element);
    },

    edit: function(eid) {
      this.scrollMeTo('addto');
      this.type = 1;
      var element = this.agenda.find(({ id }) => id === eid);

      this.org_id = eid;
      this.org_category = element.category;
      this.org_criterion = element.criterion;

      this.category = element.category;
      this.criterion = element.criterion;

      this.org_type = 1;

      this.editing = true;
    },

    del: function(eid) {
      var index = this.agenda.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.agenda.splice(index, 1);
      }
    },

    set_up1: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.agenda1.find(({ id }) => id === eid);
      this.agenda1.splice(fromIndex, 1);
      this.agenda1.splice(toIndex, 0, element);
    },

    set_down1: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.agenda1.length - 1) toIndex = this.agenda1.length - 1;

      var element = this.agenda1.find(({ id }) => id === eid);
      this.agenda1.splice(fromIndex, 1);
      this.agenda1.splice(toIndex, 0, element);
    },

    edit1: function(eid) {
      this.scrollMeTo('addto');
      this.type = 2;
      var element = this.agenda1.find(({ id }) => id === eid);

      this.org_id = eid;
      this.org_category = element.category;
      this.org_criterion = element.criterion;

      this.category = element.category;
      this.criterion = element.criterion;

      this.org_type = 2;
      
      this.editing = true;
    },

    del1: function(eid) {
      var index = this.agenda1.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.agenda1.splice(index, 1);
      }
    },

    set_up2: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.agenda2.find(({ id }) => id === eid);
      this.agenda2.splice(fromIndex, 1);
      this.agenda2.splice(toIndex, 0, element);
    },

    set_down2: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.agenda2.length - 1) toIndex = this.agenda2.length - 1;

      var element = this.agenda2.find(({ id }) => id === eid);
      this.agenda2.splice(fromIndex, 1);
      this.agenda2.splice(toIndex, 0, element);
    },

    edit2: function(eid) {
      this.scrollMeTo('addto');
      this.type = 3;
      var element = this.agenda2.find(({ id }) => id === eid);

      this.org_id = eid;
      this.org_category = element.category;
      this.org_criterion = element.criterion;

      this.category = element.category;
      this.criterion = element.criterion;

      this.org_type = 3;
      
      this.editing = true;
    },

    del2: function(eid) {
      var index = this.agenda2.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.agenda2.splice(index, 1);
      }
    },

    format_url: function(url) {
      var pattern = /^((http|https|ftp):\/\/)/;

      if(!pattern.test(url)) {
          url = "https://" + url;
      }
      return url.replace(/\\/g, "/");
    },

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },

    view_vote:function(){
      if (this.proof_id == 0) {
          Swal.fire({
              text: "Please select row to vote",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }

        if(this.record.vote_status != 'Ongoing'){
          Swal.fire({
            text: "Voting is already finished",
            icon: "warning",
            confirmButtonText: "OK",
          });
        return;
        }
        
          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_4').toggle();
        
      
  },

    result:function(){
      if (this.proof_id == 0) {
          Swal.fire({
              text: "Please select row to vote",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }
        
        if(this.record.vote_status == 'Ongoing'){
          Swal.fire({
            text: "Only when voting is finished, user is allowed to view result.",
            icon: "warning",
            confirmButtonText: "OK",
          });
        return;
        }

          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_5').toggle();
        
      
  },

    reset: function() {
      this.submit = false;

      this.agenda = [];
      this.agenda1 = [];
      this.agenda2 = [];

      this.sn = 0;
      this.sn1 = 0;
      this.sn2 = 0;

      this.type = 0;
      this.version = "";
      this.category = "";
      this.criterion = "";

      this.org_category = "";
      this.org_criterion = "";
      this.org_id = 0;
      this.org_type = 0;

      this.editing = false;
      this.submit = false;

      this.e_type = 0;
      this.e_category = "";
      this.e_criterion = "";

      this.getLeaveCredit();
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    // editing

    e_add_criterion: function() {
      if (
        this.e_type == 0 ||
        this.e_category.trim() == "" ||
        this.e_criterion.trim() == ""
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.e_type == 1) {
        var ad = {
          id: ++this.e_sn,
          category: this.e_category,
          criterion: this.e_criterion,
        };
        this.record.agenda.push(ad);
      }

      if (this.e_type == 2) {
        var ad = {
          id: ++this.e_sn1,
          category: this.e_category,
          criterion: this.e_criterion,
        };
        this.record.agenda1.push(ad);
      }

      if (this.e_type == 3) {
        var ad = {
          id: ++this.e_sn2,
          category: this.e_category,
          criterion: this.e_criterion,
        };
        this.record.agenda2.push(ad);
      }

      this.e_criterion = "";
    },

    e_cancel_criterion: function() {
      this.e_type = this.e_org_type;
      this.e_category = "";
      this.e_criterion = "";

      this.e_clear_edit();
    },

    e_update_criterion: function() {
      if (this.e_category.trim() == "" || this.e_criterion.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.e_org_type == 1) {
        var element = this.record.agenda.find(({ id }) => id === this.e_org_id);
        element.category = this.e_category;
        element.criterion = this.e_criterion;
      }

      if (this.e_org_type == 2) {
        var element = this.record.agenda1.find(
          ({ id }) => id === this.e_org_id
        );
        element.category = this.e_category;
        element.criterion = this.e_criterion;
      }

      if (this.e_org_type == 3) {
        var element = this.record.agenda2.find(
          ({ id }) => id === this.e_org_id
        );
        element.category = this.e_category;
        element.criterion = this.e_criterion;
      }

      this.e_criterion = "";

      this.e_clear_edit();
    },

    e_clear_edit: function() {
      this.e_org_type = 0;
      this.e_org_id = 0;
      this.e_org_category = "";
      this.e_org_criterion = "";

      this.e_type = 0;
      this.e_category = "";
      this.e_criterion = "";

      this.e_editing = false;
    },

    e_set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.agenda.find(({ id }) => id === eid);
      this.record.agenda.splice(fromIndex, 1);
      this.record.agenda.splice(toIndex, 0, element);
    },

    e_set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.agenda.length - 1)
        toIndex = this.record.agenda.length - 1;

      var element = this.record.agenda.find(({ id }) => id === eid);
      this.record.agenda.splice(fromIndex, 1);
      this.record.agenda.splice(toIndex, 0, element);
    },

    e_edit: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.agenda.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_category = element.category;
      this.e_org_criterion = element.criterion;

      this.e_category = element.category;
      this.e_criterion = element.criterion;

      this.e_org_type = 1;
      this.e_type = 1;

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.record.agenda.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.agenda.splice(index, 1);
      }
    },

    e_set_up1: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.agenda1.find(({ id }) => id === eid);
      this.record.agenda1.splice(fromIndex, 1);
      this.record.agenda1.splice(toIndex, 0, element);
    },

    e_set_down1: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.agenda1.length - 1)
        toIndex = this.record.agenda1.length - 1;

      var element = this.record.agenda1.find(({ id }) => id === eid);
      this.record.agenda1.splice(fromIndex, 1);
      this.record.agenda1.splice(toIndex, 0, element);
    },

    e_edit1: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.agenda1.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_category = element.category;
      this.e_org_criterion = element.criterion;

      this.e_category = element.category;
      this.e_criterion = element.criterion;

      this.e_org_type = 2;
      this.e_type = 2;

      this.e_editing = true;
    },

    e_del1: function(eid) {
      var index = this.record.agenda1.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.agenda1.splice(index, 1);
      }
    },


    e_set_up2: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.agenda2.find(({ id }) => id === eid);
      this.record.agenda2.splice(fromIndex, 1);
      this.record.agenda2.splice(toIndex, 0, element);
    },

    e_set_down2: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.agenda2.length - 1)
        toIndex = this.record.agenda2.length - 1;

      var element = this.record.agenda2.find(({ id }) => id === eid);
      this.record.agenda2.splice(fromIndex, 1);
      this.record.agenda2.splice(toIndex, 0, element);
    },

    e_edit2: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.agenda2.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_category = element.category;
      this.e_org_criterion = element.criterion;

      this.e_category = element.category;
      this.e_criterion = element.criterion;

      this.e_org_type = 3;
      this.e_type = 3;

      this.e_editing = true;
    },

    e_del2: function(eid) {
      var index = this.record.agenda2.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.agenda2.splice(index, 1);
      }
    },

    vote: function () {
      var max_votes = this.record.rule;

      // get details checked cnt
      var cnt = 0;
      for (var i = 0; i < this.record.details.length; i++) {
        if (this.record.details[i].check) cnt++;
      }

      // sweet alert if cnt > max_votes
      if (cnt > max_votes) {
        Swal.fire({
          title: "Warning!",
          text: "Vote Rule:" + this.record.rule_text,
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      // post vote resutl

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("id", this.proof_id);

      form_Data.append("answers", JSON.stringify(this.record.details));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/voting_system_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();

          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_4').toggle();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.submit = false;
        });
    },
    
  },
});
