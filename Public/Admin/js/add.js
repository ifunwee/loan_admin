$("#sub").submit(function () {
    //console.log($(this).inArray());

    $.post(fun['URL'] + "/insert", $(this).serialize(), function (result) {

        if (result.status == 1) {
            location.href = fun['URL'];
        }
    });
    //alert($(this).serialize());
    return false;
});

