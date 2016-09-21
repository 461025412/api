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
    "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
    "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
    "redirect_url"=>"http://api.schoolhand.top/callback.php",
    );
    $data['name']="xzjxzj";
    $data['summary']="11";
    $url="http://api.schoolhand.top/depart/add?".http_build_query($params);
    $res=postUrl($url,$data);
    print_r(json_decode($res));
}
function depart_search(){
   $params=array(
    "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
    "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
    "redirect_url"=>"http://api.schoolhand.top/callback.php",
    "did"       =>"57e1096008857d3d479f1c95"
    );


    $url="http://api.schoolhand.top/depart/get?".http_build_query($params);
    $res=getUrl($url);
    $json_data=json_decode($res,true);
    // echo $res;
    print_r($json_data); 
}
function depart_update(){
    $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
    
    $data["did"]="57e1096008857d3d479f1c95";
    $data["name"]="11122222";
    $url="http://api.schoolhand.top/depart/update?".http_build_query($params);
    $res=postUrl($url,$data);
    $json_data=json_decode($res,true);
    echo $res;
    print_r($json_data); 
}
function depart_delete(){
    $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
    
    $data["did"]="57e1096008857d3d479f1c95";
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
    $data["did"]="57e109718141d41f3954622a";
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
        $data["did"]="57e109718141d41f3954622a";
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
        $data["email"]="2961502706@qq.com";
        $data["dst_did"]="57e0d71c1c815f156b705000";
        $url="http://api.schoolhand.top/change/depart/member?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        echo $res;
        print_r($json_data);
}
// delete_depart_member();    
function add_depart_member(){
    $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $data["did"]="57e109718141d41f3954622a";
        $data["email"]="2961502706@qq.com";
        $url="http://api.schoolhand.top/add/depart/member?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        echo $res;
        print_r($json_data);

} 

function user_search(){
     $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
     $data["depart"]="57e109718141d41f3954622a";
     $data["uid"]="57d7ad8b493cd88cbf7df360";
     $url="http://api.schoolhand.top/user/get?".http_build_query($params);
     $res=postUrl($url,$data);
     $json_data=json_decode($res);
     echo $res;
     print_r($json_data);
}//yonghuliao
function user_update(){
    $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
     $data["depart"]="57e0d71c1c815f156b705000";
     $data["uid"]="57d7ad8b493cd88cbf7df360";
     $data["first_name"]="sssss";
     $data["last_name"]="ddd";
     $url="http://api.schoolhand.top/user/update?".http_build_query($params);
     $res=postUrl($url,$data);
     $json_data=json_decode($res);
     echo $res;
     print_r($json_data);
}
function user_delete(){
        $params=array(
        "app_key" => "23362d60-7a34-11e6-8a11-6d01c08e998f",
        "app_secret" => "2fc39627-935d-4d65-ab19-4f5021cbeff7",
        "redirect_url"=>"http://api.schoolhand.top/callback.php"
        );
        $data["depart"]="57dfb5231e03d89a02cc4ffe";
        $data["uid"]="57d7ad8b493cd88cbf7df360";
        $url="http://api.schoolhand.top/user/delete?".http_build_query($params);
        $res=postUrl($url,$data);
        $json_data=json_decode($res,true);
        echo $res;
        print_r($json_data);
}
// depart_add();
//57e109718141d41f3954622a
// depart_search();
// depart_update();
// depart_delete();
// search_depart_member();
// delete_depart_member();
// change_depart_member();
// add_depart_member();
// user_search();
// user_delete();








?>