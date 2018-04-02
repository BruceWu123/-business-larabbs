<?php
namespace App\Http\Controllers\Presenter;

/**
 * 个人中心视图辅助模式  用于处理视图逻辑
 */
class PersonalPresenter
{

    /**
     * 获取购物车数量样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getCartNumber($member_info)
    {
        if (empty($member_info['cart_number'])) {
            echo '';
        } else {
            echo '<i class="num_icon">' . $member_info['cart_number'] . '</i>';
        }
    }

    /**
     * 获取待收货订单数量样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getNoCollectOrderNumber($member_info)
    {
        if (empty($member_info['no_collect_order'])) {
            echo '';
        } else {
            echo '<i class="num_icon">' . $member_info['no_collect_order'] . '</i>';
        }
    }

    /**
     * 获取待付款订单数量样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getNoPayOrderNumber($member_info)
    {
        if (empty($member_info['no_pay_order'])) {
            echo '';
        } else {
            echo '<i class="num_icon">' . $member_info['no_pay_order'] . '</i>';
        }
    }

    /**
     * 获取待评价订单数量样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getNoEvaluateOrderNumber($member_info)
    {
        if (empty($member_info['no_evaluate_order'])) {
            echo '';
        } else {
            echo '<i class="num_icon">' . $member_info['no_evaluate_order'] . '</i>';
        }
    }

    /**
     * 获取退款订单数量样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getRefundOrderNumber($member_info)
    {
        if (empty($member_info['refund_order'])) {
            echo '';
        } else {
            echo '<i class="num_icon">' . $member_info['refund_order'] . '</i>';
        }
    }

    /**
     * 账号管理手机是否绑定
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getAccountNumberMobileIsBind($member_info)
    {
        if (empty($member_info['mobile']) || empty($member_info['verify_mobile'])) {
            echo '<div class="span_right no_bind">
                    <a href="' . action("Wap\PersonalCenterController@getCenterBindMobile") . '">未绑定</a>
                </div>';
        } else {
            echo '<div class="span_right binded">
                    <span>' . $member_info['mobile'] . '</span>
                    <a href="' . action("Wap\PersonalCenterController@getModifyPhone") . '">[修改绑定]</a>
                </div>';
        }
    }

    /**
     * 账号管理显示微信信息
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getAccountNumberWxInfo($member_info)
    {
        // if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        if (!empty($member_info['wx_name'])) {
            echo '<div class="weixin cf">
                <span class="span_left">微信:</span>
                <span class="span_right">' . $member_info['wx_name'] . '</span>
            </div>';
        } else {
            echo '';
        }
    }
}
