
var app = new Vue({
  el: '#app',
  data:{
    name: "",
    today: "",
    type: "",
    location: "",
    remark: "",
    time: "",
    latitude: 0,
    longitude: 0,
    explanation: "",

    piclatitude: 0,
    piclongitude: 0,


    file: '',

    isCameraOpen: false,
    imageCapture: {},
    photo_time:'',
    photo_gps:'',
    submit: false,

  },

  created () {

      this.getLocation()
      this.getUserName()

  },

  computed: {
    showExtra: function(){
      return (this.location=='D' || this.location=='E' || this.location=='F');
    },

    showPhoto: function(){
      return (this.location=='A' || this.location=='M' || this.location=='B' || this.location=='C' || this.location=='D' || this.location=='E' || this.location=='F');
    }
  },

  mounted(){
    this.getTimeNow();
    this.getToday();
   
  },

  methods:{


    getLocation: function() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(this.showPosition);
      } else { 
        Swal.fire({
          text: 'Geolocation is not supported by this browser.',
          icon: 'error',
          confirmButtonText: 'OK'
        })

      }
    },


    showPosition:  function(position) {
      this.latitude = position.coords.latitude;
      this.longitude = position.coords.longitude;
    },


    getToday: function() {
      var self = this;
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();

      this.today = yyyy + '/' + mm + '/' + dd;

      //setInterval(self.getToday, 1000 * 60)
    },

    getTimeNow: function() {
      var self = this;
      var today = new Date();
      var hh = String(today.getHours()).padStart(2, '0');
      var mm = String(today.getMinutes()).padStart(2, '0'); 
      var ss = String(today.getSeconds()).padStart(2, '0');

      this.time = hh + ':' + mm + ':' + ss;

      //setInterval(self.getTimeNow, 1000)
    },

    onTakePhotoButtonClick: function() {
      this.imageCapture.takePhoto()
      .then(blob => createImageBitmap(blob))
      .then(imageBitmap => {
        const canvas = document.querySelector('#takePhotoCanvas');
        this.drawCanvas(canvas, imageBitmap);
      })
      .catch(error => ChromeSamples.log(error));
    },

    ConvertDMSToDD: function(degrees, minutes, seconds, direction) {
    
        var dd = degrees + (minutes/60) + (seconds/3600);
        
        if (direction == "S" || direction == "W") {
            dd = dd * -1; 
        }
        
        return dd;
    },

/*
    onChangeFileUpload() {
          const _this = this;
            this.file = this.$refs.file.files[0];

           
                  EXIF.getData(this.$refs.file.files[0], function() {
                   
                      var result = EXIF.pretty(this);

                      // Calculate latitude decima
                      try{
                          var latDegree = this.exifdata.GPSLatitude[0].numerator;
                          var latMinute = this.exifdata.GPSLatitude[1].numerator;
                          var latSecond = this.exifdata.GPSLatitude[2].numerator;
                          var latDirection = this.exifdata.GPSLatitudeRef;

                          var latFinal = _this.ConvertDMSToDD(latDegree, latMinute, latSecond, latDirection);
                  

                          // Calculate longitude decimal
                          var lonDegree = this.exifdata.GPSLongitude[0].numerator;
                          var lonMinute = this.exifdata.GPSLongitude[1].numerator;
                          var lonSecond = this.exifdata.GPSLongitude[2].numerator;
                          var lonDirection = this.exifdata.GPSLongitudeRef;

                          var lonFinal = _this.ConvertDMSToDD(lonDegree, lonMinute, lonSecond, lonDirection);
                        

                          _this.photo_time = this.exifdata.DateTimeOriginal;

                          _this.photo_gps = latFinal+','+lonFinal;

                          _this.piclatitude = latFinal;
                          _this.piclongitude = lonFinal;

                          document.getElementById('map-link').innerHTML = '<a href="http://www.google.com/maps/place/'+latFinal+','+lonFinal+'" target="_blank">Check on Google Maps</a>';
                        }
                        catch(err) {
                          _this.msg  = err.message;
                          return;
                        }

                      _this.msg  = result;
                  });
        },
*/


        validateForm() {
              if (this.type == "") 
              {
                Swal.fire({
                  text: 'Choose Punch Type',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Choose Punch Type';
                  //$(window).scrollTop(0);
                  return false;
              } 

               if (this.location == "") 
              {
                Swal.fire({
                  text: 'Choose Punch location',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Choose Punch location';
                  //$(window).scrollTop(0);
                  return false;
              } 

              if (this.latitude == 0 || this.lngitude == 0) 
              {
                Swal.fire({
                  text: 'Submit Failed. No GPS Information Obtained. Please Turn On GPS Function.',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Choose Punch location';
                  //$(window).scrollTop(0);
                  return false;
              } 


/*
              if ((this.piclatitude == 0 || this.piclongitude == 0) && !this.$refs.file == undefined) 
              {
                Swal.fire({
                  text: 'Please turn on the function of GPS information acquiring in your camera or choose the existing photo with GPS information.',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Choose Punch location';
                  //$(window).scrollTop(0);
                  return false;
              } 
*/


              if (this.showExtra && this.explanation == "")
              {
                Swal.fire({
                  text: 'Further Explanation required',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Further Explanation required';
                  //$(window).scrollTop(0);
                  return false;
              }

              var file = "";

              if(document.getElementById("base64image") !== null)
                file =  document.getElementById("base64image").src;

              if(this.showPhoto && file === "")
              {
                Swal.fire({
                  text: 'Photo required',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Location Photo required';
                  //$(window).scrollTop(0);
                  return false;
              }
/*
              if (this.showPhoto && !this.$refs.file.files[0])
              {
                Swal.fire({
                  text: 'Location Photo required',
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
                  //this.err_msg = 'Location Photo required';
                  //$(window).scrollTop(0);
                  return false;
              }
*/
            return true;
          
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

      upload: function() {

        if(this.submit == true) {
          return;
        }

        if(!this.validateForm())
          return;

          var file;
          var ptime;

          if(document.getElementById("base64image") !== null)
            file =  document.getElementById("base64image").src;
          else
            file = "";

          if(!this.showPhoto)
            file = "";

          if(document.getElementById('photo_time') !== null)
          {
            ptime = document.getElementById('photo_time').value;
            ptime = ptime.split('/').join('');
            ptime = ptime.split(':').join('');
          }
          else
            ptime = this.today.split('/').join('') + ' ' + this.time.split(':').join('');

          if(file !== "" && ptime == "")
          {
            Swal.fire({
              text: "Can't get photo time, please take photo again.",
              icon: 'warning',
              confirmButtonText: 'OK'
            });
            return;
          }

          this.submit = true;

          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;

          form_Data.append('jwt', token);
          form_Data.append('today', this.today);
          form_Data.append('type', this.type);
          form_Data.append('location', this.location);
          form_Data.append('explan', this.explanation);
          form_Data.append('remark', this.remark);
          form_Data.append('time', this.time);
          form_Data.append('base64image', file);
          form_Data.append('latitude', this.latitude);
          form_Data.append('longitude', this.longitude);
          form_Data.append('piclatitude', this.latitude);
          form_Data.append('piclongitude', this.longitude);
          form_Data.append('photo_time', ptime);
          form_Data.append('photo_gps', this.latitude+','+this.longitude);

          axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data'
                  },
                  url: 'api/on_duty',
                  data: form_Data
              })
              .then(function(response) {
                  //handle success
                  Swal.fire({
                  text: response.data.message,
                  icon: 'success',
                  confirmButtonText: 'OK'
                })

                _this.reset();
 
              })
              .catch(function(error) {
                  //handle error
                  Swal.fire({
                  text: JSON.stringify(error),
                  icon: 'error',
                  confirmButtonText: 'OK'
                })

                _this.reset();
              });

      },

      reset: function() {
          
            this.today = '';
            this.type = '';
            this.location = '';
            this.remark = '';
            this.time = '';
            this.explanation = '';
            this.err_msg = '';
            this.submit = false;

            this.getLocation();
            this.getToday();
            this.getTimeNow();
            
        },
 
  }
});