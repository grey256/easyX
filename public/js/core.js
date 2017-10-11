$(document).ready(function() {
    $('#notice').on('mouseover mouseout', function(event) {
        if (event.type == "mouseover") {
            var msgListElement = $('#message-list');
            var messageNum = parseInt(msgListElement.children().length) - 1;
            $('#message-list').show();
            if (messageNum > 10) {
                for (var i = messageNum - 1; i > 9; --i) {
                    msgListElement.children().eq(i).hide();
                }
                $('#more-message').show();
            }
        }
    });

    $('#message-list').on('mouseover mouseout', function(e) {
        if (e.type == "mouseover") {
            $('#message-list').show();
        }
    });

    $('#message-list').mousedown(function(e) {
        e.stopPropagation();
    });

    $(document).mousedown(function() {
        if ($('#message-list').is(':visible')) {
            $('#message-list').hide();

            //不再推送被看过的消息
            $.ajax({
                type:"POST",
                dataType:"json",
                data:{},
                url:'Message/Message/delMsg',
                success:function (data) {
                    // console.log('del msg success');
                },
                error:function (er) {
                    console.log('some error in core.js:delMsg ajax: ' + er);
                }
            });
        }
    });
});
