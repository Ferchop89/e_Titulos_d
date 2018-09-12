$(function() {
  $("#seleccion_nivel").change(function() {
    if ($("#L").is(":selected")) {
      $("#nivel_mas").show();
      $("#niveles_otro").hide();
    } else {
      $("#nivel_mas").hide();
      $("#niveles_otro").show();
    }
  }).trigger('change');
});