<?php

include dirname(__FILE__) . '/config.php';
include dirname(__FILE__) . '/includes/autoload.php';

$db = new Database(DATABASE_HOST,DATABASE_USER,DATABASE_PASS,DATABASE_NAME);
$twitter = new tmhOAuth(array(
    'consumer_key'     => TWITTER_CONSUMER_KEY,
    'consumer_secret'  => TWITTER_CONSUMER_SECRET,
    'user_token'       => TWITTER_USER_TOKEN,
    'user_secret'      => TWITTER_USER_SECRET,
));

$get_since_id = $db->query("SELECT * FROM `settings` ORDER BY `id` ASC");
$row_since_id = $db->fetch($get_since_id);
$db->clear($get_since_id);
$since_id = $row_since_id['value'];
        
$twitter->request('GET',$twitter->url('1.1/statuses/mentions_timeline'),array(
    'include_entities' => 'false',
    'trim_user'        => 'false',
    'count'            => '200',
    'since_id'         => $since_id,
));
if($twitter->response['code']==200) {
    $i = 0;
    $data = json_decode($twitter->response['response']);
    foreach($data as $tweet) {
        $since_id = ($since_id>$tweet->id_str) ? $since_id : $tweet->id_str;
        $command = '@' . TWITTER_SCREEN_NAME . ' isonline ';
        if(strtolower(substr($tweet->text,0,strlen($command)))==strtolower($command)) {
            $gamertag = trim(substr($tweet->text,strlen($command)));
            $db->query("INSERT INTO `tweets` (`date`,`tweet_id`,`screen_name`,`gamertag`,`attempts`,`status`) VALUES ('".date('Y-m-d H:i:s')."','".$tweet->id_str."','".$db->escape($tweet->user->screen_name)."','".$db->escape($gamertag)."','0','0')");
        }        
        $i++;
    }
    if($i>0) {
        $db->query("UPDATE `settings` SET `value` = '".$db->escape($since_id)."' WHERE `id` = '1' LIMIT 1");
    }
}

$get_rows = $db->query("SELECT * FROM `tweets` WHERE `status` = '0' ORDER BY `id` ASC");
$num_rows = $db->count($get_rows);
if($num_rows>0) {
    while($row = $db->fetch($get_rows)) {
        $status = $row['status'];
        if($attempts>=60*24*7) {
            $status = 3;
            $twitter->request('POST',$twitter->url('1.1/statuses/update'),array(
                'status'                => '@' . $row['screen_name'] . ' ' . $row['gamertag'] . ' hasn\'t been online since your request a week ago, we\'ve had to stop checking, sorry!',
                'in_reply_to_status_id' => $row['tweet_id'],
            ));
        }
        else {
            $profile = XboxAPI::profile($row['gamertag']);
            if($profile['code']=='200') {
                $data = json_decode($profile['response']);
                if($data->Error=='Invalid Gamertag') {
                    $status = 2;
                    $twitter->request('POST',$twitter->url('1.1/statuses/update'),array(
                        'status'                => '@' . $row['screen_name'] . ' ' . $row['gamertag'] . ' isn\'t a valid Xbox gamertag',
                        'in_reply_to_status_id' => $row['tweet_id'],
                    ));
                }
                else {
                    $status = ($data->Player->Status->Online=='1') ? 1 : 0;
                    if($status) {
                        $twitter->request('POST',$twitter->url('1.1/statuses/update'),array(
                            'status'                => '@' . $row['screen_name'] . ' ' . $row['gamertag'] . ' is currently online ' . substr($data->Player->Status->Online_Status,7),
                            'in_reply_to_status_id' => $row['tweet_id'],
                        ));
                    }
                    elseif($attempt==0) {
                        if(strpos($data->Player->Status->Online_Status,'ago')) {
                            $data->Player->Status->Online_Status = strtolower(strstr($data->Player->Status->Online_Status,' ago',true) . ' ago');
                        }
                        $twitter->request('POST',$twitter->url('1.1/statuses/update'),array(
                            'status'                => '@' . $row['screen_name'] . ' ' . $row['gamertag'] . ' is currently offline, we will tweet when they come online (' . $data->Player->Status->Online_Status . ')',
                            'in_reply_to_status_id' => $row['tweet_id'],
                        ));
                    }
                    
                }
            }
            $attempts = $row['attempts']+1;
        }
        $db->query("UPDATE `tweets` SET `attempts` = '".$db->escape($attempts)."',`status` = '".$db->escape($status)."' WHERE `id` = '".$db->escape($row['id'])."' LIMIT 1");
    }
}
$db->clear($get_rows);

$db->close($db);