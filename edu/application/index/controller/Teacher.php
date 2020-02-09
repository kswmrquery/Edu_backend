<?php

namespace app\index\controller;

use app\index\controller\Base;
use think\db\Query;
use think\Session;
use think\Request;
use app\index\Model\Grade as GradeModel;
use app\index\model\Teacher as TeacherModel;

class Teacher Extends Base
{
    public function test(){
        return $this->view->fetch();
    }
    //教师列表
    public function teacherList()
    {
        $this ->view->assign('title',"教师列表");
        $teacher_list = TeacherModel::all(function($query){
            $query->order('id');
        });
        foreach($teacher_list as $value){
            $data = [
                'id'=> $value->id,
                'name'=> $value->name,
                'degree'=> $value->degree,
                'school'=> $value->school,
                'mobile'=> $value->mobile,
                'status'=> $value->status,
                'hiredate'=> $value->hiredate,
                'grade'=>isset($value->grade->name)?$value->grade->name:'<span style="color:red;">未分配</span>',
            ];
            $list[]=$data;
        }
        $this -> view ->assign('list', $list);
        $this ->view->assign('count',TeacherModel::count());
        return $this -> view ->fetch();
    }
    //添加教师页面
    public function teacherAdd()
    {
        $grade_list = GradeModel::all();
        $teacher_list = TeacherModel::all(function ($query){
            $query->where('grade_id','neq',0);
        });
        foreach ($grade_list as $item){
            $f = 1;
            foreach ($teacher_list as $value){
                if($item->id  == $value->grade->id){
                    $f = 0;
                    break;
                }
            }
            if($f == 1){
                $fen_list[]=$item;
            }
        }
        $this -> view ->assign('grade_list', $fen_list);
        return $this->view->fetch();
    }
    //教师名称重复验证（添加教师页面）
    public function checkTeacherName(Request $request)
    {
        $status = 1;
        $msg = '';
        $teacher_name = trim($request->param('name'));
        if(TeacherModel::get(['name' => $teacher_name])){
            $status = 0;
            $msg = "该班已存在";
        }
        return ['status' => $status, 'message' => $msg];
    }
    //教师添加功能
    public function addTeacher(Request $request)
    {
        $teacher = $request -> param();
        $status = 0;
        $msg ='';
        $rule =[
            'name|姓名' => 'require',
            'school|毕业学校' => 'require',
            'mobile|手机号' => 'require',
        ];
        $result = $this -> validate($teacher, $rule);
        if($result === true) {
            $rlt = TeacherModel::create($teacher);
            if($rlt == NULL){
                $msg = "添加失败！";
            }
            else{
                $status = 1;
                $msg = "添加成功！";
            }
        }
        else{
            $msg= $result;
        }
        return ['status'=>1, 'message'=>$msg];
    }
    //批量恢复
    public function unDelete()
    {
        TeacherModel::update(['delete_time'=>NULL],['is_delete'=>1]);
    }
    //教师编辑页面
    public function teacherEdit(Request $request)
    {
        $teacher_id = $request->param('id');
        $list = TeacherModel::get($teacher_id);
        $this->view->assign('list', $list);
        $grade = $list->grade;
        $this->view->assign('grade', $grade);

        //找到未分配的班级 --
        $grade_list = GradeModel::all();
        $teacher_list = TeacherModel::all(function ($query){
            $query->where('grade_id','neq',0);
        });
        foreach ($grade_list as $item){
            $f = 1;
            foreach ($teacher_list as $value){
                if($item->id  == $value->grade->id){
                    $f = 0;
                    break;
                }
            }
            if($f == 1){
                $fen_list[]=$item;
            }
        }
        $this->view->assign('fen_list', $fen_list);
        //-- 找到分配的班级的id

        return $this->view->fetch();
    }
    //教师编辑功能
    public function editTeacher(Request $request)
    {
        $status = 0;
        $msg = '';
        $data = $request -> param();
        $rlt = TeacherModel::update($data,['id' => $data['id']]);
        if ($rlt == NULL){
            $msg = "更行失败！";
        }
        else{
            $status = 1;
            $msg = "更新成功！";
        }
        return ['status'=>$status, 'message'=>$msg];
    }
    //教师删除功能
    public function deleteTeacher(Request $request)
    {
      $teacher_id = $request -> param('id');
      TeacherModel::update(['is_delete' => 1],['id'=>$teacher_id]);
      TeacherModel::destroy($teacher_id);
    }
    //状态设置
    public function setStatus(Request $request)
    {
        $teacher_id = $request->param('id');
        if(TeacherModel::get($teacher_id)->getData('status') == 1){
            TeacherModel::update(['status'=> 0],['id'=>$teacher_id]);
        }
        else{
            TeacherModel::update(['status'=> 1],['id'=>$teacher_id]);
        }
    }
}
