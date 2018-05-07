<?php
    function route_class(){
        return str_replace('.','-',Route::currentRouteName());
    }

    function make_excerpt($value,$length=200){
        $excerpt = trim(preg_replace('/\r\n|\r|\n+/',' ',strip_tags($value)));
        return str_limit($excerpt,$length);
    }

    function pr($data){
        if(is_array($data)){
            echo '<br />';
            echo '<pre>';
            print_r($data);
            echo '</pre>';
            echo '<br />';
        }else{
            echo '<br />';
            echo $data;
            echo '<br />';
        }
    }

    function model_admin_link($title, $model){
        return model_link($title, $model, 'admin');
    }

    function model_link($title, $model, $prefix = ''){
        // 获取数据模型的复数蛇形命名
        $model_name = model_plural_name($model);
        // 初始化前缀
        $prefix = $prefix ? "/$prefix/" : '/';
        // 使用站点 URL 拼接全量 URL
        $url = config('app.url') . $prefix . $model_name . '/' . $model->id;
        // 拼接 HTML A 标签，并返回
        return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
    }

    function model_plural_name($model){
        // 从实体中获取完整类名，例如：App\Models\User
        $full_class_name = get_class($model);
        // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
        $class_name = class_basename($full_class_name);
        // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
        $snake_case_name = snake_case($class_name);
        // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
        return str_plural($snake_case_name);
    }

    function help_syncUserActivedAt(){
        // 获取昨天的日期，格式如：2017-10-21
        // $yesterday_date = Carbon::yesterday()->toDateString();
        $yesterday_date = \Carbon\Carbon::now()->toDateString();
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21;
        $hash = 'larabbs_last_actived_at_'.$yesterday_date;
        // 从Redis 中获取所有哈希表里的数据
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $dates = $redis->hGetAll($hash);
        $user_model = new \App\Models\User();
        // 遍历，并同步到数据库中
        foreach($dates as $user_id => $actived_at){
            // 会将 `user_1` 转换为 1
            $user_id = str_replace('user_','',$user_id);
            // 只有当用户存在时才更新到数据库中
            if($user = $user_model->find($user_id)){
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }
        // 以数据库为中心的存储，既已同步，即可删除
        $redis->del($hash);
    }
?>