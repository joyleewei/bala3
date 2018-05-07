<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

use App\Models\User;

class UsersController extends Controller{
    // 使用中间件 登陆验证
    public function __construct(){
        $this->middleware('auth',[
            'except'=>[
                'show'
            ]
        ]);
    }

    public function show(User $user){
        return view('home.users.show',compact('user'));
    }

    public function edit(User $user){
        $this->authorize('update', $user);
        return view('home.users.edit',compact('user'));
    }

    public function update(UserRequest $request,ImageUploadHandler $uploader,User $user){
        $this->authorize('update', $user);
        $data = $request->all();

        if($request->avatar){
            $result = $uploader->save($request->avatar,'avatars',$user->id,362);
            if($result){
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功。');
    }

}
