<?php namespace App\Models\Agent\TraitBaseInfo;

//融云消息发送
trait RongCloud
{
    /**
     * 消息类型
     */
    protected static $_info_type = [
        'text'   => 'RC:TxtMsg',        //文本消息
        'img'    => 'RC:ImgMsg',        //图片消息
        'in'     => 'RC:VcMsg',         //语音消息
        'i_t'    => 'RC:ImgTextMsg',    //图文消息
        'place'  => 'RC:LBSMsg',        //位置消息
        'file'   => 'RC:FileMsg',       //文件消息
        'notice' => 'RC:InfoNtf',       //提示条（小灰条）通知消息
        'custom' => 'TY:TipMsg',        //自定义的消息类型
    ];

    /**
     * 发送的消息方式
     */
    protected static $_my_type = [
        'my'   => 'custom',          //消息方式
        'true' => true,
    ];

    /**
     * 发送消息形式：单向发送双向发送
     * 发送人区分
     */
    protected static $_send_person_type = [
        'agent' => 'one_agent',
        'user'  => 'one_user',
        'er'    => 'bothway',
    ];

    /**
     * author zhaoyf
     *
     * 经纪人获取投资人系列数据信息的消息发送（所看的投资人的数据不一定都是当前经纪人的）
     *
     * @param $param           参数集合：     数据集合，包含消息发送需要的发送者和接受的ID
     * @param $info_filed_type 消息的字段类型：就是在资源文件里定义的消息字段类型，数组和字符串两种形式
     * @param $info_type       发送消息类型：  自定义类型和融云自带的消息类型
     * @param $info_way        消息方式：     是双向发送还是单向发送，需不需要带头像等
     * @param null $send_person_type         发送对象人区分
     * @return null
     */
    public static function gatherInfoSends($param, $info_filed_type, $info_type, $info_way, $send_person_type = null)
    {
        //对传递字段类型进行处理
        if (is_string($info_filed_type)) {
            $send_datas = trans('tui.' . $info_filed_type, $param[2]);
            if ($info_way == 'my') {
                $send_data = ['content' => $send_datas];
            } else {
                $send_data = $send_datas;
            }

            //发送融云消息，返回发送
            return SendCloudMessage($param[0], $param[1], $send_data, self::$_info_type[$info_type], '', self::$_my_type[$info_way], self::$_send_person_type[$send_person_type]);

        } elseif (is_array($info_filed_type) && is_array($info_type)) {

            $send_data_1 = trans('tui.' . $info_filed_type[0], $param[2]);
            $send_data_2 = ['content' => trans('tui.' .$info_filed_type[1], $param[2])];

            //发送融云消息
            SendCloudMessage($param[0], $param[1], $send_data_1, self::$_info_type[$info_type[0]], '', self::$_my_type[$info_way[0]], self::$_send_person_type[$send_person_type]);

            //发送第二次消息，返回发送
            return SendCloudMessage($param[0], $param[1], $send_data_2, self::$_info_type[$info_type[1]], '', self::$_my_type[$info_way[1]], self::$_send_person_type[$send_person_type]);

        } else {
            return null;
        }
    }

}