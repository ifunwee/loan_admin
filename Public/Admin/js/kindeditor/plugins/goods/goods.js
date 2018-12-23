/**
 * Created with JetBrains PhpStorm.
 * User: hsl
 * Date: 14-7-17
 * Time: 上午11:23
 * To change this template use File | Settings | File Templates.
 */
KindEditor.plugin('goods', function (K) {
    var self = this, name = 'goods';
    self.plugin.goodsDialog = function (options) {
        var clickFn = options.clickFn;
        var dialogWidth = 420,
            dialogHeight = 200;
        var html = [
            '<div style="padding:20px;">',
            '<div class="ke-dialog-row">',
            '<table><tbody>',
            '<tr><td>' + self.lang('goodsID') + '：</td><td><input type="text" name="goods_id" class="text" style="height:30px;">&nbsp;<input type="button" id="check_btn" value="' + self.lang('isCheck') + '" style="height:30px;line-height: 20px;padding: 0 10px;"/></td></tr><tr><td colspan="2" id="img" style="padding-left: 80px;"></td></tr>',
            '</tbody></table>',
            '<input type="hidden" name="pic_url"><input type="hidden" name="isChecked" value="0"/></div>',
            '</div>'
        ].join('');
        var dialog = self.createDialog({
                name: name,
                width: dialogWidth,
                height: dialogHeight,
                title: self.lang(name),
                body: html,
                yesBtn: {
                    name: self.lang('yes'),
                    click: function (e) {
                        // Bugfix: http://code.google.com/p/kindeditor/issues/detail?id=319
                        if (dialog.isLoading) {
                            return;
                        }
                        var goods_id = goodsIDBox.val();
                        var pic_url = picUrlBox.val();
                        var isChecked = isCheckedBox.val();
                        if (goods_id == '') {
                            alert(self.lang('Empty_Goods_id'));
                            return;
                        }
                        if (!/^\d*$/.test(goods_id)) {
                            alert(self.lang('Goods_Error'));
                            return;
                        }
                        if (isChecked == 0) {
                            alert(self.lang('No_Checked'));
                            return;
                        }
                        self.insertHtml('<a href="ushengsheng.xjb://splash?m=shop&a=detail&id=' + goods_id + '"><img src="' + pic_url + '"/></a>');
                        clickFn.call(self, pic_url);
                    }
                },
                beforeRemove: function () {
                    checkBtn.unbind();
                }
            }),
            div = dialog.div;
        var goodsIDBox = K('[name="goods_id"]', div),
            checkBtn = K('#check_btn', div),
            imgBox = K('#img', div),
            picUrlBox = K('[name="pic_url"]', div),
            isCheckedBox = K('[name="isChecked"]', div);
        checkBtn.click(function (e) {
            var goods_id = goodsIDBox.val();
            checkBtn.val(self.lang('Doing_check')).attr('disabled', 'disabled');
            if (goods_id == '' || !/^\d*$/.test(goods_id)) {
                alert(self.lang('Invalid_Goods_ID'));
                checkBtn.val(self.lang('isCheck')).removeAttr('disabled');
                return;
            }
            K.ajax('/admin.php/goods/__ajax_getGoodsDetail/', function (data) {
                if (data == null) {
                    checkBtn.val(self.lang('isCheck')).removeAttr('disabled');
                    alert(self.lang('Invalid_Goods_ID'));
                    return;
                }
                imgBox.html(data.product_name);
                picUrlBox.val(data.pic_url);
                isCheckedBox.val(1);
                checkBtn.val(self.lang('isCheck')).removeAttr('disabled');
            }, 'POST', {
                id: goods_id
            });
        });
    };

    self.plugin.goods = {
        edit: function () {
            self.plugin.goodsDialog({
                clickFn: function (pic_url) {
                    // Bugfix: [Firefox] 上传图片后，总是出现正在加载的样式，需要延迟执行hideDialog
                    setTimeout(function () {
                        self.hideDialog();
                        if (pic_url == '')
                            alert("插入完成！");
                    }, 0);
                }
            });
        }
    };
    // 点击图标时执行
    self.clickToolbar(name, self.plugin.goods.edit);
});