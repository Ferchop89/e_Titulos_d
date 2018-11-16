var elmnt = document.body.scrollHeight;
var h = window.innerHeight;
//alert(h+" ventana | "+elmnt+" cuerpo");
if(elmnt <= h){
  document.getElementById('footer').style.position = "absolute";
  document.getElementById('footer').style.bottom = "0px";
}

window.onresize = function(){
  var elmnt = document.body.scrollHeight;
  var h =  window.innerHeight;
  //alert(h+" ventana | "+elmnt+" cuerpo");
    if(elmnt <= h){
      document.getElementById('footer').style.position = "absolute";
      document.getElementById('footer').style.bottom = "0px";
    }else{
      document.getElementById('footer').style.position = "fixed";
    }
}
