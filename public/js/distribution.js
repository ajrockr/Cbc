$(document).ready(function(){
    $("#filterTableInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#filterableTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
  });
});

$('#clearFilter').on( "click", function(event) {
    $('#filterTableInput').attr('value', "");
});

var timeout = setInterval(reloadCarts, 3000);
function reloadCarts() {
    // Breaks table filter
    // $('#distribution').load(window.location.href + " #distribution");
    $('#roulette').load(window.location.href + " #roulette");
    $('#roulette-ticker').load(window.location.href + " #roulette-ticker");
}
$(document).ready( function() {
    $('#paginatedTable').DataTable();
});