var current_event = $('#event').find('option').eq(0).attr("name");
var landscape;
select_event(current_event);
resize(current_event);


$('#event').change(function () {
    current_event = $(this).find('option:selected').attr("name");
    select_event(current_event);
    resize(current_event);
});

function select_event(event) {
    $('[data-event]').hide();
    $('#' + event + ' [data-event]').show();
    $('#' + event).show();

}

$(function () {
    $(window).on('load resize', function () {
        resize(current_event);
    });
});

function resize(event) {
    if (window.innerHeight < window.innerWidth) {
        $('#' + event + ' [data-attempts]').show();
        $('#landscape').hide();
        landscape = false;
    } else {
        $('#' + event + ' [data-attempts]').hide();
        $('#landscape').show();
        landscape = true;
    }
}

$('.popup-fade').fadeOut();

$("[data-modal]").click(function () {
    if (!landscape) {
        return false;
    }
    if ($('.popup-fade').css('display') === 'block') {
        $('.popup-fade').fadeOut();
        return false;
    }
    $('.popup-fade .popup').html($(this).data('modal'));
    $('.popup-fade').fadeIn();
    return false;
});

$('.popup-fade').click(function (e) {
    $('.popup-fade').fadeOut();
});

$('body').click(function (e) {
    if ($(e.target).closest('.popup').length == 0) {
        $('.popup-fade').fadeOut();
    }
});