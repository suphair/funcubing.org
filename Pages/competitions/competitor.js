$('[data-organizer]').change(function () {
    let values = [];
    $('[data-organizer]:checked').each(function () {
        values.push($(this).data('organizer'));
    });
    location.href = '#' + values.join("+");
    showing();
});

function showing() {
    $('[data-row-organizer]').hide();
    let values = [];
    var anc = window.location.hash.replace("#", "");
    values = anc.split('+');
    $('[data-organizer]').prop('checked', false);
    values.forEach(function (el) {
        if (el.toString() != '') {
            $('[data-organizer=' + el + ']').prop('checked', true);
        }
    });
    $('[data-row-organizer]').each(function () {
        if ($.inArray($(this).data('row-organizer').toString(), values) !== -1) {
            $(this).show();
        }
    });

    $('[data-showing]').each(function () {
        var visible = $(this).find('tbody tr:visible');
        visible.removeClass('odd');
        visible.removeClass('even');
        var n = 1;
        visible.each(function () {
            if (n !== 1) {
                $(this).addClass('odd');
                n = 1;
            } else {
                $(this).addClass('even');
                n = 2;
            }
        });
    });

}
showing();