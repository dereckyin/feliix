var app = new Vue({
  el: "#app",
  data: {
    
    submit: false,

    baseURL: "https://storage.cloud.google.com/feliiximg/",

    category: "",
    sub_category: "",
    sub_category_name: "",
    sub_cateory_item: [],

    //
    id:-1,
    record: [],
    tags: [],

    cateory_item: [],

    special_infomation: [],
    special_infomation_detail: [],

    accessory_infomation: [],
    sub_accessory_item: [],
    attributes:[],

    //
    accessory_item: [],

    edit_mode: true,

    title: [],

    title_id: 0,

    url: null,
    url1: null,
    url2: null,
    url3: null,

    // data
    pid : "",
    brand: "",
    code: "",
    price_ntd: "",
    price: "",
    price_quoted: "",
    price_ntd_change: "",
    price_change: "",
    price_ntd_org: "",
    price_org: "",
    description: "",
    notes: "",
    accessory_mode: false,
    variation_mode: false,

    quoted_price:"",
    quoted_price_org:"",
    quoted_price_change:"",
    moq:"",
    currency: "",

    str_quoted_price_change: "",
    str_price_ntd_change: "",
    str_price_change: "",

    phased_out: "",

    sheet_url: "",

    // accessory

    // variation
    variation1: "",
    variation2: "",
    variation3: "",
    variation1_custom: "",
    variation2_custom: "",
    variation3_custom: "",

    variation1_text: "1st Variation",
    variation2_text: "2nd Variation",
    variation3_text: "3rd Variation",

    variation1_value: [],
    variation2_value: [],
    variation3_value: [],

    variation_product: [],

    related_product : [],
    nColumns: 4,
    groupedItems: [],

    show_accessory: false,

    v1:"",
    v2:"",
    v3:"",

    // bulk insert
    code_checked:'',
    bulk_code:'',
    price_ntd_checked:'',
    bulk_price_ntd:'',
    price_ntd_action :'',
    price_checked:'',
    bulk_price:'',
    price_action:'',
    image_checked:'',
    bulk_url:'',
    status_checked:'',
    bulk_status:'',
    currency:'',

    submit: false,

    specification: [],

    // info
    name :"",
    title: "",
    is_manager: "",

    toggle: true,

    print_pid: 'true',
    print_brand: 'true',
    print_srp: 'true',
    print_qp: 'true',

    out : "",
    out_cnt : 0,
    phased_out_text : [],
  },

  created() {
    let _this = this;
    let uri = window.location.href.split('?');

    if (uri.length >= 2)
    {
      let vars = uri[1].split('&');
      
      let tmp = '';
      vars.forEach(function(v){
        tmp = v.split('=');
        if(tmp.length == 2)
        {
          switch (tmp[0]) {
            case "id":
              id = tmp[1];
              _this.id = id;
              
              break;
   
            default:
              console.log(`Too many args`);
          }
          //_this.proof_id = tmp[1];
        }
      });
    }

    this.get_records(this.id);
    this.getUserName();
    this.load_print_option();
  },

  computed: {
    show_ntd : function() {
      if(this.name.toLowerCase() ==='dereck' || this.name.toLowerCase() ==='ariel lin' || this.name.toLowerCase() ==='kuan')
       return true;
      else
      return false;
    }
  },

  mounted() {
   
  },

  watch: {
    price_ntd() {
      if(this.price_ntd != this.price_ntd_org)
        this.price_ntd_change = new Date().toISOString().slice(0, 10);
    },

    price () {
      if(this.price != this.price_org)
        this.price_change = new Date().toISOString().slice(0, 10);
    },

    quoted_price () {
      if(this.quoted_price != this.quoted_price_org)
        this.quoted_price_change = new Date().toISOString().slice(0, 10);
    },

    accessory_infomation() {
      for(var i = 0; i < this.accessory_infomation.length; i++)
      {
        $('#tag' + i).selectpicker('refresh');
      } 
    },
  },

  methods: {
    toggle_price : function() {
      this.toggle = !this.toggle;
    },

    chunk: function(arr, size) {
      var newArr = [];
      for (var i=0; i<arr.length; i+=size) {
        newArr.push(arr.slice(i, i+size));
      }
      this.groupedItems  = newArr;
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

    search() {
      this.filter_apply();
    },

    edit_category() {
      this.edit_mode = true;

      console.log("edit category");
    },

    PhaseOutAlert(){
      hl = "";
      for(var i = 0; i < this.phased_out_text.length; i++)
      {
        hl += "(" + Number(i+1) + ") " + this.phased_out_text[i] + "<br/>";
      }

      Swal.fire({
        title: 'Phased Out Variants:',
        html: hl,
        confirmButtonText: 'OK',
        });
      
    },

    RelatedPhaseOutAlert(phased_out_text){
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

    change_v(){
      let item_product = this.shallowCopy(
        this.variation_product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3)
      )

      if(item_product.id != undefined)
      {
        if(item_product.photo != "")
          this.url = this.baseURL + item_product.photo;
        else
          this.url = "";
        this.price_ntd = item_product.currency + " " + Number(item_product.price_ntd).toLocaleString();
        this.price = "PHP " + Number(item_product.price).toLocaleString();
        this.quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();

        this.str_price_ntd_change = (item_product.price_ntd_change != "" ? "(" + item_product.price_ntd_change + ")" : "");
        this.str_price_change = (item_product.price_change != "" ? "(" + item_product.price_change + ")" : "");
        this.str_quoted_price_change = (item_product.quoted_price_change != "" ? "(" + item_product.quoted_price_change + ")" : "");

        this.phased_out = (item_product.enabled == 0 ? "F" : "");

        this.sheet_url = 'product_spec_sheet?sd=' + this.pid + '&d=' + item_product.id;

        this.out = item_product.enabled == 1 ? "" : "Y";
        this.out_cnt = 0;

        if(this.record[0]['out'] == 'Y')
        {
          this.out = "Y";
          this.out_cnt = 0;
        }
      }
      else
      {
        this.url = this.url1;
        this.price_ntd = this.record[0]['price_ntd'];
        this.price = this.record[0]['price'];
        this.quoted_price = this.record[0]['quoted_price'];

        this.str_price_ntd_change = this.record[0]['str_price_ntd_change'];
        this.str_price_change = this.record[0]['str_price_change'];
        this.str_quoted_price_change = this.record[0]['str_quoted_price_change'];

        this.phased_out = "";

        this.sheet_url = 'product_spec_sheet?sd=' + this.pid;

        this.out = this.record[0]['out'];
        this.out_cnt = this.record[0]['phased_out_cnt'];
      }

    },

    goto_sheet(){
      window.open(this.sheet_url, '_blank');
    },

    set_up_specification() {
       let k1 = '';
       let k2 = '';

       let v1 = '';
       let v2 = '';

      for(var i=0; i < this.special_infomation.length; i++)
      {
        if(this.special_infomation[i].value != "")
        {
          if(k1 == "")
          {
            k1 = this.special_infomation[i].category;
            v1 = this.special_infomation[i].value;
          }else if(k1 !== "" && k2 == "")
          {
            k2 = this.special_infomation[i].category;
            v2 = this.special_infomation[i].value;

            obj = {k1: k1, v1: v1, k2: k2, v2: v2};
            this.specification.push(obj);
            k1  = '';
            k2  = '';
            v1  = '';
            v2  = '';
          }
        }
      }

      if(k1 == "" && this.record[0]['moq'] !== "")
      {
        k1 = 'MOQ';
        v1 = this.record[0]['moq'];
      }else if(k1 !== "" && k2 == "" && this.record[0]['moq'] !== "")
      {
        k2 = 'MOQ';
        v2 = this.record[0]['moq'];
    
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

    set_up_variants() {
      for(var i=0; i<this.variation1_value.length; i++)
      {
        $('#variation1_value').tagsinput('add', this.variation1_value[i]);
      }
      for(var i=0; i<this.variation2_value.length; i++)
      {
        $('#variation2_value').tagsinput('add', this.variation2_value[i]);
      }
      for(var i=0; i<this.variation3_value.length; i++)
      {
        $('#variation3_value').tagsinput('add', this.variation3_value[i]);
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

    change_url: function(url) {
        this.url = url;
    },

    get_records: function(id) {
        let _this = this;

        if(id === -1)
            return;

        const params = {
          id: this.id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/product_display_code", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
            _this.record = response.data;

            _this.category = _this.record[0]['category'];
            _this.sub_category = _this.record[0]['sub_category'];
            _this.sub_category_name = _this.record[0]['sub_category_name'];

            _this.sub_cateory_item = _this.record[0]['sub_category_item'];

            _this.tags = _this.record[0]['tags'];

            _this.special_infomation = _this.record[0]['special_information'][0].lv3[0];
            _this.accessory_infomation = _this.record[0]['accessory_information'];

            _this.pid = _this.record[0]['id'];
            _this.brand = _this.record[0]['brand'];
            _this.code = _this.record[0]['code'];
            _this.price_ntd = _this.record[0]['price_ntd'];
            _this.price = _this.record[0]['price'];
            _this.price_quoted = _this.record[0]['price_quoted'];
            _this.price_ntd_change = _this.record[0]['price_ntd_change'];
            _this.price_change = _this.record[0]['price_change'];
            _this.price_ntd_org = _this.record[0]['price_ntd_org'];
            _this.price_org = _this.record[0]['price_org'];
            _this.description = _this.record[0]['description'];
            _this.notes = _this.record[0]['notes'];
            _this.accessory_mode = _this.record[0]['accessory_mode'];
            _this.variation_mode = _this.record[0]['variation_mode'];

            _this.quoted_price = _this.record[0]['quoted_price'];
            _this.quoted_price_org = _this.record[0]['quoted_price_org'];
            _this.quoted_price_change = _this.record[0]['quoted_price_change'];
            _this.moq = _this.record[0]['moq'];

            _this.currency = _this.record[0]['currency'];

            _this.str_price_ntd_change = _this.record[0]['str_price_ntd_change'];
            _this.str_price_change = _this.record[0]['str_price_change'];
            _this.str_quoted_price_change = _this.record[0]['str_quoted_price_change'];

            _this.sheet_url = 'product_spec_sheet?sd=' + _this.record[0]['id'];

            _this.out = _this.record[0]['out'];
            _this.out_cnt = _this.record[0]['phased_out_cnt'];
            _this.phased_out_text = _this.record[0]['phased_out_text'];

            //var select_items = _this.record[0]['tags'].split(',');

            // if(_this.category === '10000000')
            //   $('#tag01').selectpicker('val', select_items);
            // if(_this.category === '20000000')
            //   $('#tag02').selectpicker('val', select_items);
            
            
            // if(_this.variation_mode == 1)
            //     $("#variation_mode").bootstrapToggle("on");
            // if(_this.accessory_mode == 1)
            //     $("#accessory_mode").bootstrapToggle("on");

            if(_this.record[0]['photo1'].trim() !== '')
                _this.url1 = _this.baseURL + _this.record[0]['photo1'];
            if(_this.record[0]['photo2'].trim() !== '')
                _this.url2 = _this.baseURL + _this.record[0]['photo2'];
            if(_this.record[0]['photo3'].trim() !== '')
                _this.url3 = _this.baseURL + _this.record[0]['photo3'];

            _this.url = _this.url1;

            _this.attributes = JSON.parse(_this.record[0]['attributes']);
            _this.variation_product = _this.record[0]['product'];

            _this.variation1_text = _this.record[0]['variation1_text'];
            _this.variation2_text = _this.record[0]['variation2_text'];
            _this.variation3_text = _this.record[0]['variation3_text'];

            _this.variation1_value = _this.record[0]['variation1_value'];
            _this.variation2_value = _this.record[0]['variation2_value'];
            _this.variation3_value = _this.record[0]['variation3_value'];

            _this.related_product = _this.record[0]['related_product'];
            _this.chunk(_this.related_product, 4);

            _this.variation1 = _this.record[0]['variation1'];
            _this.variation2 = _this.record[0]['variation2'];
            _this.variation3 = _this.record[0]['variation3'];

            _this.variation1_custom = _this.record[0]['variation1_custom'];
            _this.variation2_custom = _this.record[0]['variation2_custom'];
            _this.variation3_custom = _this.record[0]['variation3_custom'];
            
            _this.set_up_variants();
            _this.set_up_specification();

            _this.edit_mode = true;
          })
          .catch(function(error) {
            console.log(error);
          });
      },

    add_accessory_item: function(cat_id) {
      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      let sn = 0;
      for (let i = 0; i < items.length; i++) {
        if (items[i].id > sn) {
          sn = items[i].id;
        }
      }

      sn = sn + 1;

      item = {
        id: sn,
        cat_id: cat_id,
        url: "",
        file: {
          name: "",
        },
        code: "",
        name: "",
        price_ntd: "",
        price: "",
        photo:"",
      };

      items.push(item);
    },

    remove_accessory_item: function(cat_id, id) {
      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      for (i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          items.splice(i, 1);
          i = i - 1;
        }
      }
    },

    clear_accessory_item: function(cat_id, id) {
      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      for (i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          items[i].code = "";
          items[i].name = "";
          items[i].price_ntd = "";
          items[i].price = "";
          items[i].url = "";
          items[i].file.value = "";
        }
      }
    },

    async save_print_options()
    {
      let token = localStorage.getItem("accessToken");

      var form_Data = new FormData();

       form_Data.append('id', this.id)
       form_Data.append('pid', this.print_pid)
       form_Data.append('brand', this.print_brand)
       form_Data.append('srp', this.print_srp)
      form_Data.append('qp', this.print_qp)

      try {
        res = await axios.post("api/save_product_print_option", form_Data, {
          headers: { Authorization: `Bearer ${token}` },
        });
      } catch (err) {
        console.log(err)
        alert('error')
      }
    },

    async get_previous_print_options() {
      let token = localStorage.getItem("accessToken");
      var res = {pid: true, brand: true, srp: true, qp: true};
      const params = {
        id: this.id,
      };

      try {
        res = await axios.get("api/get_previous_product_print_option", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });

      } catch (err) {
        console.log(err)
        alert('error')
      }

      return res;
    },

    async load_print_option() {
      res = await this.get_previous_print_options();
      var pid = res.data.pid;
      var brand = res.data.brand;
      var srp = res.data.srp;
      var qp = res.data.qp;

      this.print_pid = pid;
      this.print_brand = brand;
      this.print_srp = srp;
      this.print_qp = qp;

      if(pid == true) {
        $('#print_id').removeClass('noPrint')
      } else {
        $('#print_id').addClass('noPrint')
      }

      if(brand == true) {
        $('#print_brand').removeClass('noPrint')
      } else {
        $('#print_brand').addClass('noPrint')
      }

      if(srp == true) {
        $('#print_srp').removeClass('noPrint')
      }
      else {
        $('#print_srp').addClass('noPrint')
      }

      if(qp == true) {
        $('#print_qp').removeClass('noPrint')
      }
      else {
        $('#print_qp').addClass('noPrint')
      }

    },

    async print_page() {

      //await this.print_option_page();

      window.print();
    },

    async print_option_page() {
      let _this = this;

      res = await this.get_previous_print_options();
      var pid = res.data.pid;
      var brand = res.data.brand;
      var srp = res.data.srp;
      var qp = res.data.qp;

      Swal.fire({
          title: 'Export Setting:',
          html: 'Show Product ID <input style="appearance: checkbox !important; display: inline-block !important;" type="checkbox" id="pid" ' + (pid == 'true' ? 'checked' : '') + ' /> <br/>' +
                'Show Brand Name <input style="appearance: checkbox !important; display: inline-block !important;" type="checkbox" id="brand" ' + (brand == 'true' ? 'checked' : '') + ' /> <br/>' +
                'Show SRP <input style="appearance: checkbox !important; display: inline-block !important;" type="checkbox" id="srp" ' + (srp == 'true' ? 'checked' : '') + '  /> <br/>' +
                'Show QP <input style="appearance: checkbox !important; display: inline-block !important;" type="checkbox" id="qp" ' + (qp == 'true' ? 'checked' : '') + '  />',
          confirmButtonText: 'OK',
          showCancelButton: true,
          preConfirm: () => {
            pid = Swal.getPopup().querySelector('#pid').checked
            brand = Swal.getPopup().querySelector('#brand').checked
            srp = Swal.getPopup().querySelector('#srp').checked
            qp = Swal.getPopup().querySelector('#qp').checked
            

            return {pid: pid, brand: brand, srp: srp, qp:qp}
          }
        }).then((result) => {
          //Swal.fire("alcool: "+`${result.value.alcool}`+" and Cigarro: "+`${result.value.cigarro}`);

          _this.print_pid = result.value.pid;
          _this.print_brand = result.value.brand;
          _this.print_srp = result.value.srp;
          _this.print_qp = result.value.qp;

          if(result.value.pid == true) {
            $('#print_id').removeClass('noPrint')
          } else {
            $('#print_id').addClass('noPrint')
          }

          if(result.value.brand == true) {
            $('#print_brand').removeClass('noPrint')
          } else {
            $('#print_brand').addClass('noPrint')
          }

          if(result.value.srp == true) {
            $('#print_srp').removeClass('noPrint')
          }
          else {
            $('#print_srp').addClass('noPrint')
          }

          if(result.value.qp == true) {
            $('#print_qp').removeClass('noPrint')
          }
          else {
            $('#print_qp').addClass('noPrint')
          }
          
          _this.save_print_options();
        })


      // (async () => {

      //   const {value: country} = await swal.fire({
      //       title: 'Select Ukraine',
      //       input: 'select',
      //       inputOptions: {
      //           'SRB': 'Serbia',
      //           'UKR': 'Ukraine',
      //           'HRV': 'Croatia'
      //       },
      //       inputPlaceholder: 'Select country',
      //       showCancelButton: true,
      //       inputValidator: (value) => {
      //           return new Promise((resolve) => {
      //               if (value === 'UKR') {
      //                   resolve()
      //               } else {
      //                   resolve('You need to select Ukraine :)')
      //               }
      //           })
      //       }
      //   })
      //   if (country) {
      //       // swal.fire('You selected: ' + country)

            
      //   }
      //   })()
      
        
    },

    onFileChange(e, num) {
      const file = e.target.files[0];

      if (num === 1) {
        this.url1 = URL.createObjectURL(file);
      }
      if (num === 2) {
        this.url2 = URL.createObjectURL(file);
      }
      if (num === 3) {
        this.url3 = URL.createObjectURL(file);
      }
    },

    onFileChangeBulkImage(e) {
      const file = e.target.files[0];
      this.bulk_url = URL.createObjectURL(file);
    },

    onFileChangeAccessory(e, cat_id, id) {
      const file = e.target.files[0];

      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      let url = URL.createObjectURL(file);

      for (i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          items[i].url = url;
        }
      }
    },

    clear_photo(num) {
      if (num === 1) {
        this.url1 = null;
      }
      if (num === 2) {
        this.url2 = null;
      }
      if (num === 3) {
        this.url3 = null;
      }
    },

    clear_bulk_image() {
      this.url = null;
    },

    get_special_infomation_detail: function(cat_id) {
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign").toggle();
    },

    get_special_infomation_detail_variantion1: function() {
      if(this.variation1 == "" || this.variation1 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation1)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_1").toggle();
    },

    get_special_infomation_detail_variantion2: function() {
      if(this.variation2 == "" || this.variation2 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation2)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_2").toggle();
    },

    get_special_infomation_detail_variantion3: function() {
      if(this.variation3 == "" || this.variation3 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation3)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_3").toggle();
    },

    apply_special_infomation_detail: function(cat_id, option) {
      this.$refs[cat_id][0].value = option;

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign").toggle();
    },

    apply_special_infomation_detail_variantion1: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_1");
 
      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation1_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

      //let variation_value = document
      //  .getElementById("variation1_value")
      //  .value.split(",");

      //variation_value.push(values);

      //document.getElementById("variation1_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_1").toggle();

      //$('variation1_value').tagsinput('refresh');
    },

    apply_special_infomation_detail_variantion2: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_2");

      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation2_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

   
      // let variation_value = document
      //   .getElementById("variation2_value")
      //   .value.split(",");

      // variation_value.push(values);

      // document.getElementById("variation2_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_2").toggle();

      //$('#variation2_value').tagsinput('refresh');
    },

    apply_special_infomation_detail_variantion3: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_3");
      
      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation3_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

      // let variation_value = document
      //   .getElementById("variation3_value")
      //   .value.split(",");

      // variation_value.push(values);

      // document.getElementById("variation3_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_3").toggle();

      //$('variation3_value').tagsinput('refresh');
    },


    variation_mode_change: function(e) {
      const chk = e.target.checkbox[0];

      if (chk.checked) {
        this.variation_mode = true;
      }
      if (!chk.checked) {
        this.variation_mode = false;
      }
    },


    accessory_mode_change: function(e) {
      const chk = e.target.checkbox[0];

      if (chk.checked) {
        this.accessory_mode = true;
      }
      if (!chk.checked) {
        this.accessory_mode = false;
      }
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
      element.scrollIntoView({ behavior: "smooth" });
    },

    cancel: function() {
      this.reset();
    },

    check_input: function(){
      return "";
    },


    reset: function() {
      return;

    },

    toggle_product() {
      let toogle = document.getElementById('select_all_product').checked;
        for(var i = 0; i < this.variation_product.length; i++) {
          this.variation_product[i].checked = toogle;
      }
    },

    bulk_toggle_product() {
      let toogle = document.getElementById('bulk_select_all_product').checked;
      this.code_checked = toogle;
      this.price_ntd_checked = toogle;
      this.price_checked = toogle;
      this.image_checked = toogle;
      this.status_checked = toogle;
      
    },

    clear_accessory() {
      for (var i in this.accessory_infomation) {
        this.accessory_infomation[i].detail[0] = [];
      }
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },
  },
});
