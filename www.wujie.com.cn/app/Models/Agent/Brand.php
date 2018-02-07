<?php
namespace App\Models\Agent;


use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = "brand";
    public function belongToCategory(){
        return $this->belongsTo('App\Models\Agent\Categorys', 'categorys1_id', 'id');
    }



}
