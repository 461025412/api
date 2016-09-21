<?php

class teambition{
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
        $url="https://account.teambition.com/oauth2/authorize";
        $param['client_id']=$this->app_id;
        $param['redirect_uri']=$this->callBackUrl;
        $param =http_build_query($param,'','&');
        $url=$url."?".$param;
        return $url;
        // echo $url;
        // echo $this->getUrl($url);
    }
    //通过Authorization Code获取Access Token并将token存到memcache中
    public function getAccessToken($app_key,$code){
            $mem = new Memcache;
            $mem->connect("localhost", 11211);
            $url="https://account.teambition.com/oauth2/access_token";
            $param['client_id']=$this->app_id;
            $param['client_secret']=$this->app_key;
            $param['code']=$code;
            $access_token=$this->postUrl($url,$param);
            $token=json_decode($access_token)->access_token;
            $mem->set($app_key,$token);
        
    }
    //自己写的获取token的方法 
    public function getToken(){
        $mem = new Memcache;
        $mem->connect("localhost", 11211);
        $token=$mem->get($this->app_id);
        $arr=array();
        //如果已有token则返回
        if(!isset($token)||empty($token)){
            $arr['code']=1;
            $arr['url']=$this->getAuthCode();
            return $arr;
        }//没有则返回授权地址
        else{
            $arr['code']=2;
            $arr['accessToken']=$token;
            $arr['url']=$this->getAuthCode();
            return $arr;
        }
    }
   
    //增加用户
    public function user_add($username,$password,$email,$first_name,$last_name,$mobile,$office,$telephone,$title,$depart){

    }
    //查找用户
    public function user_search($did,$uid){
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
        $params = array(
            'access_token' => $accessToken
        );
        $url="https://api.teambition.com/api/teams/{$did}/members?".http_build_query($params);
        $res=$this->getUrl($url);
        $json_data=json_decode($res,true);
        //判断授权是否过期
        $flag=$this->isValid($json_data['name']);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        if(!empty($json_data[0]['_id'])){
            foreach($json_data as $k=>$v){
            if($v['_id']==$uid){
                header("HTTP/1.0 200 ok");
                $returnArr['code']=0;
                $returnArr['message']="搜索成功";
                $returnArr['user']=$v;
                return json_encode($returnArr);
             }
          }
        }
        header("HTTP/1.0 404 error");
        $returnArr['code']=-1;
        $returnArr['message']="搜索失败";
        return json_encode($returnArr);

    }
    //修改用户
    public function user_update($did,$uid,$name=""){
        $userInfo=json_decode($this->user_search($did,$uid),true);
        if($userInfo['code']==-2||$userInfo['code']==-1){
            return json_encode($userInfo);
        }
        $member_id=$userInfo['user']['_memberId'];
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
        $params = array(
            'access_token' => $accessToken
        );
        $param['nickname']=$name;
        $url="https://api.teambition.com/members/{$member_id}?".http_build_query($params);
        $res=$this->callInterfaceCommon($url,"PUT",json_encode($param));
        return $res;

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
    //增加部门 名字可以重复。。
     public function depart_add($name,$summary=""){
        //获得token
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        //先判断是否已创建企业
        $oData=$this->getOrganizations();
        $oDataArr=json_decode($oData,true);
        //accesstoken过期
        if($oDataArr['code']=="-2"){
            return $oData;
        }
        //未创建组织
        if($oDataArr['code']=="-1"){
            $res=$this->addOrigination($tokenArr['accessToken']);
            if($res['code']=="-2"||$res['code']=="-1"){
                return json_encode($res);
            }
            $oDataArr['oId']=$res['oId'];
            $oDataArr['teamId']=$res['teamId'];
        }
        //创建部门
        $param['name']=$name;
        $param['_parentId']=$oDataArr['teamId'];
        $param['_organizationId']=$oDataArr['oId'];
        $url="https://api.teambition.com/api/teams?access_token=".$tokenArr['accessToken'];
        $res=$this->postUrl($url,$param);
        $json_arr=json_decode($res,true);
        if(!empty($json_arr['_id'])){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="添加部门成功";
            $returnArr['did']=$json_arr['_id'];
        }else{
             header("HTTP/1.0 400 error");
            $returnArr['code']=-1;
            $returnArr['message']="添加部门失败";
            $returnArr['did']="";
        }
        return json_encode($returnArr);

     }
     //创建组织
     public function addOrigination($accessToken){
        $url="https://api.teambition.com/organizations?access_token=".$accessToken;
        $param['name']="您的企业";
        $param['description']="洋葱为您创建的默认企业";
        $res=$this->postUrl($url,$param);
        $json_data=json_decode($res);
        $id=$json_data->_id;
        $teamId=$json_data->_defaultTeamId;
        //判断授权是否过期
        $flag=$this->isValid($json_data->name);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        //成功返回 teamid oId
        if(isset($id)&&!empty($id)){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="添加组织成功";
            $returnArr['oId']=$id;
            $returnArr['teamId']=$teamId;
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']=$res;
            $returnArr['oId']="";
            $returnArr['teamId']="";
        }
        return $returnArr;
     }
     //默认取第一个企业id返回
    public function getOrganizations(){
        //获取token
        $tokenArr=$this->getToken($this->app_id);
        if($tokenArr['code']==1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidCookie";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        $accessToken=$tokenArr['accessToken'];
        $params=array(
            "access_token" => $accessToken
            );
        $url="https://api.teambition.com/organizations?".http_build_query($params);  
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
        //判断请求是否成功
        if('[]'==$res){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
        }else{
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['oId']=$json_data[0]->_id;
            $returnArr['teamId']=$json_data[0]->_defaultTeamId;
        }

        return json_encode($returnArr);


    }
    //查找部门
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
        );
        //请求
        $url="https://api.teambition.com/api/teams/{$did}?".http_build_query($params);
        $res=$this->getUrl($url);
        $json_data=json_decode($res,true);
        //判断授权是否过期
        $flag=$this->isValid($json_data['name']);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        //判断是否成功
        if(!empty($json_data['_id'])){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="查找成功";
            $arr['name']=$json_data['name'];
            $arr['did']=$json_data['_id'];
            $returnArr['depart']=$arr;
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']=$res;
        }
        return json_encode($returnArr);
     }
    //修改部门
     public function depart_update($did,$name=""){
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
         $params = array(
            'access_token' => $accessToken
        );
         //发送请求
        $url="https://api.teambition.com/api/teams/".$did."?".http_build_query($params);
        if(!empty($name)){
          $param['name']=$name;
        }
        $res=$this->callInterfaceCommon($url,"PUT",json_encode($param));
        $json_data=json_decode($res,true);
        //判断授权是否过期
        $flag=$this->isValid($json_data['name']);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
        //判读请求是否成功
        if(!empty($json_data['_id'])){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="修改部门成功";
            // $returnArr['depart']=$res;
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="修改失败";
        }
        return json_encode($returnArr);
     }
    //删除部门
    public function depart_del($did){
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
            'access_token' => $accessToken
        );
         //发送请求
        $url="https://api.teambition.com/api/teams/".$did."?".http_build_query($params);
        $res=$this->callInterfaceCommon($url,"DELETE",$param);  
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
        if(empty($res)){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="删除部门成功";
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']=$res;
        }

        return json_encode($returnArr);
    }
    //增加部门成员 
    public function depart_member_add($did,$email){
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
        $url="https://api.teambition.com/api/v2/teams/{$did}/members?".http_build_query($params);
        $param['email']=$email;
        $param['_id']=$did; 
        $res=$this->postUrl($url,$param);
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
        if(!empty($json_data)){
            header("HTTP/1.0 200 ok");
            $returnArr['code']=0;
            $returnArr['message']="增加部门成员成功";
            $returnArr['did']=$did;
        }else{
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="增加部门成员失败";
            $returnArr['did']=$did;
        }
        return json_encode($returnArr);

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
    public function depart_member_search($did,$uid,$accessToken,$url){
        $params = array(
            'access_token' => $accessToken
        );
        $url="https://api.teambition.com/api/teams/{$did}/members?".http_build_query($params);
        $res=$this->getUrl($url);
        $res_json=json_decode($res);
        //判断授权是否过期
        $flag=$this->isValid($res_json->name);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$url;
            return json_encode($returnArr);
        }
        foreach($res_json as $k=>$v){
            if($v->_id==$uid){
                return $v;
            }
        }
        header("HTTP/1.0 404 error");
        $returnArr['code']=-1;
        $returnArr['message']="error";
        return json_encode($returnArr);

    }
    public function getUserInfo($email){
        $params=array(
            "email"        => $email
        );
        $url="http://api.teambition.com/users?".http_build_query($params);
        $res=$this->getUrl($url);
        return json_decode($res,true);
    }
    //修改部门成员 
    public function depart_user_change($src_did,$dst_did,$email){
        $userInfo=$this->getUserInfo($email);
        if(empty($userInfo)){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="error";
            return json_encode($returnArr);
        }
        $res=$this->depart_member_delete($src_did,$userInfo[0]['_id']);
        $json_arr=json_decode($res,true);
        if($json_arr['code']==-1){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="修改部门成员失败";
            return json_encode($returnArr);
        }
        if($json_arr['code']==-2){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$json_arr['url'];
            return json_encode($returnArr);
        }
        $res=$this->depart_member_add($dst_did,$email);
        $json_arr=json_decode($res,true);
        //判断授权是否过期
        $flag=$this->isValid($json_data['name']);
        if(!$flag){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-2;
            $returnArr['message']="InvalidAccessToken";
            $returnArr['url']=$tokenArr['url'];
            return json_encode($returnArr);
        }
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
    
    //删除部门成员 但是不能删除创建者
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
        $member=$this->depart_member_search($did,$uid,$accessToken,$tokenArr['url']);
        $memeber_json=json_decode($member,true);
        if($memeber_json['code']==-2||$memeber_json['code']==-1){
            return $member;
        }
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
    private function postUrl($url,$post_data){
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
     //CURL PUT
    public function callInterfaceCommon($URL,$type,$params="",$headers){
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