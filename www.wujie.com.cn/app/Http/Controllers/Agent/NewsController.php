<?php namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Api\CommonController;
use App\Http\Requests\News\DetailRequest;
use App\Models\Comment\Entity as Comment;
use App\Models\Orders\Entity as Orders;
use App\Services\Version\News\_v020600;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\utils\randomViewUtil;
use \Illuminate\Http\Request;
use App\Models\Orders\Items;
use App\Models\User\Praise;
use App\Models\News\Entity;
use App\Services\News;

class NewsController extends CommonController
{
    /**
     * 获取资讯列表 zhaoyf
     *
     * @param Request $request
     * @param News $news
     * @param null $version
     * @return string
     */
    public function postList(Request $request, News $news, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'news' => $news]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 资讯列表---搜索需要
     *
     * @param Request $request
     * @param News $news
     * @param null $version
     */
    public function postLists(Request $request, News $news, $version = null)
    {
        $result = json_decode($this->postList( $request,  $news, $version) ,true);
        return $result['message'];
    }

    /**
     * 获取资讯详情 zhaoyf
     *
     * @param DetailRequest $request
     * @param News|null $news
     * @param null $version
     * @return string
     */
    public function postDetail(DetailRequest $request, News $news = null, $version = null)
    {
        $result = $request->input();

        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($result, ['request' => $request, 'news' => $news]);

            return AjaxCallbackMessage($response['message'], $response['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }


    /**
     * 品牌课程资讯打卡列表
     * @User yaokai
     * @param Request $request
     * @param null $version
     * @return string
     */
    public function postClock(Request $request, $version = NULL)
    {
        $input = $request->all();
        if (empty($input['news_id'])) {
            return AjaxCallbackMessage('缺少资讯id', false);
        }
        $versionService = $this->init(__METHOD__, $version, null, 'Agent');
        if ($versionService) {
            $response = $versionService->bootstrap($request->all());
            return AjaxCallbackMessage($response['message'], $response['status']);
        }
        return AjaxCallbackMessage('api接口不再维护', false);
    }
}

