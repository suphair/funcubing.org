$('[data-owner-select]').change(function () {
    var owner = $('[data-owner-select]').val();
    $('[data-owner]').show();
    if (owner != 0) {
        $('[data-owner]').each(function () {
            if ($(this).data('owner') != owner) {
                $(this).hide();
            }
        });
    }
});