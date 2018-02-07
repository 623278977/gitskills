<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use App\Http\Controllers\Api\BrandController;
use App\Models\Distribution\Entity as Distribution;
use App\Models\Distribution\Action;
use App\Models\User\Favorite;
use App\Models\User\Industry;
use App\Models\Zone;
use App\Services\Version\Brand\_v020400;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use \DB , Closure ,Input;
use App\Models\News\Entity as News;
use Passbook\Type\StoreCard;
use App\Models\Agent\BrandChapter;
use App\Models\Agent\BrandAgentCompleteQuiz;
use App\Models\Agent\AgentBrand;

class Entity extends Model
{

    public static  $instance = null;
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    protected $dateFormat = 'U';

    protected $table = 'brand';



    //黑名单
    protected $guarded = [];




    //代理方式
    public static $_AGENCY_WAY = [
        2 => '品牌加盟',
        4 => '渠道加盟',
        6 => '品牌加盟、渠道加盟',
    ];

    public function zone()
    {
        return $this->hasOne('App\Models\Zone\Entity', 'id', 'zone_id');
    }

    public function favorite($uid = 0)
    {
        if ($uid) {
            return $this->hasMany('App\Models\User\Favorite', 'post_id')->where(
                array(
                    'model' => 'brand',
                    'uid'   => $uid
                )
            );
        } else {
            return $this->hasMany('App\Models\User\Favorite', 'post_id')->where(
                array(
                    'model' => 'brand'
                )
            );
        }
    }

    //关联品牌章节表
    public function brand_chapter(){
        return $this->hasMany(BrandChapter::class,'brand_id','id');
    }


    //大分类
    public function categorys1()
    {
        return $this->hasOne('App\Models\Categorys', 'id', 'categorys1_id');
    }


    //小分类
    public function categorys2()
    {
        return $this->hasOne('App\Models\Categorys', 'id', 'categorys2_id');
        //                                           关联的id   本表的id
    }


    //视频
    public function video()
    {
        return $this->hasMany('App\Models\Video', 'brand_id','id');
        //                                         关联表的id  本表的id
    }

    //门店
    public function store()
    {
        return $this->hasMany(BrandStore::class, 'brand_id', '');
    }

    //品牌商务代表 目前是一对一
    public function contactor()
    {
        return $this->hasOne(Contactor::class, 'brand_id', 'id');
    }


    /**
     * 作用:单纯的获得品牌
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function single($id, $with_small_cat = 0)
    {
        $brand = self::with(['zone'=>function($query){ $query->select('id','name');}])
            ->with(['categorys1'=>function($query){ $query->select('id','name');}])
            ->select(
                'zone_id',
                'categorys1_id',
                'name',
                'logo',
                'id',
                'prerequisite',
                'advantage',
                'league',
                'is_recommend',
                'issuer',
                'is_hot',
                'treaty',
                'investment_min',
                'investment_max',
                'shops_num',
                'company',
                'address',
                'is_auth',
                'fund',
                'sham_click_num as click_num',
                'distribution_id',
                'distribution_deadline',
                'summary',
                'introduce',
                'superiority',
                'supply',
                'qrcode',
                'favor_count',
                'agent_status', //todo 增加经纪人状态查询 zhaoyf 2017-12-21 16:43
                'commission_des',//提成说明
                'condition',//代理条件
                'league', 'keywords', 'shops_num', 'tags', 'products','details','created_at','slogan'
                ,'share_num','brand_summary','rebate','share_summary','agency_way'
                )
            ->addselect(DB::raw('(select IF(cl.push_money_type=1, max(cl.commission), max(cl.scale*b.amount)) from lab_commission_level cl LEFT JOIN 
            (SELECT brand_id,max(amount) amount from lab_brand_contract where is_delete=0 and type=1 GROUP BY brand_id) b on b.brand_id = cl.brand_id where cl.brand_id = lab_brand.id ) AS max_amount'))
            ->addselect(DB::raw('(select scale from lab_commission_level as cl where cl.brand_id = lab_brand.id order by scale desc limit 0,1) as max_percent'))
            ->where('id', $id)
            ->where('status', 'enable')
            ;

        if($with_small_cat){
            $brand->addSelect('categorys2_id')->with(['categorys2'=>function($query){$query->select('id','name');}])->first();
        }
        $brand = $brand->first();
        $reward = Action::where('distribution_id', $brand->distribution_id)->where('action', 'share')->first();

        if(is_object($reward)){
            $brand->share_reward_unit = $reward->give;
            $brand->share_reward_num = $reward->trigger;
        }else{
            $brand->share_reward_unit = 'none';
            $brand->share_reward_num = 0;
        }
        $brand->syncOriginal();

        $brand = self::deal($brand, 0, $with_small_cat);

        return $brand;
    }

    /**
     * 作用:批量的获得品牌
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function singles()
    {
        $query = self::with(['zone'=>function($query){ $query->select('id','name');}])
            ->with(['categorys1'=>function($query){ $query->select('id','name');}])
            ->select('zone_id', 'categorys1_id');

        return $query;
    }

    /**
     * 作用:批量的加工
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function process($collect, $uid=0)
    {
        if($collect instanceof Collection){
            foreach($collect as $k=> &$v){
                self::deal($v, $uid);
            }
        }

        if($collect instanceof Model){
            $collect = self::deal($collect, $uid);
        }

        return $collect;
    }


    /**
     * 作用:获取品牌的 资质图片
     * 参数:$id 品牌id
     *
     * 返回值:
     */
    public static function qualifyImages($id)
    {
        $self = self::select('business_licence', 'tax_registration', 'organization')->where('id', $id)->first();
        $qcds = Images::where('brand_id', $id)->where('type', 'qcds')->lists('src');
        foreach($self->attributes as $k=>$v){
            if($v){
                $self->$k = getImage($v, 'activity', '', 0);
            }else{
                unset($self->$k);
            }
        }
        $qcds_image = [];
        foreach($qcds->toArray() as $k=>$v){
            $qcds_image[] = getImage($v, 'activity', '', 0);
        }

        $self->qcds_image = $qcds_image;


        return $self;
    }


