<?php
/**
 * SOA客户端调用
 * Date: 2015-01-22
 * Time: 09:43
 */

namespace Admin\Org;

class SoaClient
{
    private static $clientList = array();

    /**
     * 获取相关yar客户端
     *
     * @param $server       string      要调用的SOA服务端名称
     * @param $service      string      要调用的SOA服务
     *
     * @return object      相关yar客户端
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
    public static function getSoa($server, $service)
    {
        if (self::$clientList[$server][$service] != null) {
            return self::$clientList[$server][$service];
        }

        //soa访问的地址
        $baseUrl = '';

        $soa_client_list = C('soa_client');
        $config          = $soa_client_list[$server];

        //如果有多个soa节点则进行随机负载
        if (is_string($config)) {
            $baseUrl = $config;
        } elseif (is_array($config)) {
            $len     = count($config);
            $rand    = rand(0, $len - 1);
            $baseUrl = $config[$rand];
        }

        //选择协议
        if (C('soa_protocol.' . $server) == 'json') {
            self::$clientList[$server][$service] = new JsonProtocol($baseUrl, $service);
        } else {
            self::$clientList[$server][$service] = new Yar($baseUrl, $service);
        }

        return self::$clientList[$server][$service];
    }
}

interface SoaService
{
    public function hasError();

    public function flushError();

    public function getErrorMsg();

    public function getErrorCode();

    public function getError();
}

class Yar implements SoaService
{
    /**
     * @var object yar对象
     */
    private $yarClient;

    /**
     * @var string 服务名称
     */
    private $service;

    /**
     * @var string url地址
     */
    private $url;

    /**
     * 构造函数
     *
     * @param $baseUrl string 基本的url
     * @param $service string 调用的服务
     */
    public function __construct($baseUrl, $service)
    {
        $this->service = $service;

        $this->url = $baseUrl . 'yarService';

        $distinctRequestId = substr(md5(APP_NAME . MODULE_NAME . ACTION_NAME . time()), 0,
                16) . rand(0, 99999999);
        $this->yarClient   = new \Yar_Client($this->url . '?distinctRequestId=' . $distinctRequestId);

        $this->yarClient->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 2000);
    }

    /**
     * 魔术方法，用于远程方法调用
     *
     * @param $method string 调用的方法名
     * @param $params array  调用的参数
     *
     * @return mixed 远程方法返回的值
     */
    public function __call($method, $params)
    {
        $result = '';

        for ($i = 1; $i <= 3; $i++) {
            $isException = false;

            try {
                $result = $this->yarClient->xySoaMethod($this->service, $method, $params);
            } catch (Exception $e) {
                if ($i < 3) {
                    Log::write('retry soa ' . $i . ' times # ' . $this->service . ' # ' . $method . ' # ' . $this->url,
                        Log::NOTICE);
                } else {
                    Log::write('soa exception ' . $e->getMessage() . ' # ' . $this->service . ' # ' . $method . ' # ' . ' ,url: ' . $this->url,
                        Log::ERR);
                }

                $isException = true;
            }

            if (!$isException) {
                break;
            }
        }

        $this->error = null;

        //假如出现错误则进行内部记录
        if (is_array($result)) {
            if (!empty($result['code'])) {
                $this->error = array(
                    'code' => $result['code'],
                    'msg'  => $result['msg'],
                );
            }
        }

        return $result;
    }

    public function hasError()
    {
        return !empty($this->error);
    }

    public function flushError()
    {
        $this->error = null;
    }

    public function getErrorMsg()
    {
        return $this->error['msg'];
    }

    public function getErrorCode()
    {
        return $this->error['code'];
    }

    public function getError()
    {
        return $this->error;
    }

}

class JsonProtocol implements SoaService
{
    /**
     * @var string url地址
     */
    private $url;

    /**
     * @var array  错误信息
     */
    private $error;

    /**
     * 构造函数
     *
     * @param $baseUrl string 基本的url
     * @param $service string 调用的服务
     */
    public function __construct($baseUrl, $service)
    {
        $distinctRequestId = substr(md5(APP_NAME . MODULE_NAME . ACTION_NAME . time()), 0,
                16) . rand(0, 99999999);

        $this->url = $baseUrl . 'service' . '?service=' . $service . '&distinctRequestId=' . $distinctRequestId . '&soa_basic=' . base64_encode(serialize($_REQUEST));
    }

    /**
     * 魔术方法，用于远程方法调用
     *
     * @param $method string 调用的方法名
     * @param $params array  调用的参数
     *
     * @return mixed 远程方法返回的值
     */
    public function __call($method, $params)
    {
        $result = '';

        for ($i = 1; $i <= 3; $i++) {
            $isException = false;

            try {
                $result = $this->request_post($this->url . '&method=' . $method,
                    'form=' . json_encode($params));
            } catch (Exception $e) {
                if ($i < 3) {
                    LOG::n('retry soa-json ' . $i . ' times # ' . ' # ' . $method . ' # ' . $this->url);
                } else {
                    LOG::e('soa-json exception ' . $e->getMessage() . ' # ' . $method . ' # ' . ' ,url: ' . $this->url);
                }

                $isException = true;
            }

            if (!$isException) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param string $url   url地址
     * @param string $param 要提交的表单内容
     *
     * @return bool|mixed
     */
    private function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl  = $url;
        $curlPost = $param;
        $ch       = curl_init();

        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);

        $data = curl_exec($ch);
        curl_close($ch);

        list($header, $body) = explode("\r\n\r\n", $data, 2);

        $data = json_decode($body, true);

        return $data;
    }

    public function hasError()
    {
        return !empty($this->error);
    }

    public function flushError()
    {
        $this->error = null;
    }

    public function getErrorMsg()
    {
        return $this->error['msg'];
    }

    public function getErrorCode()
    {
        return $this->error['code'];
    }

    public function getError()
    {
        return $this->error;
    }
}

class YarIpNotAllow
{
    public function xySoaMethod()
    {
        return array(
            'code' => 1012,
            'msg'  => 'ip is no allow!'
        );
    }
}