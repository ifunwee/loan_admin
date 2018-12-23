function init_menu() {
    var timeout = 10;
    var closetimer = 0;
    var ddmenuitem = 0;
    return function () {
        $('#menu > li').bind('mouseover', function () {
            if (closetimer) {
                window.clearTimeout(closetimer);
                closetimer = null;
            }
            if (ddmenuitem) {
                ddmenuitem.css('visibility', 'hidden');
            }
            ddmenuitem = $(this).find('ul').eq(0).css('visibility', 'visible');
        });
        $('#menu > li').bind('mouseout', function () {
            closetimer = window.setTimeout(function () {
                if (ddmenuitem) {
                    ddmenuitem.css('visibility', 'hidden');
                }
            }, timeout);
        });
    };
}

function upload_img(upload_url, callback) {
    var editor = KindEditor.editor({
        allowFileManager: false,
        uploadJson: upload_url
    });
    editor.loadPlugin('limage', function () {
        editor.plugin.imageDialog({
            showRemote: false,
            clickFn: function (url, title, width, height, border, align) {
                callback && callback(url, title, width, height);
                editor.hideDialog();
            }
        });
    });
}

function upload_img_batch(upload_url, callback) {
    var editor = KindEditor.editor({
        allowFileManager: false,
        uploadJson: upload_url
    });
    editor.loadPlugin('multiimage', function () {
        editor.plugin.multiImageDialog({
            clickFn: function (urlList) {
                callback && callback(urlList);
                editor.hideDialog();
            }
        });
    });
}


function upload_file(inputBtn, upload_url, callback, unbindread) {
    var initFunc = function (K) {
        var editor = K.editor({
            allowFileManager: false,
            uploadJson: upload_url
        });
        K('#' + inputBtn).click(function () {
            editor.loadPlugin('limage', function () {
                editor.plugin.imageDialog({
                    showRemote: false,
                    clickFn: function (url, title, width, height, border, align) {
                        callback && callback(url, title, width, height);
                        editor.hideDialog();
                    }
                });
            });
        });
    };
    if (!unbindread) {
        KindEditor.ready(function (K) {
            initFunc(K);
        });
    }
    else {
        initFunc(KindEditor);
    }
}

function ajax_tip() {
    this.timer = null;
    var bak_title = document.title;

    this.show = function () {
        this.timer = setInterval(function () {
            document.title == '操作进行中...'
            document.title = document.title == '' ? '操作进行中...' : '';
        }, 1000);

    };
    this.hide = function () {
        clearInterval(this.timer);
        document.title = bak_title;
    };
}

function show_ajax_tip() {
    var ajx = new ajax_tip();
    ajx.show();
    return ajx;
}

function hide_ajax_tip(ajx) {
    ajx.hide();
}

function show_dialog(title, url) {
    var url = url;
    var btn = [];
    var callfunc = null;
    var width = 500;
    var len = 0;
    if (typeof(arguments[arguments.length - 1]) == 'function') {
        callfunc = arguments[arguments.length - 1];
        width = parseInt(arguments[arguments.length - 2]);
        len = arguments.length - 2;
    }
    else {
        width = parseInt(arguments[arguments.length - 1]);
        len = isNaN(width) ? arguments.length : arguments.length - 1;
    }

    for (var i = 2; i < len; i++) {
        btn[i - 2] = {caption: arguments[i].title, callback: arguments[i].callback}
    }
    btn[i - 2] = {
        caption: '取消', callback: function () {
            dlg.close();
        }
    };
    if (btn.length == 1) {
        btn = [];
    }
    var loading = show_loading();
    var dlg = new $.Zebra_Dialog({
        source: {'ajax': url + '/show/1'},
        width: isNaN(width) ? 500 : width,
        type: false,
        auto_close: false,
        overlay_close: false,
        title: title,
        buttons: btn,
        close_overlay: function () {
            loading.remove();
        },
        callback: function (result) {
            callfunc && callfunc(result);
        }
    });
    return dlg;
}

function show_dialog_inline(id, title, url, callfunc) {
    var url = url;
    var width = parseInt(arguments[arguments.length - 1]);
    var len = isNaN(width) ? arguments.length : arguments.length - 1;
    var loading = show_loading();
    var dlg = new $.Zebra_Dialog({
        source: {'ajax': url + '/show/1'},
        width: isNaN(width) ? 500 : width,
        inline: $("#" + id),
        auto_close: false,
        title: title,
        callback: function (result) {
            loading.close();
            callfunc && callfunc(result);
        }
    });
    return dlg;
}

function show_question(title, content, yes_href, no_href) {
    new $.Zebra_Dialog('<div class="ZebraDialog_Tip_Info">' + content + '</div>', {
        'title': title,
        'modal': true,
        'type': 'question',
        'submodel': true,
        'buttons': [
            {
                caption: '是', callback: function () {
                if (yes_href) {
                    window.location.href = yes_href;
                }
            }
            },
            {
                caption: '否', callback: function () {
                if (no_href) {
                    window.location.href = no_href;
                }
            }
            }
        ]
    });
}

