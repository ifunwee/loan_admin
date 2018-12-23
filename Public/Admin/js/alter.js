/**
 * Created by Administrator on 2015/4/27.
 */

$("#sub").submit(function () {
    var id = $('.id').attr('value');
    var name = $('#inputusername').val();
    var remark = $('.remark').val();
    var status = $('.selectpicker').val();

    $.post("../../edit", {id: id, name: name, remark: remark, status: status}, function (data) {
        if (1 == data.status) {
            location.href = fun['URL'];
        } else {
            alert(data.info);
        }
    });
    return false;
});
