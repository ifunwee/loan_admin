/**
 * Created by Administrator on 2015/4/22.
 */
$(function () {
    var form = $("#loginForm");

    form.submit(function () {

        $.post(form.attr("action"), form.serializeArray(), function (data) {
            if (data.status == 1) {
                window.location.href = data['info']['link'];
            } else {
                alert(data.info);
                if (data.info == '请确认验证码后重新输入') {
                    $("#verify").focus();

                    $("#verify").val("");
                    pe_yzm($("#verifyImg"));
                }
                else {
                    $("#username").focus();

                    $("#verify").val("");
                }
            }
        });
        return false;
    });
})

