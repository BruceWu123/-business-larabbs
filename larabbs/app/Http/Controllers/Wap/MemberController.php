<?php
namespace App\Http\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Input;
use MiddleHandler;
use Redirect;
use SeedlingsZO\MiddleHandler\Extend\Helper;
use Validator;
use App\CouponShare;
use App\CouponRecord;
use SeedlingsZO\MemberModule\MemberCenter;

class MemberController extends Controller
{

    /**
     * 整个的控制器里的方法都需要登录验证
     *
     * * Laravel使用Controller的方式比较特别，不需要进行parent::__construct()
     * @author huangcan
     */
    public function __construct()
    {
        $this->middleware('auth2');
    }

    /**
     * 保存新地址[post方式]
     * 1, 保存新地址之后有两个跳转地址:
     *   如果是从订单确认页跳转过来(经由地址列表页),则跳回到订单确认页
     *   如果从个人中心的地址列表页新建地址,则跳回到地址列表页
     * 2, 用户地址最多保存20个(原有逻辑)
     * @author huangcan
     */
    public function addAddress(Request $request)
    {
        // 从Form中取数据并转换的过程写成一个私有方法_getPostAddressForm
        // 不后置到Modules是因为获取用户输入本来就是Controller分内之事.
        $request_data = $this->_getPostAddressForm($request, true);

        $handler        = MiddleHandler::getMemberModules();
        $new_address_id = $handler::addAddress($request_data);

        if ($request_data['is_default']) {
            $handler::setDefaultAddress($request_data['mid'], $new_address_id);
        }

        $msg = ["err_msg" => "添加成功!", "data" => [], "err_code" => 0];

        if ($new_address_id > 0) {
            \Session::save('address_id', $new_address_id);
            // 如果是订单确认页新增地址，则直接跳回订单确认
            // 否则就是地址管理的页面，跳回地址管理列表页

            $msg['data']['jump_url'] = action('Wap\OrderController@getIndex', ['new_address_id' => $new_address_id]);
            /*if ($request->input('from_order') == true) {
            } else {
            $msg['data']['jump_url'] = action('Wap\MemberController@listAddress');
            }*/
            return Response::json($msg);
        }

        // 不返回成功，就表示某个地方出错。
        return Response::json(["err_msg" => "error!", "data" => [], "err_code" => 1]);
    }

    /**
     * 新地址页面[get获取]
     * @author huangcan
     */
    public function newAddress(Request $request)
    {
        $login_user     = \Tool::loginUser();
        $validator_rule = [
            'province_id' => 'required|integer|min:1',
            'city_id'     => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $validator_rule);

        // 返回数据均可为空
        $return = ['province' => [], 'city' => [], 'from' => ''];

        // 省和市id缺一，默认不理会(完成或没开始)
        if (!$validator->fails()) {
            $areas = Helper::getArea([$request->get('province_id'), $request->get('city_id')]);
            foreach ($areas as $area) {
                if ($area['parentid'] == 0) {
                    $return['province'] = $area;
                } else {
                    $return['city'] = $area;
                }
            }
        }
        $province_list = Helper::getProvinces();
        $from_order    = $request->get('from_order') == true;

        return view('wap.member.new_address', compact('province_list', 'return', 'from_order'));
    }

    /**
     * 新增地址选择省份页面
     * @author huangcan
     */
    public function selectProvince(Request $request)
    {
        $province_list = Helper::getProvinces();
        return view('wap.member.select_province', compact('province_list'));
    }

    /**
     * 新增地址选择城市页面
     * @author huangcan
     */
    public function selectCity(Request $request)
    {
        $validator_rule = [
            'province_id' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $validator_rule);

        // 如果参数不合法，跳回重新选择省份
        if ($validator->fails()) {
            return \Redirect::action('Wap\MemberController@selectProvince');
        }
        $cities = Helper::getCities($request->get('province_id'));

        if (1 || \Request::ajax()) {
            return json_encode($cities);
        }
        //return view('wap.member.select_city', compact('cities'));
    }

    /**
     * 编辑地址
     * @author huangcan
     */
    public function getEditAddress(Request $request)
    {
        $login_user = \Tool::loginUser();
        if ($login_user) {
            $mid = $login_user['mid'];
        }

        $address_id = $request->get('address_id');
        if (!($address_id > 0)) {
            return \Redirect::action('Wap\MemberController@listAddress');
        }
        $handler = MiddleHandler::getMemberModules();

        $address = $handler::getAddresses($mid, [$address_id])->first();
        if (!$address) {
            return \Redirect::action('Wap\MemberController@listAddress');
        }
        $address    = $address->toArray();
        $from_order = $request->input('from_order') == true;

        // Dont ask me why that code is so ugly.
        if ($request->input('province_id') && $request->input('city_id')) {
            $area_ids = [$request->input('province_id'), $request->input('city_id')];
            $areas    = Helper::getArea($area_ids);
            foreach ($areas as $area) {
                if ($area['id'] == $request->input('province_id')) {
                    $province = $area;
                }
                if ($area['id'] == $request->input('city_id')) {
                    $city = $area;
                }
            }

            // 条件具备，修改地址
            if (isset($province) && isset($city)) {
                $address['province_name'] = $province['title'];
                $address['province_code'] = $province['id'];
                $address['city_name']     = $city['title'];
                $address['city_code']     = $city['id'];
            }
        }

        return view('wap.member.edit_address', compact('address', 'from_order'));
    }

