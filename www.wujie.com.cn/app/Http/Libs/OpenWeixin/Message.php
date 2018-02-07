<?php

/*
 * 消息处理
 */

namespace App\Http\Libs\OpenWeixin;

use DOMDocument;
use Illuminate\Http\Request;

class Message {

    protected $xml;
    protected $OpenID;
    protected $UserName;
    protected $Encrypt;

    //初始化处理
    public function __construct($OpenID, $UserName) {
        $this->OpenID = $OpenID;
        $this->UserName = $UserName;
        $this->xml = $this->getXml($GLOBALS["HTTP_RAW_POST_DATA"]);
    }

    protected function getXml($xmlStr) {
        $xml = new DOMDocument();
        $xml->loadXML($xmlStr);
        return $xml;
    }

    //响应处理
    public function response(Request $request) {
        $Encrypt = $this->xml->getElementsByTagName('Encrypt');
        if ($Encrypt->length == 1) {//加密消息
            if (empty($this->crypt)) {//没有注入解密无法进行
                return '';
            }
            $xmlStr = $this->crypt->decryptMsg($Encrypt->item(0)->nodeValue, $request->get('signature'), $request->get('timestamp'), $request->get('nonce'));
            if (!$xmlStr) {
                return '';
            }
            $xml = $this->parse($this->getXml($xmlStr));
            if (!$xml) {
                return $xml;
            }
            return (string) $this->crypt->encryptMsg($xml);
        } else {//明文消息
            return $this->parse($this->xml);
        }
    }

    //是否加密
    public function isEncrypt() {
        $Encrypt = $this->xml->getElementsByTagName('Encrypt');
        return $Encrypt->length == 1; //加密消息
    }

    //是否加密
    public function setEncrypt(Message\Crypt $Encrypt) {
        $this->Encrypt = $Encrypt;
    }

    //文本消息响应
    protected function parse(DOMDocument $xml) {
        $MsgType = $xml->getElementsByTagName('MsgType');
        if ($MsgType->length == 1) {
            //消息类型
            $type = $MsgType->item(0)->nodeValue;
            if (method_exists($this, $type)) {
                return $this->$type($xml);
            }
        }
        return '';
    }

    //事件响应
    protected function event(DOMDocument $xml) {
        $event = $xml->getElementsByTagName('Event');
        if ($event->length != 1) {
            return '';
        }
        $text = $event->item(0)->nodeValue;
        if ($text === 'subscribe') {//关注事件
            return $this->reply();
        }
        return '';
    }

    //文本消息响应
    protected function text(DOMDocument $xml) {
        $content = $xml->getElementsByTagName('Content');
        if ($content->length != 1) {
            return '';
        }
        $text = $content->item(0)->nodeValue;
        if ($text === '开店') {
            return $this->reply();
        }
        return '';
    }

    //回复处理
    protected function reply() {
        return '<xml>
<ToUserName><![CDATA[' . $this->OpenID . ']]></ToUserName>
<FromUserName><![CDATA[' . $this->UserName . ']]></FromUserName>
<CreateTime>' . time() . '</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[把情愫变成事业——圆你儿时的港式情怀！]]></Title>
<Description><![CDATA[1月8日，开年大戏！各位佬晒、事头婆，不见不散！]]></Description>
<PicUrl><![CDATA[http://api.wujie.com.cn//attached/image/20161223/20161223180430_43506.png]]></PicUrl>
<Url><![CDATA[http://api.wujie.com.cn/webapp/activity/detail/_v020400?pagetag=02-2&id=633&makerid=132&uid=279&is_share=1]]></Url>
</item>
</Articles>
</xml>';
    }

}
