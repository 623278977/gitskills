<?php
namespace App\Services\Version\Agent\AgentTiro;

use App\Services\Version\VersionSelect;
use DB, Input;
use App\Models\Agent\AgentTiro;

class _v010001 extends VersionSelect
{
    public function postLists($param)
    {
        $parent_id=$param['request']->input('id', 0);
        $type=$param['request']->input('type', 1);
        if($type==2){
            $result=AgentTiro::where('id',$parent_id)->select('id', 'question', 'answer','type')->first();
//            foreach ($result as &$value){
//                $value=htmlspecialchars_decode($value);
//            }
            $data=[
                'jump'=>0,
                'title'=>$result->question,
                'lists'=>$result,
            ];
            return ['message'=>$data, 'status'=> true];
        }
        $lists=AgentTiro::where('parent_id', $parent_id)
            ->where('status', 1)
            ->select('id','question', 'type')
            ->orderBy('sort', 'desc', 'created_at','desc')
            ->get();
        $title=AgentTiro::where('id',$parent_id)->select('question')->first();
        $data=[
            'jump'=>1,
            'title'=>$title ? $title->question : '',
            'lists'=>$lists
        ];
        return ['message'=>$data, 'status'=> true];
    }

    public function search()
    {
        
    }
}