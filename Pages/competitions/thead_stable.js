TableThing = function (params) {
    settings = {
        table: $('table.thead_stable'),
        thead: []
    };

    this.fixThead = function () {
        if (settings.table.length == 0) {
            return;
        }
        // empty our array to begin with
        settings.thead = [];
        // loop over the first row of td in &lt;tbody> and get the widths of individual &lt;td>'s
        $('tbody tr:eq(0) td', settings.table).each(function (i, v) {
            settings.thead.push($(v).width());
            $(v).width($(v).width());
        });
        // now loop over our array setting the widths we've got to the &lt;th>'s
        for (i = 0; i < settings.thead.length; i++) {
            $('thead th:eq(' + i + ')', settings.table).width(settings.thead[i]);
        }

        // here we attach to the scroll, adding the class 'fixed' to the &lt;thead> 
        $(window).scroll(function () {
            var windowTop = $(window).scrollTop();
            console.dir(windowTop + ' ' + settings.table.offset().top);
            if (windowTop > settings.table.offset().top) {
                $("thead", settings.table).addClass("fixed");
            } else if (windowTop < (settings.table.offset().top - 40)) {
                $("thead", settings.table).removeClass("fixed");
            }
        });
    }
}
$(function () {
    var table = new TableThing();
    table.fixThead();
    $(window).resize(function () {
        table.fixThead();
    });
});