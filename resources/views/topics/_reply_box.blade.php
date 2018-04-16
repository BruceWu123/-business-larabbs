@include('common.error')
<div class="reply-box">
    <form action="{{route('replies.store')}}" method="POST" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="topic_id" value="{{$topic->id}}">
        <div class="form-group">
            <textarea class="form-control" rows="3" placeholder="分享你的想法" name="content"></textarea>
        </div>
        <button type="submit" calss="btn btn-primary btn-sm"><i class="fa fa-share">回复</i></button>
    </form>
</div>
<hr>