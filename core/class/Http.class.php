<?php
!defined('IN_FW') && exit('Access Denied');
class Http{
    public function post($url,$data = array()){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $tmpInfo = curl_exec($ch); 
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
        curl_close($ch);
        if($httpCode == 200){
            return $tmpInfo;
        }else{
            return '';
        }
    }
    public function get($url,$is_mobile = false){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if($is_mobile){
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone;  Mobile/13D15 Safari/601.1');
        }else{
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $tmpInfo = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
        curl_close($ch);
        if($httpCode == 200){
            return $tmpInfo;
        }else{
            return '';
        }
    }
    public function get_header($url){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $tmpInfo = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($tmpInfo, 0, $header_size);
        curl_close($ch);
        return $header;
    }
    public function multi_get($urls,$change_ip = false){
        if($change_ip){
            $ip = rand(1,254) . '.' . rand(1,254) . '.' . rand(1,254) . '.' . rand(1,254);
        }else{
            $ip = getIp();
        }
        $mh = curl_multi_init();
        $conn = array();        
        foreach ($urls as $i => $url) {
            $conn[$i] = curl_init(); 
            curl_setopt($conn[$i], CURLOPT_URL, $url); 
            curl_setopt($conn[$i], CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, FALSE); 
            curl_setopt($conn[$i], CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($conn[$i], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0');
            curl_setopt($conn[$i], CURLOPT_HTTPHEADER, array(
                                                        'DNT:1','Connection:keep-alive',
                                                        'Accept-Encoding:deflate',
                                                        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                                        'X-FORWARDED-FOR:'.$ip,
                                                        'CLIENT-IP:'.$ip
                                                    ));
            curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($conn[$i], CURLOPT_AUTOREFERER, 1); 
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle ($mh,$conn[$i]);   
        }
        do{
            curl_multi_exec($mh,$active);   
        }while($active);
        $data = array();
        foreach ($urls as $i => $url) {   
            $data[$i] = curl_multi_getcontent($conn[$i]);
        }
        foreach ($urls as $i => $url) {   
            curl_multi_remove_handle($mh,$conn[$i]);   
            curl_close($conn[$i]);
        }
        curl_multi_close($mh);
        return $data;
    }
    public function multi_post($post_data){
        $mh = curl_multi_init();
        $conn = array();        
        foreach ($post_data as $i => $val) {
            $conn[$i] = curl_init(); 
            curl_setopt($conn[$i], CURLOPT_URL, $val['url']); 
            curl_setopt($conn[$i], CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($conn[$i], CURLOPT_SSL_VERIFYPEER, FALSE); 
            curl_setopt($conn[$i], CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($conn[$i], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0');
            curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($conn[$i], CURLOPT_AUTOREFERER, 1); 
            curl_setopt($conn[$i], CURLOPT_POSTFIELDS, $val['data']);
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle ($mh,$conn[$i]);   
        }
        do{
            curl_multi_exec($mh,$active);   
        }while($active);
        $data = array();
        foreach ($post_data as $i => $url) {
            $data[$i] = $url;
            $data[$i]['return'] = curl_multi_getcontent($conn[$i]);
        }
        foreach ($post_data as $i => $url) {   
            curl_multi_remove_handle($mh,$conn[$i]);   
            curl_close($conn[$i]);
        }
        curl_multi_close($mh);
        return $data;
    }
}
?>