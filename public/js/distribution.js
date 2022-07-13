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

// @todo paginate and skeleton load table rows
// $('table.paginated').each(function() {
//     var currentPage = 0;
//     var numPerPage = 10;
//     var $table = $(this);
//     $table.bind('repaginate', function() {
//         $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
//     });
//     $table.trigger('repaginate');
//     var numRows = $table.find('tbody tr').length;
//     var numPages = Math.ceil(numRows / numPerPage);
//     var $pager = $('<div class="pager"></div>');
//     for (var page = 0; page < numPages; page++) {
//         $('<span class="page-number"></span>').text(page + 1).bind('click', {
//             newPage: page
//         }, function(event) {
//             currentPage = event.data['newPage'];
//             $table.trigger('repaginate');
//             $(this).addClass('active').siblings().removeClass('active');
//         }).appendTo($pager).addClass('clickable');
//     }
//     $pager.insertBefore($table).find('span.page-number:first').addClass('active');
// });