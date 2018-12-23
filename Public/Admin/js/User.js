/**
 * Created by Administrator on 2015/5/4.
 */
$('.unlock').click(function () {
    var url = $(this).attr('href');
    $.post(url, {}, function (data) {
        alert(data.info);
        if (data.status == 1) {
            location.reload();
        }
    });
    return false;
});

$('.lock').click(function () {
    var url = $(this).attr('href');
    $.post(url, {}, function (data) {
        alert(data.info);
        if (data.status == 1) {
            location.reload();
        }
    });
    return false;
});