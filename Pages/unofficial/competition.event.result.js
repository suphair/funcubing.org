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

$('[data-competitor]').click(function () {
    click_competitor($(this));

});

function click_competitor(el) {
    $('[data-results]').data('result-competitor-id', el.data('competitor-id'));
    $('[data-results-name]').html(el.data('competitor-name'));
    $('[data-results-attempts]').val(el.data('competitor-attempts'));
    $('[data-results-attempts]').prop('disabled', false);
    $('[data-results] button').show();
    parseAttempts(el.data('competitor-attempts'), 0);
    $('[data-results-attempts]').get(0).setSelectionRange(0, 0);
    $('[data-competitor]').addClass('competitor_result');
    $('[data-competitor]').removeClass('competitor_select');
    el.addClass('competitor_select');
    el.removeClass('competitor_result');
    $('[data-results-attempts]').trigger("focus");
}
;

$('[data-results-attempts]').on('input', function () {
    parseAttempts($(this).val(), $(this).get(0).selectionStart);
});

$('[data-results-attempts]').on('click', function () {
    parseAttempts($(this).val(), $(this).get(0).selectionStart);
});

$('[data-results-attempts]').on('keyup', function () {
    parseAttempts($(this).val(), $(this).get(0).selectionStart);
});

function replaceAttempts(results) {
    results= results.toLowerCase();
    results = results.replace(/dnf/gim, '-');
    results = results.replace(/dns/gim, '0');
    results = results.replace(/dn/gim, '0');
    results = results.replace(/d/gim, '0');
    results = results.replace(/f/gim, '-');
    results = results.replace(/s/gim, '0');
    results = results.replace(/[-]/gim, ' - ');
    results = results.replace(/[^-0-9 ]/gim, '');
    results = results.replace(/ {1,}/g, " ");
    results = results.trim();
    return results;
}

