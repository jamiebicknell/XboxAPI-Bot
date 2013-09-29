<?php

class XboxAPI {
    
    function profile($gamertag) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://xboxapi.com/profile/' . urlencode($gamertag));
        curl_setopt($ch, CURLOPT_USERAGENT, trim($command));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        $output = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array('code'=>$status,'response'=>$output);
    }
    
}