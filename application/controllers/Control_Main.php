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
        $user_type = $this->session->userdata('c_job');
        if($user_name){
            $data['username']=$user_name;
            if($user_type==3){
                $this->load->view('view_main',$data);
            }
            else{
                $this->load->view('view_main',$data);
            }
        }

    }

}