function show_warning(message) {
    new $.Zebra_Dialog('<div class="ZebraDialog_Tip_Info">' + message + '</div>', {
        'buttons': false,
        'modal': true,
        'type': 'warning',
        'auto_close': 3000,
        'submodel': true
    });
}

function show_message(message) {
    new $.Zebra_Dialog('<div class="ZebraDialog_Tip_Info">' + message + '</div>', {
        'buttons': false,
        'modal': false,
        'type': 'confirmation',
        'auto_close': 2000,
        'submodel': true
    });
}

function show_loading() {
    return new $.Zebra_Dialog('', {
        'buttons': false,
        'type': 'loading',
        'modal': 'iframe',
        'submodel': true,
        'show_close_button': false,
        'overlay_close': false
    });
}

function show_loading_top() {
    return new $.Zebra_Dialog('', {
        'buttons': false,
        'type': 'loading',
        'modal': 'iframe',
        'submodel': true,
        'show_close_button': false,
        'overlay_close': false,
        'zindex': 100000
    });
}

function show_error(message) {
    new $.Zebra_Dialog('<div class="ZebraDialog_Tip_Info">' + message + '</div>', {
        'buttons': false,
        'modal': false,
        'type': 'error',
        'auto_close': false,
        'submodel': true,
        'zindex': 999999
    });
}

function post(url, param, dlg, callfunc) {
    var loading = show_loading_top();
    $.post(url, param, function (res) {
        loading.close();
        var msg = '';
        if (res.info.length > 0) {
            msg = res.info;
        } else if (res.status == 1) {
            msg = '操作成功！';
        }
        else {
            msg = res.info;
        }

        if (res.status == 1) {
            dlg && dlg.close();
            !dlg && show_message(msg);
            callfunc && callfunc(res);
            return;
        }
        if (res.status == 0) {
//            dlg && dlg.close();
            show_error(msg);
        }
    }, 'JSON');
    return false;
}

function getparamobj(param) {
    var arr = {};
    var len = param.length;
    for (var i = 0; i < len; i++) {
        arr[param[i].name] = param[i].value;
    }
    return arr;
}

function getforumelements(param) {
    var arr = {};
    var len = param.length;
    for (var i = 0; i < len; i++) {
        if (arr[param[i].name] == undefined) {
            if (param[i].name.match(/(\[|\])/)) {
                arr[param[i].name] = [param[i].value];
            }
            else {
                arr[param[i].name] = param[i].value;
            }
        }
        else {
            if ($.isArray(arr[param[i].name])) {
                arr[param[i].name].push(param[i].value);
            }
            else {
                arr[param[i].name] = [arr[param[i].name], param[i].value];
            }
        }
    }
    return arr;
}
function dailog_art(msg) {
    if (typeof(msg) != "string") {
        var _msg = msg.msg;
        var _time = msg.time
    } else {
        var _msg = msg;
        var _time = 1000;
    }
    window.top.art.dialog({
            title: '提示',
            content: '<span style="font-size:14px">' + _msg + '</span>',
            lock: true,
            width: '360',
            height: '120',
            time: _time,
            ok: function () {
                window.top.location.reload();
                window.top.art.dialog({id: 'edit'}).close();
            }
        }
    );

    //解决提示内容带iframe弹窗后不能居中的问题
    window.top.$('.aui_content').css({
        width: '',
        height: ''
    });
}

function sprintf() {
    if (arguments.length == 0)
        return null;
    var str = arguments[0];
    for (var i = 1; i < arguments.length; i++) {
        var re = new RegExp('\\{' + (i - 1) + '\\}', 'gm');
        str = str.replace(re, arguments[i]);
    }
    return str;
}

/**
 * 上传图片至七牛
 * @param dir
 * @param bucket
 * @param callback
 */
function upload_img_to_qiniu(dir, bucket, callback) {
    var upload_url = '/Admin/Upload/uploadFile/bucket/' + bucket + '/dir/' + dir;
    var editor = KindEditor.editor({
        allowFileManager: false,
        uploadJson: upload_url
    });
    editor.loadPlugin('limage', function () {
        editor.plugin.imageDialog({
            showRemote: false,
            clickFn: function (url, title, width, height, border, align) {
                callback && callback(url, title, width, height);
                editor.hideDialog();
            }
        });
    });
}

/**
 * 上传图片对话框
 * @param obj
 * @param id
 * @param dir
 * @param show_width
 * @param bucket
 */
function open_upload_dlg(obj, id, dir, show_width, bucket) {
    upload_img_to_qiniu(dir, bucket, function (url, title, width, height) {
        $(obj).parent().find('.' + id).val(url);
        url = fun.IMAGE_DOMAIN + url;
        $(obj).parent().find(".preview").attr('src', url + "?imageView/2/w/" + show_width).show();
        $(obj).parent().find(".width").val(width);
        $(obj).parent().find(".height").val(height);
    })
}

