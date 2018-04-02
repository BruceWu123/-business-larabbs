<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Input;
use Message;
use MiddleHandler;
use Redirect;
use Session;
use Tool;
use Validator;
use SeedlingsZO\MiddleHandler\Wap\Modules\MemberModules;

class OrderController extends Controller
{
    /**
     * 整个的控制器里的方法都需要登录验证
     *
     * @author huangcan
     */
    public function __construct()
    {
        $this->middleware('auth2');
    }

    /**
     * 订单确认
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getIndex(Request $request)
    {
        $user_id            = \Tool::getUserId();
        $record_ids         = [];
        $order_cart_records = $user_id . '_order_cart_records';

        if (!Session::has($order_cart_records)) {
            $cart_url = action('Wap\CartController@getIndex');
            return redirect('/login?return=' . $cart_url);
        }

        $record_ids     = Session::get($order_cart_records);
        $member_handler = MiddleHandler::getMemberModules();
        $order_handler  = MiddleHandler::getOrderModules();

        if ($request->get('new_address_id')) {
            $address = $member_handler::getAddresses($user_id, [$request->get('new_address_id')])->first();
        } else {
            $address = $member_handler::getDefaultAddress($user_id);
        }

        // 订单确认页店铺及商品数据列表
        $order_info = $order_handler::orderInfo($user_id, $record_ids);
        // 商品总价
        $order_total = $order_info->sum('goods_price');
        // 满减后的商品总价
        $order_premium = $order_info->pluck('premium')->sum('cutcount');
        $order_total_premiumed = $order_total - $order_premium;
        // 广货产品统计
        $guanghuo_total = $order_info->sum(function ($shop) {
            return $shop['store_goods']->sum('guanghuo_count');
        });

        // 是否已有地址存在
        $has_address = $member_handler::countAddresses($user_id) > 0;

        // 在线支付
        $pay_online_shippings = [];
        // 货到付款
        $cod_shippings = [];

        // 订单确认页商家配送方式
        if ($has_address && $address && $order_info->count() > 0) {
            $order_shippings = $order_handler::orderShippings(
                $order_info,
                $address,
                $member_handler::getMemberType($user_id)
            );
            $pay_online_shippings = $order_handler::getPayOnlineShippings($order_shippings);
            $cod_shippings    = $order_handler::getCashOnDeliverys($order_shippings);
            //只对药品关闭在线配送的货到付款 9/8/17
            $online_shop_codes = array_keys($pay_online_shippings);
            foreach($online_shop_codes as $shop_code){
                if($order_info[$shop_code]['has_drugs']){
                    unset($cod_shippings[$shop_code]);
                }
            }
        }
//        dd($cod_shippings);
        // 会员可用积分
        $remain_consume_score = $member_handler::getMemberRemainConsumeScore($user_id);

        // 系统设置积分比例
        $score_cost_rate = $member_handler::getScore2OrderCostRate();

        // 积分抵现的提示：(?)积分抵1元 当前值?=200
        $one_yuan_score = $member_handler::getOneYuanScore($score_cost_rate);

        // 积分抵现
        $score_cost_money_total = $member_handler::getMemberScoreCostMoneyTotal($remain_consume_score, $score_cost_rate);
        // 计算会员x倍积分
        $member_score_rate = $member_handler::getMemberScoreCalculateRate($user_id);
        // 获取会员最大可用红包
        foreach($order_info as $key => $order){
            $coupon_order_info[$key]['pay_fee'] = $order['goods_price'];
            $coupon_order_info[$key]['pay_type'] = $order['is_online_pay'] ? 1 : 2;
        }
        $coupon_list = $member_handler::getMemberCoupons($user_id, $order_total, $guanghuo_total,$coupon_order_info);
        $coupon_canuse_num = $coupon_list->where('coupon_canuse', true)->count();
        //小能所需资料
        $xiaoneng = $order_handler::getXiaoNengData($order_info);

        if (isset($_GET['data'])) {
            dd('Order Data Dump:', $order_info->toArray(), $cod_shippings, $pay_online_shippings, $coupon_list->toArray());
        }

        return view('wap.order.index', compact('address', 'has_address', 'order_info', 'score_cost_money_total', 'remain_consume_score', 'one_yuan_score', 'member_score_rate', 'cod_shippings', 'pay_online_shippings', 'coupon_list', 'coupon_canuse_num', 'xiaoneng', 'order_total', 'order_total_premiumed', 'guanghuo_total'));
    }

    /**
     * 根据订单支付方式获取可用红包
     * @param Request $request order_total,guanghuo_total,shop_order
     * shop_order = [ shop_code => [ 'pay_fee' => number, 'pay_type' => string ],...]
     * @return mixed
     */
    public function postMemberCoupons(Request $request)
    {
        $param = $request->only(['order_total','guanghuo_total','shop_order']);
        $param['member_id'] = \Tool::getUserId();
        if(empty($param['member_id'])){
            return Message::getErrorMessage(14223);
        }
        extract($param);
        $coupons = MemberModules::getMemberCoupons($member_id,$order_total,$guanghuo_total,$shop_order);
        return response()->json($coupons);
    }

