function getOffset( el ) {
 var _x = 0;
 var _y = 0;
 while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
     _x += el.offsetLeft - el.scrollLeft;
     _y += el.offsetTop - el.scrollTop;
     el = el.offsetParent;
 }
 return { top: _y, left: _x };
}
var num_cta =  "<?php echo $num_cta ?>";
var elmnt = window.innerHeight;
var y = getOffset( document.getElementById(num_cta) ).top;
window.scroll(0, y-(elmnt/2));
//document.getElementById(num_cta).style.background = "rgb(255, 235, 204)";
document.getElementById(num_cta).style.background = "rgb(230, 240, 255)";
