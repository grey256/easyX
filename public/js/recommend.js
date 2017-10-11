$(document).ready(function() {
    //Star
    $(document.body).on('click', '.give-star', function(event) {
        if ($(this)) {
            event.preventDefault();
            event.stopPropagation();

            var selfElement = $(this);
            var fid = selfElement.attr('fid');
            var type = selfElement.attr('f-type');
            $.ajax({
                type:"POST",
                dataType:"json",
                data:{type:type,fid:fid,model:'star'},
                url:'Recommend/Star/mark',
                success:function (data) {
                    if (data == true) {
                        var starDetailElement = $(".star-detail");
                        var preNum = $.trim(starDetailElement.text());
                        starDetailElement.empty();
                        starDetailElement.text(parseInt(preNum) + 1);
                        selfElement.attr('class', 'btn btn-default btn-sm unstar');
                        selfElement.html('<span class="glyphicon glyphicon-star-empty"></span> Unstar');
                    } else {
                        console.log('there are some errors in the backend(in recommend.js:give-star ajax)');
                    }
                },
                error:function (er) {
                    console.log('some error in recommend.js:give-star ajax: ' + er);
                }
            });
        }
    });

    //Unstar
    $(document.body).on('click', '.unstar', function(event) {
        if ($(this)) {
            event.preventDefault();
            event.stopPropagation();

            var selfElement = $(this);
            var fid = $(this).attr('fid');
            var type = $(this).attr('f-type');
            $.ajax({
                type:"POST",
                dataType:"json",
                data:{type:type,fid:fid,model:'star'},
                url:'Recommend/Star/unMark',
                success:function (data) {
                    if (data == true) {
                        var starDetailElement = $(".star-detail");
                        var preNum = $.trim(starDetailElement.text());
                        starDetailElement.empty();
                        starDetailElement.text(parseInt(preNum) - 1);
                        selfElement.attr('class', 'btn btn-default btn-sm give-star');
                        selfElement.html('<span class="glyphicon glyphicon-star"></span> Star');
                    } else {
                        console.log('there are some errors in the backend(in recommend.js:give-star ajax)');
                    }
                },
                error:function (er) {
                    console.log('some error in recommend.js:give-star ajax: ' + er);
                }
            });
        }
    });

    //关注
    $(document.body).on('click', '.give-watch', function(event) {
        if ($(this)) {
            event.preventDefault();
            event.stopPropagation();

            var selfElement = $(this);
            var fid = selfElement.attr('fid');
            var type = selfElement.attr('f-type');
            $.ajax({
                type:"POST",
                dataType:"json",
                data:{type:type,fid:fid,model:'watch'},
                url:'Recommend/Watch/mark',
                success:function (data) {
                    if (data == true) {
                        var watchDetailElement = $(".watch-detail");
                        var preNum = $.trim(watchDetailElement.text());
                        watchDetailElement.empty();
                        watchDetailElement.text(parseInt(preNum) + 1);
                        selfElement.attr('class', 'btn btn-default btn-sm unwatch');
                        selfElement.html('<span class="glyphicon glyphicon-eye-close"></span> 取消关注');
                    } else {
                        console.log('there are some errors in the backend(in recommend.js:give-watch ajax)');
                    }
                },
                error:function (er) {
                    console.log('some error in recommend.js:give-watch ajax: ' + er);
                }
            });
        }
    });

    //取消 关注
    $(document.body).on('click', '.unwatch', function(event) {
        if ($(this)) {
            event.preventDefault();
            event.stopPropagation();

            var selfElement = $(this);
            var fid = selfElement.attr('fid');
            var type = selfElement.attr('f-type');
            $.ajax({
                type:"POST",
                dataType:"json",
                data:{type:type,fid:fid,model:'watch'},
                url:'Recommend/Watch/unMark',
                success:function (data) {
                    if (data == true) {
                        var watchDetailElement = $(".watch-detail");
                        var preNum = $.trim(watchDetailElement.text());
                        watchDetailElement.empty();
                        watchDetailElement.text(parseInt(preNum) - 1);
                        selfElement.attr('class', 'btn btn-default btn-sm give-watch');
                        selfElement.html('<span class="glyphicon glyphicon-eye-open"></span> 关注');
                    } else {
                        console.log('there are some errors in the backend(in recommend.js:give-watch ajax)');
                    }
                },
                error:function (er) {
                    console.log('some error in recommend.js:give-watch ajax: ' + er);
                }
            });
        }
    });

    //心碎
});
