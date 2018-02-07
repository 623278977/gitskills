<?php
/**
 * 短信记录
 * @author Administrator
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSms extends Model
{
    protected $table = 'log_sms';

    protected $fillable = array('uid', 'ip', 'client_id', 'type', 'content', 'phone', 'status', 'platform', 'app_name', 'nation_code', 'non_reversible');

    protected function getDateFormat()
    {
        return date(time());
    }
}
