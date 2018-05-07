<?php
namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper{
    // 缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';
    // 将访问时间写入redis 当中。
    public function recordLastActivedAt(){
        // Redis 哈希表的命名，如: larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        // 字段名称，如user_1
        $field = $this->getHashField();
        // 当前时间，如：2018-05-07 10:55:20
        $now = Carbon::now()->toDateTimeString();
        // 数据写入redis ,字段已存在会被更新
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        // dd($redis->hGetAll($hash));
        $redis->hSet($hash,$field,$now);
    }

    // 将redis 中的数据同步到数据库中
    public function syncUserActivedAt(){
        // $yesterday_date = Carbon::now()->toDateString();
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21;
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());
        // 从Redis 中获取所有哈希表里的数据
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $dates = $redis->hGetAll($hash);
        // 遍历，并同步到数据库中
        foreach($dates as $user_id => $actived_at){
            // 会将 `user_1` 转换为 1
            $user_id = str_replace($this->field_prefix,'',$user_id);
            // 只有当用户存在时才更新到数据库中
            if($user = $this->find($user_id)){
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }
        // 以数据库为中心的存储，既已同步，即可删除
        $redis->del($hash);
    }

    // 用户获取访问时间
    public function getLastActivedAtAttribute($value){
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        // 字段名称，如：user_1
        $field = $this->getHashField();
        // 三元运算符，有限选择Redis 中的数据，否则使用数据库中的数据
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $datetime = $redis->hGet($hash,$field) ? : $value;
        // 如果存在的话，返回对应的Carbon 实体
        if($datetime){
            return new Carbon($datetime);
        }else{
            // 否则使用用户注册时间
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date){
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix.$date;
    }

    public function getHashField(){
        // 字段名称，如：user_1
        return $this->field_prefix.$this->id;
    }

}