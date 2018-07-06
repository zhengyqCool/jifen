<?php
function sendSMS ($mobile,$param){
	$accessKeyId = config('dayu_appkey');
    $accessKeySecret = config('dayu_secretKey');
    $params["PhoneNumbers"] = $mobile;
    $params["SignName"] = config('dayu_sign');
    $params["TemplateCode"] = config('dayu_template');
    $params['TemplateParam'] = Array (
        "code" => $param['code']
    );
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }
    $helper = new AliyunSms();
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        ))
    );
	if ($content->Code == 'OK'){
		return true;
	}else{
		return false;
	}
}
