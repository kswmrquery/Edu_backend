<?php

namespace app\index\controller;

use app\index\controller\Base;
use app\index\Model\Grade as GradeModel;
use app\index\model\Teacher as TeacherModel;
use think\db\Query;
use think\Session;
use think\Request;
use app\index\model\Student as StudentModel;

class Student Extends Base
{

    public function StudentList()
    {
        $this ->view->assign('title',"学生列表");
        //需要解决静态方法 排序（分页）
        $student_M = new StudentModel();
        $list = $student_M->order('age')->paginate(5);
        foreach($list as $value){
            $value->grade = isset($value->grade->name)? $value->grade->name :'<span style="color:red;">未参加</span>';
        }
        $this ->view->assign('count',StudentModel::count());
        $this ->view->assign('list', $list);
        return $this -> view ->fetch();
    }
    //添加学生页面
    public function StudentAdd()
    {
        $grade_list = GradeModel::all();
        $this -> view ->assign('grade_list', $grade_list);
        return $this-> view ->fetch();
    }
    //教师学生功能
    public function addStudent(Request $request)
    {
        $student = $request -> param();
        $status = 0;
        $msg ='';
        $rule =[
            'name|姓名' => 'require',
            'age|年龄' => 'require',
            'mobile|手机号' => 'require',
            'email|邮箱' => 'require|email',
        ];
        $result = $this -> validate($student, $rule);
        if($result === true) {
            $rlt = StudentModel::create($student);
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
        StudentModel::update(['delete_time'=>NULL],['is_delete'=>1]);
    }
    //学生编辑页面
    public function StudentEdit(Request $request)
    {
        $student_id = $request->param('id');
        $list = StudentModel::get($student_id);
        $this->view->assign('list', $list);
        $grade = $list->grade;
        $this->view->assign('grade', $grade);

        $fen_list = GradeModel::all();
        $this->view->assign('fen_list', $fen_list);
        return $this->view->fetch();
    }
    //学生编辑功能
    public function editStudent(Request $request)
    {
        $status = 0;
        $msg = '';
        $data = $request -> param();
        $rlt = StudentModel::update($data,['id' => $data['id']]);
        if ($rlt == NULL){
            $msg = "更行失败！";
        }
        else{
            $status = 1;
            $msg = "更新成功！";
        }
        return ['status'=>$status, 'message'=>$msg];
    }
    //学生删除功能
    public function deleteStudent(Request $request)
    {
        $student_id = $request -> param('id');
        StudentModel::update(['is_delete' => 1],['id'=>$student_id]);
        StudentModel::destroy($student_id);
    }
    //状态设置
    public function setStatus(Request $request)
    {
        $student_id = $request->param('id');
        if(StudentModel::get($student_id)->getData('status') == 1){
            StudentModel::update(['status'=> 0],['id'=>$student_id]);
        }
        else{
            StudentModel::update(['status'=> 1],['id'=>$student_id]);
        }
    }
}
