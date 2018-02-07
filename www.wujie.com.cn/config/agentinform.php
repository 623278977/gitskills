<?php

//发送通知设置
return [
    'agentinform' => [
        'sign'               => App\Models\Agent\Activity\Sign::class,
        'Agent_currency_Log' => App\Models\Agent\AgentCurrencyLog::class,
       // 'agent_customer'     => App\Models\Agent\AgentCustomer::class,
        'agent_customer'     => App\Models\Agent\AgentCustomerLog::class,
    ],
];