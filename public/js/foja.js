$( document ).ready(function() {
   var options="";
   $("#libro").on('change',function(){
       var value=$(this).val();
       if(value!="0"){
         var fojas_sel=<?php echo json_encode($fojas_sel); ?>; //Fojas por libro [OPTIMIZAR]
         options_def += "<option value='0'>--Foja--</option>";
         fojas_sel.forEach(function(element) {
           if(element.substr(0,4) == value){
            options_def += "<option value='"+element.substr(5)+"'>"+element.substr(5)+"</option>";
           }
         });
         $("#foja").html(options_def);
       }
       else{
         var options_def="";
         var fojas=<?php echo json_encode($fojas); ?>; //Fojas
         options_def += "<option value='0'>--Foja--</option>";
         fojas.forEach(function(element) {
           options_def += "<option value='"+element.foja+"'>"+element.foja+"</option>";
         });
         $("#foja").html(options_def);
       }
   });
});