    /**
     * 保存编辑地址
     * @author huangcan
     */
    public function postEditAddress(Request $request)
    {
        $address_data = $this->_getPostAddressForm($request);
        $id           = $request->input('id');

        $msg = ["err_msg" => "修改成功!", "data" => [], "err_code" => 0];

        if ($id > 0) {
            $address_data['id'] = $id;
            $handler            = MiddleHandler::getMemberModules();
            $edit               = $handler::editAddress($address_data['mid'], $address_data);

            if (!$edit) {
                $msg['err_msg']  = '修改地址数据出错！';
                $msg['err_code'] = 1;
            }

            // 设置为默认地址
            if ($address_data['is_default']) {
                $handler::setDefaultAddress($address_data['mid'], $id);
            }
        } else {
            // 修改地址出错
            $msg['err_msg']  = '选择地址错误';
            $msg['err_code'] = 2;
        }

        if (\Request::ajax()) {
            if ($request->input('from_order') == true) {
                $msg['data']['jump_url'] = action('Wap\OrderController@getIndex', ['new_address_id' => $id]);
            } else {
                $msg['data']['jump_url'] = action('Wap\MemberController@listAddress');
            }

            return json_encode($msg);
        } else {
            return Redirect::action('Wap\MemberController@listAddress');
        }
    }

    /**
     * 地址列表页
     * @author huangcan
     */
    public function listAddress(Request $request)
    {
        $login_user = \Tool::loginUser();
        $mid        = $login_user['mid'];

        $handler = MiddleHandler::getMemberModules();

        // 查找默认地址
        $default_address = $handler::getAddresses($mid)->where('is_default', 1)->first();
        $default_address = $default_address ? $default_address->toArray() : null;

        // 其他地址
        $other_addresses = $handler::getAddresses($mid)
            ->where('is_default', '!=', '1')
            ->take(($default_address ? 19 : 20))
            ->get()
            ->toArray();

        // 是否订单确认页跳转过来？
        $from_order = $request->get('from') == 'order_info' ? true : false;

        return view('wap.member.list_address', compact('default_address', 'other_addresses', 'from_order'));
    }

    /**
     * 新建地址获取用户输入表单并组成成业务逻辑所需格式
     * TODO:输出表单数据组织成值对象的话，代码逻辑更清晰太多：
     * (值对象构造时可以自检属性是否合法，所以这部分检查输入的逻辑可以隐藏到值对象里面而且这种做法不能更加自然了)
     * @author huangcan
     */
    private function _getPostAddressForm(Request $request, $numberLimit = false)
    {
        $member_id = \Tool::getUserId();
        if ($member_id) {
            // 先检查是否允许添加地址(最多20个，以后可能有其他条件)
            $handler = MiddleHandler::getMemberModules();
            // 会员地址最多20个
            if ($numberLimit && !($request->input('id') > 0) && $handler::hadMaxAddresses($member_id)) {
                echo json_encode(["err_msg" => "您的地址已经有20个了", "data" => [], "err_code" => 400300]);
                exit();
            }
        }

        // 手机号码校验，为了前端提醒方便
        $validator_rule = [
            'number' => 'required|mobile',
        ];

        $validation = Validator::make($request->all(), $validator_rule);

        // 被调用的时候只能不优雅的中断了
        // 处于被其他Action调用，而这是违反Laravel的设计的
        // 但如果不这样做的话，
        // 1, 就要在两个地方(新建地址、修改地址)复制同样的代码
        // 2, 如果满足Laravel的设计方式，需要使用中间层产生地址格式的值对象(表单对象)，但这不合团队的代码风格
        // 如有疑问，或者确信目前的代码设计有问题，请修改之，以使代码更简洁优雅。
        // -- huangcan

        if ($validation->fails()) {
            echo json_encode(["err_msg" => $validation->errors(), "data" => [], "err_code" => 400301]);
            exit;
        }

        // 其它校验
        $validator_rule = [
            'city_id'     => 'required|integer|min:1',
            'province_id' => 'required|integer|min:1',
            'name'        => 'required|max:50',
            'is_default'  => 'bool',
            'address'     => 'required|max:300',
        ];
        $validation = Validator::make($request->all(), $validator_rule);
        if ($validation->fails()) {
            echo json_encode(["err_msg" => $validation->errors(), "data" => [], "err_code" => 400302]);
            exit;
        }

        // 不验证的request数据也不会保存

        $request_data['city_code']     = $request->input('city_id');
        $request_data['province_code'] = $request->input('province_id');
        $request_data['name']          = $request->input('name');
        $request_data['mobile']        = $request->input('number');
        $request_data['is_default']    = $request->input('is_default') ? 1 : 0;
        $request_data['address']       = $request->input('address');
        $request_data['mid']           = $member_id;

        $areas = Helper::getArea([$request->input('province_id'), $request->input('city_id')]);
        foreach ($areas as $area) {
            if ($area['id'] == $request->input('province_id')) {
                $request_data['province_name'] = $area['title'];
            }
            if ($area['id'] == $request->input('city_id')) {
                $request_data['city_name'] = $area['title'];
            }
        }

        return $request_data;
    }


}
