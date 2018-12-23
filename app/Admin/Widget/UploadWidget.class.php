<?php

namespace Admin\Widget;

use Think\Controller;

class UploadWidget extends Controller
{
    /**
     * 上传图片
     *
     * @param $name        图片在表单中名称
     * @param $id          图片在表单中的ID
     * @param $image       图片值
     * @param $dir         图片保存路径
     * @param $bucket      图片空间别名
     * @param $show_width  图片显示宽度
     *
     * by funwee
     */
    function image($name, $id, $image, $dir = 'upload', $show_width = '200', $bucket = 'image')
    {
        $image_domain = C('image_domain');
        $this->assign('name', $name);
        $this->assign('id', $id);
        $this->assign('image', $image);
        $this->assign('dir', $dir);
        $this->assign('bucket', $bucket);
        $this->assign('show_width', $show_width);
        $this->assign('image_domain', $image_domain);

        $this->display('Widget:upload_image');
    }
}