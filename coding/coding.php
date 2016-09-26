<?php

class coding{
	//APP ID
    private $app_id="";
    //APP KEY
    private $app_key="";
    //回调地址
    private $callBackUrl="";
    //Authorization Code
    private $code="";
    //access Token
    private $accessToken="";
	public function __construct($key,$secret,$callBackUrl){
		$this->app_id="";
        $this->app_key="";
        $this->callBackUrl="";
		$this->app_id=$key;
		$this->app_key=$secret;
		$this->callBackUrl=$callBackUrl;

	}
	//获取Authorization Code 返回一个授权地址
    public function getAuthCode(){
        $url="https://coding.net/oauth_authorize.html";
        $param['client_id']=$this->app_id;
        $param['redirect_uri']=$this->callBackUrl;
        $param['response_type']="code";
        $param['scope']="all";
        $param =http_build_query($param,'','&');
        $url=$url."?".$param;
        return $url;
        // echo $url;
        // echo $this->getUrl($url);
    }
    //通过Authorization Code获取Access Token并将token存到memcache中
    public function getAccessToken($code){
            $mem = new Memcache;
            $mem->connect("localhost", 11211);
            $url="https://coding.net/api/oauth/access_token?";
            $param['client_id']=$this->app_id;
            $param['client_secret']=$this->app_key;
            $param['code']=$code;
            $param['grant_type']="authorization_code";
            $url=$url.http_build_query($param);
            $access_token=$this->getUrl($url);
            $token=json_decode($access_token)->access_token;
            $keep_time=json_decode($access_token)->expires_in;
            if($keep_time>2592000){
                $keep_time=2592000;
            }
            $refresh_token=json_decode($access_token)->refresh_token;
            if(empty($token)){
                header("HTTP/1.0 404 error");
                $returnArr['code']=-1;
                $returnArr['message']="操作失败，无法获取token";
                return json_encode($returnArr);
            }
            $key="coding".$this->app_id;
            $res=$this->addToMysql($key,$refresh_token);
            if($res['code']==0){
                $mem->set($key,$token, 0,$keep_time);
                header("HTTP/1.0 200 ok");
                return json_encode($res);
            }else{
                header("HTTP/1.0 404 error");
                $returnArr['code']=-1;
                $returnArr['message']="操作失败，无法插入数据库";
                return json_encode($returnArr);
            }
        
    }
    public function addToMysql($app_key,$refresh_token){
            $db_connect=mysql_connect('127.0.0.1', 'root', 'xzj36199477');
            mysql_select_db("api",$db_connect);
            $app_key="coding".$this->app_id;
            $sql="select * from api where app_key='".$app_key."'";
            $res=mysql_query($sql);
            $data=mysql_fetch_assoc($res);
            if(!empty($data['id'])){
                $res=mysql_fetch_array($res);
                $sql="update api set refresh_token='".$refresh_token."'";
                $result=mysql_query($sql);
                if($result){
                    $arr=array();
                    $arr['code']="0";
                    $arr['message']="更新token操作成功";
                    return $arr;
                }

            }else{
                $sql="insert into api(app_key,refresh_token) values('".$app_key."','".$refresh_token."')";
                $result=mysql_query($sql);
                if($result){
                    $arr=array();
                    $arr['code']="0";
                    $arr['message']="添加token操作成功";
                    return $arr;
                }
            }

    }
    public function getFromMysql(){
            $db_connect=mysql_connect('127.0.0.1', 'root', 'xzj36199477');
            mysql_select_db("api",$db_connect);
            $app_key="coding".$this->app_id;
            $sql="select * from api where app_key='{$app_key}'";
            $res=mysql_query($sql);
            $data=mysql_fetch_assoc($res);
            if(!empty($data['id'])){
                return mysql_fetch_array($res);
            }
            return '';
    }
    //自己写的获取token的方法 
    public function getToken(){
        $mem = new Memcache;
        $mem->connect("localhost", 11211);
        $key="coding".$this->app_id;
        $token=$mem->get($key);
        $arr=array();
        //token为空
        if(empty($token)){
            //从数据库中取数据
            $res=$this->getFromMysql();
            //判断数据是否存在
            //不存在
            if(empty($res)){
                $arr['code']=1;
                $arr['url']=$this->getAuthCode();
                return $arr;
            }//存在
            else{
                $url="https://coding.net/api/oauth/access_token?";
                $param['client_id']=$this->app_id;
                $param['client_secret']=$this->app_key;
                $param['scope']="all";
                $param['refresh_token']=$res['refresh_token'];
                $param['grant_type']="refresh_token";
                $url=$url.http_build_query($param);
                $access_token=$this->getUrl($url);
                $token=json_decode($access_token)->access_token;
                $keep_time=json_decode($access_token)->expires_in;
                $refresh_token=json_decode($access_token)->refresh_token;
                $res=$this->addToMysql($key,$refresh_token);
                $mem->set($key,$token, 0,$keep_time);
                $arr['code']=2;
                $arr['accessToken']=$token;
                $arr['url']=$this->getAuthCode();
                return $arr;
            }
        }
        //如果已有token则返回
            $arr['code']=2;
            $arr['accessToken']=$token;
            $arr['url']=$this->getAuthCode();
            return $arr;
    }
   
