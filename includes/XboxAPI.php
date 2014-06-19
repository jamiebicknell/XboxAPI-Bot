<?php

class XboxAPI
{

    public function limit()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://xboxapi.com/v1/limit');
        curl_setopt($ch, CURLOPT_USERAGENT, 'XboxAPI Bot');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        $output = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status=='200') {
            list($calls, $allowed) = explode('/', $output);
            if ($allowed - $calls > 0) {
                return true;
            }
        }
        return false;
    }

    public function profile($gamertag)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://xboxapi.com/v1/profile/' . urlencode($gamertag));
        curl_setopt($ch, CURLOPT_USERAGENT, 'XboxAPI Bot');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        $output = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array('code'=>$status, 'response'=>$output);
    }
}
