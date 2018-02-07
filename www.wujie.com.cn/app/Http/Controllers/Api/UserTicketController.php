<?php

/**
 * 用户票券
 */

namespace App\Http\Controllers\Api;

use App\Models\Passbook;
use App\Models\User\Entity;
use App\Models\User\Ticket;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Api\CommonController;
use DB,
    Auth;
use App\Models\Activity\Entity as Activity;
use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Structure;
use Passbook\Type\EventTicket;
use Passbook\Type\StoreCard;
use App\Models\Activity\Ticket as ActivityTicket;

class UserTicketController extends CommonController
{
    /**
     * @param Request $request
     * @return string
     * 门票详情
     */
    public function postDetail(Request $request)
    {
        $id = $request->input('id');
        if (empty($id))
            return AjaxCallbackMessage('参数有误', false);
        $where['ut.id'] = $id;
        $type = self::getTicketType($id);
        $data = Ticket::getDetail($where,$type);
        return AjaxCallbackMessage($data, true);
    }

    /**
     * @param Request $request
     * @return string
     * 删除门票
     */
    public function postDelete(Request $request)
    {
        $id = $request->input('id');
        $uid = $request->input('uid');
        if (!Entity::checkAuth($uid))
            return AjaxCallbackMessage('账号异常', false);
        Ticket::where(array('id' => $id))->update(array('status' => -4));
        return AjaxCallbackMessage('票券删除成功', true);
    }

    /**
     * @param Request $request
     * 生成passbook
     */
    public function postPassbook(Request $request)
    {
        $ticket_id = (int)$request->input('ticket_id');
        $uid = (int)$request->input('uid');
        if (!$uid || !($user_ticket = Ticket::getRow(compact('uid') + ['status' => 1, 'id' => $ticket_id])) || !($ticket = ActivityTicket::find($user_ticket->ticket_id))) {
            return AjaxCallbackMessage('参数异常', false);
        }
        $activity = Activity::find($ticket->activity_id);
        $maker = \App\Models\Maker\Entity::find($user_ticket->maker_id);
        \ComposerAutoloaderInit1d61f2687b3a9ac9c62e9d8f33170b5e::getLoader()
            ->set('Passbook\\', app_path('/Http/Libs/passbook/src'));
        $name = 'user_activity_ticket_' . $ticket_id;
//        $pass = new StoreCard($name, $activity->subject);
        $pass = new EventTicket($name, $activity->subject);
        $pass->setBackgroundColor('rgb(255, 255, 255)');
        $pass->setLogoText('无界商圈');
        $pass->setForegroundColor('rgb(255, 188, 168)');
        $pass->setLabelColor('rgb(166, 166, 166)');
        //add logo
        $logo = new Image(public_path('images/dock-logo.png'), 'logo');
        $pass->addImage($logo);
        //add icon
        $icon = new Image(public_path('images/dock-logo.png'), 'icon');
        $pass->addImage($icon);
        //add background
        $image_path = public_path($activity->list_img);
        if (!file_exists($image_path) || !is_file($image_path)) {
            return AjaxCallbackMessage('活动无主图，生成失败！', false);
        }
        $image_string = file_get_contents(public_path($activity->list_img));
        $img = @imagecreatefromstring($image_string);
        if (!$img) {
            return AjaxCallbackMessage('活动主图异常，生成失败！', false);
        }
        $file_path = storage_path('app/' . uniqid(md5($activity->list_img)) . '.png');
        imagepng($img, $file_path);
        $background = new Image($file_path, 'strip');
        $pass->addImage($background);
        // Create pass structure
        $structure = new Structure();

        $primary = new Field('maker-zone', $activity->subject . '—' . $maker->zone->name);
//        $structure->addPrimaryField($primary);
        $structure->addSecondaryField($primary);

        // Add auxiliary field
        $datetime = new Field('datetime', Activity::formatTime($activity->begin_time, $activity->end_time));
        $datetime->setLabel('时间');
        $structure->addAuxiliaryField($datetime);
        // Add auxiliary field
        $site = new Field('site', $maker->subject);
        $site->setLabel('地点');
        $structure->addAuxiliaryField($site);
        // Set pass structure
        $pass->setStructure($structure);

        // Add barcode
        $barcode = new Barcode(Barcode::TYPE_QR, 'ticketinfo:ticket_no=' . $user_ticket->ticket_no);
        $barcode->setAltText('免费');
        $pass->setBarcode($barcode);


        define('P12_FILE', app_path('/Http/Libs/passbook/tests/path/to/p12/Certificate.p12'));
        define('WWDR_FILE', app_path('/Http/Libs/passbook/tests/path/to/AppleWWDRCA.pem'));

        // Create pass factory instance
        $factory = new PassFactory(config('app.PASS_TYPE_IDENTIFIER'), config('app.TEAM_IDENTIFIER'), config('app.ORGANIZATION_NAME'), P12_FILE, config('app.P12_PASSWORD'), WWDR_FILE);
        $public_pkass_path = '/attached/file/pkass/';
        $factory->setOutputPath(public_path($public_pkass_path));
        $factory->package($pass);
        $file = $request->getUriForPath($public_pkass_path . $name . '.pkpass');
        unlink($file_path);
        return AjaxCallbackMessage($file, true) ;
    }
    /*
    * 作用:获取门票类型
    * 参数:
    *
    * 返回值:1购买票 0 赠票
    */
    public static function getTicketType($id)
    {
        $ticket = DB::table('user_ticket')
            ->where('id',$id)
            ->first();
        return $ticket->order_id == 0 ? 0 :1;
    }
}