    /**
     * 作用:单条的处理
     * 参数:$collect 品牌
     *
     * 返回值:
     */
    public static function deal($collect, $uid=0, $with_small_cat = 0)
    {
        if($collect instanceof Model){
            if(isset($collect->zone->name )){
                $collect->zone_name = str_replace('市', '市', $collect->zone->name);
            }
            if(isset($collect->categorys1->name)){
                $collect->category_name = $collect->categorys1->name;
            }

            if($with_small_cat &&  isset($collect->categorys2->name)){
                $collect->category_name .= ' - '.$collect->categorys2->name;
            }

            if(isset($collect->keywords )){
                $collect->keywords = ($collect->keywords?explode(' ', $collect->keywords):[]);
            }
            if(isset($collect->logo )){
                $collect->logo = getImage($collect->logo,'activity', '', 0);
            }
            if(isset($collect->investment_max)){
                $collect->investment_max = abandonZero($collect->investment_max);
            }
            if(isset($collect->investment_min)){
                $collect->investment_min = abandonZero($collect->investment_min);
                $collect->investment_arrange = $collect->investment_min.' ~ '.$collect->investment_max.'万';
            }
            if(isset($collect->tags)){
                $collect->tags ?$collect->tags = explode(' ', $collect->tags):$collect->tags=[];
            }
            if(isset($collect->products)){
                $collect->products ?$collect->products = explode(' ', $collect->products):$collect->products=[];
            }

            if(isset($collect->summray)){
                $collect->summray = strip_tags($collect->summray);
            }

            if(isset($collect->introduce)){
                $collect->introduce = strip_tags($collect->introduce);
            }


            if(isset($collect->brand_summary)){
                $collect->introduce = strip_tags($collect->introduce);
            }


            if(isset($collect->details)){
                $collect->detail = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','',$collect->details));
                if(!$collect->brand_summary){
                    $collect->brand_summary = extractText($collect->detail);
                }
                unset($collect->details);
            }


            $collect->isRoadShow = self::isRoadShow($collect);
            $collect->canJoin = self::canJoin($collect);
            if($uid){
                //是否已收藏
                $favorite = Favorite::getRow(['uid'=>$uid, 'model'=>'brand','status'=>1, 'post_id'=>$collect->id]);
                $collect->is_favorite = is_object($favorite) ?1:0;
            }

            unset($collect->zone, $collect->categorys1);
        }else{
            return false;
        }

        return $collect;
    }


