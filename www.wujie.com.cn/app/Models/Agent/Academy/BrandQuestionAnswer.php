<?php namespace App\Models\Agent\Academy;

use Illuminate\Database\Eloquent\Model;

class BrandQuestionAnswer extends Model
{
    protected $table = 'brand_question_answer';

    const ENABLE_TYPE = 1;  //启用状态

    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 获取问答相关数据信息
     */
    public function gainAnswerDatasLists($param)
    {
        $result = self::where([
            'brand_id' => $param['brand_id'],
            'status'   => self::ENABLE_TYPE
        ])
         ->orderBy('sort', 'desc')
         ->orderBy('created_at', 'desc')
         ->get();

        //对结果进行处理
        if (is_object($result)) {
            return $result;
        } else {
            return null;
        }
    }
}