<?php
//57d7ad8b493cd88cbf7df360  29
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
function  depart_add(){
    $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
    $data['key']="xzjxzj";
    $data['name']="woshixzj";
    $url="http://api.schoolhand.top/depart/add?".http_build_query($params);
    $res=postUrl($url,$data);
    echo $res;
    print_r(json_decode($res));
}
function depart_search(){
   $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php",
        "did"       => "546306,546274"
        );

    $url="http://api.schoolhand.top/depart/get?".http_build_query($params);
    $res=getUrl($url);
    $json_data=json_decode($res,true);
    echo $res;
    print_r($json_data); 
}
function depart_update(){
    $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
    
    $data["id"]="546274";
    $data["name"]="xzjxzj";
    $data["description"]="test";
    $url="http://api.schoolhand.top/depart/update?".http_build_query($params);
    $res=postUrl($url,$data);
    $json_data=json_decode($res,true);
    echo $res;
    print_r($json_data); 
}
function depart_delete(){
    $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
    
    $data["name"]="msssssss";
    $data["key"]="xzjxzj";
    $url="http://api.schoolhand.top/depart/delete?".http_build_query($params);
    $res=postUrl($url,$data);
    $json_data=json_decode($res,true);
    echo $res;
    print_r($json_data);  
}
function search_depart_member(){
    $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
    
    $data["did"]="57dfb5231e03d89a02cc4ffe";
    $url="http://api.schoolhand.top/search/depart/member?".http_build_query($params);
    $res=postUrl($url,$data);
    $json_data=json_decode($res,true);
    echo $res;
    print_r($json_data); 
}
function getme(){
        $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $url="http://api.schoolhand.top/getme?".http_build_query($params);
        $res=getUrl($url,$data);
        $json_data=json_decode($res,true);
        echo $res;
        print_r($json_data); 
}
function delete_depart_member(){
        $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $data["did"]="57dfb5231e03d89a02cc4ffe";
        $data["uid"]="57d7ad8b493cd88cbf7df360";
        $url="http://api.schoolhand.top/delete/depart/member?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        echo $res;
        print_r($json_data);
}//57dfc671309262d7269e1391
function change_depart_member(){
        $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $data["src_did"]="57dfb5231e03d89a02cc4ffe";
        $data["uid"]="57d7ad8b493cd88cbf7df360";
        $data["dst_did"]="57dfc671309262d7269e1391";
        $url="http://api.schoolhand.top/change/depart/member?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        echo $res;
        print_r($json_data);
}
function user_add(){
        $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $data["email"]="2961502706@qq.com";
        $data["username"]="xzjxzj";
        $url="http://api.schoolhand.top/user/add?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        print_r($res);
        print_r($json_data);
}    
function user_update(){
        $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $data["first_name"]="xss";
        $data["last_name"]="xx";
        $url="http://api.schoolhand.top/user/update?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        // var_dump($json_data);
        print_r($res);
        print_r($json_data);
}    
function user_search(){///api/social/search  只能搜索别人，不能搜索自己
        $params=array(
        "app_key" => "c0f4cc35a21bc912d73894fb121b1a62",
        "app_secret" => "89100c1f1af666a3f490d7aec5d28097d3bc27cb",
        "redirect_url"=>"http://api.schoolhand.top/callback.php",
        "key"       => "xzjxzj1"
        );
        $url="http://api.schoolhand.top/user/get?".http_build_query($params);
        $res=getUrl($url);
        $json_data=json_decode($res,true);
        print_r($res);
        print_r($json_data);

}
//CURL POST
    function postUrl1($url,$post_data){
        $timeout = 5000;
        $ch = curl_init();
        // $bo='---------------------------20250462941632225602859586036';
        $headers = array();
        $headers[] = 'Accept: application/json';
        // $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch,CURLINFO_HEADER_OUT,true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        $result=curl_exec($ch);
        $headerStr=curl_getinfo($ch,CURLINFO_HEADER_OUT);
        list($responseStr,$contentStr)=explode("\r\n\r\n",$result,2);
        echo "request header:".$headerStr;
        echo '<br/>';
        echo 'response header:'.$responseStr;
        echo '<br/>';
        echo 'response content:'.$contentStr;
        die;
        curl_close($ch);
        return $result;
    }
function callInterfaceCommon($URL,$type,$params="",$headers=""){
    $ch = curl_init($URL);
    $timeout = 5;
    if($headers!=""){
        curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
    }else {
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    }
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    switch ($type){
        case "PUT" : curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
        case "PATCH": curl_setopt($ch, CULROPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);break;
        case "DELETE":curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
    }
    $file_contents = curl_exec($ch);//获得返回值
    return $file_contents;
    curl_close($ch);
}
//depart_add();
//depart_search();
//depart_delete();
//depart_update();
//user_search();

//$project="msdd";
//         $params = array(
//             'access_token' => 'a8b011af95a709402380f7fafa897866',
//             'name'        =>$project
//          );
//         $key="xzjxzj";

//         $url="https://coding.net/api/user/{$key}/project/{$project}?".http_build_query($params);
//          $data['name']=$project;
         // $data['global_key']="xzjxzjxzjxzj";
         // $data['password']="111122223333";
//         echo 111;
//         $res=postUrl($url, $data);
//$res=callInterfaceCommon($url,"DELETE");
//         echo $res;






?>