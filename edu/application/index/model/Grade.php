<?php

namespace app\index\Model;

use think\Model;
use traits\model\SoftDelete;

class Grade extends Model
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
    protected $dateFormat='Y年m月d日';

    public function getStatusAttr($value)
    {
        $result = [
            1 => "已启用",
            0 =>  "已禁用",
            ];
        return $result[$value];
    }

    public function teacher()
    {
        return $this -> hasOne('Teacher');
    }

    public function student()
    {
        return $this -> hasMany('Student');
    }
}

