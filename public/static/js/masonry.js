$(function () {
    //页面初始化时执行瀑布流
    var container = $('#container');
    container.masonry({
        itemSelector: '.item',
        isAnimated: true
    }); 
    
    //用户拖动滚动条，达到底部时ajax加载一次数据
    var loading = $("#loading").data("status", 0);//通过给loading这个div增加属性status，来判断执行一次ajax请求，0可以加载，1正在加载，2已全部加载
    $(window).scroll(function () {
        if (loading.data("status") !== 0) return;
        if ($(document).scrollTop() > $(document).height() - $(window).height() - $('.footer').height()) {//页面拖到底部了
            //加载更多数据
            loading.data("status", 1).fadeIn();//在这里将status设为1来阻止继续的ajax请求
            $.get(
                loading.attr('url') + container.find('.item').length + '/3',
                {},
                function (data) {
                    //获取到了数据data,后面用JS将数据新增到页面上
                    var html = "";
                    if ($.isArray(data)) {
                        for (i in data) {
                            var item = data[i];
                            html += '<div class="col-xs-12 col-sm-6 col-md-4 item">';
                            html += '<div class="panel panel-primary">';
                            html += '<div class="panel-heading">';
                            html += '<h3 class="panel-title"><a href="' + item.trackViewUrl + '" target="_blank">' + item.trackName + '</a></h3>';
                            html += '</div>';
                            html += '<div class="panel-body">';
                            html += '<a class="description" href="' + item.trackViewUrl + '" target="_blank">';
                            html += '<img class="ico" src="' + item.artworkUrl100 + '" alt="' + item.trackName + '">';
                            html += '<div>' + item.description + '</div>';
                            html += '</a>';
                            html += '</div>';
                            html += '<div class="panel-footer">';
                            html += '<div class="row">';
                            html += '<div class="col-xs-8">';
                            html += '<a href="' + item.artistViewUrl + '" target="_blank">' + item.artistName + '</a>';
                            html += '<div>版本：' + item.version + '</div>';
                            html += '<div>大小：' + Math.round(item.fileSizeBytes / 100000) / 10 + ' MB</div>';
                            html += '</div>';
                            html += '<div class="col-xs-4">';
                            html += '<a class="btn btn-primary btn-block" href="' + item.trackViewUrl + '" target="_blank">' + item.formattedPrice + '</a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        }
                        var newElems = $(html).css({ opacity: 0 }).appendTo(container);
                        newElems.ready(function () {
                            newElems.animate({ opacity: 1 });
                            container.masonry('appended', newElems, true);
                        });
                        //一次请求完成，将status设为0，可以进行下一次的请求
                        if (data.length < 3) {
                            loading.data("status", 2);
                        } else {
                            loading.data("status", 0);
                        }
                    }
                    loading.fadeOut();
                },
                "json"
                );
        }
    });
});