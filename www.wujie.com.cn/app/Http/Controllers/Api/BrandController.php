<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Brand\CollectRequest;
use App\Http\Requests\Brand\ConsultRequest;
use App\Http\Requests\Brand\DetailRequest;
use App\Http\Requests\Brand\EnterRequest;
use App\Http\Requests\Brand\FetchFundRequest;
use App\Http\Requests\Brand\GoodsRequest;
use App\Http\Requests\Brand\MessageRequest;
use App\Http\Requests\Brand\NewsRequest;
use App\Http\Requests\Brand\AskRequest;
use App\Http\Requests\Brand\TodayGoodsRequest;
use App\Http\utils\randomViewUtil;
use App\Models\Brand\Entity;
use App\Services\Brand;
use App\Services\Categorys;
use App\Services\Version\Brand\_v020400;
use Illuminate\Http\Request;
use DB, Input;
use App\Models\Brand\Entity as BrandModel;
use App\Models\Industry;
use App\Models\Identify;
use App\Exceptions\ExecuteException;
use App\Http\Requests\Brand\BrandRedpacketRequest;

class BrandController extends CommonController
{
    /**
     * 获取某个品牌详情
     */
    public function postDetail(DetailRequest $request, Brand $brand = null, $version = null)
    {
        $data = $request->input();
        $type = $request->get('type', 'app');
//        $status = Entity::where('id',$data['id'])->value('status');
//        if($status == 'disable'){
//            return AjaxCallbackMessage('异常，该品牌不存在，或者已经被删除', false);
//        }
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result = $versionService->bootstrap($data, ['type' => $type]);
        } else {
            $result = $brand->detail($data['id'], $data['uid'], $type);
        }

