<?php

namespace App\Http\Middleware;

use Closure;
use App\CouponRecord;
use Message;

class CouponGroup2
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
        $account      = $request->get('mobile_phone');
        $coupon_link  = $request->get('coupon_link');
        $couponRecord = new CouponRecord();
        $this_coupon_record = $couponRecord->where(['account'=>$account, 'couponid'=>5974, 'comment'=>$coupon_link])->count();
        if($this_coupon_record > 0){
            return Message::jsonResponse(3, '这个红包已经领过了');
        }
        $coupon_records = $couponRecord->where(['account'=>$account, 'couponid'=>5974])->whereBetween('createdate',['2017-11-29','2017-12-4'])->count();
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
