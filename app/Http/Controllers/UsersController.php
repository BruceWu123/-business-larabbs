<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Log;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }


    //个人主页
    public function show(User $user){

        return view('users.show',compact('user'));
    }

    //编辑个人信息
    public function edit(User $user){

        $this->authorize('update', $user);

        return view('users.edit',compact('user'));
    }

    //提交个人信息
    public function update(UserRequest $request, User $user,ImageUploadHandler $uploader)
    {

        $this->authorize('update', $user);

        $data = $request->all();

        if($request->avatar){

            $result  = $uploader->save($request->avatar,'avatars',$user->id,362);

            if($result){
                $data['avatar']  =$result ['path'];

                $oldImgPath = $result['upload_path'];

            }

        }

        //数据库获取旧图地址
        $oldDataImgPath =$user->avatar;

        //判断是否存在旧图，存在则删除旧图
        if($oldDataImgPath){

            //切割旧图地址获取到图片需要的信息
            $oldImg = explode('/',$user->avatar);

            //旧图片名称
            $oldImgData =$oldImg[8];

            //获取旧图片的绝对路径
            $oldImgPath = $oldImgPath.'/'.$oldImgData;

            unlink($oldImgPath);
        }

        $user->update($data);

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');

    }
}
