$("a[name='del']").click(function () {


    $.get($(this).attr('href'), {}, function (result) {

        alert(result.info);

        location.href = fun['URL'];

    });
    return false;
});

$('.edit').click(function () {
    if ($("input[name='select']:checked").val() == null) {
        alert('请选择编辑项');
    } else {
        window.location.href = fun['URL'] + "/alterUser/id/" + $("input[name='select']:checked").val();
    }

});

$('.del').click(function () {
    if ($("input[name='select']:checked").val() == null) {
        alert('请选择编辑项');
    } else {
        window.location.href = fun['URL'] + "/del/id/" + $("input[name='select']:checked").val();
    }
});