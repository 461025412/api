<?php 
session_start();
//获取appkey appsecret
$app_key=$_REQUEST['app_key'];
$app_secret=$_REQUEST['app_secret'];
//回调地址
$redirect_url=$_REQUEST['redirect_url'];
//构建路径
$uri=$_SERVER["REQUEST_URI"];
$uri=parse_url($uri);
$url=$uri['path'];
include_once "teambition.php";
//获取get数据 post数据
$postData=$_POST;
$getData=$_GET;

//根据构建的路径跳转到对应的方法
changeTo($url,$getData,$postData,$app_key,$app_secret,$redirect_url);
//判断数据是否为空
function dataIsTrue($data){
	if(!isset($data)||empty($data)){
		return false;
	}else{
		return true;
	}
}
function changeTo($url,$getData='',$postData='',$app_key,$app_secret,$redirect_url){
	switch ($url) {
		//用户添加
	case '/getAccessToken':
        //判断数据是否存在
        if(!dataIsTrue($getData['code'])){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
        }
		$obj=new teambition($app_key,$app_secret,$redirect_url);
	 	 echo $obj->getAccessToken($app_key,$getData['code']);
	 	 break;
	case '/user/add':
        //判断数据是否存在
        if(!dataIsTrue($postData['did'])){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
        }
        //判断数据是否存在
        if(!dataIsTrue($postData['email'])){
            header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
        }
		$obj=new teambition($app_key,$app_secret,$redirect_url);
        $res=$obj->depart_member_add($postData['did'],$postData['email']);
		echo $res;
        break;
		//搜索用户
	case '/user/get':
		//对访问类型判断
	    if($_SERVER['REQUEST_METHOD']!="POST"){

	    	header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="REQUEST_METHOD is error";
            echo json_encode($returnArr);
            die;
	    }
	    //判断数据是否存在
		if(!dataIsTrue($postData['depart'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}
		//判断数据是否存在
		if(!dataIsTrue($postData['uid'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;

		}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->user_search($postData['depart'],$postData['uid']);
		echo $res;
		break;
		//用户更新
	case '/user/update':
		//对访问类型判断
	    if($_SERVER['REQUEST_METHOD']!="POST"){

	    	header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="REQUEST_METHOD is error";
            echo json_encode($returnArr);
            die;
	    }
	    //判断数据是否存在
		if(!dataIsTrue($postData['depart'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}
		//判断数据是否存在
		if(!dataIsTrue($postData['uid'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;

		}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$name='';
		if(!empty($postData['first_name'])){
			$name.=$postData['first_name'];
		}
		if(!empty($postData['last_name'])){
			$name.=$postData['last_name'];
		}
		echo $obj->user_update($postData['depart'],$postData['uid'],$name);
		break;
		//用户删除
	case '/user/delete':
		//对访问类型判断
	    if($_SERVER['REQUEST_METHOD']!="POST"){
			    	header("HTTP/1.0 404 error");
		            $returnArr['code']=-1;
		            $returnArr['message']="REQUEST_METHOD is error";
		            echo json_encode($returnArr);
		            die;
			    }
	    //判断数据是否存在
	    if(!dataIsTrue($postData['depart'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		//判断数据是否存在
		if(!dataIsTrue($postData['uid'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}
		$obj=new teambition($app_key,$app_secret);
		$res=$obj->depart_member_delete($postData['depart'],$postData['uid']);
		echo $res;
		break;
		//添加部门
	case '/depart/add':
	    //对访问类型判断
	    if($_SERVER['REQUEST_METHOD']!="POST"){

	    	header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="REQUEST_METHOD is error";
            echo json_encode($returnArr);
            die;
	    }
	    //判断数据是否存在
		if(!dataIsTrue($postData['name'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}

		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->depart_add($postData['name']);
		echo $res;
		break;
		//查找部门
	case '/depart/get':
	//对访问类型判断
	    if($_SERVER['REQUEST_METHOD']!="GET"){

	    	header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="REQUEST_METHOD is error";
            echo json_encode($returnArr);
            die;
	    }
	    //判断数据是否存在
		if(!dataIsTrue($getData['did'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->depart_search($getData['did']);
		echo $res;
		break;
	case '/depart/update':
	//对访问类型判断
		if($_SERVER['REQUEST_METHOD']!="POST"){
		    	header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="REQUEST_METHOD is error";
	            echo json_encode($returnArr);
	            die;
		    }
		    //判断数据是否存在
		if(!dataIsTrue($postData['did'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->depart_update($postData['did'],$postData['name']);
		echo $res;
		break;
	case '/depart/delete':
	//对访问类型判断
		if($_SERVER['REQUEST_METHOD']!="POST"){
			    	header("HTTP/1.0 404 error");
		            $returnArr['code']=-1;
		            $returnArr['message']="REQUEST_METHOD is error";
		            echo json_encode($returnArr);
		            die;
			    }
		//判断数据是否存在
	    if(!dataIsTrue($postData['did'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->depart_del($postData['did']);
		echo $res;
		break;
		//增加部门成员 
	case '/add/depart/member':
	//对访问类型判断
		if($_SERVER['REQUEST_METHOD']!="POST"){
			    	header("HTTP/1.0 404 error");
		            $returnArr['code']=-1;
		            $returnArr['message']="REQUEST_METHOD is error";
		            echo json_encode($returnArr);
		            die;
			    }
		$obj=new teambition($app_key,$app_secret,$redirect_url);

		$res=$obj->depart_member_add($postData['did'],$postData['email']);
		echo $res;
		break;
	case '/search/depart/member':
	//对访问类型判断
		if($_SERVER['REQUEST_METHOD']!="POST"){
			    	header("HTTP/1.0 404 error");
		            $returnArr['code']=-1;
		            $returnArr['message']="REQUEST_METHOD is error";
		            echo json_encode($returnArr);
		            die;
			    }
		//判断数据是否存在
	    if(!dataIsTrue($postData['did'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->depart_all_member($postData['did']);
		echo $res;
		break;
	case '/change/depart/member':
	//对访问类型判断
	 	if($_SERVER['REQUEST_METHOD']!="POST"){
			    	header("HTTP/1.0 404 error");
		            $returnArr['code']=-1;
		            $returnArr['message']="REQUEST_METHOD is error";
		            echo json_encode($returnArr);
		            die;
			    }
		//判断数据是否存在
	    if(!dataIsTrue($postData['src_did'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		//判断数据是否存在
		if(!dataIsTrue($postData['dst_did'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		//判断数据是否存在
		if(!dataIsTrue($postData['email'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		$obj=new teambition($app_key,$app_secret,$redirect_url);
		$res=$obj->depart_user_change($postData['src_did'],$postData['dst_did'],$postData['email']);
		echo  $res;
		break;
	case '/delete/depart/member':
	//对访问类型判断
	    if($_SERVER['REQUEST_METHOD']!="POST"){
			    	header("HTTP/1.0 404 error");
		            $returnArr['code']=-1;
		            $returnArr['message']="REQUEST_METHOD is error";
		            echo json_encode($returnArr);
		            die;
			    }
	    //判断数据是否存在
	    if(!dataIsTrue($postData['did'])){
				header("HTTP/1.0 404 error");
	            $returnArr['code']=-1;
	            $returnArr['message']="params error";
	            echo json_encode($returnArr);
	            die;
			}
		//判断数据是否存在
		if(!dataIsTrue($postData['uid'])){
			header("HTTP/1.0 404 error");
            $returnArr['code']=-1;
            $returnArr['message']="params error";
            echo json_encode($returnArr);
            die;
		}
		$obj=new teambition($app_key,$app_secret);
		$res=$obj->depart_member_delete($postData['did'],$postData['uid']);
		echo $res;
		break;
		//获得组织列表
	case '/organizations':
	     $obj=new teambition($app_key,$app_secret,$redirect_url);
	     $res=$obj->getOrganizations();
	     echo $res;
	     break;
	case '/getme':
	     $obj=new teambition($app_key,$app_secret,$redirect_url);
	     $res=$obj->getme();
	     echo $res;
	default:
		echo "error";
		break;
}
}
?>
