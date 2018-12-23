/**
 * Created by Administrator on 2015/4/28.
 */
$("#add").submit(function () {
    var url = $(this).attr('action');
    $.post(url, $(this).serialize(), function (data) {
        if (1 == data.status) {
            window.location.href = fun['URL'] + '/userlist';
        } else {
            alert(data.info);
        }
    });
    return false;
});
