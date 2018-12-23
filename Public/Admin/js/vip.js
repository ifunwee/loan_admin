/**
 * Created by Administrator on 2015/5/4.
 */
$('.del').click(function () {
    var url = $(this).attr('href');
    $.post(url, {}, function (data) {
        alert(data.info);
        if (data.status == 1) {
            location.reload();
        }
    });
    return false;
});

$('.addnode').submit(function () {
    var url = $(this).attr('action');
    $.post(url, $(this).serializeArray(), function (data) {
        alert(data.info);
        if (data.status == 1) {
            window.location.href = fun['URL'] + "/index.html";
        }
    });
    return false;
});

$('.alt').submit(function () {
    var url = $(this).attr('action');
    $.post(url, $(this).serializeArray(), function (data) {
        alert(data.info);
        if (data.status == 1) {
            window.location.href = fun['URL'] + "/index.html";
        }
    });
    return false;
});
