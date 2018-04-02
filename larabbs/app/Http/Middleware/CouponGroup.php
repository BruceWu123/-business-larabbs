<?php

namespace App\Http\Middleware;

use Closure;
use Message;
use Log;
use App\CouponShare;
use App\CouponRecord;

class CouponGroup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(time() < strtotime('2017-11-28')){
            return Message::jsonResponse(5,'活动还没开始');
        }
        if(time() > strtotime('2017-12-04')){
            return Message::jsonResponse(5,'本活动已结束，感谢您的关注，敬请期待下次活动');
        }
        $account      = $request->get('mobile_phone');
        $coupon_link  = $request->get('coupon_link');
        if(empty($account) || empty($coupon_link)){
            return Message::jsonResponse(9,'缺少参数');
        }

        $couponShare  = new CouponShare();
        $coupon_share = $couponShare->where('coupon_link', $coupon_link)->first();
        if(!$coupon_share){
            return Message::jsonResponse(6, '红包链接不存在');
        }
        //领取自己的红包
        if($coupon_share->account == $account){
            return Message::jsonResponse(4, '不能领取自己分享的红包');
        }

        $couponRecord = new CouponRecord();
        $this_coupon_record = $couponRecord->where(['account'=>$account, 'couponid'=>5985, 'comment'=>$coupon_link])->count();
        if($this_coupon_record > 0){
            return Message::jsonResponse(3, '这个红包已经领过了');
        }
        $coupon_records = $couponRecord->where(['account'=>$account, 'couponid'=>5985])->whereBetween('createdate',['2017-11-28','2017-12-4'])->count();
        //领取超过3次
        if($coupon_records >= 3){
            return Message::jsonResponse(2, '领取次数超过3次');
        }
        if($coupon_records > 3){
            Log::error('==活动异常==双12组团红包领取超过3次，用户账号为'.$account);
        }

        return $next($request);
    }
}
