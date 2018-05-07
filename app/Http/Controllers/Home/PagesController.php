<?php
namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagesController extends Controller{
    public function root(){
        return view('home.pages.root');
    }

    public function permissionDenied(){
        // 如果当前用户有权限访问后台，就直接跳转访问
        if(config('administrator.permission')()){
            return redirect(url(config('administrator.uri')),302);
        }
        // 否则使用视图
        return view('home.pages.permission_denied');
    }
}
