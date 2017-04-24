<?php

namespace app\index\controller;

use think\Controller;

class About extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}
