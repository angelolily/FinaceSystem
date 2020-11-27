<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Control_Main extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

    }

    public function index()
    {
        $user_name = $this->session->userdata('c_EmName');
        $user_type = $this->session->userdata('c_invoice_job');
        if($user_name){
            $data['username']=$user_name;

            if($user_type==1){
                $data['url']=base_url('index.php/Control_invoiceApp');
                $data['menu_name']="发票申请管理";

            }
            if($user_type==2){
                $data['url']=base_url('index.php/Control_invoiceVerify');
                $data['menu_name']="发票审核管理";
            }
            if($user_type==3){
                $data['url']=base_url('index.php/Control_invoice');
                $data['menu_name']="发票信息管理";
            }
            $this->load->view('view_main',$data);
        }

    }

}