        //点击数加1
        BrandModel::where('id', $data['id'])->increment('click_num', 1);
        //伪浏览量
        $sham_view = BrandModel::where('id', $data['id'])->value('sham_click_num');
        $increment = randomViewUtil::getRandViewCount($sham_view);//增量
        BrandModel::where('id', $data['id'])->increment('sham_click_num', $increment);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 获取品牌咨询信息一个月内的
     */
    public function postConsult(ConsultRequest $request, Brand $brand = null)
    {
        $size = $request->input('size', 0);
        $id = $request->input('id', 0);
        $result = $brand->consult($id, $size);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 根据id获取一个商品信息
     */
    public function postGoods(GoodsRequest $request, Brand $brand = null)
    {
        $id = $request->input('id', 0);
        $type = $request->input('type', 'live');
        $result = $brand->goods($id, $type);

        if ($result == -1) {
            return AjaxCallbackMessage('异常，该商品对应的品牌不存在，或者已经被删除', false);
        }
        $result['detail'] = strip_tags($result['detail']);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 获取某个品牌相关的50个品牌  随机排序
     */
    public function postRecommend(Request $request, Brand $brand = null)
    {
        $data = $request->input();
        $model = BrandModel::single($data['id']);
        $result = $brand->recommend($model, 1);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 获取某个品牌相关的新闻列表
     */
    public function postNews(NewsRequest $request, Brand $brand = null)
    {
        $data = $request->input();
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 15);
        $result = $brand->news($data['id'], $page, $page_size);

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 收藏或取消收藏某个品牌
     */
    public function postCollect(CollectRequest $request, Brand $brand = null, $version = null)
    {
        $data = $request->input();

        if (substr($version, -5) >= 20700) {
            $versionService = $this->init(__METHOD__, $version);

            if ($versionService) {
                $response = $versionService->bootstrap($data, ['uid' => $this->uid]);

                return AjaxCallbackMessage($response['message'], $response['status']);
            }
        }

        $result = $brand->collect($data['id'], $data['uid'], $data['type']);

        if ($result) {
            return AjaxCallbackMessage('操作成功', true);
        } else {
            return AjaxCallbackMessage('操作失败', false);
        }
    }

    /**
     * 对品牌提问
     */
    public function postAsk(AskRequest $request, Brand $brand = null, $version = null)
    {
        $data = $request->input();

//        if (!checkMobileBlur(trim($data['mobile']))) {
//            return AjaxCallbackMessage('手机号格式不对', false, '');
//        }

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        $result = $brand->ask($data['id'], $data['uid'], $data['content']);

        if ($result) {
            return AjaxCallbackMessage('提问成功', true);
        } else {
            return AjaxCallbackMessage('提问失败', false);
        }
    }

    /**
     * 对品牌留言
     */
    public function postMessage(MessageRequest $request, Brand $brand = null, $version = null)
    {
        $data = $request->input();
        if (!checkMobileBlur(trim($data['mobile']))) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }

        $type = $request->get('type', 'app');
        $zone_id = $request->get('zone_id', 0);
        $address = $request->get('address', '');
        if (!in_array($type, ['app', 'html5'])) {
            return AjaxCallbackMessage('type只能为app或html5', false, '');
        }
//        if($type=='html5' && !isset($data['code'])){
//            return AjaxCallbackMessage('当type为html5时，code参数必传', false, '');
//        }

        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['type' => $type, 'zone_id' => $zone_id, 'address' => $address]);

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        $result = $brand->message(
            $data['id'],
            $data['uid'],
            $data['mobile'],
            $data['realname'],
            $data['zone_id'],
            $data['address'],
            $data['consult']
        );

        if ($result) {
            return AjaxCallbackMessage('操作成功', true);
        } else {
            return AjaxCallbackMessage('操作失败', false);
        }
    }

    /**
     * 获取品牌的分类  二级
     */
    public function postCategories(Categorys $categorys = null)
    {
        $result = $categorys->lists();

        return AjaxCallbackMessage($result, true);
    }

    /**
     * 品牌入驻
     */
    public function postEnter(EnterRequest $request, Brand $brand = null)
    {
        $data = $request->input();
        $introduce = $request->get('introduce', '');
        if (!checkMobileBlur(trim($data['mobile']))) {
            return AjaxCallbackMessage('手机号格式不对', false, '');
        }
        $result = $brand->enter($data['uid'], $data['mobile'], $data['realname'], $data['brand_name'], $data['category_id'], $introduce);

        if ($result) {
            return AjaxCallbackMessage('操作成功', true);
        } else {

            return AjaxCallbackMessage('操作失败', false);
        }
    }

    /*
     * 品牌列表
     */
    public function postLists($version = null, $search = 0, $uid = 0)
    {
        $uid = $uid ?: Input::input('uid', 0);
        $type = Input::input('type', 'brandList');
        if ($uid == 0 && $type != 'banner') {
            if ($type != 'recommend') {
                $type = 'brandList';
            }
        }
        $pageSize = Input::input('page_size', 8);
        $param = Input::all();
        if ($search) {
            $param = Input::except('uid');
        }

        //初始化
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $param['type'] = $type;
            $param['page_size'] = $pageSize;
            $data = $versionService->bootstrap($param);
        } else {
            $data = BrandModel::baseLists(
                $type,
                $param,
                function ($builder) {
                    $builder->select(
                        'id',
                        'uid',
                        'logo',
                        'name',
                        'investment_min',
                        'investment_max',
                        'keywords',
                        'introduce',
                        'issuer',
                        'summary',
                        'is_recommend',
                        'slogan',
                        'details as detail',
                        DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
                        DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
                        DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name')
                    );

                    return $builder;
                },
                function ($data) {
                    $service = new _v020400();
                    foreach ($data as $item) {
                        $item->investment_min = formatMoney($item->investment_min);
                        $item->investment_max = formatMoney($item->investment_max);
                        $item->logo = getImage($item->logo);
                        $item->investment_arrange = $item->investment_min . '万-' . $item->investment_max . '万';
                        $item->zone_name = $service->formatZoneName($item->zone_name);
                        $item->remark = $service->getBrandRemark($item->activity_id);
                        $item->detail = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $item->detail));
                        //$item->industry_ids = $this->getBrandIndustry($item->industry_ids);
                        if ($item->keywords) {
                            $item->keywords = strpos($item->keywords, ' ') !== false ? explode(' ', $item->keywords) : [$item->keywords];
                        } else {
                            $item->keywords = [];
                        }
                    }

                    return $data;
                },
                $pageSize
            );
        }

        $is_return = $search ?: 0;
        $return = $data ? (is_array($data) ? $data : $data->toArray()['data']) : [];
        if (!$return && !$is_return) {
            return AjaxCallbackMessage($return, true);
        }

        return ($is_return == 1) ? $return : AjaxCallbackMessage($return, true);
    }

    /**
     * 品牌下当天可购买的商品
     */
    public function postTodayGoods(TodayGoodsRequest $request, Brand $brand = null)
    {
        $brand_id = $request->get('brand_id', '');
        $goods = $brand->getGoods($brand_id);

        return AjaxCallbackMessage($goods, true);
    }

    /*
     * 领取创业基金
    */
    public function postFetchFund(FetchFundRequest $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 领取创业基金
    */
    public function postQuestion(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 15);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['page' => $page, 'page_size' => $page_size]);

            return AjaxCallbackMessage($response['data'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
    * 品牌墙
    */
    public function postWall(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        $page = $request->get('page', 1);
        $page_size = $request->get('page_size', 12);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['page' => $page, 'page_size' => $page_size]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
    * 品牌招商详情
    */
    public function postShowDetail(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
    * 品牌咨询电话留存
    */
    public function postTel(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        //开始事务
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            \DB::commit();
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }

    /*
     * 品牌评论
     * */
    public function postComments(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);
        //开始事务
        if ($versionService) {
            $response = $versionService->bootstrap($request->all(), ['request' => $request]);
            \DB::commit();
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


    /**
     * 在线客服咨询
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postAdvisory(Request $request, $version = null)
    {
        $data = $request->all();
        if (!isset($data['uid'])) {
            return AjaxCallbackMessage('缺少uid参数', false, '');
        }

        if (!isset($data['brand_id'])) {
            return AjaxCallbackMessage('brand_id', false, '');
        }

        
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($data);
            \DB::commit();
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }




    /**
     * 取消咨询
     *
     * @param Request $request
     * @param null $version
     * @return string
     * @author tangjb
     */
    public function postCancelAdvisory(Request $request, $version = null)
    {
        $data = $request->input();

        if (empty($data['send_investor_id'])) {
            return AjaxCallbackMessage('缺少品牌send_investor_id', false);
        }




        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($data);

            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('该接口不存在', false);
    }


    /*
     * 获取某个品牌的所有可用红包
     * */
    public function postGetBrandRedpacket(BrandRedpacketRequest $request, $version = null){
        $versionService = $this->init(__METHOD__, $version);
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }


}