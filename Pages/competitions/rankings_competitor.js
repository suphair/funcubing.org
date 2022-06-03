$('[data-event-select]').on('click', function (e) {
    var event = $(this).data('event-select');
    $('[data-event-select] i').removeClass('select');
    $('[data-event-select=' + event + '] i').addClass('select');
    $('[data-results-event]').hide();
    $('[data-results-event=' + event + ']').show();
    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?event=' + event;
    window.history.pushState({path: newurl}, '', newurl);
    scroll_event();
    return false;
});


function scroll_event() {
    var scroll_results = $('[data-results-scroll]');
    if (scroll_results.data('results-scroll') == 1) {
        var scrollTop = scroll_results.offset().top;
        $(document).scrollTop(scrollTop);
    }
}

window.onload = function () {
    scroll_event();
}

$('[data-event-record]').hover(function () {
    $(this).addClass('select');
    $(this).css('cursor', 'pointer');
}, function () {
    $(this).removeClass('select');
    $(this).css('cursor', 'auto');
});

$('[data-event-record]').on('click', function (e) {
    var event = $(this).data('event-record');
    $('[data-event-select=' + event + '] i').trigger("click");
});