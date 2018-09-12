$(function() {
  $("#nacionalidad").change(function() {
    if ($("#1").is(":selected")) {
      $("#paises_mexicano").show();
      $("#paises_otro").hide();
    } else {
      $("#paises_mexicano").hide();
      $("#paises_otro").show();
    }
  }).trigger('change');
});