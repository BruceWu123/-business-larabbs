<?php
namespace App\Http\Controllers\Presenter;

/**
 * 订单视图辅助模式  用于处理视图逻辑
 */
class OrderPresenter
{

    /**
     * 订单退款详情流程图样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getRefundProcessFlow($dualstatus)
    {
        $flow_s = 'finish';
        $flow_e = 'finish';
        $flow_c = 'finish';
        if ($dualstatus == 0) {
            $flow_s = 'success';
            $flow_c = '';
            $flow_e = '';
        }

        if ($dualstatus == 1) {
            $flow_s = 'finish';
            $flow_c = 'success';
            $flow_e = '';
        }

        if ($dualstatus == 2) {
            $flow_s = 'finish';
            $flow_c = 'finish';
            $flow_e = 'success';
        }

        $flow_string = '<li class="' . $flow_s . '">
                            <i class="disc"></i>
                            <span>申请退款</span>
                        </li>
                        <li class="' . $flow_c . '">
                            <i class="disc"></i>
                            <span>正在处理</span>
                        </li>';

        $span = '完成退款';
        if ($dualstatus == 4) {
            $span   = '拒绝退款';
            $flow_e = 'refuse_status';
        }

        $flow_string .= '<li class="' . $flow_e . '">
                            <i class="disc"></i>
                            <span>' . $span . '</span>
                        </li>';

        echo $flow_string;
    }

    /**
     * 订单退款详情状态内容样式显示
     *
     * @author chenfeng
     * @date 2017-01-03
     * @param array $member_info 个人中心信息
     * @return string
     */
    public function getRefundStatusContent($data)
    {
        $dualstatus = $data['dualstatus'];

        $status_clas_c1 = '';
        $status_clas_c2 = '';
        if (in_array($dualstatus, [0, 1, 5])) {
            $status_clas_c1 = 'cur';
            $status_clas_c2 = 'solid_icon';
        }

        $status_str_1 = '<li class="' . $status_clas_c1 . '">
                    <i class="' . $status_clas_c2 . '"></i>
                    <p class="handle_time">' . $data['applydates'] . '</p>
                    <p class="handle_title">退款原因：<span class="agree">' . $data['reason'] . '</span></p>
                    <p class="handle_title">买家留言：<span class="">' . $data['remark'] . '</span></p>
                </li>';

        if ($dualstatus == 4) {
            echo '<li class="cur">
                    <i class="solid_icon"></i>
                    <p class="handle_time">' . $data['dualdates'] . '</p>
                    <p class="handle_title">处理状态：<span class="refuse">不同意退款</span></p>
                    <p class="handle_title">商家意见：<span class="">' . $data['notapplyreason'] . '</span></p>
                </li>' . $status_str_1;
        }

        if ($dualstatus == 2) {
            echo '<li class="cur">
                    <i class="solid_icon"></i>
                    <p class="handle_time">' . $data['dualdates'] . '</p>
                    <p class="handle_title">处理状态：<span class="agree">同意退款</span></p>
                    <p class="handle_title">商家意见：<span class="">' . $data['notapplyreason'] . '</span></p>
                </li>' . $status_str_1;
        }

        if (in_array($dualstatus, [0, 1, 5])) {
            echo $status_str_1;
        }
    }

    /**
     * 订单评论评分显示
     *
     * @author chenfeng
     * @date 2017-02-15
     * @param integer $point 评分
     * @return string
     */
    public function orderEvaluatedInfoPoint($point)
    {
        $i_str = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $point) {
                $i_str .= '<i class="icon solid_icon"></i>';
            } else {
                $i_str .= '<i class="icon dotted_icon"></i>';
            }
        }

        echo $i_str;
    }
}
