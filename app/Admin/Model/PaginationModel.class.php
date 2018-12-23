<?php

namespace Admin\Model;

use Think\Think;

class PaginationModel
{
    private $host = '';
    private $uri = '';
    private $queryParams = array();

    private $total = 0;

    private $currentPage = 1;
    private $pageSize = 10;
    private $totalPages = 0;

    private $currentRow = 1;
    private $rowSize = 5;
    private $totalRows = 0;

    private $pageParamName = 'p';

    private $links = array(
        'firstPage'    => '',
        'lastPage'     => '',
        'previousPage' => '',
        'nextPage'     => '',
        'previousRow'  => '',
        'nextRow'      => '',
        'numberLinks'  => array()
    );

    public function config(
        $total, $pageSize = 10, $rowSize = 5, $pageParamName = 'p', $autoGetCurrentPage = true, $autoGetHost = true, $autoGetUri = true, $autoGetQueryParams = true
    ) {
        $this->total    = intval($total);
        $this->pageSize = intval($pageSize) < 1 ? 1 : intval($pageSize);
        $this->rowSize  = intval($rowSize) < 2 ? 2 : intval($rowSize);

        $this->totalPages = ceil($this->total / $this->pageSize);
        $this->totalRows  = ceil($this->totalPages / $this->rowSize);

        $this->pageParamName = trim($pageParamName);

        if ($autoGetCurrentPage && isset($_GET[$this->pageParamName])) {
            $this->setCurrentPage(intval($_GET[$this->pageParamName]));
        }

        if ($autoGetHost) {
            $this->host = 'http://' . $_SERVER['HTTP_HOST'];
        }

        if ($autoGetUri) {
            $parseReuslt = parse_url($_SERVER['REQUEST_URI']);
            $this->uri   = $parseReuslt['path'];
        }

        if ($autoGetQueryParams) {
            $queryParams = $_GET;
            if (isset($queryParams[$this->pageParamName])) {
                unset($queryParams[$this->pageParamName]);
            }
            $this->queryParams = $queryParams;
        }
    }

    public function setCurrentPage($page)
    {
        $page = intval($page);
        $page = $page < 1 ? 1 : $page;
        $page = $page > $this->totalPages ? $this->totalPages : $page;

        $this->currentPage = $page;
        $this->currentRow  = ceil($this->currentPage / $this->rowSize);
    }

    public function addQueryParams($params)
    {
        if (is_string($params)) {
            parse_str($params, $paramsArray);
        } elseif (is_array($params)) {
            $paramsArray = $params;
        } else {
            $paramsArray = array();
        }

        $this->queryParams = array_merge($this->queryParams, $paramsArray);
    }

    private function buildHrefByPage($page)
    {
        if ($page > 0 && $page <= $this->totalPages) {
            $this->queryParams[$this->pageParamName] = $page;
            $href                                    = sprintf('%s/%s?%s', rtrim($this->host, '/'),
                trim($this->uri, '/'), http_build_query($this->queryParams));
        } else {
            $href = '';
        }

        return $href;
    }

    public function buildLinks()
    {
        if ($this->currentRow == 1) {
            $this->links['firstPage'] = '';
        } else {
            $this->links['firstPage'] = $this->buildHrefByPage(1);
        }

        if ($this->currentRow == $this->totalRows) {
            $this->links['lastPage'] = '';
        } else {
            $this->links['lastPage'] = $this->buildHrefByPage($this->totalPages);
        }

        $this->links['previousPage'] = $this->buildHrefByPage($this->currentPage - 1);
        $this->links['nextPage']     = $this->buildHrefByPage($this->currentPage + 1);

        $this->links['previousRow'] = $this->buildHrefByPage($this->currentPage - $this->rowSize);
        $this->links['nextRow']     = $this->buildHrefByPage($this->currentPage + $this->rowSize);

        $this->links['numberLinks'] = array();
        for ($i = 1; $i <= $this->rowSize; $i++) {
            $page                              = ($this->currentRow - 1) * $this->rowSize + $i;
            $this->links['numberLinks'][$page] = $this->buildHrefByPage($page);
        }
        $this->links['numberLinks'] = array_filter($this->links['numberLinks']);

        return $this->links;
    }

    public function asArray()
    {
        return array(
            'total'       => $this->total,
            'currentPage' => $this->currentPage,
            'pageSize'    => $this->pageSize,
            'totalPages'  => $this->totalPages,
            'rowSize'     => $this->rowSize,
            'totalRows'   => $this->totalRows,
            'links'       => $this->buildLinks(),
        );
    }

    public function asHtml()
    {
        $restore = C('LAYOUT_ON');
        C('LAYOUT_ON', false);

        $view = Think::instance('Think\View');
        $view->assign($this->asArray());
        $html = $view->fetch('Public:pagination');

        C('LAYOUT_ON', $restore);

        return self::minifyHtml($html);
    }

    protected static function minifyHtml($html)
    {
        $search  = array(
            '/\>(\s)+/s', // strip whitespaces after tags
            '/(\s)+\</s', // strip whitespaces before tags
            '/(\s)+/s'    // shorten multiple whitespace sequences
        );
        $replace = array(
            '>',
            '<',
            '\\1'
        );

        return preg_replace($search, $replace, $html);
    }
}
