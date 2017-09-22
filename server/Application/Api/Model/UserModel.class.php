<?php
namespace Api\Model;
use Api\Model\BaseModel;

class UserModel extends BaseModel {

    /**
     * 用户名是否已经存在
     * 
     */
    public function isExist($username){
        return  $this->where("username = '%s'",array($username))->find();
    }

    public function isExistUserOrEmail($username){
        return  $this->where("username = '%s' or email = '%s'",array($username,$username))->find();
    }

    /**
     * 用户名是否已经存在
     * 
     */
    public function isExistEMail($email){
        return  $this->where("email = '%s'",array($email))->find();
    }

    public function setEmailVerify($email,$cookie_token,$cookie_token_expire){
        return $this->where("email ='%s' ",array($email))->save(array('cookie_token'=>$cookie_token,'cookie_token_expire'=>$cookie_token_expire));
    }

    /**
     * 注册新用户
     * 
     */
    public function register($username,$password){
        $password = md5(base64_encode(md5($password)).'576hbgh6');
        return $this->add(array('username'=>$username ,'password'=>$password , 'reg_time'=>time()));
    }

    public function register2($username,$password){
        $password = md5(base64_encode(md5($password)).'576hbgh6');
        return $this->add(array('username'=>$username ,'password'=>$password ,'email'=>$username,'email_actived'=>1, 'reg_time'=>time()));
    }

    //修改用户密码
    public function updatePwd($uid, $password){
        $password = md5(base64_encode(md5($password)).'576hbgh6');
        return $this->where("uid ='%d' ",array($uid))->save(array('password'=>$password));   
    }

    /**
     * 返回用户信息
     * @return 
     */
    public function userInfo($uid){
        return  $this->where("uid = '%d'",array($uid))->find();
    }

    /**
     *@param username:登录名  
     *@param password 登录密码   
     */
    
    public function checkLogin($username,$password){
        $password = md5(base64_encode(md5($password)).'576hbgh6');
        $where=array($username,$password,$username,$password);
        return $this->where("( username='%s'  and password='%s' ) ",$where)->find();
    }
    //设置最后登录时间
    public function setLastTime($uid){
        return $this->where("uid='%s'",array($uid))->save(array("last_login_time"=>time()));
    }
}
