<?php

namespace App\Http\Controllers\Tencentgdt;

use App\Http\Requests\Activity\ScrollsRequest;
use App\Models\Gdt\Gdt;
use App\Models\Gdt\Status;
use Illuminate\Http\Request;
use App\Http\Libs\Weixin\Lib\WxPayOrderQuery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GdtController extends Controller
{
    /**
     * 接收点击接口
     * muid          :string 设备id
     * click_time    :string 点击发生的时间
     * click_id      :string 广点通后台生成的点击id,唯一
     * appid         :int Android，iOS应用id
     * advertiser_id :int 广告主在广点通（e.qq.com）的账户id
     * app_type      :string app类型 取值为 android或ios
     */
    public function getGdt(Request $request)
    {

        $data = $request->input();
        $result = Gdt::gdt($data);

        return $result;
    }

    /**
     * 转化数据上报接口
     */
    public function postGdt()
    {
        $result = Gdt::handle();
        return $result;
    }


}
