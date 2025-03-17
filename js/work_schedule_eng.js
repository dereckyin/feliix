var app = new Vue({
    el: "#app",
    data: {
        submit: false,
        
        
        id:0,
        
        
        //img_url: 'https://storage.googleapis.com/feliiximg/',
        
        img_url: 'https://storage.googleapis.com/feliiximg/',
        
        // menu
        show_day_and_rate: false,
        show_item_list: false,
        
        // header
        period : 0,
        rate_leadman : '',
        rate_sr_technician : '',
        rate_technician : '',
        rate_electrician : '',
        rate_helper : '',
        
        // temp header
        temp_period : 0,
        temp_rate_leadman : '1400',
        temp_rate_sr_technician : '1200',
        temp_rate_technician : '1000',
        temp_rate_electrician : '1400',
        temp_rate_helper : '900',
        
        // item list
        item_list: [],
        temp_item_list: [],
        
        // blocks
        man_power: [],
        man_power1 : [],
        man_power2 : [],
        man_power3 : [],
        man_power4 : [],
        man_power5 : [],
        man_power6 : [],

        man_power_sum: [],
        
    },
    
    async created()  {
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
                        
                        break;
                        default:
                        console.log(`Too many args`);
                    }
                }
            });
        }
        
        if(this.id > 0)
        {
            this.get_records(this.id);
        }
        
    },
    
    computed: {
        show_gantt() {
            return this.period > 0 && this.item_list.length > 0;
        }
    },
    
    mounted() {
        
    },
    
    watch: {
        
        show_access() {
            if(this.show_access) {
                this.show_day_and_rate = false;
                this.show_item_list = false;
                
            }
        },
        
        show_day_and_rate() {
            if(this.show_day_and_rate) {
                this.show_item_list = false;
            }
        },
        
        show_item_list() {
            if(this.show_item_list) {
                this.show_day_and_rate = false;
            }
        },
        
        
        
    },
    
    methods: {

        save_total: function() {
            if (this.submit == true) return;
            
            this.submit = true;
            
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            
            form_Data.append("jwt", token);
            
            form_Data.append("id", this.id);
            form_Data.append("period", this.temp_period);
            form_Data.append("rate_leadman", this.temp_rate_leadman);
            form_Data.append("rate_sr_technician", this.temp_rate_sr_technician);
            form_Data.append("rate_technician", this.temp_rate_technician);
            form_Data.append("rate_electrician", this.temp_rate_electrician);
            form_Data.append("rate_helper", this.temp_rate_helper);

            axios({
                method: "post",
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                url: "api/work_schedule_eng_days_insert",
                data: form_Data,
            })
            .then(function(response) {
                //handle success
                Swal.fire({
                    html: response.data.message,
                    icon: "info",
                    confirmButtonText: "OK",
                });

                _this.id = response.data.id;

                _this.period = parseInt(_this.temp_period, 0);
                _this.rate_leadman = _this.temp_rate_leadman;
                _this.rate_sr_technician = _this.temp_rate_sr_technician;
                _this.rate_technician = _this.temp_rate_technician;
                _this.rate_electrician = _this.temp_rate_electrician;
                _this.rate_helper = _this.temp_rate_helper;

                _this.reload();

                _this.setup_man_power();

                _this.submit = false;
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

        setup_man_power: function() {
            if(this.period == 0)
                return;
            this.man_power1 = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
            this.man_power2 = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
            this.man_power3 = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
            this.man_power4 = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
            this.man_power5 = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
            this.man_power6 = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");

            this.man_power_sum = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
        },
        
        sum_man_power: function() {
            // pivot man powers into man_power_sum
            var sum1 = 0;
            var sum2 = 0;
            var sum3 = 0;
            var sum4 = 0;
            var sum5 = 0;
            var sum6 = 0;
            for(var i = 0; i < this.period; i++) {
                sum1 = parseInt(this.man_power1[i]) || 0;
                sum2 = parseInt(this.man_power2[i]) || 0;
                sum3 = parseInt(this.man_power3[i]) || 0;
                sum4 = parseInt(this.man_power4[i]) || 0;
                sum5 = parseInt(this.man_power5[i]) || 0;
                sum6 = parseInt(this.man_power6[i]) || 0;

                this.man_power_sum[i] = sum1 + sum2 + sum3 + sum4 + sum5 + sum6;
            }

            this.$forceUpdate();
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
            .get("api/work_schedule_eng", {
                params,
                headers: { Authorization: `Bearer ${token}` },
            });

            
            if(res.data.length > 0) {
                console.log(res.data);
                record = res.data;
                if (record.length > 0) {
                    this.id = record[0].id;
                    this.period = parseInt(record[0].period, 0);
                    this.rate_leadman = record[0].rate_leadman;
                    this.rate_sr_technician = record[0].rate_sr_technician;
                    this.rate_technician = record[0].rate_technician;
                    this.rate_electrician = record[0].rate_electrician;
                    this.rate_helper = record[0].rate_helper;
                    this.item_list = JSON.parse(record[0].items);
                    
                    this.temp_period = parseInt(record[0].period, 0);
                    this.temp_rate_leadman = record[0].rate_leadman;
                    this.temp_rate_sr_technician = record[0].rate_sr_technician;
                    this.temp_rate_technician = record[0].rate_technician;
                    this.temp_rate_electrician = record[0].rate_electrician;
                    this.temp_rate_helper = record[0].rate_helper;
                    this.temp_item_list = JSON.parse(record[0].items);
                    
                    this.man_power = JSON.parse(record[0].man_power);
                }

                if(this.period > 0 && this.man_power.length == 0 && this.item_list.length > 0)
                    this.setup_man_power();
            }
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
            
            if(this.temp_item_list.length != 0)
                order = Math.max.apply(Math, this.temp_item_list.map(function(o) { return o.id; }))
            
            types = [];

            obj = {
                "id" : order + 1,
                "legend" : "",
                "name" : "",
                "types" : types,
            }, 
            
            this.temp_item_list.push(obj);
        },
        
        add_item(eid) {
            var element = this.temp_item_list.find(({ id }) => id === eid);
            
            let obj_id = 0;
            
            if(element.types.length != 0)
                obj_id = Math.max.apply(Math, element.types.map(function(o) { return o.id; }))

            days = [];

            if(parseInt(this.period, 0) > 0)
                days = Array.from({length: parseInt(this.period, 0)}, (v, k) => "");
            
            obj = {
                "id" : obj_id + 1,
                "legend" : "",
                "name" : "",
                "days" : days,
            }, 
            
            element.types.push(obj);
        },

        
        page_up: function(fromIndex, eid) {
            var toIndex = fromIndex - 1;
            
            if (toIndex < 0) 
                return;
            
            var element = this.temp_item_list.find(({ id }) => id === eid);
            this.temp_item_list.splice(fromIndex, 1);
            this.temp_item_list.splice(toIndex, 0, element);
        },
        
        page_down: function(fromIndex, eid) {
            var toIndex = fromIndex + 1;
            
            if (toIndex > this.temp_item_list.length - 1) 
                return;
            
            var element = this.temp_item_list.find(({ id }) => id === eid);
            this.temp_item_list.splice(fromIndex, 1);
            this.temp_item_list.splice(toIndex, 0, element);
        },
        
        page_del: function(eid) {
            
            var index = this.temp_item_list.findIndex(({ id }) => id === eid);
            if (index > -1) {
                this.temp_item_list.splice(index, 1);
            }
        },
        
        set_up: function(pid, fromIndex, eid) {
            var toIndex = fromIndex - 1;
            
            if (toIndex < 0) toIndex = 0;
            
            var page = this.temp_item_list.find(({ id }) => id === pid);
            
            var element = page.types.find(({ id }) => id === eid);
            page.types.splice(fromIndex, 1);
            page.types.splice(toIndex, 0, element);
        },
        
        set_up_page: function(pid, page_index, fromIndex, eid) {
            var toIndex = page_index - 1;
            
            if (toIndex < 0)
                return;
            
            var page = this.temp_item_list.find(({ id }) => id === pid);
            
            var element = page.types.find(({ id }) => id === eid);
            page.types.splice(fromIndex, 1);
            this.temp_item_list[toIndex].types.splice(this.temp_item_list[toIndex].types.length - 1, 0, element);
        },
        
        set_down: function(pid, fromIndex, eid) {
            var toIndex = fromIndex + 1;
            
            var page = this.temp_item_list.find(({ id }) => id === pid);
            
            if (toIndex > page.types.length - 1) toIndex = page.types.length - 1;
            
            var element = page.types.find(({ id }) => id === eid);
            page.types.splice(fromIndex, 1);
            page.types.splice(toIndex, 0, element);
        },
        
        set_down_page: function(pid, page_index, fromIndex, eid) {
            var toIndex = page_index + 1;
            
            var page = this.temp_item_list.find(({ id }) => id === pid);
            
            if (toIndex > page.types.length - 1) 
                return;
            
            var element = page.types.find(({ id }) => id === eid);
            page.types.splice(fromIndex, 1);
            this.temp_item_list[toIndex].types.splice(this.temp_item_list[toIndex].types.length - 1, 0, element);
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
            
            await this.reload();
        },
        
        del_block: function(pid, eid) {
            var page = this.temp_pages.find(({ id }) => id === pid);
            var index = page.types.findIndex(({ id }) => id === eid);
            if (index > -1) {
                page.types.splice(index, 1);
            }
        },
        
        page_save_pre: async function() {
            let _this = this;

            if (this.submit == true) return;
            this.submit = true;

            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            
            form_Data.append("jwt", token);
            form_Data.append("id", this.id);

            form_Data.append("items", JSON.stringify(this.temp_item_list));

            try {
                let res = await axios({
                    method: 'post',
                    url: 'api/work_schedule_eng_items_insert',
                    data: form_Data,
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                if(res.status == 200){
                    _this.submit = false;
                    await this.reload();
                    Swal.fire({
                        html: res.data.message,
                        icon: "info",
                        confirmButtonText: "OK",
                    });

                    _this.setup_man_power();
                }
            } catch (err) {
                console.log(err)
                Swal.fire({
                    text: JSON.stringify(error),
                    icon: "info",
                    confirmButtonText: "OK",
                });
            } finally { 
                _this.submit = false;
            }
        },

        
        // page_save_pre: async function() {
        //     let _this = this;
        //     let empty = true;
        //     // check if page is empty
            
        //     if (this.submit == true) return;
        //     this.submit = true;
            
            
        //     await this.get_latest_record();
            
            
        //     this.submit = false;
            
        //     // check if this.temp_pages and this.temp_pages_verify identical
        //     if(JSON.stringify(this.pages ) != JSON.stringify(this.temp_pages_verify))
        //         {
        //         Swal.fire({
        //             text: "This quotation has been modified by other user. Please reload the page and try again.",
        //             icon: "info",
        //             confirmButtonText: "OK",
        //         });
                
        //         return;
        //     }
            
        //     for(var i = 0; i < this.temp_pages.length; i++) {
        //         if(this.temp_pages[i].types.length != 0){
        //             empty = false;
        //             break;
        //         }
        //     }
            
        //     if(this.temp_pages.length != 0)
        //         empty = false;
            
        //     if(empty)
        //         {
        //         const alert =  await Swal.fire({
        //             title: "WARNING",
        //             text: "If click yes, all the pages and subtotal blocks will be erased.",
        //             icon: "warning",
        //             showCancelButton: true,
        //             confirmButtonColor: "#3085d6",
        //             cancelButtonColor: "#d33",
        //             confirmButtonText: "Yes",
        //         });
                
        //         if(alert.value == true)
        //             await _this.page_save();
        //     }
        //     else
        //     {
                
        //         await _this.page_save();
        //     }
        // },
        
        
        reload : async function() {
            this.close_all();
            
            this.is_load = false;
            
            await this.get_records(this.id);
        },
        
        close_all() {
            this.show_day_and_rate = false;
            this.show_item_list = false;
            
            console.log("close all");
        },

    }
    
});
