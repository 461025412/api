<?php 

$code=$_GET['code'];
$params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php",
        "code"         =>$code
        );
$url="http://api.schoolhand.top/getAccessToken?".http_build_query($params);
$res=getUrl($url);


function postUrl($url,$post_data){
        $timeout = 5000;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    } 
    //CURL GET
function getUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    } 