    /**
     * 检查用户红包是否可用
     * @param Request $request
     * @return array
     * @author huangcan
     */
    public function postCheckCoupon(Request $request)
    {
        $validator_rule = [
            'coupon_code' => 'required|max:50',
        ];

        $validation = Validator::make($request->all(), $validator_rule);
        if ($validation->fails()) {
            return Response::json(["err_msg" => $validation->errors(), "data" => [], "err_code" => 1]);
        }

        $coupon_code = $request->input('coupon_code');
        $user_id     = Tool::getUserId();

        $order_handler     = MiddleHandler::getOrderModules();
        $promotion_handler = MiddleHandler::getPromotionModules();
        $member_handler    = MiddleHandler::getMemberModules();

        $record_ids         = [];
        $order_cart_records = $user_id . '_order_cart_records';
        if (Session::has($order_cart_records)) {
            $record_ids = Session::get($order_cart_records);
        }

        $order_info = $order_handler::orderInfo($user_id, $record_ids);

        // TODO: ASK: ?红包计算在商品上的吧？计算时需要加入优惠券、运费等等吗？
        $total_price = $order_info->sum('goods_price');
        $shop_order = $request->get('shop_order');
        // 首先检查是否通用红包
        $response_data = $promotion_handler::checkUnlimitedCoupon($coupon_code, $user_id, $total_price, $shop_order);

        // 红包不存在的话，再在用户当前可用红包中检查是否存在
        if (!$response_data) {
            $coupon_list = $member_handler::getMemberCoupons($user_id, $total_price,0,$shop_order);
            if ($coupon_list) {
                $coupon = $coupon_list->where('COUPONCODE', $coupon_code);
                if ($coupon->count() > 0) {
                    $coupon_data   = $coupon->first();
                    $response_data = ['use' => 1, 'amount' => $coupon_data['AMOUNT'], 'couponid' => $coupon_data['COUPONID']];
                }
            }
            // 还是查找不到
            if (!$response_data) {
                $response_data = ['use' => 2, 'msg' => "对不起，您输入的红包不存在或无法使用！"];
            }
        }

        return $response_data;
    }

    /**
     * 检查用户输入的商家优惠券编号是否可用
     * @param $request
     * @author huangcan
     */
    public function postCheckCard(Request $request)
    {
        // 检测格式来自数据库字段
        $validator_rule = [
            'shop_code' => 'required|max:50',
            'card_prex' => 'required|max:45',
            'cart_id'   => 'required',
        ];

        $validation = Validator::make($request->all(), $validator_rule);
        if ($validation->fails()) {
            return Response::json(['use' => 2, 'msg' => "对不起，您输入的优惠券不存在或无法使用！"]);
        }

        $order_handler     = MiddleHandler::getOrderModules();
        $promotion_handler = MiddleHandler::getPromotionModules();

        $shop_code  = $request->input('shop_code');
        $card_prex  = $request->input('card_prex');
        $record_ids = $request->input('cart_id');
        $user_id    = Tool::getUserId();

        $order_info = $order_handler::orderInfo($user_id, $record_ids);

        $response_data = $promotion_handler::checkCardDistCardByPrex($shop_code, $card_prex, $user_id, $order_info);

        if (\Request::ajax()) {
            return Response::json($response_data);
        }
    }

