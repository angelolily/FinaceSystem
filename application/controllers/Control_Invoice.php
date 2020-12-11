<?php

/**
 * User: lchangelo
 * Date: 2020/10/26
 * Time: 09:55
 * 简要描述：财务员发票信息管理
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Control_Invoice extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
        $this->load->helper('tool');
        $this->load->model('Sys_Model');
        $this->load->model('jko_Model');
	}
	public function index()
	{

		$data=array();
		$jgID=$this->session->userdata('c_jgID');
		if(!($jgID)){
			//session 过期 重新登陆
			$this->load->view('login');
		}
       //获取已完成的报告编号
        $rpoid_sql="select distinct c_rpoid,C_CJRPOTDATE,C_PROJNAME,C_ENTRUST,C_AMOUNT from jko_projinfotb where c_jgID='$jgID' and c_projstate='报告已完成'";



        $data['rpoid']=$this->jko_Model->execute_sql($rpoid_sql);

        $data['jgID']=$this->jko_Model->table_seleRow('c_jgID,c_jgName','jko_jggroup');

		$this->load->view('view_invoice',$data);


	}

    //获取发票数据
    public function get_invoice()
    {


        $where="1=1 ";
        $like="";
        $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');



        if(!($limit)){
            $limit=15;
        }




        if(count($search_val)>=1){

            if($search_val['s_invoice_name']!="")
            {
                $like=" and fdata_invoice_name like '%{$search_val['s_invoice_name']}%'";
            }
            if($search_val['s_repoid']!="")
            {
                $where=$where." and fdata_repoid='{$search_val['s_repoid']}'";
            }
            if($search_val['fdata_statue']!="")
            {
                $where=$where." and fdata_statue='{$search_val['fdata_statue']}'";
            }
            if($search_val['kbdd']!="" && $search_val['kedd']!="")
            {
                $where=$where." and fdata_invoice_date>='{$search_val['kbdd']}' and fdata_invoice_date<='{$search_val['kedd']}'";

            }
            if($search_val['bdd']!="" && $search_val['edd']!="")
            {
                $where=$where." and fdata_cjrpotdate>='{$search_val['bdd']}' and fdata_cjrpotdate<='{$search_val['edd']}'";


            }
            if($search_val['fdata_jg_id']!="")
            {
                $where=$where." and fdata_jg_id='{$search_val['fdata_jg_id']}'";

            }
            if($search_val['invoice_money1']!="" && $search_val['invoice_money2']!="")
            {
                $where=$where." and fdata_invoice_money>='{$search_val['invoice_money1']}' and fdata_invoice_money<='{$search_val['invoice_money2']}'";


            }

        }


        $items=$this->Sys_Model->get_All_invoice($curr,$limit,$where,$like);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);



    }

    //excel输出
    public function outexcel()
    {
        $where="1=1 ";
        $like="";
        $search_val=$this->input->GET('val');

        if(count($search_val)>=1){

            if($search_val['s_invoice_name']!="")
            {
                $like=" and s_invoice_name like '%{$search_val['s_invoice_name']}%'";
            }
            if($search_val['s_repoid']!="")
            {
                $where=$where." and fdata_repoid='{$search_val['s_repoid']}'";
            }
            if($search_val['fdata_statue']!="")
            {
                $where=$where." and fdata_statue='{$search_val['fdata_statue']}'";
            }
            if($search_val['kbdd']!="" && $search_val['kedd']!="")
            {
                $where=$where." and fdata_invoice_date>='{$search_val['kbdd']}' and fdata_invoice_date<='{$search_val['kedd']}'";

            }
            if($search_val['bdd']!="" && $search_val['edd']!="")
            {
                $where=$where." and fdata_cjrpotdate>='{$search_val['bdd']}' and fdata_cjrpotdate<='{$search_val['edd']}'";


            }
            if($search_val['fdata_jg_id']!="")
            {
                $where=$where." and fdata_jg_id='{$search_val['fdata_jg_id']}'";

            }
            if($search_val['invoice_money1']!="" && $search_val['invoice_money2']!="")
            {
                $where=$where." and fdata_invoice_money>='{$search_val['invoice_money1']}' and fdata_invoice_money<='{$search_val['invoice_money2']}'";


            }

        }


        $items=$this->Sys_Model->output_excel($where,$like);
        echo json_encode($items,true);


    }




    //发票信息填写修改
    public function invoice_info_update()
    {
        $sqls=[];
        $result=array();
        $val=$this->input->post('val');
        $fdata=$this->input->post('fdata');
        $type=$this->input->post('type');

        if($val && $fdata)
        {

            if($fdata[0]['fdata_total_flag']!="")
            {
                $where['fdata_total_flag']=$fdata[0]['fdata_total_flag'];
            }
            else
            {
                $where['fdata_num']=$fdata[0]['fdata_num'];
            }

            switch ($type)
            {
                case 1:$val['fdata_statue']="已开票";$val['fdata_finace_emp']=$this->session->userdata('c_EmName');break;
                case 2:$val['fdata_statue']="已退票";$val['fdata_finace_emp']=$this->session->userdata('c_EmName');break;
                case 3:$val['fdata_statue']="改开票";$val['fdata_finace_emp']=$this->session->userdata('c_EmName');break;
                case 4:$val['fdata_statue']="申请退改";break;
            }



            $result_update=$this->Sys_Model->table_updateRow("finance_data",$val,$where);


            if($result_update)
            {

                $result['code']=true;
                $result['msg']='发票信息更新成功';
            }
            else
            {
                $result['code']=false;
                $result['msg']='发票信息更新审核失败';
            }


        }
        else
        {
            $result['code']=false;
            $result['msg']='发票信息更新,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);






    }


//    public function outExcel()
//    {
//        $excel_type=$this->input->GET('excel_type');//导出到excel的标识
//        $search_val=$this->input->GET('val');
//
//        $where="1=1 ";
//        $like="";
//
//        if(count($search_val)>=1){
//
//            if($search_val['s_invoice_name']!="")
//            {
//                $like=" and s_invoice_name like '%{$search_val['s_invoice_name']}%'";
//            }
//            if($search_val['s_repoid']!="")
//            {
//                $where=$where." and fdata_repoid='{$search_val['s_repoid']}'";
//            }
//            if($search_val['fdata_statue']!="")
//            {
//                $where=$where." and fdata_statue='{$search_val['fdata_statue']}'";
//            }
//            if($search_val['kbdd']!="" && $search_val['kedd']!="")
//            {
//                $where=$where." and fdata_invoice_date>='{$search_val['kbdd']}' and fdata_invoice_date<='{$search_val['kedd']}'";
//
//            }
//            if($search_val['bdd']!="" && $search_val['edd']!="")
//            {
//                $where=$where." and fdata_cjrpotdate>='{$search_val['bdd']}' and fdata_cjrpotdate<='{$search_val['edd']}'";
//
//
//            }
//            if($search_val['fdata_jg_id']!="")
//            {
//                $where=$where." and fdata_jg_id='{$search_val['fdata_jg_id']}'";
//
//            }
//            if($search_val['invoice_money1']!="" && $search_val['invoice_money2']!="")
//            {
//                $where=$where." and fdata_invoice_money>='{$search_val['invoice_money1']}' and fdata_invoice_money<='{$search_val['invoice_money2']}'";
//
//
//            }
//
//        }
//
//
//
//    }







}


