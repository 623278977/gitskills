<?php
/**
 * 活动（游戏）
 *
 * @author Administrator
 *
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameWinners extends Model
{
    protected $table = 'game_winners';
    
    protected $fillable = array('prize_id','address','tel','name','uid','non_reversible');
    
    protected function getDateFormat()
    {
    	return date(time());
    }
}
