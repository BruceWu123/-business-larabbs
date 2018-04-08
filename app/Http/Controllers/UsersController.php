<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Log;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{

    //个人主页
    public function show(User $user){

        return view('users.show',compact('user'));
    }

    //编辑个人信息
    public function edit(User $user){

        return view('users.edit',compact('user'));
    }

    //提交个人信息
    public function update(UserRequest $request, User $user,ImageUploadHandler $uploader)
    {

        $data = $request->all();

        //log::info($data);

        if($request->avatar){

           $result  = $uploader->save($request->avatar,'avatars',$user->id,362);

           if($result){
               $data['avatar']  =$result ['path'];

               $oldImgPath = $result['upload_path'];
           }

        }

        //切割旧图地址获取到图片需要的信息
        $oldImg = explode('/',$user->avatar);

        //旧图片名称
        $oldImgData =$oldImg[8];

        //获取旧图片的绝对路径
        $oldImgPath = $oldImgPath.'/'.$oldImgData;

        $user->update($data);

        if($user->update($data)&&$oldImgPath!=null){
            unlink($oldImgPath);
        }

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');

        /*$user->update($request->all());*/
    }
}
