<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //用户与话题中间的关系是 一对多 的关系，一个用户拥有多个主题，在 Eloquent 中使用 hasMany() 方法进行关联。
    public function topics(){
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model){
        return $this->id == $model->user_id;
    }

    //一个用户可以有多条回复
    public function replies(){
        return $this->hasMany(Reply::class);
    }
}
