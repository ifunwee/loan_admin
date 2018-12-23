$(function () {
    var curr = window.location.pathname;

    $('#sidebar a').each(function (i, n) {
        var href = $(this).attr('href');
        var curURI = curr.match(/(\/.*?\/.*?\/)/g);
        curURI = !!curURI ? curURI[0] : '';
        var r = new RegExp(curURI);
        if (!!curURI && r.test(href)) {
            $(this).parent('li').addClass('active');
            $(this).parent().parent().parent('li').addClass('open');
        }
        else {
            $(this).parent().parent().parent('li').addClass('open');
        }

    })
});