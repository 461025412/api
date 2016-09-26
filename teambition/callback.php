<?php 

$code=$_GET['code'];
$params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php",
        "code"         =>$code
        );
$url="http://api.schoolhand.top/getAccessToken?".http_build_query($params);
$res=getUrl($url);
echo $res;
print_r(json_decode($res));
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

function depart_add(){
	 $data['name']="11";
	$data['summary']="11";
	$url="http://api.schoolhand.top/depart/add?accessToken=".$accessToken;
	$res=postUrl($url,$data);
}
function depart_search(){
	$params = array(
            'accessToken' => $accessToken,
            'did'     => "57de4b1cdf4fc0987f76d5d6"
        );
	$url="http://api.schoolhand.top/depart/get?".http_build_query($params);
	$res=getUrl($url);
	echo $res;
}
function depart_update(){
	$data['name']="xzjxzj";
	$data['summary']="xzjxzj";
	$data['did']="57de4b1cdf4fc0987f76d5d6";
	$url="http://api.schoolhand.top/depart/update?accessToken=".$accessToken;
	$res=postUrl($url,$data);
	print_r(json_decode($res));
}
function depart_del(){
    $data['did']="57de4b832ad870bd47becb05";
	$url="http://api.schoolhand.top/depart/delete?accessToken=".$accessToken;
	$res=postUrl($url,$data);
	// echo $res;
	print_r(json_decode($res));
}

function depart_all_search(){
	$data['did']="57de4bdb37a2a0f15b54bcd9";
	$url="http://api.schoolhand.top/search/depart/member?accessToken=".$accessToken;
	$res=postUrl($url,$data);
	// echo $res;
	print_r(json_decode($res));
}
//获取当前授权的用户信息
 function getme($accessToken){
        $params = array(
            'access_token' => $accessToken
        );
        $url="https://api.teambition.com/users/me?".http_build_query($params);
        $res=getUrl($url);
        print_r($res);

    }
    // $res=getme($accessToken);
    // print_r($res);

    // $data['did']="57de571a2ad870bd47bf70a2";
    // $data['uid']="57d7ad8b493cd88cbf7df360";
    // echo $url;
	// $url="https://api.teambition.com/organizations?access_token=".$accessToken;

	// $res=getUrl($url);
 //    if($res=="[]"){
 //        echo 111;
 //    }

	// print_r(json_decode($res));
	// print_r(json_decode($res));

	// $obj->getToken();








