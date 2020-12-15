<?php

/**
 * User: lchangelo
 * Date: 2020/10/26
 * Time: 09:55
 * 简要描述：发票申请
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Control_InvoiceApp extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('tool');
        $this->load->model('Sys_Model');
        $this->load->model('Jko_Model');
    }

    public function index()
    {

        $data = array();
        $jgID = $this->session->userdata('c_jgID');
        if (!($jgID)) {
            //session 过期 重新登陆
            $this->load->view('login');
        }
        //获取已完成的报告编号
        $rpoid_sql="select distinct fdata_repoid from finance_data where fdata_jg_id='$jgID'";

        $data['rpoid']=$this->Sys_Model->execute_sql($rpoid_sql);


        $data['account'] = $this->Sys_Model->table_seleRow('account_tax_num,account_bank_phone,account_bank_name,account_bank_address,account_id,account_invoice_name,account_bank_num', 'finance_enterprise_account', array('account_jg_id' => $jgID));

        $this->load->view('view_invoice_app', $data);


    }

    //获取发票数据
    public function get_invoiceApp_data()
    {


        $where = array();
        $like = array();
        $curr = $this->input->GET('page');
        $limit = $this->input->GET('limit');
        $search_val = $this->input->GET('val');
        $jgID = $this->session->userdata('c_jgID');

        if (!($limit)) {
            $limit = 15;
        }


        if (count($search_val) >= 1) {

            if ($search_val['s_invoice_name'] != "") {
                $like['fdata_invoice_name'] = $search_val['s_invoice_name'];
            }
            if ($search_val['s_repoid'] != "") {
                $where['fdata_repoid'] = $search_val['s_repoid'];
            }
            if ($search_val['kbdd'] != "" && $search_val['kedd'] != "") {
                $where['fdata_invoice_date >'] = $search_val['kbdd'];
                $where['fdata_invoice_date <'] = $search_val['kedd'];

            }
            if ($search_val['bdd'] != "" && $search_val['edd'] != "") {
                $where['fdata_cjrpotdate >='] = $search_val['bdd'];
                $where['fdata_cjrpotdate <='] = $search_val['edd'];

            }
            if ($search_val['fdata_statue'] != "") {
                $where['fdata_statue'] = $search_val['fdata_statue'];
            }

        }

        $where['fdata_jg_id'] = $jgID;
        $items = $this->Sys_Model->get_All_invoiceData($curr, $limit, $where, $like);
        $items['msg'] = '';
        $items['code'] = 0;

        echo json_encode($items, true);


    }

    //发票申请添加
    public function add_invoice_App()
    {
        $result = array();
        $finace_data = array();
        $enterprise_data = array();
        $jgID = $this->session->userdata('c_jgID');
        $val = $this->input->post('val');
        if ($val) {

            //区分更新财务数据表与企业开票信息账户表
            //填充企业开票数据
            $enterprise_data['account_bank'] = $val['fdata_bank'];
            $enterprise_data['account_bank_address'] = $val['fdata_bank_address'];
            $enterprise_data['account_bank_name'] = $val['fdata_bank'];
            $enterprise_data['account_bank_phone'] = $val['fdata_bank_phone'];
            $enterprise_data['account_tax_num'] = $val['fdata_tax_num'];
            $enterprise_data['account_jg_id'] = $jgID;
            $enterprise_data['account_invoice_name'] = $val['fdata_invoice_name'];
            $enterprise_data['account_create_by'] = $this->session->userdata('c_EmName');
            $enterprise_data['account_create_time'] = date('Y-m-d H:i:s');
            $enterprise_data['account_bank_num'] = $val['fdata_bank_num'];

            //填充财务数据表
            $finace_data = bykey_reitem($val, "invoice_name");
            $finace_data['fdata_create_time'] = date('Y-m-d H:i:s');
            $finace_data['fdata_jg_id'] = $jgID;
            $finace_data['fdata_num'] = time();
            $finace_data['fdata_emp'] = $this->session->userdata('c_phone');
            $finace_data['fdata_create_time'] = date('Y-m-d H:i:s');
            $finace_data['fdata_statue'] = "发票申请中";

            //通过税号，判断开户行是否存在与数据库中
            if ($val['fdata_tax_num']) {
                //不管是否存在，都先删除数据库中相同税号的开户信息，然后在重新插入新的
                $this->Sys_Model->table_del("finance_enterprise_account", array('account_tax_num' => $val['fdata_tax_num']));

            }
            $this->Sys_Model->table_addRow("finance_enterprise_account", $enterprise_data);
            $this->Jko_Model->table_updateRow("jko_projinfotb", array('C_PZZT' => "1"), array("C_RPOID" => $val['fdata_repoid']));
            $db_result = $this->Sys_Model->table_addRow("finance_data", $finace_data);
            if ($db_result >= 0) {
                $result['code'] = true;
                $result['msg'] = '发票申请成功';


            } else {
                $result['code'] = false;
                $result['msg'] = '发票申请失败';
            }


        } else {
            $result['code'] = false;
            $result['msg'] = '发票申请失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);

    }

    //通过报告编号获取报告其他信息
    public function get_proj_info()
    {
        $rpid = $this->input->post('rpid');
        $result_projinfo = array();
        if ($rpid) {
            $result_projinfo = $this->jko_Model->table_seleRow('C_CJRPOTDATE,C_PROJNAME,C_ENTRUST,C_AMOUNT', 'jko_projinfotb', array('c_rpoid' => $rpid));
            if (count($result_projinfo) > 0) {
                if ($result_projinfo[0]['C_AMOUNT'] > 0) {
                    $result_projinfo[0]['c_evaluation'] = $this->count_total_price($result_projinfo[0]['C_AMOUNT']);
                }

                $result['code'] = true;
                $result['msg'] = '';
                $result['data'] = $result_projinfo;
            } else {
                $result['code'] = false;
                $result['msg'] = '查询不到该报告信息';
                $result['data'] = '';

            }

        } else {
            $result['code'] = false;
            $result['msg'] = '报告编号接受失败';
            $result['data'] = '';

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //计算应收评估费
    public function count_total_price()
    {
        $h_Total = $this->input->post('amount');
        $charge = 0;
        $h_Total = $h_Total * 10000;
        if ($h_Total <= 1000000) {
            if ($h_Total * 0.004 < 2000) {//如果收费低于2000元/条 那么就按两千算
                $charge = 2000;
            } else {
                $charge = $h_Total * 0.004;//如果高于就按高的算
            }

        }
        if ($h_Total > 1000000 && $h_Total <= 10000000) {//高于100万，小于1000
            $charge = 4000 + ($h_Total - 1000000) * 0.002;

        }
        if ($h_Total > 10000000 && $h_Total <= 20000000) {//高于1000万小于2000万
            $charge = 22000 + ($h_Total - 10000000) * 0.0014;

        }
        if ($h_Total > 20000000 && $h_Total <= 50000000) {
            $charge = 36000 + ($h_Total - 20000000) * 0.0007;

        }
        if ($h_Total > 50000000 && $h_Total <= 80000000) {
            $charge = 57000 + ($h_Total - 50000000) * 0.0004;

        }
        if ($h_Total > 80000000 && $h_Total <= 100000000) {
            $charge = 69000 + ($h_Total - 80000000) * 0.0002;

        }
        if ($h_Total > 100000000) {
            $charge = 73000 + ($h_Total - 100000000) * 0.0001;

        }

        $result = array('charge' => $charge);
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);

    }



    /**
     * Notes:取消申请
     * User: Administrator
     * DateTime: 2020/12/7 12:19
     * @return bool
     */
    public function cancel_invoice_app()
    {
        $result = array();
        $val = $this->input->post('val');

        if (is_array($val)) {

            //如果是合计开票，撤回申请后，需要将合计开票金额减少
            if ($val['fdata_total_flag'] <> "") {
                $money = $val['fdata_total_money'] - $val['fdata_invoice_money'];

                $result_upd = $this->Sys_Model->table_updateRow("finance_data", array('fdata_total_money' => $money), array('fdata_total_flag' => $val['fdata_total_flag']));
                if ($result_upd < 0) {
                    $result['code'] = false;
                    $result['msg'] = '撤销申请失败,合并金额未更新';
                    header("HTTP/1.1 201 Created");
                    header("Content-type: application/json");
                    echo json_encode($result);
                    return true;
                }

            }

            $result_del = $this->Sys_Model->table_del('finance_data', array('fdata_num' => $val['fdata_num']));
            if ($result_del >= 0) {
                $this->Jko_Model->table_updateRow("jko_projinfotb", array('C_PZZT' => null), array("C_RPOID" => $val['fdata_repoid']));

                $result['code'] = true;
                $result['msg'] = '撤销申请成功';
            } else {
                $result['code'] = false;
                $result['msg'] = '撤销申请失败';
            }


        } else {
            $result['code'] = false;
            $result['msg'] = '发票撤销申请失败,无法获取数据';

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);

    }

    //合并开票
    public function merge_invoice_info()
    {

        $result = array();
        $sqls = [];
        $val = $this->input->post('val');
        $money = $this->input->post('money');
        if (is_array($val)) {
            $fdata_total_flag = time();
            foreach ($val as $row) {

                $up_sql = "update finance_data set fdata_total_flag='" . $fdata_total_flag . "',fdata_total_money='" . $money . "' where fdata_num='" . $row . "'";
                array_push($sqls, $up_sql);
            }

            $result_update = $this->Sys_Model->table_trans($sqls);

            if ($result_update) {

                $result['code'] = true;
                $result['msg'] = '合并开票申请成功';
            } else {
                $result['code'] = false;
                $result['msg'] = '合并开票申请失败';
            }

        } else {
            $result['code'] = false;
            $result['msg'] = '合并开票申请失败,无法获取数据';

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }


    public function get_rpoid()
    {
        $result = array();
        $jgID = $this->session->userdata('c_jgID');
        //获取已完成的报告编号
        $rpoid_sql = "select distinct c_rpoid,C_CJRPOTDATE,C_PROJNAME,C_ENTRUST,C_AMOUNT from jko_projinfotb where c_jgID='$jgID' and c_projstate='报告已完成' and C_PZZT is null";
        $result = $this->Jko_Model->execute_sql($rpoid_sql);

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);
    }

    //发票退改后，重新申请。
    public function refund_invoice_free()
    {

        $upl_val= [];
        $where_data= [];
        $result= [];
        $val = $this->input->post('val');
        if(is_array($val) && count($val)>0)
        {
            //如果是合计开票，重新申请后，需要将合并开票解散，合计金额清零
            if ($val[0]['fdata_total_flag'] <> "") {
                $upl_val['fdata_total_money']="";
                $upl_val['fdata_total_flag']="";

                $where_data['fdata_total_flag']=$val[0]['fdata_total_flag'];
            }
            else
            {
                $where_data['fdata_num']=$val['fdata_num'];

            }
            $upl_val['fdata_statue']="发票申请中";
            $result_upd = $this->Sys_Model->table_updateRow("finance_data", $upl_val, $where_data);
            if ($result_upd >= 0) {
                $result['code'] = true;
                $result['msg'] = '操作成功';

            }
            else{
                $result['code'] = false;
                $result['msg'] = '操作失败';
            }



        }
        else{
            $result['code'] = false;
            $result['msg'] = '数据获取失败';
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);

    }



}


