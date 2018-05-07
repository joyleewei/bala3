<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Category;
use App\Models\User;
use App\Models\Link;

class CategoriesController extends Controller{
    public function show(Category $category,Request $request,Topic $topic,User $user,Link $link){
        // 读取分类ID 关联的话题，并按每 20条 分页 Topic::with('user','category')
        // $topics = Topic::with('user','category')->where('category_id',$category->id)->paginate(20);
        $topics = $topic->withOrder($request->order)->where('category_id',$category->id)->paginate(20);
        // 活跃用户列表
        $active_users = $user->getActiveUsers();
        // 获取外链
        $links = $link->getAllCached();
        // 传参变量话题和分类到模板当中
        return view('home.topics.index',compact('topics','category','active_users','links'));
    }
}
