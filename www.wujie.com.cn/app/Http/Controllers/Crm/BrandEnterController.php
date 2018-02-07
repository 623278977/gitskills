<?php
/**
 * Created by PhpStorm.
 * Title：G端CRM品牌入驻
 * User: yaokai
 * Date: 2017/11/20 0020
 * Time: 14:25
 */

namespace App\Http\Controllers\Crm;

use App\Models\Agent\Brand;
use App\Models\Brand\Enter;
use DB, Input;
use App\Http\Controllers\Api\CommonController;
use Illuminate\Http\Request;

class BrandEnterController extends CommonController
{
    /**
     * 入驻列表   --数据中心版
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postList(Request $request)
    {
        $input = $request->all();

        $page_size = $request->get('page_size','10');

        //构建对象
        $builder = Enter::with([
            'categorys1' => function ($query) {
                $query->select('id','name');
            }
            ]);

        //条件搜索
        $builder = Enter::enterList($builder, $input);

        //分页结果
        $enter = paginate($builder->orderBy('id', 'desc')->paginate($page_size));

        //整理数据
        $enter['data'] = Enter::formatList($enter,$input);


        return AjaxCallbackMessage($enter, true);
    }


    /**
     * 审核入驻
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postEdit(Request $request)
    {
        $input = $request->all();

        if (empty($input['id'])) {
            return AjaxCallbackMessage('缺少id', false);
        }

        if (empty($input['status'])){
            return AjaxCallbackMessage('缺少状态', false);
        }
        if ($input['status']=='success'&&empty($input['contract_no'])){
            return AjaxCallbackMessage('成功必须有单号', false);
        }
        if(empty($input['remark'])){
            return AjaxCallbackMessage('缺少反馈', false);
        }

        //修改状态
        $res = Enter::where('id',$input['id'])
            ->where('status','pending')//等待中
            ->update([
            'status'        => $input['status'],
            'remark'        => $input['remark'],
            'contract_no'   => $input['contract_no']
            ]);

        if (!$res){
            return AjaxCallbackMessage('异常，请重试', false);
        }
        //审核结果发送push通知
        if($res=='success'){
            $text = trans('notification.brand_enter_success', ['brand'=>$res->brand_name]);
            send_notification('品牌入驻提交审核结果', $text, json_encode(['type' => 'brand_enter', 'style' => 'id','value'=>$res->id]), $res->uid, null, true);
        }else{
            send_notification('品牌入驻提交审核结果', $input['remark'], json_encode(['type' => 'brand_enter', 'style' => 'id','value'=>$res->id]), $res->uid, null, true);
        }

        return AjaxCallbackMessage('审核成功', true);

    }


}