// function getOffset( el ) {
//  var _x = 0;
//  var _y = 0;
//  while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
//      _x += el.offsetLeft - el.scrollLeft;
//      _y += el.offsetTop - el.scrollTop;
//      el = el.offsetParent;
//  }
//  return { top: _y, left: _x };
// }
//
// function visualiza(){}
//   var elmnt = window.innerHeight;
//   var y = getOffset( document.getElementById('codigo_postal') ).top;
//   window.scroll(0, y-(elmnt/2));
// }

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

$("#filtraXCodigo").click(function () {
  var elmnt = window.innerHeight;
  var y = getOffset( document.getElementById('codigo_postal') ).top;
  window.scroll(0, y-(elmnt/2));
}
