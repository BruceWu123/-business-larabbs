@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel panel-default col-md-10 col-md-offset-1">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-edit"></i> 编辑个人资料
            </h4>
        </div>

        @include('common.error')

        <div class="panel-body">

            <form action="{{ route('users.update', $user->id) }}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data" >
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <label for="name-field">用户名</label>
                    <input class="form-control" type="text" name="name" id="name-field" value="{{ old('name', $user->name ) }}" />
                </div>
                <div class="form-group">
                    <label for="email-field">邮 箱</label>
                    <input class="form-control" type="text" name="email" id="email-field" value="{{ old('email', $user->email ) }}" />
                </div>
                <div class="form-group">
                    <label for="introduction-field">个人简介</label>
                    <textarea name="introduction" id="introduction-field" class="form-control" rows="3">{{ old('introduction', $user->introduction ) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="" class="avatar-label">用户头像</label>
                    <input type="file" onchange="selectImage(this);" name="avatar" id="avatar" />
                    @if($user->avatar)
                    <img class="thumbnail img-responsive" id="image" src="{{$user->avatar}}"  width="200"/>
                    @else
                        <img class="thumbnail img-responsive" id="image" src="https://fsdhubcdn.phphub.org/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60"  width="200"/>
                    @endif

                </div>

                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<script>
    var image = '';
    function selectImage(file){
        if(!file.files || !file.files[0]){
            return;
        }
        var reader = new FileReader();
        reader.onload = function(evt){
            document.getElementById('image').src = evt.target.result;
            image = evt.target.result;

        }
        reader.readAsDataURL(file.files[0]);
    }

</script>