<?php

/**
 * User: lchangelo
 * Date: 2020/10/26
 * Time: 09:55
 * 简要描述：发票申请审核管理
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Control_InvoiceVerify extends CI_Controller
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
        $rpoid_sql="select distinct fdata_repoid from finance_data";

       $data['rpoid']=$this->Sys_Model->execute_sql($rpoid_sql);


        $data['jgID']=$this->jko_Model->table_seleRow('c_jgID,c_jgName','jko_jggroup');

		$this->load->view('view_invoice_verify',$data);


	}


	//审核发票申请
    public function verify_invoice_app()
    {
        $sqls=[];
        $result=array();
        $val=$this->input->post('val');
        $type=$this->input->post('type');

        $name=$this->session->userdata('c_EmName');
        if(is_array($val))
        {

            $fdata_back_reason=$this->input->post('back_reason');
            $fdata_express=$this->input->post('express');

            if($type==1)
            {
                $fdata_statue="申请驳回";
            }
            else if($type==2)
            {
                $fdata_statue="开票中";

            }
            else
            {
                $fdata_statue="发票已寄出";
            }

            if($fdata_express)
            {
                $fdata_express_company=$fdata_express['fdata_express_company'];
                $fdata_express_data=$fdata_express['fdata_express_date'];
                $fdata_express_num=$fdata_express['fdata_express_num'];
                $fdata_invoice_emp=$fdata_express['fdata_invoice_emp'];
            }
            else
            {
                $fdata_express_company="";
                $fdata_express_data="";
                $fdata_express_num="";
                $fdata_invoice_emp="";
            }

            foreach ($val['data'] as $row)
            {

                //如果是合计开票，撤回申请后，需要将合计开票金额减少
                if($row['fdata_total_flag']<>"")
                {
                    $upl_sql="update finance_data set fdata_invoice_emp='$fdata_invoice_emp',fdata_express_num='$fdata_express_num',fdata_express_date='$fdata_express_data',fdata_express_company='$fdata_express_company',fdata_verify_emp='".$name."',fdata_statue='$fdata_statue',fdata_back_reason='$fdata_back_reason' where fdata_total_flag='".$row['fdata_total_flag']."'";

                }
                else
                {
                    $upl_sql="update finance_data set fdata_invoice_emp='$fdata_invoice_emp',fdata_express_num='$fdata_express_num',fdata_express_date='$fdata_express_data',fdata_express_company='$fdata_express_company',fdata_verify_emp='".$name."',fdata_statue='$fdata_statue',fdata_back_reason='$fdata_back_reason' where fdata_num='".$row['fdata_num']."'";

                }

                array_push($sqls,$upl_sql);

            }

            $result_update=$this->Sys_Model->table_trans($sqls);

            if($result_update)
            {

                $result['code']=true;
                $result['msg']='审核成功';
            }
            else
            {
                $result['code']=false;
                $result['msg']='审核失败';
            }

        }
        else
        {
            $result['code']=false;
            $result['msg']='审核失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);

    }

    //获取报告路径
    public function get_doc_path()
    {
        $result_projinfo=array();
        $result=array();
        $val=$this->input->POST('val');

        if($val)
        {
            $result_projinfo=$this->jko_Model->table_seleRow("c_docpath,c_rpoid,c_jgID","jko_projinfotb",array('c_rpoid'=>$val['fdata_repoid']));
            if(count($result_projinfo)>0)
            {
                $result['code']=true;
                $result['msg']='数据查询成功';
                $result['data']=$result_projinfo;

            }
            else
            {
                $result['code']=false;
                $result['msg']='数据查询失败';
                $result['data']='';

            }

        }
        else
        {
            $result['code']=false;
            $result['msg']='数据接收失败';
            $result['data']='';

        }
        echo json_encode($result,true);
    }

    //获取发票审核数据
    public function get_invoiceApp_data()
    {


        $where=array();
        $like=array();
        $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');
        $jgID=$this->session->userdata('c_jgID');

        if(!($limit)){
            $limit=15;
        }

        //合计开票明细查询条件
        if(count($search_val)>=1){
            if(array_key_exists('fdata_total_flag',$search_val))
            {
                if($search_val['fdata_total_flag']!="")
                {
                    $where['fdata_total_flag']=$search_val['fdata_total_flag'];
                }
            }
            //发票审核数据查询条件
            else
            {
                if($search_val['s_invoice_name']!="")
                {
                    $like['fdata_invoice_name']=$search_val['s_invoice_name'];
                }
                if($search_val['s_repoid']!="")
                {
                    $where['fdata_repoid']=$search_val['s_repoid'];
                }
                if($search_val['kbdd']!="" && $search_val['kedd']!="")
                {
                    $where['fdata_invoice_date >']=$search_val['kbdd'];
                    $where['fdata_invoice_date <']=$search_val['kedd'];

                }
                if($search_val['bdd']!="" && $search_val['edd']!="")
                {
                    $where['fdata_cjrpotdate >=']=$search_val['bdd'];
                    $where['fdata_cjrpotdate <=']=$search_val['edd'];

                }
                if($search_val['fdata_jg_id']!="")
                {
                    $where['fdata_jg_id']=$search_val['fdata_jg_id'];
                }
                if($search_val['fdata_statue']!="")
                {
                    $where['fdata_statue']=$search_val['fdata_statue'];
                }

            }




        }
        else{
            $where['fdata_statue']="发票申请中";
        }


        $items=$this->Sys_Model->get_All_invoiceData($curr,$limit,$where,$like);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);



    }

	//通过报告编号获取报告其他信息
    public function get_proj_info()
    {
        $rpid=$this->input->post('rpid');
        $result_projinfo=array();
        if($rpid)
        {
            $result_projinfo=$this->jko_Model->table_seleRow('C_CJRPOTDATE,C_PROJNAME,C_ENTRUST,C_AMOUNT','jko_projinfotb',array('c_rpoid'=>$rpid));
            if(count($result_projinfo)>0)
            {
                if($result_projinfo[0]['C_AMOUNT']>0)
                {
                    $result_projinfo[0]['c_evaluation']=$this->count_total_price($result_projinfo[0]['C_AMOUNT']);
                }

                $result['code']=true;
                $result['msg']='';
                $result['data']=$result_projinfo;
            }
            else
            {
                $result['code']=false;
                $result['msg']='查询不到该报告信息';
                $result['data']='';

            }

        }
        else
        {
            $result['code']=false;
            $result['msg']='报告编号接受失败';
            $result['data']='';

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }


	
	
	



}


