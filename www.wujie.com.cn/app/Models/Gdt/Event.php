<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Gdt;

use Illuminate\Database\Eloquent\Model;
use \DB, Closure, Input;
use Validator;


class Event extends Model
{

    protected $dateFormat = 'U';
    protected $connection = 'razor';
    protected $primaryKey = 'event_id';

    protected $table = 'event_defination';

    public function eventdata()
    {
        $startdate = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $enddate = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        return $this->belongsToMany(Clientdata::class, 'eventdata', 'event_id', 'deviceid')
            ->select('clientdata.imei', 'eventdata.deviceid', 'clientdata.platform', 'eventdata.clientdate')
            ->where('eventdata.clientdate', '>=', $startdate)
            ->where('eventdata.clientdate', '<', $enddate);
    }

}