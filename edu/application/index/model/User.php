<?php

namespace app\index\Model;

use think\Model;
use traits\Model\SoftDelete;

class User extends Model{

    use SoftDelete;
    protected $deleteTime = 'delete_time';

    // 保存自动完成列表
    protected $auto = [
        'is_delete' => 1, // 是否可删除
        'delete_time' => NULL,
    ];
    // 新增自动完成列表
    protected $insert = [
        'login_count' => 0,
        'login_time' => NULL,
    ];
    // 更新自动完成列表
    protected $update = [];
    // 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
    protected $autoWriteTimestamp=true;
    // 创建时间字段
    protected $createTime = 'create_time';
    // 更新时间字段
    protected $updateTime = 'update_time';
    // 时间字段取出后的默认时间格式
    protected $dateFormat='Y年m月d日';

    public function  getRoleAttr($value)
    {
        $role = [
            0 => "管理员",
            1 => "超级管理员",
        ];
        return $role[$value];
    }

    public function getStatusAttr($value)
    {
        $status = [
            0=>'已禁用',
            1=> '已启用'
        ];
        return $status[$value];
    }

    public function setPasswordAttr($value)
    {
        return md5($value);
    }

    public function getLoginTimeAttr($value)
    {
        return date('Y年m月d日 h:i',$value);
    }
}