    /**
     * 作用:判断某个品牌是否已经参加过品牌招商会
     * 参数:$collect 品牌
     *
     * 返回值:
     */
    public static function isRoadShow($brand)
    {
        $isRoadShow = DB::table('activity_brand')
            ->leftJoin('activity', 'activity_brand.activity_id', '=', 'activity.id')
            ->where('activity.begin_time', '<', time())
            ->where('activity.status', 1)
            ->where('activity_brand.brand_id', $brand->id)
            ->first();

        return is_object($isRoadShow)?1:0;
    }


    /**
     * 作用:判断某个品牌是否已经参加过品牌招商会
     * 参数:$collect 品牌
     *
     * 返回值:
     */
    public static function canJoin($brand)
    {
        $canJoin = DB::table('brand_goods')
            ->where('brand_goods.status', 'allow')
            ->where('brand_goods.brand_id', $brand->id)
            ->first();

        return is_object($canJoin)?1:0;
    }

    /*
     * 列表基类
     */
    static function baseLists($type, array $param ,Closure $callback ,Closure $format ,$pageSize = NULL)
    {
        $builder = self::where('status','enable');
        if($callback){
            $builder = $callback($builder);
        }
        $builder = self::$type($builder,$param);
        if($type == 'guessYouLike'){
            $industy_ids = DB::table('user_industry')->where('uid',$param['uid'])->select('industry_id')->get();
            if(count($industy_ids)>0){
                $industy_ids = array_flatten(objToArray($industy_ids));
                $industy_ids = implode(',',$industy_ids);
            }else{
                $industy_ids = 0;
            }
            $data = $builder
                ->addSelect(DB::raw('(select distinct brand_id from lab_brand_industry a WHERE a.industry_id in ('.$industy_ids.') and a.brand_id = lab_brand.id ) as industry'))
                ->orderBy('industry','desc')
                //->orderBy('click_num','desc')
                ->get();

        }else{
            $data = $pageSize ? $builder->paginate($pageSize) :$builder->get();
            $data->dataCount = $builder->count();
        }

        if(count($data)>0 && $format){
            $data = $format($data);
        }
        if($type == 'guessYouLike'){
            $data = self::fillGuessYouLike($data);
        }

        return $data;
    }

