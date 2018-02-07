<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/11/6 0006
 * Time: 17:17
 */
namespace App\Services\Version\Agent\Brand;

use App\Models\Agent\AgentAchievementLog;
use App\Models\Agent\Contract;
use App\Services\Version\VersionSelect;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentBrandLog;
use DB, Input;
use App\Models\Brand\Entity as BrandModel;
use App\Models\Brand\Entity\V020800 as BrandAgent;
use App\Models\Video\Entity\AgentVideo as VideoAgent;
use App\Models\News\Entity\AgentNews as NewsAgent;
use App\Models\Agent\AgentBrand as AgentBtandModel;
use App\Models\Agent\AgentCustomer;

class _v010003 extends _v010000
{
    /**
     * 品牌专栏列表
     * @User yaokai
     * @param array $input
     * @return array
     */
    public function postColumnList($input = [])
    {
        $page_size = Input::input('page_size', 10);

        $builder = BrandModel::where('agent_status', '1')->where('status', 'enable');//经纪人端只显示agent_status为1的品牌

        $builder = AgentBrand::selectList($builder);

        //条件搜索
//        $builder = BrandModel::brandList($builder, $input);

        //根据结果构建分页结果
        $result = paginator($builder);

        //格式化数据
        $data = BrandAgent::format($result, $input);

        return ['message' => $data, 'status' => true];
    }





}