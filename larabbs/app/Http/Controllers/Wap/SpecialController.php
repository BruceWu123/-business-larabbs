<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MiddleHandler;
use Message;
use SeedlingsZO\MiddleHandler\Wap\Factory\MemberFactory;
use Crypt;
use DB;
use App\CouponShare;
use App\CouponRecord;
use SeedlingsZO\MemberModule\MemberCenter;
use Validator;
use Tool;
use App\Http\Requests\SmsCodeRequest;
use Faker\Factory as Faker;
use Cache;
use Carbon\Carbon;
/**
 * 专题活动管理控制器
 */
class SpecialController extends Controller
{
    /**
     * 普通入口
     * @param $name -专题名称
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($name)
    {
        $login_user = \Tool::loginUser();
        return view('wap.special.'.$name.'/index', compact('login_user'));
    }

    /**
     *  母亲节活动-首页
     * @author huzisong
     * @date   2017-05-03 09:37:06
     */
    public function getMothersDayIndex()
    {
        return view('wap.special.mothersDay.index');
    }

    /**
     *  母亲节活动-商品页
     * @author huzisong
     * @date   2017-05-03 09:37:06
     */
    public function getMothersDayGoods()
    {
        return view('wap.special.mothersDay.goods');
    }

    /**
     *  父亲节活动-首页
     * @author huzisong
     * @date   2017-06-06 11:57:27
     */
    public function getFathersDayIndex()
    {
        $login_user = \Tool::loginUser();

        $response = MiddleHandler::adviceOrPraiseStatistic($login_user);

        return view('wap.special.fathersDay.index', compact('response'));
    }

    /**
     *  父亲节活动-商品页
     * @author huzisong
     * @date   2017-06-06 11:57:33
     */
    public function getFathersDayGoods()
    {
        return view('wap.special.fathersDay.goods');
    }

