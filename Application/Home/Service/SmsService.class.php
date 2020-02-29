<?php
namespace Home\Service;

class SmsService {
    /**
     * 云之讯发送短信
     */
    static public function sendYzxSms($mobile, $carNo, $ck) {
        vendor('Ucpaas.Ucpaas');
        $option['accountSid'] = 'd3c851fd4156615b8b4f9eebf331a502';
        $option['token'] = 'dad7f136c0fb4346dc6061bc6632766d';
        $option['appid'] = 'b194a6bfad7941c4b8b12011aa9b4914';
        $option['templateid'] = '532773';
        $option['param'] = $carNo.','.$ck;
        $Ucpaas = new \Ucpaas($option);
        return $Ucpaas->SendSms($option['appid'],$option['templateid'],$option['param'],$mobile,$uid=null);
    }

}