var format_result = $('[data-event-result]').data('event-result');
var is_time = (format_result === 'time');
var is_amount_asc = (format_result === 'amount_asc');
var is_amount_desc = (format_result === 'amount_desc');
const DNF = ['f', 'F', '/', '-', 'd', 'D'];
const DNS = ['s', 'S', '*'];
$('[data-competitor-chosen]').change(function () {
    var val = $(this).val().toString();
    click_competitor($('[data-competitor-id=' + val + ']'));
});
$(".chosen-select").chosen({max_selected_options: 1});
$(".chosen-search-input").focus();
$('[data-results-attempt]').on('input', function () {
    enter_result($(this));
});
$('[data-results-attempt]').on('click', function () {
    enter_result($(this));
});
function enter_result(el) {
    el.val(result_format(el.val()));
    var attempts = $("[data-event-attempts]").data('event-attempts');
    var results = '';
    var result;
    for (var i = 1; i <= attempts; i++) {
        result = zip_result($('[data-results-attempt=' + i + ']').val());
        if (i < attempts) {
            result = result + ' ';
        }
        results = results + result;
    }
    $('[data-results-attempts]').val(results);
}

function zip_result(result) {
    result = result.replace(/[\.\:]/g, '');
    result = result.replace(/^0+/g, '');
    result = result.replace(/DNF/g, '-');
    result = result.replace(/DNS/g, '0');
    if (result == '') {
        result = '_';
    }
    return result;
}

function public_result(result) {
    result = result.replace(/^0+/g, '');
    result = result.replace(/^:/g, '');
    result = result.replace(/^0+/g, '');
    result = result.replace(/^\./g, '0.');
    result = result.replace(/_/g, '');
    return result;
}


$('[data-results]').submit(function () {

    var attempts_count = $('[data-event-attempts]').data('event-attempts');
    for (var i = 1; i <= attempts_count; i++) {
        var attempt = $('#attempt_' + i).val();
        $('#attempt_' + i).val(public_result(result_format(attempt)));
    }

    var best = -1;
    var average = 0;
    var mean = 0;
    var complete = 0;
    var wrong = 0;
    var worst = 0;
    var sum = 0;
    var worst_i = 0;
    var best_i = 0;
    var wrong_i = 0;
    var empty = 0;
    for (var i = 1; i <= attempts_count; i++) {
        var attempt = $('#attempt_' + i).val();
        var centisecond = input_to_centisecond(attempt);
        if ((centisecond < best || best == -1) && centisecond > 0) {
            best = centisecond;
            best_i = i;
        }
        if (centisecond > 0) {
            complete++;
            sum += centisecond;
        }
        if (centisecond < 0) {
            if (wrong_i === 0) {
                wrong_i = i;
            }
            wrong++;
        }
        if (centisecond === 0) {
            empty++;
        }
    }

    for (var i = 1; i <= attempts_count; i++) {
        var attempt = $('#attempt_' + i).val();
        var centisecond = input_to_centisecond(attempt);
        if (centisecond > worst && best_i !== i) {
            worst = centisecond;
            worst_i = i;
        }
    }

    if (complete == 5) {
        average = Math.round((sum - worst - best) / 3, 2);
    }
    if (complete == 4) {
        average = Math.round((sum - best) / 3, 2);
    }

    if (complete == 3) {
        if (is_time) {
            mean = Math.round(sum / 3, 2);
        } else {
            mean = Math.round(sum * 100 / 3, 0) / 100;
        }
    }

    if (wrong >= 2 && empty == 0) {
        average = -1;
    }
    if (wrong >= 1 && empty == 0) {
        mean = -1;
    }

    if (is_amount_desc && worst) {
        var t = best;
        var t_i = best_i;
        best = worst;
        best_i = worst_i;
        worst = t;
        worst_i = t_i;
    }

    var exclude = 0;
    if (attempts_count === 5 && empty === 0) {
        if (complete === 5) {
            exclude = worst_i + best_i * 10;
        }
        if (complete >= 1 && complete < 5) {
            exclude = wrong_i + best_i * 10;
        }
        if (complete === 0) {
            exclude = 12;
        }
        $('[data-results-exclude]').val(exclude);
    }

    if (wrong == 0 && complete == 0) {
        average = 0;
        mean = 0;
        best = 0;
        $('[data-results-attempts]').val('');
    }

    if (is_time) {
        $('[data-results-attempts-best]').val(public_result(result_format_enter(best)));
        $('[data-results-attempts-average]').val(public_result(result_format_enter(average)));
        $('[data-results-attempts-mean]').val(public_result(result_format_enter(mean)));
    } else {
        $('[data-results-attempts-best]').val(result_format_amount_100(best));
        $('[data-results-attempts-average]').val(result_format_amount_100(average));
        $('[data-results-attempts-mean]').val(result_format_amount_100(mean));
    }

    var competitor = $('[data-results]').data('result-competitor-id');
    $('[data-save-competitor-id]').val(competitor);
    var average;
    var best;
    var mean;
    average = $('[data-results-attempts-average]').html();
    mean = $('[data-results-attempts-mean]').html();
    best = $('[data-results-attempts-best]').html();
    $('#attempt_mean').val(mean);
    $('#attempt_average').val(average);
    $('#attempt_best').val(best);
});
function result_format(value) {
    if (is_time) {
        var second_first = value.toString().slice(-5)[0];
        if (DNF.indexOf(value.toString().slice(-1)) == -1
                && DNS.indexOf(value.toString().slice(-1)) == -1
                && (second_first == '6' ||
                        second_first == '7' ||
                        second_first == '8' ||
                        second_first == '9') && value.length >= 5) {
            value = value.replace(/\D+/g, '');
            value = '000000' + value;
            value = value.substr(value.length - 6);
            minute_format = value.substr(value.length - 6, 2);
            second_format = value.substr(value.length - 4, 2);
            centisecond_format = value.substr(value.length - 2);
            return  minute_format + ':' + second_format + '.' + centisecond_format;
        }
        return result_format_enter(input_to_centisecond(value));
    } else {
        return result_format_amount(value);
    }
}

