<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $apps = controller('api/MyApps')->index(0,6);
        $this->assign('apps',$apps);
        return $this->fetch();
    }
}
