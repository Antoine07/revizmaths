(function ($) {
    $.getScript('constants.js', function () {
        alert('ooo');
        var url_site = constants.url_site;

        $("#search").keyup(function () {
            var search = $(this).val();

            if (search.length > 3) {
                $.ajax({
                    url: url_site + 'admin/user/search/' + search,
                    type: 'GET',
                    dataType: 'json', // On désire recevoir du json
                    beforeSend: function () {
                        if ($(".loading").length == 0) {
                            $("form#search .results").append('<div class="loading"></div>');
                        }
                    },
                    success: function (response, status) {
                        $("form#search .results").remove();
                        $("#results").html(response).show();
                    },
                    error: function (results, status, error) {

                    }
                });
            } else
                $("#results").html('').show();

        });
    });
})(jQuery);