    /**
     * 创建订单
     *
     * @author chenfeng
     * @date 2016-11-25
     * @param Request $request 请求参数
     * @return json
     */
    public function postCreateOrder(Request $request)
    {
        $login_user = \Tool::loginUser();
        if (empty($login_user)) {
            return Message::getErrorMessage(14223);
        }
        \Log::info("=======记录用户session========", $login_user);

        if (!\Request::ajax()) {
            return Message::getErrorMessage(14225);
        }

        $return = [
            'code' => 0,
            'msg'  => '',
            'data' => ['is_on_sale' => 0, 'url' => ''],
        ];

        $create_order = MiddleHandler::createOrder($login_user['mid'], $request->all());
        if (isset($create_order['err_code'])) {
            $return['code'] = $create_order['err_code'];
            $return['msg']  = $create_order['err_msg'];
            $data           = $create_order['data'];
        } else {

           /*
           * 给特定渠道的用户添加推广订单数量
           * ddys -> 叮叮医生用户
           */
            MiddleHandler::addCpsOrder($create_order['batch_oid'], $login_user['mid'], ['ddys']);

            $data = ['url' => action("Wap\PayController@getIndex", [$create_order['batch_oid']])];
        }

        $return['data'] = array_merge($return['data'], $data);

        return $return;
    }

    /**
     * 订单管理
     *
     * @author yufuchu
     * @param Request $request
     * @return view
     */
    public function getOrderManage(Request $request)
    {
        $page      = 1;
        $page_size = 10;
        if ($request->has('page')) {
            $page = $request->get('page');
        }
        $member_id     = Tool::getUserId();
        $response_data = MiddleHandler::getUserAllOrders($member_id, $page, $page_size);
        // dd($response_data);
        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        return view('wap.order_management.order_management_all', compact('response_data'));
    }

    /**
     * 订单详情
     *
     * @author chenfeng
     * @date 2017-02-07
     * @param integer $order_id 订单ID
     * @return view
     */
    public function getOrderInfo($order_id)
    {
        $user_id = \Tool::getUserId();

        $response_data = MiddleHandler::getOrderDetail($order_id, $user_id);
        if (isset($response_data['err_code'])) {
            return \Redirect::action("Wap\ErrorController@getIndex");
        }

        // 计算会员x倍积分
        $member_handler    = MiddleHandler::getMemberModules();
        $member_score_rate = $member_handler::getMemberScoreCalculateRate($user_id);
        $score_rate        = isset($member_score_rate['cal_rate']) ? floor($member_score_rate['cal_rate']) : 1;

        return view('wap.order_management.order_info', compact('response_data', 'score_rate'));
    }

    /**
     * 待付款
     * @author JokerLinly
     * @date   2017-01-16
     * @return [array]     [description]
     */
    public function getPayment(Request $request)
    {
        $page      = 1;
        $page_size = 10;
        if ($request->has('page')) {
            $page = $request->get('page');
        }
        $member_id     = Tool::getUserId();
        $response_data = MiddleHandler::getUserUnPaymentOrders($member_id, $page, $page_size);
        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        return view('wap.order_management.order_payment', compact('response_data'));
    }

    /**
     * 待收货
     * @author JokerLinly
     * @date   2017-01-16
     * @return [type]     [description]
     */
    public function getHarvested(Request $request)
    {
        $page      = 1;
        $page_size = 10;
        if ($request->has('page')) {
            $page = $request->get('page');
        }
        $member_id     = Tool::getUserId();
        $response_data = MiddleHandler::getUserUnHarvestedOrders($member_id, $page, $page_size);

        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        return view('wap.order_management.order_harvested', compact('response_data'));
    }

    /**
     * 退款
     * @author JokerLinly
     * @date   2017-01-16
     * @param  Request    $request [description]
     * @return [type]              [description]
     */
    public function getRefund(Request $request)
    {
        $page      = 1;
        $page_size = 10;
        if ($request->has('page')) {
            $page = $request->get('page');
        }
        $member_id     = Tool::getUserId();
        $response_data = MiddleHandler::getUserRefundOrders($member_id, $page, $page_size);
        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        return view('wap.order_management.order_refund', compact('response_data'));
    }

    /**
     * 退款申请
     *
     * @author chenfeng
     * @date 2017-01-18
     * @return view
     */
    public function getRdfundApply($oid)
    {
        return view('wap.order_management.order_refund_apply', compact('oid'));
    }

    /**
     * 退款申请提交
     *
     * @author chenfeng
     * @date 2017-01-18
     * @return array
     */
    public function postRdfundApply(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();
        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => ''];

