// $(window).load(function() {
//    $(".loader").fadeOut("slow");
//    $("btnEnviar").onclick()
//
// });

$(document).ready(function() {
   $(".loader").hide();
   $("#btnEnviar").click(function (e) {
      $(".loader").fadeIn( 100 ).delay( 6000 ).slideUp( 100 );
      // $(".loader").show();
      $.ajax({
    xhr: function(){
       var xhr = new window.XMLHttpRequest();
       //Download progress
       xhr.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
              var percentComplete = evt.loaded / evt.total;
              //Do something with download progress
              console.log(percentComplete);
            }
       }, false);
       console.log(xhr);
       return xhr;
    },
    complete:function(){
        console.log("Request finished.");
    }
});
   });
});

// function descargar(url) {
// window.onfocus = finalizada;
// document.location = url;
// }
// function finalizada() {
// window.onfocus = vacia;
// // Modificar a partir de aquí
// alert();
// }
