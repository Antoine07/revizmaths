(function ($) {
    var constants = {
        url_site: 'http://revizmaths.local'
    };
    $(document.body).on('keyup', "#search", function () {
        var search = $(this).val();
        if (search.length > 3) {
            $.post(constants.url_site + '/admin/user/search', {'search': search}, function (data) {
                if (data.length > 2) {
                    data = $.parseJSON(data);
                    $("#results li").remove();

                    var terms = new Array();
                    $.each(data, function (k, o) {
                        terms.push(o);
                    });

                    for (ind in terms)
                        $("#results ul").prepend('<li><a class="search__add_module">' + terms[ind].name + '(add module)</a></li>');
                } else
                    $("#results ul").html("<li>No results</li>");

            });
        } else $("#results ul").html("<li>No results</li>");
    });
    $(document.body).on('click', '.user__commands_active_action', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        var id = $(this).data('id');

        $.post(constants.url_site + '/admin/user/command/active', {'id': id, 'action': action},
            function (data) {
                data = $.parseJSON(data);

                $("#user__commands li").remove();

                var terms = new Array();
                $.each(data, function (k, o) {
                    terms.push(o);
                });
                terms.reverse();
                for (ind in terms) {
                    var action = 'disabled';
                    if (terms[ind].active == 0) action = 'enabled';

                    $("#user__commands").prepend('<li><a class="user__commands_active_action" '
                        + 'data-id="' + terms[ind].id + '" '
                        + 'data-action="' + action + '">' + terms[ind].name + ' ' + action + '</a></li>');
                }

            });
    });

})(jQuery);