<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        header("Location: ./Admin/Public/login.html");
    }
}