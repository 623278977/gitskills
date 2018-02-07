<?php  namespace App\Models\User;

use App\Models\Categorys;
use Illuminate\Database\Eloquent\Model;

class UserFondCate extends Model
{
    protected $table = 'user_fond_cate';

    /**
     * 关联：分类表
     */
    public function hasOneCategorys()
    {
        return $this->hasOne(Categorys::class, 'id', 'cate_id');
    }

    //关联分类表
    public function categorys()
    {
        return $this->hasOne(Categorys::class, 'id', 'cate_id');
    }
}