    /**
     *  七月凿冰山活动-首页
     * @author wengxiaohui
     * @date   2017-06-06 11:57:27
     */
    public function getIceDayIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.iceDay.index');
    }

    /**
     *  七月凿冰山活动-商品页
     * @author wengxiaohui
     * @date   2017-06-06 11:57:33
     */
    public function getIceDayGoods()
    {
        return view('wap.special.iceDay.goods');
    }

    /**
     *  八月周年庆活动-首页
     * @author wengxiaohui
     * @date   2017-08-01 11:57:27
     */
    public function getAugAnnivIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.augAnniv.index');
    }

    /**
     *  风湿关节炎专题-首页
     * @author wengxiaohui
     * @date   2017-08-08 09:14:27
     */
    public function getAugRheumatismIndex()
    {
        $login_user = \Tool::loginUser();

        $response = MiddleHandler::adviceOrPraiseStatistic($login_user);

        return view('wap.special.augRheumatism.index', compact('response'));
    }

    /**
     *  神经专题-首页
     * @author wengxiaohui
     * @date   2017-08-10 11:01:27
     */
    public function getAugNerveIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.augNerve.index');
    }

    /**
     *  八月今秋收割活动-首页
     * @author wengxiaohui
     * @date   2017-08-15 15:25:27
     */
    public function getFallDayIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.fallDay.index');
    }

    /**
     *  八月今秋收割活动-商品页
     * @author wengxiaohui
     * @date   2017-08-15 15:25:27
     */
    public function getFallDayGoods()
    {
        return view('wap.special.fallDay.goods');
    }

    /**
     *  前列腺专题-首页
     * @author wengxiaohui
     * @date   2017-08-10 11:01:27
     */
    public function getAugProstateIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.augProstate.index');
    }

    /**
     *  过敏专题-首页
     * @author wengxiaohui
     * @date   2017-09-05 11:01:27
     */
    public function getSepDermatologyIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.sepDermatology.index');
    }

    /**
     *  国庆中秋专题-首页
     * @author wengxiaohui
     * @date   2017-09-18 11:01:27
     */
    public function getOctNationalDayIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.octNationalDay.index');
    }

    /**
     *  十月会员专题-首页
     * @author wengxiaohui
     * @date   2017-09-27 11:01:27
     */
    public function getOctMemberIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.octMember.index');
    }

    /**
     *  双十一预热一活动-首页
     * @author wengxiaohui
     * @date   2017-10-12 11:01:27
     */
    public function getDouble11OneIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.double11One.index');
    }

    /**
     *  双十一预热活动
     * @author zhanghaobin
     * @date   2017-10-17 10:21:32
     */
    public function getDouble11TwoIndex()
    {
        $login_user = \Tool::loginUser();
        $time2 = strtotime('2017-10-26 23:59:59');
        if (time() <= $time2) {
            // 双十一预热一活动-预热二页
            return view('wap.special.double11One.preTwo');
        } else if (time() > $time2) {
            // 双十一预热二活动-首页
            return view('wap.special.double11Two.index');
        }
    }

    /**
     *  双十一主会场活动-首页
     * @author wengxiaohui
     * @date   2017-10-27 11:01:27
     */
    public function getDoubleElevenMainIndex()
    {
        //5947， 5948，5949
        $user_id = \Tool::getUserId();
        $isLogin = !empty($user_id);
        $coupon1 = MemberFactory::getCouponRecord(5947, $user_id)->count();
        $coupon2 = MemberFactory::getCouponRecord(5948, $user_id)->count();
        $coupon3 = MemberFactory::getCouponRecord(5949, $user_id)->count();
        return view('wap.special.DoubleElevenMain.index', compact('isLogin', 'coupon1', 'coupon2', 'coupon3'));
    }

    /**
     *  双十一主会场活动-领券
     * @author wengxiaohui
     * @date   2017-10-12 11:01:27
     */
    public function getDoubleElevenCouponIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.DoubleElevenCoupon.index');
    }

    /**
     *  十一月会员专题-首页
     * @author wengxiaohui
     * @date   2017-11-3 11:01:27
     */
    public function getNovMemberIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.novMember.index');
    }

    /**
     *  双十一返场活动-首页
     * @author wengxiaohui
     * @date   2017-11-9 11:01:27
     */
    public function getDoubleElevenBackIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.doubleElevenBack.index');
    }

    /**
     *  双十二组团红包分享页
     * @author wengxiaohui
     * @date   2017-11-14 11:57:33
     */
    public function getTwelveGroupInviting()
    {
        return view('wap.special.theDoubleTwelve.inviting');
    }

    /**
     * 双十二组团红包首页
     */
    public function getTwelveGroupIndex(CouponShare $couponShare)
    {
        //用户是否登录
        $is_login = \Tool::getUserId();
        //领取记录假数据
        $faker = Faker::create('zh_CN');
        $records = array();
        for ($i = 0; $i < 100; $i++) {
            $records[$i]['phone'] = substr_replace($faker->phoneNumber, '****', 3, 4);
            $records[$i]['amount'] = mt_rand(1, 10);
        }

        return view('wap.special.theDoubleTwelve.index', compact('records', 'is_login'));
    }

    /**
     * 生成红包分享码
     * @param CouponShare $couponShare
     * @return mixed
     * * @SWG\Get(path="/api/doubleTwelve/createCouponLink",
     *   tags={"生成红包分享码"},
     *   summary="生成红包分享码",
     *   description="根据验证通过的手机号生成分享码",
     *   operationId="createCouponLink",
     *   produces={"application/json"},
     *   @SWG\Response(response="default", description="操作成功")
     * )
     */
    public function createCouponLink(Request $request, CouponShare $couponShare)
    {
        $loginUser = Tool::loginUser();
        $account = $request->get('mobile_phone', $loginUser['account']);
        if (empty($account)) {
            return Message::jsonResponse(0, '账号参数缺失');
        }
        $data['mid'] = \Tool::getUserId();
        $data['account'] = $account;
        $data['coupon_link'] = md5($account);
        $record = $couponShare->where('account', $account)->first();
        if (!$record) {
            if ($couponShare->firstOrCreate($data)) {
                return Message::jsonResponse(1, '获取链接成功', ['coupon_link' => $data['coupon_link']]);
            } else {
                return Message::jsonResponse(0, '网络异常，请稍后再试');
            }
        }
        return Message::jsonResponse(1, '获取链接成功', ['coupon_link' => $record->coupon_link]);
    }

    /**
     * 双十二领取红包
     * @param Request $request
     * @param SmsCodeRequest $smsCodeRequest
     * @param CouponShare $couponShare
     * @param MemberCenter $memberCenter
     * @return mixed
     * * @SWG\POST(path="/api/doubleTwelve/postActivityBonus",
     *   tags={"领取红包"},
     *   summary="领取红包",
     *   description="手机号验证通过后自动登录，方便后续生成分享码",
     *   operationId="postActivityBonus",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     name="mobile_phone",
     *     type="integer",
     *     description="手机号",
     *     required=true,
     *   ),
     *   @SWG\Parameter(
     *     in="formData",
     *     name="captcha_code",
     *     type="string",
     *     description="图形验证码",
     *     required=false,
     *   ),
     *     @SWG\Parameter(
     *     in="formData",
     *     name="sms_code",
     *     type="string",
     *     description="短信验证码",
     *     required=true,
     *   ),
     *     @SWG\Parameter(
     *     in="formData",
     *     name="coupon_link",
     *     type="string",
     *     description="红包分享码",
     *     required=true,
     *   ),
     *   @SWG\Response(response="default", description="操作成功")
     * )
     */
    public function postActivityBonus(Request $request, CouponShare $couponShare, MemberCenter $memberCenter)
    {
        //给团长和团员送红包并短信通知，活动期间（11.29号0点—12.3号24点）每人最多送三次，在自己分享出去的链接不能领取
        $receiver_account = $request->get('mobile_phone');
        $coupon_link = $request->get('coupon_link');
        $coupon_share = $couponShare->where('coupon_link', $coupon_link)->first();
        //TODO 红包送到手机号所属的账号下，微信账号和手机账号不互通；手机号未注册(?手机号绑定了微信号)则自动注册；
        $res = $memberCenter->sendCoupon($receiver_account, 5985, $coupon_link, 'getter');//送领取人，领取人一个分享码领一次
        if ($res['status'] == 1) {
            $ret = $memberCenter->sendCoupon($coupon_share->account, 5985, $coupon_link, 'sender');//送分享人，分享人一个分享码领三次
            if ($ret['status'] == 1) {
                $request->mobile_phone = $coupon_share->account;
                $this->validate($request, [
                    'mobile_phone' => 'required|mobile'
                ]);
                $request_data['mobile_phone'] = $coupon_share->account;
                $request_data['scenarios_alias'] = 'doubleTwelve_coupon_notify';
                $request_data['platform_code'] = 'm.800pharm.com';
                $send_url = \Config::get('sms.send_code_url');
                \Tool::sendSmsService($request_data, $send_url);
            }
            return Message::jsonResponse(1, '红包领取成功');
        } else {
            return Message::jsonResponse(0, $res['msg']);
        }
    }

    //招行活动
    public function getBankActivityIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.bankActivity.index');
    }


    //双12优惠券
    public function getD12DiscountIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.D12Discount.index');
    }


    /**  商家同康双12活动-首页
     * @author wengxiaohui
     * @date   2017-11-29 11:01:27
     */
    public function getTheTongkangMainIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.theTongkangMain.index');
    }

    /**
     *  商家同康双12活动-一元购会场
     * @author wengxiaohui
     * @date   2017-11-29 11:01:27
     */
    public function getTheTongkangMainPageOne()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.theTongkangMain.pageOne');
    }

    /**
     *  商家同康双12活动-单品疗程会场
     * @author wengxiaohui
     * @date   2017-11-29 11:01:27
     */
    public function getTheTongkangMainPageTwo()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.theTongkangMain.pageTwo');
    }

    /**
     *  商家同康双12活动-类目分会场
     * @author wengxiaohui
     * @date   2017-11-29 11:01:27
     */
    public function getTheTongkangMainPageThree()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.theTongkangMain.pageThree');
    }

    //同康年末活动 chris 2017/12/18
    public function getTongKSubjectIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.tongKSubject.index');
    }

    //双旦活动 chris 2017/12/18
    public function getShuangDanIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.shuangDan.index');
    }

    public function getShuangDanActivity()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.shuangDan.activity', compact('login_user'));
    }

    /**
     *  1月会员日活动-首页
     * @author wengxiaohui
     * @date   2017-01-04 11:01:27
     */
    public function getJanMemberIndex()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.janMember.index');
    }

    /**
     *  呼吸专题活动
     * @author masterXimen
     * @date   2018-02-02 11:01:27
     */
    public function getbreathedissertation()
    {
        $login_user = \Tool::loginUser();

        return view('wap.special.breathedissertation.index');
    }

}

