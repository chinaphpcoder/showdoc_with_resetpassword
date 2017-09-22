<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController {

	public function sendmail(){
		echo U('user/resetpasswordbyurl?url=22','',true,true);
	}

	//注册
	public function register(){
		if (!IS_POST) {
			  $this->assign('CloseVerify',C('CloseVerify'));
			  $this->display ();
			}else{
			  $username = I("username");
			  $password = I("password");
			  $confirm_password = I("confirm_password");
			  $v_code = I("v_code");
			  if (C('CloseVerify') || $v_code && $v_code == session('v_code') ) {
		  		if ( $password != '' && $password == $confirm_password) {

			  		if ( ! D("User")->isExistUserOrEmail($username) ) {
						$ret = D("User")->register2($username,$password);
						if ($ret) {
					    	$this->message(L('register_succeeded'),U('Home/User/login'));					    
						}else{
							$this->message('register fail');
						}
			  		}else{
			  			$this->message(L('username_exists'));
			  		}

			  	}else{
			  		$this->message(L('code_much_the_same'));
			  	}
			  }else{
				    $this->message(L('verification_code_are_incorrect'));
			  }
			  
			}
	}

	public function resetPassword(){
		if (!IS_POST) {
				$this->display ();
			}else{
				$email = I("email");
		        $v_code = I("v_code");
		        if( null == $email ) {
		        	$this->message('请输入邮箱');
		            return;
		        }
		        if (!$v_code || $v_code != session('v_code')) {
		            $this->message(L('verification_code_are_incorrect'));
		            return;
		        }

		        $email_info = D("User")->isExistEMail($email);
		        if( $email_info ) {
		            if( $email_info['email_actived'] == 1) {
		                $str = $email.get_millisecond().get_rand_char(8);
		                $token = sha256($str);
		                $token_expire = date("Y-m-d H:i:s",strtotime('+1 days'));
		                D("User")->setEmailVerify($email,$token,$token_expire);		                
		                $url =U("user/resetpasswordbyurl?uid={$email_info['uid']}&email={$email}&token={$token}",'',true,true);
		                $content = $this->genEmail($email_info['username'],$url);
		                //$this->message($content);
		                //return;
		                //$content = "http://doc.study365.org/index.php?s=home/user/resetpasswordbyurl&uid={$email_info['uid']}&email={$email}&token={$token}";
		                $status = think_send_mail($email,'','密码找回',$content);
		                if( true === $status ) {
		                    $this->message('已成功发送重置密码邮件到您的邮箱中。请登录并查看邮件');
		                } else {
		                    $this->message(L('email_send_error'));
		                }
		            } else {
		                $this->message(L('email_does_not_exist'));
		            }
		        }else{
		            $this->message(L('email_does_not_exist'));
		        }		  
			}
	}


	//登录
	public function login()
	{
		if (!IS_POST) {
			//如果有cookie记录，则自动登录
			$cookie_token = cookie('cookie_token');
			if ($cookie_token) {
				$ret = D("UserToken")->getToken($cookie_token);
				if ($ret && $ret['token_expire'] > time() ) {
					D("User")->setLastTime($ret['uid']);
					$login_user = D("User")->where(array('uid' => $ret['uid']))->field('password', true)->find();
					session("login_user" , $login_user);
					$this->message(L('auto_login_succeeded'),U('Home/Item/index'));
					exit();
				}
			}
			$this->assign('CloseVerify',C('CloseVerify'));
		  	$this->display ();

		}else{
		  $username = I("username");
		  $password = I("password");
		  $v_code = I("v_code");
		  if (C('CloseVerify')) { //如果关闭验证码
		  	$ret = D("User")->checkLogin($username,$password);
		    if ($ret) {
		      session("login_user" , $ret );
		      D("User")->setLastTime($ret['uid']);
		      $token = D("UserToken")->createToken($ret['uid']);
	          cookie('cookie_token',$token,60*60*24*90);//此处由服务端控制token是否过期，所以cookies过期时间设置多久都无所谓
		      unset($ret['password']);
	          $this->message(L('login_succeeded'),U('Home/Item/index'));		        
		    }else{
		      $this->message(L('username_or_password_incorrect'));
		    }
		  }else{
			  if ($v_code && $v_code == session('v_code')) {
			    $ret = D("User")->checkLogin($username,$password);
			    if ($ret) {
			      session("login_user" , $ret );
			  D("User")->setLastTime($ret['uid']);
		      	  $token = D("UserToken")->createToken($ret['uid']);
          		  cookie('cookie_token',$token,60*60*24*90);//此处由服务端控制token是否过期，所以cookies过期时间设置多久都无所谓
			      unset($ret['password']);

		          $this->message(L('login_succeeded'),U('Home/Item/index'));		        
			    }else{
			      $this->message(L('username_or_password_incorrect'));
			    }

			  }else{
			    $this->message(L('verification_code_are_incorrect'));
			  }	
		  }
		  

		}
	}

	//生成验证码
	public function verify(){
	  //生成验证码图片
	  Header("Content-type: image/PNG");
	  $im = imagecreate(44,18); // 画一张指定宽高的图片
	  $back = ImageColorAllocate($im, 245,245,245); // 定义背景颜色
	  imagefill($im,0,0,$back); //把背景颜色填充到刚刚画出来的图片中
	  $vcodes = "";
	  srand((double)microtime()*1000000);
	  //生成4位数字
	  for($i=0;$i<4;$i++){
	  $font = ImageColorAllocate($im, rand(100,255),rand(0,100),rand(100,255)); // 生成随机颜色
	  $authnum=rand(1,9);
	  $vcodes.=$authnum;
	  imagestring($im, 5, 2+$i*10, 1, $authnum, $font);
	  }
	  $_SESSION['v_code'] = $vcodes;

	  for($i=0;$i<200;$i++) //加入干扰象素
	  {
	    $randcolor = ImageColorallocate($im,rand(0,255),rand(0,255),rand(0,255));
	    imagesetpixel($im, rand()%70 , rand()%30 , $randcolor); // 画像素点函数
	  }
	  ImagePNG($im);
	  ImageDestroy($im);
	}

	public function setting(){
		$user = $this->checkLogin();
		if (!IS_POST) {
		  $this->assign("user",$user);
		  $this->display ();

		}else{
			$username = $user['username'];
			$password = I("password");
			$new_password = I("new_password");
			$ret = D("User")->checkLogin($username,$password);
			if ($ret) {
					$ret = D("User")->updatePwd($user['uid'],$new_password);
					if ($ret) {
						$this->message(L('modify_succeeded'),U("Home/Item/index"));
					}else{
						$this->message(L('modify_faild'));

					}

				}else{	
					$this->message(L('old_password_incorrect'));
				}

		}
	}

	public function updatepwd(){
		$user = $this->checkLogin();
		if (!IS_POST) {
		  $this->assign("user",$user);
		  $this->display ();

		}else{
			$username = $user['username'];
			$password = I("password");
			$new_password = I("new_password");
			$ret = D("User")->checkLogin($username,$password);
			if ($ret) {
					$ret = D("User")->updatePwd($user['uid'],$new_password);
					if ($ret) {
						$this->message(L('modify_succeeded'),U("Home/Item/index"));
					}else{
						$this->message(L('modify_faild'));

					}

				}else{	
					$this->message(L('old_password_incorrect'));
				}

		}
	}

	public function updateemail(){
		$user = $this->checkLogin();
		if (!IS_POST) {
		  $this->assign("user",$user);
		  $this->display ();

		}else{
			$username = $user['username'];
			$password = I("password");
			$new_password = I("new_password");
			$ret = D("User")->checkLogin($username,$password);
			if ($ret) {
					$ret = D("User")->updatePwd($user['uid'],$new_password);
					if ($ret) {
						$this->message(L('modify_succeeded'),U("Home/Item/index"));
					}else{
						$this->message(L('modify_faild'));

					}

				}else{	
					$this->message(L('old_password_incorrect'));
				}

		}
	}

	public function resetpasswordbyurl(){
		$uid = I("request.uid");
		$email = I("request.email");
		$token = I("request.token");
		if (!IS_POST) {
			$ret = D("User")->checkEmailVerfiy($uid,$email,$token);
			if ($ret) {
				if( $ret['email_actived'] != 1) {
					$this->message(L('email_does_not_exist'));
					return;
				}
				$cookie_token_expire = $ret['cookie_token_expire'];
				if( (strtotime($cookie_token_expire) - time() ) <=  (48*3600) ) {
					$this->display();
				} else {
					$this->message(L('url_had_expired'));
				}
			}else{	
				$this->message(L('url_had_expired'));
			}
		} else {
			$password = I("request.password");
			$ret = D("User")->checkEmailVerfiy($uid,$email,$token);
			if ($ret) {
				$cookie_token_expire = $ret['cookie_token_expire'];
				if( (strtotime($cookie_token_expire) - time() ) <=  (48*3600) ) {
					$ret = D("User")->resetPwd($uid,$password);
					if( $ret ){
						$this->message(L('modify_succeeded'),U("Home/user/login"));
					} else {
						$this->message('修改失败');
					}
				} else {
					$this->message(L('url_had_expired'));
				}
			}else{	
				$this->message(L('url_had_expired'));
			}
		}
	}

	//退出登录
	public function exist(){
		$login_user = $this->checkLogin();
		session("login_user" , NULL);
		cookie('cookie_token',NULL);
		session(null);
		$this->message(L('logout_succeeded'),U('Home/index/index'));
	}

	private function genEmail($username,$url){
		$content = <<<EOF
			<p>
			    <span style="font-family: 宋体, SimSun;">亲爱的{$username}：</span>
			</p>
			<p style="text-indent: 2em;">
			    <span style="font-family: 宋体, SimSun;">欢迎使用showdoc邮箱验证功能。请点击以下链接验证您的邮箱（链接48小时内有效，如无法点击，请复制链接到浏览器访问） ：</span>
			</p>
			<p style="text-indent: 2em;">
			    <a href="{$url}" target="_blank" style="font-family: 宋体, SimSun; text-decoration: underline;"><span style="font-family: 宋体, SimSun;">$url</span></a>
			</p>
			<p>
			    <br/>
			</p>
			<p>
			    <span style="font-family: 宋体, SimSun;">如果您没有申请邮箱验证，请忽略此邮件。</span>
			</p>
			<p>
			    <br/>
			</p>
			<p>
			    <span style="font-family: 宋体, SimSun;">沙小僧</span>
			</p>
			<p>
			    <span style="font-family: 宋体, SimSun;">2017-09-21</span>
			</p>
			<p>
			    <br/>
			</p>
			<p>
			    <span style="font-family: 宋体, SimSun;">（本邮件由系统自动发出，请勿回复。）</span>
			</p>
			<p>
			    <br/>
			</p>
EOF;
		return $content;
	}
}