    /*
     * 品牌列表条件筛选
     */
    static public function brandList($builder, array $param = []){
        if(isset($param['hotwords']) && !empty($param['hotwords'])){
            $builder->where(function($query) use($param){
                $query->where('name', 'like', '%' . $param['hotwords'] . '%')
                    ->orWhere('keywords', 'like', '%' . $param['hotwords'] . '%')
                    ->orWhere('introduce', 'like', '%' . $param['hotwords'] . '%');
            });
        }
        if(isset($param['keywords']) && !empty($param['keywords'])){
            $builder->where('name','like','%'.$param['keywords'].'%');
        }
        if(isset($param['keyword']) && !empty($param['keyword'])){
            $builder->where('name','like','%'.$param['keyword'].'%');
        }
        if(isset($param['uid']) && !empty($param['uid'])){
            $builder->where('uid',$param['uid']);
        }
        if(isset($param['zone_id']) && !empty($param['zone_id'])){
            if(Zone::find($param['zone_id'])->name != '全国'){
                $sonids = Zone::getZoneIds($param['zone_id']);
                $sonids[] = $param['zone_id'];
                $builder->whereIn('zone_id',$sonids);
            }
        }
        if(isset($param['categorys1_id']) && !empty($param['categorys1_id'])){
            $builder->where('categorys1_id',$param['categorys1_id']);
        }
        if(isset($param['categorys2_id']) && !empty($param['categorys2_id'])){
            $builder->where('categorys2_id',$param['categorys2_id']);
        }


        if(isset($param['agency_way']) && !empty($param['agency_way'])){
            $builder->whereRaw('agency_way & '.$param['agency_way'] .' != ' .'0');
        }

        if(isset($param['orderby']) && !empty($param['orderby'])){
            if($param['orderby']=='new'){ // 最新加入
                $builder->orderBy('created_at','desc');
            }
            if($param['orderby']=='investment_asc'){ // 加盟金额低到高
                $builder->orderBy('investment_min','asc');
            }
            if($param['orderby']=='investment_desc'){ // 加盟金额高到低
                $builder->orderBy('investment_max','desc');
            }
            if($param['orderby']=='hot'){ // 人气最高
                $builder->orderBy('click_num','desc');
            }
            if($param['orderby']=='joined'){ // 已举办招商会
                $builder->whereIn('id',function($query){
                   $query->from('activity_brand')
                       ->select('brand_id')
                       ->get();
                });
            }
            //佣金排序
            if($param['orderby']=='commission_asc'){ // 提成金额低到高
                $builder->orderBy('max_amount','asc');
            }
            if($param['orderby']=='commission_desc'){// 提成金额高到低
                $builder->orderBy('max_amount','desc');
            }
            //加盟人数排序
            if($param['orderby']=='join_num_asc'){//加盟人数由低到高
                $builder->orderBy('contract_num','asc');
            }
            if($param['orderby']=='join_num_desc'){//加盟人数由高到低
                $builder->orderBy('contract_num','desc');
            }


        }
        //投资额
        if(isset($param['investment_min']) && isset($param['investment_max'])){
            if(!empty($param['investment_min']) && !empty($param['investment_max'])){
                $builder->where(function($query) use($param){
                    $query->where('investment_min','>=',$param['investment_min'])->where('investment_min','<',$param['investment_max'])
                        ->orWhere('investment_max','>=',$param['investment_min'])->where('investment_max','<',$param['investment_max']);
                });
            }
        }else if(isset($param['investment_max']) && !empty($param['investment_max'])){
            $builder->where(function($query) use($param){
                $query->where('investment_min','>=',0)->where('investment_min','<',$param['investment_max'])
                    ->orWhere('investment_max','>=',0)->where('investment_max','<',$param['investment_max']);
                ;
            });
        }else if(isset($param['investment_min']) && !empty($param['investment_min'])){
            $builder->where('investment_max','>',$param['investment_min'].'.00');
        }

        //佣金提成
        if (isset($param['commission_min']) && isset($param['commission_max'])) {
            if (!empty($param['commission_min']) && !empty($param['commission_max'])) {
                $builder->having('max_amount', '>=', $param['commission_min'])->having('max_amount', '<=', $param['commission_max']);
            }
        } else if (isset($param['commission_min']) && !empty($param['commission_min'])) {
            $builder->having('max_amount', '>=', $param['commission_min']);
        } else if (isset($param['commission_max']) && !empty($param['commission_max'])) {
            $builder->having('max_amount', '<=', $param['commission_max']);
        }

        //佣金比例筛选
        if (isset($param['percent_min']) && isset($param['percent_max'])) {
            if (!empty($param['percent_min']) && !empty($param['percent_max'])) {
                $builder->having('max_percent', '>=', $param['percent_min']/100)->having('max_percent', '<=', $param['percent_max']/100);
            }
        } else if (isset($param['percent_min']) && !empty($param['percent_min'])) {
            $builder->having('max_percent', '>=', $param['percent_min']/100);
        } else if (isset($param['percent_max']) && !empty($param['percent_max'])) {
            $builder->having('max_percent', '<=', $param['percent_max']/100);
        }


        $builder->orderBy('sort','desc');
        $builder->orderBy('is_recommend','asc');
        $builder->orderBy('is_hot','asc');
        if($param['orderby']!='hot'){
            $builder->orderBy('click_num','desc');
        }
        return $builder;
    }

    /*
     * 品牌推荐
     */
    static private function recommend($builder){
        $builder->where('is_recommend','yes');
        $builder->orderBy('sort','desc');
        //$builder->orderBy('is_hot','asc');
        //$builder->orderBy('click_num','desc');
        return $builder;
    }

    /*
     *猜你喜欢
     */
    static private function guessYouLike($builder, array $param = [])
    {
        //用户的行业,所在地
        $uid = Auth::id()?:$param['uid'];
        $zone_id = \App\Models\User\Entity::find($uid)->zone_id;
        $zone_upid = Zone::find($zone_id)->upid;
        $zone_sonid = Zone::getZoneIds($zone_id);
        $industry = Industry::where('uid',$uid)->get();
        $industry_arr = [];//用户行业
        $zone_arr = array_unique(array_merge([$zone_id,$zone_upid],$zone_sonid));//用户所在地
        foreach($industry as $item){
            $industry_arr[] = $item->industry_id;
        }
        //地区和行业
        $builder->where('is_recommend','no')
            ->where('is_hot','no')
            ->whereIn('id',function($query) use($industry_arr){
            $query->from('brand_industry')
                ->whereIn('industry_id',$industry_arr)
                ->select('brand_id')
                ->get();
        })
            ->whereIn('zone_id',$zone_arr)
            //行业
            ->orWhere(function($query) use($industry_arr){
                $query->where('is_recommend','no')
                    ->where('is_hot','no')
                    ->whereIn('id',function($query) use($industry_arr){
                    $query->from('brand_industry')
                        ->whereIn('industry_id',$industry_arr)
                        ->select('brand_id')
                        ->get();
                });
            })
            //地区
            ->orWhere(function($query) use($zone_arr){
                $query->where('is_recommend','no')
                    ->where('is_hot','no')
                    ->whereIn('zone_id',$zone_arr);
            })
            //->orderBy('sort','desc')
            //->orderBy('click_num','desc')
            ->distinct();
        //dd($builder->toSql());
        return $builder;
    }