    //增加用户
    public function user_add($email,$username){
        // return 11;
        //获得token
        $tokenArr=$this->getToken();
        $params = array(
            'access_token' => $tokenArr['accessToken']
        ); 
        $headers = array();
        $headers[] = "Accept: */*";
        $headers[] = "Content-Type: application/json";
        $url="https://coding.net/api/v2/account/register?".http_build_query($params);
        $data['email']=$email;
        $data['global_key']=$username;
        // $data['password']=sha1("a36199477");
        $res=$this->postUrl($url,json_encode($data),$headers);
        // return $url;
        return $res;
    }
    //查找用户
    public function user_search($key){
        //获得token
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $params = array(
            'access_token' => $tokenArr['accessToken'],
            'key'         =>  $key,
            'page'         => '1',
            'pageSize'      => '10'
            // 'sex'           => 0
         ); 
        $url="https://coding.net/api/social/search?".http_build_query($params);
        $res=$this->getUrl($url);
        $json_data=json_decode($res,true);
        if(!empty($json_data['data'][0]['name'])){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="搜索成功";
            $returnArr['res']=$json_data['data'][0];
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=0;
            $returnArr['message']="搜索失败";
            $returnArr['res']=$json_data;
        }
        return json_encode($returnArr);
    }
    //修改用户
    public function user_update($name){
        //获得token
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $params = array(
            'access_token' => $tokenArr['accessToken'],
            // 'name'         =>  $name,
            // 'sex'           => 0
         ); 
        $data['name']=$name;
        $data['sex']=0;
        $url="https://coding.net/api/account/update_info?".http_build_query($params);
        $res=$this->postUrl($url,$data);
        $json_data=json_decode($res,true);
         if(!is_array($json_data)){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="修改用户信息成功";
        }else{
            header("HTTP/1.0 400 error");
            $returnArr['code']=-1;
            $returnArr['message']="修改用户信息失败";
            $returnArr['res']=$json_data;
        }
        return json_encode($returnArr);

    }
    //删除用户
    public function user_delete($uid,$depart){

    }
    //判断token是否过期
    public function isValid($name=""){
        if($name=="InvalidAccessToken"){
            return false;
        }
        return true;
    }
    //增加部门 默认创建公有资源
     public function depart_add($key,$name){

        //获得token
        $tokenArr=$this->getToken();
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $params = array(
            'access_token' => $tokenArr['accessToken']
        ); 
        $data['name']=$name;
        $data['type']="1";
        $data['gitEnabled']="true";
        $data['gitReadmeEnabled']="false";
        $data['vcsType']="git";
        $data['global_key']=$key;
        $url="https://coding.net/api/user/{$key}/project?".http_build_query($params);
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $res=$this->postUrl($url,$data,$headers);
        $json_arr=json_decode($res,true);
        if($json_arr['code']===0){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="添加部门成功";
            $returnArr['path']=$json_arr['data'];
        }else{//名字重复会报错  项目名只允许字母、数字或者下划线(_)、中划线(-)、点(.)，必须以字母或者数字开头,且点不能连续,且不能以.git结尾
             header("HTTP/1.0 400 error");
            $returnArr['code']=-1;
            $returnArr['message']="添加部门失败";
            $returnArr['res']=$json_arr;
        }
        return json_encode($returnArr);

     }
    
