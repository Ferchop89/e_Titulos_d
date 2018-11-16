$( document ).ready(function() {
   var options="";
   $("#fecha").on('change',function(){
      var date=$(this).val();
      var value=date.substr(0,4);
      //alert(value);
      if(value!=""){
         options="<option value='"+value+"'>"+value+"</option>";
         $("#libro").html(options);
      }
      else{
         var options_def="";
         var libros=<?php echo json_encode($libros); ?>; //Libros
         options_def += "<option value='0'>--Libro--</option>";
         libros.forEach(function(element) {
           options_def += "<option value='"+element.libro+"'>"+element.libro+"</option>";
         });
         $("#libro").html(options_def);
      }
   });
});
