<?php

namespace Admin\Org;

class Qiniu
{

    protected $_useragent = 'taqu mall ms';
    protected $_url;
    protected $_followlocation;
    protected $_timeout;
    protected $_maxRedirects;
    protected $_cookieFileLocation = './cookie.txt';
    protected $_post;
    protected $_postFields;
    protected $_referer = "taqu_mall_ms";
    protected $_session;
    protected $_webpage;
    protected $_includeHeader;
    protected $_noBody;
    protected $_status;
    protected $_binaryTransfer;
    protected $proxy;
    public $authentication = 0;
    public $auth_name = '';
    public $auth_pass = '';

    public function useAuth($use)
    {
        $this->authentication = 0;
        if ($use == true) {
            $this->authentication = 1;
        }
    }

    public function setName($name)
    {
        $this->auth_name = $name;
    }

    public function setPass($pass)
    {
        $this->auth_pass = $pass;
    }

    public function __construct($url, $followlocation = true, $timeOut = 30, $maxRedirecs = 4, $binaryTransfer = false, $includeHeader = false, $noBody = false)
    {
        $this->_url            = $url;
        $this->_followlocation = $followlocation;
        $this->_timeout        = $timeOut;
        $this->_maxRedirects   = $maxRedirecs;
        $this->_noBody         = $noBody;
        $this->_includeHeader  = $includeHeader;
        $this->_binaryTransfer = $binaryTransfer;

        $this->_cookieFileLocation = dirname(__FILE__) . '/cookie.txt';
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function setReferer($referer)
    {
        $this->_referer = $referer;
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    public function setPost($postFields)
    {
        $this->_post       = true;
        $this->_postFields = $postFields;
    }

    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
    }

    public function createCurl($url = 'nul')
    {
        if ($url != 'nul') {
            $this->_url = $url;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $this->_maxRedirects);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->_followlocation);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);

        if ($this->authentication == 1) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->auth_name . ':' . $this->auth_pass);
        }

        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }

        if ($this->_post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postFields);
        }

        if ($this->_includeHeader) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }

        if ($this->_noBody) {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        curl_setopt($ch, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ch, CURLOPT_REFERER, $this->_referer);

        $this->_webpage = curl_exec($ch);
        $this->_status  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

    public function getHttpStatus()
    {
        return $this->_status;
    }

    public function __tostring()
    {
        $result = json_decode($this->_webpage, true);

        if ($result['status'] == 'success') {
            return json_encode(array(
                'error'  => 0,
                'url'    => $result['data']['cloud_url'],
                'title'  => '',
                'width'  => $result['data']['width'],
                'height' => $result['data']['height'],
            ));
        } else {
            return json_encode(array(
                'error'   => 1,
                'message' => $result['msg']
            ));
        }
    }

}