var app = new Vue({
    el: "#app",
    data: {
      submit: false,

      // id of the quotation
      l_id:0,
      id:0,

      p_pid:0,
      p_brand:0,

      //img_url: 'https://storage.googleapis.com/feliiximg/',

      img_url: 'https://storage.googleapis.com/feliiximg/',

      // menu
      show_header: false,
      show_footer: false,
      show_page: false,
      show_subtotal: false,
      show_total: false,
      show_term: false,
      show_payment_term: false,
      show_signature: false,

      // header
      first_line : '',
      second_line : '',
      project_category : '',
      quotation_no : '',
      quotation_date : '',

      prepare_for_first_line : '', 
      prepare_for_second_line : '',
      prepare_for_third_line : '',

      prepare_by_first_line : '',
      prepare_by_second_line : '',
      prepare_by_third_line : '',

      // _header
      temp_first_line : '',
      temp_second_line : '',
      temp_project_category : '',
      temp_quotation_no : '',
      temp_quotation_date : '',

      temp_prepare_for_first_line : '', 
      temp_prepare_for_second_line : '',
      temp_prepare_for_third_line : '',

      temp_prepare_by_first_line : '',
      temp_prepare_by_second_line : '',
      temp_prepare_by_third_line : '',

      // footer
      footer_first_line : '',
      footer_second_line : '',

      // _footer
      temp_footer_first_line : '',
      temp_footer_second_line : '',

      // page
      pages : [],
      temp_pages : [],

      

      // block_names
      block_names : [],
      block_value : [],

      // blocks
      blocks : [],

      edit_type_a : false,
      edit_type_b : false,

      temp_block_a : [],
      temp_block_b : [],

      edit_type_a_image : false,
      edit_type_a_noimage : false,

      edit_type_b_noimage : false,

  
      position: [],
      title: [],
      department: "",
      title_id: 0,
  
      // data
     
      editing: false,
  
  
      receive_records: [],

      product_records: [],
      product : {},
  
      title_info: {},
      template: {},
      library: {},
  
      view_detail: false,
      record: {},
  
         // evaluate
         evals:{},
         avg:10.0,
         avg1:10.0,
         avg2:0.0,
     
      total: {
        id:0,
        page: 0,
        discount:'0',
        vat : '',
        show_vat : '',
        valid : '',
        total : '',
        real_total : '',
        back_total : '',
      },

      
      term:
      {
        page: 0,
        item: [],
      },

      payment_term:
      {
        page: 0,
        payment_method: '',
        brief : '',
        item: { 
          bank_name : '',
          first_line : '',
          second_line : '',
          third_line : '',
        },
      },


      sig:
      {
        page: 0,
        item_client: [],
        item_company: [],
      },

      subtotal:0,
      subtotal_novat:0,

      subtotal_novat_a:0,
      subtotal_novat_b:0,

      subtotal_info_not_show_a:0,
      subtotal_info_not_show_b:0,

      show_title : true,

      // version II new parameters

      

        // paging
        product_page: 1,
    pg:0,
    //perPage: 10,
    product_pages: [],

    product_pages_10: [],

    product_total:0,

        inventory: [
        {name: '10', id: 10},
        {name: '25', id: 25},
        {name: '50', id: 50},
        {name: '100', id: 100},
        {name: 'All', id: 10000}
        ],

        perPage: 10,

        url: '',
        url1: '',
        url2: '',
        url3: '',
        code: '',
        brand: '',
        category: '',
        sub_category_name: '',

        tags:[],
        price: '',
        quoted_price: '',
        variation1_value:[],
        variation2_value: [],
        variation3_value: [],
        variation4_value: [],
    
        variation_product: [],

        v1:"",
        v2:"",
        v3:"",
        v4:"",

        accessory_infomation: [],

        related_product: [],
        specification: [],
        description: "",

        // vat for each product
        product_vat : '',

        // info
        name :"",
        title: "",
        is_manager: "",

        url : "",

        brands: [],
        fil_brand: "",
        fil_code: "",
        fil_tag : [],
        fil_id: "",
        fil_keyword: "",
        fil_category: "",

        special_infomation: [],
        special_infomation_detail: [],
        attributes:[],

        toggle_type:'',

        groupedItems : [],

        product_array: [],
        qp:'',
        srp:'',

        of1:'',
        ofd1:'',

        phased : 0,

        signature_codebook: [],
        tag_group : [],

        out : "",
        out_cnt : 0,

        is_load : false,

        product_set : [],
        show_accessory: false,

        temp_pages_verify : [],

        is_last_order : '',
        last_order_name : '',
        last_order_at : '',
        last_order_url : '',


        show_access : false,
        quotation_control : false,

        temp_can_view : '',
        can_view : '',
        temp_can_duplicate: '',
        can_duplicate: '',
        
    },
  
    created() {
      let _this = this;
      let uri = window.location.href.split("?");
      if (uri.length >= 2) {
        let vars = uri[1].split("&");

        let tmp = "";
        vars.forEach(async function(v) {
          tmp = v.split("=");
          if (tmp.length == 2) {
            switch (tmp[0]) {
              case "id":
                _this.id = tmp[1];

                if(_this.id != 0)
                 _this.l_id = _this.id;

                break;
              default:
                console.log(`Too many args`);
            }
          }
        });
      }
      
      this.getRecord();
    //   this.get_product_records();
    //   this.getUserName();
    //   this.get_brands();
    //   this.get_signature();
    //   this.getTagGroup();
    //   this.getQuotationControl();
    },
  
    computed: {
        displayedPosts() {
            //if(this.pg == 0)
            //  this.filter_apply_new();
      
            this.setPages();
            return this.paginate(this.product_records);
          },

          show_ntd : function() {
  
            return false;
          }
    },
  
    mounted() {
      
    },
  
    watch: {

      
  
      show_header() {
        if(this.show_header) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_footer() {
        if(this.show_footer) {
          this.show_header = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_page() {
        if(this.show_page) {
          this.show_footer = false;
          this.show_header = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_subtotal() {
        if(this.show_subtotal) {
          this.show_footer = false;
          this.show_page = false;
          this.show_header = false;
          this.show_total = false;
          this.show_term = false;
          this.show_signature = false;
        }
      },

      show_total() {
        if(this.show_total) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_header = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_term() {
        if(this.show_term) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_header = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_payment_term() {
        if(this.show_payment_term) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_header = false;
          this.show_term = false;
          this.show_signature = false;
        }
      },

      show_signature() {
        if(this.show_signature) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_header = false;
        }
      },
      
      department() {
        this.title = this.shallowCopy(
          this.position.find((element) => element.did == this.department)
        ).items;
  
      },
  
    },
  
    methods: {
      
      getQuotationControl: function() {
        var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append('jwt', token);
  
        axios({
            method: 'get',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/quotation_control',
            data: form_Data
        })
        .then(function(response) {
            //handle success
            _this.quotation_control = response.data.quotation_control;
  
        })
        .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            })
        });
      },

      getTagGroup: function() {
        let _this = this;
          
            let token = localStorage.getItem('accessToken');
            const params = {

          };
            axios
                .get('api/tag_mgt_get', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.tag_group = res.data;
  
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },
      sort_me(type) {

        if(type == 1) {
          this.of1 = '4';
          this.ofd1 = '1';
        } else if(type == 2) {
          this.of1 = '4';
          this.ofd1 = '2';
        } else if(type == 3) {
          this.of1 = '5';
          this.ofd1 = '1';
        } else if(type == 4) {
          this.of1 = '5';
          this.ofd1 = '2';
        } else if(type == 0) {
          this.fil_id = '';
          this.fil_code = '';
          this.fil_keyword = '';
          this.fil_category = '';
          this.fil_tag = [];
          this.fil_brand = '';
          this.of1 = '';
          this.ofd1 = '';
          this.pg = 1;
          $("#tag01").val('default');
          $("#tag01").selectpicker("refresh");
        }

        this.filter_apply_new();
      },

      selectall(){
        let stat = "";

        for(let i = 0; i < this.product_array.length; i++)
        {
          if(this.product_array[i].pid !== 0)
          {
            stat = this.product_array[i].is_selected;
            break;
          }
        }

        this.product_array.forEach(element => {
          if(element.pid !== 0)
            stat == 1 ? element.is_selected = '' : element.is_selected = 1;
        });
      },

      prod_export : async function() {
 
        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("q_id", this.id);
        form_Data.append("items", JSON.stringify(this.product_array));
        form_Data.append("pid", this.p_pid);
        form_Data.append("brand", this.p_brand);
        form_Data.append("qp", this.qp);
        form_Data.append("srp", this.srp);

        let res = await axios({
          method: 'post',
          url: 'api/quotation_export',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        window.open("product_display_export?id=" + this.id, '_blank');

      },

      approval_form_post : async function() {

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        var parameters = {qid: this.id};

        let res = await axios({
          method: 'get',
          url: 'api/approval_form',
          params: parameters,
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        var newid = 0;
        if(res.data.length > 0)
        {
          var newid = res.data[0].id;
          window.open("approval_form?id=" + newid, '_blank');
        }

      },

      
      soa_post : async function() {

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        var parameters = {qid: this.id};

        let res = await axios({
          method: 'get',
          url: 'api/soa',
          params: parameters,
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        var newid = 0;
        if(res.data.length > 0)
        {
          var newid = res.data[0].id;
          window.open("soa?id=" + newid, '_blank');
        }

      },

        specification_sheet() {
            $('#modal_specification_sheet').modal('toggle');
        },

      add_with_image(all) {

        var photo = "";
        var photo2 = "";
        var photo3 = "";

        var price = "";
        var list = "";

        var srp = 0;

        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3 && element.v4 == this.v4)
        )

        if(this.product.product.length > 0 && item_product.id == undefined && all != 'all') {
          alert('Please choose an option for each attribute');
          return;
        }

        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            photo = item_product.photo;

            photo2 = this.product.photo2;
            photo3 = this.product.photo3;

            // price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
            price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
            srp =  Number(item_product.price);
            if(this.v1 != "")
              list += (item_product.k1 + ': ' + item_product.v1) + "\n";
            if(this.v2 != "")
              list += (item_product.k2 + ': ' + item_product.v2) + "\n";
            if(this.v3 != "")
              list += (item_product.k3 + ': ' + item_product.v3) + "\n";
            if(this.v4 != "")
              list += (item_product.k4 + ': ' + item_product.v4) + "\n";
        }
        else
        {
          photo = this.product.photo1;
          photo2 = this.product.photo2;
          photo3 = this.product.photo3;
          // price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
          price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
          srp = Number(this.product.price_org);
          list = "";
        }

        if(all == 'all')
        {
          list = "";
          var k1, k2, k3, k4;
          k1 = this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1;
          k2 = this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2;
          k3 = this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3;
          k4 = this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4;

          if(k1 !== '')
            list += (this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1) + ': ' + this.product.variation1_value.join(', ') + "\n";
          if(k2 !== '')
            list += (this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2) + ': ' + this.product.variation2_value.join(', ') + "\n";
          if(k3 !== '')
            list += (this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3) + ': ' + this.product.variation3_value.join(', ') + "\n";
          if(k4 !== '')
            list += (this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4) + ': ' + this.product.variation4_value.join(', ') + "\n";

          photo = this.product.photo1;
          photo2 = this.product.photo2;
          photo3 = this.product.photo3;

          if(this.product.srp !== null || this.product.srp_quoted !== null)
            price = this.product.srp_quoted !== null ? this.product.srp_quoted : this.product.srp;

          srp = this.product.srp;
            //price = this.product.srp !== null ? this.product.srp : this.product.srp_quoted;

          if(price == null)
            //price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
            price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
            
        }

        for(var i=0; i<this.specification.length; i++)
        {
            if(this.specification[i].k1 !== '')
              list += this.specification[i].k1 + ': ' + this.specification[i].v1 + "\n";
            if(this.specification[i].k2 !== '')
              list += this.specification[i].k2 + ': ' + this.specification[i].v2 + "\n";
        }

        // add phased out information
        if((this.product.phased_out_cnt > 0 && this.phased == 1) || (this.product.phased_out_cnt > 0 && all == 'all'))
        {
          list += "\n";
          list += "Phased-out Variants:\n";
          list += this.product.phased_out_text.split("<br/>").join("\n");
        }

        if(price == null)
          price = this.product.srp_quoted !== 0 ?  this.product.srp_quoted : this.product.srp;
          // price = this.product.srp !== 0 ?  this.product.srp : this.product.srp_quoted;

        if(srp == null)
          srp = 0;

        var block_a_image = 'image';
        var sn = 0;
        if(this.toggle_type == 'A')
          var items = this.temp_block_a;

        if(this.toggle_type == 'B')
          var items = this.temp_block_b;

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        list.replace(/\n+$/, "");
        sn = sn + 1;

        description = "";
        if(this.product.category == 'Systems Furniture')
        {
          description = list;
          list = this.product.description;
        }

        if(this.toggle_type == 'A')
        {
          item = {
            id: sn,
            url: photo !== '' ? this.img_url + photo  : '',
            url2 : photo2 !== '' ? this.img_url + photo2 : '',
            url3 : photo3 !== '' ? this.img_url + photo3 : '',

            file: {
              name: "",
            },
            type : block_a_image,
            code: this.product.code,
            photo: photo,
            photo2: photo2,
            photo3: photo3,
            qty: "",
            price: price,
            srp: srp,
            discount: "0",
            amount: "",
            desc: description,
            list: list,
            num:"",
            ratio:1.0,
            notes: "",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
            v4: all == 'all' ? '' : this.v4,
          };
        }

        if(this.toggle_type == 'B')
        {
          item = {
            id: sn,
            
            url:  '',
            url2 : '',
            url3 : '',

            code: "",
            photo: photo,
            qty: "1",
            price: price,
            srp: srp,
            ratio:1.0,
            discount: "0",
            amount: "",
            desc: description,
            list: list,
            num:"",
            notes: "",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
            v4: all == 'all' ? '' : this.v4,
          };
        }

        items.push(item);

        alert('Add Successfully');
      },

      add_without_image(all) {

        var photo = "";
        var price = "";
        var list = "";

        var srp = 0;

        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3 && element.v4 == this.v4)
        )

        if(this.product.product.length > 0 && item_product.id == undefined && all != 'all') {
          alert('Please choose an option for each attribute');
          return;
        }

        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            photo = item_product.photo;
            //price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
            price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
            srp =  Number(item_product.price);
            if(this.v1 != "")
              list += (item_product.k1 + ': ' + item_product.v1) + "\n";
            if(this.v2 != "")
              list += (item_product.k2 + ': ' + item_product.v2) + "\n";
            if(this.v3 != "")
              list += (item_product.k3 + ': ' + item_product.v3) + "\n";
            if(this.v4 != "")
              list += (item_product.k4 + ': ' + item_product.v4) + "\n";
        }
        else
        {
          photo = this.product.photo1;
          //price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
          price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
          srp = Number(this.product.price_org);
          list = "";
        }

        if(all == 'all')
        {
          list = "";
          var k1, k2, k3, k4;
          k1 = this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1;
          k2 = this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2;
          k3 = this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3;
          k4 = this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4;

          if(k1 !== '')
            list += (this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1) + ': ' + this.product.variation1_value.join(', ') + "\n";
          if(k2 !== '')
            list += (this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2) + ': ' + this.product.variation2_value.join(', ') + "\n";
          if(k3 !== '')
            list += (this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3) + ': ' + this.product.variation3_value.join(', ') + "\n";
          if(k4 !== '')
            list += (this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4) + ': ' + this.product.variation4_value.join(', ') + "\n";

          photo = this.product.photo1;

          if(this.product.srp !== null || this.product.srp_quoted !== null)
            price = this.product.srp_quoted !== null ? this.product.srp_quoted : this.product.srp;
       
            //price = this.product.srp !== null ? this.product.srp : this.product.srp_quoted;

          if(price == null)
            //price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
            price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
            
            
          srp = this.product.srp;
        }

        if(price == null)
          price = this.product.srp_quoted !== 0 ?  this.product.srp_quoted : this.product.srp;
          //price = this.product.srp !== 0 ?  this.product.srp : this.product.srp_quoted;

        if(srp == null)
          srp = 0;

        for(var i=0; i<this.specification.length; i++)
        {
            if(this.specification[i].k1 !== '')
              list += this.specification[i].k1 + ': ' + this.specification[i].v1 + "\n";
            if(this.specification[i].k2 !== '')
              list += this.specification[i].k2 + ': ' + this.specification[i].v2 + "\n";
        }

        // add phased out information
        if((this.product.phased_out_cnt > 0 && this.phased == 1) || (this.product.phased_out_cnt > 0 && all == 'all'))
        {
          list += "\n";
          list += "Phased-out Variants:\n";
          list += this.product.phased_out_text.split("<br/>").join("\n");
        }

        list.replace(/\n+$/, "");

        var block_a_image = 'noimage';
        var sn = 0;
        if(this.toggle_type == 'A')
          var items = this.temp_block_a;

        if(this.toggle_type == 'B')
          var items = this.temp_block_b;

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        description = "";
        if(this.product.category == 'Systems Furniture')
        {
          description = list;
          list = this.product.description;
        }

        if(this.toggle_type == 'A')
        {
          item = {
            id: sn,
            url: "",
            url2: "",
            url3: "",
            file: {
              name: "",
            },
            type : block_a_image,
            code: this.product.code,
            photo: "",
            qty: "",
            price: price,
            srp: srp,
            discount: "0",
            amount: "",
            desc: description,
            list: list,
            num:"",
            notes: "",
            ratio:1.0,
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
            v4: all == 'all' ? '' : this.v4,
          };
        }

        if(this.toggle_type == 'B')
        {
          item = {
            id: sn,
            
            url: '',
            url2 : '',
            url3 : '',

            code: this.product.code,
            photo: "",
            qty: "1",
            price: price,
            srp: srp,
            ratio:1.0,
            discount: "0",
            amount: "",
            desc: description,
            list: list,
            num:"",
            notes: "",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
            v4: all == 'all' ? '' : this.v4,
          };
        }

        items.push(item);
        alert('Add Successfully');
      },
      
      set_up_specification() {
        let k1 = '';
        let k2 = '';
 
        let v1 = '';
        let v2 = '';

        this.specification = [];
 
       for(var i=0; i < this.attributes.length; i++)
       {
         if(this.attributes[i].type != "custom")
         {
           if(k1 == "")
           {
             k1 = this.attributes[i].category;
             v1 = this.attributes[i].value.join(' ');
           }else if(k1 !== "" && k2 == "")
           {
             k2 = this.attributes[i].category;
             v2 = this.attributes[i].value.join(' ');
 
             obj = {k1: k1, v1: v1, k2: k2, v2: v2};
             this.specification.push(obj);
             k1  = '';
             k2  = '';
             v1  = '';
             v2  = '';
           }
         }
       }
 
       if(k1 == "" && this.product.moq !== "")
       {
         k1 = 'MOQ';
         v1 = this.product.moq;
       }else if(k1 !== "" && k2 == "" && this.product.moq !== "")
       {
         k2 = 'MOQ';
         v2 = this.product.moq;
     
         obj = {k1: k1, v1: v1, k2: k2, v2: v2};
         this.specification.push(obj);
         k1  = '';
         k2  = '';
         v1  = '';
         v2  = '';
       }
 /*
       if(k1 == "" && this.record[0]['notes'] !== "")
       {
         k1 = 'Notes';
         v1 = this.record[0]['notes'];
       }else if(k1 !== "" && k2 == "" && this.record[0]['notes'] !== "")
       {
         k2 = 'Notes';
         v2 = this.record[0]['notes'];
     
         obj = {k1: k1, v1: v1, k2: k2, v2: v2};
         this.specification.push(obj);
         k1  = '';
         k2  = '';
         v1  = '';
         v2  = '';
       }
 */
       if(k1 !== "" && k2 == "")
       {
         k2 = '';
         v2 = '';
     
         obj = {k1: k1, v1: v1, k2: k2, v2: v2};
         this.specification.push(obj);
       
       }
     },

      get_brands: function() {
        let _this = this;
  
        const params = {
    
        };
  
        let token = localStorage.getItem("accessToken");
        axios
          .get("api/product_brands", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
            let res = response.data;
          
              _this.brands = response.data;
               
          })
          .catch(function(error) {
            console.log(error);
          });
      },

      get_signature: function() {
        let _this = this;
  
        const params = {
    
        };
  
        let token = localStorage.getItem("accessToken");
        axios
          .get("api/signature_codebook?page=1&size=10000", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
       
              _this.signature_codebook = response.data;
               
          })
          .catch(function(error) {
            console.log(error);
          });
      },

      change_url: function(url) {
        this.url = (url !== '' ? this.img_url + url : '');
      },

      close_single: function() {
        $('#modal_product_display').modal('toggle');
    },


    
    get_records: async function(id) {
      let _this = this;
      let record = {};

      if(id === -1)
          return {};

      const params = {
        id: id,
      };

      let token = localStorage.getItem("accessToken");

      let res = await axios
        .get("api/product_display_code", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });

        return res.data;
        
    },

    getSingleProduct : function(id) {


      let _this = this;


      const params = {
        sd: id,
        c: '',
        t: '',
        b: '',
        of1: '',
        ofd1: '',
        of2: '',
        ofd2: '',
        page: 1,
        size: 10,
      };
  
      let token = localStorage.getItem("accessToken");
  
      axios
        .get("api/product_calatog", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
          {
            _this.product = response.data[0];
            _this.url = _this.product.photo1 !== '' ? _this.img_url + _this.product.photo1 : '';

            _this.special_infomation = _this.product.special_information[0].lv3[0];
            _this.attributes = _this.product.attribute_list;
    
            _this.related_product  = _this.product.related_product;

            _this.quoted_price = _this.product.quoted_price;
            _this.price = _this.product.price;

            _this.v1 = "";
            _this.v2 = "";
            _this.v3 = "";
            _this.v4 = "";
    
            _this.chunk(_this.related_product, 4);
    
            _this.set_up_variants();
            _this.set_up_specification();
          }

    
        })
        .catch(function(error) {
          console.log(error);
        });

    },

    PhaseOutAlert(phased_out_text){
      hl = "";
      for(var i = 0; i < phased_out_text.length; i++)
      {
        hl += "(" + Number(i+1) + ") " + phased_out_text[i] + "<br/>";
      }

      Swal.fire({
        title: 'Phased Out Variants:',
        html: hl,
        confirmButtonText: 'OK',
        });
      
    },

      btnEditClick: async function(product) {

        let _this = this;
        let data = {};
        if(product.sub_category == '10020000')
         {
            data = await this.get_records(product.id);

            this.product = data[0];

            this.product_set = this.product['product_set'];

            for(var i = 0; i < this.product_set.length; i++)
            {
              this.product_set[i]['special_infomation'] = this.product_set[i].record[0]['special_information'][0].lv3[0]
              this.product_set[i]['specification'] = [];
              this.set_up_specification_set(this.product_set[i]);
            }

          
         }
         else
          this.product = product;

        $('#modal_product_display').modal('toggle');
  
        this.url = (this.product.photo1 !== '' && this.product.photo1 !== undefined) ? this.img_url + this.product.photo1 : '';

        this.special_infomation = product.special_information[0].lv3[0];
        this.attributes = product.attribute_list;

        this.related_product  = product.related_product;

        this.quoted_price = product.quoted_price;
        this.price = product.price;

        this.v1 = "";
        this.v2 = "";
        this.v3 = "";
        this.v4 = "";

        this.out = product.out;
        this.out_cnt = product.phased_out_cnt;

        this.last_order_name = product.last_order_name;
        this.last_order_at = product.last_order_at;
        this.last_order_url = product.last_order_url;

        this.chunk(this.related_product, 4);

        this.set_up_variants();
        this.set_up_specification();
      },

      chunk: function(arr, size) {
        var newArr = [];
        for (var i=0; i<arr.length; i+=size) {
          newArr.push(arr.slice(i, i+size));
        }
        this.groupedItems  = newArr;
      },

      set_up_variants() {
        for(var i=0; i<this.product.variation1_value.length; i++)
        {
          $('#variation1_value').tagsinput('add', this.product.variation1_value[i]);
        }
        for(var i=0; i<this.product.variation2_value.length; i++)
        {
          $('#variation2_value').tagsinput('add', this.product.variation2_value[i]);
        }
        for(var i=0; i<this.product.variation3_value.length; i++)
        {
          $('#variation3_value').tagsinput('add', this.product.variation3_value[i]);
        }
        for(var i=0; i<this.product.variation4_value.length; i++)
        {
          $('#variation4_value').tagsinput('add', this.product.variation4_value[i]);
        }
        
      },

      set_special_attributes() {
        for (let i = 0; i < this.attributes.length; i++) {
          let cat_id = this.attributes[i].cat_id;
          let value = this.attributes[i].value;
          for (let j = 0; j < this.special_infomation.length; j++) {
            if(this.special_infomation[j].cat_id == cat_id)
              this.$refs[cat_id][0].value = value;
          }
        }
      },

      getUserName: function() {
        var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append('jwt', token);
  
        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/on_duty_get_myname',
            data: form_Data
        })
        .then(function(response) {
            //handle success
            _this.name = response.data.username;
            _this.is_manager = response.data.is_manager;
            _this.title = response.data.title.toLowerCase();
  
        })
        .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            })
        });
      },
      
    get_product_records: function() {
      let _this = this;

    const params = {
      d: '',
      c: '',
      t: '',
      b: '',
      of1: '',
      ofd1: '',
      of2: '',
      ofd2: '',
      page: 1,
      size: 10,
    };

    let token = localStorage.getItem("accessToken");

    this.product_total = 0;

    axios
      .get("api/product_calatog", {
        params,
        headers: { Authorization: `Bearer ${token}` },
      })
      .then(function(response) {
        console.log(response.data);
        let res = response.data;
        if(res.length > 0) 
        {
          _this.product_records = response.data;
          _this.product_total = _this.product_records[0].cnt;
        }

        if(_this.pg !== 0)
        { 
          _this.product_page = _this.pg;
          _this.setPages();
        }
        
  
      })
      .catch(function(error) {
        console.log(error);
      });
  },
        
    filter_apply_new: function() {
      let _this = this;

      _this.pg = 1;

      const params = {
        sd: _this.fil_id,
        c: _this.fil_code,
        k: _this.fil_keyword,
        g: _this.fil_category,
        t: JSON.stringify(_this.fil_tag),
        b: _this.fil_brand,
        of1: _this.of1,
        ofd1: _this.ofd1,
        of2: '',
        ofd2: '',
        page: _this.pg,
        size: 10,
      };
  
      let token = localStorage.getItem("accessToken");
  
      this.product_records = [];
      this.product_total = 0;
  
      axios
        .get("api/product_calatog", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
          {
            _this.product_records = response.data;
            _this.product_total = _this.product_records[0].cnt;
          }

          _this.pg = 1;
  
          if(_this.pg !== 0)
          { 
            _this.product_page = _this.pg;
            _this.setPages();
          }
          
    
        })
        .catch(function(error) {
          console.log(error);
        });
  
  
      },

      change_total_amount(row) {
        
        row.discount = Math.floor(row.discount);
        if(row.discount > 100)
          row.discount = 100;

        var total = 0;

        total = ((this.subtotal_info_not_show_a * 1 + this.subtotal_info_not_show_b * 1) * (1 - row.discount * 0.01)).toFixed(2);
        
        if(row.vat == 'Y')
        {
          total = ((this.subtotal_novat_a * 1 + this.subtotal_info_not_show_b * 1) * (1 - row.discount * 0.01)).toFixed(2);
          total =  (total * 1) + (this.subtotal_novat_a * (1 - row.discount * 0.01) * 0.12);
        }

        if(row.vat == 'P')
        {
          total = ((this.subtotal_novat_a * 1 + this.subtotal_info_not_show_b * 1) * (1 - row.discount * 0.01)).toFixed(2);
          total =  (total * 1) + (this.subtotal_novat_a * (1 - row.discount * 0.01) * 0.12);
        }

        total = Number(total).toFixed(2);
        //this.real_total = row.total;
        row.real_total = total;
      },

      print_page() {
        this.show_title = false;
        app.$forceUpdate();
        window.print();
      },

      clear_sig_photo(_id) {
        var item = this.sig.item_company.find(({ id }) => id === _id);
        item.url = "";
  
        document.getElementById('sig_image_'+_id).value = "";
      },
      
      onSigFileChangeImage(e, _id) {
        const file = e.target.files[0];

        var item = this.sig.item_company.find(({ id }) => id === _id);
    
        let url = URL.createObjectURL(file);
  
        item.url = url;
          
      },

      sig_item_company_del: function(fromIndex, eid) {

        var index = this.sig.item_company.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.sig.item_company.splice(index, 1);
        }
      },

      sig_item_company_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.sig.item_company.length - 1) 
          return;
  
        var element = this.sig.item_company.find(({ id }) => id === eid);
        this.sig.item_company.splice(fromIndex, 1);
        this.sig.item_company.splice(toIndex, 0, element);
      },

      sig_item_company_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.sig.item_company.find(({ id }) => id === eid);
        this.sig.item_company.splice(fromIndex, 1);
        this.sig.item_company.splice(toIndex, 0, element);
      },

      sig_item_client_del: function(fromIndex, eid) {

        var index = this.sig.item_client.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.sig.item_client.splice(index, 1);
        }
      },

      sig_item_client_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.sig.item_client.length - 1) 
          return;
  
        var element = this.sig.item_client.find(({ id }) => id === eid);
        this.sig.item_client.splice(fromIndex, 1);
        this.sig.item_client.splice(toIndex, 0, element);
      },

      sig_item_client_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.sig.item_client.find(({ id }) => id === eid);
        this.sig.item_client.splice(fromIndex, 1);
        this.sig.item_client.splice(toIndex, 0, element);
      },

      add_sig_client_item() {
        let order = 0;
        
        if(this.sig.item_client.length != 0)
          order = Math.max.apply(Math, this.sig.item_client.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "type" : 'C',
          "photo" : '',
          "url" : '',
          "name" : '',
          "position" : '',
          "phone" : '',
          "email" : '',
        }, 
  
        this.sig.item_client.push(obj);
      },

      signature_import(item) {  
        let order = 0;
        
        if(this.sig.item_company.length != 0)
          order = Math.max.apply(Math, this.sig.item_company.map(function(o) { return o.id; }))

        obj = {
          "id" : order + 1,
          "type" : 'F',
          "photo" : item.pic_url,
          "url" : item.url,
          "name" : item.name,
          "position" : item.position,
          "phone" : item.phone,
          "email" : item.email,
        }, 
  
        this.sig.item_company.push(obj);

        alert('Add Successfully');
      },

      add_sig_company_item() {
        let order = 0;
        
        if(this.sig.item_company.length != 0)
          order = Math.max.apply(Math, this.sig.item_company.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "type" : 'F',
          "photo" : '',
          "url" : '',
          "name" : '',
          "position" : '',
          "phone" : '',
          "email" : '',
        }, 
  
        this.sig.item_company.push(obj);
      },

      add_signature_codebook() {
        $('#modal_signature_codebook').modal('toggle');
      },

      sig_save: async function() {
        if (this.submit == true) return;

        //if(this.sig.page == 0) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("detail", JSON.stringify(this.sig));

        for (var i = 0; i < this.sig.item_company.length; i++) {
          let file = document.getElementById('sig_image_' + this.sig.item_company[i].id);
          if(file) {
            let f = file.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('sig_image_' + this.sig.item_company[i].id, f);
          }
        }

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_sig_insert',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
        
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();
      
      
      },

      close_sig() {
        this.show_signature = false;

      },

      term_save: async function() {
        if (this.submit == true) return;

        //if(this.term.page == 0) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("detail", JSON.stringify(this.term));

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_term_insert',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
   
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();
  
      
      },

      
      payment_term_save: async function() {
        if (this.submit == true) return;

        //if(this.payment_term.page == 0) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("detail", JSON.stringify(this.payment_term));

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_payment_term_insert',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
   
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();
  
      
      },

      close_term() {
        this.show_term = false;

      },

      close_payment_term() {
        this.show_payment_term = false;

      },

      term_item_del: function(fromIndex) {

        var index = fromIndex;
        if (index > -1) {
          this.term.item.splice(index, 1);
        }
      },

      payment_term_item_del: function(fromIndex) {

        var index = fromIndex;
        if (index > -1) {
          this.payment_term.item.splice(index, 1);
        }
      },

      term_item_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.term.item.length - 1) 
          return;
  
        var element = this.term.item.find(({ id }) => id === eid);
        this.term.item.splice(fromIndex, 1);
        this.term.item.splice(toIndex, 0, element);
      },

      payment_term_item_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.payment_term.item.length - 1) 
          return;
  
        var element = this.payment_term.item.find(({ id }) => id === eid);
        this.payment_term.item.splice(fromIndex, 1);
        this.payment_term.item.splice(toIndex, 0, element);
      },

      term_item_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.term.item.find(({ id }) => id === eid);
        this.term.item.splice(fromIndex, 1);
        this.term.item.splice(toIndex, 0, element);
      },

      payment_term_item_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.payment_term.item.find(({ id }) => id === eid);
        this.payment_term.item.splice(fromIndex, 1);
        this.payment_term.item.splice(toIndex, 0, element);
      },

      add_term_item() {
        let order = 0;
        
        if(this.term.item.length != 0)
          order = Math.max.apply(Math, this.term.item.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "title" : '',
          "brief" : '',
          "list" : '',
        }, 
  
        this.term.item.push(obj);
      },

      
      add_payment_term_item() {
        let order = 0;
        
        if(this.payment_term.item.length != 0)
          order = Math.max.apply(Math, this.payment_term.item.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "bank_name" : '',
          "first_line" : '',
          "second_line" : '',
          "third_line" : '',
        }, 
  
        this.payment_term.item.push(obj);
      },

      close_total() {
        this.show_total = false;

        if(this.total.id == 0)
        {
          this.total = {
            id:0,
            page: 0,
            discount:'',
            vat : '',
            show_vat : '',
            valid : '',
            total : '',
          };
        }
      },

      save_total: async function() {
        if (this.submit == true) return;

        //if(this.total.page == 0) return;

        if(this.total.discount > 100 || this.total.discount < 0)
        {
          Swal.fire({
            text: "Discount must between 0 and 100.",
            icon: "info",
            confirmButtonText: "OK",
          });
          return;
        }

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("page", this.total.page);
        form_Data.append("discount", this.total.discount);
        form_Data.append("vat", this.total.vat);
        form_Data.append("show_vat", this.total.show_vat);
        form_Data.append("valid", this.total.valid);
        form_Data.append("total", this.total.total);
      
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/quotation_total_insert",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            //_this.id = response.data.id;
            
            Swal.fire({
              html: response.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
  
            _this.reload();
            _this.submit = false;
          })
          .catch(function(error) {
            //handle error
            Swal.fire({
              text: JSON.stringify(error),
              icon: "info",
              confirmButtonText: "OK",
            });
  
            _this.reload();
            _this.submit = false;
          });
      
      },

      subtotal_save_changes: async function (id, type_id, temp_block) {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", id);
        form_Data.append("type_id", type_id);
        form_Data.append("block", JSON.stringify(temp_block));
      
        for (var i = 0; i < temp_block.length; i++) {
          let file = document.getElementById('block_image_' + temp_block[i].id + '_1');
          if(file) {
            let f = file.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id + '_1', f);
          }

          let file2 = document.getElementById('block_image_' + temp_block[i].id + '_2');
          if(file2) {
            let f = file2.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id + '_2', f);
          }

          let file3 = document.getElementById('block_image_' + temp_block[i].id + '_3');
          if(file3) {
            let f = file3.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id + '_3', f);
          }
        }
    

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_page_type_block_update',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
            _this.block_value = [];
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();

        },

      subtotal_save() {
        if(this.block_value.id == undefined)
          return;

        if(this.check_value() == false)
          return;

        var _id = this.block_value.id;
        var _type = this.block_value.type;
        var _page = this.block_value.page;

        var element = this.receive_records[0].pages.find(({ page }) => page === _page);
        var type = element.types.find(({ id }) => id === _id);
        
        if(_type == "A" && this.temp_block_a.length >= 0) {
          type.block_a = this.temp_block_a;

          this.subtotal_save_changes(this.id, _id, this.temp_block_a);
        }

        if(_type == "B" && this.temp_block_b.length >= 0) {
          type.block_b = this.temp_block_b;

          this.subtotal_save_changes(this.id, _id, this.temp_block_b);
        }
        
        
        this.subtotal_close();

      },

      check_value() {
        for(var i = 0; i < this.temp_block_a.length; i++) {
          if(this.temp_block_a[i].qty < 1 || this.temp_block_a[i].qty === '') {
            Swal.fire({
              text: "Qty must greater then 1.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }

          if(this.temp_block_a[i].discount < 0 || this.temp_block_a[i].discount > 100 || this.temp_block_a[i].discount === '') {
            Swal.fire({
              text: "Discount must between 0 and 100.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }
        }

        for(var i = 0; i < this.temp_block_b.length; i++) {
          if(this.temp_block_b[i].qty < 1 || this.temp_block_b[i].qty === '') {
            Swal.fire({
              text: "Qty must greater then 1.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }

          if(this.temp_block_b[i].discount < 0 || this.temp_block_b[i].discount > 100 || this.temp_block_b[i].discount === '') {
            Swal.fire({
              text: "Discount must between 0 and 100.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }
        }

        return true;
      },

      subtotal_close() {
        this.show_subtotal = false;
        this.edit_type_a = false;
        this.edit_type_b = false;
        this.temp_block_a = [];
        this.temp_block_b = [];

        this.edit_type_a_image = false;
        this.edit_type_a_noimage = false;
          
        this.edit_type_b_noimage = false;

        this.block_value = [];

        this.is_load = false;
      },

      block_b_up: function(fromIndex, eid) {

        let _this = this;

        Swal.fire({
          title: 'Determine Steps',
          html: 'Input how many steps you want to move: <br/> <input type="text" id="steps" value="1" /> <br/>',
          confirmButtonText: 'OK',
          showCancelButton: true,
          preConfirm: () => {
            steps = Swal.getPopup().querySelector('#steps').value
      
            return {steps: steps}
          }
        }).then((result) => {
          //Swal.fire("alcool: "+`${result.value.alcool}`+" and Cigarro: "+`${result.value.cigarro}`);

          var steps = result.value.steps;
      
          for(var i = 0; i < steps; i++)
          {
            var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = _this.temp_block_b.find(({ id }) => id === eid);
        _this.temp_block_b.splice(fromIndex, 1);
        _this.temp_block_b.splice(toIndex, 0, element);

            fromIndex = toIndex;
          }

        })
        
        
      },

      block_b_down: function(fromIndex, eid) {

        let _this = this;

        Swal.fire({
          title: 'Determine Steps',
          html: 'Input how many steps you want to move: <br/> <input type="text" id="steps" value="1" /> <br/>',
          confirmButtonText: 'OK',
          showCancelButton: true,
          preConfirm: () => {
            steps = Swal.getPopup().querySelector('#steps').value
      
            return {steps: steps}
          }
        }).then((result) => {
          //Swal.fire("alcool: "+`${result.value.alcool}`+" and Cigarro: "+`${result.value.cigarro}`);

          var steps = result.value.steps;

          for(var i = 0; i < steps; i++)
          {
            var toIndex = fromIndex + 1;

            if (toIndex > _this.temp_block_b.length - 1) 
              return;
      
            var element = _this.temp_block_b.find(({ id }) => id === eid);
            _this.temp_block_b.splice(fromIndex, 1);
            _this.temp_block_b.splice(toIndex, 0, element);

          fromIndex = toIndex;
        }
          
          })

        
      },

      block_b_del: function(eid) {

        var index = this.temp_block_b.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.temp_block_b.splice(index, 1);
        }
      },

      block_a_up: function(fromIndex, eid) {
        let _this = this;

        Swal.fire({
          title: 'Determine Steps',
          html: 'Input how many steps you want to move: <br/> <input type="text" id="steps" value="1" /> <br/>',
          confirmButtonText: 'OK',
          showCancelButton: true,
          preConfirm: () => {
            steps = Swal.getPopup().querySelector('#steps').value
      
            return {steps: steps}
          }
        }).then((result) => {
          //Swal.fire("alcool: "+`${result.value.alcool}`+" and Cigarro: "+`${result.value.cigarro}`);

          var steps = result.value.steps;
      
          for(var i = 0; i < steps; i++)
          {
            var toIndex = fromIndex - 1;
  
            if (toIndex < 0) 
              return;
  
            var element = _this.temp_block_a.find(({ id }) => id === eid);
            _this.temp_block_a.splice(fromIndex, 1);
            _this.temp_block_a.splice(toIndex, 0, element);

            fromIndex = toIndex;
          }

        })

      },

      block_a_down: function(fromIndex, eid) {
        let _this = this;

        Swal.fire({
          title: 'Determine Steps',
          html: 'Input how many steps you want to move: <br/> <input type="text" id="steps" value="1" /> <br/>',
          confirmButtonText: 'OK',
          showCancelButton: true,
          preConfirm: () => {
            steps = Swal.getPopup().querySelector('#steps').value
      
            return {steps: steps}
          }
        }).then((result) => {
          //Swal.fire("alcool: "+`${result.value.alcool}`+" and Cigarro: "+`${result.value.cigarro}`);

          var steps = result.value.steps;

          for(var i = 0; i < steps; i++)
          {
          var toIndex = fromIndex + 1;

          if (toIndex > _this.temp_block_a.length - 1) 
            return;
    
          var element = _this.temp_block_a.find(({ id }) => id === eid);
          _this.temp_block_a.splice(fromIndex, 1);
          _this.temp_block_a.splice(toIndex, 0, element);

          fromIndex = toIndex;
        }
          
          })
      },

      block_a_del: function(eid) {

        var index = this.temp_block_a.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.temp_block_a.splice(index, 1);
        }
      },

      clear_photo(_id, num) {
        var item = this.temp_block_a.find(({ id }) => id === _id);
        
        if (num == 1)
          item.url = "";

        if (num == 2)
          item.url2 = "";

        if (num == 3)
          item.url3 = "";
  
        document.getElementById('block_image_'+_id + '_' + num).value = "";
      },
      
      onFileChangeImage(e, _id, num) {
        const file = e.target.files[0];

        var item = this.temp_block_a.find(({ id }) => id === _id);
    
        let url = URL.createObjectURL(file);
        
        if (num == 1)
          item.url = url;

        if (num == 2)
          item.url2 = url;

        if (num == 3)
          item.url3 = url;
          
      },

      chang_discount : function(row) {
        if(row.discount > 100)
          row.discount = 100;

        let charge = (Number(row.price) * Number(row.ratio) * (100 - Math.floor(row.discount)) / 100).toFixed(2);
          row.amount = charge;

          if(charge < row.srp)
        {
          Swal.fire({
            text: "Warning!! Current discounted product price (P " + charge  + ") is already lower than SRP (P " + Number(row.srp).toFixed(2) + ").",
            icon: "warning",
            confirmButtonText: "OK",
          });
        }
       
      },

      chang_my_amount : function(row) {

        let charge = (Number(row.price) * Number(row.ratio) * (100 - Math.floor(row.discount)) / 100).toFixed(2);

        if(row.amount < row.srp * Number(row.qty))
        {
          Swal.fire({
            text: "Warning!! Current discounted product price (P " + (row.amount / Number(row.qty)).toFixed(2) + ") is already lower than SRP (P " + Number(row.srp).toFixed(2) + ").",
            icon: "warning",
            confirmButtonText: "OK",
          });
        }
      },

      chang_amount: function(row) {
        if(row.qty == '')
          return;

        if(row.price == '')
          return;

        row.qty = Math.floor(row.qty);
        row.discount = Math.floor(row.discount);
        if(row.discount > 100)
          row.discount = 100;

        
        // let charge = this.payment_record.charge;
        let charge = (Number(row.qty)) * Number(row.price) * Number(row.ratio)  * ((100 - Math.floor(row.discount)) / 100);

        if(this.product_vat == 'P')
          charge = charge * 1.12;

        row.amount = charge.toFixed(2);

        let ss =  Number(row.price) * Number(row.ratio)  * ((100 - Math.floor(row.discount)) / 100);
        if(charge < row.srp * Number(row.qty))
        {
          Swal.fire({
            text: "Warning!! Current discounted product price (P " + (row.amount / Number(row.qty)).toFixed(2) + ") is already lower than SRP (P " + Number(row.srp).toFixed(2) + ").",
            icon: "warning",
            confirmButtonText: "OK",
          });
        }
      },

      add_block_a() {
        var block_a_image = document.getElementById('with_image').value;
        var sn = 0;
        var items = this.temp_block_a;

        var _list = `Beam Angle:
Lumens:
CRI / RA:
CCT:
Wattage:
IP Rating:
Life Hours:
Color Finish:
Installation:`;

        if(this.project_category != 'Lighting')
          _list = "";


        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        item = {
          id: sn,
          url: "",
          url2: "",
          url3: "",
          file: {
            name: "",
          },
          type : block_a_image,
          code: "",
          photo: "",
          qty: "1",
          price: "",
          discount: "0",
          amount: "",
          notes: "",
          desc: "",
          list: _list,
          num:"",
          pid:0,
          ratio:1.0,
        };

        items.push(item);

      },

      add_block_b() {
        
      
        var sn = 0;
        var items = this.temp_block_b;

        var _list = `Beam Angle:
Lumens:
CRI / RA:
CCT:
Wattage:
IP Rating:
Life Hours:
Color Finish:
Installation:`;

        if(this.project_category != 'Lighting')
          _list = "";

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        item = {
          id: sn,

          url: "",
          url2: "",
          url3: "",
          
          code: "",
          photo: "",
          qty: "1",
          price: "",
          notes: "",
          discount: "0",
          amount: "",
          desc: "",
          list: _list,
          num:"",
          pid:0,
          ratio:1.0,
        };

        items.push(item);

      },

      load_block() {
        var value = this.block_value;
        
        if(value.type == 'A')
        {
          this.edit_type_a = true;
          this.edit_type_b = false;

          this.edit_type_a_image = false;
          this.edit_type_a_noimage = false;
          
          this.edit_type_b_noimage = false;

          this.temp_block_a = this.block_value.blocks;

          this.is_load = true;
        }

        if(value.type == 'B')
        {
          this.edit_type_b = true;
          this.edit_type_a = false;

          this.edit_type_a_image = false;
          this.edit_type_a_noimage = false;

          this.edit_type_b_noimage = false;

          this.temp_block_b = this.block_value.blocks;

          this.is_load = true;
        }
      
      },

      filter_apply: function(pg) {
        let _this = this;

        pg !== undefined ? this.pg  = pg : this.pg = this.product_page;
  
        const params = {
          sd: _this.fil_id,
          c: _this.fil_code,
          k: _this.fil_keyword,
          g: _this.fil_category,
          t: JSON.stringify(_this.fil_tag),
          b: _this.fil_brand,
          of1: _this.of1,
          ofd1: _this.ofd1,
          of2: '',
          ofd2: '',
          page: _this.pg,
          size: 10,
        };
    
        let token = localStorage.getItem("accessToken");
    
        this.product_total = 0;
    
        axios
          .get("api/product_calatog", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
            let res = response.data;
            if(res.length > 0) 
            {
              _this.product_records = response.data;
              _this.product_total = _this.product_records[0].cnt;
            }
    
            if(_this.pg !== 0)
            { 
              _this.product_page = _this.pg;
              _this.setPages();
            }
            
      
          })
          .catch(function(error) {
            console.log(error);
          });
      },

      cancel_header() {
        this.show_header = false;
      },
      
      cancel_access() {
        this.show_access = false;
      },

      save_header() {
        this.show_header = false;

        this.first_line = this.temp_first_line;
        this.second_line = this.temp_second_line;
        this.project_category = this.temp_project_category;
        this.quotation_no = this.temp_quotation_no;
        this.quotation_date = this.temp_quotation_date;
        this.prepare_for_first_line = this.temp_prepare_for_first_line;
        this.prepare_for_second_line = this.temp_prepare_for_second_line;
        this.prepare_for_third_line = this.temp_prepare_for_third_line;

        this.prepare_by_first_line = this.temp_prepare_by_first_line;
        this.prepare_by_second_line = this.temp_prepare_by_second_line;
        this.prepare_by_third_line = this.temp_prepare_by_third_line;

        this.header_save();
        
      },

      save_access() {
        this.show_access = false;

        this.can_view = this.temp_can_view;
        this.can_duplicate = this.temp_can_duplicate

        this.access_save();
        
      },

      cancel_footer() {
        this.show_footer = false;
      },

      save_footer() {
        this.show_footer = false;

        this.footer_first_line = this.temp_footer_first_line;
        this.footer_second_line = this.temp_footer_second_line;

        this.footer_save();
        
      },

      reset: function() {
        this.submit = false;
        // header
        this.first_line = '';
        this.second_line = '';
        this.project_category = '';
        this.quotation_no = '';
        this.quotation_date = '';

        this.prepare_for_first_line = '';
        this.prepare_for_second_line ='';
        this.prepare_for_third_line = '';

        this.prepare_by_first_line = '';
        this.prepare_by_second_line = '';
        this.prepare_by_third_line = '';

        // footer
        this.footer_first_line = '';
        this.footer_second_line = '';

        // _header
        this.temp_first_line = '';
        this.temp_second_line = '';
        this.temp_project_category = '';
        this.temp_quotation_no = '';
        this.temp_quotation_date = '';

        this.temp_prepare_for_first_line = '';
        this.temp_prepare_for_second_line ='';
        this.temp_prepare_for_third_line = '';

        this.temp_prepare_by_first_line = '';
        this.temp_prepare_by_second_line = '';
        this.temp_prepare_by_third_line = '';

        // _footer
        this.temp_footer_first_line = '';
        this.temp_footer_second_line = '';

        this.subtotal = 0;
        this.subtotal_novat_a = 0;
        this.subtotal_novat_b = 0;

        // page
        this.pages = [];
        this.temp_pages = [];

        this.can_view = '';
        this.can_duplicate = '';
        this.temp_can_view = '';
        this.temp_can_duplicate = '';

      },

      get_latest_record: async function() {
        let _this = this;
        if(_this.id == 0)
          return;
  
        const params = {
          id: _this.id,
        };
  
        let token = localStorage.getItem("accessToken");

        let res = await axios({ 
          method: 'get', 
          url: 'api/quotation', 
          params,
          headers: { Authorization: `Bearer ${token}` },
        });

        this.temp_pages_verify = JSON.parse(JSON.stringify(res.data[0].pages));

        },


      getRecord: function() {
        let _this = this;

        if(_this.id == 0)
          return;
  
        const params = {
          id: _this.id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/quotation_test", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            })
          .then(function(response) {
            console.log(response.data);
            _this.receive_records = response.data;
            if (_this.receive_records.length > 0) {
              _this.id = _this.receive_records[0].id;
              _this.first_line = _this.receive_records[0].first_line;
              _this.second_line = _this.receive_records[0].second_line;
              _this.project_category = _this.receive_records[0].project_category;
              _this.quotation_no = _this.receive_records[0].quotation_no;
              _this.quotation_date = _this.receive_records[0].quotation_date;

              _this.prepare_for_first_line = _this.receive_records[0].prepare_for_first_line;
              _this.prepare_for_second_line = _this.receive_records[0].prepare_for_second_line;
              _this.prepare_for_third_line = _this.receive_records[0].prepare_for_third_line;

              _this.prepare_by_first_line = _this.receive_records[0].prepare_by_first_line;
              _this.prepare_by_second_line = _this.receive_records[0].prepare_by_second_line;
              _this.prepare_by_third_line = _this.receive_records[0].prepare_by_third_line;

              // footer
              _this.footer_first_line = _this.receive_records[0].footer_first_line;
              _this.footer_second_line = _this.receive_records[0].footer_second_line;

              // page
              _this.pages = _this.receive_records[0].pages;
              _this.temp_pages = JSON.parse(JSON.stringify(_this.pages));

              // block_names(subtotal)
              _this.block_names = _this.receive_records[0].block_names;
              
              _this.subtotal = _this.receive_records[0].subtotal_info;
              _this.subtotal_novat_a = _this.receive_records[0].subtotal_novat_a;
              _this.subtotal_novat_b = _this.receive_records[0].subtotal_novat_b;

              // total
              _this.total = _this.receive_records[0].total_info;
              // get product_vat from total.vat
              _this.total.vat !== undefined ? _this.product_vat = _this.total.vat : _this.product_vat = '';

              _this.subtotal_info_not_show_a = _this.receive_records[0].subtotal_info_not_show_a;
              _this.subtotal_info_not_show_b = _this.receive_records[0].subtotal_info_not_show_b;
              _this.count_subtotal();

              // term
              _this.term = _this.receive_records[0].term_info;

              // term
              _this.payment_term = _this.receive_records[0].payment_term_info;

              // sig
              _this.sig = _this.receive_records[0].sig_info;

              _this.can_view = _this.receive_records[0].can_view;
              _this.can_duplicate = _this.receive_records[0].can_duplicate;

              // temp
              _this.id = _this.receive_records[0].id;
              _this.temp_first_line = _this.receive_records[0].first_line;
              _this.temp_second_line = _this.receive_records[0].second_line;
              _this.temp_project_category = _this.receive_records[0].project_category;
              _this.temp_quotation_no = _this.receive_records[0].quotation_no;
              _this.temp_quotation_date = _this.receive_records[0].quotation_date;

              _this.temp_prepare_for_first_line = _this.receive_records[0].prepare_for_first_line;
              _this.temp_prepare_for_second_line = _this.receive_records[0].prepare_for_second_line;
              _this.temp_prepare_for_third_line = _this.receive_records[0].prepare_for_third_line;

              _this.temp_prepare_by_first_line = _this.receive_records[0].prepare_by_first_line;
              _this.temp_prepare_by_second_line = _this.receive_records[0].prepare_by_second_line;
              _this.temp_prepare_by_third_line = _this.receive_records[0].prepare_by_third_line;

              // footer
              _this.temp_footer_first_line = _this.receive_records[0].footer_first_line;
              _this.temp_footer_second_line = _this.receive_records[0].footer_second_line;

              // product array
              _this.product_array = _this.receive_records[0].product_array;
              
              _this.temp_can_view = _this.receive_records[0].can_view;
              _this.temp_can_duplicate = _this.receive_records[0].can_duplicate;
            }
          })
          .catch(function(error) {
            console.log(error);
          });
  
      },

      count_subtotal() {
        if(this.total.total == '0.00')
        {
          //this.total.total = (this.subtotal * (1 - this.total.discount * 0.01));
          //if(this.total.vat == 'Y')
          //  this.total.total = (this.total.total * 1) + (this.subtotal_novat_a * 0.12);
          this.total.total = "";
        }
        else
          this.total.total = Number(this.total.total).toFixed(2);

        this.total.real_total = ((this.subtotal_info_not_show_a * 1 + this.subtotal_info_not_show_b * 1)  * (1 - this.total.discount * 0.01));

        if(this.total.vat == 'Y')
          this.total.real_total = (this.total.real_total * 1) + (this.subtotal_info_not_show_a * (1 - this.total.discount * 0.01) * 0.12);

          this.total.real_total = Number(this.total.real_total).toFixed(2);
    
      },

      add_page() {
        let order = 0;
        
        if(this.temp_pages.length != 0)
          order = Math.max.apply(Math, this.temp_pages.map(function(o) { return o.id; }))
          
        types = [];

        sig = {
          "page" : order + 1,
          "item_client" : [],
          "item_company" : [],
        }
        
        obj = {
          "id" : order + 1,
          "page" : order + 1,
          "sig" : sig,
          "term" : [],
          "payment_term" : [],
          "total" : [],
          "types" : types,
        }, 
  
        this.temp_pages.push(obj);
      },

      add_item(eid) {
        var element = this.temp_pages.find(({ id }) => id === eid);

        let obj_id = 0;

        if(element.types.length != 0)
          obj_id = Math.max.apply(Math, element.types.map(function(o) { return o.id; }))
        
        obj = {
          "id" : obj_id + 1,
          "org_id" : 0,
          "type" : document.getElementById('block_type_' + eid).value,
          "name" : "",
          "real_amount" : "0.0",
          "not_show" : "",
        }, 

        element.types.push(obj);
      },

      item_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.product_array.find(({ id }) => id === eid);
        this.product_array.splice(fromIndex, 1);
        this.product_array.splice(toIndex, 0, element);
      },

      item_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.product_array.length - 1) 
          return;
  
        var element = this.product_array.find(({ id }) => id === eid);
        this.product_array.splice(fromIndex, 1);
        this.product_array.splice(toIndex, 0, element);
      },

      page_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.temp_pages.find(({ id }) => id === eid);
        this.temp_pages.splice(fromIndex, 1);
        this.temp_pages.splice(toIndex, 0, element);
      },

      page_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.temp_pages.length - 1) 
          return;
  
        var element = this.temp_pages.find(({ id }) => id === eid);
        this.temp_pages.splice(fromIndex, 1);
        this.temp_pages.splice(toIndex, 0, element);
      },

      page_del: function(eid) {

        var index = this.temp_pages.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.temp_pages.splice(index, 1);
        }
      },

      set_up: function(pid, fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) toIndex = 0;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        page.types.splice(toIndex, 0, element);
      },

      set_up_page: function(pid, page_index, fromIndex, eid) {
        var toIndex = page_index - 1;
  
        if (toIndex < 0)
          return;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        this.temp_pages[toIndex].types.splice(this.temp_pages[toIndex].types.length - 1, 0, element);
      },
  
      set_down: function(pid, fromIndex, eid) {
        var toIndex = fromIndex + 1;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        if (toIndex > page.types.length - 1) toIndex = page.types.length - 1;
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        page.types.splice(toIndex, 0, element);
      },

      set_down_page: function(pid, page_index, fromIndex, eid) {
        var toIndex = page_index + 1;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        if (toIndex > page.types.length - 1) 
          return;
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        this.temp_pages[toIndex].types.splice(this.temp_pages[toIndex].types.length - 1, 0, element);
      },

      page_copy: async function(pid,  eid) {
        // page
        var page = this.temp_pages.find(({ id }) => id === pid);
        var element = page.types.find(({ id }) => id === eid);

        let obj_id = 0;

        if(page.types.length != 0)
          obj_id = Math.max.apply(Math, page.types.map(function(o) { return o.id; }))

        var obj = JSON.parse(JSON.stringify(element));
        obj.id = obj_id + 1
        obj.page_id = page.id;
       

     

        // subtotal
        // var block = this.block_names.find(({ id }) => id === eid);
        var block = this.block_names.filter(element => {
          return element.id === eid && element.page_id === pid;
        });

        var new_blocks = JSON.parse(JSON.stringify(block[0].blocks));

        for(var i = 0; i < new_blocks.length; i++) {
          new_blocks[i].type_id = obj_id + 1;
        }


        await this.page_copy_save(this.id, obj_id + 1, new_blocks, obj);

        this.reload();
      },

      del_block: function(pid, eid) {
        var page = this.temp_pages.find(({ id }) => id === pid);
        var index = page.types.findIndex(({ id }) => id === eid);
        if (index > -1) {
          page.types.splice(index, 1);
        }
      },

      header_save : function() {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);
        form_Data.append("first_line", this.first_line);
        form_Data.append("second_line", this.second_line);
        form_Data.append("project_category", this.project_category);
        form_Data.append("quotation_no", this.quotation_no);
        form_Data.append("quotation_date", this.quotation_date);
        form_Data.append("prepare_for_first_line", this.prepare_for_first_line);
        form_Data.append("prepare_for_second_line", this.prepare_for_second_line);
        form_Data.append("prepare_for_third_line", this.prepare_for_third_line);
        form_Data.append("prepare_by_first_line", this.prepare_by_first_line);
        form_Data.append("prepare_by_second_line", this.prepare_by_second_line);
        form_Data.append("prepare_by_third_line", this.prepare_by_third_line);
    
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_header_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_header_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },

      
      access_save : function() {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);

        form_Data.append("can_view", this.can_view);
        form_Data.append("can_duplicate", this.can_duplicate);
          
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_access_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_access_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },


      footer_save : function() {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);

        form_Data.append("footer_first_line", this.footer_first_line);
        form_Data.append("footer_second_line", this.footer_second_line);
          
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_footer_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_footer_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },

      page_copy_save: async function(id, type_id, temp_block, page) {

        console.log(type_id);

        var form_Data = new FormData();
        let _this = this;

        var token = localStorage.getItem("token");
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);

        form_Data.append("types", JSON.stringify(page));

        let res = await axios({
          method: 'post',
          url: 'api/quotation_page_update',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        type_id = res.data.message;

        console.log(type_id);
            
        var form_Data1 = new FormData();
        form_Data1.append("jwt", token);
 
        form_Data1.append("id", id);
        form_Data1.append("type_id", type_id);
        form_Data1.append("block", JSON.stringify(temp_block));
      
        for (var i = 0; i < temp_block.length; i++) {
          let file = document.getElementById('block_image_' + temp_block[i].id + '_1');
          if(file) {
            let f = file.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id + '_1', f);
          }

          let file2 = document.getElementById('block_image_' + temp_block[i].id + '_2');
          if(file2) {
            let f = file2.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id + '_2', f);
          }

          let file3 = document.getElementById('block_image_' + temp_block[i].id + '_3');
          if(file3) {
            let f = file3.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id + '_3', f);
          }
        }

        let res1 = await axios({
          method: 'post',
          url: 'api/quotation_page_type_block_update_copy',
          data: form_Data1,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

      },

      page_save_pre: async function() {
        let _this = this;
        let empty = true;
        // check if page is empty

        await this.get_latest_record();

        // check if this.temp_pages and this.temp_pages_verify identical
        if(JSON.stringify(this.pages ) != JSON.stringify(this.temp_pages_verify))
        {
          Swal.fire({
            text: "This quotation has been modified by other user. Please reload the page and try again.",
            icon: "info",
            confirmButtonText: "OK",
          });
          return;
        }

        for(var i = 0; i < this.temp_pages.length; i++) {
          if(this.temp_pages[i].types.length != 0){
            empty = false;
            break;
          }
        }

        if(this.temp_pages.length != 0)
          empty = false;
        
        if(empty)
        {
          Swal.fire({
            title: "WARNING",
            text: "If click yes, all the pages and subtotal blocks will be erased.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
          }).then((result) => {
            if (result.value) {
              _this.page_save();
            }
          });
        }
        else
          _this.page_save();
        },

      page_save : function() {
      
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);

        form_Data.append("first_line", this.first_line);
        form_Data.append("second_line", this.second_line);
        form_Data.append("project_category", this.project_category);
        form_Data.append("quotation_no", this.quotation_no);
        form_Data.append("quotation_date", this.quotation_date);
        form_Data.append("prepare_for_first_line", this.prepare_for_first_line);
        form_Data.append("prepare_for_second_line", this.prepare_for_second_line);
        form_Data.append("prepare_for_third_line", this.prepare_for_third_line);
        form_Data.append("prepare_by_first_line", this.prepare_by_first_line);
        form_Data.append("prepare_by_second_line", this.prepare_by_second_line);
        form_Data.append("prepare_by_third_line", this.prepare_by_third_line);

        form_Data.append("footer_first_line", this.footer_first_line);
        form_Data.append("footer_second_line", this.footer_second_line);

        form_Data.append("pages", JSON.stringify(this.temp_pages));
          
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },

      reload : function() {
        this.close_all();

        this.is_load = false;

        if(this.l_id == 0 && this.id != 0) 
        {
          this.l_id = this.id;
          this.filter_apply();
        }
        else
          this.getRecord();
      },

      product_catalog_a() {
        this.toggle_type = 'A';
        $('#modal_product_catalog').modal('toggle');
        $("#tag01").selectpicker("refresh");
      },

      product_catalog_b() {
        this.toggle_type = 'B';
        $('#modal_product_catalog').modal('toggle');
        $("#tag01").selectpicker("refresh");
    },

      close_all() {
        this.show_signature = false;
        this.show_footer = false;
        this.show_page = false;
        this.show_subtotal = false;
        this.show_total = false;
        this.show_term = false;
        this.show_payment_term = false;
        this.show_header = false;
        console.log("close all");
      },

      search() {
          this.filter_apply();
      },
  
    
      
      getLeaveCredit: function() {
        let _this = this;
  
        const params = {
          id: _this.title_id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/template_library", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            })
          .then(function(response) {
            console.log(response.data);
            _this.receive_records = response.data;
            _this.title_info = _this.receive_records.title;
            _this.template = _this.receive_records.template;
            _this.library = _this.receive_records.library;
  
          })
          .catch(function(error) {
            console.log(error);
          });
  
      },
  
      view_template:function(){
          if (this.title_id == 0) {
              Swal.fire({
                  text: "Please choose position to view template",
                  icon: "warning",
                  confirmButtonText: "OK",
                });
              return;
            }else
            {
              
              this.reset();
  
              this.getLeaveCredit();
              this.view_detail = true;
  
            }
          
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
  
        this.e_title = this.shallowCopy(
          this.position.find((element) => element.did == this.record.did)
        ).items;
  
        this.e_department = this.record.did;
        this.e_tid = this.record.tid;
  
        this.e_sn = this.record.agenda.length;
        this.e_sn1 = this.record.agenda1.length;
        this.e_sn2 = this.record.agenda2.length;
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
  
      scrollMeTo(refName) {
        var element = this.$refs[refName];
        element.scrollIntoView({ behavior: 'smooth' });
    
      },
  
      on_grade_change:function(event) {
          console.log(event.target.value);
          var grade = this.$refs.grade;
    
          var score =0.0;
          var cnt = 0;
          for (i = 0; i < grade.length; i++) {
            if(grade[i].value === 'N/A')
              score += 0;
            else
            {
              score += parseInt(grade[i].value);
              cnt += 1;
            }
          }
    
          if(cnt === 0)
            this.avg = 0;
          else
            this.avg = (score / cnt).toFixed(1);
    
        },
    
        on_grade1_change:function(event) {
          console.log(event.target.value);
          var grade = this.$refs.grade1;
    
          var score =0.0;
          var cnt = 0;
          for (i = 0; i < grade.length; i++) {
            if(grade[i].value === 'N/A')
              score += 0;
            else
            {
              score += parseInt(grade[i].value);
              cnt += 1;
            }
          }
    
          if(cnt === 0)
            this.avg1 = 0;
          else
            this.avg1 = (score / cnt).toFixed(1);
    
        },
  
        on_grade2_change:function(event) {
          console.log(event.target.value);
          var grade = this.$refs.grade2;
    
          var score =0.0;
          var cnt = 0;
          for (i = 0; i < grade.length; i++) {
            if(grade[i].value === 'N/A')
              score += 0;
            else
            {
              score += parseInt(grade[i].value);
              cnt += 1;
            }
          }
    
          if(cnt === 0)
            this.avg2 = 0;
          else
            this.avg2 = (score / cnt).toFixed(1);
    
        },
  
      reset: function() {
   
        this.receive_records = [];
        this.title_info = {};
        this.template = {};
        this.library = {};
  
        this.evals = {};
        this.avg = 10.0;
        this.avg1 = 10.0;
        this.avg2 = 10.0;
  
        this.view_detail = false;
      },
  
      shallowCopy(obj) {
        console.log("shallowCopy");
        var result = {};
        for (var i in obj) {
          result[i] = obj[i];
        }
        return result;
      },

      pre_page: function(){
        let tenPages = Math.floor((this.product_page - 1) / 10) + 1;
  
          this.product_page = parseInt(this.product_page) - 10;
          if(this.product_page < 1)
            this.product_page = 1;
   
          this.product_pages_10 = [];
  
          let from = tenPages * 10;
          let to = (tenPages + 1) * 10;
  
          this.product_pages_10 = this.product_pages.slice(from, to);
        
      },
  
      nex_page: function(){
        let tenPages = Math.floor((this.product_page - 1) / 10) + 1;
  
        this.product_page = parseInt(this.product_page) + 10;
        if(this.product_page > this.product_pages.length)
          this.product_page = this.product_pages.length;
  
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        let pages_10 = this.product_pages.slice(from, to);
  
        if(pages_10.length > 0)
          this.product_pages_10 = pages_10;
  
      },
  
      setPages() {
        console.log("setPages");
        this.product_pages = [];
        let numberOfPages = Math.ceil(this.product_total / this.perPage);
  
        if (numberOfPages == 1) this.product_page = 1;
        if (this.product_page < 1) this.product_page = 1;
        for (let index = 1; index <= numberOfPages; index++) {
          this.product_pages.push(index);
        }
      },
  
      paginate: function(posts) {
        console.log("paginate");
        if (this.product_page < 1) this.product_page = 1;
        if (this.product_page > this.product_pages.length) this.product_page = this.product_pages.length;
  
        let tenPages = Math.floor((this.product_page - 1) / 10);
        if(tenPages < 0)
          tenPages = 0;
        this.product_pages_10 = [];
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        
        this.product_pages_10 = this.product_pages.slice(from, to);
  
        return this.product_records;
      },

      change_v(){
        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3 && element.v4 == this.v4)
        )
  
        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            this.url = this.img_url + item_product.photo;
          else
            this.url = "";
          this.price_ntd = "NTD " + Number(item_product.price_ntd).toLocaleString();
          this.price = "PHP " + Number(item_product.price).toLocaleString();
          this.quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();
          this.phased = item_product.enabled == 0 ? 1 : 0;

          this.out = item_product.enabled == 1 ? "" : "Y";
          this.out_cnt = 0;

          if(this.product['out'] == 'Y')
          {
              this.out = "Y";
              this.out_cnt = 0;
          }

          this.last_order_name = this.product.last_order_name;
          this.last_order_at = this.product.last_order_at;
          this.last_order_url = this.product.last_order_url;

          this.product.last_order_name = item_product.last_order_name;
          this.product.last_order_at = item_product.last_order_at;
          this.product.last_order_url = item_product.last_order_url;
          this.last_have_spec = false;
          this.product.last_have_spec = false;

        }
        else
        {
          this.url = this.img_url + this.product['photo1'];
          this.price_ntd = this.product['price_ntd'];
          this.price = this.product['price'];
          this.quoted_price = this.product['quoted_price'];
          this.phased = 0;

          this.out = this.product['out'];
          this.out_cnt = this.product['phased_out_cnt'];

          this.product.last_order_name = this.last_order_name;
          this.product.last_order_at = this.last_order_at;
          this.product.last_order_url = this.last_order_url;

          this.last_order_name = "";
          this.last_order_at = "";
          this.last_order_url = "";

          this.product.last_order_url = "";
          this.last_have_spec = true;
          this.product.last_have_spec = true;
        }
  
      },

      last_order_info: function(info) {
        Swal.fire({
          title: "<h2><i>Last Order History</i></h2><br>",
          html: info,
          confirmButtonText: "Close",
        });
      },

      phased_out_info: function(info) {
        Swal.fire({
          title: "<i>Phased-out Variants:</i>", 
          html: info,  
          confirmButtonText: "Close", 
        });
      },

      replacement_info: function(info) {
        Swal.fire({
          title: "<i>Replacement Product:</i>", 
          html: info,  
          confirmButtonText: "Close", 
        });
      },

      export_excel() {
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
  
  
        axios({
          method: "post",
          url: "api/quotation_export_excel",
          data: form_Data,
          responseType: "blob",
        })
            .then(function(response) {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                 
                    link.setAttribute('download', 'Quotation Export_' + _this.quotation_no + '.xlsx');
                 
                  document.body.appendChild(link);
                  link.click();
  
            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });
      },

      
      cost_analysis() {
        if(this.name !== 'Dennis Lin' && this.name !== 'Ariel Lin' && this.name !== 'testmanager')
          return;
        
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
  
  
        axios({
          method: "post",
          url: "cost_analysis_quotation",
          data: form_Data,
          responseType: "blob",
        })
            .then(function(response) {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                 
                  link.setAttribute('download', 'Cost_Analysis_' + _this.quotation_no + '.xlsx');
                 
                  document.body.appendChild(link);
                  link.click();
  
            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });
      },

      print_me() {
 
        html2canvas(document.querySelector(".company_signature"), { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true}).then(canvas => {
          //document.body.appendChild(canvas)
          return Canvas2Image.saveAsPNG(canvas);

          //const el = this.$refs.printMe;
          // add option type to get the image version
          // if not provided the promise will return 
          // the canvas.
          // const options = {
          //   type: 'dataURL'
          // };
          // (async () => {
          //     html2canvas(document.querySelector('.specific'), {
          //       onrendered: function(canvas) {
          //         // document.body.appendChild(canvas);
          //         return Canvas2Image.saveAsPNG(canvas);
          //       }
          //     });
          // })()
        
      });
      
      /*
        const options = {
          type: 'dataURL'
        };
        (async () => {
            html2canvas(document.querySelector('.company_signature'), {
              useCORS: true,
              allowTaint : true,
              onrendered: function(canvas) {
                //document.body.appendChild(canvas);
                //return Canvas2Image.saveAsPNG(canvas);

                const context = canvas.getContext('2d');
                context.mozImageSmoothingEnabled = false;
                context.webkitImageSmoothingEnabled = false;
                context.msImageSmoothingEnabled = false;
                context.imageSmoothingEnabled = false;
                const src64 = canvas.toDataURL();
                const newImg = document.createElement('img');
                newImg.crossOrigin = 'Anonymous';
                newImg.src = src64;
                document.body.appendChild(newImg);
              },
              logging:true
            });
        })()

*/


      },

      change_url_set: function(set, uid) {
        if(uid == 1)
          set.url = set.url1;
        if(uid == 2)
          set.url = set.url2;
        if(uid == 3)
          set.url = set.url3;
    },
  
    PhaseOutAlert_set(phased_out_text){
      hl = "";
      for(var i = 0; i < phased_out_text.length; i++)
      {
        hl += "(" + Number(i+1) + ") " + phased_out_text[i] + "<br/>";
      }
  
      Swal.fire({
        title: 'Phased Out Variants:',
        html: hl,
        confirmButtonText: 'OK',
        });
      
    },
  
    change_v_set(set){
      let item_product = this.shallowCopy(
        set.variation_product.find((element) => element.v1 == set.v1 && element.v2 == set.v2 && element.v3 == set.v3 && element.v4 == set.v4)
      )
    
      if(item_product.id != undefined)
      {
        if(item_product.photo != "")
        set.url = this.img_url + item_product.photo;
        else
        set.url = "";
        set.price_ntd = item_product.currency + " " + Number(item_product.price_ntd).toLocaleString();
        set.price = "PHP " + Number(item_product.price).toLocaleString();
        set.quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();
    
        set.str_price_ntd_change = (item_product.price_ntd_change != "" ? "(" + item_product.price_ntd_change + ")" : "");
        set.str_price_change = (item_product.price_change != "" ? "(" + item_product.price_change + ")" : "");
        set.str_quoted_price_change = (item_product.quoted_price_change != "" ? "(" + item_product.quoted_price_change + ")" : "");
    
        set.phased_out = (item_product.enabled == 0 ? "F" : "");
    
        set.sheet_url = 'product_spec_sheet?sd=' + set.pid + '&d=' + item_product.id;
    
        set.out = item_product.enabled == 1 ? "" : "Y";
        set.out_cnt = 0;
    
        if(set.record[0]['out'] == 'Y')
        {
          set.out = "Y";
          set.out_cnt = 0;
        }

        set.last_order_name = item_product.last_order_name;
        set.last_order_at = item_product.last_order_at;
        set.last_order_url = item_product.last_order_url;
        set.last_have_spec = false;
      }
      else
      {
        set.url = set.url1;
        set.price_ntd = set.record[0]['price_ntd'];
        set.price = set.record[0]['price'];
        set.quoted_price = set.record[0]['quoted_price'];
    
        set.str_price_ntd_change = set.record[0]['str_price_ntd_change'];
        set.str_price_change = set.record[0]['str_price_change'];
        set.str_quoted_price_change = set.record[0]['str_quoted_price_change'];
    
        set.phased_out = "";
    
        set.sheet_url = 'product_spec_sheet?sd=' + set.pid;
    
        set.out = set.record[0]['out'];
        set.out_cnt = set.record[0]['phased_out_cnt'];

        set.last_order_name = "";
      set.last_order_at = "";
      set.last_order_url = "";
      set.last_have_spec = true;
      }
    
      this.check_all_set();
    
    },
    
    check_all_set(){
      let change = true;
      let price_ntd = 0;
      let price = 0;
      let quoted_price = 0;
    
      for(var i=0; i < this.product_set.length; i++){
        let item_product = this.shallowCopy(
          this.product_set[i].variation_product.find((element) => element.v1 == this.product_set[i].v1 && element.v2 == this.product_set[i].v2 && element.v3 == this.product_set[i].v3 && element.v4 == this.product_set[i].v4)
        )
    
        if(item_product.id != undefined)
        {
          price_ntd += item_product.price_ntd * 1;
          price += item_product.price * 1;
          quoted_price += item_product.quoted_price * 1;
        }
        else
          change = false;
      }
    
      if(change)
      {
        //this.price_ntd = price_ntd;
        this.product.price = "PHP " + Number(price).toLocaleString();;
        this.product.quoted_price = "PHP " + Number(quoted_price).toLocaleString();
      }
      else
      {
        this.product.price = this.price;
          this.product.quoted_price = this.quoted_price;
      }
    },
  
    
    add_with_image_set_select(all) {
      let change = true;
      let price_ntd = 0;
      let price = 0;
      let quoted_price = 0;
      let qty = 0;
      let srp = 0;

      let list = "";
      let ps_var = "";

      let sets = [];
    
      for(var i=0; i < this.product_set.length; i++){
        let item_product = this.shallowCopy(
          this.product_set[i].variation_product.find((element) => element.v1 == this.product_set[i].v1 && element.v2 == this.product_set[i].v2 && element.v3 == this.product_set[i].v3 && element.v4 == this.product_set[i].v4)
        )

        var list_g = "";

        for(var j=0; j<this.product_set[i].specification.length; j++)
        {
            if(this.product_set[i].specification[j].k1 !== '')
              list_g += this.product_set[i].specification[j].k1 + ': ' + this.product_set[i].specification[j].v1 + "\n";
            if(this.product_set[i].specification[j].k2 !== '')
              list_g += this.product_set[i].specification[j].k2 + ': ' + this.product_set[i].specification[j].v2 + "\n";
        }

        // add phased out information
        if((this.product_set[i].phased_out_cnt > 0 && this.phased == 1) || (this.product_set[i].phased_out_cnt > 0 && all == 'all'))
        {
          list_g += "\n";
          list_g += "Phased-out Variants:\n";
          list_g += this.product_set[i].phased_out_text.split("<br/>").join("\n");
        }
        
    
        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            this.product_set[i].photo = item_product.photo;

          price_ntd += item_product.price_ntd * 1;
          price += item_product.price * 1;
          quoted_price += item_product.quoted_price * 1;
          qty += this.product_set[i].qty * 1;

          srp = quoted_price != 0 ? quoted_price : price;

          ps_var = ('id: ' + this.product_set[i].id) + "\n";

          if(item_product.v1 != ""){
            list += (item_product.k1 + ': ' + item_product.v1) + "\n";
            ps_var += (item_product.k1 + ': ' + item_product.v1) + "\n";
          }
          if(item_product.v2 != ""){
            list += (item_product.k2 + ': ' + item_product.v2) + "\n";
            ps_var += (item_product.k2 + ': ' + item_product.v2) + "\n";
          }
          if(item_product.v3 != ""){
            list += (item_product.k3 + ': ' + item_product.v3) + "\n";
            ps_var += (item_product.k3 + ': ' + item_product.v3) + "\n";
          }
          if(item_product.v4 != ""){
            list += (item_product.k4 + ': ' + item_product.v4) + "\n";
            ps_var += (item_product.k4 + ': ' + item_product.v4) + "\n";
          }

          sets.push(ps_var);

          list += list_g;

          list += "\n";

        }
        else
          change = false;
      }
    
      if(change)
      {
        list.replace(/\n+$/, "");

        var block_a_image = 'image';
        var sn = 0;
        if(this.toggle_type == 'A')
          var items = this.temp_block_a;

        if(this.toggle_type == 'B')
          var items = this.temp_block_b;

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        item = {
          id: sn,
          url: this.product_set[0] != undefined ? this.product_set[0].url : "",
          url2: this.product_set[1] != undefined ? this.product_set[1].url : "",
          url3: this.product_set[2] != undefined ? this.product_set[2].url : "",
          file: {
            name: "",
          },
          type : block_a_image,
          code: this.product.code,
          photo: this.product_set[0] != undefined ? this.product_set[0].photo : "",
          photo2: this.product_set[1] != undefined ? this.product_set[1].photo : "",
          photo3: this.product_set[2] != undefined ? this.product_set[2].photo : "",
          qty: 1,
          price: srp,
          srp: quoted_price,
          discount: "0",
          amount: srp,
          desc: "",
          list: list,
          num:"",
          notes: "",
          ratio:1.0,
          pid: this.product.id,
          v1: "",
          v2: "",
          v3: "",
          v4: "",

          ps_var : sets,
        };

      }
      else{
        alert('Please choose option for each attribute of every sub-product');
        return;
      }
    
      items.push(item);
      alert('Add Successfully');
    },

    
    add_without_image_set_select(all) {
      let change = true;
      let price_ntd = 0;
      let price = 0;
      let quoted_price = 0;
      let qty = 0;
      let srp = 0;

      let list = "";
      let ps_var = "";

      let sets = [];
    
      for(var i=0; i < this.product_set.length; i++){
        let item_product = this.shallowCopy(
          this.product_set[i].variation_product.find((element) => element.v1 == this.product_set[i].v1 && element.v2 == this.product_set[i].v2 && element.v3 == this.product_set[i].v3 && element.v4 == this.product_set[i].v4)
        )

        var list_g = "";
 
        for(var j=0; j<this.product_set[i].specification.length; j++)
        {
            if(this.product_set[i].specification[j].k1 !== '')
              list_g += this.product_set[i].specification[j].k1 + ': ' + this.product_set[i].specification[j].v1 + "\n";
            if(this.product_set[i].specification[j].k2 !== '')
              list_g += this.product_set[i].specification[j].k2 + ': ' + this.product_set[i].specification[j].v2 + "\n";
        }

        // add phased out information
        if((this.product_set[i].phased_out_cnt > 0 && this.phased == 1) || (this.product_set[i].phased_out_cnt > 0 && all == 'all'))
        {
          list_g += "\n";
          list_g += "Phased-out Variants:\n";
          list_g += this.product_set[i].phased_out_text.split("<br/>").join("\n");
        }
    
        if(item_product.id != undefined)
        {
          price_ntd += item_product.price_ntd * 1;
          price += item_product.price * 1;
          quoted_price += item_product.quoted_price * 1;
          qty += this.product_set[i].qty * 1;

          srp = quoted_price != 0 ? quoted_price : price;

          ps_var = ('id: ' + this.product_set[i].id) + "\n";

          if(item_product.v1 != ""){
            list += (item_product.k1 + ': ' + item_product.v1) + "\n";
            ps_var += (item_product.k1 + ': ' + item_product.v1) + "\n";
          }
          if(item_product.v2 != ""){
            list += (item_product.k2 + ': ' + item_product.v2) + "\n";
            ps_var += (item_product.k2 + ': ' + item_product.v2) + "\n";
          }
          if(item_product.v3 != ""){
            list += (item_product.k3 + ': ' + item_product.v3) + "\n";
            ps_var += (item_product.k3 + ': ' + item_product.v3) + "\n";
          }
          if(item_product.v4 != ""){
            list += (item_product.k4 + ': ' + item_product.v4) + "\n";
            ps_var += (item_product.k4 + ': ' + item_product.v4) + "\n";
          }

          sets.push(ps_var);

          list += list_g;

          list += "\n";

        }
        else
          change = false;
      }
    
      if(change)
      {

        list.replace(/\n+$/, "");

        var block_a_image = 'noimage';
        var sn = 0;
        if(this.toggle_type == 'A')
          var items = this.temp_block_a;

        if(this.toggle_type == 'B')
          var items = this.temp_block_b;

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        item = {
          id: sn,
          url: "",
          url2: "",
          url3: "",
          file: {
            name: "",
          },
          type : block_a_image,
          code: this.product.code,
          photo: "",
          qty: 1,
          price: srp,
          srp: quoted_price,
          discount: "0",
          amount: srp,
          desc: "",
          list: list,
          num:"",
          notes: "",
          ratio:1.0,
          pid: this.product.id,
          v1: "",
          v2: "",
          v3: "",
          v4: "",

          ps_var : sets,
        };

      }
      else{
        alert('Please choose option for each attribute of every sub-product');
        return;
      }

      items.push(item);
      alert('Add Successfully');
    
    },
  
    
    set_up_specification_set(set) {
      let k1 = '';
      let k2 = '';
  
      let v1 = '';
      let v2 = '';
  
     for(var i=0; i < set.special_infomation.length; i++)
     {
       if(set.special_infomation[i].value != "")
       {
         if(k1 == "")
         {
           k1 = set.special_infomation[i].category;
           v1 = set.special_infomation[i].value;
         }else if(k1 !== "" && k2 == "")
         {
           k2 = set.special_infomation[i].category;
           v2 = set.special_infomation[i].value;
  
           obj = {k1: k1, v1: v1, k2: k2, v2: v2};
           set.specification.push(obj);
           k1  = '';
           k2  = '';
           v1  = '';
           v2  = '';
         }
       }
     }
  
     if(k1 == "" && set.record[0]['moq'] !== "")
     {
       k1 = 'MOQ';
       v1 = set.record[0]['moq'];
     }else if(k1 !== "" && k2 == "" && set.record[0]['moq'] !== "")
     {
       k2 = 'MOQ';
       v2 = set.record[0]['moq'];
   
       obj = {k1: k1, v1: v1, k2: k2, v2: v2};
       set.specification.push(obj);
       k1  = '';
       k2  = '';
       v1  = '';
       v2  = '';
     }
  /*
     if(k1 == "" && this.record[0]['notes'] !== "")
     {
       k1 = 'Notes';
       v1 = this.record[0]['notes'];
     }else if(k1 !== "" && k2 == "" && this.record[0]['notes'] !== "")
     {
       k2 = 'Notes';
       v2 = this.record[0]['notes'];
   
       obj = {k1: k1, v1: v1, k2: k2, v2: v2};
       this.specification.push(obj);
       k1  = '';
       k2  = '';
       v1  = '';
       v2  = '';
     }
  */
     if(k1 !== "" && k2 == "")
     {
       k2 = '';
       v2 = '';
   
       obj = {k1: k1, v1: v1, k2: k2, v2: v2};
       set.specification.push(obj);
     
     }
   },

   add_with_image_set(set, all) {

    var photo = "";
    var photo2 = "";
    var photo3 = "";

    var price = "";
    var list = "";

    var srp = 0;

    let item_product = this.shallowCopy(
      set.product.find((element) => element.v1 == set.v1 && element.v2 == set.v2 && element.v3 == set.v3 && element.v4 == set.v4)
    )

    if(set.product.length > 0 && item_product.id == undefined && all != 'all') {
      alert('Please choose an option for each attribute');
      return;
    }

    if(item_product.id != undefined)
    {
      if(item_product.photo != "")
        photo = item_product.photo;

        photo2 = set.photo2;
        photo3 = set.photo3;

        // price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
        price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
        srp =  Number(item_product.price);
        if(set.v1 != "")
          list += (item_product.k1 + ': ' + item_product.v1) + "\n";
        if(set.v2 != "")
          list += (item_product.k2 + ': ' + item_product.v2) + "\n";
        if(set.v3 != "")
          list += (item_product.k3 + ': ' + item_product.v3) + "\n";
    }
    else
    {
      photo = set.photo1;
      photo2 = set.photo2;
      photo3 = set.photo3;
      // price = set.price_org !== null ? set.price_org : set.quoted_price_org;
      price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
      srp = Number(set.price_org);
      list = "";
    }

    if(all == 'all')
    {
      list = "";
      var k1, k2, k3, k4;
      k1 = set.variation1 === "custom" ? set.variation1_custom : set.variation1;
      k2 = set.variation2 === "custom" ? set.variation2_custom : set.variation2;
      k3 = set.variation3 === "custom" ? set.variation3_custom : set.variation3;
      k4 = set.variation4 === "custom" ? set.variation4_custom : set.variation4;

      if(k1 !== '')
        list += set.variation1 === "custom" ? set.variation1_custom + ': ' + set.variation1_value.join(', ') + "\n" : set.variation1 + ': ' + set.variation1_value.join(', ') + "\n";
      if(k2 !== '')
        list += set.variation2 === "custom" ? set.variation2_custom + ': ' + set.variation2_value.join(', ') + "\n" : set.variation2 + ': ' + set.variation2_value.join(', ') + "\n";
      if(k3 !== '')
        list += set.variation3 === "custom" ? set.variation3_custom + ': ' + set.variation3_value.join(', ') + "\n" : set.variation3 + ': ' + set.variation3_value.join(', ') + "\n";
      if(k4 !== '')
        list += set.variation4 === "custom" ? set.variation4_custom + ': ' + set.variation4_value.join(', ') + "\n" : set.variation4 + ': ' + set.variation4_value.join(', ') + "\n";

      photo = set.photo1;
      photo2 = set.photo2;
      photo3 = set.photo3;

      if(set.srp !== null || set.srp_quoted !== null)
        price = set.srp_quoted !== null ? set.srp_quoted : set.srp;

      srp = set.srp;
        //price = set.srp !== null ? set.srp : set.srp_quoted;

      if(price == null)
        //price = set.price_org !== null ? set.price_org : set.quoted_price_org;
        price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
        
    }

    for(var i=0; i<set.specification.length; i++)
    {
        if(set.specification[i].k1 !== '')
          list += set.specification[i].k1 + ': ' + set.specification[i].v1 + "\n";
        if(set.specification[i].k2 !== '')
          list += set.specification[i].k2 + ': ' + set.specification[i].v2 + "\n";
    }

    // add phased out information
    if((set.phased_out_cnt > 0 && set.phased == 1) || (set.phased_out_cnt > 0 && all == 'all'))
    {
      // if string or is string array
      if(typeof set.phased_out_text === 'string' || set.phased_out_text instanceof String)
      {
        list += "\n";
        list += "Phased-out Variants:\n";
        list += set.phased_out_text.split("<br/>").join("\n");
      }
      else if(Array.isArray(set.phased_out_text))
      {
        for(var i=0; i<set.phased_out_text.length; i++)
        {
          list += "\n";
          list += "Phased-out Variants:\n";
          list += set.phased_out_text[i].split("<br/>").join("\n");
        }
      }
      else
      {
        list += "\n";
        list += "Phased-out Variants:\n";
        list += set.phased_out_text.split("<br/>").join("\n");
      }
      
    }

    if(price == null)
      price = set.srp_quoted !== 0 ?  set.srp_quoted : set.srp;
      // price = set.srp !== 0 ?  set.srp : set.srp_quoted;

    if(srp == null)
      srp = 0;

    var block_a_image = 'image';
    var sn = 0;
    if(this.toggle_type == 'A')
      var items = this.temp_block_a;

    if(this.toggle_type == 'B')
      var items = this.temp_block_b;

    for (let i = 0; i < items.length; i++) {
      if (items[i].id > sn) {
        sn = items[i].id;
      }
    }

    list.replace(/\n+$/, "");
    sn = sn + 1;

    if(this.toggle_type == 'A')
    {
      item = {
        id: sn,
        url: photo !== '' ? this.img_url + photo  : '',
        url2 : photo2 !== '' ? this.img_url + photo2 : '',
        url3 : photo3 !== '' ? this.img_url + photo3 : '',

        file: {
          name: "",
        },
        type : block_a_image,
        code: set.code,
        photo: photo,
        photo2: photo2,
        photo3: photo3,
        qty: set.qty,
        price: price,
        srp: srp,
        discount: "0",
        amount: "",
        desc: "",
        list: list,
        num:"",
        ratio:1.0,
        notes: "",
        pid: set.id,
        v1: all == 'all' ? '' : set.v1,
        v2: all == 'all' ? '' : set.v2,
        v3: all == 'all' ? '' : set.v3,
        v4: all == 'all' ? '' : set.v4,
      };
    }

    if(this.toggle_type == 'B')
    {
      item = {
        id: sn,
        
        url:  '',
        url2 : '',
        url3 : '',

        code: "",
        photo: photo,
        qty: "1",
        price: price,
        srp: srp,
        ratio:1.0,
        discount: "0",
        amount: "",
        desc: "",
        list: list,
        num:"",
        notes: "",
        pid: set.id,
        v1: all == 'all' ? '' : set.v1,
        v2: all == 'all' ? '' : set.v2,
        v3: all == 'all' ? '' : set.v3,
        v4: all == 'all' ? '' : set.v4,
      };
    }

    items.push(item);

    alert('Add Successfully');
  },

  add_without_image_set(set, all) {

    var photo = "";
    var price = "";
    var list = "";

    var srp = 0;

    let item_product = this.shallowCopy(
      set.product.find((element) => element.v1 == set.v1 && element.v2 == set.v2 && element.v3 == set.v3 && element.v4 == set.v4)
    )

    if(set.product.length > 0 && item_product.id == undefined && all != 'all') {
      alert('Please choose an option for each attribute');
      return;
    }

    if(item_product.id != undefined)
    {
      if(item_product.photo != "")
        photo = item_product.photo;
        //price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
        price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
        srp =  Number(item_product.price);
        if(set.v1 != "")
          list += (item_product.k1 + ': ' + item_product.v1) + "\n";
        if(set.v2 != "")
          list += (item_product.k2 + ': ' + item_product.v2) + "\n";
        if(set.v3 != "")
          list += (item_product.k3 + ': ' + item_product.v3) + "\n";
        if(set.v4 != "")
          list += (item_product.k4 + ': ' + item_product.v4) + "\n";
    }
    else
    {
      photo = set.photo1;
      //price = set.price_org !== null ? set.price_org : set.quoted_price_org;
      price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
      srp = Number(set.price_org);
      list = "";
    }

    if(all == 'all')
    {
      list = "";
      var k1, k2, k3, k4;
      k1 = set.variation1 === "custom" ? set.variation1_custom : set.variation1;
      k2 = set.variation2 === "custom" ? set.variation2_custom : set.variation2;
      k3 = set.variation3 === "custom" ? set.variation3_custom : set.variation3;
      k4 = set.variation4 === "custom" ? set.variation4_custom : set.variation4;

      if(k1 !== '')
        list += set.variation1 === "custom" ? set.variation1_custom + ': ' + set.variation1_value.join(', ') + "\n" : set.variation1 + ': ' + set.variation1_value.join(', ') + "\n";
      if(k2 !== '')
        list += set.variation2 === "custom" ? set.variation2_custom + ': ' + set.variation2_value.join(', ') + "\n" : set.variation2 + ': ' + set.variation2_value.join(', ') + "\n";
      if(k3 !== '')
        list += set.variation3 === "custom" ? set.variation3_custom + ': ' + set.variation3_value.join(', ') + "\n" : set.variation3 + ': ' + set.variation3_value.join(', ') + "\n";
      if(k4 !== '')
        list += set.variation4 === "custom" ? set.variation4_custom + ': ' + set.variation4_value.join(', ') + "\n" : set.variation4 + ': ' + set.variation4_value.join(', ') + "\n";

      photo = set.photo1;

      if(set.srp !== null || set.srp_quoted !== null)
        price = set.srp_quoted !== null ? set.srp_quoted : set.srp;
   
        //price = set.srp !== null ? set.srp : set.srp_quoted;

      if(price == null)
        //price = set.price_org !== null ? set.price_org : set.quoted_price_org;
        price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
        
        
      srp = set.srp;
    }

    if(price == null)
      price = set.srp_quoted !== 0 ?  set.srp_quoted : set.srp;
      //price = set.srp !== 0 ?  set.srp : set.srp_quoted;

    if(srp == null)
      srp = 0;

    for(var i=0; i<set.specification.length; i++)
    {
        if(set.specification[i].k1 !== '')
          list += set.specification[i].k1 + ': ' + set.specification[i].v1 + "\n";
        if(set.specification[i].k2 !== '')
          list += set.specification[i].k2 + ': ' + set.specification[i].v2 + "\n";
    }

    // add phased out information
    if((set.phased_out_cnt > 0 && set.phased == 1) || (set.phased_out_cnt > 0 && all == 'all'))
    {
      // if string or is string array
      if(typeof set.phased_out_text === 'string' || set.phased_out_text instanceof String)
      {
        list += "\n";
        list += "Phased-out Variants:\n";
        list += set.phased_out_text.split("<br/>").join("\n");
      }
      else if(Array.isArray(set.phased_out_text))
      {
        for(var i=0; i<set.phased_out_text.length; i++)
        {
          list += "\n";
          list += "Phased-out Variants:\n";
          list += set.phased_out_text[i].split("<br/>").join("\n");
        }
      }
      else
      {
        list += "\n";
        list += "Phased-out Variants:\n";
        list += set.phased_out_text.split("<br/>").join("\n");
      }
    }

    list.replace(/\n+$/, "");

    var block_a_image = 'noimage';
    var sn = 0;
    if(this.toggle_type == 'A')
      var items = this.temp_block_a;

    if(this.toggle_type == 'B')
      var items = this.temp_block_b;

    for (let i = 0; i < items.length; i++) {
      if (items[i].id > sn) {
        sn = items[i].id;
      }
    }

    sn = sn + 1;

    if(this.toggle_type == 'A')
    {
      item = {
        id: sn,
        url: "",
        url2: "",
        url3: "",
        file: {
          name: "",
        },
        type : block_a_image,
        code: set.code,
        photo: "",
        qty: set.qty,
        price: price,
        srp: srp,
        discount: "0",
        amount: "",
        desc: "",
        list: list,
        num:"",
        notes: "",
        ratio:1.0,
        pid: set.id,
        v1: all == 'all' ? '' : set.v1,
        v2: all == 'all' ? '' : set.v2,
        v3: all == 'all' ? '' : set.v3,
        v4: all == 'all' ? '' : set.v4,
      };
    }

    if(this.toggle_type == 'B')
    {
      item = {
        id: sn,
        
        url: '',
        url2 : '',
        url3 : '',

        code: set.code,
        photo: "",
        qty: "1",
        price: price,
        srp: srp,
        ratio:1.0,
        discount: "0",
        amount: "",
        desc: "",
        list: list,
        num:"",
        notes: "",
        pid: set.id,
        v1: all == 'all' ? '' : set.v1,
        v2: all == 'all' ? '' : set.v2,
        v3: all == 'all' ? '' : set.v3,
        v4: all == 'all' ? '' : set.v4,
      };
    }

    items.push(item);
    alert('Add Successfully');
  },
  

    }
  
  });
  