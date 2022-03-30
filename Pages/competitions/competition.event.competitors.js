$('[data-competitor-place-select]').click(function () {
    $('[data-competitor-place]').prop('checked', false);
    var place_selected = $(this).data('competitor-place-select');
    $('[data-competitor-place]').each(function () {
        var place = $(this).data('competitor-place');
        if (place > 0 & place <= place_selected) {
            $(this).prop('checked', true);
        }

    });
    return false;
});