
let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    id: 0,
    department: '',
   

        // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],
    perPage: 10000,

    user_records: [],
    record: {},
   
};

var app = new Vue({
	el: '#mainContent',

	data: mainState,

	created () {
      console.log('Vue created');
      this.getReceiveRecords();
      this.perPage = this.inventory.find(i => i.id === this.perPage);

    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');

        this.setPages();
        return this.paginate(this.user_records);
      }
    },

    watch: {
      user_records () {

        this.setPages();
      },

    },
 
	methods:{
		getReceiveRecords: function(keyword) {
    
            axios.get('../api/admin/department')
                .then(function(response) {
                    console.log(response.data);
                    app.user_records = response.data;


                })
                .catch(function(error) {
                    console.log(error);
                });
        },

		getIndex(index) {
            return ((this.page - 1) * this.perPage.id) + index
        },

        setPages () {
          console.log('setPages');
          this.pages = [];
          let numberOfPages = Math.ceil(this.user_records.length / this.perPage.id);

          if(numberOfPages == 1)
            this.page = 1;
          for (let index = 1; index <= numberOfPages; index++) {
            this.pages.push(index);
          }
        },

        paginate: function (posts) {
          console.log('paginate');
          if(this.page < 1)
            this.page = 1;
          if(this.page > this.pages.length)
            this.page = this.pages.length;

          let page = this.page;
          let perPage = this.perPage.id;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return  this.user_records.slice(from, to);
        },

		toggleCheckbox()
        {
            var i;
            for (i = 0; i < this.user_records.length; i++) 
            {
              this.user_records[i].is_checked = (this.clicked == 1 ? 0 : 1);
            }

            this.clicked = (this.clicked == 1 ? 0 : 1);
          //$(".alone").prop("checked", !this.clicked);
          //this.clicked = !this.clicked;
        },

        resetError: function() {
          console.log("resetError");
           
        },

        unCheckCheckbox()
        {
            for (i = 0; i < this.user_records.length; i++) 
            {
              this.user_records[i].is_checked = false;
            }
          //$(".alone").prop("checked", false);
          //this.clicked = false;
        },
 
		toFormData: function(obj){
			var form_data = new FormData();
			for(var key in obj){
				form_data.append(key, obj[key]);
			}
			return form_data;
		},

		shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },
 
		clearMessage: function(){
			app.errorMessage = '';
			app.successMessage = '';
    },
    

		deleteRecord() {
          console.log("deleteRecord");
          var favorite = [];
          this.resetError();

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            for (i = 0; i < this.user_records.length; i++) 
            {
              if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].id);
            }

            if (favorite.length < 1) {
                alert("請選一筆資料進行修改刪除 (Please select rows to delete!)");
                $(window).scrollTop(0);
                return;
            }

            var r = confirm("是否確定刪除? (Are you sure to delete?)");
            if (r == true) {
              this.delReceiveRecord(favorite.join(", "));

              app.resetForm();
              this.unCheckCheckbox();

              $(window).scrollTop(0);
            }
        },

        delReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            //targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
 
            formData.append('crud', "del");
            formData.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: '../api/admin/department',
                    data: formData
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    if (response.data !== "")
                        console.log(response.data);
                    //this.$forceUpdate();
                    app.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        editRecord() {
          console.log("editRecord");
            var favorite = [];
            this.resetError();

            for (i = 0; i < this.user_records.length; i++) 
            {
              if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                return;
            }
            this.record = this.shallowCopy(app.user_records.find(element => element.id == favorite));
            this.isEditing = true;

            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        updateEditStatus: function(event) {
          console.log("updateEditStatus");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.record.status = 1;
            } else {
                this.record.status = 0;
            }
        },

        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            let formData = new FormData();

            if (this.record.department == "") 
            {
              this.error_username = '名稱需輸入 (department name required)';
              $(window).scrollTop(0);
              return false;
            } 

            formData.append('department', this.record.department)
           
            formData.append('crud', "update");
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: '../api/admin/department',
                    data: formData
                    
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
             
                  app.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        createReceiveRecord: function() {


            var formData = new FormData();

            if (this.department == "") 
            {
              Swal.fire({
                text: '部門名稱需輸入 (department name required)',
                icon: 'error',
                confirmButtonText: 'OK'
              });
         
              $(window).scrollTop(0);
              return false;
            } 


            formData.append('department', this.department)
           
            formData.append('crud', 'insert')


            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    url: '../api/admin/department',
                    data: formData
                })
                .then(function(response) {
                    //handle success
                    console.log(response)

                    app.resetForm();

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
            
        },

        resetForm: function() {
          console.log("resetForm");
            this.department = '';
         
            this.isEditing = false;
            this.record = {};

       

            this.resetError();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getReceiveRecords();
        },

	}
});