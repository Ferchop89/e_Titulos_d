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

var options_lib="";
$("#libro").on('change',function(){
   var value=$(this).val();
   if(value!="0"){
     var fojas_sel=<?php echo json_encode($fojas_sel); ?>; //Fojas por libro [OPTIMIZAR]
     options_def_l += "<option value='0'>--Foja--</option>";
     fojas_sel.forEach(function(element) {
       if(element.substr(0,4) == value){
        options_def_l += "<option value='"+element.substr(5)+"'>"+element.substr(5)+"</option>";
       }
     });
     $("#foja").html(options_def_l);
   }
   else{
     var options_def_l="";
     var fojas=<?php echo json_encode($fojas); ?>; //Fojas
     options_def_l += "<option value='0'>--Foja--</option>";
     fojas.forEach(function(element) {
       options_def_l += "<option value='"+element.foja+"'>"+element.foja+"</option>";
     });
     $("#foja").html(options_def_l);
   }
});
