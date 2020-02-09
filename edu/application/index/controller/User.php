<?php
namespace app\index\controller;

use app\index\controller\Base;
use think\db\Query;
use think\Request;
use app\index\Model\User as UserModel;
use think\Session;

class User Extends Base
{
    //    $this->validate($data, $rule, $mes); 验证
    //登录
    public function login()
    {
        $this -> aleadyLogin();
        return $this -> view -> fetch();
    }
    //登录验证
    public function checkLogin(Request $request)
    {
        $status=0;
        $data=$request -> param();

        $rule = [
            'name|用户名' => 'require',
            'password|密码' => 'require',
            'verify|验证码' => 'require|captcha',
        ];

        $msg = [
            'name' => ['require' => '用户名不能为空， 请输入用户名'],
            'password' => ['require' => '密码不能为空，请输入密码'],
            'verify' =>[
                'require' => '验证码不能为空， 请输入验证码',
                'captcha' => '验证码错误， 请检查',
            ],
        ];
        $result = $this -> validate($data, $rule, $msg);

        if($result === true) {
            $map = [
                'name' => $data['name'],
                'password' => md5($data['password']),
            ];

            $user = UserModel::get($map);
            if($user == null){
                $result = "该用户不存在！";
            }
            else if($user -> getData('status') == 0){
                $result = "该用户已被禁用，不许登录！";
            }
            else{
                $status = 1;
                $result = "登录成功";
                UserModel::get($map) ->setInc('login_count');
                Session::set('user_id',$user->id);
                Session::set('user_info',$user->getData());

            }

        }


        return ['status'=>$status,'message'=>$result,'data'=>$data];
    }
    //退出管理员
    public function logout() {
        UserModel::update(["login_time" => time()],['id' => Session::get('user_id')]);
        Session::delete('user_id');
        Session::delete('user_info');
        $this -> success("注销成功 正在返回","user/login");
    }
    //管理员列表
    public function adminList() {
        $this->isLogin();
        $this -> view -> assign("title", "管理员列表");
        $user_role = Session::get("user_info.role");
        if($user_role == 1){
            $list= UserModel::all(function ($query){
                $query -> order('id');
            });
        }
        else{
            $list = UserModel::all(['name' => Session::get("user_info.name")]);
        }
        $this -> view -> assign("list", $list);
        $this -> view -> count = UserModel::count();
        return  $this -> view -> fetch();
    }
    //管理员编辑页面
    public function adminEdit(Request $request)
    {
        $user_id = $request ->param('id');
        $result = UserModel::get($user_id);
        $this -> assign('list', $result ->getData());
        return $this -> view ->fetch();
    }
    //管理员编辑功能
    public function editUser(Request $request)
    {
        $param = $request->param();
        $status = 1;
        $msg = '';
        foreach ($param as $key => $value){
            if (empty($value) && $key == 'password') continue;
            $data[$key] = $value;
        }
        $result = UserModel::update($data, ['id' => $data['id']]);
        if($result == NULL) {
            $status = 0;
            $msg = "修改失败， 请检查！";
        }
        else {
            $status = 1;
            $msg = "修改成功";
        }
        return ['status' => $status, 'message' => $msg];
    }
    //管理员停用和启用功能
    public function setStatus(Request $request)
    {
        $user_id = $request -> param('id');
        $vr = UserModel::get($user_id)->getData('status');
        if($vr == 1){
            UserModel::update(['status' => 0], ['id' => $user_id]);
        }
        else{
            UserModel::update(['status' => 1], ['id' => $user_id]);
        }

    }
    //管理员删除功能 （软删除）
    public function deleteUser(Request $request)
    {
        $user_id = $request->param('id');
        UserModel::update(['is_delete' => 1],['id' => $user_id]);
        UserModel::destroy($user_id);
    }
    //管理员批量恢复功能
    public function unDelete()
    {
        UserModel::update(['delete_time' => NULL],['is_delete' => 1]);
    }
    //添加管理员页面
    public function adminAdd()
    {
        return $this -> view -> fetch();
    }
    //管理员添加功能
    public function addUser(Request $request)
    {
        $data = $request -> param();

        $status = 0;
        $msg = '';
        $rule = [
            'name|管理员名' => 'require|min:3|max:20',
            'password|密码' => 'require|min:3|max:50',
            'email|邮箱' => 'require|email'
        ];

        $result = $this -> validate($data, $rule);

        if($result === true) {
            $user = UserModel::create($data);
            if($user == null){
                $msg = "添加失败";
            }
            else{
                $status = 1;
                $msg = "添加成功";
            }
        }
        else{
            $msg = $result;
        }

        return ['status' => $status, 'message' => $msg];
    }
    //管理员名验证功能（管理员添加页面）
    public function checkUserName(Request $request)
    {

        $user_name = trim($request-> param('name'));
        $status = 1;
        $msg = '';
        if(UserModel::get(['name' => $user_name])){
            $status = 0;
            $msg = "管理名重复，请重新输入！";
        }
        return ['status' => $status, 'message' => $msg];
    }
    //邮箱名验证功能（管理员添加页面）
    public function checkUserEmail(Request $request)
    {
        $user_email = trim($request -> param('email'));
        $status = 1;
        $msg = '';
        if (UserModel::get(['email' => $user_email])) {
            $status = 0;
            $msg = "邮箱重复，请重新输入！";
        }
        return ['status' => $status, 'message' => $msg];
    }
}
