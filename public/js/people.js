$(document).ready(function(){
  $("#filterTableInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#filterableTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});