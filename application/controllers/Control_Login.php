<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Control_Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('jko_Model');
        $this->load->library('encrypt');


    }
	public function index()
	{
		$this->load->view('view_login');
	}

    public function userlogin(){

        $val=$this->input->get('login_val');
        if($val){
            $user_phone = $val['user_phone'];
            $user_pwd= $val['user_pwd'];
            try{
                $user_data=$this->jko_Model->table_seleRow('c_invoice_job,c_phone,c_EmName,c_jgID,c_jgName,c_gzh_openid,c_pwd,c_job,c_state','jko_employer',array('c_phone'=>$user_phone));
                if(count($user_data)>0){
                    $pwd=$this->encrypt->decode($user_data[0]['c_pwd']);
                    if($user_pwd==$pwd){
                        if($user_data[0]['c_state']=="正常"){
                            $code = 0;
                            $msg = '登录成功！';
                            $this->session->set_userdata($user_data[0]);

                        }
                        else{
                            $code = 1;
                            $msg = '用户状态异常，请联系管理员！！';
                        }
                    }
                    else{
                        $code = 2;
                        $msg = '用户名或密码错误！';
                    }
                }
                else{
                    $code = 3;
                    $msg = '用户不存在';
                }


            }catch (Exception $e){
                log_message('error',$e->getMessage());
            }

        }
        else{
            $code = 3;
            $msg = '登陆错误';

        }

        $raw_success = array('code' => $code, 'msg' => $msg);
        $res_success = json_encode($raw_success,true);
        echo $res_success;



    }




}