function parseAttempts(results, pos) {
    results = results.toString();
    var result_in = results;
    var attempts_count = $('[data-event-attempts]').data('event-attempts');
    var result_amount = $('[data-event-result]').data('event-result') === 'amount';
    var results_attempts = $('[data-results-attempts]');
    var results_average = $('[data-results-attempts-average]');
    var results_best = $('[data-results-attempts-best]');
    var results_mean = $('[data-results-attempts-mean]');
    var attemps = [];
    for (var i = 1; i <= 5; i++) {
        attemps[i] = $('[data-event-attempt-' + i + ']');
    }
    for (var i = 1; i <= attempts_count; i++) {
        attemps[i] = $('[data-event-attempt-' + i + ']');
        attemps[i].css("color", "var(--black)");
        attemps[i].css("border", "2px solid var(--white)");
    }
    
    

    results = replaceAttempts(results);
    var res_cut = replaceAttempts((result_in.substr(0, pos)));
    var atts = results.split(' ');
    pos_len = 0;
    if (result_in.substr(pos - 1, 1) === ' ' & result_in.substr(pos, 1) !== ' ') {
        if (res_cut !== '') {
            res_current = res_cut.split(' ').length + 1;
        } else {
            res_current = res_cut.split(' ').length;
        }
    } else {
        res_current = res_cut.split(' ').length;
    }

    if (res_current <= attempts_count) {
        attemps[res_current].css("border", "1px solid var(--green)");
        attemps[res_current].css("border-radius", "10px");
    }

    var attemp_input = results.split(' ').length;
    if (attemp_input > attempts_count) {
        results_attempts.css("background-color", " orange");
    } else if (attemp_input < attempts_count) {
        results_attempts.css("background-color", "#FFD");
    } else {
        results_attempts.css("background-color", "var(--light_green)");
    }

    if (attemp_input > 0 && pos > 0) {
        results_best.css("color", "var(--green)");
    } else {
        results_best.css("color", "var(--red)");
    }

    var att;
    var att_correct = 0;
    var attemp_miliseconds = [];
    for (a = atts.length; a < attempts_count; a++) {
        attemps[a + 1].html('dns');
        attemps[a + 1].css("color", "var(--red)");
        attemp_miliseconds[a + 1] = 999999;
    }
    for (a = 0; a < Math.min(atts.length, attempts_count); a++) {
        attemp_miliseconds[a + 1] = 999999;
        att = atts[a];
        if (att === '-') {
            attemps[a + 1].html('dnf');
            attemps[a + 1].css("color", "var(--red)");
        } else if (att == '') {
            if (a > 0) {
                attemps[a + 1].html('dnf');
            } else {
                attemps[a + 1].html('dns');
            }
            attemps[a + 1].css("color", "var(--red)");
        } else if (att == '0') {
            attemps[a + 1].html('dns');
            attemps[a + 1].css("color", "var(--red)");
        } else {
            if (a < attempts_count) {
                attemps[a + 1].css("color", "var(--black)");
                att_correct = att_correct + 1;
                if (result_amount) {
                    attemp_miliseconds[a + 1] = Number(att);
                    attemps[a + 1].html(att);
                } else {
                    att = "000000" + att;
                    att = att.substring(att.length - 6);
                    var minute = att.substr(0, 2);
                    var second = att.substr(2, 2);
                    var milisecond = att.substr(4, 2);
                    if (minute.substr(0, 1) === '0') {
                        minute = minute.substr(1, 1);
                    }
                    attemp_miliseconds[a + 1] = Number(minute) * 60 * 100 + Number(second) * 100 + Number(milisecond);
                    attemps[a + 1].html(minute + ':' + second + '.' + milisecond);
                }
            }

        }
    }

    if (att_correct === 5) {
        var average = 0;
        var max = 0;
        var min = 999999;
        for (var a = 0; a < 5; a++) {
            average = average + attemp_miliseconds[a + 1];
            if (min > attemp_miliseconds[a + 1]) {
                min = attemp_miliseconds[a + 1];
            }
            if (max < attemp_miliseconds[a + 1]) {
                max = attemp_miliseconds[a + 1];
            }

        }

        average = average - min - max;
        results_average.css("color", "var(--green)");
        if (result_amount) {
            average = Math.round(average * 100 / 3.0) / 100;
            average = average.toFixed(2);
            results_average.html(average);
        } else {
            average = Math.round(average / 3.0, 0);
            var minute = Math.floor(average / 60 / 100);
            var second = Math.floor((average - minute * 60 * 100) / 100);
            var milisecond = Math.round(average - minute * 60 * 100 - second * 100, 0);
            results_average.html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
        }
    }
    if (att_correct === 4) {
        var average = 0;
        var min = 999999;
        for (var a = 0; a < 5; a++) {
            if (attemp_miliseconds[a + 1] !== 999999) {
                average = average + attemp_miliseconds[a + 1];
                if (min > attemp_miliseconds[a + 1]) {
                    min = attemp_miliseconds[a + 1];
                }
            }
        }
        average = average - min;
        results_average.css("color", "var(--green)");
        if (result_amount) {
            average = Math.round(average * 100 / 3.0) / 100;
            average = average.toFixed(2);
            results_average.html(average);
        } else {
            average = Math.round(average / 3.0, 0);
            var minute = Math.floor(average / 60 / 100);
            var second = Math.floor((average - minute * 60 * 100) / 100);
            var milisecond = Math.round(average - minute * 60 * 100 - second * 100, 0);
            results_average.html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
        }
    }

    if (att_correct === 3) {

        var mean = 0;
        for (var a = 0; a < 3; a++) {
            mean = mean + attemp_miliseconds[a + 1];
        }
        results_mean.css("color", "var(--green)");
        if (result_amount) {
            mean = Math.round(mean * 100 / 3.0) / 100;
            mean = mean.toFixed(2);
            results_mean.html(mean);
        } else {
            mean = Math.round(mean / 3.0);
            var minute = Math.floor(mean / 60 / 100);
            var second = Math.floor((mean - minute * 60 * 100) / 100);
            var milisecond = Math.round(mean - minute * 60 * 100 - second * 100, 0);
            results_mean.html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
        }
    } else {
        results_mean.css("color", "var(--red)");
        results_mean.html('dnf');
    }

    if (att_correct < 4) {
        results_average.css("color", "var(--red)");
        results_average.html('dnf');
    }

    if (att_correct <= 2 && attemps[3].html() == 'dns' && attemps[4].html() == 'dns' && attemps[5].html() == 'dns') {
        results_average.css("color", "var(--red)");
        results_average.html('-cutoff');
    }

    if (att_correct >= 1) {
        var min = 999999;
        for (var a = 0; a < attempts_count; a++) {
            if (attemp_miliseconds[a + 1] !== 999999) {
                if (min > attemp_miliseconds[a + 1]) {
                    min = attemp_miliseconds[a + 1];
                }
            }
        }
        var minute = Math.floor(min / 60 / 100);
        var second = Math.floor((min - minute * 60 * 100) / 100);
        var milisecond = Math.round(min - minute * 60 * 100 - second * 100, 0);
        results_best.css("color", "var(--green)");
        if (result_amount) {
            results_best.html(min);
        } else {
            results_best.html(minute + ':' + ('00' + second).slice(-2) + '.' + ('00' + milisecond).slice(-2));
        }
    } else {
        results_best.css("color", "var(--red)");
        results_best.html('dnf');
    }

}

$('[data-competitor-chosen]').change(function () {
    var val = $(this).val();
    $(".chosen-select").val('');
    $(".chosen-select").chosen('destroy');
    $(".chosen-select").chosen({max_selected_options: 1});
    click_competitor($('[data-competitor-id=' + val + ']'));
    $('[data-results]').focus();
});

$(".chosen-select").chosen({max_selected_options: 1});
$(".chosen-search-input").focus();


var posts = location.search.substr(1).split('=');
if (posts[1] !== undefined) {
    click_competitor($('[data-competitor-id=' + posts[1] + ']'));
    $('[data-competitor-chosen]').trigger('change');
}

$('[data-results]').submit(function () {
    var attempts_count = $('[data-event-attempts]').data('event-attempts');
    for (var i = 1; i <= attempts_count; i++) {
        var attempt = $('[data-event-attempt-' + i + ']').html();;
        $('#attempt_' + i).val(attempt);
    }
    var competitor=$('[data-results]').data('result-competitor-id');
    $('[data-save-competitor-id]').val(competitor);
    
    var average;
    average=$('[data-results-attempts-average]').html();
    $('#attempt_average').val(average);
    
    var mean;
    mean=$('[data-results-attempts-mean]').html();
    $('#attempt_mean').val(mean);
    
    var best;
    best=$('[data-results-attempts-best]').html();
    $('#attempt_best').val(best);
});



//ParseResults($("#AttemptS").val(),0);
//ParseName($("#CompetitorName").val());