function result_format_amount(val) {
    val = val.toString();
    if (val === '') {
        return  '';
    } else if (DNF.indexOf(val.slice(-1)) !== -1) {
        return 'DNF';
    } else if (DNS.indexOf(val.slice(-1)) !== -1) {
        return  'DNS';
    }
    return val.replace(/[^+\d]/g, '');
}

function result_format_amount_100(val) {
    if (val == -1) {
        return 'DNF';
    }

    if (val.toString().indexOf('.') !== -1) {
        return result_format_amount(val) / 100;
    }
    return result_format_amount(val);
}



function input_to_centisecond(val) {
    var centisecond;
    value = val.toString().replace(/[\.\:]/g, '');
    if (value === '') {
        centisecond = 0;
    } else if (DNF.indexOf(value.slice(-1)) !== -1) {
        centisecond = -1;
    } else if (DNS.indexOf(value.slice(-1)) !== -1) {
        centisecond = -2;
    } else {
        value = value.replace(/\D+/g, '');
        value = '000000' + value;
        value = value.substr(value.length - 6);
        minute = Number.parseInt(value.substr(value.length - 6, 2));
        second = Number.parseInt(value.substr(value.length - 4, 2));
        centisecond = Number.parseInt(value.substr(value.length - 2));
        value = minute * 100 * 60 + second * 100 + centisecond;
        centisecond = value;
    }

    return centisecond;
}

function result_format_enter(result) {

    if (result == -1) {
        return 'DNF';
    }
    if (result == -2) {
        return 'DNS';
    }
    if (result == 0) {
        return '';
    }

    var minute = Math.floor(result / (100 * 60));
    result = result - minute * 100 * 60;
    var second = Math.floor(result / 100);
    result = result - second * 100;
    var centisecond = result;
    if (minute < 10) {
        minute_format = '0' + minute;
    } else {
        minute_format = minute;
    }

    if (second < 10) {
        second_format = '0' + second;
    } else {
        second_format = second;
    }

    if (centisecond < 10) {
        centisecond_format = '0' + centisecond;
    } else {
        centisecond_format = centisecond;
    }

    return  minute_format + ':' + second_format + '.' + centisecond_format;
}

function click_competitor(el) {
    $(".chosen-select").val('');
    $(".chosen-select").chosen('destroy');
    $(".chosen-select").chosen({max_selected_options: 1});
    $('[data-results]').data('result-competitor-id', el.data('competitor-id'));
    $('[data-save-competitor-id]').val(el.data('competitor-id'));
    $('[data-results-name]').val(el.data('competitor-name'));
    $('[data-results-attempts]').val(el.data('competitor-attempts'));
    $('[data-results-attempts]').prop('disabled', false);
    $('[data-results] button').show();
    var attempts_count = $('[data-event-attempts]').data('event-attempts');
    for (var i = 1; i <= attempts_count; i++) {
        $('[data-results-attempt=' + i + ']').val(result_format(el.data('competitor-attempt' + i)));
    }

    $(".chosen-search-input").val(el.data('competitor-name'));
    $("[data-competitor-chosen]").data('placeholder', el.data('competitor-name'));
    $('#attempt_1').focus();
    $("[data-competitor-name]").removeClass("choose_row");
    $(el).addClass("choose_row");
}

$('[data-competitor]').click(function () {
    click_competitor($(this));
});
$('[data-competitor]').hover(function () {
    $(this).addClass("cursor");
    $(this).addClass("select_row");
});
$('[data-competitor]').mouseleave(function () {
    $(this).removeClass("cursor");
    $(this).removeClass("select_row");
});
$('[data-results]').keydown(function () {
    var key = event.which || event.keyCode;
    if (key === 13 || key === 40 || key === 38) {
        fn = function (elements, start) {
            for (var i = start; i < elements.length; i++) {
                var element = elements[i];
                if ((element.tagName === 'INPUT'
                        || element.tagName === 'BUTTON')
                        && element.disabled === false) {
                    element.focus();
                    break;
                }
            }
            return i;
        };
        fn_up = function (elements, start) {
            for (var i = start; i > 0; i--) {
                var element = elements[i];
                if ((element.tagName === 'INPUT'
                        || element.tagName === 'BUTTON')
                        && element.disabled === false) {
                    element.focus();
                    break;
                }
            }
            return i;
        };
        var current = event.target || event.srcElement;
        for (var i = 0; i < this.elements.length; i++) {
            if (this.elements[i] === current) {
                break;
            }
        }
        if (key === 38) {
            if (fn_up(this.elements, i - 1) === 0) {
                fn_up(this.elements, 0);
            }
        } else {
            if (fn(this.elements, i + 1) === this.elements.length) {
                fn(this.elements, 0);
            }
        }
        if (current.tagName !== 'BUTTON') {
            return false;
        }
    }
});
$("#submit_results").focus(function () {
    var attempts_count = $('[data-event-attempts]').data('event-attempts');
    for (var i = 1; i <= attempts_count; i++) {
        var attempt = $('#attempt_' + i).val();
        $('#attempt_' + i).val(public_result(result_format(attempt)));
    }
});
$("#submit_results").focusout(function () {
    var attempts_count = $('[data-event-attempts]').data('event-attempts');
    for (var i = 1; i <= attempts_count; i++) {
        var attempt = $('#attempt_' + i).val();
        $('#attempt_' + i).val(result_format(attempt));
    }
}
);

