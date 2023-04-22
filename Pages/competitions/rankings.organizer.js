$('[data-event-code-select]').click(function () {
    var event = $(this).data('event-code-select');

    $('[data-event-code]').hide();
    $('[data-event-code=' + event + ']').show();
    $('[data-event-code-select]').removeClass('select');
    $(this).addClass('select');
    return false;
});

$('[data-organizers-add-link]').click(function () {
    $(this).hide();
    $('[data-organizers-add-block]').show();
    $('#countries_chosen_select_chosen').css('width', '100%');
});

$('[data-event-code]').each(function () {
    var place = 0;
    var event_place = false;
    $(this).find('[data-event-place]').each(function () {
        if ($(this).data('event-place') !== event_place) {
            place = place + 1;
            event_place = $(this).data('event-place');
        }
        $(this).html(place);
    });
});