    /*
     * 猜你喜欢补全数据
     */
    static private function fillGuessYouLike($data){
        //数据不满8条, 补全到8条
        $needFill = 8 - count($data);
        $ids = [];
        foreach($data as $item){
            $ids[] = $item->id;
        }
        $fillData = self::where('status','enable')
            ->whereNotIn('id',$ids)
            //->where('is_recommend','no')
            //->where('is_hot','no')
            ->select(
                'id', 'uid', 'logo', 'name', 'investment_min' ,'investment_max','keywords','is_recommend','is_hot','slogan','details as detail',
                DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
                DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
                DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name')
            )
            ->orderBy('click_num','desc')
            ->get();
        $obj = new _v020400();
        foreach($fillData as $item){
            $item->investment_min = formatMoney($item->investment_min);
            $item->investment_max = formatMoney($item->investment_max);
            $item->logo = getImage($item->logo);
            $item->investment_arrange = $item->investment_min . '万-' .$item->investment_max .'万';
            $item->zone_name = $obj->formatZoneName($item->zone_name);
            $item->remark = $obj->getBrandRemark($item->activity_id);
            $item->detail = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#','',$item->detail));
            //$item->industry_ids = $this->getBrandIndustry($item->industry_ids);
            if($item->keywords){
                $item->keywords = strpos($item->keywords,' ')!==FALSE ? explode(' ',$item->keywords) : [$item->keywords];
            }else{
                $item->keywords = [];
            }
        }
        $new = $fillData->toArray();
        $old = count($data)>0?$data->toArray():[];
        $data = array_merge($old,$new);
        $is_recommend = $is_hot = $rest = [];
        foreach($data as &$v){
            if($v['is_recommend'] == 'yes'){
                unset($v['is_recommend']);
                $is_recommend[] = $v;
            }else if($v['is_hot'] == 'yes'){
                unset($v['is_hot']);
                $is_hot[] = $v;
            }else{
                $rest[] = $v;
            }
        }
        //推荐和热门的放在最前面
        if($is_hot){
            foreach($is_hot as $hot){
                array_unshift($rest,$hot);
            }
        }
        if($is_recommend){
            foreach($is_recommend as $recommend){
                array_unshift($rest,$recommend);
            }
        }

        $perPage = Input::get('page_size',8);
        $pageStart = Input::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        // Get only the items you need using array_slice
        $return = array_slice($rest, $offSet, $perPage, TRUE);
        return $return;
    }

