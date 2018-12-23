$(function () {
    var nav = document.getElementById('sidebar').getElementsByTagName('li');
    $('#sidebar a').each(function (i, n) {
        nav[i].onclick = function () {
            for (var i = 0; i < nav.length; i++) {
                $(this).parent('li').addClass('active');
                $(this).parent().parent().parent('li').addClass('open');
            }
            this.className = 'current';
        }
    })

});