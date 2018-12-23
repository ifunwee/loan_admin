<?php

namespace Admin\Controller;

use Common\Controller\CommonController;
use Admin\Org\Qiniu;
use Admin\Org\SoaClient;

class UploadController extends CommonController
{
    public function index()
    {
        $this->assign('title', '文件上传');
        $this->display();
    }

    public function get_user_id()
    {
        vendor('phpRPC.phprpc_client');
        $a          = $_POST['type'];
        $subname    = mb_detect_encoding($a, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        $newsubname = mb_convert_encoding($a, 'GBK', $subname);
        mkdir('./Public/Uploads/' . $newsubname, 0777, true);
        chmod('./Public/Uploads/' . $newsubname, 0777, true);

        return $newsubname;
    }

    /**
     * @api upload-上传文件
     */
    public function upload()
    {
        $upload           = new \Think\Upload();
        $upload->maxSize  = 1048576;
        $upload->exts     = array();
        $upload->rootPath = './Public/Uploads/';
        $upload->savePath = '';
        $upload->autoSub  = true;
        $upload->subName  = $this->get_user_id();
        $info             = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->ajaxReturn($upload->getError());
        } else {// 上传成功
            foreach ($info as $file) {
                echo $file['savapath'] . $file['name'];
            }
            $this->ajaxReturn('success');
        }
    }

    /**
     * @api demo-文件上传
     */
    public function demo()
    {
        $this->assign('title', '文件上传');
        $this->display();
    }

    /**
     * 上传图片到七牛
     */
    public function upload_img_to_cloud()
    {
        $file_input = trim($_REQUEST['file']);
        $file_input = empty($file_input) ? 'imgFile' : $file_input;
        $bucket     = trim($_REQUEST['bucket']);
        $directory  = trim($_REQUEST['dir']);

        $file_extension = substr($_FILES[$file_input]['name'],
            strrpos($_FILES[$file_input]['name'], '.') + 1);
        $qiniu_model    = new Qiniu(C('IMAGE_SYSTEM'));
        $postFields     = array(
            'bucket'         => $bucket,
            'save_directory' => $directory,
            'imgFile'        => base64_encode(file_get_contents($_FILES[$file_input]['tmp_name'])),
            'ext_type'       => $file_extension,
        );

        $qiniu_model->setPost($postFields);
        $qiniu_model->createCurl();

        echo $qiniu_model->__tostring();
        exit;
    }

    public function uploadFile()
    {
        $file_info = pathinfo($_FILES['imgFile']['name']);
        $name      = time() . rand(1000, 9999) . '.' . $file_info['extension'];

        $data = base64_encode(file_get_contents($_FILES['imgFile']['tmp_name']));
        $soa  = SoaClient::getSoa('psn', 'Qiniu');
        $url  = $soa->uploadFile($data, $name, 'loan-market');
        if ($soa->hasError()) {
            return $this->getJsonData(1, $soa->getErrorMsg());
        }

        return $this->getJsonData(0, '上传成功', $url);
    }

    public function getJsonData($error_code, $title, $url = '')
    {
        //        $return = array(
        //            'error' => $error_code,
        //            'message' => $title
        //        );
        //        if (!empty($url)) {
        //            $return['url'] = $url;
        //        }
        //        return json_encode($return);

        if (empty($error_code)) {
            echo json_encode(array(
                'error'  => $error_code,
                'url'    => $url,
                'title'  => $title,
                'width'  => '',
                'height' => '',
            ));
        } else {
            echo json_encode(array(
                'error'   => $error_code,
                'message' => $title
            ));
        }
    }
}