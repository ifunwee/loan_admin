<?php

// 引入SOA库
use Admin\Org\SoaClient;
use Think\Cache;

/**
 * 获取相关yar客户端
 *
 * @param $server       string      要调用的SOA服务端名称
 * @param $method       string      要调用的SOA服务
 *
 * @return Yar_Client   object      相关yar客户端
 *
 * soa_config配置需要增加相关地址
 *   'soa_client' => array(
 *        'tqco' => 'http://10.10.50.205:1009/tqco/v1/Soa/"
 *   )
 *
 *  调用方法为
 *  $soa    = SoaClient::getSoa('bbs', 'postInfo');
 *  $result = $soa->getAndSet($appcode, $platform, $token);
 *
 */
function getSoa($server, $method)
{
    return SoaClient::getSoa($server, $method);
}

function node_merge($node, $access = null, $pid = 0)
{
    $arr = array();
    foreach ($node as $v) {
        if (is_array($access)) {
            $v['access'] = in_array($v['id'], $access) ? 1 : 0;
        }
        if ($v['pid'] == $pid) {
            $v['child'] = node_merge($node, $access, $v['id']);
            $arr[]      = $v;
        }
    }

    return $arr;
}

function check_verify($code, $id = '')
{
    $verify = new \Think\Verify();

    return $verify->check($code, $id);
}

/**
 * 获取七牛缩略图
 *
 * @param        $url
 * @param string $width
 *
 * @return string
 */
function get_thumb_image($url, $width = '80')
{
    if ($url) {
        $result = $url . '?imageView/2/w/' . $width;

        return $result;
    }
}

/**
 * @param string $url   url地址
 * @param string $param 要提交的表单内容
 *
 * @return bool|mixed
 */
function request_curl($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl  = $url;
    $curlPost = $param;
    $ch       = curl_init();
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_REFERER, md5('pc.admin.cn'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curlPost));

    $data = curl_exec($ch);
    if (curl_error($ch)) {
        $data = 'Curl error: ' . curl_error($ch);
    }

    curl_close($ch);

    return $data;
}

/**
 * 获取redis缓存实例
 *
 * @return mixed
 */
function getRedis($redis_config = 'common_redis')
{
    $cache = Cache::getInstance('Redis', C($redis_config));

    return $cache;
}

/**
 * 判断url地址是否带有http
 *
 */
function checkUrl($url)
{
    if (empty($url)) {
        return '';
    }

    if (strpos($url, 'http://') !== false) {
        return $url;
    } else {
        $new_url = "http://" . $url;

        return $new_url;
    }
}

/**
 * 读取文件
 *
 * @access public
 *
 * @param string $file
 *
 * @return string
 */
function read_file($file)
{
    if (!file_exists($file)) {
        return false;
    }

    if (function_exists('file_get_contents')) {
        return file_get_contents($file);
    }

    if (!$fp = @fopen($file, 'rb')) {
        return false;
    }

    flock($fp, LOCK_SH);

    $data = '';
    if (filesize($file) > 0) {
        $data =& fread($fp, filesize($file));
    }

    flock($fp, LOCK_UN);
    fclose($fp);

    return $data;
}

/**
 * 写入文件
 *
 * @access public
 *
 * @param string $path
 * @param string $data
 * @param string $mode
 *
 * @return boolean
 */
function write_file($path, $data, $mode = "wb")
{
    if (!$fp = @fopen($path, $mode)) {
        return false;
    }

    flock($fp, LOCK_EX);
    fwrite($fp, $data);
    flock($fp, LOCK_UN);
    fclose($fp);

    return true;
}


