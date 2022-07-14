$(window).on("load", function () {
  $('#paginatedTable').DataTable({
    order: [[2, 'asc']],
    stateSave: true,
    scrollY: '50vh',
    scrollCollapse: true,
    paging: false,
  });
});

var timeout = setInterval(reloadCarts, 3000);
function reloadCarts() {
    // Breaks table filter
    // $('#distribution').load(window.location.href + " #distribution");
    $('#roulette').load(window.location.href + " #roulette");
    $('#roulette-ticker').load(window.location.href + " #roulette-ticker");
}