        return Message::jsonMessage(MiddleHandler::createdRefundOrder($request->input()));
    }

    /**
     * 待评价
     * @author JokerLinly
     * @date   2017-01-16
     * @param  Request    $request [description]
     * @return [type]              [description]
     */
    public function getEvaluated(Request $request)
    {
        $page      = 1;
        $page_size = 10;
        if ($request->has('page')) {
            $page = $request->get('page');
        }
        $member_id     = Tool::getUserId();
        $response_data = MiddleHandler::getUserUnEvaluatedOrders($member_id, $page, $page_size);
        if (\Request::ajax()) {
            return json_encode($response_data);
        }
        return view('wap.order_management.order_evaluated', compact('response_data'));
    }

    // 退款详情
    public function getRdfundInfo($order_id)
    {
        $response_data = MiddleHandler::refundOrderDetail($order_id);
        if (isset($response_data['err_code'])) {
            return \Redirect::action("Wap\ErrorController@getIndex");
        }

        return view('wap.order_management.order_refund_info', compact('response_data'));
    }

    /**
     * 订单评论
     *
     * @author chenfeng
     * @date 2017-01-10
     * @param integer $order_id 订单ID
     * @return view
     */
    public function getOrderEvaluate($order_id)
    {
        $user_id = \Tool::getUserId();

        $evaluate_data = MiddleHandler::orderEvaluateData($order_id, $user_id);
        if (isset($evaluate_data['err_code'])) {
            return \Redirect::action("Wap\ErrorController@getIndex");
        }
        return view('wap.order_management.order_evaluated_info', compact('evaluate_data'));
    }

    /**
     * 订单评价提交
     *
     * @author chenfeng
     * @date 2017-01-11
     * @param Request $request 请求数据
     * @return json
     */
    public function postOrderEvaluate(Request $request)
    {
        //如果不是json请求
        $is_ajax_request = Tool::isAjaxRequest();

        if (isset($is_ajax_request['err_code'])) {
            return Message::jsonMessage($is_ajax_request);
        }

        $data = ['url' => action('Wap\OrderController@getOrderManage')];
        return Message::jsonMessage(MiddleHandler::orderEvaluateSubmit($request->input()), $data);
    }

    /**
     * 删除订单
     * @author JokerLinly
     * @date   2017-01-19
     * @param  Request    $request [description]
     * @return [type]              [description]
     */
    public function postDelOrderByOid(Request $request)
    {
        $userid = \Tool::getUserId();

        $oid = $request->oid;
        if (empty($oid)) {
            return Message::getErrorMessage(10000);
        }

        $res = MiddleHandler::postDelOrderByOid($oid, $userid);

        if (!$res) {
            return Message::getErrorMessage(10000);
        }
        return Message::jsonMessage(['msg' => '删除订单成功!']);
    }

    /**
     * 取消订单
     * @author JokerLinly
     * @date   2017-01-19
     * @param  string     $value [description]
     * @return [type]            [description]
     */
    public function postCancelOrderByOid(Request $request)
    {
        $userid = \Tool::getUserId();

        $oid      = $request->oid;
        $reasonId = $request->reasonId;
        if (empty($oid)) {
            return Message::getErrorMessage(10000);
        }

        $res = MiddleHandler::postCancelOrderByOid($oid, $userid, $reasonId);

        if (!$res) {
            return Message::getErrorMessage(10000);
        }
        return Message::jsonMessage(['msg' => '取消订单成功!']);
    }

    /**
     * 确认收货
     * @author JokerLinly
     * @date   2017-01-19
     * @param  string     $value [description]
     * @return [type]            [description]
     */
    public function postMakeSureGetProduct(Request $request)
    {
        $userid = \Tool::getUserId();

        $oid = $request->oid;
        if (empty($oid)) {
            return Message::getErrorMessage(10000);
        }

        $res = MiddleHandler::postMakeSureGetProduct($oid, $userid);

        if (!$res) {
            return Message::getErrorMessage(10000);
        }
        return Message::jsonMessage(['msg' => '确认收货成功!']);
    }

    /**
     * 测试
     * @author chenfeng
     * @date 2016-11-25
     * @return [type] [description]
     */
    public function getCreateOrder()
    {
        $order_info = [
            'address_id'  => 1419059,
            'point'       => 0,
            'coupon_code' => 0, //5641,
            'invoice'     => '发票',
            'shop'        => [
                [
                    'id'             => 25330,
                    'shop_id'        => 100281,
                    'coupon_id'      => 0,
                    'logistics_id'   => 62602,
                    'pay_type'       => 'online',
                    'leave_message'  => '我在测试',
                    'whetherkuajing' => '',
                    'kuajingName'    => '',
                    'kuajingNum'     => '',
                    'c_id'           => [2943269, 2943270],
                ],
            ],
        ];

        //l1:1057962/l2:1075392/f1:1078510/fc:1106494
        //return MiddleHandler::createOrder(1075392, $order_info);
    }
}