    //查找部门 只能查找公有的资源
     public function depart_search($did){
        //获取token
        $tokenArr=$this->getToken($this->app_id);
        //判断token是否过期
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
        $params = array(
            'access_token' => $accessToken,
            'page'         => "1",
            'pageSize'      =>"10"
        );
        //请求
         $dids=explode(",",$did);
        $url="https://coding.net/api/user/projects?".http_build_query($params);
        $res=$this->getUrl($url);
        $json_data=json_decode($res,true);
        $departs=$json_data['data']['list'];
         $depart_data=array();
         $i=0;
        foreach($departs as $key=>$val){
            if(in_array($val['id'],$dids)){
                $depart_data[$i]=$val;
                $i++;
            }
        }
         if(!empty($depart_data[0])){
             header("HTTP/1.0 200 ok");
             $returnArr['code']=0;
             $returnArr['message']="搜索成功";
             $returnArr['depart']=$depart_data;
         }else{
             header("HTTP/1.0 404 error");
             $returnArr['code']=-1;
             $returnArr['message']="没有该部门";
         }

        return json_encode($returnArr);
     }
    //修改部门
     public function depart_update($id,$name="",$description=""){
        //获取token值
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
         $param = array(
            'access_token' => $accessToken,
             'id'           =>$id
        );
         if(!empty($name)){
             $param['name']=$name;
         }
         if(!empty($description)){
             $param['description']=$description;
         }
         //发送请求
        $url="https://coding.net/api/project?".http_build_query($param);
        $res=$this->callInterfaceCommon($url,"PUT");
        $json_data=json_decode($res,true);
        //判读请求是否成功
        if($json_data['code']==0){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="修改部门成功";
            // $returnArr['depart']=$res;
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="修改部门失败";
            $returnArr['res']=$json_data;
        }
        return json_encode($returnArr);
     }
    //删除部门
    public function depart_del($key,$name){
        //获取token
        $tokenArr=$this->getToken($this->app_id);
        //判断token是否过期
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
         $params = array(
            'access_token' => $accessToken,
             'name'        =>$name
        );
         //发送请求
        $url="https://coding.net/api/user/{$key}/project/{$name}?".http_build_query($params);
        $res=$this->callInterfaceCommon($url,"DELETE");
        $json_data=json_decode($res,true);
        if($json_data['code']==0){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="删除部门成功";
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="删除部门失败";
            $returnArr['res']=$json_data;
        }
        return json_encode($returnArr);
    }
    //增加部门成员 没有搜索用户的接口
    public function depart_member_add($did,$uid){
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
        $params = array(
            'access_token' => $accessToken
        );
        $url="https://api.teambition.com/api/v2/teams/{did}/members?".http_build_query($params);
        // $param['email']=$email;

    }
    //查找所有部门成员
    public function depart_all_member($did){
        //获取token
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
        //发送请求
        $params = array(
            'access_token' => $accessToken
        );

        $url="https://api.teambition.com/api/teams/{$did}/members?".http_build_query($params);
        $res=$this->getUrl($url);
        $json_data=json_decode($res);
        //判断授权是否过期
        $flag=$this->isValid($json_data->name);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $user=array();
        //判断是否成功
        if($json_data){
            $returnInfo['code']=0;
            $returnInfo['users']=$json_data;
            $returnInfo['message']="部门成员查询成功";
            
        }else{
            $returnInfo['code']=-1;
            $returnInfo['message']=$res;
        }
        return json_encode($returnInfo);
        // return $res;
        
    }
    //查找一位部门成员 返回数组
    public function depart_member_search($did,$uid,$accessToken){
        $params = array(
            'access_token' => $accessToken
        );
        $url="https://api.teambition.com/api/teams/{$did}/members?".http_build_query($params);
        $res=$this->getUrl($url);
        $res_json=json_decode($res);
        foreach($res_json as $k=>$v){
            if($v->_id==$uid){
                return $v;
            }
        }

    }
    //修改部门成员 
    public function depart_user_change($src_did,$dst_did,$uid){
        $res=$this->depart_member_delete($src_did,$uid);
        $json_arr=json_decode($res,true);
        if($json_arr['code']==-1||$json_arr['code']==-2){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="修改部门成员失败";
            return json_encode($returnArr);
        }
        $res=$this->depart_member_add($dst_did,$uid);
        $json_arr=json_decode($res,true);
        if($json_arr['code']==-1||$json_arr['code']==-2){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="修改部门成员失败";
            return json_encode($returnArr);
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=0;
            $returnArr['message']="修改部门成员成功";
            return json_encode($returnArr);
        }
    }
    
    //删除部门成员
    public function depart_member_delete($did,$uid){
        //获取token
        $tokenArr=$this->getToken($this->app_id);
        //判断token是否过期
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
        //获取部门用户信息
        $member=$this->depart_member_search($did,$uid,$accessToken);
        $member_id=$member->_userId;
        $params = array(
            'access_token' => $accessToken
        );
        //删除部门用户
        $url="https://api.teambition.com/api/teams/{$did}/members/{$member_id}?".http_build_query($params);
        $res=$this->callInterfaceCommon($url,"DELETE");
        $json_data=json_decode($res);
        //判断授权是否过期
        $flag=$this->isValid($json_data->name);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        //判断请求是否成功
        if($res=="{}"){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="删除部门成员成功";
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="删除部门成员失败";
        }
        // print_r($returnArr);
        return json_encode($returnArr);
        
    }
    //获取当前授权的用户信息
    public function getme(){
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
        $params = array(
            'access_token' => $accessToken
        );
        $url="https://api.teambition.com/users/me?".http_build_query($params);
        $res=$this->getUrl($url);
        return $res;

    }
     //CURL GET
    private function getUrl($url){
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
    
    //CURL POST
    private function postUrl($url,$post_data,$headers=''){
        $timeout = 5000;
        $ch = curl_init();
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
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
     //CURL PUT
    public function callInterfaceCommon($URL,$type,$params="",$headers=""){
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
    
}

// $obj=new teambition("e51eefc0-7985-11e6-a466-7dfb6c40689a","34d47b1b-31d5-4977-a97a-fa71365ff229","http://www.schoolhand.top/callback.php");
// $obj->getAuthCode();





?>