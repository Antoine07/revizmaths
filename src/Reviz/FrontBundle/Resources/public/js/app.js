(function ($) {
    var constants = {
        url_site: 'http://revizmaths.local'
    };

    $(document.body).on('keyup', "#search", function () {
        var search = $(this).val();
        var userId = $(this).data('user');
        if (search.length > 3) {
            $.post(constants.url_site + '/admin/user/search', {'search': search, 'user': userId}, function (data) {
                if (data.length > 2) {
                    data = $.parseJSON(data);
                    $("#results li").remove();

                    var terms = new Array();
                    $.each(data, function (k, o) {
                        terms.push(o);
                    });

                    for (ind in terms)
                        $("#results ul").prepend('<li><a ' +
                            'data-userid="' + userId + '" ' +
                            'data-moduleid="' + terms[ind].id + '" class="user__commands-action-add">' + terms[ind].name + '(add module)</a></li>');
                } else
                    $("#results ul").html("<li>No results</li>");

            });
        } else $("#results ul").html("<li>No results</li>");
    });

    // activation
    $(document.body).on('click', '.user__commands-action-active', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        var id = $(this).data('id');

        $.post(constants.url_site + '/admin/user/command/active', {'id': id, 'action': action},
            function (data) {

                var terms = $fill._array(data);
                var $message = terms.pop() || 'error contact administrator';

                if (terms.length > 0) {
                    $("#user__commands li").remove();
                    var elements = $fill._elements(terms);
                    $("#user__commands").prepend(elements);
                }

            });
    });

    // delete
    $(document.body).on('click', '.user__commands-action-delete', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        var id = $(this).data('id');

        $.post(constants.url_site + '/admin/user/command/delete', {'id': id, 'action' : action},
            function (data) {
                var terms = $fill._array(data);
                var $message = terms.pop() || 'error contact administrator';

                $("#user__commands li").remove();
                var elements = $fill._elements(terms);
                $("#user__commands").prepend(elements);

                $fill._show_message($message);
            });
    });

    // add new module
    $(document.body).on('click', '.user__commands-action-add', function (e) {
        e.preventDefault();
        var userId = $(this).data('userid');
        var moduleId = $(this).data('moduleid');

        $.post(constants.url_site + '/admin/user/command/add', {'moduleId': moduleId, 'userId': userId},
            function (data) {

               $fill._render(data);

            });
    });

    var $fill =
    {
        _array: function (data) {
            data = $.parseJSON(data);

            var terms = new Array();
            $.each(data, function (k, o) {
                terms.push(o);
            });

            return terms;

        },
        _elements: function (terms) {
            var html = '';
            if (terms.length > 0) {
                for (ind in terms) {
                    var action = 'is locked, click here to enabled it?';
                    if (terms[ind].active == 0) action = 'is not locked, click here to disabled it?';
                    html += '<li><a class="user__commands-action-active" '
                        + 'data-id="' + terms[ind].id + '" '
                        + 'data-action="' + action + '" data-id="' + terms[ind].id + '">' + terms[ind].name + ' ' + action + '</a>  ' +
                        '<a data-id="' + terms[ind].id + '" class="user__commands-action-delete">Or click here to delete it</a></li>';
                }
            }

            return html;
        },
        _render: function (data) {
            var terms = $fill._array(data);
            var $message = terms.pop() || 'error contact administrator';

            if (terms.length > 0) {
                $("#user__commands li").remove();
                var elements = $fill._elements(terms);
                $("#user__commands").prepend(elements);
            }

            $fill._show_message($message);

        },
        _show_message: function (message) {
            this._container_message.html('');
            this._container_message.hide().html(message).fadeIn().fadeOut(this._duration);
        },
        _container_message: $("#message"),
        _duration: 1000
    };

})(jQuery);