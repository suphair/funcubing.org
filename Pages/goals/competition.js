
function GoalEnter(el, event, format) {
    var value = el.val();

    value = value.replace(/\D+/g, '');
    value = value.replace(/^0+/, '');
    value = value.substring(0, 7);
    if (value === '') {
        el.val(value);
        return;
    }

    if (event === '333fm' && format === 'single') {
        if (value.length > 2) {
            value = value.substr(0, 2);
        }
        el.val(value);
        return;
    }

    if (event === '333fm' && format === 'average') {
        value = '000' + value;
        value = value.substr(-4, 4)
        value = value.substr(0, 2) + '.' + value.substr(2, 2);
        value = value.replace(/^[0]+/g, "");
        el.val(value);
        return;
    }

    var minute = 0;
    var second = 0;
    var milisecond = 0;

    if (value.length === 1) {
        value = '0.0' + value;
    } else if (value.length === 2) {
        value = '0.' + value;
    } else if (value.length === 3) {
        second = Number.parseInt(value.substr(0, 1));
        value = value.substr(0, 1) + '.' + value.substr(1, 2);
    } else if (value.length === 4) {
        second = Number.parseInt(value.substr(0, 2));
        value = value.substr(0, 2) + '.' + value.substr(2, 2);
    } else if (value.length === 5) {
        second = Number.parseInt(value.substr(1, 2));
        minute = Number.parseInt(value.substr(0, 1));
        value = value.substr(0, 1) + ':' + value.substr(1, 2) + '.' + value.substr(3, 2);
    } else if (value.length === 6) {
        second = Number.parseInt(value.substr(2, 2));
        minute = Number.parseInt(value.substr(0, 2));
        milisecond = Number.parseInt(value.substr(4, 2));
        if (milisecond >= 50) {
            second = second + 1;
        }
        if (second === 60) {
            second = 0;
            minute = minute + 1;
        }
        value = ('0' + minute).substr(-2, 2) + ':' + ('0' + second).substr(-2, 2) + '.00';
    } else {
        value = '';
    }
    el.val(value);
}


$('[data-competitor-row] a ').click(function () {
    var competitor = $(this).parent().data('competitor-row');
    $('[data-competitor-div]').hide();
    $('[data-competitor-div=' + competitor + ']').show();
    $('[data-competitor-row] a ').removeClass('goal_select');
    $(this).addClass('goal_select');
});