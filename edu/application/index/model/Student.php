<?php

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Student Extends Model
{
    use SoftDelete;
    protected $deleteTime = "delete_time";

    protected $auto= [
        'is_delete' => 1,
        'delete_time' => NULL,
    ];
    protected $insert=[];
    protected $update=[];
    // 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
    protected $autoWriteTimestamp=true;
    // 创建时间字段
    protected $createTime = 'create_time';
    // 更新时间字段
    protected $updateTime = 'update_time';
    // 时间字段取出后的默认时间格式
    protected $dateFormat='Y/m/d';
    // 字段类型或者格式转换
    protected $type = [
        'start_time' => 'timestamp',
    ];

    public function grade()
    {
        return $this->belongsTo('Grade');
    }

    public function getStatusAttr($value)
    {
        $status =[
            0 => '已禁用',
            1 => '已启用',
        ];
        return $status[$value];
    }

    public function getSexAttr($value)
    {
        $sex =[
            0 => '女',
            1 => '男',
        ];
        return $sex[$value];
    }
}