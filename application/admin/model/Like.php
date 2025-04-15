<?php

namespace app\admin\model;

use think\Model;


class Like extends Model
{

    

    

    // 表名
    protected $name = 'like';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function caipu()
    {
        return $this->belongsTo('Caipu', 'caipu_id', 'content', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'nickname', [], 'LEFT')->setEagerlyType(0);
    }
}
