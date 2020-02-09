<?php
namespace app\index\controller;

use app\index\controller\Base;

class Index Extends Base
{
    public function index()
    {
        $this->isLogin();
        $this -> view ->assign('title','教学管理系统');
        return $this -> view -> fetch();
    }
}
