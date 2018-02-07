<?php

/*
 * 城市合伙人
 */

namespace App\Models\Agent;


use Illuminate\Database\Eloquent\Model;
class ContractCommissionLevel extends Model {

    protected $table = 'contract_commission_level';
    protected $dateFormat = 'U';

    protected  $guarded = [];
    public static $_RULES = [//验证规则

    ];
    public static $_MESSAGES = [//验证字段说明

    ];
    public static $_STATUS = [
        -1 => '禁用',
        0 => '未激活',
        1 => '正常'
    ];



}
