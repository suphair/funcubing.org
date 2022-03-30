var count = Math.ceil((document.body.clientHeight - 155) / 67);
if (count < 1) {
    count = 1;
}
var show_row = count;

var total_row = $("#table_data tbody tr").length;

$('#table_data thead td').each(
        function (index, el) {
            $(el).width($(el).width());
        }
);
$('[data-row_competitor]').hide();


var row = 1;
setInterval(function () {
    if (row <= show_row) {
        $('[data-row_competitor=' + row + ']').show(900);
        row = row + 1;
    }
}, 100);

setInterval(function () {
    if (show_row >= total_row) {
        location.reload();
    }
    $('[data-row_competitor]').hide();
    show_row = show_row + count;
}, 12000);