$(document).ready(function() {
   $(".Cell.btns").hover(function (e) {
      $(this).css("background-color", "rgb(202, 207, 210)");
      $(this).parents(".fila").css("background-color", "rgb(202, 207, 210)");
   }
   , function(){
      $(this).css("background-color", "white");
      $(this).parents(".fila").css("background-color", "white");
   });
});