    /*
     * 获取我的收藏
     */
    static function getFavoriteData($data){
        $return = [];
        $brand = new _v020400();
        foreach($data as $k=>$item){
            $return[$k]['id'] = $item->id;
            $return[$k]['uid'] = $item->uid;
            $return[$k]['logo'] = getImage($item->logo);
            $return[$k]['name'] = $item->name;
            $return[$k]['slogan'] = $item->slogan;
            $return[$k]['investment_min'] = formatMoney($item->investment_min);
            $return[$k]['investment_max'] = formatMoney($item->investment_max);
            $return[$k]['investment_arrange'] = $item->investment_min . '万-' .$item->investment_max .'万';
            $return[$k]['keywords'] = empty($item->keywords) ? [] : (strpos($item->keywords,' ')!==FALSE ? explode(' ',$item->keywords) : [$item->keywords]);
            //$return[$k]['zone_name'] = isset($item->zone)?$brand->formatZoneName($item->zone_name):'';
            //$return[$k]['zone_name'] = isset($item->zone)?$item->zone->name:'';
            $zone_name = ($res = DB::table('zone')->where('id',$item->zone_id)->first())?$res->name:'';
            if($zone_name){
                if(strpos($zone_name,'市')){
                    $zone_name = str_replace('市','',$zone_name);
                }
            }
            $return[$k]['zone_name'] = $zone_name;
            $return[$k]['activity_id'] = ($res = DB::table('activity_brand')->where('brand_id',$item->id)->select(DB::raw('(group_concat(activity_id)) as ids'))->first())?$res->ids:0;
            //$return[$k]['category_name'] = isset($item->categorys1)?$item->categorys1->name:'';
            $category_name = ($res = DB::table('categorys')->where('id',$item->categorys1_id)->first())?$res->name:'';
            $return[$k]['category_name'] = $category_name;
            $return[$k]['remark'] = $brand->getBrandRemark($item->id);
            $return[$k]['description'] = $item->introduce;
            $return[$k]['collect_time'] = $item->uf_created_at;
            $return[$k]['introduce'] = $item->introduce;
            $return[$k]['is_recommend'] = $item->is_recommend;
//            $return[$k]['detail'] = strip_tags($item->details);
            $return[$k]['detail'] = trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;|\r|\n|\t#','',$item->details));
            //v020700使用
            $return[$k]['brand_summary'] = $item->brand_summary;
            if (empty($return[$k]['brand_summary'])) {
                $return[$k]['brand_summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($item->details))),0,50);
            }
        }
        //dd($return);
        return $return;
    }

    /*
     * 品牌产品信息
     */
    static function productInfo($product_id,$type = ''){
        if($type == 'brand'){
            $table = 'live_brand_goods';
        }elseif($type == 'brand_goods'){
            $table = 'brand_goods';
        }
        $goods = DB::table($table)->where('id',$product_id)->first();
        if(!$goods) return 0;
        $data = self::where('id',$goods->brand_id)
            ->select(
                //'id','name','logo','summary','keywords','status',
                //DB::raw('(select code from lab_live_brand_goods as lbg WHERE  lbg.brand_id = lab_brand.id) as code')
                'id', 'uid', 'logo', 'name', 'investment_min' ,'investment_max','keywords','treaty',
                DB::raw('(select concat_ws(",",id,name) from lab_zone WHERE lab_zone.id = lab_brand.zone_id) as zone_name'),
                DB::raw('(select if(GROUP_CONCAT(activity_id), GROUP_CONCAT(activity_id), 0) from lab_activity_brand WHERE lab_activity_brand.brand_id = lab_brand.id) as activity_id'),
                DB::raw('(select name from lab_categorys as c where c.id = lab_brand.categorys1_id ) AS category_name')
                //DB::raw('(select code from lab_live_brand_goods as lbg WHERE  lbg.brand_id = lab_brand.id) as code')
                )
            ->first();
        if($data){
            $data->code = $goods->code;
            $data->title = $goods->title;
        }

        return $data?:0;
    }

    /*
     * 活动相关品牌
     */
    static function activityBrandList($builder, array $param = [])
    {
        $builder->whereIn('id',function($query) use($param){
            $query->from('activity_brand')
                ->where('activity_id',$param['activity_id'])
                ->select('brand_id')
                ->get();
        });

        return $builder;
    }

    static function getPublicData($obj, $uid=0){

        $data = self::single($obj->id);
        $return = [];
        //获取3天前的时间戳
        $threeTime = time() - 3600 * 24 * 3;

        if($data){
            $return['id'] = $data->id;
            $return['name'] = $data->name;
            $return['logo'] = $data->logo;
            $return['slogan'] = $data->slogan;
            $return['summary'] = preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data->summary)));
            $return['brand_summary'] = preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data->brand_summary)));
            $return['detail'] = preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($data->detail)));
            $return['introduce'] = $data->introduce;
            $return['issuer'] = $data->issuer;
            $return['is_recommend'] = 0;
            $return['rebate'] = Distribution::Integer($data->rebate);
            $return['keywords'] = $data->keywords;

            $return['is_distribution'] = Distribution::IsDeadline($data->distribution_id,$data->distribution_deadline);//分销是否失效
            $return['share_score'] = Distribution::shareScore($data->distribution_id);//分销分享得的积分

            //是否最新
            if (strtotime($data->created_at) > $threeTime) {
                $return['is_new'] = 1;//最新
            } else {
                $return['is_new'] = 0;//非最新
            }
            //是否最热
            $return['is_hot'] = $data->is_hot == 'yes'?'1':'0';

            //品牌描述
            if (empty($return['brand_summary'])) {
                $return['brand_summary'] = mb_substr($return['detail'], 0, 50) . '...';
            }

            if($data->is_recommend == 'yes' || $data->is_hot == 'yes'){
                $return['is_recommend'] = 1;
            }

            if(!$return['is_recommend']){
                if(time() - (is_object($data->created_at)?$data->created_at->getTimestamp():$data->created_at) < 30*24*3600){
                    $return['is_new'] = 1;
                }
            }

            if(!$uid){
                $return['url'] = createUrl('brand/detail', array('id' => $obj->id));
                $return['share_reward_num'] = Action::getDistributionByAction('brand', $obj->id, 'share')->trigger;
                $return['share_reward_unit'] = Action::getDistributionByAction('brand', $obj->id, 'share')->give;
            }else{
                $return['url'] = createUrl('brand/detail', array('id' => $obj->id, 'share_mark' => makeShareMark($obj->id, 'brand', $uid)));
            }


        }

        return $return;
    }

    /**
     * 为首页获取品牌列表数据
     */
    static function getPubliclistData($uid=0)
    {
        $data = self::where('status','enable')
            ->orderBy(\DB::raw('RAND()'))
            ->take(4)
            ->get();
        $brand_list = [];
        foreach ($data as $key=>$value){
            $brand_list[$key]['id'] = $value->id;
            $brand_list[$key]['name'] = $value->name;
            $brand_list[$key]['slogan'] = $value->slogan;
            $brand_list[$key]['logo'] = getImage($value->logo);
            $brand_list[$key]['brand_summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($value->brand_summary))),0,50);
            if(empty($brand_list[$key]['brand_summary'])){
                $brand_list[$key]['brand_summary'] = mb_substr(preg_replace('/\s+/i','',str_replace('&nbsp;','',strip_tags($value->details))),0,50);
            }
            $brand_list[$key]['categorys1'] = $value->categorys1->name;
            $brand_list[$key]['categorys2'] = $value->categorys2->name;
            $brand_list[$key]['investment_min'] = Distribution::Integer($value->investment_min);
            $brand_list[$key]['investment_max'] = Distribution::Integer($value->investment_max);
//            $brand_list[$key]['issuer'] = $value->issuer;
//            $brand_list[$key]['is_recommend'] = 0;
//            $brand_list[$key]['is_new'] = 0;

//            if($value->is_recommend == 'yes' || $value->is_hot == 'yes'){
//                $brand_list[$key]['is_recommend'] = 1;
//            }
//
//            if(!$brand_list[$key]['is_recommend']){
//                if(time() - (is_object($value->created_at)?$value->created_at->getTimestamp():$value->created_at) < 30*24*3600){
//                    $brand_list[$key]['is_new'] = 1;
//                }
//            }

            if(!$uid){
                $brand_list[$key]['url'] = createUrl('brand/detail', array('id' => $value->id));
                $brand_list[$key]['share_reward_num'] = Action::getDistributionByAction('brand', $value->id, 'share')->trigger;
                $brand_list[$key]['share_reward_unit'] = Action::getDistributionByAction('brand', $value->id, 'share')->give;
            }else{
                $brand_list[$key]['url'] = createUrl('brand/detail', array('id' => $value->id, 'share_mark' => makeShareMark($value->id, 'brand', $uid)));
            }
        }

        return $brand_list;
    }

    /*
    * 获取招商现场视频列表
    * 该方法经纪人端专用
    *
    * */
    public static function getViedoList($page,$pageCount,$hotwords){
        $data = [ 'brand'=>[] , 'count'=>[] ];
        $brandListArr=self::with(['video'=>function($query){
            $query->where('agent_status',1)->orderBy('created_at','desc');
        }])
        ->where('agent_status',1)
        ->where('name','like','%'.$hotwords.'%')
        ->get()->toArray();
        $brandList = collect($brandListArr)->filter(function($item){
            return !empty($item['video']);
        })
        ->sortByDesc(function($item){
            return $item['video'][0]['created_at'];
        })
        ->forPage($page,$pageCount)->toArray();
        $count = 0;
        foreach ($brandList as $oneBrand){
            $arr = [];
            $arr['name'] = trim($oneBrand['name']);
            foreach ($oneBrand['video'] as $oneVideo){
                $arr['videos'][] = array(
                    'id' => trim($oneVideo['id']),
                    'image' => getImage($oneVideo['image'],'video',''),
                    'title' => trim($oneVideo['subject']),
                    'brand_id' => trim($oneBrand['id']),
                    'brand_title' => trim($oneBrand['name']),
                    'created_at' => trim($oneVideo['created_at']),
                    'is_recommend' => trim($oneVideo['is_recommend']),
                );
                $count++;
            }
            $data['brand'][] = $arr;
        }
        $data['count'] = trim($count);
        return $data;
    }


    /*
     * 获取品牌章节列表
     * shiqy
     * */
    public static function getBrandChapterList($brandId,$agentId ,$videoId){
        $brandInfo = self::with(['brand_chapter.brand_course.brand_video'=>function($query){
            $query->select('id' , 'subject');
        }])
            ->with(['brand_chapter.brand_course.news'=>function($query){
                $query->select('id' , 'title');
            }])
            ->with(['brand_chapter.brand_course'=>function($query){
                $query->where('status' , 1);
            }])
            ->with(['brand_chapter'=>function($query){
                $query->orderBy('sort','desc');
                $query->where('status',1);
            }])
            ->where('id',$brandId)->where('status','enable')
            ->where('agent_status',1)
            ->first();
        if(!is_object($brandInfo)){
            return ['message'=>'品牌数据不存在','error'=>1];
        }

        $brandInfo = $brandInfo->toArray();
        $data = [];
        $data['brand_name'] = trim($brandInfo['name']);
        $data['brand_slogan'] = trim($brandInfo['slogan']);
        $data['completeness'] = BrandChapter::getChapterCompleteness($brandId,$agentId);
        $data['is_complete'] = '0';
        intval($data['completeness']) == 1 && $data['is_complete'] = '1';
        //获取已完成的品牌视频和资讯
        $completeContents = BrandAgentCompleteQuiz::allCompleteContent($agentId);
        $completeVideos = $completeContents['video'];
        $completeNews = $completeContents['news'];
        $data['chapter'] = [];
        foreach ($brandInfo['brand_chapter'] as $key => $oneChapter){
            $arr = [];
            $chapterNum = $key + 1;
            $arr['chapter_num'] = "第".num2char($chapterNum,false)."章";
            $arr['name'] = trim($oneChapter['title']);
            $arr['content'] = [];

            //章内排序
            $chapterInfo = collect($oneChapter['brand_course'])->sortByDesc('sort')->values()->toArray();

            foreach ($chapterInfo as $contentKey => $oneContent){
                $contentArr = [];
                $cotentNum = $contentKey + 1;
                $contentArr['cotent_num'] = $chapterNum.'.'.$cotentNum;
                $contentArr['id'] = trim($oneContent['post_id']);
                $contentArr['is_complete'] = '0';
                $contentArr['is_curr'] = '0';
                if($oneContent['type'] == 1){
                    $contentArr['type'] = 'video';
                    $contentArr['title'] = trim($oneContent['brand_video']['subject']);
                    in_array($oneContent['post_id'],$completeVideos) && $contentArr['is_complete'] = '1';
                    $oneContent['post_id'] == $videoId && $contentArr['is_curr'] = '1';
                }
                else{
                    $contentArr['type'] = 'article';
                    $contentArr['title'] = trim($oneContent['news']['title']);
                    in_array($oneContent['post_id'],$completeNews) && $contentArr['is_complete'] = '1';
                }
                $data['completeness'] == '1' && $contentArr['is_complete'] = '1';
                $arr['content'][] = $contentArr;
            }
            $data['chapter'][] = $arr;
        }
        return $data;
    }

    //获取品牌的加盟方式
    public function agentWay(){
        return [
            'single'=>sprintf("%'03s" , decbin($this->agency_way))[2],
            'area'=>sprintf("%'03s" , decbin($this->agency_way))[1],
            'channel'=>sprintf("%'03s" , decbin($this->agency_way))[0],
        ];
    }

    public static function _agentWay($agentWay){
        return [
            'single'=>sprintf("%'03s" , decbin($agentWay))[2],
            'area'=>sprintf("%'03s" , decbin($agentWay))[1],
            'channel'=>sprintf("%'03s" , decbin($agentWay))[0],
        ];
    }





}