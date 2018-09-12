$(document).ready(function () {
   var n = $( "tr" ).length-1;
   console.log(n);
   for (var i=0; i<n; i++) {
      if ($("#solicitud_" +i).text() == 1) {
         $("#solicitud_"+i).parent().css("background-color", "#F5F5F5");
      }
   }
   $("#solicitud_0").click(function () {
      console.log($("#solicitud_" +n).text());
      $("#solicitud_0").parent().css("background-color", "red");
   });
});
