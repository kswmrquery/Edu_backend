<?php

namespace app\index\controller;

use app\index\controller\Base;
use think\db\Query;
use think\Request;
use think\Session;
use app\index\model\Grade as GradeModel;

Class Grade Extends Base
{
    //班级列表
    public function gradeList()
    {
        $this -> view ->assign('title', "班级管理列表");
        $grade_list = GradeModel::all(function($query){
           $query -> order('id');
        });
        foreach($grade_list as $value){
            $data=[
                'id' => $value->id,
                'name' => $value->name,
                'length' => $value->length,
                'price' => $value->price,
                'create_time' => $value->create_time,
                'status' => $value->status,
                'teacher' => isset($value->teacher->name)? ($value->teacher->name) : '<span style="color:red;">未分配</span>',
            ];
            $list[]=$data;
        }
        $this -> view ->assign('list', $list);
        $this -> view ->assign('count',GradeModel::count());
        return $this -> view -> fetch();
    }
    //添加班级页面
    public function gradeAdd()
    {
        return $this -> view -> fetch();
    }
    //班级名称重复验证（添加班级页面）
    public function checkGradeName(Request $request)
    {
        $status = 1;
        $msg = '';
        $grade_name = trim($request->param('name'));
        if(GradeModel::get(['name' => $grade_name])){
            $status = 0;
            $msg = "该班已存在";
        }
        return ['status' => $status, 'message' => $msg];
    }
    public function addGrade(Request $request)
    {
        $grade = $request -> param();
        $status = 0;
        $msg ='';
        $rule =[
          'name|班级名' => 'require',
          'length|学制' => 'require',
          'price|学费' => 'require|number',
        ];
        $result = $this -> validate($grade, $rule);
        if($result === true) {
            $rlt = GradeModel::create($grade);
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
        GradeModel::update(['delete_time' => NULL],['is_delete' => 1]);
    }
    //班级编辑页面
    public function gradeEdit(Request $request)
    {
        $grade_id = $request -> param('id');
        $list = GradeModel::get($grade_id) -> getData();
        $this -> view ->assign('list', $list);
        return $this-> view ->fetch();
    }
    //班级编辑功能
    public function editGrade(Request $request)
    {
        $status = 0;
        $msg = '';
        $data = $request -> param();
        $rlt = GradeModel::update($data,['id' => $data['id']]);
        if ($rlt == NULL){
            $msg = "更行失败！";
        }
        else{
            $status = 1;
            $msg = "更新成功！";
        }
        return ['status'=>$status, 'message'=>$msg];
    }
    //班级删除功能
    public function deleteGrade(Request $request)
    {
       $grade_id = $request->param('id');
       GradeModel::update(['is_delete' => 1],['id' => $grade_id]);
       GradeModel::destroy($grade_id);
    }
    //状态设置
    public function setStatus(Request $request)
    {
        $grade_id = $request->param('id');
        $user = GradeModel::get($grade_id);
        if($user -> getData('status') == 1){
            GradeModel::update(['status' => 0],['id' => $grade_id]);
        }
        else{
            GradeModel::update(['status' => 1],['id' => $grade_id]);
        }
    }
}